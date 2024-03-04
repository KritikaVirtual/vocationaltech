<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['username']) && $_POST['username'] != "" && isset($_POST['password']) && $_POST['password'] != "" ) {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
    $response = array("error" => FALSE);
    $username = $_POST['username'];
    $password = $_POST['password'];
   
	
    $user = $db->getLogin($username, $password);

    if ($user != '') {
        if($_POST['fcm_token'] != "" && $_POST['device_type'] != "") {
        $uid = $user['uid'];
        $pushId = $_POST['fcm_token'];
	    $device = $_POST['device_type'];
        $woupdate = $db->checkPushId($uid, $pushId, $device );
        } 
        $response['status'] = 200;
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
        $response['status'] = 400;
        $response['error'] = TRUE;
        $response['error_msg'] = "incorrect user name or password";
        echo json_encode($response);
    }
} else {
    $response['status'] = 400;
	$response['error'] = TRUE;
	$response['error_msg'] = "Required parameter is missing";
	echo json_encode($response);
}



?>