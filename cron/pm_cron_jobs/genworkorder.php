<?php



	
	
// Set Start_Date_Adjut to NUll
$Current_Work_Order_Date = "" ;


// Check if the building Number and the SearchBuiding_number are the same
// IF not search the Work Order Table and find the last work order number for building
// Add "1" to the last work order number and asign all new work orders
// for that building.
if($row["BuildingID"] != $SearchBuilding_Number){                                             // 1700 - Begin IF Statement
	$SearchBuilding_Number = $row["BuildingID"];
	$WorkOrder_Number_Increment_on_ID = $row["AU_Equipment_Detail_ID"];
	$Sql_WO_Number = "SELECT
						pm_au_work_order.BuildingID AS Max_BuildingID,
						Max(pm_au_work_order.PM_WO_Number) AS Max_PM_WO_Number
						FROM
						pm_au_work_order
						WHERE
						pm_au_work_order.BuildingID = ". $SearchBuilding_Number ."
						GROUP BY
						pm_au_work_order.BuildingID
						ORDER BY
						Max_PM_WO_Number";
	$WOresult = $conn->query($Sql_WO_Number);
	if ($WOresult){
		if ($WOresult->num_rows == 0){                                                          // If no record are returned, set 1st work order to 1001
				$WO_Number = '1001';
		}else{                                                                                  // If Record Have been found, Find the Largent Number and add 1
				$WO_Row = $WOresult->fetch_assoc();
				$WO_Number = $WO_Row["Max_PM_WO_Number"] + 1;
		}
		$WOresult->close();
		//	$WO_Number_Last_Added = $WO_Number; // Initialze Lay Work Order nummber
	}else echo "WO-I-015 - " .($conn->error) . "<br><br> SQL is " . $Sql_WO_Number . "<br>";
} 

echo "<br> \r";
                                                                                             // 1700 - End IF Statement
echo "WO-I-100 - Work Order Number : " , $WO_Number ."<br>\r"; 
echo "WO-I-110 - WO Increment ID #: $WorkOrder_Number_Increment_on_ID | Equipment Detail ID #: " . $row["AU_Equipment_Detail_ID"]."<br>\r"; 
echo "WO-I-120 - Generate Daily / Week PM Jobs Separately: '" . $row_PM_WO_Options["PM_Auto_Exclude"] . "' | Schedule " . $row_PM_WO_Options["PM_Auto_Schedule"]."<br>\r"; 
echo "WO-I-130 - Building Currently Active:  " . $Active . "<br>\r";	
				
if($WorkOrder_Number_Increment_on_ID != $row["AU_Equipment_Detail_ID"] or
	($row_PM_WO_Options["PM_Auto_Exclude"]== 'Y' and $row_PM_WO_Options["PM_Auto_Schedule"] == 'M' and $row["Freq_Inverval_Addinit"] == "DAY") or
	($row_PM_WO_Options["PM_Auto_Exclude"]== 'Y' and $row_PM_WO_Options["PM_Auto_Schedule"] == 'M' and $row["Freq_Inverval_Addinit"] == "WEEK") or
	($row_PM_WO_Options["PM_Auto_Exclude"]== 'Y' and $row_PM_WO_Options["PM_Auto_Schedule"] == 'W' and $row["Freq_Inverval_Addinit"] == "DAY") or
	($row_PM_WO_Options["PM_Auto_Exclude"]== 'Y' and $row_PM_WO_Options["PM_Auto_Schedule"] == 'W' and $row["Freq_Inverval_Addinit"] == "WEEK")
	
	
	 ){     // Check Work Order Increment

	if($WO_Number_Last_Added == $WO_Number){
			$WO_Number = $WO_Number + 1; 
		}                                                              // Add 1 to work order number
			$WorkOrder_Number_Increment_on_ID = $row["AU_Equipment_Detail_ID"];
	}
echo "WO-I-140 - Building ID :" . $row["BuildingID"] . " | Building Name: " . $row_PM_WO_Options["buildingName"] . " <br>\r";						


$Multipler = ($row["Freq_Inverval_Value"] * $row["Inveral"]);                        // Set the Mulitplyer of the Date Inverval

if ($Multipler  == 0){
	$Multipler =1;}

$Calculate_Next_Work_Order_Date = new DateTime($row["WO_Create_Date"]);                            // Retrieve the current Start Date from Database
$Calculate_Next_Work_Order_Date->modify('+'. $Multipler  . " " . $row["Freq_Inverval_Addinit"] );  // Calculate Next Date Using the multiplyer and the Frequescy - (ever 3 Month)
$WorkOrder_Date = new DateTime($row["WO_Create_Date"]);                              // The current date when WO will be assigned to User



if($row["Seasonal_Task"] == "Y"){                                                    // 1800 Begin IF - Check if Task or Reading is Seasonal
	// Check if Before Start Date
	$Start_Month = new DateTime($Todays_Days->format('Y'). "-". $row["Seasonal_Start_Date"] ."-01");       // Get the Start Month of the Seasonal Task/Reading
	$End_Month = new DateTime($Todays_Days->format('Y'). "-". $row["Seasonal_End_Date"]."-01");            // Get the End Month of the Seasonal Task/Reading

		if ($Start_Month->format('m') > $End_Month->format('m')){                        // Check is Start Month is After the then End Month, if so, we must add 1 year to the end date
			$End_Month->modify('+1 year');											     // Add 1 Year to End Month
		}

	$interval = DateInterval::createFromDateString('1 month');
	$period   = new DatePeriod($Start_Month, $interval, $End_Month->modify('first day of next month'));
	$Active = false;
	foreach ($period as $dt) {                                                      // 1900 Begin Foreach Loop | Check if Season is between active Months
			if ( $dt->format("m") == $Calculate_Next_Work_Order_Date->format('m')) {                  // IF month is equal then Season Falls between Active Months
//					echo " *****************Fall Between active months ****************<br>";
					$Active = True;                                                        // Set Active 'True'
					break;
			}
	}                                                                               // 1900 End of Foreach Loop
	if ($Active == False){ 
//					echo " +++++++++++++++++++ Out of Active Months +++++++++++++++++++<br>";
		while ($Calculate_Next_Work_Order_Date->format('m') != $Start_Month->format('m')){       // Loop Until $Calculate_Next_Work_Order_Date is equal to Seasonal Start Date
				$Calculate_Next_Work_Order_Date->modify('+1 month');	                               // Add 1 Month until both are equal
			}

	}		
	}else{
	$Active = True;                                                                   // IF not a Seasonal Tast/Reading automatically set to 'True'
}                                                                                     // 1800 End IF - Check if Task or Reading is Seasonal

// If $Active is False, then the Task is Seasonal and the start date is some time in the future, so we do not want to add Task / Reading new PM Work Order

if ($Active == False){                                                              // 1900 - Begin IF ($Active == False)
		$Current_Work_Order_Date = 	new DateTime($Calculate_Next_Work_Order_Date->format('Y-m-d'));       // Set Current_Work_Order_Date to the Calculate_Next_Work_Order_Date, do Not update `pm_au_work_order Table`
		echo "WO-I-500 - Task/Reading Seasonal - Recalcuate Date : ". $Current_Work_Order_Date->format('Y-m-d') . "<br>\r";
//					if($row_PM_WO_Options["PM_Auto_Exclude"] = 'Y' and $row_PM_WO_Options["PM_Auto_Schedule"]= 'M'){
//						$WO_Number = $WO_Number -1;
//					}
	}
else{                                                                               // Task/Reading is active
	$Current_Work_Order_Date = new datetime($WorkOrder_Date->format('Y-m-d'));
	echo "WO-I-700 -  WO System ID Number : " . $row["Task_Reading_ID"] . " | Current Start Date is : " . $Current_Work_Order_Date->format('Y-m-d') . 
		" | Multiplyer is : " . $Multipler . " | Frequency is : " . $row["Freq_Inverval_Addinit"] . "<br>\r";

	while ($Calculate_Next_Work_Order_Date->format('Y-m-d') <= $Todays_Days->format('Y-m-d'))
		{	// Check Start date, if start date is before Todays Date then update to a Future date
			$Calculate_Next_Work_Order_Date->modify('+'. $Multipler  . " " . $row["Freq_Inverval_Addinit"] );
		}
	echo "WO-I-710 - System generate next Work Order Date : " . $Calculate_Next_Work_Order_Date->format('Y-m-d') . "<br>\r";
	
	$Next_WO_Day_Of_Week = $Calculate_Next_Work_Order_Date->format('l');                           // Get the Acutal Day the week of the WO Date
	// Get the Day of the week in number form
	// 1 - Monday
	// 2 - Tuesday
	// 3 - Wednesday
	// 4 - Thursday
	// 5 - Friday
	// 6 - Saturday
	// 7 - Sunday
	$Next_WO_Day_Of_Week_Number = $Calculate_Next_Work_Order_Date->format('N');  
	echo "WO-I-750 - Current Work Order must be Valid for days: `". $row["StartDateAdjustment"] ."` <br>\r";
	
	// Call Function for Date Adjustment
	adjustDateandWeek($Current_Work_Order_Date, $Next_WO_Day_Of_Week_Number, $row["StartDateAdjustment"], $row["Freq_Inverval_Addinit"], $Multipler, $Calculate_Next_Work_Order_Date);

// if ($_GET['Task_or_Reading'] == "TandR"){ 
if ($Task_Reading_Select == "TandR"){ 
	$Task_Reading_Table = ($row["TorR"] == "R") ? "pm_au_equipment_readings" : "pm_au_equipment_task";
	$Task_Reading_ID = ($row["TorR"] == "R") ? "AU_Equipment_Readings_ID" : "AU_Equipment_Task_ID";
	$WorkOrder_Reading_or_Task = $row["TorR"];
}
	
	$sqlInsert = "INSERT INTO `pm_au_work_order` (
				  `AU_Equipment_Task_Reading_ID` ,
				  `AU_Equipment_Detail_ID`, 
				  `Parent_ID` , 
				  `PM_WO_StartDate`, 
				  `Reading_Task` ,
				  `BuildingID` , 
				  `AU_Template_Designation_ID`, 
				  `PM_WO_Number`) 
		VALUES (" . 
					$row["Task_Reading_ID"] .", " . 
					$row["AU_Equipment_Detail_ID"] .", " . 
					$row["Parent_ID"] .", '" . 
													$Current_Work_Order_Date->format('Y-m-d') . "', '" . 
					$WorkOrder_Reading_or_Task ."', " . 
					$row["BuildingID"] . ",  " .
					$row["Template_Designation_ID"] . ",  " .
					$WO_Number . ")";
if (mysqli_query($conn, $sqlInsert)) {
	//	echo "New record Inserter successfully " . $row["AU_Template_Task_ID"] . " - " . $row["Task_Instruction"] ."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .  $sqlInsert . "<br>";
	$WO_Number_Last_Added = $WO_Number;				// Keep Track of last work order number for Exclude Later
} else {
		echo "OP-E-002 - Error: " . $sqlInsert . "<br>" . mysqli_error($conn) . "<br><br>\r\r";
}
}
$Update_StartDate_Month = "";
if($row["Freq_Inverval_Addinit"] == "DAY" or $row["Freq_Inverval_Addinit"] == "WEEK"){
	$Update_StartDate_Month = "', `Startdate_month` = '" . 	$Calculate_Next_Work_Order_Date->format('d');
	// echo "WO-I-800 - SQL Insert`". $Update_StartDate_Month ."`<br>"; 
}
                                                                                  // 1900 - End IF ($Active == False)


// Update Table Pm_AU_Equipment_Task and PM_AU_Equipment_Reading

$sqlUpdate = "UPDATE `". $Task_Reading_Table ."` SET `Start_date` ='" . $Calculate_Next_Work_Order_Date->format('M Y') . 
$Update_StartDate_Month . "' WHERE `" . $Task_Reading_ID ."` = " .  $row["Task_Reading_ID"];

	if (mysqli_query($conn, $sqlUpdate)) {
 		echo "New record updated successfully " . $row["AU_Template_Task_ID"] . " - " . $row["Task_Instruction"] ." " .  $sqlUpdate . "<br>";
	} else {
	echo "OP-E-003 - Error: " . $sqlUpdate . "<br>\r" . mysqli_error($conn) . "<br><br>\r\r";
	}
	
if(isset($_GET['Task_or_Reading'])){	
?> 



<tr> 
<td><?php echo $row["Task_Reading_ID"]; ?></td>
<td><?php echo $row["WO_Create_Date"]; ?></td>
<td><?php echo $Current_Work_Order_Date->format('l') .  " &nbsp; (" . $Current_Work_Order_Date->format('N') .")"; ?></td>
<!-- <td><?php //echo $row["Parent_ID"]; ?></td> -->
<!--<td><?php //echo $row["AU_Template_Task_ID"]; ?></td>-->
<td><?php echo $row["StartDateAdjustment"]; ?></td>
<td><?php echo $Current_Work_Order_Date->format('Y-m-d') ?></td>
<td><?php echo $Calculate_Next_Work_Order_Date->format('Y-m-d'); ?></td>
<!--<td><?php // echo $row["Freq_Inverval_Value"]; ?></td>-->
<!--<td><?php // echo $row["Inveral"]; ?></td>-->
<td><?php echo $Multipler . " " . $row["Freq_Inverval_Addinit"]; ?></td>
<td><?php echo $row["AU_Equipment_Name"]; ?></td>
<td><?php echo $row["Equipment_Floor"] . " | " . $row["Equipment_Unit"] ; ?></td>
<td><?php echo $row["Seasonal_Start_Date"]; ?></td>
<td><?php echo $row["Seasonal_End_Date"]; ?></td>
<td><?php if( $Active == False){echo "----";}else {echo $WO_Number;}; echo " | " . $WO_Number_Last_Added; ?></td>
</tr>
<?php
}
?>
