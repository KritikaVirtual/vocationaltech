<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['notedate']) && $_POST['notedate'] != "" || isset($_POST['notetext']) && $_POST['notetext'] != "") {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
    
        $uid = $_POST['uid'];
        $data = array();
        $data['note_date'] = $_POST['notedate'];
        $data['internal'] = $_POST['noteselect'];
        $data['note'] = $_POST['notetext'];
        $data['woId'] = $_POST['woId'];
        $data['notify_tenant'] = $_POST['notify_tenant'];
        $data['notify_account_user'] = $_POST['notify_account_user'];
        $data['role_id'] = $_POST['role_id'];

        $wo_material = $db->appSaveNoteServicesPoUp($data, $uid);
       
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