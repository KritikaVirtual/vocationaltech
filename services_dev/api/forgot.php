<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['email']) && $_POST['email'] != "") {
    require_once '../include/APIFunctions.php';
        $db = new APIFunctions();
        $response = array("error" => FALSE);
        $userDetail = $db->isUserExist($_POST['email']);  
        if(!empty($userDetail)) { 
                
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, "http://www.qaworkorder.com/sendemail");
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $userDetail);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $result = curl_exec($ch);
          curl_close($ch);
          
     	          $response['status'] = 200;
                  $response['email'] = $_POST['email'];
                  $response['error_msg'] = "Successfully sent mail to your email";
                  echo json_encode($response);
				
            } else {
                  $response['status'] = 400;
                  $response['error'] = TRUE;
                  $response['email'] = $_POST['email'];
                  $response['error_msg'] = "Invalid email address or user not exist !!!";
                  echo json_encode($response);
            }

    
} else {
    $response['status'] = 400;
	$response['error'] = TRUE;
	$response['error_msg'] = "Required parameter is missing";
	echo json_encode($response);
}


?>