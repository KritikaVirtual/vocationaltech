<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['woid']) && $_POST['woid'] != "" || isset($_POST['emp_id']) && $_POST['emp_id'] != "") {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
    
        $uid = $_POST['uid'];
        $data = array();
        $data['bl_id'] = $_POST['labor_charge'];
        $data['charge_hour'] = $_POST['charge_hour'];
        $data['emp_id'] = $_POST['emp_id'];
        $data['job_time'] = $_POST['job_time'];
        $data['rate_charge'] = $_POST['rate_charge'];
        $data['woid'] = $_POST['woid'];

        $wo_material = $db->appSaveLabourService($data, $uid);
       
        if ($wo_material != FALSE) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['msg'] = $wo_material;
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