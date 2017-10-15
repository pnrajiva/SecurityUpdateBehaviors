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
    
</script>
<body>
<script type="text/javascript">
    function validate()
    {
        var txt = "";
        if( document.myForm.age.value == false  )
        {
            alert( "Please provide your age" );
            return false;
        }

        if( document.myForm.Gender.value == false  )
        {
            alert( "Please answer the question on gender" );
            return false;
        }


        if ( txt === "lt18"){ //exclusion criteria
            $(window).unbind('beforeunload');
            document.getElementById('start').style.display = "none";
            document.getElementById('catnum12').innerHTML = "We are sorry to hear that you don't qualify to participate in our experiment (Underage). \nThank You for your interest";
            return false;
        }
        $(window).unbind('beforeunload');
    }
</script>

<?php
session_start();
//Set the MTurk ID
$_SESSION["workerId"] = $_GET["MTId"];

//Set the Name of the page to go next
$result = "Instructions.php";

// Load Experiment configuration as an array.
$Exp_config = parse_ini_file('ExperimentConfiguration.ini');
$_SESSION["Condition"]=$Exp_config['Condition'];

//$rand = mt_rand(1,10000)/10000;
//Based on the random number, choose the next step
//$rand = 0.4; //changed it from 0.6 to 0.4 for running fixed condition
//$rand = 0.6; //changed it from 0.6 to 0.4 for running fixed condition
//if($rand<0.51) {
//    $result = "GI2_Fixed.php";
//    $_SESSION["Condition"]=1;
//}
//else {
//    $result = "Instructions.php";
//    $_SESSION["Condition"]=2;
//}

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
//echo $line;
/*$pieces = explode("+",$line);
$servername = "localhost";
$username = trim( $pieces[0]);
$password = trim($pieces[1]);
$dbname = trim($pieces[2]);*/

// Load configuration as an array. Use the actual location of your configuration file
$config = parse_ini_file('../Config.ini');

// Create a new connection with the DB server details from config.ini
$conn = new mysqli($config['server'],$config['username'],$config['password'],$config['dbname']);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT UserID FROM UpdateDemographics Where UserID='".$_SESSION["workerId"]."'";
$result1 = $conn->query($sql);
if ($result1->num_rows > 0) {
    header('Location: Error.html');
}
$conn->close();
?>

<label id="catnum12" style="font-size:x-large; color:black; font-style:italic;" ></label>
<div id="start">
<form name="myForm" method="get" onsubmit="return validate();" action=<?php echo $result;?>>
    <h3>What is your age?</h3>
    <!--<label style="font-size: medium;"><input type="radio" name="age" value="lt18"/> Less than 18<br/></label> -->
    <label style="font-size: medium;"><input type="radio" name="age" value="18-25"/> 18-25<br/></label>
    <label style="font-size: medium;"><input type="radio" name="age" value="26-35"/> 26-35<br/></label>
    <label style="font-size: medium;"><input type="radio" name="age" value="36-45"/> 36-45<br/></label>
    <label style="font-size: medium;"><input type="radio" name="age" value="46-55"/> 46-55<br/></label>
    <label style="font-size: medium;"><input type="radio" name="age" value="56-65"/> 56-65<br/></label>
    <label style="font-size: medium;"><input type="radio" name="age" value="66-75"/> 66-75<br/></label>
    <label style="font-size: medium;"><input type="radio" name="age" value="75+"/> 76 and above<br/></label>
    <hr>

    <h3>Which of the following describes how you think of yourself?</h3>
    <label style="font-size: medium;"><input type="radio" name="Gender" value="F"/> Female<br/></label>
    <label style="font-size: medium;"><input type="radio" name="Gender" value="M"/> Male<br/></label>
    <label style="font-size: medium;"><input type="radio" name="Gender" value="I"/> Other<br/></label>
    <label style="font-size: medium;"><input type="radio" name="Gender" value="N"/> Prefer not to say<br/></label>
    <hr>


    <br/><br/>
    <input type="submit" name="submit" class="btn-style" value="Submit">
</form>
</div>
</body>
</html>