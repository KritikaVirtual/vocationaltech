<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['bId']) && $_POST['bId'] != "" ) {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
         $bid = $_POST['bId'];
         //$woid = $_POST['woid'];
        
        $vendorList = $db->getVendorListByBid($bid);
       
        $wpDetails = $db->getWoParameterByBid($bid);
     
       if ($vendorList != FALSE) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['vendors'] = $vendorList;
            $response['default_markup'] = $wpDetails['dft_markup'];
            $response['default_tax'] = $wpDetails['auto_charge'];
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