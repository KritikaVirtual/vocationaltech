<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['uid']) && $_POST['uid'] != "" && isset($_POST['cust_id']) && $_POST['cust_id']) {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
        
        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];

        $buildings = $db->getBuildingsByUser($role_id, $uid, $cust_id);
        

        if ($buildings != FALSE) {
             $response['status'] = 200;
            $response['error'] = FALSE;
            $response['buildingList'] = $buildings;
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