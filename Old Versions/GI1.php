<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../Style.css">
    <script src="../jquery-3.1.0.min.js"></script>
</head>
<script type="text/javascript">
    $(document).ready(function(){
        $(window).bind("beforeunload", function(){ return(false); });
    });
    function onsubmitform() {
        $(window).unbind('beforeunload');
    }
</script>
<?php
session_start();
date_default_timezone_set('America/New_York');
$my_date = date("Y-m-d H:i:s");
/**
 * Created by PhpStorm.
 * User: prashanthrajivan
 * Date: 1/30/17
 * Time: 10:22 AM
 */
//Get the UserID
$ID = $_SESSION["workerId"];

//Get the Condition value
$Condition = $_SESSION["Condition"];


$line = "";
$file = fopen("Config.txt","r");
$temp = 0;
while(! feof($file))
{
    if($temp==0){
        $line = fgets($file);

    }
    $line = $line."+".fgets($file);
    $temp = $temp + 1;
}
fclose($file);

$pieces = explode("+",$line);
$servername = "localhost";
$username = trim( $pieces[0]);
$password = trim($pieces[1]);
$dbname = trim($pieces[2]);
$conn = new mysqli($servername, $username, $password, $dbname);
//<-- The first update is available at the start of the year and subsequent updates are available every 10 days. Therefore, you will make decisions for about 36 ten-day periods. -->
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO UpdateDemographics (UserID, Age, Gender, ExpCondition, starttime) VALUES('" . $_SESSION["workerId"] . "','" . $_GET['age'] . "','" . $_GET['Gender'] . "','".$Condition."','".$my_date."');";

if ($conn->query($sql) === TRUE) {
    # "New record created successfully";
    //Initiatilize the period and trial
    $_SESSION["period"] = 1;
    $_SESSION["day"] = 0;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
?>
<body>
<div id="wrapperC"><h1>Security Update Decision Task</h1></div>
<div id="wrapperL">
    <div id="Instructions">


        <p>In this experiment you will make a series of decisions regarding software updates to your computer. You will not be making actual updates but your decisions will be recorded for experiment purposes.</p>


        <h2>Scenario</h2>
        <p>Imagine that you are a using a computer software. Every 10 days, a new update will be available for your software. You must choose whether to install the update or to continue without updating. Installing the update protects you from future cyber attacks until the end of the 10-day period. <b>For each day you do not update, there is a 2% chance that you will be attacked.</b> If you are attacked, the whole computer system breaks down causing you to lose personal- and work-related information. For the purpose of this task, <b>if you are attacked, you will lose 100 points</b> and you will be unable to continue making decisions for that period. <b>If you are not attacked, you can continue to the next day</b>. You can then choose to update on any day within that period, provided that you have not been attacked.
        </p>

        <h2>Cost of Updating</h2>
        <p>Updating involves a cost of personal time and effort: saving your work, installation time, wait time, and restart time. For the purpose of the task, the cost is represented in points. <b>The cost of updating is 9 points.</b>

        <h2>Experiment Duration</h2>
        <p>You will make software update decisions for 20 periods. Each period contains 10 days. Each 10-day period is independent from the others. This means that the updates protect only until the end of the 10th day, and that if you were attacked in the previous period, the system has recovered when the next period starts.
        </p>

        <h2>Payment</h2>
        <p>At the start of the experiment, you will be given <b>800 points</b> to cover your losses and/or costs in this experiment. At the end of the experiment, any number of points that you don't spend or lose will be converted to real dollars at a rate of <b>10 points=2.5 cents</b> as bonus.Your bonus in the end will be added to your $1.5 base payment.
            <!-- You will receive a bonus of anywhere between $0 and $2 depending on your decisions in this task. --></p>
        <br>
    </div>
</div>

<div id="Examples">
    <div id="wrapperC">
    <form name="myForm" method="get" onsubmit="return onsubmitform();" action="../StartTask.php">
        <h2>Please do <i>not</i> refresh the page or click "back" button during the experiment. Doing so will affect your bonus.</h2>
        <input type="submit" name="submit" class="btn-style" value="Submit">
        <br/><br/><br/>
    </form>
        </div>

</div>
</body>
</html>
