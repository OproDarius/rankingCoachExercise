<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>RankingCoach - Web Recruitment Exercise</title>
        <meta name="description" content="RankingCoach - Web Recruitment Exercise">
        <meta name="author" content="Opro Darius">
        <link rel="stylesheet" href="assets/css/style.css?v=1.0">
    </head>
    <body>

        <section class="user-input-panel">
            <div class="section-header">
                <h1>Insert your Adwords Budget History data:</1>
            </div> 
            <div class="section-body">
                <div class="instructions">
                    <p>You can paste data below in CSV format (<bold>date, budgetChangeValue1, budgetChangeDate1, budgetChangeValue2, budgetChangeDate2 etc...</bold>).
                    <br /> Comma separated values and each entry on a new line, like in the example below: <br /><br /></p>
                    01.01.2019, 7, 10:00, 0, 11:00, 1, 12:00, 6, 23:00<br />
                    01.05.2019, 2, 10:00<br />
                    01.06.2019, 0, 00:00<br />
                    02.09.2019, 1, 13:13<br />
                    03.01.2019, 0, 12:00, 1, 14:00<br />
                </div>
                <form action="includes/dailybudget-from-processor.php" method="POST">
                    <textarea id="budget-input-data" name="inputdata" rows="15" cols="100" required></textarea>
                    <input id="submit" type="submit" name="submit" value="Generate costs & Daily Report"></input>
                </form>
            </div>       
        </section>

        <section class="output-panel">
            
        </section>

        <script src="assets/js/jquery-3.5.1.min.js" type="text/javascript"></script>
        <script src="assets/js/scripts.js" type="text/javascript"></script>
    </body>
</html>