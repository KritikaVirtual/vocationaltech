<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['woid']) && $_POST['woid'] != "" || isset($_POST['vendor']) && $_POST['vendor'] != "") {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
    
        $uid = $_POST['uid'];
        $data = array();
        $data['job_cost'] = $_POST['job_cost'];
        $data['job_description'] = $_POST['job_description'];
        $data['markup'] = $_POST['markup'];
        $data['tax'] = $_POST['tax'];
         $data['vendor'] = $_POST['vendor'];
        $data['woid'] = $_POST['woid'];

        $wo_outside = $db->appSaveOutsideService($data, $uid);
       
        if ($wo_outside != FALSE) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['msg'] = $wo_outside;
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