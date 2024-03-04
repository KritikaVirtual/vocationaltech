<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['build_id']) && $_POST['build_id'] != "" && isset($_POST['uid']) && $_POST['uid'] != "" && isset($_POST['cust_id']) && $_POST['cust_id']) {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
        
         $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
        $cust_id = $_POST['cust_id'];
        $build_id = $_POST['build_id'];

        $company = $db->getCompanyByUser($role_id, $uid, $cust_id, $build_id);

        if($company != FALSE) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['companyList'] = $company;
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