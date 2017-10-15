<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="Style.css">
    <script src="jquery-3.1.0.min.js"></script>
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

//Get the UserID
$ID = $_SESSION["workerId"];

//Get the Condition value
$Condition = $_SESSION["Condition"];

//$line = "";
//$file = fopen("Config.txt","r");
//$temp = 0;
//while(! feof($file))
//{
//    if($temp==0){
//        $line = fgets($file);
//
//    }
//    $line = $line."+".fgets($file);
//    $temp = $temp + 1;
//}
//fclose($file);
//
//$pieces = explode("+",$line);
//$servername = "localhost";
//$username = trim( $pieces[0]);
//$password = trim($pieces[1]);
//$dbname = trim($pieces[2]);

// Load configuration as an array. Use the actual location of your configuration file
$config = parse_ini_file('../Config.ini');

// Create a new connection with the DB server details from config.ini
$conn = new mysqli($config['server'],$config['username'],$config['password'],$config['dbname']);

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
    //$_SESSION["payoff"] = 0;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
?>

<body>
<div id="wrapperC"><h1>Investment decisions and security failures</h1></div>
<div id="wrapperL">
    <div id="Instructions">
        <p>
            In this study, you will play through a simulation in which you will make repeated work-related decisions.
        </p>
        <p>
        	<b><u>Your work consists of making investment decisions</u></b> over 20 work periods. Each work period consists of 10 days (see figure below). On each day, you must choose between safe Investment A, which provides you a guaranteed return of 2 points, and risky Investment B, which gives you 0 or 4 points. 
          <!--   <b><u>Your regular work is to make investment decisions</u></b>, choosing between an option (A) that provides you a safe return (2, measured in points) and a risky investment (B) that can give you 0 or 4 points. -->
        <br/><br/>
        	During the course of your work, <b><u>a failure in the security of your business may randomly arise.</u></b> This failure is very costly to your business; every time you have a security failure, you lose 100 points. Your security team estimates the chance of a security failure on any given day is 3%.
            <!-- <b><u>A security failure can be very costly to your business, resulting in a big loss.</u></b> Your security team estimates a 3% chance of security failures that would result in 100 points loss. -->
        </p>
<!--        <p>-->
<!--            Updates to protect your work will be made available some times. The cost of an update is most often (85% of the time) 10 points; but sometimes (15% of the time), an update may be offered for free!!, 0 points. If you choose to update, this will reduce your chance of confronting a security failure, from 3% to 1%.-->
<!--        </p>-->
        <p>
            <!-- At some point, updates to protect your work will be made available. The cost of an update is most often 10 points (85% of the time); but sometimes (15% of the time), an update may be available for free (0 points). <br/>
            If you choose to update, your chance of confronting a security failure reduces from 3% to 1%. -->
            At some point during your work, <b><u>security updates to lower the risk of failure will be made available.</u></b> The cost of an update is most often (85% of the time) 10 points, but sometimes (15% of the time) an update will be available for free (0 points). <br/>
            If you choose to update, your chance of confronting a security failure reduces from 3% to 1%.
        </p>
        <p>
            <!-- You will start this task with 100 points. You will go over 20 work periods, each period has 10 days (see figure below). In each day you will make one investment decision by clicking on one of two buttons:  A or B. You will get informed of the points received from your investment decision and you will see the total points accumulated within the period. You will be informed when an update is available and at what cost. -->
            You will start this task with 100 points. As you make investment decisions each day (i.e., choosing between “A” and “B”), you will receive feedback on the points you earned from your decision and the total number of points accumulated within the period. You will be informed when a security update is available and at what cost.
        </p>

        <p>
            <b>Payment:</b> At the end of the study, your accumulated points will be converted to US dollars at a rate of 100 points = $0.25. If your accumulated points are below zero, you will get a bonus of $0, and depending on your decisions, your bonus may be as high as $1.5. This bonus will be added to your $1.50 base payment.

        </p>
        <div id="wrapperC" style="border-style: solid;">
        <img src="images/Screenshot.png" alt="Experiment" style="width:600px;height:228px;"></div>
        <!-- <h2>Scenario</h2>
        <p>Imagine that you are using a computer software to make work-related choices on a daily basis. For the purpose of this task, you will choose between two options. Based on the option you choose each day, you will accumulate points.
        </p>
        <p>There is a 3% chance that you might incur a loss of 100 points due to a security event. </p>

        <h2>Experiment Duration</h2>
        <p>You will make decisions for 20 periods. Each period contains 10 days.
        </p>

        <h2>Payment</h2>
        <p>At the start of the experiment, you will be given 100 points to cover possible losses in this experiment. At the end of the experiment, any number of points that you have remaining will be converted to real dollars at a rate of 10 points=2.5 cents as bonus. Your bonus in the end will be added to your $1.5 base payment. You will receive a bonus of anywhere between $0 and $2 depending on your decisions in this task.
        <br> -->

    </div>
</div>
<div id="Examples">
    <div id="wrapperC">
    <form name="myForm" method="get" onsubmit="return onsubmitform();" action="StartTask.php">
        <h2 style="color: red;"><b>Important:</b> Please do <i>not</i> refresh the page or click "back" button during the experiment. Doing so will disqualify you from receiving a bonus payment.</h2>
        <h3>Click <b>Start</b> if you are ready to start. Good Luck!</h3>
        <input type="submit" name="submit" class="btn-style" value="Start">
        <br/><br/><br/>
    </form>
</div>
</div>
</body>
</html>
