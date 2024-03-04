<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['wfId']) && $_POST['wfId'] != "") {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
        
        $wfId = $_POST['wfId'];

         $deleteattachment = $db->deleteAttachment($wfId);

        if ($deleteattachment != FALSE) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['error_msg'] = "Attachment deleted sucessfully.";
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