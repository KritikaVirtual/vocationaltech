<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['woid']) && $_POST['woid'] != "") {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
        
        $woid = $_POST['woid'];
        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];
        $building_id = $_POST['building_id'];
        $wo_status = $_POST['wo_status'];
        $new_wo_status = $_POST['new_wo_status'];

        $wo_notes = $db->updateWorkOrderStatus($woid, $role_id, $uid, $cust_id, $wo_status, $new_wo_status, $building_id);
       
        if ($wo_notes != FALSE) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['msg'] = "Status updated successfully" ;
            echo json_encode($response);

        } else {
            $response['status'] = 200;
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }
} else {
    $response['status'] = 400;
	$response['error'] = TRUE;
	$response['error_msg'] = "Required parameter is missing";
	echo json_encode($response);
}
?>