<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['cust_id']) && $_POST['cust_id'] != "" ) {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
    $cust_id = $_POST['cust_id'];
    
    $notes = $db->getPredefinedNotes($cust_id);

        if ($notes != FALSE) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['predefined_notes'] = $notes;
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