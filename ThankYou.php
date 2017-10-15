<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="Style.css">
    <meta name="author" content="Rajivan">
</head>
<script type="text/javascript">
    function validate() {
        var val = document.myForm.comment.value;

        if (val == null || val.trim() == "") {
            alert("Please provide your comments");
            return false;
        }
    }
</script>
<?php
/**
 * Created by PhpStorm.
 * User: prashanthrajivan
 * Date: 2/3/17
 * Time: 12:23 PM
 */

session_start();
date_default_timezone_set('America/New_York');
$my_date = date("Y-m-d H:i:s");
/**
 * function to generate random strings
 * @param 		int 	$length 	number of characters in the generated string
 * @return 		string	a new string is created with random characters of the desired length
 */
function RandomString($length = 5) {
    $randstr='';
    srand((double) microtime(TRUE) * 1000000);
    //our array add all letters and numbers if you wish
    $chars = array(
        '1', '2', '3', '4', '5',
        '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
        'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

    for ($rand = 0; $rand <= $length; $rand++) {
        $random = rand(0, count($chars) - 1);
        $randstr .= $chars[$random];
    }
    return $randstr;
}
$code = RandomString();
$finalendow = $_SESSION["endow"];
if($finalendow<0){
    $finalendow=0;
}
$endowdollar = $finalendow*0.0025;
$endowdollar1 = round($endowdollar,2);
$total = $endowdollar1 + 1.5;

// Load Experiment configuration as an array.
$Exp_config = parse_ini_file('ExperimentConfiguration.ini');


//Establish a database connection
// Load configuration as an array. Use the actual location of your configuration file
$config = parse_ini_file('../Config.ini');

// Create a new connection with the DB server details from config.ini
$conn = new mysqli($config['server'],$config['username'],$config['password'],$config['dbname']);

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
$ID = $_SESSION["workerId"];
//$pieces = explode("+",$line);
//$servername = "localhost";
//$username = trim( $pieces[0]);
//$password = trim($pieces[1]);
//$dbname = trim($pieces[2]);
//$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql2="SELECT COUNT(*) FROM UpdateDecisions WHERE UserID='$ID';";
$result1 = $conn->query($sql2);
$num = $result1->fetch_row();
$completed=1;
if($num[0]<200){
    $completed=0;
}

$sql="UPDATE UpdateDemographics SET Completed='$completed', Code='$code', Bonus='$endowdollar1', endtime='$my_date' WHERE UserID='$ID';";
$conn->query($sql);
?>

<body>

<div id="wrapperC">
    <form name="myForm" method="get" onsubmit="return validate();" action="Final.php">
        
        <input type="hidden" name="code" value="<?php echo $code; ?>">
        <input type="hidden" name="finalendow" value="<?php echo $finalendow; ?>">
        <input type="hidden" name="endowdollar1" value="<?php echo $endowdollar1; ?>">
        <input type="hidden" name="total" value="<?php echo $total; ?>">
<br/><br/><br/><br/>
        <h2>Finally, please answer the following question:</h2>
        <h2>What motivated your decisions to update or not in this experiment?</h2>
        <textarea name="comment" rows="5" cols="100"></textarea><br/>
        <br/>
        <input type="submit" name="submit" class="btn-style" value="Submit">
    </form>
</div>

   <!-- <br>
    <div id="wrapperC">
    <h2>Thank you for participating in this study!</h2>
    <h2>Your responses has been recorded! Your completion code: <b><?php /*echo $code; */?></b></h2>
        </div>
    <div id="wrapperC">
    -----------------------------------------
    <br/>
        The TOTAL POINTS you have earned: <u><?php /*echo $finalendow; */?></u> points (<b><i>$<?php /*echo $endowdollar1; */?></i></b>) <br/><br/>
<!--        Your bonus payment = <b><i>$--><?php /*//echo $endowdollar1; */?><!--</i></b></br>-->
<!--        Your base payment  = <b><i>$1.5</i></b></br>-->
<!--        <b>Total  payment = <i>$--><?php ///*echo $total; */?><!--</i></b></br>-->
<!--    ------------------------------------------->
<!--    <br/>-->
<!--</div>-->
<!--    <div id="wrapperC">-->
<!--        <h3>Please return to mTurk and enter the completion code.</h3>-->
<!--    </div>-->

</body>
</html>