<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['bId']) && $_POST['bId'] != "" ) {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
        $bid = $_POST['bId'];
         
        //$nottenant = 1; // this for not listing the tenant user here.
        $users = $db->getUserByBuildingId($bid);
        
        $blList = $db->getActiveBillLaborByBId($bid);
       
        $brList = $db->getActiveBillRateByBId($bid);
       
        $job_time = '00:00';

        $wpDetails = $db->getWoParameterByBidLabour($bid);
       
        if ($wpDetails) {
            $time_charge = $wpDetails['time_min_charge'];
            $time_fact = explode(" ", $time_charge);
            $time_fact_measure = trim($time_fact[1]);
            $time_fact_minute = trim($time_fact[0]);
            if ($time_fact_measure == 'Minutes') {
                $job_time = '00:' . $time_fact[0];
                
            } 
            else {
                $job_time = $time_fact[0] . ':00';
                
            }
        }
     
       if ($users != FALSE) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['labour_list'] = $users;
            $response['labor_charge'] = $blList;
            $response['rate_charge'] = $brList;
            $response['job_time'] = $job_time;
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