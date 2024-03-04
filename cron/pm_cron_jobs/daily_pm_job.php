
<?php
$servername = "localhost";
$username = "ve";
$password = "Vision@4424";
$dbname = "ve_crm_new";

echo "Connecting ...................</br>";
//if(!isset($_GET['Task_or_Reading'])){
//	echo " Task or Reading Value is not set, will exit ";
//	exit();
//}

$_GET['Manually_Connect'];
echo "Manually Connect is set for : " . $_GET['Manually_Connect'] ." </br>";
if ($Manually_Connect == False){

	if(!isset( $argv[1]) and !isset($_GET['Task_or_Reading']) ){
		echo " Task or Reading Value is not set, will exit ";
		exit();
	}
	
	if(isset( $argv[1])){
		$Task_Reading_Select = $argv[1];
	}
	else{
		$Task_Reading_Select = $_GET['Task_or_Reading'];
	}
	$Where_SQL = "pm_au_work_order_options.PM_Auto_Create_Jobs = 'Y'";
}
else{
	echo "Manuall Connect is true</br>";
	$Task_Reading_Select = "TandR";
	$Where_SQL = "pm_au_work_order_options.PM_Auto_Create_Jobs = 'N' And
    				buildings.build_id = $Where_Building_ID And
    				buildings.uniqueCostCenter = $Where_Cost_Center ";
					
					
	echo "Where+Sql : " . $Where_SQL ."</br>";
	}
	
	

	
	
	
//include_once ("reset.php");

include_once ("Functions.php");

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("OP-E-001 - Connection failed: " . $conn->connect_error);
} 


// WO - Work Order
// OP - Option
// I - Information

// Set time Zone to US New Your EST
date_default_timezone_set("America/New_York");

echo "DB-I-001 - Connected successfully <br><br> \r\r";
echo "DB-I-002 - PHP Version " . phpversion() . "<br> \r";
//echo "DB-I-003 - Task or Reading : " . $_GET['Task_or_Reading'] . "<br> \r";
echo "DB-I-003 - Task or Reading : " . $Task_Reading_Select . "<br> \r";

// Retrieve all Buildings that have thier "PM_Auto_Create_Jobs" set to "Y"
$PM_WO_Option_Date = new DateTime('now  America/New_York');
$Todays_Days = new DateTime('now  America/New_York');
echo "DB-I-004 - Todays Date :       " . $Todays_Days->format('Y-m-d h:i:sa') . "<br><br> \r\r\r";
echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++<br> \r";

$sql_PM_WO_Options = "SELECT
						  buildings.buildingName,
						  company.companyName,
						  pm_au_work_order_options.*
						FROM
						  pm_au_work_order_options
						  LEFT JOIN buildings ON buildings.build_id = pm_au_work_order_options.BuildingID
						  LEFT JOIN company ON company.cust_id = buildings.cust_id
						WHERE " .
							$Where_SQL //   pm_au_work_order_options.PM_Auto_Create_Jobs = 'Y'
						. "ORDER BY
						  pm_au_work_order_options.BuildingID";
						  
						  
echo "The Sql is : " . 	$sql_PM_WO_Options . "</br>";

					  
$result_PM_WO_Options = $conn->query($sql_PM_WO_Options);

if($result_PM_WO_Options->num_rows>0) {                                                         // 0900 - Begin IF statement
	while($row_PM_WO_Options = $result_PM_WO_Options->fetch_assoc()){                           // 1000 - Begin While Loop
		$Schedule_Array = array();																// Initialize Array
		//''!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$PM_WO_Option_BuildingID = $row_PM_WO_Options["BuildingID"];                           // Set Building ID
		// Remove the number to when testing is over
		
		//''!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$PM_WO_Option_Date = new DateTime('now  America/New_York');                            // Set the Time Zone
		echo "OP-I-005 - Building Number : " . $PM_WO_Option_BuildingID . "<br> \r";
		echo "OP-I-006 - Building Name : " . $row_PM_WO_Options["buildingName"] . "<br> \r";
		echo "OP-I-007 - Company: " . $row_PM_WO_Options["companyName"] . "<br> \r";
		//echo "==============================================================<br> \r";
		echo "OP-I-008 - E - Every Date , F - Week Day's Only , W - Weekly, M - Monthly<br> \r";
		echo "OP-".$row_PM_WO_Options["PM_Auto_Schedule"]."-009 - Schedule : " . $row_PM_WO_Options["PM_Auto_Schedule"]. "<br> \r";
		//echo "==============================================================<br> \r";
		if ($Manually_Connect == False){
			switch ($row_PM_WO_Options["PM_Auto_Schedule"]) {                                     // 1100 - Begin Switch  | E - Every Date , F - Week Day's Only , W - Weekly, M - Monthly
					
					case "E":                                                                     // E - Every Day
						// Do nothing, Date has already been set about to todays date
						echo "OP-E-050 - Generate PM Work Orders for the Day of " .  $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";
						echo "OP-E-060 - Exclude Daily and Weekly - Not Applicable when Every Day selected<br> \r";
						
		
							$Schedule_Array = array                                                                                                                  // Setup Array
							(
							array("Day", "'" . $PM_WO_Option_Date->format('Y-m-d'). "'" ),                                    // Select today's date for 'Day'
							);
						
						break;
						
					case "F":                                                                     // F - Week Day's only
						// Retrieve to Days day in number format - $PM_WO_Option_Date->format('N')
						// Retrieve todays day in text format i.e. `Friday` - $PM_WO_Option_Date->format('l')
						echo "OP-F-010 - M-F Week Day :       " . $PM_WO_Option_Date->format('N') . " - " . $PM_WO_Option_Date->format('l') . "<br> \r";
						if($PM_WO_Option_Date->format('N') > 5 ){                                // if `N` is 6 or 7 (Saturday or Sunday)
							$PM_WO_Option_Date->modify('next monday');                           // Then set day to the next Monday
							echo "OP-F-040 - Change Date to next monday " .  $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";
							 
						}
						echo "OP-F-050 - Generate PM Work Orders for the Day of " .  $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";
		
							$Schedule_Array = array                                                                                                  // Setup Array
							(
							array("Day", "'" . $PM_WO_Option_Date->format('Y-m-d') . "'"),                    // Set $PM_WO_Option_Date for 'Day'
							//array("Week", "'" . $PM_WO_Option_Date->format('Y-m-d') . "' AND pm_au_frequency.`Interval` = 'Week'"),                  // Set $PM_WO_Option_Date for 'Week'
							//array("Month", "'" . $PM_WO_Option_Date->format('Y-m-d') . 
							 //      "' AND pm_au_frequency.`Interval` != 'Day' AND pm_au_frequency.`Interval` != 'Week'")                             // Set $PM_WO_Option_Date for 'Month'
							);
						break;
					
					case "W":                                                                                            // W - Weekly
					
						
						// Check `PM_Auto_Month_Generate` Day is not vaild, change to '1' (Monday) $M_F_Day = 1 else set to $row_PM_WO_Options["PM_Auto_Month_Generate"]
						$M_F_Day = ($row_PM_WO_Options["PM_Auto_Month_Generate"] == "Day") ?  1 :  $row_PM_WO_Options["PM_Auto_Month_Generate"];
						
						echo "OP-W-010 - Today's Date : " . $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";           // Get current Date
						echo "OP-W-020 - WO Options Generate WO Every : " .  jddayofweek(($M_F_Day)-1,1) . " - (" .$M_F_Day. ")<br> \r";  // Retrieve Day from var $M_F_Day, must subtract (-) 1 (Mon, Tues ect...)
						echo "OP-W-030 - Current Day of Week : " . $PM_WO_Option_Date->format('l') . " - (" . $PM_WO_Option_Date->format('N') . ")<br> \r";
						 
						if ($M_F_Day == $PM_WO_Option_Date->format('N')){                                            // If today's day is the same as  $M_F_Day then generate for the following week
							$PM_WO_Option_Date->modify('Next ' . $PM_WO_Option_Date->format('l'));                   // Add 1 week to the date
							echo "OP-W-040 - Create PM WO for the Week Ending : " .  $PM_WO_Option_Date->format('Y-m-d'). "<br> \r";
						}
						
						
						echo "OP-W-050 - Generate PM Work Orders for the week of " .  $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";
					
						// If PM_Auto_Exclude == Y then Daily work order will be crated each day, If N, then create Daily for the week
						$DateDay = new DateTime('now  America/New_York');
						($row_PM_WO_Options["PM_Auto_Exclude"] == 'Y') ? $DateDay->modify($Todays_Days->format('Y-m-d')) : $DateDay->modify($PM_WO_Option_Date->format('Y-m-d'));
						echo "OP-W-550 - Generate Daily Work Orders for the Date of : " . $DateDay->format('Y-m-d') . "<br> \r";
						// If PM_Reports_Separate == Y then generate Day, Week and Monthly seperatly
						$ExcludeOps = ($row_PM_WO_Options["PM_Reports_Separate"] == 'Y') ? 'Y-Y' : $row_PM_WO_Options["PM_Reports_Exclude_Daily"] ."-". $row_PM_WO_Options["PM_Reports_Exclude_Weekly"];
						echo "OP-W-060 - Exclude Options Selected " . $ExcludeOps . "<br> \r";
						$Schedule_Array = SetupArray($ExcludeOps, $DateDay, $PM_WO_Option_Date); // Call Function and Setup Array
								
						break;	
						
					case "M":                                                                     // M- Monthly
						$DateDay = new DateTime('now  America/New_York');
					
						if(($row_PM_WO_Options["PM_Auto_Month_Generate"]) == "Day"){              // IF `Day` then assign to var $Convert_to_Day
							$Convert_to_Day = $row_PM_WO_Options["PM_Auto_Month_Generate"];
						}else{                                                                    // If a number, convert to Monday, Tuesday ect
							$Convert_to_Day = jddayofweek(($row_PM_WO_Options["PM_Auto_Month_Generate"]) -1,1);
						}
						
						echo "OP-M-010 - Today's Date : " . $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";
						echo "OP-M-015 - Generate on the: '" . $row_PM_WO_Options["PM_Auto_Month_Day_Of_Week"] . "' - " . $Convert_to_Day . " of the month<br> \r";
						
						
						switch ($row_PM_WO_Options["PM_Auto_Month_Day_Of_Week"]) {             // 1200 Begin  switch | Database Values can be : 1st - 7th, 15th and Last
							
							case "Last":                                                       // IF `Last' do the followong
									switch ($row_PM_WO_Options["PM_Auto_Month_Generate"]){     // 1300 Begin Switch | Database Values can be " 1 thru 7, and Day (1 - Monday, 2 - Tuesday, ect...)
										
										case "Day":	
												$PM_WO_Option_Date->modify('last day of this month');                                      // Modify Date to 'LAST' 'DAY' of current month
												echo "OP-M-040 - Last Day of Month : " .  $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";
												if($PM_WO_Option_Date->diff($Todays_Days)->format("%r%a") >= 0){              // Check Date Difference, if 0 or Positive calculate for next month
												 $PM_WO_Option_Date->modify("last day of next month");                                     // Set date to 'LAST' 'DAY' of the next month
												 echo "OP-M-045 - Generate PM Work Order Jobs ending " .  $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";
												}
												break;
												
										default:
												$PM_WO_Option_Date->modify('last ' . $Convert_to_Day . ' of this month');   // Modify Date to "LAST" var  $Convert_to_Day (Monday, Tuesday Wednesday ect..)
												echo "OP-M-040 - Convert to Last $Convert_to_Day of Month " . $PM_WO_Option_Date->format('Y-m-d') . " <br> \r";
												if($PM_WO_Option_Date->diff($Todays_Days)->format("%r%a") >= 0){              // Check Date Difference, if 0 or Positive calculate for next month
													$PM_WO_Option_Date->modify('last ' . $Convert_to_Day .' of next month');  // Set Date to 'LAST" $Convert_to_Day of the month
													echo "OP-M-045 - Generate PM Work Order Jobs ending        " .  $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";
												}
												break;
									}                                                                              // 1300 - End of Switch ($row_PM_WO_Options["PM_Auto_Month_Generate"]) Statement 
									echo "OP-M-046 - Exclude Daily and Weekly " .  $row_PM_WO_Options["PM_Auto_Exclude"] . "<br> \r";
									($row_PM_WO_Options["PM_Auto_Exclude"] == 'Y') ? $DateDay->modify($Todays_Days->format('Y-m-d')) : $DateDay->modify($PM_WO_Option_Date->format('Y-m-d'));
									echo "OP-M-550 - Generate Daily Work Orders for the Date of : " . $DateDay->format('Y-m-d') . "<br> \r";
									// If PM_Reports_Separate == Y then generate Day, Week and Monthly separately
									$ExcludeOps = ($row_PM_WO_Options["PM_Reports_Separate"] == 'Y') ? 'Y-Y' : $row_PM_WO_Options["PM_Reports_Exclude_Daily"] ."-". 
										$row_PM_WO_Options["PM_Reports_Exclude_Weekly"];
									echo "OP-M-060 - Exclude Options Selected " . $ExcludeOps . "<br> \r";
	
									$Schedule_Array = SetupArray($ExcludeOps, $DateDay, $PM_WO_Option_Date); // Call Function and Setup Array
									break;
									
									
							default:                                                                               // Default - for all values except for 'LAST"
									
									$converday = $row_PM_WO_Options["PM_Auto_Month_Day_Of_Week"] -1;               // Database Values will be 1 thu 7 (Need to Subtract -1, because we will add to  )
									
									switch ($row_PM_WO_Options["PM_Auto_Month_Generate"]){                         // 1400 - Begin Switch
										
										case "Day":	                                                               // Database Values can be " 1 thru 7, and Day (1 - Monday, 2 - Tuesday, ect...)
												$PM_WO_Option_Date->modify('First day of this month')->modify("+$converday day");         // Start at 1st day of this month, add VAR days
												echo "OP-M-040 - " . ($converday + 1) . " Day of Month :       " .  $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";
												if($PM_WO_Option_Date->diff($Todays_Days)->format("%r%a") >= 0){              // Check Date Difference, if 0 or Positive calculate for next month
													$PM_WO_Option_Date->modify("+1 month")->modify("-1 day");                                 // Add 1 Month 
													echo "OP-M-045 - Generate PM Work Order Jobs ending " .  $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";
												}
												break;
										default:
	
												switch ($row_PM_WO_Options["PM_Auto_Month_Day_Of_Week"]){	            //1500 - Begin switch | Convert Number 1,2,3, to First, Second, Third
													case 1:
														$converday = "First";
														break;	
													case 2:
														$converday  = "second";
														break;	
													default:                                                            // Convert any number 3 or larger to the 'Third'
														$converday  = "Third";
														break;	
												}                                                                       // 1500 - End of Switch, ($row_PM_WO_Options["PM_Auto_Month_Generate"]) statement
												
												$PM_WO_Option_Date->modify($converday . " " . $Convert_to_Day .' of this month'); 
												echo "OP-M-050 - $converday '$Convert_to_Day' of Month :       " .  $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";
												if($PM_WO_Option_Date->diff($Todays_Days)->format("%r%a") >= 0){              // Check Date Difference, if 0 or Positive calculate for next month
													$PM_WO_Option_Date->modify($converday . " " . $Convert_to_Day .' of next month')->modify("-1 day");
													echo "OP-M-055 - Generate PM Work Order Jobs ending " .  $PM_WO_Option_Date->format('Y-m-d') . "<br> \r";
												}
												break;
									}                                                                    // 1400 - End of switch ($row_PM_WO_Options["PM_Auto_Month_Generate"]) Statement
									
									echo "OP-M-046 - Exclude Daily and Weekly " .  $row_PM_WO_Options["PM_Auto_Exclude"] . "<br> \r";
									($row_PM_WO_Options["PM_Auto_Exclude"] == 'Y') ? $DateDay->modify($Todays_Days->format('Y-m-d')) : $DateDay->modify($PM_WO_Option_Date->format('Y-m-d'));
									echo "OP-M-550 - Generate Daily Work Orders for the Date of : " . $DateDay->format('Y-m-d') . "<br> \r";
									// If PM_Reports_Separate == Y then generate Day, Week and Monthly separately
									$ExcludeOps = ($row_PM_WO_Options["PM_Reports_Separate"] == 'Y') ? 'Y-Y' : $row_PM_WO_Options["PM_Reports_Exclude_Daily"] ."-". 
										$row_PM_WO_Options["PM_Reports_Exclude_Weekly"];
									echo "OP-M-060 - Exclude Options Selected " . $ExcludeOps . "<br> \r";
	
									$Schedule_Array = SetupArray($ExcludeOps , $DateDay, $PM_WO_Option_Date); // Call Function and Setup Array
									
									
									
							
							break;
						
						}                                                                                     // 1200 - End of switch ($row_PM_WO_Options["PM_Auto_Month_Day_Of_Week"])  Statement
			}  																								// 1100 - End of switch ($row_PM_WO_Options["PM_Auto_Schedule"] Statement
		}else{
			echo "The date sent is " . $Where_WO_Date->format('Y-m-d') . "</br>";
			$DateDay = new DateTime('now  America/New_York');
			$DateDay->modify($Where_WO_Date->format('Y-m-d'));
			$PM_WO_Option_Date->modify($Where_WO_Date->format('Y-m-d'));
			$ExcludeOps = 'Y-Y';
			$Schedule_Array = SetupArray($ExcludeOps , $DateDay, $PM_WO_Option_Date); // Call Function and Setup Array
			
		}
		foreach ($Schedule_Array as $SQL_Filter){                                                         // 1600 - Begin foreach
			list($Frequency,$SqlFilter) =  $SQL_Filter;
			echo "OP-". $row_PM_WO_Options["PM_Auto_Schedule"] . "-070 - Filter Database By: $Frequency  | SQL Filter : $SqlFilter <br>\r";	//}                                                                                                          // End of while($row_PM_WO_Options = $result_PM_WO_Options->fetch_assoc()) Statement
		
		
	//	switch($_GET['Task_or_Reading']) { 
		switch($Task_Reading_Select) {	
			case "AU_Reading_Generate":
			
					$sql = "SELECT
							  pm_au_equipment_detail.BuildingID AS BuildingID,
							  pm_au_equipment_name.AU_Equipment_Name,
							  pm_au_equipment_readings.AU_Equipment_Readings_ID AS Task_Reading_ID,
							  Str_To_Date(Concat(SubString_Index(pm_au_equipment_readings.Start_Date, ' ', 1), ' ',
							  pm_au_equipment_readings.Startdate_month, ' ', SubString_Index(pm_au_equipment_readings.Start_Date, ' ', -1)),
							  '%M %d %Y') AS WO_Create_Date,
							  DayName(Str_To_Date(Concat(SubString_Index(pm_au_equipment_readings.Start_Date, ' ', 1), ' ',
							  pm_au_equipment_readings.Startdate_month, ' ', SubString_Index(pm_au_equipment_readings.Start_Date, ' ', -1)),
							  '%M %d %Y')) AS Initial_Day_of_Week,
							  pm_au_template_reading.Parent_ID AS Parent_ID,
							  pm_au_template_reading.AU_Template_Reading_ID,
							  pm_au_template_reading.Interval_Value AS Inveral,
							  pm_au_equipment_detail.AU_Equipment_Detail_ID,
							  pm_au_startdateadjustment.Name AS StartDateAdjustment,
							  pm_au_frequency.`Interval` AS Freq_Inverval_Addinit,
							  pm_au_frequency.Interval_Value AS Freq_Inverval_Value,
							  pm_au_frequency.AU_Frequency_ID AS Freq_ID,
							  pm_au_template_reading.Seasonal_Task,
							  pm_au_template_reading.Seasonal_Start_Date,
							  pm_au_template_reading.Seasonal_End_Date,
							  pm_au_template_reading.AU_Template_Designation_ID AS Template_Designation_ID,
    						  pm_au_equipment_detail.Equipment_Floor,
    						  pm_au_equipment_detail.Equipment_Unit
							FROM
							  pm_au_equipment_name
							  RIGHT JOIN pm_au_equipment_detail ON pm_au_equipment_name.AU_Equipment_Name_ID =
								pm_au_equipment_detail.AU_Equipment_Name_ID
							  RIGHT JOIN pm_au_equipment_readings ON pm_au_equipment_detail.AU_Equipment_Detail_ID =
								pm_au_equipment_readings.AU_Equipment_Detail_ID
							  LEFT JOIN pm_au_template_reading ON pm_au_template_reading.AU_Template_Reading_ID =
								pm_au_equipment_readings.AU_Template_Reading_ID
							  LEFT JOIN pm_au_frequency ON pm_au_frequency.AU_Frequency_ID = pm_au_template_reading.AU_Frequency_ID
							  LEFT JOIN pm_au_startdateadjustment ON pm_au_startdateadjustment.AU_sda_ID = pm_au_template_reading.AU_sda_ID
							WHERE
							  Str_To_Date(Concat(SubString_Index(pm_au_equipment_readings.Start_Date, ' ', 1), ' ',
							  pm_au_equipment_readings.Startdate_month, ' ', SubString_Index(pm_au_equipment_readings.Start_Date, ' ', -1)),
							  '%M %d %Y') <= ".  $SqlFilter ." AND
							  pm_au_startdateadjustment.Name <> '' AND
  								pm_au_equipment_detail.BuildingID = " . $PM_WO_Option_BuildingID ."
							ORDER BY
							  BuildingID,
							  pm_au_equipment_detail.AU_Equipment_Detail_ID";
						 
						$Task_Reading_Table = "pm_au_equipment_readings";
						$Task_Reading_ID = "AU_Equipment_Readings_ID";
					//	$Work_Order_Task_Reading_Table = "pm_au_work_order_reading";
						$WorkOrder_Reading_or_Task = "R";
						break;  
			
			case "AU_Task_Generate":
			
					$sql = "SELECT
							  pm_au_equipment_detail.BuildingID AS BuildingID,
							  pm_au_equipment_name.AU_Equipment_Name,
							  pm_au_equipment_task.AU_Equipment_Task_ID AS Task_Reading_ID,
							  Str_To_Date(Concat(SubString_Index(pm_au_equipment_task.Start_Date, ' ', 1), ' ',
							  pm_au_equipment_task.Startdate_month, ' ', SubString_Index(pm_au_equipment_task.Start_Date, ' ', -1)), '%M %d %Y') AS
							  WO_Create_Date,
							  DayName(Str_To_Date(Concat(SubString_Index(pm_au_equipment_task.Start_Date, ' ', 1), ' ',
							  pm_au_equipment_task.Startdate_month, ' ', SubString_Index(pm_au_equipment_task.Start_Date, ' ', -1)), '%M %d %Y')) AS
							  Initial_Day_of_Week,
							  pm_au_template_task.Parent_ID AS Parent_ID,
							  pm_au_template_task.AU_Template_Task_ID,
							  pm_au_template_task.Interval_Value AS Inveral,
							  pm_au_startdateadjustment.Name AS StartDateAdjustment,
							  pm_au_frequency.`Interval` AS Freq_Inverval_Addinit,
							  pm_au_frequency.Interval_Value AS Freq_Inverval_Value,
							  pm_au_frequency.AU_Frequency_ID AS Freq_ID,
							  pm_au_template_task.Seasonal_Task,
							  pm_au_template_task.Seasonal_Start_Date,
							  pm_au_template_task.Seasonal_End_Date,
							  pm_au_equipment_detail.AU_Equipment_Detail_ID,
							  pm_au_template_task.AU_Template_Designation_ID AS Template_Designation_ID,
    						  pm_au_equipment_detail.Equipment_Floor,
    						  pm_au_equipment_detail.Equipment_Unit
							FROM
							  pm_au_equipment_name
							  RIGHT JOIN pm_au_equipment_detail ON pm_au_equipment_name.AU_Equipment_Name_ID =
								pm_au_equipment_detail.AU_Equipment_Name_ID
							  RIGHT JOIN pm_au_equipment_task ON pm_au_equipment_detail.AU_Equipment_Detail_ID =
								pm_au_equipment_task.AU_Equipment_Detail_ID
							  LEFT JOIN pm_au_template_task ON pm_au_template_task.AU_Template_Task_ID = pm_au_equipment_task.AU_Template_Task_ID
							  LEFT JOIN pm_au_startdateadjustment ON pm_au_startdateadjustment.AU_sda_ID = pm_au_template_task.AU_sda_ID
							  LEFT JOIN pm_au_frequency ON pm_au_frequency.AU_Frequency_ID = pm_au_template_task.AU_Frequency_ID
							WHERE
							  Str_To_Date(Concat(SubString_Index(pm_au_equipment_task.Start_Date, ' ', 1), ' ',
							  pm_au_equipment_task.Startdate_month, ' ', SubString_Index(pm_au_equipment_task.Start_Date, ' ', -1)), '%M %d %Y') <=
							  ".  $SqlFilter ." AND
							  pm_au_startdateadjustment.Name <> ''  AND
  							  pm_au_equipment_detail.BuildingID = " . $PM_WO_Option_BuildingID ."
							ORDER BY
							  BuildingID,
							  pm_au_equipment_detail.AU_Equipment_Detail_ID";
											  
						$Task_Reading_Table = "pm_au_equipment_task";
						$Task_Reading_ID = "AU_Equipment_Task_ID";
				//		$Work_Order_Task_Reading_Table = "pm_au_work_order_task";
						$WorkOrder_Reading_or_Task = "T";
						break;
						
			case "TandR":
						$sql = "Select
							pm_au_equipment_detail.BuildingID As BuildingID,
							pm_au_equipment_name.AU_Equipment_Name,
							pm_au_equipment_readings.AU_Equipment_Readings_ID As Task_Reading_ID,
							Str_To_Date(Concat(SubString_Index(pm_au_equipment_readings.Start_Date, ' ', 1), ' ',
							pm_au_equipment_readings.Startdate_month, ' ', SubString_Index(pm_au_equipment_readings.Start_Date, ' ', -1)),
							'%M %d %Y') As WO_Create_Date,
							DayName(Str_To_Date(Concat(SubString_Index(pm_au_equipment_readings.Start_Date, ' ', 1), ' ',
							pm_au_equipment_readings.Startdate_month, ' ', SubString_Index(pm_au_equipment_readings.Start_Date, ' ', -1)),
							'%M %d %Y')) As Initial_Day_of_Week,
							pm_au_template_reading.Parent_ID As Parent_ID,
							pm_au_template_reading.AU_Template_Reading_ID  AS Template_TorR_ID,
							pm_au_template_reading.Interval_Value As Inveral,
							pm_au_equipment_detail.AU_Equipment_Detail_ID,
							pm_au_startdateadjustment.Name As StartDateAdjustment,
							pm_au_frequency.`Interval` As Freq_Inverval_Addinit,
							pm_au_frequency.Interval_Value As Freq_Inverval_Value,
							pm_au_frequency.AU_Frequency_ID As Freq_ID,
							pm_au_template_reading.Seasonal_Task,
							pm_au_template_reading.Seasonal_Start_Date,
							pm_au_template_reading.Seasonal_End_Date,
							pm_au_template_reading.AU_Template_Designation_ID As Template_Designation_ID,
							pm_au_equipment_detail.Equipment_Floor,
							pm_au_equipment_detail.Equipment_Unit,
							concat('R') As TorR
						From
							pm_au_equipment_name Right Join
							pm_au_equipment_detail On pm_au_equipment_name.AU_Equipment_Name_ID = pm_au_equipment_detail.AU_Equipment_Name_ID
							Right Join
							pm_au_equipment_readings On pm_au_equipment_detail.AU_Equipment_Detail_ID =
									pm_au_equipment_readings.AU_Equipment_Detail_ID Left Join
							pm_au_template_reading On pm_au_template_reading.AU_Template_Reading_ID =
									pm_au_equipment_readings.AU_Template_Reading_ID Left Join
							pm_au_frequency On pm_au_frequency.AU_Frequency_ID = pm_au_template_reading.AU_Frequency_ID Left Join
							pm_au_startdateadjustment On pm_au_startdateadjustment.AU_sda_ID = pm_au_template_reading.AU_sda_ID
						WHERE
							Str_To_Date(Concat(SubString_Index(pm_au_equipment_readings.Start_Date, ' ', 1), ' ',
							pm_au_equipment_readings.Startdate_month, ' ', SubString_Index(pm_au_equipment_readings.Start_Date, ' ', -1)),
							'%M %d %Y') <= ".  $SqlFilter ." AND
							pm_au_startdateadjustment.Name <> '' AND
							pm_au_equipment_detail.BuildingID = " . $PM_WO_Option_BuildingID ."
						Union 
						Select
							pm_au_equipment_detail.BuildingID As BuildingID,
							pm_au_equipment_name.AU_Equipment_Name,
							pm_au_equipment_task.AU_Equipment_Task_ID As Task_Reading_ID,
							Str_To_Date(Concat(SubString_Index(pm_au_equipment_task.Start_Date, ' ', 1), ' ',
							pm_au_equipment_task.Startdate_month, ' ', SubString_Index(pm_au_equipment_task.Start_Date, ' ', -1)),
							'%M %d %Y') As WO_Create_Date,
							DayName(Str_To_Date(Concat(SubString_Index(pm_au_equipment_task.Start_Date, ' ', 1), ' ',
							pm_au_equipment_task.Startdate_month, ' ', SubString_Index(pm_au_equipment_task.Start_Date, ' ', -1)),
							'%M %d %Y')) As Initial_Day_of_Week,
							pm_au_template_task.Parent_ID As Parent_ID, 
							pm_au_template_task.AU_Template_Task_ID AS Template_TorR_ID,
							pm_au_template_task.Interval_Value As Inveral,
							pm_au_equipment_detail.AU_Equipment_Detail_ID, 
							pm_au_startdateadjustment.Name As StartDateAdjustment,  
							pm_au_frequency.`Interval` As Freq_Inverval_Addinit,  
							pm_au_frequency.Interval_Value As Freq_Inverval_Value,  
							pm_au_frequency.AU_Frequency_ID As Freq_ID, 
							pm_au_template_task.Seasonal_Task,
							pm_au_template_task.Seasonal_Start_Date,
							pm_au_template_task.Seasonal_End_Date,  
							pm_au_template_task.AU_Template_Designation_ID As Template_Designation_ID,
							pm_au_equipment_detail.Equipment_Floor,
							pm_au_equipment_detail.Equipment_Unit,
							concat('T') As TorR
						From
							pm_au_equipment_name Right Join
							pm_au_equipment_detail On pm_au_equipment_name.AU_Equipment_Name_ID = pm_au_equipment_detail.AU_Equipment_Name_ID
							Right Join
							pm_au_equipment_task On pm_au_equipment_detail.AU_Equipment_Detail_ID = pm_au_equipment_task.AU_Equipment_Detail_ID
							Left Join
							pm_au_template_task On pm_au_template_task.AU_Template_Task_ID = pm_au_equipment_task.AU_Template_Task_ID Left Join
							pm_au_startdateadjustment On pm_au_startdateadjustment.AU_sda_ID = pm_au_template_task.AU_sda_ID Left Join
							pm_au_frequency On pm_au_frequency.AU_Frequency_ID = pm_au_template_task.AU_Frequency_ID
						Where
							Str_To_Date(Concat(SubString_Index(pm_au_equipment_task.Start_Date, ' ', 1), ' ',
							pm_au_equipment_task.Startdate_month, ' ', SubString_Index(pm_au_equipment_task.Start_Date, ' ', -1)), '%M %d %Y') <=
							".  $SqlFilter ." AND
							pm_au_startdateadjustment.Name <> ''  AND
							pm_au_equipment_detail.BuildingID = " . $PM_WO_Option_BuildingID ."
						Order By
							BuildingID,
							AU_Equipment_Detail_ID";		
			
						break;
			
			default:
					echo "WO-I-010 - No Task or Reading has been selected, will exit ! <br>\r";
					$conn->close();
					exit;
					break;
					  
		  
		}
		// echo "<br><br>" .$sql . "<br><br>";
		
		// Set Search building number to 0, use this for finding Work order number 
		$SearchBuilding_Number = 0;
		$WorkOrder_Number_Increment_on_ID = 0;
		$Active = True;
		$result = $conn->query($sql);
		
		if(isset($_GET['Task_or_Reading'])){ 
		?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td>Line ID</td>
            <td>Original Dates</td>
			<td>Current Day of Week</td>
		<!--    <td>Parent ID</td>
			<td>AU_Template_Task_ID</td>-->
			<td>Task/Reading Valid Day(s)</td>
			<td>Current Work Order Date</td>
			<td>Next Work Order Date</td>
		   <!-- <td>Frequency Intev Value</td>-->
		<!--    <td>Inveral multy</td>-->
			<td>Interval Addinit</td>
		 	<td>Equipment</td>
		   <td>Floor | Unit</td>
			<td>Start Date</td>
			<td>End Date</td>
            <td>WO Number</td>
			
		  </tr>
		   <tr>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
              <td>&nbsp;</td>
		     <td>&nbsp;</td>
			  <td>&nbsp;</td>
			 <!--  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>-->
			</tr>
		
		 <?php
		}
		 echo "WO-I-015 - Number of Task / Reading to be updated : " .  $result->num_rows ."<br><br> \r\r";
		 $RecordResults = "No Record Found... Have a nice day";
		 do{
			 $Daily_Task_Reading = "False";
					if ($result->num_rows > 0) {                                                                   // 2500 - Begin If ($result->num_rows > 0)
						// output data of each row
						//echo "WO-I-016 - Date Ending ". $PM_WO_Option_Date->format('Y-m-d') . " " . $row_PM_WO_Options["PM_Auto_Exclude"] . "<br><br>\r\r";
						$RecordResults = "Work Order database update complete ... Have a nice day";
						while($row = $result->fetch_assoc()) {
								
							$ExculdeDay = $row_PM_WO_Options["PM_Auto_Schedule"]. "-". $row_PM_WO_Options["PM_Auto_Exclude"]. "-".$row["Freq_Inverval_Addinit"];
							echo "WO-I-020 - Exclude Options: " . $ExculdeDay."<br><br />\r\r";
							switch($ExculdeDay){
						
									case 'M-N-DAY':  // M = Month, N = Created daily work order for the whole month, DAY = Only if the Frequency is Daily
											$DailyStartDate = new DateTime('now  America/New_York');        // Retrieve the current Start Date from Database
											$Daily_Task_Reading = "TRUE"; 									// If True then run query again until false
											include "genworkorder.php";
											break;
									case 'M-N-WEEK': // M = Month, N = Created daily work order for the whole month, WEEK = Only if the Frequency is Weekly
											$DailyStartDate = new DateTime('now  America/New_York');        // Retrieve the current Start Date from Database
											$Daily_Task_Reading = "TRUE"; 									// If True then run query again until false
											include "genworkorder.php";
											break;
									case 'W-N-DAY': // M = Month, N = Created daily work order for the whole month, WEEK = Only if the Frequency is Weekly
											$DailyStartDate = new DateTime('now  America/New_York');        // Retrieve the current Start Date from Database
											$Daily_Task_Reading = "TRUE"; 									// If True then run query again until false
											include "genworkorder.php";
											break;
									case 'W-N-WEEK': // M = Month, N = Created daily work order for the whole month, WEEK = Only if the Frequency is Weekly
											$DailyStartDate = new DateTime('now  America/New_York');        // Retrieve the current Start Date from Database
											$Daily_Task_Reading = "TRUE"; 									// If True then run query again until false
											include "genworkorder.php";
											break;

									default:
											$Daily_Task_Reading = "False";
											include "genworkorder.php";
											break;
							}
									
						}
							 

					} else {
						
						echo "$RecordResults  <br>\r "; //
						echo "*************************************************************************************************************************************************<br><br><br>\r\r";
						// echo $sql ." <br><br><br><br>";
					}  // 2500 - End If ($result->num_rows > 0)
					 if ($Daily_Task_Reading == "TRUE") {
						 echo "WO-I-018 - Run query again is  : ". $Daily_Task_Reading . " | This WO is a Daily/Weekly Task/Reading, but will generate for the extire month<br>\r";
						 mysqli_free_result($result); 	// clear result set
						 switch($ExculdeDay){
								case 'M-N-DAY':
									echo "WO-I-021 - PM Report Generate: Create Daily Work Orders with Unique WO Numbers : " .$row_PM_WO_Options["PM_Reports_Exclude_Daily"] . "<br>\r";
									if ($row_PM_WO_Options["PM_Reports_Exclude_Daily"] == 'Y'){$SearchBuilding_Number = 0;} break;
								case 'M-N-WEEK':
									echo "WO-I-022 - PM Report Generate: Create Weekly Work Orders with Unique WO Numbers : " .$row_PM_WO_Options["PM_Reports_Exclude_Weekly"] . "<br>\r";
									if ($row_PM_WO_Options["PM_Reports_Exclude_Weekly"] == 'Y'){$SearchBuilding_Number = 0;} break;
								case 'W-N-DAY':
									echo "WO-I-023 - PM Report Generate: Create Daily Work Orders with Unique WO Numbers : " .$row_PM_WO_Options["PM_Reports_Exclude_Daily"] . "<br>\r";
									if ($row_PM_WO_Options["PM_Reports_Exclude_Daily"] == 'Y'){$SearchBuilding_Number = 0;} break;
								case 'W-N-WEEK':
									echo "WO-I-024 - PM Report Generate: Create Weekly Work Orders with Unique WO Numbers : " .$row_PM_WO_Options["PM_Reports_Exclude_Weekly"] . "<br>\r";
									if ($row_PM_WO_Options["PM_Reports_Exclude_Weekly"] == 'Y'){$SearchBuilding_Number = 0;} break;							
																	
						 }
						 $result = $conn->query($sql);
						 mysqli_data_seek($result, 0);
					 }
		 } while ($Daily_Task_Reading == "TRUE");
		 
		 				if(isset($_GET['Task_or_Reading'])){ 		
						?>
						 
					</table>	 
					<?php
						}
		if ($row_PM_WO_Options["PM_Auto_Schedule"] == "E" or $row_PM_WO_Options["PM_Auto_Schedule"] == "F" ){ // E: Evry Date, F: Monday thru Friday
			break;
		}
	  }                                                                                              	    // 1600 - End of foreach ($Schedule_Array as $SQL_Filter)
	 echo "==========================================================================================================================================================<br><br> \r \r";
	}                                                                                                      // 1000 - End of while($row_PM_WO_Options = $result_PM_WO_Options->fetch_assoc()) Statement
}                                                                                                          // 0990 - End of If statement | if($result_PM_WO_Options->num_rows>0)



function SetupArray($ExcludeOps, $DateDay, $PM_WO_Option_Date ){
	
	switch ($ExcludeOps){
		case 'N-N':			// Combine Daily, Weekly and Monthly Do not Exclude any
			return $Schedule_Array = array                                                                                // Setup Array
			(
				array("Day", "'" . $PM_WO_Option_Date->format('Y-m-d'). "'" ),                                    // Select today's date for 'Day'
			);
			break;
		case 'Y-N':			// Exclude Daily = Y, Exclude Weekly = "N" | Create Daily separately, combine Weekly and Monthly
			return $Schedule_Array = array                                                                                              // Setup Array
			(
				array("Day", "'" . $DateDay->format('Y-m-d') . "' AND pm_au_frequency.`Interval` = 'DAY'"),                    // Generate Daily for $DateDay from above VAR
				array("Week", "'" . $PM_WO_Option_Date->format('Y-m-d') . "' AND pm_au_frequency.`Interval` != 'DAY'")         // Combine Weekly and Monthly
			);
			break;
		case 'Y-Y':				// Both Exclude Daily = Y, Exclude Weekly = "N"	 | Seperate Daily, Weekly and Monthly
			return $Schedule_Array = array                                                                                              // Setup Array
			(
				array("Day", "'" . $DateDay->format('Y-m-d') . "' AND pm_au_frequency.`Interval` = 'DAY'"),                      // Generate Daily for $DateDay from above VAR
				array("Week", "'" . $PM_WO_Option_Date->format('Y-m-d') . "' AND pm_au_frequency.`Interval` = 'WEEK'"),          // Set week to $PM_WO_Option_Date

				array("Month", "'" . $PM_WO_Option_Date->format('Y-m-d') . 
					   "' AND pm_au_frequency.`Interval` != 'DAY' AND pm_au_frequency.`Interval` != 'WEEK'")                     // Set Month to $PM_WO_Option_Date
			);
			break;
		}
		
	
	
}
$conn->close();
?>