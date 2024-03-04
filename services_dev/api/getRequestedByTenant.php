<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['tnadmin_id']) && $_POST['tnadmin_id'] != "") {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
        
        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];
        $tnadmin_id = $_POST['tnadmin_id'];

        $company = $db->getTenants($role_id, $uid, $cust_id, $tnadmin_id);
       
        if($company != FALSE) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['requestedList'] = $company;
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