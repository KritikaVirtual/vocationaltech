<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if(empty($_POST['buildings_list']) && $_POST['buildings_list'] == ""){
    $response['status'] = 400;
	$response['error'] = TRUE;
	$response['error_msg'] = "Please select a building from the list";
	echo json_encode($response);
	exit();
}
if(empty($_POST['company_list']) && $_POST['company_list'] == ""){
    $response['status'] = 400;
	$response['error'] = TRUE;
	$response['error_msg'] = "Please select a tenant from the list";
	echo json_encode($response);
	exit();
}
if(empty($_POST['tenantusers_list']) && $_POST['tenantusers_list'] == ""){
    $response['status'] = 400;
	$response['error'] = TRUE;
	$response['error_msg'] = "Please select requested by";
	echo json_encode($response);
	exit();
}
if(empty($_POST['category_list']) && $_POST['category_list'] == ""){
    $response['status'] = 400;
	$response['error'] = TRUE;
	$response['error_msg'] = "Please select a category";
	echo json_encode($response);
	exit();
}
if(empty($_POST['wo_request']) && $_POST['wo_request'] == ""){
    $response['status'] = 400;
	$response['error'] = TRUE;
	$response['error_msg'] = "Please enter work order request";
	echo json_encode($response);
	exit();
}

if (isset( $_POST['buildings_list']) && !empty($_POST['buildings_list']) && isset( $_POST['company_list']) && !empty($_POST['company_list'])) {
   //print_r($_POST);
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
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

        if (!empty($save)) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['tenant'] = $save;
            $response['woId'] = $save['woId'];
            echo json_encode($response);

        } else {
            $response['status'] = 400;
            $response['error'] = TRUE;
            $response['error_msg'] = "Something went wrong.";
            echo json_encode($response);
        }

}



?>