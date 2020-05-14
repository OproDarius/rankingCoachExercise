<?php 

    class outputCampaignDataTable{

        // This function is used just to output data from $campaignData array
        public function __construct()
        {
            global $campaignData;

            // Print table START
            echo '<table><thead><tr><td>Date</td><td>Max. Budget</td><td>Total Cost</td><td>Costs generated</td></tr></thead><tbody>';

            // Foreach day, determinate the date, max budget, total cost and all costs generated, and print them
            foreach($campaignData as $campaignDay)
            {

                $date = $campaignDay['date']; // Current day date
                $maxBudget = ''; // Declare max budget
                $totalCosts = 0;
                $listGeneratedCosts = '';


                // Find the max budget for this day, 
                // If current day have budget changes, take the greatest one
                // else take the maximum budget left from "yesterday"
                if (array_key_exists('bugetPerHour', $campaignDay)){

                    foreach($campaignDay['bugetPerHour'] as $existingBudget)
                    {
                        if($existingBudget['buget'] > $maxBudget)
                        {
                            $maxBudget = $existingBudget['buget'];
                        }
                    }

                }else{
                    foreach($campaignDay['randomHourlyCost'] as $existingBudget)
                    {
                        if($existingBudget['buget'] > $maxBudget)
                        {
                            $maxBudget = $existingBudget['buget'];
                        }
                    }
                }

                // Find the sum of costs generated                
                foreach($campaignDay['randomHourlyCost'] as $existingCosts)
                {
                    $totalCosts = $totalCosts + $existingCosts['cost'];
                }
                
                // Add each cost generated in the string $listGeneratedCosts for output
                foreach($campaignDay['randomHourlyCost'] as $existingCosts)
                {
                    $listGeneratedCosts .= '<b>'.$existingCosts['cost'].'</b> ('.$existingCosts['hour'].'); ';
                }

                echo '<tr>';
                echo '<td>'.$date.'</td>';
                echo '<td>'.$maxBudget.'</td>';
                echo '<td>'.$totalCosts.'</td>';
                echo '<td id="listGeneratedCosts">'.$listGeneratedCosts.'</td>';
                echo '</tr>';

            }

            // Print table END
            echo '</tbody></table>';
        }
    }
?>