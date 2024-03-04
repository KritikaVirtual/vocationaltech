<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['bsid']) && $_POST['bsid'] != "" ) {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
         $bsid = $_POST['bsid'];
        
        $services = $db->getBuildingServicesData($bsid);

        if ($services != FALSE) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['services_list'] = $services;
            echo json_encode($response);

        } else {
            $response['status'] = 400;
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