<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['bId']) && $_POST['bId'] != "" ) {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
        $bid = $_POST['bId'];
         
        //$nottenant = 1; // this for not listing the tenant user here.
        $materialList = $db->getActiveMaterialByBId($bid);
        
        $default_tax = 0;
        $wpDetails = $db->getWoParameterByBidLabour($bid);
       if ($wpDetails) {
            $default_tax = $wpDetails['auto_charge'];
			if($wpDetails['override_markup'] == 1) {
				$default_markup = $wpDetails['dft_markup'];
			} else {
				$default_markup = '';
			}
			$default_closed = $wpDetails['status_closed'];
        }
     
       if ($materialList != FALSE || $wpDetails != FALSE) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['material_list'] = $materialList;
            $response['default_tax'] = $default_tax;
            $response['default_markup'] = $default_markup;
            $response['default_closed'] = $default_closed;
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