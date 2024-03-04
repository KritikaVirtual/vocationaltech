<?php
$servername = "localhost";
$username = "ve";
$password = "Vision@4424"; 
$dbname = "ve_crm_new";


$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully <br><br>";

mysqli_query($conn,"TRUNCATE TABLE `meters_BillingMonth`");
mysqli_query($conn,"TRUNCATE TABLE `meters_Meter`");
mysqli_query($conn,"TRUNCATE TABLE `meters_MeterDetails`");
mysqli_query($conn,"TRUNCATE TABLE `meters_MeterReading`");
mysqli_query($conn,"TRUNCATE TABLE `meters_MeterUtility`");       // Clear History, does not get updated
mysqli_query($conn,"TRUNCATE TABLE `meters_UtilityCompany`");
mysqli_query($conn,"TRUNCATE TABLE `meters_WO_Ten_Link`");


$sSql = "INSERT INTO `meters_BillingMonth` (`BillingMonth_ID`, `BillingMonth_Date`) VALUES
(1, '2021-01-01'),
(2, '2021-02-01'),
(3, '2021-03-01'),
(4, '2021-04-01'),
(5, '2021-05-01'),
(6, '2021-06-01'),
(7, '2021-07-01'),
(8, '2021-08-01'),
(9, '2021-09-01'),
(10, '2021-10-01'),
(11, '2021-11-01')";
if($conn->query($sSql) === TRUE) {
	echo "Update meters_BillingMonth from 001 to 012 <br>";
} else {
	echo "*********************************************************************************************************************************</br>";
	echo "Error - meters_BillingMonth : " . $conn->error;
	echo "</br>*********************************************************************************************************************************</br>";
}

$sSql = "INSERT INTO `meters_Meter` (`Meter_ID`, `Building_ID`, `uniqueCostCenter`, `MeterNameNumber`, `MeterLocation`, `MeterIntialNumber`, `MeterMultiplyer`, `MeterNew`, `MeterTurnOver`, `MeterDeleted`, `UtilityCompany_ID`, `MeterSortNumber`, `MeterInitialDate`, `MeterBillable`, `MeterConversion`, `IsParent`, `Parent_MeterID`, `IsChild`, `Child_Install_Date`) VALUES
(1, 72, 1581178031, 'Meter-001', 'First Floor', 95250, 1, 1, 99999, 0, 1, 1, '2021-10-01', 1, 109, 0, 0, 0, NULL),
(2, 72, 1581178031, 'Meter-002', 'Second Floor', 2500, 1, 1, 99999, 0, 1, 2, '2021-10-01', 1, 109, 0, 0, 0, NULL),
(3, 72, 1581178031, 'Meter-002', 'Second Floor', 2500, 1, 1, 99999, 0, 1, 3, '2021-10-01', 1, 109, 0, 0, 0, NULL),
(4, 44, 1460335468, 'Main Meter', 'Main First Floor', 2500, 1, 1, 99999, 0, 2, 1, '2021-10-01', 1, 109, 1, 0, 0, NULL),
(5, 44, 1460335468, 'Sub - 001', 'Main - Sub Meter 1', 2500, 1, 1, 99999, 0, 2, 2, '2021-10-01', 1, 109, 0, 5, 1, NULL),
(6, 44, 1460335468, 'Sub 0 002', 'Main  - Sub Meter 2', 2541, 1, 1, 99999, 0, 2, 3, '2021-10-01', 1, 109, 0, 5, 1, NULL)";
if($conn->query($sSql) === TRUE) {
	echo "Update meters_Meter from 001 to 012 <br>";
} else {
	echo "*********************************************************************************************************************************</br>";
	echo "Error - meters_Meter : " . $conn->error;
	echo "</br>*********************************************************************************************************************************</br>";
}

$sSql = "INSERT INTO `meters_MeterDetails` (`MeterDetail_ID`, `CompanyID`, `Meter_ID`, `uniqueCostCenter`, `MultyMeterPercentage`, `Building_ID`) VALUES
(1, 648, 1, 1581178031, 25, 72),
(2, 649, 1, 1581178031, 50, 72),
(3, 647, 1, 1581178031, 25, 72),
(4, 653, 2, 1581178031, 100, 72),
(5, 663, 3, 1581178031, 100, 72),
(6, 364, 4, 1460335468, 100, 44),
(10, 191, 5, 1460335468, 50, 44),
(9, 189, 5, 1460335468, 50, 44),
(12, 438, 6, 1460335468, 100, 44)";
if($conn->query($sSql) === TRUE) {
	echo "Update meters_MeterDetails from 001 to 012 <br>";
} else {
	echo "*********************************************************************************************************************************</br>";
	echo "Error - meters_MeterDetails : " . $conn->error;
	echo "</br>*********************************************************************************************************************************</br>";
}

$sSql = "INSERT INTO `meters_MeterReading` (`MeterReading_ID`, `mrMeterReading`, `MeterUtility_ID`, `Meter_ID`, `mrTurnOver`, `mrOldReading`, `mrTakenBY`, `mrEnteredBY`, `BillingMonth_ID`, `mrUsage`, `Building_ID`, `uniqueCostCenter`, `MeterReadingEntered`	) VALUES
(1, 0, 1, 1, 0, 0, 'Auto Entry', 'Meter Program', 1, 0, 72, 1581178031, 'Y'),
(2, 0, 1, 1, 0, 0, 'Auto Entry', 'Meter Program', 2, 0, 72, 1581178031, 'Y'),
(3, 0, 1, 1, 0, 0, 'Auto Entry', 'Meter Program', 3, 0, 72, 1581178031, 'Y'),
(4, 0, 1, 1, 0, 0, 'Auto Entry', 'Meter Program', 4, 0, 72, 1581178031, 'Y'),
(5, 0, 1, 1, 0, 0, 'Auto Entry', 'Meter Program', 5, 0, 72, 1581178031, 'Y'),
(6, 0, 1, 1, 0, 0, 'Auto Entry', 'Meter Program', 6, 0, 72, 1581178031, 'Y'),
(7, 0, 1, 1, 0, 0, 'Auto Entry', 'Meter Program', 7, 0, 72, 1581178031, 'Y'),
(8, 0, 1, 1, 0, 0, 'Auto Entry', 'Meter Program', 8, 0, 72, 1581178031, 'Y'),
(9, 0, 1, 1, 0, 0, 'Auto Entry', 'Meter Program', 9, 0, 72, 1581178031, 'Y'),
(10, 95250, 1, 1, 0, 95250, 'Auto Entry', 'Meter Program', 10, 0, 72, 1581178031, 'Y'),
(11, 0, 1, 2, 0, 0, 'Auto Entry', 'Meter Program', 1, 0, 72, 1581178031, 'Y'),
(12, 0, 1, 2, 0, 0, 'Auto Entry', 'Meter Program', 2, 0, 72, 1581178031, 'Y'),
(13, 0, 1, 2, 0, 0, 'Auto Entry', 'Meter Program', 3, 0, 72, 1581178031, 'Y'),
(14, 0, 1, 2, 0, 0, 'Auto Entry', 'Meter Program', 4, 0, 72, 1581178031, 'Y'),
(15, 0, 1, 2, 0, 0, 'Auto Entry', 'Meter Program', 5, 0, 72, 1581178031, 'Y'),
(16, 0, 1, 2, 0, 0, 'Auto Entry', 'Meter Program', 6, 0, 72, 1581178031, 'Y'),
(17, 0, 1, 2, 0, 0, 'Auto Entry', 'Meter Program', 7, 0, 72, 1581178031, 'Y'),
(18, 0, 1, 2, 0, 0, 'Auto Entry', 'Meter Program', 8, 0, 72, 1581178031, 'Y'),
(19, 0, 1, 2, 0, 0, 'Auto Entry', 'Meter Program', 9, 0, 72, 1581178031, 'Y'),
(20, 2500, 1, 2, 0, 2500, 'Auto Entry', 'Meter Program', 10, 0, 72, 1581178031, 'Y'),
(21, 0, 1, 3, 0, 0, 'Auto Entry', 'Meter Program', 1, 0, 72, 1581178031, 'Y'),
(22, 0, 1, 3, 0, 0, 'Auto Entry', 'Meter Program', 2, 0, 72, 1581178031, 'Y'),
(23, 0, 1, 3, 0, 0, 'Auto Entry', 'Meter Program', 3, 0, 72, 1581178031, 'Y'),
(24, 0, 1, 3, 0, 0, 'Auto Entry', 'Meter Program', 4, 0, 72, 1581178031, 'Y'),
(25, 0, 1, 3, 0, 0, 'Auto Entry', 'Meter Program', 5, 0, 72, 1581178031, 'Y'),
(26, 0, 1, 3, 0, 0, 'Auto Entry', 'Meter Program', 6, 0, 72, 1581178031, 'Y'),
(27, 0, 1, 3, 0, 0, 'Auto Entry', 'Meter Program', 7, 0, 72, 1581178031, 'Y'),
(28, 0, 1, 3, 0, 0, 'Auto Entry', 'Meter Program', 8, 0, 72, 1581178031, 'Y'),
(29, 0, 1, 3, 0, 0, 'Auto Entry', 'Meter Program', 9, 0, 72, 1581178031, 'Y'),
(30, 2541, 1, 3, 0, 2541, 'Auto Entry', 'Meter Program', 10, 0, 72, 1581178031, 'Y'),
(31, 97152, 1, 1, 0, 95250, 'Smith Smith', 'Robert A Palermo|12/18/2021 10:09:19 AM', 11, 1902, 72, 1581178031, 'Y'),
(32, 3215, 1, 2, 0, 2500, 'Smith Smith', 'Robert A Palermo|12/18/2021 10:09:19 AM', 11, 715, 72, 1581178031, 'Y'),
(33, 3128, 1, 3, 0, 2541, 'Smith Smith', 'Robert A Palermo|12/18/2021 10:09:19 AM', 11, 587, 72, 1581178031, 'Y'),
(34, 0, 2, 4, 0, 0, 'Auto Entry', 'Meter Program', 1, 0, 44, 1460335468, 'Y'),
(35, 0, 2, 4, 0, 0, 'Auto Entry', 'Meter Program', 2, 0, 44, 1460335468, 'Y'),
(36, 0, 2, 4, 0, 0, 'Auto Entry', 'Meter Program', 3, 0, 44, 1460335468, 'Y'),
(37, 0, 2, 4, 0, 0, 'Auto Entry', 'Meter Program', 4, 0, 44, 1460335468, 'Y'),
(38, 0, 2, 4, 0, 0, 'Auto Entry', 'Meter Program', 5, 0, 44, 1460335468, 'Y'),
(39, 0, 2, 4, 0, 0, 'Auto Entry', 'Meter Program', 6, 0, 44, 1460335468, 'Y'),
(40, 0, 2, 4, 0, 0, 'Auto Entry', 'Meter Program', 7, 0, 44, 1460335468, 'Y'),
(41, 25000, 2, 4, 0, 25000, 'Auto Entry', 'Meter Program', 8, 0, 44, 1460335468, 'Y'),
(42, 0, 2, 5, 0, 0, 'Auto Entry', 'Meter Program', 1, 0, 44, 1460335468, 'Y'),
(43, 0, 2, 5, 0, 0, 'Auto Entry', 'Meter Program', 2, 0, 44, 1460335468, 'Y'),
(44, 0, 2, 5, 0, 0, 'Auto Entry', 'Meter Program', 3, 0, 44, 1460335468, 'Y'),
(45, 0, 2, 5, 0, 0, 'Auto Entry', 'Meter Program', 4, 0, 44, 1460335468, 'Y'),
(46, 0, 2, 5, 0, 0, 'Auto Entry', 'Meter Program', 5, 0, 44, 1460335468, 'Y'),
(47, 0, 2, 5, 0, 0, 'Auto Entry', 'Meter Program', 6, 0, 44, 1460335468, 'Y'),
(48, 0, 2, 5, 0, 0, 'Auto Entry', 'Meter Program', 7, 0, 44, 1460335468, 'Y'),
(49, 4000, 2, 5, 0, 4000, 'Auto Entry', 'Meter Program', 8, 0, 44, 1460335468, 'Y'),
(50, 0, 2, 6, 0, 0, 'Auto Entry', 'Meter Program', 1, 0, 44, 1460335468, 'Y'),
(51, 0, 2, 6, 0, 0, 'Auto Entry', 'Meter Program', 2, 0, 44, 1460335468, 'Y'),
(52, 0, 2, 6, 0, 0, 'Auto Entry', 'Meter Program', 3, 0, 44, 1460335468, 'Y'),
(53, 0, 2, 6, 0, 0, 'Auto Entry', 'Meter Program', 4, 0, 44, 1460335468, 'Y'),
(54, 0, 2, 6, 0, 0, 'Auto Entry', 'Meter Program', 5, 0, 44, 1460335468, 'Y'),
(55, 0, 2, 6, 0, 0, 'Auto Entry', 'Meter Program', 6, 0, 44, 1460335468, 'Y'),
(56, 0, 2, 6, 0, 0, 'Auto Entry', 'Meter Program', 7, 0, 44, 1460335468, 'Y'),
(57, 3000, 2, 6, 0, 3000, 'Auto Entry', 'Meter Program', 8, 0, 44, 1460335468, 'Y')";

if($conn->query($sSql) === TRUE) {
	echo "Update meters_MeterReading from 001 to 012 <br>";
} else {
	echo "*********************************************************************************************************************************</br>";
	echo "Error - meters_MeterReading : " . $conn->error;
	echo "</br>*********************************************************************************************************************************</br>";
}

$sSql = "INSERT INTO `meters_MeterUtility` (`MeterUtility_ID`, `Building_ID`, `uniqueCostCenter`, `muLocalSubMeterDate`, `muBillFromDate`, `muBillToDate`, `muCost`, `muKWH`, `muCostperKWH`, `UtilityCompany_ID`, `muDateInput`, `BillingMonth_ID`) VALUES
(1, 72, 1581178031, '2021-01-01', '2021-01-01', '2021-01-01', 0, 0, 0, 1, '2021-01-01', 1),
(2, 72, 1581178031, '2021-02-01', '2021-02-01', '2021-02-01', 0, 0, 0, 1, '2021-02-01', 2),
(3, 72, 1581178031, '2021-03-01', '2021-03-01', '2021-03-01', 0, 0, 0, 1, '2021-03-01', 3),
(4, 72, 1581178031, '2021-04-01', '2021-04-01', '2021-04-01', 0, 0, 0, 1, '2021-04-01', 4),
(5, 72, 1581178031, '2021-05-01', '2021-05-01', '2021-05-01', 0, 0, 0, 1, '2021-05-01', 5),
(6, 72, 1581178031, '2021-06-01', '2021-06-01', '2021-06-01', 0, 0, 0, 1, '2021-06-01', 6),
(7, 72, 1581178031, '2021-07-01', '2021-07-01', '2021-07-01', 0, 0, 0, 1, '2021-07-01', 7),
(8, 72, 1581178031, '2021-08-01', '2021-08-01', '2021-08-01', 0, 0, 0, 1, '2021-08-01', 8),
(9, 72, 1581178031, '2021-09-01', '2021-09-01', '2021-09-01', 0, 0, 0, 1, '2021-09-01', 9),
(10, 72, 1581178031, '2021-10-11', '2021-10-11', '2021-11-10', 25000, 50000, 0.5, 1, '2021-10-01', 10),
(11, 72, 1581178031, '2021-12-18', '2021-11-11', '2021-12-10', 4152653, 7485966, 0.554725068214309, 1, '2021-12-18', 11),
(12, 44, 1460335468, '2021-01-01', '2021-01-01', '2021-01-01', 0, 0, 0, 2, '2021-01-01', 1),
(13, 44, 1460335468, '2021-02-01', '2021-02-01', '2021-02-01', 0, 0, 0, 2, '2021-02-01', 2),
(14, 44, 1460335468, '2021-03-01', '2021-03-01', '2021-03-01', 0, 0, 0, 2, '2021-03-01', 3),
(15, 44, 1460335468, '2021-04-01', '2021-04-01', '2021-04-01', 0, 0, 0, 2, '2021-04-01', 4),
(16, 44, 1460335468, '2021-05-01', '2021-05-01', '2021-05-01', 0, 0, 0, 2, '2021-05-01', 5),
(17, 44, 1460335468, '2021-06-01', '2021-06-01', '2021-06-01', 0, 0, 0, 2, '2021-06-01', 6),
(18, 44, 1460335468, '2021-07-01', '2021-07-01', '2021-07-01', 0, 0, 0, 2, '2021-07-01', 7),
(19, 44, 1460335468, '2021-08-05', '2021-08-05', '2021-09-04', 142536, 512523, 0.28, 2, '2021-08-01', 8)";
if($conn->query($sSql) === TRUE) {
	echo "Update meters_MeterUtility from 001 to 012 <br>";
} else {
	echo "*********************************************************************************************************************************</br>";
	echo "Error - meters_MeterUtility : " . $conn->error;
	echo "</br>*********************************************************************************************************************************</br>";
	
}

$sSql = "INSERT INTO `meters_UtilityCompany` (`UtilityCompany_ID`, `Building_ID`, `uniqueCostCenter`, `utType`, `utAccountNumber`, `utCompanyName`, `utCompanyAdd`, `utContact`, `utPhone`, `utEmail`, `utBillSchedual`, `utDeleted`, `utBillToDate`, `utBillFromDate`, `muUtilityBill`, `muUsage`, `muCostperUnit`, `muUOM`, `utInvoiceForMonthOf`) VALUES
(1, 72, 1581178031, '1', 'B210', 'Mass Electric', 'One Mass Ave', 'Rob Palemo', '617957834', '', '1', 'N', '2021-11-10', '2021-10-11', 25000, 50000, 0.5, 'kWh ', '2021-10-11'),
(2, 44, 1460335468, '1', 'B201', 'Palham Meter Electric', 'One Electric Drive', '', '', '', '1', 'N', '2021-09-04', '2021-08-05', 142536, 512523, 0.28, 'kWh ', '2021-08-05')";

if($conn->query($sSql) === TRUE) {
	echo "Update meters_UtilityCompany from 001 to 012 <br>";
} else {
	echo "*********************************************************************************************************************************</br>";
	echo "Error - meters_UtilityCompany : " . $conn->error;
	echo "</br>*********************************************************************************************************************************</br>";
	
}

 mysqli_close($conn); 
?>