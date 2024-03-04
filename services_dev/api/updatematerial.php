<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['woid']) && $_POST['woid'] != "" || isset($_POST['material_id']) && $_POST['material_id'] != "") {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
    
        $uid = $_POST['uid'];
        $data = array();
        $data['cost'] = $_POST['cost'];
        $data['markup'] = $_POST['markup'];
        $data['material_id'] = $_POST['material_id'];
        $data['quantity'] = $_POST['quantity'];
        $data['tax'] = $_POST['tax'];
        $data['woid'] = $_POST['woid'];
        $data['mcId'] = $_POST['mcId'];
        
        $wo_material = $db->appSaveMaterialService($data, $uid);
       
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