<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['bId']) && $_POST['bId'] != "" ) {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
         $bId = $_POST['bId'];
        
        $services = $db->getBuildingServicesPopUp($bId);

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