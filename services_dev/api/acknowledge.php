<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['woId']) && !empty($_POST['woId']) && isset($_POST['sId']) && !empty($_POST['sId']) && isset($_POST['ckey']) && !empty($_POST['ckey'])) {
   require_once '../include/APIFunctions.php';
$db = new APIFunctions();
        $woId = $_POST['woId']; 
		$sId = $_POST['sId'];
		$ckey = $_POST['ckey'];
		$statusChange = $_POST['statusChange']; 
		$userId =  $_POST['uid']; 
		
        $msg ='';
        $wSchedule = $db->getWoSchedule($woId,$sId);
        //print_r($wSchedule); 
          if(($wSchedule) &&  $ckey!='0'){
            
		   // change current schedule and assign new schedule
		  if(($wSchedule['ckey']==$ckey) && ($wSchedule['current_status']=='1')){			  
			 
				   /******************* set timezone through building id ****************************/
				   
				   $wo_building_data = $db->getAckWorkOrder($woId);
				   //$wpTimeZone = new Model_TimeZone();				   
				   //$wpTimeZone->setTimezone($wo_building_data['building']);
				   /******************* end - set timezone through building id ****************************/
				   
				   // change current schedule
				   
				   //$chData = array();
				   $current_status = 0;
				   $ckey = '';
				   $updated_at = date('Y-m-d H:i:s');
				   //$changeSchedule = $db->updateWoSchedule($current_status, $ckey, $updated_at, $wSchedule['wssId']);
				   $wo_id = $woId;
				   $wo_status = 2;
				   $created_at = date('Y-m-d H:i:s');
				   $userId = $userId;
				   $insertWp = $db->insertWorkOrderUpdateAck($wo_id, $wo_status, $created_at, $userId);
				   
				   // insert next schedule
				  // $schModel = new Model_Schedule();
				   $schData = $db->getNextSchedule($sId);
			        //print_r($schData);
		
				   if($schData){
					   $worder_id=	$woId;
					   $schedule_id= $schData['id'];
					   $priority_id= $schData['priority_id'];
					   $status= 1;
					   $current_status= 1;
					   $created_at = date('Y-m-d H:i:s');
					   $ws_insert = $db->insertWoSchedule($worder_id, $schedule_id, $priority_id, $status, $current_status, $created_at);
					   
					   //$wpModel = new Model_WorkOrderUpdate();
					    
					  
					    $wpCurrentData = $db->getCurrentWoUpdate($woId);
					    //print_r($wpCurrentData);
					    $upId = $wpCurrentData['upId'];
					    $woId = $wpCurrentData['wo_id'];
					    $woRequest = $wpCurrentData['wo_request'];
					    $internalNote = $wpCurrentData['internal_note'];
					    $woStatus = $wpCurrentData['wo_status'];
					    $billableOpt = $wpCurrentData['billable_opt'];
					    $createdAt = $wpCurrentData['created_at'];
					    $currentUpdate = 0;
					    $updated_at = date('Y-m-d H:i:s');
					    $userId = $wpCurrentData['user_id'];
					   
					    // reset work order update
					    $resetCurrent = $db->updateWorkOrderByWoId($woId, $woRequest, $internalNote, $woStatus, $billableOpt, $createdAt, $currentUpdate, $updated_at, $userId, $upId);
					   
					    $wo_id = $woId;
						$wo_status = $schData['start_status'];
						$current_update=1;
						$created_at = date('Y-m-d H:i:s');
						$insertWp = $db->insertWorkOrderUpdate($wo_id, $wo_status, $current_update, $created_at);
					    
				   }
				   $msg = true;
				   
				   if($statusChange=='acknowledge') {
				   /*********History Log *********/
				    $woId = $woId;
				    $log_type = 'status';
				    $current_value = 1;
				    $change_value = 2;
				    $user_id = $userId;
					$created_at = date('Y-m-d H:i:s');
				    $insertWHL = $db->insertHistoryLog($woId, $log_type, $current_value,  $change_value, $user_id, $created_at);
					
					}
						   
		    }else{
			 $msg = false;
		  }	
		}else{
			$msg = false;
		}
    $response['status'] = 200;
	$response['error_msg'] = $msg;
	echo json_encode($response);
            
} else {
    $response['status'] = 400;
	$response['error'] = TRUE;
	$response['error_msg'] = "Required parameter is missing";
	echo json_encode($response);
}



?>