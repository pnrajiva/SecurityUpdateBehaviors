<html>
<head>
    <link rel="stylesheet" type="text/css" href="Style.css">
    <script src="jquery-3.1.0.min.js"></script>
</head>

<?php
/**
 * Created by PhpStorm.
 * User: prashanthrajivan
 * Date: 2/2/17
 * Time: 6:32 PM
 */
session_start();
$_SESSION["outcome"]=0;
$_SESSION["outcomeday"]=0;
$_SESSION["outcomecost"]=0;
$_SESSION["payoff"] = 0;
$_SESSION["DayPayoff"]=0;
$_SESSION["outcome2"]=0;
$_SESSION["outcomeday2"]=0;
?>
<script type="text/javascript">
    $(document).ready(function(){
        $(window).bind("beforeunload", function(){ return(false); });
    });
    function onsubmitform() {
        $(window).unbind('beforeunload');
    }
</script>
<body>
<div id="wrapperC">
    <!--<form name="myForm" method="get" onsubmit="return onsubmitform();" action="Task.php">
        <br><br>

        <h3>You are given <b>100</b> points to cover your losses and costs in this experiment</h3>
        <h4>Click 'Begin' to begin the task</h4>
        <h4>This will take you to the version with sequential decisions</h4>
        <input type="submit" name="submit" class="btn-style" value="Begin">
    </form>-->
    <form name="myForm" method="get" onsubmit="return onsubmitform();" action="Task_parallel.php">
        <br><br>
        <h3>You are given <b>100</b> points to cover your losses and costs in this experiment</h3>
        <h4>Click 'Begin' to begin the task</h4>
<!--        <h4>This will take you to the version with parallel decisions</h4>-->
        <input type="submit" name="submit" class="btn-style" value="Begin">
    </form>
</div>
</body>
</html>