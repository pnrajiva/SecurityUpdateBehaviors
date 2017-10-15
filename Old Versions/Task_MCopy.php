<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../Style.css">
    <script src="../jquery-3.1.0.min.js"></script>

</head>

<?php
/**
 * Created by PhpStorm.
 * User: prashanthrajivan
 * Date: 1/30/17
 * Time: 10:23 AM
 */

session_start();

?>


<?php
//Establish a database connection
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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


//Get the UserID
$ID = $_SESSION["workerId"];
//Get the Condition value
$Condition = $_SESSION["Condition"];
//get the period number
$period = $_SESSION["period"];
//get the previous day number
$day = $_SESSION["day"];

//Between periods - calculate the cost of each day
if($day==10){
    //increment the period number
    $period +=1;
    $_SESSION["period"] = $period;
    $day = 0;
    $_SESSION["outcome"]=0;
    $_SESSION["outcomeday"] = 0;
}
//Close the database connection
$conn->close();

//increment the day number
$day +=1;
$_SESSION["day"] = $day;

//on the first period - calculate the cost of each day
if($day==1){
    if($Condition == 1){
        $cost = array(9,9,9,9,9,9,9,9,9,9);
        $_SESSION["costlist"] = implode(",",$cost);
    }
    else{
        //Calculate the cost for each day in the period
        $cost = array(10,10,10,10,0,10,10,10,10,10);
        shuffle($cost);
        $_SESSION["costlist"] = implode(",",$cost);
    }
}

//get the cost of update for that day
$costlist = $_SESSION["costlist"];
$costarray = explode(",",$costlist);
$index = $day-1;
$UpdateCost = $costarray[$index];
$Attacked = 0;

//Calculate the probability of being attacked except for the first day
if($day>1 and $_SESSION['outcome']<1){
$rand = mt_rand(1,10000)/10000;
if($rand<=0.7){
    $Attacked = 0;
}
else{
    $Attacked = 1;
}
}

?>
<script type="text/javascript">

    $(document).ready(function(){
        $(window).bind("beforeunload", function(){ return(false); });
    });
    function onsubmitform() {
        $(window).unbind('beforeunload');
    }

    function A_click(){

        //disable the update and continue buttons
        //save the decision to hidden file
        $("#ButtonA").hide();
        $("#ButtonB").hide();
        $("#Msg1").hide();
        $("#Msg2").show();
        $("#Decision").val("1");
        $("#Msg2").text("You chose to update. You are protected for this period. Click submit to proceed to next day");
        $("#submit").show();


    }
    function B_click(){
        $("#ButtonA").hide();
        $("#ButtonB").hide();
        $("#Msg1").hide();
        $("#Msg3").show();
        $("#Decision").val("0");
        $("#Msg3").text("You chose to continue. Click submit to proceed to the next day");
        $("#submit").show();

    }
    function cond_run(){
        $('#submit').hide().delay(100).fadeIn(2200);
    }
    $(document).ready(function(){
        // Animate loader off screen
        <?php if($_SESSION["outcome"]<1){ ?>
        $('#ButtonA').hide().delay(100).fadeIn(2200);
        $('#ButtonB').hide().delay(100).fadeIn(2200);
        $('#Msg4').fadeOut(2000);
        <?php }else{ ?>
        $('#submit').hide().delay(100).fadeIn(2200);
        $('#Msg5').fadeOut(2000);
        <?php } ?>

    });

</script>

<body>
<div id="wrapperC">
    <h1>PERIOD <?php echo $period; ?></h1>
</div>

    <div id="wrapperL">
        <span style="font-size: large;">Day: <b><?php echo $day; ?></b></span> <br>


        <span style="font-size: large;">Current security status: <b>
                <?php if ($_SESSION["outcome"]==2 || $Attacked) {
                    echo "You have been attacked in this period (100 points lost)";
                } elseif ($_SESSION["outcome"]==1) {
                    echo "You are protected for this period";
                } else {
                    echo "No attacks so far in this period (0 points lost)";
                } ?>
            </b></span><br>
    </div>
    <br><br>
    <div id="wrapperC">
        <button id="1" class="round-button" <?php if($day==1){echo "style=\"color:##000000; background:#0000ff\"";} ?>>Day1</button>
        &nbsp
        <button id="2" class="round-button" <?php if($day==2){echo "style=\"color:##000000; background:#0000ff\"";} ?>>Day2</button>
        &nbsp
        <button id="3" class="round-button" <?php if($day==3){echo "style=\"color:##000000; background:#0000ff\"";} ?>>Day3</button>
        &nbsp
        <button id="4" class="round-button" <?php if($day==4){echo "style=\"color:##000000; background:#0000ff\"";} ?>>Day4</button>
        &nbsp
        <button id="5" class="round-button" <?php if($day==5){echo "style=\"color:##000000; background:#0000ff\"";} ?>>Day5</button>
        &nbsp
        <button id="6" class="round-button" <?php if($day==6){echo "style=\"color:##000000; background:#0000ff\"";} ?>>Day6</button>
        &nbsp
        <button id="7" class="round-button" <?php if($day==7){echo "style=\"color:##000000; background:#0000ff\"";} ?>>Day7</button>
        &nbsp
        <button id="8" class="round-button" <?php if($day==8){echo "style=\"color:##000000; background:#0000ff\"";} ?>>Day8</button>
        &nbsp
        <button id="9" class="round-button" <?php if($day==9){echo "style=\"color:##000000; background:#0000ff\"";} ?>>Day9</button>
        &nbsp
        <button id="10" class="round-button" <?php if($day==10){echo "style=\"color:##000000; background:#0000ff\"";} ?>>Day10</button>
        <br/><br/>

        <?php if ($_SESSION["outcome"]<1) { ?>
            <span style="font-size: large;">Security update status: <b><?php echo "Available"; ?></b></span> <br>
            <span style="font-size: large;">Today's cost of update: <b><?php if($UpdateCost>0){echo '-'.$UpdateCost;}else{echo $UpdateCost;} ?> Points</b></span><br>
        <?php } elseif ($_SESSION["outcome"]==2){ ?>
            <span style="font-size: large;">Security update status: </span> <label style="background-color: grey">   NA   </label> <br>
            <span style="font-size: large;">Today's cost of update: </span> <label style="background-color: grey">-<?php echo $UpdateCost; ?> Points</label> <br>
        <?php } elseif ($_SESSION["outcome"]==1) {?>
            <span style="font-size: large;">Security update status: </span> <b>Updated</b>   <br>
            <span style="font-size: large;">Today's cost of update: </span> <b>NA</b>  <br>
        <?php } ?>
        <br/><br/>


        <?php if($Attacked) { ?>
            <!-- <h2 style="color: red;">You have been attacked. You have lost 100 points from the attack.</h2> -->

                <Label id="Msg6" style="font-size: large; font-weight: bold; color: red; ">You have been attacked. The system will recover only in the next period.</Label>
                <h3>Click submit to proceed to the next day</h3>

                <?php } elseif($_SESSION["outcome"]>0){ ?>
                    <?php if($_SESSION["outcome"]==2){ ?>
                        <Label id="Msg6" style="font-size: large; font-weight: bold; color: red; ">You have been attacked. The system will recover only in the next period.</Label>
                    <?php } if($_SESSION["outcome"]==1){ ?>
                        <Label id="Msg7" style="font-size: large; font-weight: bold; color: darkgreen; ">You updated. You are protected for this period</Label>
                    <?php } ?>

                    <h3>Click submit to proceed to the next day</h3>
                    <Label id="Msg5" style="font-size: Medium; font-weight: bold; ">Loading......</Label><br/><br/>

        <?php } elseif($_SESSION["outcome"]<1) { ?>
        <Label id="Msg1" style="font-size: large; font-weight: bold;">Choose whether to update or continue.</Label>
        <Label id="Msg2" style="font-size: large; font-weight: bold; color: darkgreen; display: none; "></Label>
        <Label id="Msg3" style="font-size: large; font-weight: bold; color: brown; display: none; "></Label>
        <br/><br/>
        <Label id="Msg4" style="font-size: Medium; font-weight: bold; ">Loading......</Label>
        <br/><br/>
        <button id="ButtonA" class="btn-style" style="display: none;" onclick="A_click();return false;"> Update </button>
        &nbsp
        <button id="ButtonB" class="btn-style" style="display: none;" onclick="B_click();return false;">Continue</button>

    </div>
    <?php }  ?>


<form name="myForm" method="get" onsubmit="return onsubmitform();" action="Save.php">
    <div id="wrapperC">
        <input type="hidden" name="Period" value="<?php echo $period; ?>"/>
        <input type="hidden" name="Day" value="<?php echo $day; ?>"/>
        <input type="hidden" name="Cost" value="<?php echo $UpdateCost; ?>"/>
        <input type="hidden" id="Decision" name="Decision" value="0"/>
        <input type="hidden" name="Attacked" value="<?php if($Attacked){echo 100;}else{echo 0;} ?>"/>

        <?php if($Attacked || $_SESSION["outcome"]>0) { ?>
            <input type="submit" id="submit" name="submit" class="btn-style" value="Submit" />
        <?php } else { ?>
            <input type="submit" id="submit" name="submit" class="btn-style" value="Submit" style="display: none" />
        <?php } ?>

    </div>
</form>

</body>
</html>

