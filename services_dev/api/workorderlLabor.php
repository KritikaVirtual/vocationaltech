<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['woid']) && $_POST['woid'] != "" ) {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
         $woid = $_POST['woid'];
        
        $wo_labor = $db->getLabor($woid);

        if ($wo_labor != FALSE) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['wo_labor'] = stripslashes($wo_labor['html']);
            $response['total_labor_charge'] = $wo_labor['total_labor_charge'];
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