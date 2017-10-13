<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="Style.css">
    <meta name="author" content="Rajivan">
</head>
    <?php
/**
 * Created by PhpStorm.
 * User: prashanthrajivan
 * Date: 2/3/17
 * Time: 12:23 PM
 *
 */
?>
<?php
session_start();
$code = $_GET["code"];
$finalendow = $_GET["finalendow"];
$endowdollar1 = $_GET["endowdollar1"];
$total = $_GET["total"];
$comment = $_GET["comment"];
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

$ID = $_SESSION["workerId"];
$pieces = explode("+",$line);
$servername = "localhost";
$username = trim( $pieces[0]);
$password = trim($pieces[1]);
$dbname = trim($pieces[2]);
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql="UPDATE UpdateDemographics SET Comments='$comment' WHERE UserID='$ID';";
$conn->query($sql);
?>
<body>
<br/><br/><br/>
<div id="wrapperC">
    <h2>Your responses has been recorded</h2>
    You have earned a <b>total of <?php echo $finalendow; ?> points (<i>$<?php echo $endowdollar1; ?></i>) </b><br/>
    <h3>Total  payment = <i>$<?php echo $total; ?></i></h3>

    <br/>
    <h3>Your completion code is: <?php echo $code; ?></h3>
    Please return to mTurk and enter the completion code. After you have done so, you may close this window. <br/><br/>
    <h2>Thank you for participating in this study!</h2>

</div>
</body>
</html>
