
<?php
$servername = "localhost";
$username = "ve";
$password = "Vision@4424";
$dbname = "ve_crm_new";

echo "Connecting ...................</br>";

	
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

// Retrieve all Buildings that have thier "PM_Auto_Create_Jobs" set to "Y"
$PM_WO_Option_Date = new DateTime('now  America/New_York');
$Todays_Days = new DateTime('now  America/New_York');
echo "DB-I-004 - Todays Date :       " . $Todays_Days->format('Y-m-d h:i:sa') . "<br><br> \r\r\r";
echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++<br> \r";

$sql_PM_WO_Options = "SELECT
						  buildings.buildingName,
						  company.companyName,
						  company.company_logo,
						  pm_au_work_order_options.*
						FROM
						  pm_au_work_order_options
						  LEFT JOIN buildings ON buildings.build_id = pm_au_work_order_options.BuildingID
						  LEFT JOIN company ON company.cust_id = buildings.cust_id
						WHERE 
							   pm_au_work_order_options.PM_Auto_Create_Jobs = 'Y'
						ORDER BY
						  pm_au_work_order_options.BuildingID";
						  
						  
//echo "DB-I-003 - SQL Connection : " . 	$sql_PM_WO_Options . "</br> \r";

					  
$result_PM_WO_Options = $conn->query($sql_PM_WO_Options);

if($result_PM_WO_Options->num_rows>0) {                                                         // 0900 - Begin IF statement
	while($row_PM_WO_Options = $result_PM_WO_Options->fetch_assoc()){                           // 1000 - Begin While Loop
         echo $row_PM_WO_Options;
		//''!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$PM_WO_Option_BuildingID = $row_PM_WO_Options["BuildingID"];                           // Set Building ID
		// Remove the number to when testing is over
		
		//''!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$PM_WO_Option_Date = new DateTime('now  America/New_York');                            // Set the Time Zone
		echo "OP-I-005 - Building Number : " . $PM_WO_Option_BuildingID . "<br> \r";
		echo "OP-I-006 - Building Name : " . $row_PM_WO_Options["buildingName"] . "<br> \r";
		echo "OP-I-007 - Company: " . $row_PM_WO_Options["company_logo"] . "<br> \r";
		//echo "==============================================================<br> \r";
		echo "OP-I-008 - Auto-created Work Orders " . $row_PM_WO_Options["PM_Auto_Create_Jobs"] . "<br> \r";
		
		//echo "==============================================================<br> \r";

		include "pm_email_generate.php";

		                                                                                  	    // 1600 - End of foreach ($Schedule_Array as $SQL_Filter)
	 echo "==========================================================================================================================================================<br><br> \r \r";
	}                                                                                                      // 1000 - End of while($row_PM_WO_Options = $result_PM_WO_Options->fetch_assoc()) Statement
}                                                                                                          // 0990 - End of If statement | if($result_PM_WO_Options->num_rows>0)




$conn->close();
?>