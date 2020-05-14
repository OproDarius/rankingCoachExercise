<?php 
        
    class generateCampaignData{
        
        // Run all functions needed to process the client input and create $campaignData
        public function __construct()
        {
            $this->formatUserInputData();
            $this->createDailyCampaignInterval();
            $this->generateRandomCostsAndBudgetLimits();
        }

        // Transform the user data to Array and save it to $inputArrayCSV
        public function formatUserInputData()
        {
            global $inputArrayCSV;
            global $campaignData;
            
            // Transform the CSV input to Array
            $lines = explode("\n", $_POST["inputdata"]);
            foreach ($lines as $line) 
            {
                $inputArrayCSV[] = str_getcsv($line);
            }

            // Change to data format to be recognized by strtotime() function.
            foreach($inputArrayCSV as $array => $date)
            {
                $inputArrayCSV[$array][0] = str_replace('.', '/', $date[0]);

            }
        }


        // Create an array list for every day of the campaign and store it in $campaignData.
        // Add every budget change to $campaignData acording to the day where belongs.
        public function createDailyCampaignInterval()
        {
            global $inputArrayCSV; // User Data Input
            global $campaignData; // Campaign Days

            // Get the first date entered by the user to calculate 3 months from now.
            $firstInputDate = $inputArrayCSV['0'];
            $startTime = strtotime($firstInputDate['0']); // Get the start Campaign Date
            $endTime = strtotime($firstInputDate['0'] . "+3 months"); // Get the date when Campaign Ends
            
            for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) // Add everyday date in the $campaignData for 3 months
            {
                $thisDate = date( 'm/d/Y', $i );
                $campaignData[$thisDate] = ['date' => $thisDate]; 

                // Go through every budget change and verify if it coresponds to the actual day, if so, add the budget changes (value,time) to the array. 
                foreach($inputArrayCSV as $array)
                {
                    if($array[0] == $thisDate)
                    {
                        array_shift($array);

                        $j=0;
                        $resultArr=[];
                        $bugets=[];
                        $hours=[];

                        foreach($array as $arr)
                        {
                            if($j % 2 !== 0)
                            {
                                $hours[] = $arr;
                            } else {
                                $bugets[] = $arr;
                            }
                            $j++;
                        }

                        for($k=0;$k<sizeof($hours);$k++)
                        {
                            $resultArr[] = 
                            [
                                'hour'  => $hours[$k],
                                'buget' => $bugets[$k]
                            ];
                        }

                        $campaignData[$thisDate]['bugetPerHour'] = $resultArr;
                    }
                }
            }
        }

        // Return randomly between 1 and 10 hours(H:i) that will be used to simulate costs
        public function generateRandomHours(){
            
            $randomHour = [];
            $randomCostsNumber = mt_rand(3,10); // Generate between 3 an 10 costs daily

            // Generate random ammount of hours 
            for($i = 0; $i<$randomCostsNumber; $i++){
                $randomHour[] = mt_rand(0,23).":".str_pad(mt_rand(0,59), 2, "0", STR_PAD_LEFT);
            }

            // Sort the random hours Array
            usort($randomHour, function($a, $b) {
                return (strtotime($a) > strtotime($b));
             });

            return $randomHour;
        }

        
        // For each simulated Cost process, calculate the Maximum Budget Available
        public function calculateBudgetForInterval($randomHourlyCost, $currentDate)
        {
            global $campaignData;
            global $foundBuget; // Declare the return value of $this function

            // Check if currentDate is the first day of the campaign
            if(array_key_first($campaignData) == $campaignData[$currentDate]['date'])
            {
                // Go through each budget change and find the budget for the current $randomHourlyCost
                for($k=0;$k<sizeof($campaignData[$currentDate]['bugetPerHour']);$k++)
                {
                    // Get budget hour and random hour and transorm them into date format for math
                    $bugetChangeHour = $campaignData[$currentDate]['bugetPerHour'][$k]['hour'];
                    $dateBugetChangeHour = date("H:i", strtotime($bugetChangeHour)); 
                    $dateRandomHourlyCost = date("H:i", strtotime($randomHourlyCost)); 
                    
                    // If the random hour is bigger or equal to the budget hour, move to next, if not, means the previews is te correct one and break;
                    if($dateRandomHourlyCost >= $dateBugetChangeHour)
                    {
                        $foundBuget = $campaignData[$currentDate]['bugetPerHour'][$k]['buget'];
                        
                    }else{
                        break;
                    }
                }
            }else{
                // If current currentDate have budget changes then calculate the budget for each random hour
                // If not, take the last budget used from previews day
                if(array_key_exists('bugetPerHour', $campaignData[$currentDate]))
                {
                    $foundBuget = 'none'; 

                    // Go through each budget change and find the budget for the current $randomHourlyCost
                    for($k=0;$k<sizeof($campaignData[$currentDate]['bugetPerHour']);$k++)
                    {
                        // Get budget hour and random hour and transorm them into date format for math
                        $bugetChangeHour = $campaignData[$currentDate]['bugetPerHour'][$k]['hour'];
                        $dateBugetChangeHour = date("H:i", strtotime($bugetChangeHour)); 
                        $dateRandomHourlyCost = date("H:i", strtotime($randomHourlyCost)); 

                        // If the random hour is bigger or equal to the budget hour, move to next, if not, means the previews is te correct one and break;
                        if($dateRandomHourlyCost >= $dateBugetChangeHour)
                        {
                            $foundBuget = $campaignData[$currentDate]['bugetPerHour'][$k]['buget'];
                            
                        }else{
                            break;
                        }
                    }

                    // Check if a budget is found, if not, get the last budget value used yesterday
                    if($foundBuget == 'none')
                    {
                        $beforeCurrentDate = date('m/d/Y',(strtotime ( '-1 day' , strtotime ( $currentDate) ) )); // Get yesterday date
                        $arr = $campaignData[$beforeCurrentDate];
        
                        // If yesterday date have budget changes, return the last budget change
                        // If not, take the last budget used for cost generation
                        if (array_key_exists('bugetPerHour', $arr)) 
                        {
                            $arrSelectLast = end($arr['bugetPerHour']);
                            $foundBuget = $arrSelectLast['buget'];
                        }else{
                            $arrSelectLast = end($arr['randomHourlyCost']);
                            $foundBuget = $arrSelectLast['buget'];
                        }
                    }
                    
                }else{
                    $beforeCurrentDate = date('m/d/Y',(strtotime ( '-1 day' , strtotime ( $currentDate) ) )); // Get yesterday date
                    $arr = $campaignData[$beforeCurrentDate];
    
                    // If yesterday date have budget changes, return the last budget change
                    // If not, take the last budget used for cost generation
                    if (array_key_exists('bugetPerHour', $arr)) 
                    {
                        $arrSelectLast = end($arr['bugetPerHour']);
                        $foundBuget = $arrSelectLast['buget'];
                    }else{
                        $arrSelectLast = end($arr['randomHourlyCost']);
                        $foundBuget = $arrSelectLast['buget'];
                    }
                }
            }

            // Check if a budget is found, if not, the campaign is on pause. Set the budget to 0
            if($foundBuget == '')
            {
                return 0;
            }else{
                return $foundBuget;
            }
        }

        // Generate the actual cost of current add process simulation
        public function generateCost($randomHourlyCost, $currentDate, $budgetForThisInterval)
        {
            // The logic behind generateCost():
            //
            // - Requests
            // 1. The cumulated daily cost can not be greater than two times of what the budget is set in the given moment
            // 2. The cumulated cost per month can not not be greater than the sum of the maximum budget for each days within the month
            //
            // Solved by:
            // 1) Verifying if current day has more than one budget change (excluding 0 because it means the campaign is paused): 
            // - Take the smallest budget set and multiply it by 2. This will be upper limit for the max costs for today ($minimumBudgetToday);
            // - Searching for biggest number of current day($maximumBudgetToday), if $minimumBudgetToday x 2 is bigger than $maximumBudgetToday, the maximumBudgetToday is becoming the max costs from today.
            // - The resulted maxCostsToday it will be divided by the ammount of random ours generated and the result will be the upper cost limit for each cost generated.
            // 2) [else] Verifying if this day has only one budget change, that budget it will be used to calculate the max costs for today. 

            global $campaignData;
            $generatedCost = 0; // Declare the return value of $this function
            $currentDateBudgetChanges = 0; // Declare budget changes count
            $todayBudgets = array(); // Store all budgets for today
            $todayBudgetsCount = 0;// Store the number of budget changes
            
            // Loop trough all day to determinate all the budgets 
            foreach($campaignData[$currentDate]['randomHourlyCost'] as $campaignDay)
            {
                if($campaignDay['buget'] !== 0)
                {
                    array_push($todayBudgets, $campaignDay['buget']);
                }
            }

            $todayBudgetsCount = ( count( array_unique($todayBudgets) ) );

            // Sanitize $todayBudgets;
            foreach ($todayBudgets as $array_key => $array_item) 
            {
                if ($todayBudgets[$array_key] == 0) {
                  unset($todayBudgets[$array_key]);
                }
            }

            if($todayBudgetsCount >= 2)
            {
                $minimumBudgetToday = min($todayBudgets);
                $maximumBudgetToday = max($todayBudgets) / 10;
                $maximumBudgetCost = 0;

                if($budgetForThisInterval > 0)
                {

                    if($minimumBudgetToday * 2 > $maximumBudgetToday)
                    {
                        $maximumBudgetCost = $maximumBudgetToday;
                        $generatedCost = (mt_rand(0,$maximumBudgetCost * 10) / 10) / $todayBudgetsCount;
                    }else{
                        $maximumBudgetCost = $minimumBudgetToday * 2;
                        $generatedCost = (mt_rand(0,$maximumBudgetCost * 10) / 10) / $todayBudgetsCount;
                    }

                }else{
                    $generatedCost = 0;
                }

            }else{

                
                if($budgetForThisInterval > 0)
                {
                        $maximumBudgetToday = max($todayBudgets) / 10; // divided by number of costs generated today

                        $maximumBudgetCost = $maximumBudgetToday;
    
                        $generatedCost = (mt_rand(0,$maximumBudgetCost * 10) / 10) / $todayBudgetsCount;
    
                }else{
                    $generatedCost = 0; 
                }
            }

            return number_format((float)$generatedCost, 2, '.', ''); 
        }

        
        // Generate all the costs and return them in the $campaignData array
        public function generateRandomCostsAndBudgetLimits()
        {
            global $campaignData;

            // Loop through each day of campaign to set the hour and the budget of that hour
            foreach ($campaignData as $campaignDay)
            {
                $randomHourlyCosts = $this->generateRandomHours();
                $currentDate = $campaignDay['date'];
                $resultArr=[];

                foreach($randomHourlyCosts as $randomHourlyCost)
                {

                    $budgetForThisInterval = $this->calculateBudgetForInterval($randomHourlyCost, $currentDate);

                    $resultArr[] = 
                    [
                        'hour'  => $randomHourlyCost,
                        'buget' => $budgetForThisInterval,
                        'cost'  => '' // This will be generated after entire $campaignDay hours and budget is calculated
                    ];
                }

                $campaignData[$currentDate]['randomHourlyCost'] = $resultArr;

            }
            
            // Loop through each day of campaign and each cost generation (hour and budget) to set the cost
            foreach ($campaignData as $campaignDay)
            {
                $currentDate = $campaignDay['date'];

                foreach ($campaignDay['randomHourlyCost'] as $campaignCost => $cost)
                {
                    
                    $randomHourlyCost = $campaignDay['randomHourlyCost'][$campaignCost]['hour'];
                    $budgetForThisInterval = $campaignDay['randomHourlyCost'][$campaignCost]['buget'];

                    $generatedCost = $this -> generateCost($randomHourlyCost, $currentDate, $budgetForThisInterval);
                    // Replace each cost with the generated one
                    $campaignData[$currentDate]['randomHourlyCost'][$campaignCost]['cost'] = $generatedCost;

                }
            }
        }
    }

?>