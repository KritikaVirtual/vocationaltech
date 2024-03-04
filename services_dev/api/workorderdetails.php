<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['woid'])) {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
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
            $response['status'] = 200;
            $response['wo_number'] = $work_order['wo_number'];
            $response['tenantName'] = stripslashes($work_order['tenantName']);
            $response['categoryName'] = stripslashes($work_order['categoryName']);
            $response['work_order_request'] = stripslashes($work_order['work_order_request']);
            $response['date_requested'] = $work_order['date_requested'];
            $response['time_requested'] = $time_requested;
            $response['updated_date'] = $updated_date;
            $response['updated_time'] = $updated_time;
            $response['wo_status'] = $work_order['wo_status'];
            $response['building_id'] = $work_order['building'];
			$response['attachment'] = $work_order['attachment'];
			$response['buildingName'] = stripslashes($work_order['buildingName']);
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['status'] = 400;
            $response['error_msg'] = "Invalid Work Order Number Or Building Id";
            echo json_encode($response);
        }

} else {
    $response['status'] = 400;
	$response['error'] = TRUE;
	$response['error_msg'] = "Required parameter is missing";
	echo json_encode($response);
}



?>