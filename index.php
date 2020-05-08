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
                    <p>You can paste data below in CSV format (<bold>timeStamp, budgetValue</bold>). Comma separated values and each entry on a new line, like in the example below: </p>
                    <p>01.01.2019 10:00:00, 7</p>
                    <p>01.01.2019 11:00:00, 0</p>
                    <p>01.01.2019 21:00:01, 1</p>
                    <p>01.01.2019 21:00:01, 6</p>
                    <p>01.01.2019 21:00:01, 2</p>
                    <p>01.01.2019 21:00:01, 0</p>
                    <p>01.01.2019 21:00:01, 1</p>
                    <p>01.01.2019 21:00:01, 0</p>
                    <p>01.01.2019 21:00:01, 1</p>
                </div>
                <form>
                    <textarea id="budget-input-data" rows="15" cols="100" required></textarea>
                    <input id="submit" type="submit" value="Generate costs & Daily Report"></input>
                </form>
                <div class="form-split">

                </div>
                
            </div>       
        </section>



        <script src="assets/js/scripts.js" async></script>
    </body>
</html>