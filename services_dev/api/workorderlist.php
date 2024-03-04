<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
    if (isset($_POST['filter']) && !empty($_POST['filter'])) {
            $filter = $_POST['filter'];
        } else {
            $filter = "1,2";
        } 
		$woid_array = '';
		$workorderid ='';
		$exsting_header = '';
        $role_id = $_POST['role_id'];
        $uid = $_POST['uid'];
		$max_wo_val ='';
		$wo_limit ='';
        $cust_id = $_POST['cust_id']; 
		if(isset($_POST['max_wo_val']) && !empty($_POST['max_wo_val'])) {
			$max_wo_val = $_POST['max_wo_val'];
			
		} else {
			$max_wo_val ='';
		}
		
		if(isset($_POST['wo_limit']) && !empty($_POST['wo_limit'])) {
			$wo_limit = $_POST['wo_limit'];
			
		} else {
			$wo_limit ='0';
		}
		if(isset($_POST['wo_offset']) && !empty($_POST['wo_offset'])) {
			$wo_offset = $_POST['wo_offset'];
			
		} else {
			$wo_offset ='15';
		}
		if(isset($_POST['exsting_header']) && !empty($_POST['exsting_header'])) {
			$exsting_header = $_POST['exsting_header'];
			
		} else {
			$exsting_header ='';
		}
		if(isset($_POST['woid_array'])) {
			$woid_array = $_POST['woid_array']; 
		}
		if(isset($_POST['onlyWorkorder'])) {
			$filter = ""; 
			if(isset($_POST['workorderid'])) {
				$workorderid = $_POST['workorderid']; 
			}
		}

        $work_order = $db->getWorkOrderNew($role_id, $uid, $cust_id, $filter, $woid_array,$workorderid, $wo_limit, $exsting_header, $max_wo_val,$wo_offset);
	
        if ($work_order != FALSE) {
            $response['error'] = FALSE;
            $response['status'] = 200;
            $response['wo_list'] = $work_order['work_rows_array'];
			$response['max_wo_val'] = $work_order['max_wo_val'];
			$response['headerexist'] = $work_order['headerexist'];
			$response['newcount'] = $work_order['newcount'];
			
            echo json_encode($response);

        } else {
            $response['error'] = TRUE;
            $response['status'] = 400;
            $response['error_msg'] = "No results found .";
            echo json_encode($response);
        }



?>