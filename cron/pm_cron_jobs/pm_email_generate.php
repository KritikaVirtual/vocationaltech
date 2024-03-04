<?php



	
	
// Set Start_Date_Adjut to NUll
$Current_Work_Order_Date = "" ;


// Check if the building Number and the SearchBuiding_number are the same
// IF not search the Work Order Table and find the last work order number for building
// Add "1" to the last work order number and asign all new work orders
// for that building.

	$SearchBuilding_Number = $row_PM_WO_Options["BuildingID"];
	$WorkOrder_Number_Increment_on_ID = $row["AU_Equipment_Detail_ID"];
// echo "Building Number : " . $SearchBuilding_Number . " <br> \r";
$SQL_Email_to ="Select
			    pm_au_work_order.BuildingID As Max_BuildingID,
			    Count(pm_au_work_order.PM_WO_Number) As Max_PM_WO_Number,
				pm_au_work_order.PM_WO_SendEMail,
				email_group.group_name,
				users.userName,
				users.firstName,
				users.lastName,
				users.email,
				users.uid
			From
				pm_au_work_order Left Join
				email_group On email_group.building_id = pm_au_work_order.BuildingID Left Join
				email_group_users On email_group_users.group_id = email_group.id Left Join
				users On users.uid = email_group_users.user_id
			Where
				pm_au_work_order.BuildingID = ". $SearchBuilding_Number ."  And
				pm_au_work_order.PM_WO_SendEMail = 'Y' And
				email_group.group_name = 'PM-WorkOrders'
			Group By
				pm_au_work_order.BuildingID,
				pm_au_work_order.PM_WO_SendEMail,
				email_group.group_name,
				users.userName,
				users.firstName,
				users.lastName,
				users.email
			Order By
				users.lastName,
				users.firstName";

// echo "SQL " . $SQL_Email_to . "<br> \r";
	
$result_ReturnedEmails = $conn->query($SQL_Email_to);

if($result_ReturnedEmails->num_rows>0) {  
		while($row_Returned_Emails = $result_ReturnedEmails->fetch_assoc()){   

			echo "<br> \r";
                                                                                             // 1700 - End IF Statement
			echo "WO-I-100 - Send Email To : " . $row_Returned_Emails["firstName"] . " " . $row_Returned_Emails["lastName"] ."<br>\r"; 
			echo "WO-I-110 - Email Address : " . $row_Returned_Emails["email"]."<br>\r"; 	
			echo "WO-I-120 - User-Id : " . $row_Returned_Emails["uid"]."<br>\r"; 
		
			
			$SQL_Created_WOs = "SELECT
								  pm_au_work_order.BuildingID AS BuildingID,
								  pm_au_equipment_name.AU_Equipment_Name,
								  pm_au_work_order.PM_WO_Number AS WO_Number,
								  pm_au_equipment_detail.Equipment_Floor,
								  pm_au_equipment_detail.Equipment_Unit,
								  pm_au_equipment_detail.Equipment_Make_Model,
								  pm_au_equipment_detail.Equipment_Location,
								  pm_au_equipment_detail.Equipment_Serial_Number,
								  pm_au_equipment_detail.Equipment_Inservice_Date,
								  pm_au_equipment_detail.Equipment_Notes,
								  pm_au_equipment_detail.Equipment_Image,
								  pm_au_equipment_detail.Equipment_Status,
								  pm_au_template_name.AU_Template_Name AS Template_Group_Name,
								  pm_au_template_typedesignation.AU_TypeDesignation AS Template_Type_Designation
								FROM
								  pm_au_work_order
								  LEFT JOIN pm_au_equipment_detail ON pm_au_equipment_detail.AU_Equipment_Detail_ID =
									pm_au_work_order.AU_Equipment_Detail_ID
								  LEFT JOIN pm_au_equipment_name ON pm_au_equipment_name.AU_Equipment_Name_ID =
									pm_au_equipment_detail.AU_Equipment_Name_ID
								  LEFT JOIN pm_au_template_typedesignation ON pm_au_template_typedesignation.AU_Template_Designation_ID =
									pm_au_work_order.AU_Template_Designation_ID
								  LEFT JOIN pm_au_template_name ON
									pm_au_template_name.AU_Template_Name_ID = pm_au_template_typedesignation.AU_Template_Name_ID
								WHERE
								  pm_au_work_order.BuildingID IN (". $SearchBuilding_Number .")  AND
								  pm_au_equipment_detail.Equipment_Status = 1
								GROUP BY
								  pm_au_work_order.BuildingID,
								  pm_au_equipment_name.AU_Equipment_Name,
								  pm_au_work_order.PM_WO_Number,
								  pm_au_equipment_detail.Equipment_Floor,
								  pm_au_equipment_detail.Equipment_Unit,
								  pm_au_equipment_detail.Equipment_Make_Model,
								  pm_au_equipment_detail.Equipment_Location,
								  pm_au_equipment_detail.Equipment_Serial_Number,
								  pm_au_equipment_detail.Equipment_Inservice_Date,
								  pm_au_equipment_detail.Equipment_Notes,
								  pm_au_equipment_detail.Equipment_Image,
								  pm_au_equipment_detail.Equipment_Status,
								  pm_au_template_name.AU_Template_Name,
								  pm_au_template_typedesignation.AU_TypeDesignation
								ORDER BY
								  BuildingID,
								  WO_Number";
			$result_ReturnedWOs = $conn->query($SQL_Created_WOs);
		
            $senderquery = "SELECT from_name, from_email FROM `setting`";
		    $result_sender = $conn->query($senderquery);
		    $row_sender_data = $result_sender->fetch_assoc();
		    
           require_once '/home/ve/public_html/cron/init.php';
		    $emailMapper = new Model_Email();
            $pmLoadTemplate = $emailMapper->loadEmailTemplate(65);
           if ($pmLoadTemplate) {
            $emailTemplate = $pmLoadTemplate[0];
            $subject = $emailTemplate['email_subject'];
            $content = $emailTemplate['email_content'];
            
           
        /* * *****Get voc-tech logo******* */
         $uri = "https://qaworkorder.com/";
        $sdModel = new Model_SystemDefault();
        $sdData = $sdModel->getSystemDefault();
        $emailContent = $sdData[0];
        $voc_logo = $emailContent['voc_logo'];

        if (isset($voc_logo) && !empty($voc_logo)) {
            $voctech_logo_src = '<img src="' . $uri . 'public/images/uploads/' . $voc_logo . '">';
        } else {
            $voctech_logo_src = "";
        }
        /* * *****Get Company Data******* */

        // Company logo
        if (isset($row_PM_WO_Options["company_logo"]) && !empty($row_PM_WO_Options["company_logo"])) {
            $building_logo_src = '<img src="' . $uri . 'public/images/clogo/' . $row_PM_WO_Options["company_logo"] . '">';
        } else {
            //$building_logo_src	=	'<img src="'.$uri.'/public/images/logo.png">';				
            $building_logo_src = '';
        }
			
		}
		         ///// header 
            $content = str_replace('[[++companyLogo]]', $building_logo_src, $content);
            $content = str_replace('[[++voctechLogo]]', $voctech_logo_src, $content);
         ///// end header
         
          $currDate = date('F d, Y');
          $content = str_replace('[[++requestDate]]', $currDate, $content);
          $content = str_replace('[[++companyName]]', $row_PM_WO_Options["companyName"], $content);
         
          if (isset($PM_WO_Option_BuildingID)) {
            $building = new Model_Building();
            $buildingDetail = $building->getbuildingbyid($PM_WO_Option_BuildingID);
            if ($buildingDetail) {
			$buildingData = $buildingDetail[0];
            $content = str_replace('[[++buildingName]]', $buildingData['buildingName'], $content);
            $content = str_replace('[[++phone]]', $buildingData['phoneNumber'], $content);
            $content = str_replace('[[++address1]]', $buildingData['address'], $content);
            $content = str_replace('[[++city]]', $buildingData['city'], $content);
			$content = str_replace('[[++state]]', $buildingData['state'], $content);
			$content = str_replace('[[++postalCode]]', $buildingData['postalCode'], $content);
			$content = str_replace('[[++User_Id]]', $row_Returned_Emails["uid"], $content);
			$content = str_replace('[[++costNumber]]', $buildingData['uniqueCostCenter'], $content);
			$content = str_replace('[[++buildingID]]', $PM_WO_Option_BuildingID, $content);
		
            }
          }
          	if($result_ReturnedWOs->num_rows>0) { 
			   while($row_Returned_WOs = $result_ReturnedWOs->fetch_assoc()){ 
			          $work_rows .= "<tr>
			          
				        <td style='width:20%'><a href='https://qaworkorder.com/vnsreports/index.php?report_key=PM%20-%20Work%20Order%20Report%20-%20Separeted%20Jobs%20link.mrt&Cost_Center_Number=".$buildingData['uniqueCostCenter']."&buildkey=". $PM_WO_Option_BuildingID."&User=".$row_Returned_Emails["uid"]."&PM_WO_Number=".$row_Returned_WOs['WO_Number']."' target='_blank'>".$row_Returned_WOs['WO_Number']."</a></td>
				        <td style='width:20%'><a href='https://qaworkorder.com/vnsreports/index.php?report_key=PM%20-%20Work%20Order%20Report%20-%20Separeted%20Jobs%20link.mrt&Cost_Center_Number=".$buildingData['uniqueCostCenter']."&buildkey=". $PM_WO_Option_BuildingID."&User=".$row_Returned_Emails["uid"]."&PM_WO_Number=".$row_Returned_WOs['WO_Number']."' target='_blank'>".$row_Returned_WOs['AU_Equipment_Name']."</a></td>
				        <td style='width:20%'><a href='https://qaworkorder.com/vnsreports/index.php?report_key=PM%20-%20Work%20Order%20Report%20-%20Separeted%20Jobs%20link.mrt&Cost_Center_Number=".$buildingData['uniqueCostCenter']."&buildkey=". $PM_WO_Option_BuildingID."&User=".$row_Returned_Emails["uid"]."&PM_WO_Number=".$row_Returned_WOs['WO_Number']."' target='_blank'>".$row_Returned_WOs['Equipment_Floor']."</a></td>
				        <td style='width:20%'><a href='https://qaworkorder.com/vnsreports/index.php?report_key=PM%20-%20Work%20Order%20Report%20-%20Separeted%20Jobs%20link.mrt&Cost_Center_Number=".$buildingData['uniqueCostCenter']."&buildkey=". $PM_WO_Option_BuildingID."&User=".$row_Returned_Emails["uid"]."&PM_WO_Number=".$row_Returned_WOs['WO_Number']."' target='_blank'>".$row_Returned_WOs['Equipment_Unit']."</a></td>
				        <td style='width:20%'><a href='https://qaworkorder.com/vnsreports/index.php?report_key=PM%20-%20Work%20Order%20Report%20-%20Separeted%20Jobs%20link.mrt&Cost_Center_Number=".$buildingData['uniqueCostCenter']."&buildkey=". $PM_WO_Option_BuildingID."&User=".$row_Returned_Emails["uid"]."&PM_WO_Number=".$row_Returned_WOs['WO_Number']."' target='_blank'>".$row_Returned_WOs['Equipment_Location']."</a></td>
				       </tr>";
				
				}
           
           }
          $content = str_replace('[[++wo_details]]', $work_rows, $content);
        
          
          	///// Footer 
         $sdModel = new Model_SystemDefault();
        $sdData = $sdModel->getSystemDefault();
        $emailContent = $sdData[0];
        $footer_info = $emailContent['footer_info'];
        $content = str_replace('[[++footerInfo]]',  $footer_info, $content);
            ///// End Footer
          
         // Mail code start from the Here
   $to = $row_Returned_Emails["email"];
  // $to = 'durgeshchaubey@virtualemployee.com';
   $subject =  $subject;
   
//$message = '<html><body>';   
$message = $content;
//$message = '</body></html>';

$headers = "From: ".$row_sender_data['from_name']."<".$row_sender_data['from_email'].">\r\n";
$headers .= "Reply-To: -fsupport@visionworkorders.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
 
   try {
    $retval = mail($to,$subject,$message,$headers);
    echo "Message sent!<br />\n";
} catch (Exception $ex) {
    echo "Failed to send mail! " . $ex->getMessage() . "<br />\n";
}
          
          
	}
		
		
		
		
}else {
			echo "WO-I-150 - Send Email has not been configured, <br> \r \t &ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp; To Configured create an Email Distrubition Group named 'PM-WorkOrders' <br> \r \t  &ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp; and then add users to group <br> \r";
		}
$result_ReturnedEmails->close();
?>
