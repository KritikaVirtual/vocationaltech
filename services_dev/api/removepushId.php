<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['uid']) && $_POST['uid'] != "") {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
    
        $uid = $_POST['uid'];
		$pushId = $_POST['pushId'];
		$woupdate = $db->removepushId($uid, $pushId);
        echo "<pre>";
        print_r($woupdate);
       
        if ($woupdate != '1') {
            $response['status'] = 200;
            $response['error'] = FALSE;
            echo json_encode($response);

        } else {
            $response['status'] = 200;
            $response['error'] = TRUE;
            echo json_encode($response);
        }
} else {
    $response['status'] = 400;
	$response['error'] = TRUE;
	$response['error_msg'] = "Required parameter is missing";
	echo json_encode($response);
}
?>