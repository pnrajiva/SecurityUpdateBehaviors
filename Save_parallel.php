<?php
session_start();
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

//Get the UserID
$ID = $_SESSION["workerId"];
//Get the Condition value
$Condition = $_SESSION["Condition"];
//get the period number
$period = $_GET["Period"];
//get the trial number
$Day = $_GET["Day"];
//get the cost
$UpdateCost = $_GET["Cost"];
//get the Attack Loss
$AttackLoss = $_GET["Attacked"];
//get the decision
$Decision = $_GET["Decision"];
//get the gamble
$Gamble = $_GET["Gamble"];
//get the previous day
$prevday = $Day - 1;

$_SESSION["DayPayoff"]=$Gamble;

//get the current endowment value
if(($period==1) && ($Day==1)){ //if period 1 and day 1 set the endowment to 1000
    $endowment = $Exp_config['Endowment'];
}
else{ //if > Day 1, get the endowment from previous trial
    $sql = "SELECT CurEndowment FROM UpdateDecisions WHERE UserID='$ID' ORDER BY ID DESC LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $endowment = $row["CurEndowment"];
    } else {
        echo "Something is wrong. No results";
    }
}


$costofupdating = 0;
if($Decision==1)
{
    $costofupdating = $UpdateCost;
}

$lossfromattack = 0;
if($AttackLoss == $Exp_config['Loss']){
    $lossfromattack = $AttackLoss;
}

//calculate endowment
$endowment = $endowment - $costofupdating - $lossfromattack + $Gamble;
//Update endowment
$_SESSION["endow"] = $endowment;
$_SESSION["payoff"] = $_SESSION["payoff"] - $costofupdating - $lossfromattack + $Gamble;


$sqlcheck = "SELECT COUNT(*) FROM UpdateDecisions WHERE UserID='$ID' AND Period='$period' AND Day='$Day';";
$resultcheck = $conn->query($sqlcheck);
$num = $resultcheck->fetch_row();

//Insert the decision to master list
/*if ($AttackLoss == 100){
    $sql="UPDATE UpdateDecisions SET AttackLoss=100, CurEndowment='$endowment' WHERE UserID='$ID' AND Period='$period' AND Day='$prevday';";

}else{*/
if($num[0]==0) {
    $sql = "INSERT INTO UpdateDecisions (UserID, ExpCondition, Period, Day, Decision, UpdateCost, CostIncurred, AttackLoss, CurEndowment, Gamble) VALUES ('$ID','$Condition','$period','$Day','$Decision','$UpdateCost','$costofupdating','$AttackLoss','$endowment','$Gamble');";
//}

//if the decision was successfully inserted
    if ($conn->query($sql) === TRUE) {

        $_SESSION["day"] = $Day;
        //if chosen to update
        if ($Decision == 1) {
            $sql = "INSERT INTO DecisionSummary (UserID, ExpCondition, Period, Day, Decision, UpdateCost, CostIncurred, AttackLoss, CurEndowment, Gamble) VALUES ('$ID','$Condition','$period','$Day','$Decision','$UpdateCost','$costofupdating','$AttackLoss','$endowment','$Gamble');";
            $result1 = $conn->query($sql);
            $_SESSION["period"] = $period;
            $_SESSION["outcome"] = 1;
            $_SESSION["outcomeday"] = $Day;
            $_SESSION["outcomecost"] = $costofupdating;
        } else if ($AttackLoss == $Exp_config['Loss']) {
            $sql = "INSERT INTO DecisionSummary (UserID, ExpCondition, Period, Day, Decision, UpdateCost, CostIncurred, AttackLoss, CurEndowment, Gamble) VALUES ('$ID','$Condition','$period','$Day','$Decision','$UpdateCost','$costofupdating','$AttackLoss','$endowment','$Gamble');";
            $result1 = $conn->query($sql);
            $_SESSION["period"] = $period;
            if($_SESSION["outcome"] == 1){
                $_SESSION["outcome2"]=1;
                $_SESSION["outcomeday2"]= $_SESSION["outcomeday"];
                $_SESSION["outcome"] = 2;
            }else{
                $_SESSION["outcome"] = 2;
            }
            $_SESSION["outcomeday"] = $Day;
            $_SESSION["outcomecost"] = $lossfromattack;
        } else if ($_SESSION['outcome'] < 1 && $Day == 10) {
            $sql = "INSERT INTO DecisionSummary (UserID, ExpCondition, Period, Day, Decision, UpdateCost, CostIncurred, AttackLoss, CurEndowment, Gamble) VALUES ('$ID','$Condition','$period','$Day','$Decision','$UpdateCost','$costofupdating','$AttackLoss','$endowment','$Gamble');";
            $result1 = $conn->query($sql);
            $_SESSION["period"] = $period;
        }
    } else {
        echo "Error: Inform the experimenter";
    }
}
$conn->close();
//if($endowment<0){
//    $endowment =0;
//}
if($_SESSION["outcome"]==1 && $Day==10){

    if($period==20){ //end of the entire 360 days
                //if end of the last period
                // display the endowment value..with a submit button to redirect to the final page
            ?>
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
                <body>
                <form name="myForm" method="get" onsubmit="return onsubmitform();" action="ThankYou.php">
                    <div id="wrapperC"><br><br><br><br><br>
                        <br>
                        <div style="border: dotted;">
                            <br/>
                            <!-- <span style="font-size: large;">Your payoff for yesterday: <b><?php echo $Gamble; ?></b></span><br> -->
                            <span style="font-size: large;">You completed period <b><?php echo $period; ?></b>.</span><br>
                            <span style="font-size: large;">TOTAL POINTS you have up to this point: <b><?php echo $endowment; ?></b> points</span><br/>
                            <span style="font-size: large;">You chose to update on <b>Day <?php echo $_SESSION["outcomeday"]; ?></b> in this period.</span><br>
                            <span style="font-size: large;">You spent <b><?php echo $_SESSION["outcomecost"]; ?></b> points to update </span><br>
                            <span style="font-size: large; color: green;">Fortunately, you did <b><u>not</u></b> confront any security failure in this period!! &#9786;</span><br/>
                            <br/>
                            <input type="submit" name="submit" class="btn-style" value="Proceed" />
                            <br/><br/>
                        </div>
                    </div>
                </form></body></html>


            <?php } else { ?>
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
                <body>
                <form name="myForm" method="get" onsubmit="return onsubmitform();" action="Task_parallel.php">
                    <div id="wrapperC"><br><br><br><br><br>
                        <br>
                        <div style="border: dotted;">
                            <br/>
                            <!-- <span style="font-size: large;">Your payoff for yesterday: <b><?php echo $Gamble; ?></b></span><br> -->
                            <span style="font-size: large;">You completed period <b><?php echo $period; ?></b>.</span><br>
                            <span style="font-size: large;">TOTAL POINTS you have up to this point: <b><?php echo $endowment; ?></b> points</span><br/>
                            <span style="font-size: large;">You chose to update on <b>Day <?php echo $_SESSION["outcomeday"]; ?></b> in this period.</span><br>
                            <span style="font-size: large;">You spent <b><?php echo $_SESSION["outcomecost"]; ?></b> points to update </span><br>
                            <span style="font-size: large; color: green;">Fortunately, you did <b><u>not</u></b> confront any security failure in this period!! &#9786;</span><br/>
                            <br/>
                            <input type="submit" name="submit" class="btn-style" value="Proceed to the next period" />
                            <br/><br/>
                        </div>
                    </div>
                </form></body></html>
                <?php
            }
    }
    else if($_SESSION["outcome"]==2 && $Day==10 ){ //if incurred an attack



        if($period==20){ //end of the entire 200 days
        //if end of the last period
        // display the endowment value..with a submit button to redirect to the final page
        ?>
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
            <body>
            <form name="myForm" method="get" onsubmit="return onsubmitform();" action="ThankYou.php">
                <div id="wrapperC"><br><br><br><br><br>
                    <br>
                    <div style="border: dotted;">
                        <br/>
                        <!-- <span style="font-size: large;">Your payoff for yesterday: <b><?php echo $Gamble; ?></b></span><br> -->
                        <span style="font-size: large;">You completed period <b><?php echo $period; ?></b></span><br>
                        <span style="font-size: large;">TOTAL POINTS you have up to this point: <b><?php echo $endowment; ?></b> points</span><br/>
<!--                        <span style="font-size: large;">You had a security event on day <b>--><?php //echo $_SESSION["outcomeday"]; ?><!--</b> in this period </span><br>-->
<!--                        <span style="font-size: large;">You lost <b>--><?php //echo $_SESSION["outcomecost"]; ?><!--</b> points</span><br>-->
<!--<!--                        <span style="font-size: large;">You have <b>-->-<?php ////echo $endowment; ?><!--<!--</b> points remaining</span><br>-->
                        <?php if($_SESSION["outcome2"]==1){ ?>
                            <span style="font-size: large;">You chose to update on <b>Day <?php echo $_SESSION["outcomeday2"]; ?></b> in this period.</span><br>
                        <?php } ?>
                        <span style="font-size: large; color: red;">Unfortunately, you confronted a security failure in this period and you lost 100 points!! &#9785;</span><br/>

                        <br/>
                        <input type="submit" name="submit" class="btn-style" value="Proceed" />
                        <br/><br/>
                    </div>
                </div>
            </form></body></html>


        <?php } else {?>
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
            <body>
        <form name="myForm" method="get" onsubmit="return onsubmitform();" action="Task_parallel.php">
            <div id="wrapperC"><br><br><br><br><br>
                <br>
                <div style="border: dotted;">
                    <br/>
                    <!-- <span style="font-size: large;">Your payoff for yesterday: <b><?php echo $Gamble; ?></b></span><br> -->
                    <span style="font-size: large;">You completed period <b><?php echo $period; ?></b></span><br>
                    <span style="font-size: large;">TOTAL POINTS you have up to this point: <b><?php echo $endowment; ?></b> points</span><br/>
<!--                    <span style="font-size: large;">You had a security event on day <b>--><?php //echo $_SESSION["outcomeday"]; ?><!--</b> in this period </span><br>-->
<!--                    <span style="font-size: large;">You lost <b>--><?php //echo $_SESSION["outcomecost"]; ?><!--</b> points </span><br>-->
                    <?php if($_SESSION["outcome2"]==1){ ?>
                        <span style="font-size: large;">You chose to update on <b>Day <?php echo $_SESSION["outcomeday2"]; ?></b> in this period.</span><br>
                    <?php } ?>
                    <span style="font-size: large; color: red;">Unfortunately, you confronted a security failure in this period and you lost 100 points!! &#9785;</span><br/>
<!--                <span style="font-size: large;">You have <b>--><?php //echo $endowment; ?><!--</b> points remaining</span><br>-->
                    <br/>
                    <input type="submit" name="submit" class="btn-style" value="Proceed to the next period" />
                    <br/><br/>
                    </div>
            </div>
        </form></body></html>

        <?php  }
    }
    else if($_SESSION["outcome"]<1 && $Day==10){ //end of 10 days
        //$sql = "INSERT INTO DecisionSummary (UserID, ExpCondition, Period, Day, Decision, UpdateCost, CostIncurred, AttackLoss, CurEndowment) VALUES ('$ID','$Condition','$period','$Day','$Decision','$UpdateCost','$costofupdating','$AttackLoss','$endowment');";
        //$result1 = $conn->query($sql);
        $_SESSION["period"]=$period;

        if($period==20){ //end of the entire 360 days
            //if end of the last period
            // display the endowment value..with a submit button to redirect to the final page
            ?>
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
            <body>

            <form name="myForm" method="get" onsubmit="return onsubmitform();" action="ThankYou.php">
                <div id="wrapperC"><br><br><br><br><br>
                    <br>
                    <div style="border: dotted;">
                        <br/>
                        <span style="font-size: large;">You completed period <b><?php echo $period; ?></b></span><br/>
                        <span style="font-size: large;">TOTAL POINTS you have up to this point: <b><?php echo $endowment; ?></b> points</span><br/>
                        <?php if(!($period<4)){ ?>
                            <span style="font-size: large;">You did <b><u>not</u></b> update in this period.</span><br/>
                        <?php }?>
                        <span style="font-size: large; color: green;">Fortunately, you did <b><u>not</u></b> confront any security failure in this period!! &#9786;</span><br/>
                        <br/>
                        <input type="submit" name="submit" class="btn-style" value="Proceed" />
                        <br/><br/>
                    </div>
                </div>
            </form></body></html>


            <?php
        }
        else
        {
            //if end of a particular period
            //display the endowment value..with a submit button to redirect to the task page
            ?>
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
            <body>

            <form name="myForm" method="get" onsubmit="return onsubmitform();" action="Task_parallel.php">
                <div id="wrapperC"><br><br><br><br><br>
                    <br/>
                    <div style="border: dotted;">
                        <br/>
                        <span style="font-size: large;">You completed period <b><?php echo $period; ?></b></span><br/>
                        <span style="font-size: large;">TOTAL POINTS you have up to this point: <b><?php echo $endowment; ?></b> points</span><br/>
                        <?php if(!($period<4)){ ?>
                            <span style="font-size: large;">You did <b><u>not</u></b> update in this period.</span><br/>
                        <?php }?>
                        <span style="font-size: large; color: green;">Fortunately, you did <b><u>not</u></b> confront any security failure in this period!! &#9786;</span><br/>
                        <br/>
                        <input type="submit" name="submit" class="btn-style" value="Proceed to the next period" />
                        <br/><br/>
                    </div>
                </div>
            </form></body></html>
        <?php  }
    }
    else{
        header('location:Task_parallel.php');
        exit; ?>
<?php }  ?>