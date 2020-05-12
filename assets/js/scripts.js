// Form Submission 
$(document).ready(function() {
    $("form").submit(function(event) {
        event.preventDefault();
        var inputdata = $("#budget-input-data").val();
        var submit = $("#submit").val();
        $(".output-panel").load("includes/dailybudget-from-processor.php", {
            inputdata: inputdata,
            submit: submit
        });
    });
});