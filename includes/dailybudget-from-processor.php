<?php

    // Development Functions
    function dd($input)
    {
        echo '<pre>';
        print_r($input);
        echo '</pre>';
    }
    
    if(isset($_POST['submit'])) 
    {

        $inputArrayCSV = array();
        $campaignData = array();

        function formatUserInputData()
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
        formatUserInputData();

        // Create an array list for every day of the campaign and store it in $campaignData.
        // Add every budget change to $campaignData.
        function createDailyCampaignInterval()
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
        createDailyCampaignInterval();

        // Return randomly between 1 and 10 random hours (H:i)
        function generateRandomHours(){
            
            $randomHour = [];
            $randomCostsNumber = mt_rand(9,10);

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





        function calculateBudgetForInterval($randomHourlyCost, $currentDate)
        {
            global $campaignData;
            global $foundBuget; // Declare the return value of $this function

            // Check if currentDate is the first day of the campaign
            if(array_key_first($campaignData) == $campaignData[$currentDate]['date'])
            {





                // Loop through each budget of current day
                foreach($campaignData[$currentDate]['bugetPerHour'] as $todayBudgets){

                    $dateTodayBudgets = date("H:i", strtotime($todayBudgets['hour'])); 
                    $dateRandomHourlyCost = date("H:i", strtotime($randomHourlyCost)); 

                    if($dateTodayBudgets < $dateRandomHourlyCost)
                    {
                        $foundBuget = $todayBudgets['buget'];
                    }else{
                        $foundBuget = 0;
                    }

                }

                



            }else{
                $foundBuget = 'not yet';
            }

            return $foundBuget;
            
        }





        function generateRandomCosts()
        {
            global $campaignData;

            // Loop through each day of campaign
            foreach ($campaignData as $campaignDay)
            {
                $randomHourlyCosts = generateRandomHours();
                $currentDate = $campaignDay['date'];
                $resultArr=[];

                foreach($randomHourlyCosts as $randomHourlyCost)
                {

                    $budgetForThisInterval = calculateBudgetForInterval($randomHourlyCost, $currentDate);

                    $resultArr[] = 
                    [
                        'hour'  => $randomHourlyCost,
                        'buget' => $budgetForThisInterval,
                        'cost'  => ''
                    ];
                }

                $campaignData[$currentDate]['randomHourlyCost'] = $resultArr;

            }
        }
        generateRandomCosts();

        dd($campaignData);

    }

?>