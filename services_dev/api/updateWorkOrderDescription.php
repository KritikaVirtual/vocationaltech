<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['woid']) && $_POST['woid'] != "" && isset($_POST['description']) && $_POST['description'] != "") {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
        
        $woid = $_POST['woid'];
        $description = $_POST['description'];
        $uid = $_POST['uid'];
        
        $wo_description = $db->appUpdateDescription($woid, $description, $uid);
       
        if ($wo_description != '') {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['msg'] = $wo_description ;
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