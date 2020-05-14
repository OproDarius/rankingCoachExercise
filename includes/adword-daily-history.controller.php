<?php
    if(isset($_POST['submit']))
    {

        require "adword-daily-history.model.php";
        require "adword-daily-history.view.php";
        
        $inputArrayCSV = array();   // Declare $inputArrayCSV, here, all the input from the user it will be stored
        $campaignData = array();    // Declare $campaignData, here, all the input from the user it will be manipulated to generate daily campaign costs


        $instance = new generateCampaignData();
        $output = new outputCampaignDataTable();

        // Developer output
        echo '<h2>Print Output of $campaignData for developing</h2>';
        echo'<pre>';
        print_r($campaignData);
        echo'</pre>';
        
    }
?>