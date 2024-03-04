<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Work Order Status
 *
 * @author Brijesh
 */
class WorkstatusController extends Ve_Controller_Base {
   
   
    public function init()  {
       parent::init();         
       $this->wstModel = new Model_WoScheduleStatus();        
    }// close of init function
    
    public function changeAction(){
		$woId = $this->_getParam('woId',0);
		$sId = $this->_getParam('sId',0);
		$ckey = $this->_getParam('ckey',0);
		$statusChange = $this->_getParam('statusChange',0);
		$userId =  $this->_getParam('userId',0);
		$msg ='';
		$wSchedule = $this->wstModel->getWoSchedule($woId,$sId);	
		if(($wSchedule) &&  $ckey!='0'){
		  $wsData = $wSchedule[0];
		  // change current schedule and assign new schedule
		  if(($wsData['ckey']==$ckey) && ($wsData['current_status']=='1')){			  
			  try{
				   /******************* set timezone through building id ****************************/
				   $wpModel = new Model_WorkOrder();	
				   $wo_building_data=$wpModel->getWorkOrder($woId);
                                   $wpTimeZone = new Model_TimeZone();				   
				   $wpTimeZone->setTimezone($wo_building_data[0]['building']);
				   /******************* end - set timezone through building id ****************************/				   
				   // change current schedule
				   $chData = array();
				   $chData['current_status'] = 0;
				   $chData['ckey'] = '';
				   $chData['updated_at'] = date('Y-m-d H:i:s');
				   $changeSchedule = $this->wstModel->updateWoSchedule($chData,$wsData['wssId']);
                                 
				   
				   // insert next schedule
				   $schModel = new Model_Schedule();
				   $schData = $schModel->getScheduleData($sId);
                                   //print_r($schData);
                                   //die;
				   if($schData){
					   $wss_data = array();
					   $wss_data['worder_id']=	$woId;
					   $wss_data['schedule_id']= $schData[0]->id;
					   $wss_data['priority_id']= $schData[0]->priority_id;
					   $wss_data['status']= 1;
					   //$wss_data['ckey']= md5(time());
					   $wss_data['current_status']= 1;
					   $wss_data['created_at'] = date('Y-m-d H:i:s');
					   $ws_insert = $this->wstModel->insertWoSchedule($wss_data);
					   $wpModel = new Model_WorkOrderUpdate();
					   $wpCurrentData = $wpModel->getCurrentWoUpdate($woId);
					    
					    // reset work order update
					    $resetCurrent = $wpModel->updateWorkOrderByWoId(array('current_update'=>0),$woId);
					    $wpData = array();
					    $wpData['wo_id'] = $woId;
						//$wpData['wo_request'] = $wpCurrentData[0]['wo_request'];
						//$wpData['wo_status'] = $schData[0]->start_status;
                                                $wpData['wo_status'] = $schData[0]->end_status;
						$wpData['current_update']=1;
						$wpData['created_at'] = date('Y-m-d H:i:s');
						$insertWp = $wpModel->insertWorkOrderUpdate($wpData);
				   }
				   $msg = true;
				   
				   if($statusChange=='acknowledge') {
				   /*********History Log *********/
				    $whlModel = new Model_WoHistoryLog();		   
				    $whData= array();
				    $whData['woId']=$woId;
				    $whData['log_type']='status';
				    $whData['current_value']=1;
				    $whData['change_value']=2;
				    $whData['user_id']=$userId;
				    $whData['created_at'] = date('Y-m-d H:i:s');
				    $insertWHL = $whlModel->insertHistoryLog($whData);
					
					}
				   
				  $this->sendOnlyBadge($woId); 
				   
				   
			   }catch(Exception $e){
				   echo $e->getMessage();
			   }			   
		  }else{
			  $msg = false;
		  }	
		}else{
			$msg = false;
		}		
		$this->view->msg = $msg;
	} // close of change function
    
	public function sendOnlyBadge($woId) { 
		
		$userMapper = new Model_User();
		$woModel = new Model_WorkOrder();
		$bid = $woModel->getBuildingbyworkorder($woId);
		$userData = $userMapper->getAllAccountUserOfBuilding($bid[0]->building);
		$userList = array();
		$androidPushIdList = array();
		$iosPushIdList = array();
		$status = array('1');
		$badges = array();
		if($userData !='') {
		foreach($userData as $userDataVal) {
			$userList[] = $userDataVal->uid;
			// Fetch all new work order of user
			$res = $woModel->getWorkorderbystatus($userDataVal->uid, $status);
			$badges[$userDataVal->uid] = $res[0]->count_workorder;
			// End Fetch new work order
		}  
		$pushModel = new Model_PushNotification();
		$pushDetails = $pushModel->getPushId($userList);
		
		foreach($pushDetails as $pushVal) {
			
			// Start Android
			if($pushVal['device'] == 1) {
				$registrationIds = $pushVal['push_id'];
				$badgescount = $badges[$pushVal['user_id']];  
				//$pushModel->updatePushId(array('badges'=>$$badgescount), $registrationIds);
				 $msg = array
				(
					
					'sound'		=> 1,
					'largeIcon'	=> 'small_icon',
					'smallIcon'	=> 'small_icon',
					'badge' => $badgescount,
					
				);
				$fields = array
				(
					'registration_ids' 	=>array( $registrationIds),
					'data'			=> $msg,
				);
				 
				$headers = array
				(
					'Authorization: key=' . API_ACCESS_KEY,
					'Content-Type: application/json'
				);
				$ch = curl_init();
				curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
				curl_setopt( $ch,CURLOPT_POST, true );
				curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
				curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
				$result = curl_exec($ch );
				
				curl_close( $ch );
			}
			// End Android
			
			//Start IOS
			if($pushVal['device'] == 2) {
				
				$registrationIds = $pushVal['push_id'];
				$tToken[0] = $registrationIds;
				$badgescount = $badges[$pushVal['user_id']];
			
				
				$tHost = T_HOST;
				$tPort = 2195;
				$tCert = PEM_URL.'ck2.pem';
				$tPassphrase = '1choc3747*1'; 
				//$tAlert = 'Category- '.$userDetails['categoryName'] .', Building- '.$userDetails['buildingName'];
				$tBadge = intval($badgescount);
				
				$tPayload = 'APNS Message Handled by LiveCode'; $tBody =array();
				$tBody['aps'] = array ('badge' => $tBadge,);
				$tBody ['payload'] = $tPayload;
				$tBody = json_encode ($tBody);
				$tContext = stream_context_create ();
				stream_context_set_option ($tContext, 'ssl', 'local_cert', $tCert);
				stream_context_set_option ($tContext, 'ssl', 'passphrase', $tPassphrase);
				$tSocket = stream_socket_client ($tHost.':'.$tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $tContext);
				if (!$tSocket)
					exit();
					//exit ("APNS Connection Failed: $error $errstr" . PHP_EOL);
					
				
					$tMsg = chr (0) . chr (0) . chr (32) . pack ('H*', str_replace(' ', '',$tToken[0])) . pack ('n', strlen ($tBody)) . $tBody;
					$tResult = fwrite ($tSocket, $tMsg, strlen ($tMsg)); 
				
				//if ($tResult)
				//echo 'Delivered Message to APNS' . PHP_EOL;
				//else
				//	echo 'Could not Deliver Message to APNS' . PHP_EOL;
// Close the Connection to the Server.
				fclose ($tSocket);
			//die;
			}
			// END IOS
			
			}
		
		}
	 }
    
    public function reminderAction(){
		$currentSchedule = $this->wstModel->getCurrentWoSchedule();				
		if($currentSchedule){
			$lenModel = new Model_Length();
			$lenList = $lenModel->getLength();			
			$length = array();
			foreach($lenList as $ll){
				$length[$ll['lID']]=$ll['title'];
			}			
			$currTime = time();
			$woModel = new Model_WorkOrder();		
			
			foreach($currentSchedule as $curSch){
				
				if($curSch->wssId)
				/*********** get schedule data ***********/
				$schData = '';
				$schModel = new Model_Schedule();
				$schDetail = $schModel->getScheduleById($curSch->schedule_id);		    
				if($schDetail)
				$schData = $schDetail[0];				
				$lastTime ='';
				if($curSch->updated_at!='0000-00-00 00:00:00'){
					$lastTime =$curSch->updated_at;
				}else
				    $lastTime =$curSch->created_at;				    
				 $timeDiff = $currTime - strtotime($lastTime);		
				 $time = $curSch->Time;				
				 $reminderStatus = $this->wstModel->getReminderStatus($timeDiff,$time,$curSch->length);
				if($reminderStatus){
					try{
					   $chData = array();
					   $chData['reminder'] = $curSch->reminder+1;				   
					   $chData['updated_at'] = date('Y-m-d H:i:s');
					   $saveReminder = $this->wstModel->updateWoSchedule($chData,$curSch->wssId);
					   
					  //echo $notificationMail = $woModel->sendReminderNotification($curSch->worder_id,$curSch->schedule_id);
					  if($schData['start_status']!='1'){
						  echo 'remind'.$schData['start_status'];
						  echo $notificationMail = $woModel->sendReminderNotification($curSch->worder_id,$curSch->schedule_id);				
						}else{
							echo 'work order';
							$tenantMapper = new Model_Tenant();
							$tenantDetail     = $tenantMapper->getTenantByWoId($curSch->worder_id);			
							$tenantInfo = $tenantDetail[0];
							$tenantData       = (array)$tenantInfo;
							echo $notificationMail = $woModel->resendWorkOrderEmail($curSch->worder_id,$tenantData);
						}
				   }catch(Exception $e){
					   echo $e->getMessage();
				   }
				}
				echo '<br/>';    
			}
		}	
		exit;
	}
}// close class
