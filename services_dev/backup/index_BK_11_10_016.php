<?php
header('Access-Control-Allow-Origin: *');
include_once('constant.php');
if (isset($_POST['tag']) && $_POST['tag'] != "") {
    $tag = $_POST['tag'];
    require_once 'include/DBFunctions.php';
    $db = new DBFunctions();
    $response = array("tag" => $tag, "error" => FALSE);
    if ($tag == "login") {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $user = $db->getLogin($username, $password);

        if ($user != FALSE) {
            $response['error'] = FALSE;
            $response['role_id'] = $user['role_id'];
            $response['uid'] = $user['uid'];
            $response['cust_id'] = $user['cust_id'];
            $response['user']['firstName'] = $user['firstName'];
            $response['user']['lastName'] = $user['lastName'];
            $response['user']['email'] = $user['email'];
            $response['user']['phoneNumber'] = $user['phoneNumber'];
			
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "incorrect email or password";
            echo json_encode($response);
        }
    } else if($tag == "getAccessMatrix") {
		 $response = $db->getAccessMatrix($_POST['role']);
		  echo json_encode($response);
	
	}else if ($tag == "predefined_notes") {
		$cust_id='';

		if(isset($_POST['cust_id'])) {
			$cust_id = $_POST['cust_id'];
		}
        $notes = $db->getPredefinedNotes($cust_id);

        if ($notes != FALSE) {
            $response['error'] = FALSE;
            $response['predefined_notes'] = $notes;
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "schedule_status") {
        if (isset($_POST['selected'])) {
            $selected = $_POST['selected'];
        } else {
            $selected = "";
        }
        $status = $db->getStatus($selected);

        if ($status != FALSE) {
            $response['error'] = FALSE;
            $response['status_list'] = $status;
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "check_work_order_exist") {

        $building_id = $_POST['building_id'];
        $wo_number = $_POST['wo_number'];
        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];

        $woid = $db->checkWorkOrderExist($role_id, $uid, $cust_id, $wo_number, $building_id);

        if ($woid != "FALSE") {
            $response['error'] = FALSE;
            $response['wo_exist'] = 'TRUE';
            $response['woid'] = $woid;
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['wo_exist'] = 'FALSE';
            echo json_encode($response);
        }

    } else if ($tag == "get_work_order") {
        if (isset($_POST['filter']) && !empty($_POST['filter'])) {
            $filter = $_POST['filter'];
        } else {
            $filter = "1,2";
        } 
		$woid_array = '';
		$workorderid='';
        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id']; 
		if(isset($_POST['woid_array'])) {
			$woid_array = $_POST['woid_array']; 
		}
		if(isset($_POST['onlyWorkorder'])) {
			$filter = ""; 
			if(isset($_POST['workorderid'])) {
				$workorderid = $_POST['workorderid']; 
			}
		}

        $work_order = $db->getWorkOrder($role_id, $uid, $cust_id, $filter, $woid_array,$workorderid);

        if ($work_order != FALSE) {
            $response['error'] = FALSE;
            $response['wo_list'] = $work_order;
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No results found .";
            echo json_encode($response);
        }

    } else if ($tag == "work_order_detail" && isset($_POST['woid'])) {

        $woid = $_POST['woid'];
        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];

        $work_order = $db->getWorkOrderDetail($role_id, $uid, $cust_id, $woid);

        if ($work_order != FALSE) {

            $updated = explode(" ", $work_order['created_at']);
            $updated_date = $updated[0];
            $updated_time = $updated[1];
            $updated_time = date("g:i a", strtotime($updated_time));
            $time_requested = date("g:i a", strtotime($work_order['time_requested']));

            $response['error'] = FALSE;
            //$response['wo'] = $work_order;
            $response['wo_number'] = $work_order['wo_number'];
            $response['tenantName'] = $work_order['tenantName'];
            $response['categoryName'] = $work_order['categoryName'];
            $response['work_order_request'] = $work_order['work_order_request'];
            $response['date_requested'] = $work_order['date_requested'];
            $response['time_requested'] = $time_requested;
            $response['updated_date'] = $updated_date;
            $response['updated_time'] = $updated_time;
            $response['wo_status'] = $work_order['wo_status'];
            $response['building_id'] = $work_order['building'];
			$response['attachment'] = $work_order['attachment'];
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "Invalid Work Order Number Or Building Id";
            echo json_encode($response);
        }

    } else if ($tag == "description_of_work") {

        $woid = $_POST['woid'];
        $description = $db->getDescriptionOfWork($woid);

        if ($description != FALSE) {
            $response['error'] = FALSE;
            $response['description'] = stripslashes($description);
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "wo_labor") {

        $woid = $_POST['woid'];
        $wo_labor = $db->getLabor($woid);

        if ($wo_labor != FALSE) {
            $response['error'] = FALSE;
            $response['wo_labor'] = stripslashes($wo_labor['html']);
            $response['total_labor_charge'] = $wo_labor['total_labor_charge'];
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "wo_building_services") {

        $woid = $_POST['woid'];
        $wo_building_services = $db->getBuildingServices($woid);

        if ($wo_building_services != FALSE) {
            $response['error'] = FALSE;
            $response['wo_building_services'] = stripslashes($wo_building_services['html']);
            $response['total_bs_charge'] = $wo_building_services['total_bs_charge'];
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "wo_materials") {

        $woid = $_POST['woid'];
        $wo_materials = $db->getMaterials($woid);

        if ($wo_materials != FALSE) {
            $response['error'] = FALSE;
            $response['wo_materials'] = stripslashes($wo_materials['html']);
            $response['total_material_charge'] = $wo_materials['total_material_charge'];

            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "wo_outside_services") {

        $woid = $_POST['woid'];
        $wo_outside_services = $db->getOutsideServices($woid);

        if ($wo_outside_services != FALSE) {
            $response['error'] = FALSE;
            $response['wo_outside_services'] = stripslashes($wo_outside_services['html']);
            $response['total_outside_charge'] = $wo_outside_services['total_outside_charge'];
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "wo_notes") {

        $woid = $_POST['woid'];
        $wo_notes = $db->getNotes($woid);

        if ($wo_notes != FALSE) {
            $response['error'] = FALSE;
            $response['wo_notes'] = stripslashes($wo_notes);
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "update_wo_status") {

        $woid = $_POST['woid'];
        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];
        $building_id = $_POST['building_id'];
        $wo_status = $_POST['wo_status'];
        $new_wo_status = $_POST['new_wo_status'];

        $wo_notes = $db->updateWorkOrderStatus($woid, $role_id, $uid, $cust_id, $wo_status, $new_wo_status, $building_id);

        if ($wo_notes != FALSE) {
            $response['error'] = FALSE;
            $response['wo_notes'] = stripslashes($wo_notes);
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "test") {
        $building_id = $_POST['building_id'];
        $test = $db->setTimezone($building_id);

        if ($test != FALSE) {
            $response['error'] = FALSE;
            $response['test'] = stripslashes($test);
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "get_buildings_by_user") {

        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];

        $buildings1 = $db->getBuildingsByUser($role_id, $uid, $cust_id);
        $buildings = explode(":", $buildings1);

        if ($buildings1 != FALSE) {
            $response['error'] = FALSE;
            $response['buildings'] = stripslashes($buildings[0]);
            $response['first_building'] = stripslashes($buildings[1]);
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "get_company_by_user") {

        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];
        $build_id = $_POST['build_id'];

        $company1 = $db->getCompanyByUser($role_id, $uid, $cust_id, $build_id);
        $company = explode(":", $company1);

        if ($company1 != FALSE) {
            $response['error'] = FALSE;
            $response['company'] = stripslashes($company[0]);
            $response['first_companyuser'] = stripslashes($company[1]);
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "get_tenants") {

        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];
        $tnadmin_id = $_POST['tnadmin_id'];

        $company = $db->getTenants($role_id, $uid, $cust_id, $tnadmin_id);

        if ($company != FALSE) {
            $response['error'] = FALSE;
            $response['tenantusers'] = stripslashes($company);
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "get_company_category") {

        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];
        $build_id = $_POST['build_id'];

        $category = $db->getCompanyCategory($role_id, $uid, $cust_id, $build_id);

        if ($category != FALSE) {
            $response['error'] = FALSE;
            $response['category'] = stripslashes($category);
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "get_building_services") {

        $build_id = $_POST['build_id'];

        $services = $db->getBuildingServicesPopUp($build_id);

        if ($services != FALSE) {
            $response['error'] = FALSE;
            $response['services_list'] = stripslashes($services);
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "update_services_cost") {

        $bsid = $_POST['bsid'];

        $data = $db->getUpdateServicesCost($bsid);
        $data = explode(";", $data);
        $cost = $data[0];
        $minimum = $data[1];
        if ($data != FALSE) {
            $response['error'] = FALSE;
            $response['cost'] = stripslashes($cost);
            $response['minimum'] = stripslashes($minimum);
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "get_Date_And_Time") {

        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];
        $build_id = $_POST['build_id'];

        $DatenTime = $db->getBuildingDateAndTime($role_id, $uid, $cust_id, $build_id);
        $DateTime = explode(";", $DatenTime);

        if ($DatenTime != FALSE) {
            $response['error'] = FALSE;
            $response['date'] = stripslashes($DateTime[0]);
            $response['time'] = stripslashes($DateTime[1]);
            $response['timezone'] = stripslashes($DateTime[2]);
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }

    } else if ($tag == "create_save_work_order") {

        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];
        $buildings_list = $_POST['buildings_list'];
        $company_list = $_POST['company_list'];
        $tenantusers_list = $_POST['tenantusers_list'];
        $category_list = $_POST['category_list'];
        $current_date_field = $_POST['current_date_field'];
        $current_time_field = $_POST['current_time_field'];
        $internal_wo = $_POST['internal_wo'];
        $wo_request = $_POST['wo_request'];
        $wo_notes = $_POST['wo_notes'];
        //$wo_file = $_FILES['wo_file']; 
		if(isset($_POST['file_name']))  {
			$file_name = $_POST['file_name'];
			$save = $db->createSaveWorkOrder($role_id, $uid, $cust_id, $buildings_list, $company_list, $tenantusers_list, $category_list, $current_date_field, $current_time_field, $internal_wo, $wo_request, $wo_notes, $file_name);
		} else {
			$save = $db->createSaveWorkOrder($role_id, $uid, $cust_id, $buildings_list, $company_list, $tenantusers_list, $category_list, $current_date_field, $current_time_field, $internal_wo, $wo_request, $wo_notes);
		}

        
        //print_r($save);
        //die;

        if ($save != FALSE) {
            $response['error'] = FALSE;
            $response['tenant'] = $save;
            echo json_encode($save);

        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "Something went wrong.";
            echo json_encode($response);
        }

    } else if ($tag == "get_wo_description") {
        $woid = $_POST['woid'];
        $desc = $db->getWoDescription($woid);
        if ($desc != FALSE) {
            $response['error'] = FALSE;
            $response['desc'] = $desc;
            echo json_encode($response);
        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = "Something went wrong.";
            echo json_encode($response);
        }
    }  
	else {
        $response['error'] = TRUE;
        $response['error_msg'] = "Unknown action attempt.";
        echo json_encode($response);
    }
} else {
	if (isset($_GET['tag']) && $_GET['tag'] != "") {
		$tag = $_GET['tag'];
		if ($tag == "view_file"){
		$filename = $_GET['file'];
			if(!empty($filename)){ 
				$info = new SplFileInfo($filename);
				if(strtolower($info->getExtension()) !='pdf') { 
					header('Content-Type: image/png');  
					readfile(BASE_PATH."public/work_order/".$filename); 
				} else {
				  header('Content-Type: application/pdf'); 
				  readfile(BASE_PATH."public/work_order/".$filename);
				  
				  }				
			}
		}
	}
	else{
		$response['error'] = TRUE;
		$response['error_msg'] = "Required parameter is missing";
		echo json_encode($response);
	}
}


?>