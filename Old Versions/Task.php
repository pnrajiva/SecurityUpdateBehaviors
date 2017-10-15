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
/*//Establish a database connection
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
}*/


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
    $_SESSION["payoff"] = 0;
}
//Close the database connection
//$conn->close();

//increment the day number
$day +=1;
$_SESSION["day"] = $day;


$nextday = $_SESSION["outcomeday"] + 1;

//get the cost of update for that day
$UpdateCost = 10;
if($day!=1){
    $rand1 = mt_rand(1,10000)/10000;
    if($rand1<0.15){
        $UpdateCost = 0;
    }
}

//on the second period - determine the day of attack
/*if($day==1 && $period==2){
    /*if($Condition == 1){
        $cost = array(9,9,9,9,9,9,9,9,9,9);
        $_SESSION["costlist"] = implode(",",$cost);
    }*/
//    else{
    //Calculate the attacks for each day in the period
   // $cost = array(0,0,0,0,1,0,0,0,0,0);
  //  shuffle($cost);
   // $_SESSION["costlist"] = implode(",",$cost);
//    }
//}


//Calculate the probability of being attacked
$Attacked = 0;
//for period 2
if($period==2){
    if($day==1){
        $cost = array(0,0,0,0,1,0,0,0,0);
        shuffle($cost);
        $_SESSION["costlist"] = implode(",",$cost);
    }
    else{
        $costlist = $_SESSION["costlist"];
        $costarray = explode(",",$costlist);
        $index = $day-2;
        $Attacked = $costarray[$index];
    }
}
if($period == 3){
    unset($_SESSION["costlist"]);
}
//for periods above 3
if($period>3){
    $rand = mt_rand(1,10000)/10000;
    if($day>1 and $_SESSION['outcome']<1){
        if($rand<=0.97){//change to 0.97
            $Attacked = 0;
        }
        else{
            $Attacked = 1;
        }
    }
    if($day>1 and $_SESSION['outcome']==2){
        if($rand<=0.99){//change to 0.99
            $Attacked = 0;
        }
        else{
            $Attacked = 1;
        }
    }
}
$rand1 = mt_rand(1,10000)/10000;
?>
<script type="text/javascript">

    $(document).ready(function(){
        $(window).bind("beforeunload", function(){ return(false); });
    });
    function onsubmitform() {
        $(window).unbind('beforeunload');
    }
    function A_click(){
        $("#Gamble").val("2");
        $("#ButtonA").html("2");
        //$("#MsgG").hide();
        $("#updateD").show();
        $("#MsgG").text("You earned");
        $("#ButtonA").prop("disabled",true);
        $("#ButtonB").prop("disabled",true);
        delaydisplay();

    }

    function B_click() {
        //var rand = Math.random();
        var rand = $("#randval").val();
        if(rand<0.5){
            $("#Gamble").val("0");
            $("#ButtonB").html("0");
        }
        else{
            $("#Gamble").val("4");
            $("#ButtonB").html("4");
        }
        //$("#MsgG").hide();
        $("#updateD").show();
        $("#MsgG").text("You earned");
        $("#ButtonA").prop("disabled",true);
        $("#ButtonB").prop("disabled",true);
        delaydisplay();
    }

    function U_click(){

        //disable the update and continue buttons
        //save the decision to hidden file
//        $("#ButtonA").hide();
//        $("#ButtonB").hide();
//        $("#Msg1").hide();
//        $("#Msg2").show();

        $("#Decision").val("1");
//        $("#Msg2").text("You chose to update. You are protected for this period. Click submit to proceed to next day");
//        $("#submit").show();


    }
    function C_click(){
//        $("#ButtonA").hide();
//        $("#ButtonB").hide();
//        $("#Msg1").hide();
//        $("#Msg3").show();

        $("#Decision").val("0");
//        $("#Msg3").text("You chose to continue. Click submit to proceed to the next day");
//        $("#submit").show();

    }
    function ins_click() {
        $("#ins").hide();
        $("#main").show();
        
    }
//    function cond_run(){
//        $('#submit').hide().delay(100).fadeIn(2200);
//    }
   // $(document).ready(function(){
        // Animate loader off screen
    function delaydisplay(){
        <?php if($_SESSION["outcome"]<1){ ?>
        $('#Update').hide().delay(1000).fadeIn(1200);
        $('#Continue').hide().delay(1000).fadeIn(1200);
        $('#Msg4').fadeOut(2000);
        <?php }else{ ?>
        $('#Continue').hide().delay(1000).fadeIn(1200);
        $('#Msg4').fadeOut(2000);
        <?php } ?>
    }
  //  });

</script>

<body>
<?php if($period==4 && $day==1){ ?>
    <div id="ins">
        <object type="text/html" data="Period3Ins.html" width="800px" height="400px">
        </object>
        <br/>
        <button id="ButtonIns" class="btn-style" onclick="ins_click();return false;">Continue</button>
        <br/>
    </div>
<div id="main" style="display: none">
<?php }else{ ?>

    <div id="main">
<?php } ?>


<div id="wrapperC">
    <h1>PERIOD <?php echo $period; ?></h1>
</div>

    <div id="wrapperL">
        <span style="font-size: large;">Day: <b><?php echo $day; ?></b></span> <br>


        <span style="font-size: large;">Current security status: <b>
                <?php if ($Attacked) { ?>
                     <span style="font-size: large; font-weight: bold; color: red; ">You incurred losses from a security event</span>
                   <?php //echo "You have been attacked in this period (100 points lost)";
                } elseif ($_SESSION["outcome"]==1 && $day==$nextday) {
                    //echo "You are protected for this period"; ?>
                 <span style="font-size: large; font-weight: bold; color: darkgreen; ">You updated. You are less likely to face a security event. </span>
              <?php  }
                /*elseif ($_SESSION["outcome"]==1 && $day!=$nextday) {
                    echo "Protected for this period";
                }*/
                elseif($_SESSION["outcome"]==2){
                    echo "You had a security event on day ".$_SESSION["outcomeday"]." in this period";
                }
                else {
                    echo "No security events so far in this period";
                } ?>
            </b></span><br>
    </div>

    <br><br>

    <div id="wrapperC">
        <div>
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
        </div>
        <span style="font-size: large;">Accumulated Payoff for this period: </span> <b><?php echo $_SESSION["payoff"]; ?></b>   <br>

        <?php if ($Attacked) { ?>
        <span style="font-size: large; font-weight: bold; color: red; ">Today you lost 100 points from a security event</span>
        <?php } ?>

        <?php if($_SESSION["outcome"]==1 && $day==$nextday){ ?>
        <span style="font-size: large; font-weight: bold; color: green; ">You spent <?php echo $UpdateCost; ?> points on update</span>
        <?php } ?>
        <br/>
        <!--        --><?php //if($Attacked) { ?>
<!---->
<!--            <span style="font-size: large;">Payoff for this day: </span> <span style="font-size: large; font-weight: bold; color: red; ">-100</span>   <br>-->
<!---->
<!---->
<!--        --><?php //} elseif($_SESSION["outcome"]>0){ ?>
<!--            --><?php //if($_SESSION["outcome"]==2){ ?>
<!--                <span style="font-size: large;">Accumulated Payoff: </span> <b>0</b>   <br>-->
<!---->
<!--            --><?php //} if($_SESSION["outcome"]==1 && $day==$nextday){ ?>
<!--                <span style="font-size: large;">Payoff for this day: </span> <span style="font-size: large; font-weight: bold; color: green; ">--><?php //echo "-".$UpdateCost; ?><!--</span>   <br>-->
<!---->
<!--            --><?php //}if($_SESSION["outcome"]==1 && $day!=$nextday){ ?>
<!---->
<!--                <span style="font-size: large;">Payoff for this day: </span> <b>0</b>   <br>-->
<!---->
<!--        --><?php //}}else{ ?>
<!--            <span style="font-size: large;">Payoff for this day: </span> <b>0</b>   <br>-->
<?php //} ?>
        <br/><br/>
</div>
<form name="myForm" method="get" onsubmit="return onsubmitform();" action="Save.php">
    <div id="wrapperC">
        <input type="hidden" name="Period" value="<?php echo $period; ?>"/>
        <input type="hidden" name="Day" value="<?php echo $day; ?>"/>
        <input type="hidden" name="Cost" value="<?php echo $UpdateCost; ?>"/>
        <input type="hidden" id="Gamble" name="Gamble" value="0"/>
        <input type="hidden" id="Decision" name="Decision" value="0"/>
        <input type="hidden" name="Attacked" value="<?php if($Attacked){echo 100;}else{echo 0;} ?>"/>
        <input type="hidden" id="randval" name="randval" value=<?php echo $rand1; ?> />

        <div id="gambleD" name="gambleD" >
            <Label id="MsgG" style="font-size: large; font-weight: bold;">Choose between A and B</Label>
            <br/>
            <br/>
            <button id="ButtonA" class="btn-style1" onclick="A_click();return false;">A</button>
            &nbsp
            <button id="ButtonB" class="btn-style1" onclick="B_click();return false;">B</button>
            <br/>

        </div>
        <div id="updateD" name="updateD" style="border: thick; display: none;">

            <?php if($period>3){ ?>
                <br/><br/><br/>

        <?php if($Attacked || $_SESSION["outcome"]>0) { ?>
            <Label id="Msg1" style="font-size: large; font-weight: bold;">Click continue to proceed to the next day</Label>
            <br/>
                  <?php  if ($_SESSION["outcome"]>1){ ?>
                    <span style="font-size: large;">Update status: </span> <label style="background-color: grey">   NA   </label> <br>
                    <span style="font-size: large;">Today's cost of update: </span> <label style="background-color: grey"><?php echo $UpdateCost; ?> Points</label> <br>
                <?php } elseif ($_SESSION["outcome"]==1) {?>
                    <span style="font-size: large;">Update status: </span> <b>Updated</b>   <br>
                    <span style="font-size: large;">Today's cost of update: </span> <b>NA</b>  <br>
                <?php } ?>
                    <br/>
            <Label id="Msg4" style="font-size: Medium; font-weight: bold; ">Loading......</Label>
            <br/><br/>
            <input type="submit" id="Continue" name="Continue" onclick="C_click();" class="btn-style" value="Continue" />

        <?php } else { ?>
            <Label id="Msg1" style="font-size: large; font-weight: bold;">Would you like to <i>update</i> or <i>continue</i> without updating.</Label>
            <br/>
                    <span style="font-size: large;">Update status: <b><?php echo "Available"; ?></b></span> <br>
                    <span style="font-size: large;">Today's cost of update: <b><?php if($UpdateCost>0){echo $UpdateCost;}else{echo $UpdateCost;} ?> Points</b></span><br>
                    <br/>
            <Label id="Msg4" style="font-size: Medium; font-weight: bold; ">Loading......</Label>
            <br/><br/>
            <input type="submit" id="Update" name="Update" onclick="U_click();" class="btn-style" value="Update" />
            <input type="submit" id="Continue" name="Continue" onclick="C_click();" class="btn-style" value="Continue" />
        <?php } } else { ?>
                <br/>
                <Label id="Msg4" style="font-size: Medium; font-weight: bold; ">Loading......</Label>
                <br/>
                <input type="submit" id="Continue" name="Continue" class="btn-style" value="Submit" />
            <?php } ?>
        </div>
    </div>
</form>
</div>
</body>
</html>

