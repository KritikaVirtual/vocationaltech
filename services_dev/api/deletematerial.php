<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['woid']) && $_POST['woid'] != "" || isset($_POST['mcId']) && $_POST['mcId'] != "") {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
    
        $uid = $_POST['uid'];
        $data = array();
        $data['woid'] = $_POST['woid'];
        $data['mcId'] = $_POST['mcId'];
        
        $wo_material = $db->appDeleteMaterialService($data, $uid);
       
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