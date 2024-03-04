<?php

$Manually_Connect = true;

ob_start();
// echo "hi";
// print_r($_POST);
//print_r(phpinfo());

$content = ob_get_contents();
ob_end_clean();
file_put_contents('log.txt', $content, FILE_APPEND);

if(!isset($_POST['PM_MAN_DATE']) ){
	echo " Work Order PM Generate Date is not set ";
	exit();
}

if(!isset($_POST['Key_Building_Number']) ){
	echo " Work Order Buidling ID is not set ";
	exit();
}

if(!isset($_POST['User_Id']) ){
	echo " Work Order User ID is not set ";
	exit();
}

if(!isset($_POST['Cost_Center_Number']) ){
	echo " Work Order Cost Center is not set ";
	exit();
}

echo $Where_Building_ID = $_POST['Key_Building_Number'];
echo $Where_Cost_Center = $_POST['Cost_Center_Number'];
$Where_WO_Date = new DateTime($_POST['PM_MAN_DATE']);


echo "PM Date " . $_POST['PM_MAN_DATE'] . "</br>";

include_once ("daily_pm_job.php"); 
?>