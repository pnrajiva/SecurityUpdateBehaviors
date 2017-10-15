<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="Style.css">
    <script src="jquery-3.1.0.min.js"></script>

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
// Load Experiment configuration as an array.
$Exp_config = parse_ini_file('ExperimentConfiguration.ini');
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
    //Reset the rest of the variables - to start a new period
    $_SESSION["period"] = $period;
    $day = 0;
    $_SESSION["outcome"]=0;
    $_SESSION["outcomeday"] = 0;
    $_SESSION["payoff"] = $Exp_config['InitialPayoff'];
}

//increment the day number
$day +=1;
$_SESSION["day"] = $day;
$nextday = $_SESSION["outcomeday"] + 1;

//get the cost of update for that day
$UpdateCost = $Exp_config['HighUpdateCost']; //Default is the high update cost

if($day!=1){ // For days greater than 1, there is a chance for lower cost
    $rand1 = mt_rand(1,10000)/10000;
    if($rand1<$Exp_config['PDiscount']){  //there is a probability of low update cost based on Pdiscount - which varies by condition
        $UpdateCost = $Exp_config['LowUpdateCost']; //set low update cost
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


///////////////////////////Calculate the probability of being attacked
$Attacked = 0; //default is 0

################ Commented the below to keep all periods identical ############

////not attacked during period 1
////for period 2
//if($period==2){ //in period 2 we randomly choose a day to be attacked
//    if($day==1){ //cannot be attacked on Day 1
//        $cost = array(0,0,0,0,1,0,0,0,0);
//        shuffle($cost); //shuffle the list to make the decision later
//        $_SESSION["costlist"] = implode(",",$cost);
//    }
//    else{
//        $costlist = $_SESSION["costlist"];
//        $costarray = explode(",",$costlist);
//        $index = $day-2;
//        $Attacked = $costarray[$index];
//    }
//}
//
////not attacked during period 3 remove the list from session
//if($period == 3){
//    unset($_SESSION["costlist"]);
//}

//for periods above 3
//if($period>3){
    ###### Above parts are commented to keep all periods identical #################

$rand = mt_rand(1,10000)/10000;//get a random value between 0 and 1

if($day>1){ //cannot be attacked on Day 1
        if($_SESSION['outcome']==1){ //if updated, some probability of being attacked - based on experiment variables
            if($rand<=(1-$Exp_config['PAttackProtection'])){
                $Attacked = 0;
            }
            else{
                $Attacked = 1;
            }
        }
        else{
            if($rand<=(1-$Exp_config['PAttack'])){    //if not updated, probability of being attacked (irrespective of whether attacked earlier)
                $Attacked = 0;
            }
            else{
                $Attacked = 1;
            }
        }
}
//}
///////////////////

$rand1 = mt_rand(1,10000)/10000;
$onupdate = $_SESSION["payoff"]-$UpdateCost;
$onattack = $_SESSION["payoff"]-$Exp_config['Loss'];
?>

<script type="text/javascript">

    $(document).ready(function(){
        $(window).bind("beforeunload", function(){ return(false); });
        $('form').submit(function (e) {
            $(window).unbind('beforeunload');
            $("#ButtonA").prop("disabled", true);
            $("#ButtonB").prop("disabled", true);
            $("#ButtonA").fadeTo("fast",0.33);
            $("#ButtonB").fadeTo("fast",0.33);
            $('#lossFeedback').hide();
            var form = this;
            e.preventDefault();
            setTimeout(function () {
                form.submit();
            }, 500); // in milliseconds

        });
    });
    /*function onsubmitform(e) {
        $(window).unbind('beforeunload');

    }*/




    function A_click(){
        $("#Gamble").val("2");
//        $("#ButtonA").hide();
      //  $("#ButtonA").prop("disabled", true);
//        $("#ButtonA").fadeTo( "slow", 0.01 );
//        $("#ButtonB").hide();
       // $("#ButtonB").prop("disabled", true);
//        $("#ButtonB").fadeTo( "slow", 0.01 );
        $("#ypayoffA").hide();
        $("#ypayoffB").hide();
        //$('#ucostA').hide();
        $('#Msg4').show();
        $('#Update').prop("disabled",true);
        //$('#test').text("Choice: A Payoff: 2");
        $('#test').html("Payoff: <b>2</b>");
        //$('#test').delay(5000).fadeIn(2000);
        $('#test').show();
        //delay(5000);
    }

    function B_click() {
        var rand = $("#randval").val();
        if(rand<0.5){
            $("#Gamble").val("0");
            $('#test').html("Payoff: <b>0</b>");
        }
        else{
            $("#Gamble").val("4");
            $('#test').html("Payoff: <b>4</b>");
        }
//        $("#ButtonA").hide();
//        $("#ButtonB").hide();
       // $("#ButtonA").prop("disabled", true);
//        $("#ButtonA").fadeTo( "slow", 0.01 );
       // $("#ButtonB").prop("disabled", true);
//        $("#ButtonB").fadeTo( "slow", 0.01 );
        $("#ypayoffA").hide();
        $("#ypayoffB").hide();
        //$('#ucostA').hide();
        $('#Msg4').show();
        $('#Update').prop("disabled",true);
        //$('#test').delay(5000).fadeIn(2000);
        $('#test').show();
        //delay(5000);
    }

    function U_click(){
        $("#Decision").val("1");
        $("#Update").hide();
        //$("#Msg1").hide();
        $("#ucost").hide();
        $("#ucostA").show();
        $("#status").html("Update Status: Updated");
        $("#accpayoff").hide();
        $("#accpayoff1").show();
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
   $(document).ready(function(){
       $('#ButtonA').hide().delay(50).fadeIn(50);
        $('#ButtonB').hide().delay(50).fadeIn(50);
//        $('#ypayoffB').hide().delay(400).fadeIn(100);
//        $('#ypayoffA').hide().delay(400).fadeIn(100);
        $('#Msg4').fadeOut(100);
    });

</script>

<body>
<?php //if($period==4 && $day==1){ ?>
<!--    <div id="ins">-->
<!--        <br/><br/><br/><br/>-->
<!--        <div id="wrapperC" style="font-size: x-large;">Good news: Software updates will be made available now!!</div>-->
<!--        <div id="wrapperC">-->
<!--            <p style="font-size: x-large;">-->
<!--                --><?php //if($Condition==1){ //Fixed cost?>
<!--                    <b>Remember:</b> The cost of an update is 9.5 points. <br/>-->
<!--                    If you choose to update, your chance of confronting a security failure reduces from 3% to 1%.-->
<!--                --><?php //} ?>
<!---->
<!--                --><?php //if($Condition==2){ //Variable Cost?>
<!--                <b>Remember:</b> The cost of an update is most often 10 points (85% of the time); but sometimes (15% of the time), an update may be available for free (0 points). <br/>-->
<!--                If you choose to update, your chance of confronting a security failure reduces from 3% to 1%.-->
<!--                --><?php //} ?>
<!---->
<!--            </p>-->
<!--        </div>-->
<!--        <br/>-->
<!--        <button id="ButtonIns" class="btn-style" onclick="ins_click();return false;">Proceed</button>-->
<!--        <br/>-->
<!--    </div>-->
<!--<div id="main" style="display: none">-->
<?php //}else{ ?>
<?php //} ?>


<div id="main">
<div id="wrapperC">
    <h1>PERIOD <?php echo $period; ?></h1>
</div>
    <br/>
    <div id="wrapperC">
        <?php if($Attacked){ ?>
            <span id="accpayoff" style="font-size: 16pt;">POINTS you accumulated in this period: <b><?php echo $onattack; ?> points</b></span>
            <span id="accpayoff1" style="font-size: 16pt;display: none;">POINTS you accumulated in this period: <b><?php echo $onupdate; ?> points</b></span>
            <br/><br/>
        <?php }else{?>
            <span id="accpayoff" style="font-size: 16pt;">POINTS you accumulated in this period: <b><?php echo $_SESSION["payoff"]; ?> points</b></span>
            <span id="accpayoff1" style="font-size: 16pt;display: none;">POINTS you accumulated in this period: <b><?php echo $onupdate; ?> points</b></span>
            <br/><br/>
        <?php } ?>
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
    </div>

<form name="myForm" method="get" action="Save_parallel.php">
    <div id="wrapperC">
        <input type="hidden" name="Period" value="<?php echo $period; ?>"/>
        <input type="hidden" name="Day" value="<?php echo $day; ?>"/>
        <input type="hidden" name="Cost" value="<?php echo $UpdateCost; ?>"/>
        <input type="hidden" id="Gamble" name="Gamble" value="0"/>
        <input type="hidden" id="Decision" name="Decision" value="0"/>
        <input type="hidden" name="Attacked" value="<?php if($Attacked){echo $Exp_config['Loss'];}else{echo 0;} ?>"/>
        <input type="hidden" id="randval" name="randval" value=<?php echo $rand1; ?> />

<!--        --><?php //if($period>3){ ?>
            <div id="updateD" name="updateD">
                    <?php if($Attacked || $_SESSION["outcome"]>0) { ?>
                        <?php  if ($_SESSION["outcome"]>1 || $Attacked){ ?>
                            <span style="font-size: large;">Update status: </span> <label style="background-color: grey">   NA   </label> <br/>
                            <span style="font-size: large;">Cost: </span> <label style="background-color: grey"><?php echo $UpdateCost; ?> Points</label> <br/>
                        <?php } elseif ($_SESSION["outcome"]==1) { ?>
                            <span style="font-size: large;">Update status: </span> <b>Updated</b>   <br>
                            <span style="font-size: large; background-color: grey">Cost: <?php echo $UpdateCost; ?> Points</span> <br/>
                        <?php } ?>
                    <?php } else { ?>
                         <!--<Label id="Msg1" style="font-size: large; font-weight: bold;">Would you like to <i>update</i> today?</Label><br/>-->
                        <span id="status" style="font-size: large;">Update status: <b><?php echo "Available"; ?></b></span> <br>
                        <span id="ucost" style="font-size: large;">Cost: <b><?php if($UpdateCost>0){echo $UpdateCost;}else{echo $UpdateCost;} ?> Points</b></span><br>
                        <span id="ucostA" style="font-size: large; color: green; display: none;">You paid <b><?php if($UpdateCost>0){echo $UpdateCost;}else{echo $UpdateCost;} ?> points to update</b></span><br>
                        <button id="Update" class="btn-style1" onclick="U_click(); return false;" class="btn-style1">Update</button> <br/>
                    <?php } ?>
            </div>
<!--        --><?php //} ?>

        <div id="gambleD" name="gambleD" >
            <br/>
            <Label id="MsgG" style="font-size: x-large; font-weight: bold;">Choose between A and B</Label>
            <br/>
            <Label id="Msg4" style="font-size: Medium; font-weight: bold; "></Label>
            <br/><br/>
            <input type="submit" id="ButtonA" name="ButtonA"  onclick="A_click();" class="btn-style" value="A" style="Display: none;" /> &nbsp; &nbsp;
            <input type="submit" id="ButtonB" name="ButtonB"  onclick="B_click();" class="btn-style" value="B" style="Display: none;" />
            <br/><br/>
        </div>
        <!-- <label id="test" style="font-size: large; font-style: italic; display: none"></label> <br/><br/> -->
        <span id="test" style="font-size: large; font-style: italic; display: none"></span> <br/><br/>
        <?php /*if($day>1){
            if($_SESSION["DayPayoff"]==2) { */?><!--
            <label id="ypayoffA" style="font-size: large;Display: none;">You chose <b>A</b> yesterday and your payoff was <b><?php /*echo $_SESSION["DayPayoff"]; */?> points</b> </label>
                <br/>
        <?php /*} if($_SESSION["DayPayoff"]==0||$_SESSION["DayPayoff"]==4) { */?>
                <label id="ypayoffB" style="font-size: large;Display: none; ">You chose <b>B</b> yesterday and your payoff was  <b><?php /*echo $_SESSION["DayPayoff"]; */?> points</b> </label>
                <br/>
      --><?php /* } } */?>

        <?php if ($Attacked) { ?>
            <span id="lossFeedback" style="font-size: x-large; font-weight: bold; color: red; ">A security failure has occurred!!! You lost <?php echo $Exp_config['Loss'] ?> points &#9785;</span>
            <br/>
        <?php } ?>
    </div>
</form>
</div>
</body>
</html>

