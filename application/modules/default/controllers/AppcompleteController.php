<?php
require_once 'CompleteController.php';
class AppcompleteController extends CompleteController {

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
        $this->accessHelper = $this->_helper->access;
        $this->closewo_location = 4;
    }
   
    // Call befor any action and check is user login or not
    public function preDispatch() {
       
        $tag = $_POST['tag'];
        switch ($tag) {
        case 'AppUpdateWorkOrderDescription':
            header('Access-Control-Allow-Origin: *');
            $woId = $this->_getParam('woId');
            $description = $this->_getParam('description');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['woId'] = $woId;
            $data['description'] = $description;
            $this->appUpdateDescription($data, $user_id);
            break;
        case 'save_building_charge':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['woId'] = $this->_getParam('woId');
            $data['service'] = $this->_getParam('service');
            $data['charge'] = $this->_getParam('charge');
            $data['amount_requested'] = $this->_getParam('amount_requested');
            $data['comment'] = $this->_getParam('comment');
            //$data['bsId'] = $this->_getParam('bsId');
            //print_r($data); exit;
            $this->appSaveBuildingServicesPoUp($data, $user_id);
            break;
        case 'update_building_charge':
            header('Access-Control-Allow-Origin: *');

            $user_id = $this->_getParam('uid');
            $data = array();
            $data['woId'] = $this->_getParam('woId');
            $data['service'] = $this->_getParam('service');
            $data['charge'] = $this->_getParam('charge');
            $data['amount_requested'] = $this->_getParam('amount_requested');
            $data['comment'] = $this->_getParam('comment');
            $data['bsId'] = $this->_getParam('bsId');
            //print_r($data); exit;
            $this->appSaveBuildingServicesPoUp($data, $user_id);
            break;
        case 'delete_building_charge':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['bsId'] = $this->_getParam('bsId');
            $data['woId'] = $this->_getParam('woId');
            $this->appDeleteBuildingServicesPoUp($data, $user_id);
            break;
        // for  note tab
        case 'save_notes_data':
            header('Access-Control-Allow-Origin: *');
            $user_id = $_POST['uid'];
            $data = array();
            $data['note_date'] = $_POST['notedate'];
            $data['internal'] = $_POST['noteselect'];
            $data['note'] = $_POST['notetext'];
            $data['woId'] = $_POST['woId'];
			$data['notify_tenant'] = $_POST['notify_tenant'];
			$data['notify_account_user'] = $_POST['notify_account_user']; 
			$data['role_id'] = $_POST['role_id']; 
            $this->appSaveNoteServicesPoUp($data, $user_id);
            break;
        case 'update_notes_data':
            header('Access-Control-Allow-Origin: *');
            $user_id = $_POST['uid'];
            $data = array();
            $data['note_date'] = $_POST['notedate'];
            $data['internal'] = $_POST['noteselect'];
            $data['note'] = $_POST['notetext'];
            $data['woId'] = $_POST['woId'];
            $data['wnId'] = $_POST['noteId'];
            //print_r($_REQUEST);
            $this->appSaveNoteServicesPoUp($data, $user_id);
            break;
        case 'delete_notes_data':
            header('Access-Control-Allow-Origin: *');
            $user_id =  $_POST['uid'];
            $data = array();
            $data['wnId'] = $_POST['noteId'];
            $data['woId'] =  $_POST['woId'];
            $this->appDeleteNoteService($data, $user_id);
            break;
        // for outside service tab
        case 'getoutside_service_vendor':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['bId'] = $this->_getParam('bId');
            $data['woId'] = $this->_getParam('woId');
            $this->appGetOutsideServiceVendorList($data);
            break;
        case 'saveoutside':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['job_cost'] = $this->_getParam('job_cost');
            $data['job_description'] = $this->_getParam('job_description');
            $data['markup'] = $this->_getParam('markup');
            $data['tax'] = $this->_getParam('tax');
            $data['vendor'] = $this->_getParam('vendor');
            $data['woId'] = $this->_getParam('woId');
            $this->appSaveOutsideService($data, $user_id);
            break;
        case 'updateoutside':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['job_cost'] = $this->_getParam('job_cost');
            $data['job_description'] = $this->_getParam('job_description');
            $data['markup'] = $this->_getParam('markup');
            $data['tax'] = $this->_getParam('tax');
            $data['vendor'] = $this->_getParam('vendor');
            $data['woId'] = $this->_getParam('woId');
            $data['osId'] = $this->_getParam('osId');
            $this->appSaveOutsideService($data, $user_id);
            break;
        case 'deleteoutside':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['osId'] = $this->_getParam('osId');
            $data['woId'] = $this->_getParam('woId');
            $this->appDeleteoutsideService($data, $user_id);
            break;
        // for labour tab
        case 'get_labour_byBuildingId':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['bId'] = $this->_getParam('bId');
            //$data['woId'] = $this->_getParam('woId');
            $this->appGetLabourTabData($data);
            break;
        case 'savelabor':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['bl_id'] = $this->_getParam('labor_charge');
            $data['charge_hour'] = $this->_getParam('charge_hour');
            $data['emp_id'] = $this->_getParam('emp_id');
            $data['job_time'] = $this->_getParam('job_time');
            $data['rate_charge'] = $this->_getParam('rate_charge');
            $data['woId'] = $this->_getParam('woId');
            $this->appSaveLabourService($data, $user_id);
            break;
        case 'updatelabour':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['bl_id'] = $this->_getParam('labor_charge');
            $data['charge_hour'] = $this->_getParam('charge_hour');
            $data['emp_id'] = $this->_getParam('emp_id');
            $data['job_time'] = $this->_getParam('job_time');
            $data['rate_charge'] = $this->_getParam('rate_charge');
            $data['woId'] = $this->_getParam('woId');
            $data['lid'] = $this->_getParam('lid');
            $this->appSaveLabourService($data, $user_id);
            break;
        case 'deletelabour':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['lid'] = $this->_getParam('lid');
            $data['woId'] = $this->_getParam('woId');
            $this->appDeleteLabourService($data, $user_id);
            break;
        // for material tab
        case 'get_material_byBuildingId':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['bId'] = $this->_getParam('bId');
            //$data['woId'] = $this->_getParam('woId');
            $this->appGetMaterialTabData($data);
            break;
        case 'savematerial':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['cost'] = $this->_getParam('cost');
            $data['markup'] = $this->_getParam('markup');
            $data['material_id'] = $this->_getParam('material_id');
            $data['quantity'] = $this->_getParam('quantity');
            $data['tax'] = $this->_getParam('tax');
            $data['woId'] = $this->_getParam('woId');
            $this->appSaveMaterialService($data, $user_id);
            break;
        case 'updatematerial':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['cost'] = $this->_getParam('cost');
            $data['markup'] = $this->_getParam('markup');
            $data['material_id'] = $this->_getParam('material_id');
            $data['quantity'] = $this->_getParam('quantity');
            $data['tax'] = $this->_getParam('tax');
            $data['woId'] = $this->_getParam('woId');
            $data['mcId'] = $this->_getParam('mcId');
            $this->appSaveMaterialService($data, $user_id);
            break;
        case 'deletematerial':
            header('Access-Control-Allow-Origin: *');
            $user_id = $this->_getParam('uid');
            $data = array();
            $data['mcId'] = $this->_getParam('mcId');
            $data['woId'] = $this->_getParam('woId');
            $this->appDeleteMaterialService($data, $user_id);
            break;
		case 'get_Date_And_Time':
            header('Access-Control-Allow-Origin: *');
            $build_id = $this->_getParam('build_id');
            $this->getDateAndTimeByID($build_id);
            break;
		case 'sendMail_createNewWorkOrder':
            header('Access-Control-Allow-Origin: *');
            $data = $this->getRequest()->getPost();
            $this->appSendEmailorder($data);
            break;
		case 'acknowledge':
            header('Access-Control-Allow-Origin: *'); 
           
            $data['woId'] = $this->_getParam('woId',0);
			$data['sId'] = $this->_getParam('sId',0);
			$data['ckey'] = $this->_getParam('ckey',0);
			$data['statusChange']= $this->_getParam('statusChange',0);
			$data['userId']=  $this->_getParam('userId',0);
			//$this->sendOnlyBadge($data['woId']);
            $this->changeAction($data);  
			
            break;
		case 'forgotpass':
		header('Access-Control-Allow-Origin: *');
		$data['email'] = $this->_getParam('username',0);  
		$this->forgetpassword($data); 
		break;
        default:
            if (!Zend_Auth::getInstance()->hasIdentity()) {
                $this->_redirect('/index');
            }

            $level = (Zend_Auth::getInstance()->getStorage()->read()) ? Zend_Auth::getInstance()->getStorage()->read()->role_id : '';
            $this->userId = Zend_Auth::getInstance()->getStorage()->read()->uid;
            $this->roleId = Zend_Auth::getInstance()->getStorage()->read()->role_id;
            $this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
            break;
        }

    }
	
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
					'badge' => $badgescount-1,
					
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
				$tHost =  T_HOST;
				$tPort = 2195;
				$tCert = PEM_URL.'ck2.pem';
				$tPassphrase = '1choc3747*1'; 
				//$tAlert = 'Category- '.$userDetails['categoryName'] .', Building- '.$userDetails['buildingName'];
				$tBadge = intval($badgescount-1);
				
				$tPayload = 'APNS Message Handled by LiveCode'; $tBody = array();
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
				
				if ($tResult)
				echo 'Delivered Message to APNS' . PHP_EOL;
				else
					echo 'Could not Deliver Message to APNS' . PHP_EOL;
// Close the Connection to the Server.
				fclose ($tSocket);
			//die;
			}
			// END IOS
			
			}
		
		}
	 }
	
	public function forgetpassword($data) {
		
		
        $returndata = '';
        $forgetDetail = array(
            "email"     => "",
            "message"   => ""
        ); 
        $message = "";
        if($data !=''){ 
            $userModel = new Model_User(); 
            $userDetail = $userModel->isUserExist($data['email']);  
            if(!empty($userDetail)) {  
                $this->sendResetInstruction($userDetail);
				 $forgetDetail = array(
                    "email"     => $data['email'],
                    "message"   => "Successfully sent mail to your email.",
                );
				if($forgetDetail['message'] != "") 
				{
					$returndata = $forgetDetail;
	
				} 
            } else {
                 $forgetDetail = array(
                    "email"     => $data['email'],
                    "message"   => "Invalid email address or user not exist !!!",
                );
				if($forgetDetail['message'] != "") 
				{
					$returndata = $forgetDetail;
				} 
            }    
			//echo "<pre>";
			//print_r($forgetDetail);
			//exit;
				
        }
		echo json_encode($returndata);
		die;
    }
	
	
	
	public function changeAction($data){ 
		$woId = $data['woId']; 
		$sId = $data['sId'];
		$ckey = $data['ckey'];
		$statusChange = $data['statusChange']; 
		$userId =  $data['userId']; 
		$msg ='';
		$wsModel = new Model_WoScheduleStatus();
		$wSchedule = $wsModel->getWoSchedule($woId,$sId);	
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
				   $changeSchedule = $wsModel->updateWoSchedule($chData,$wsData['wssId']);
				   
				   // insert next schedule
				   $schModel = new Model_Schedule();
				  echo $schData = $schModel->getNextSchedule($sId); die;
				   if($schData){
					   $wss_data = array();
					   $wss_data['worder_id']=	$woId;
					   $wss_data['schedule_id']= $schData[0]->id;
					   $wss_data['priority_id']= $schData[0]->priority_id;
					   $wss_data['status']= 1;
					   //$wss_data['ckey']= md5(time());
					   $wss_data['current_status']= 1;
					   $wss_data['created_at'] = date('Y-m-d H:i:s');
					   $ws_insert = $wsModel->insertWoSchedule($wss_data);
					   
					   $wpModel = new Model_WorkOrderUpdate();
					    
					   $wpCurrentData = $wpModel->getCurrentWoUpdate($woId);
					    
					    // reset work order update
					    $resetCurrent = $wpModel->updateWorkOrderByWoId(array('current_update'=>0),$woId);
					    $wpData = array();
					    $wpData['wo_id'] = $woId;
						//$wpData['wo_request'] = $wpCurrentData[0]['wo_request'];
						$wpData['wo_status'] = $schData[0]->start_status;
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
		exit(0);
	}

    public function appUpdateDescription($data, $user_id) {

        if ($data['description'] == '') {
            echo 'error';
            exit(0);
        }
        //print_r($data);
        try {
			$woModel = new Model_WorkOrder();
			$workOrder = $woModel->getWorkOrder($data['woId']);
			$this->setTimezone($workOrder[0]['building']);
            $data['description'] = addslashes($data['description']);
            $wdModel = new Model_WorkDescription();
            $wpDetails = $wdModel->getDescByWoId($data['woId']);

            $id = ($wpDetails) ? $wpDetails[0]['id'] : '0';

            $whlModel = new Model_WoHistoryLog();
            $current_details_value = $whlModel->getWoHistoryLogByLog($data['woId'], 'Description of Work', $user_id);
            $whData = array();
            $whData['woId'] = $data['woId'];
            $whData['log_type'] = 'Description of Work';
            if ($id != '0') {
                $whData['current_value'] = ($current_details_value[0]['change_value'] != Null) ? $current_details_value[0]['change_value'] : '';
            } else {
                $whData['current_value'] = '';
            }

            $whData['change_value'] = json_encode($data);
            $whData['user_id'] = $user_id;
			$whData['created_at'] = date('Y-m-d H:i:s');

            $insertWHL = $whlModel->insertHistoryLog($whData);

            if ($id != '0') {
                $updateData = $wdModel->updateDescription($data, $id);
            } else {
                $insertData = $wdModel->insertDescription($data);
            }
            echo 'success';
        } catch (Exception $e) {
            echo 'error';
        }
        exit(0);
    }

    public function appSaveBuildingServicesPoUp($data, $user_id) {

        $message = array();
        $current_details_value = '';
        if ($data['service'] == '' || $data['charge'] == '' || $data['amount_requested'] == '') {
            $message['status'] = 'error';
            $message['msg'] = 'Fill the Form Properly';
        } else {
            try {
				$woModel = new Model_WorkOrder();
				$workOrder = $woModel->getWorkOrder($data['woId']);
				$this->setTimezone($workOrder[0]['building']);
                $data['comment'] = addslashes($data['comment']);
                $bserviceModel = new Model_BuildingService();
                $whlModel = new Model_WoHistoryLog();
                if (isset($data['bsId']) && $data['bsId'] != '') {
                    $current_details_value = $whlModel->getWoHistoryLogByBuilding($data['bsId']);
                    $current_details_value = json_encode($current_details_value[0]);
                    $updateBuildingService = $bserviceModel->updateBuildingService($data, $data['bsId']);
                } else {
                    $insertData = $bserviceModel->insertBuildingService($data);
                }

                $whData = array();

                $whData['woId'] = $data['woId'];
                $whData['log_type'] = 'building services';
                if (isset($data['bsId']) && $data['bsId'] != '') {
                    $whData['current_value'] = $current_details_value;
                } else {
                    $whData['current_value'] = '';
                }
                if (isset($insertData)) {$data['bsId'] = $insertData;}
                $whData['change_value'] = json_encode($data);
                $whData['user_id'] = $user_id;
				$whData['created_at'] = date('Y-m-d H:i:s');
                $insertWHL = $whlModel->insertHistoryLog($whData);

                $message['status'] = 'success';
                $message['msg'] = 'Building Service Charge has been saved successfully.';
            } catch (Exception $e) {
                $message['status'] = 'error';
                $message['msg'] = 'Error Occurred during the save building service charge';
            }
        }
        echo json_encode($message);
        exit(0);
    }

    public function appDeleteBuildingServicesPoUp($data, $user_id) {
        $message = array();
        if ($data['bsId'] != '') {
            try {

                $whlModel = new Model_WoHistoryLog();
                $whData = array();
                $current_details_value = $whlModel->getWoHistoryLogByBuilding($data['bsId']);
                $whData['woId'] = $data['woId'];
                $whData['log_type'] = 'building services';
                $whData['current_value'] = json_encode($current_details_value[0]);
                $whData['change_value'] = '';
                $whData['user_id'] = $user_id;
                $insertWHL = $whlModel->insertHistoryLog($whData);
                $bserviceModel = new Model_BuildingService();
                $deleteBuildingService = $bserviceModel->deleteBuildingService($data['bsId']);
                $message['status'] = 'success';
            } catch (Exception $e) {
                $message['status'] = 'success';
            }

        }
        echo json_encode($message);
        exit(0);
    }
    /************* Added by khush for work Note ***************/
    public function appSaveNoteServicesPoUp($data, $user_id) {
        $message = array();
        $current_details_value = '';
        if ($data['note_date'] == '' || $data['note'] == '') {
            $message['status'] = 400;
            $message['error'] = TRUE;
            $message['msg'] = 'Fill the Form Properly';
        } else {
            try {
				if(isset($data['notify_tenant'])) {
					$notify_tenant = $data['notify_tenant'];
					 unset($data['notify_tenant']);
				 }
				 if(isset($data['notify_account_user'])) {
					$notify_account_user = $data['notify_account_user'];
					 unset($data['notify_account_user']);
				 }
				$role_id = $data['role_id'];
				unset($data['role_id']);
				$woModel = new Model_WorkOrder();
				$workOrder = $woModel->getWorkOrder($data['woId']);
				$this->setTimezone($workOrder[0]['building']);
                $wnModel = new Model_WoNote();
                $whlModel = new Model_WoHistoryLog();
                $data['note_date'] = date('Y-m-d', strtotime($data['note_date']));
                if (isset($data['wnId']) && $data['wnId'] != '') {
                    $current_details_value = $whlModel->getWoHistoryLogByNotes($data['wnId']);
                    $current_details_value = json_encode($current_details_value[0]);
                    $updateNote = $wnModel->updateWoNote($data, $data['wnId']);
                } else {
					$data['user_id'] = $user_id;
                    $insertData = $wnModel->insertWoNote($data);
					$tenantMapper = new Model_Tenant();
					 $tenantDetail = $tenantMapper->getTenantById($workOrder[0]['tenant']);
					 $tenantInfo = $tenantDetail[0];;
					 $tenantData = (array)$tenantInfo; 
					 $tenantData['tenantId'] = $tenantData ['id'];
					 $tenantData['note_created_user'] = $workOrder[0]['create_user'];
					 if(isset($insertData) && $insertData != 0) {
						$tenantData['note_insert_id'] = $insertData;
						$tenantData['login_roleId'] = $role_id;
						$tenantData['login_userId'] = $user_id;
					 }
					 if($notify_tenant == 1) { 
						$sendmail = $woModel->sendEmailToTenant($data['woId'],$tenantData);
					 }
					 if($notify_account_user == 1) {
						$sendmail = $woModel->sendEmailToAccountUsers($data['woId'],$tenantData);
					 }
                }
                $whData = array();

                $whData['woId'] = $data['woId'];
                $whData['log_type'] = 'notes';
                if (isset($data['wnId']) && $data['wnId'] != '') {
                    $whData['current_value'] = $current_details_value;
                } else {
                    $whData['current_value'] = '';
                }
                if (isset($insertData)) {$data['wnId'] = $insertData;}
                $whData['change_value'] = json_encode($data);
                $whData['user_id'] = $user_id;
				$whData['created_at'] = date('Y-m-d H:i:s');
                $insertWHL = $whlModel->insertHistoryLog($whData);

                $message['status'] = 200;
                $message['error'] = FALSE;
                $message['msg'] = 'Note has been saved successfully.';
            } catch (Exception $e) {
                $message['status'] = 400;
                $message['error'] = TRUE;
                $message['msg'] = 'Error Occurred during the save note';
            }

        }
        echo json_encode($message);
        exit(0);
    } /* close save outside service data*/
    /* close delete from app note */
    public function appDeleteNoteService($data, $userId) {

        if ($data['wnId'] != '') {
            try {

                $whlModel = new Model_WoHistoryLog();
                $whData = array();
                $current_details_value = $whlModel->getWoHistoryLogByNotes($data['wnId']);
                $whData['woId'] = $data['woId'];
                $whData['log_type'] = 'notes';
                $whData['current_value'] = json_encode($current_details_value[0]);
                $whData['change_value'] = '';
                $whData['user_id'] = $userId;
                $insertWHL = $whlModel->insertHistoryLog($whData);

                $wnModel = new Model_WoNote();
                $deleteNote = $wnModel->deleteWoNote($data['wnId']);
                $message['status'] = 200;
                $message['error'] = FALSE;
                $message['msg'] = 'Note has been delete successfully.';
            } catch (Exception $e) {
                $message['status'] = 400;
                $message['error'] = TRUE;
                $message['msg'] = 'Error Occurred during the delete note';
            }

        }
        echo json_encode($message);
        exit(0);
    } /* close delete note */
    public function indexAction() {
        echo "hi..";exit(0);
    }
    /*************EBD Added by khush for work Note ***************/
    public function appGetOutsideServiceVendorList($data) {
        $vendorModel = new Model_Vendor();
        $vendorList = $vendorModel->getVendorListByBid($data['bId']);
        $default_markup = 0;
        $default_tax = 0;
        $wpModel = new Model_WoParameter();
        $wpDetails = $wpModel->getWoParameterByBid($data['bId']);
        if ($wpDetails) {
            $default_markup = $wpDetails[0]['dft_markup'];
            $default_tax = $wpDetails[0]['auto_charge'];
        }
        $dataout = '';
        $container = '';
        if ($vendorList) {
            foreach ($vendorList as $vl) {
                $dataout['vid'] = $vl['vid'];
                $dataout['company_name'] = stripslashes($vl['company_name']);
                $container['vendors'][] = $dataout;
            }
        } else {
            $container['vendors'] = null;
        }
        $container['default_markup'] = $default_markup;
		$container['default_tax'] = $default_tax;
        $container['error'] = false;

        print json_encode($container);
        exit(0);
    }
    public function appSaveOutsideService($data, $user_id) {
        $message = array();
        $current_details_value = '';
        if ($data['vendor'] == '' || $data['job_cost'] == '' || $data['job_cost'] == '0' || $data['markup'] == '') {
            $message['status'] = 'error';
            $message['msg'] = 'Fill the Form Properly';
        } else {
            try {
				$woModel = new Model_WorkOrder();
				$workOrder = $woModel->getWorkOrder($data['woId']);
				$this->setTimezone($workOrder[0]['building']);
                $osModel = new Model_OutsideService();
                $whlModel = new Model_WoHistoryLog();
                if (isset($data['osId']) && $data['osId'] != '') {
                    $current_details_value = $whlModel->getWoHistoryLogByOutside($data['osId']);
                    $current_details_value = json_encode($current_details_value[0]);
                    $updateOutSide = $osModel->updateOutsideService($data, $data['osId']);
                } else {
                    $insertData = $osModel->insertOutsideService($data);
                }

                $whData = array();

                $whData['woId'] = $data['woId'];
                $whData['log_type'] = 'outside service';
                if (isset($data['osId']) && $data['osId'] != '') {
                    $whData['current_value'] = $current_details_value;
                } else {
                    $whData['current_value'] = '';
                }
                if (isset($insertData)) {$data['osId'] = $insertData;}
                $whData['change_value'] = json_encode($data);
                $whData['user_id'] = $user_id;
				$whData['created_at'] = date('Y-m-d H:i:s');
                $insertWHL = $whlModel->insertHistoryLog($whData);

                $message['status'] = 'success';
                $message['msg'] = 'Outside service Charge has been saved successfully.';
            } catch (Exception $e) {
                $message['status'] = 'error';
                $message['msg'] = 'Error Occurred during the save outside service charge';
            }
        }

        echo json_encode($message);
        exit(0);
    } /* close save outside service data*/

    public function appDeleteoutsideService($data, $user_id) {
        if ($data['osId'] != '') {
            try {

                $whlModel = new Model_WoHistoryLog();
                $whData = array();
                $current_details_value = $whlModel->getWoHistoryLogByOutside($data['osId']);
                $whData['woId'] = $data['woId'];
                $whData['log_type'] = 'outside service';
                $whData['current_value'] = json_encode($current_details_value[0]);
                $whData['change_value'] = '';
                $whData['user_id'] = $user_id;
                $insertWHL = $whlModel->insertHistoryLog($whData);
                $osModel = new Model_OutsideService();
                $deleteOutside = $osModel->deleteOutsideService($data['osId']);
                echo 'true';
            } catch (Exception $e) {
                echo 'false';
            }
        }
        exit(0);
    } /* close delete outside service */

    public function deletelaborAction() {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $data = $this->_request->getPost();
            if ($data['lid'] != '') {
                try {
                    $whlModel = new Model_WoHistoryLog();
                    $whData = array();
                    $current_details_value = $whlModel->getWoHistoryLogByLabor($data['lid']);
                    $whData['woId'] = $data['woId'];
                    $whData['log_type'] = 'Labor';
                    $whData['current_value'] = json_encode($current_details_value[0]);
                    $whData['change_value'] = '';
                    $whData['user_id'] = $this->userId;
                    $insertWHL = $whlModel->insertHistoryLog($whData);
                    $laborModel = new Model_Labor();
                    $deleteLabor = $laborModel->deleteLabor($data['lid']);
                    echo 'true';
                } catch (Exception $e) {
                    echo 'false';
                }

            }
        }
        exit(0);
    } // close delete labor charge

    public function savelaborAction() {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $data = $this->_request->getPost();
            $message = array();
            $current_details_value = '';
            if ($data['emp_id'] == '' || $data['charge_hour'] == '' || $data['rate_charge'] == '' || $data['job_time'] == '') {
                $message['status'] = 'error';
                $message['msg'] = 'Fill the Form Properly';
            } else {
                try {
					$woModel = new Model_WorkOrder();
					$workOrder = $woModel->getWorkOrder($data['woId']);
					$this->setTimezone($workOrder[0]['building']);
                    $laborModel = new Model_Labor();
                    //print_r($data);
                    $whlModel = new Model_WoHistoryLog();
                    if (isset($data['lid']) && $data['lid'] != '') {
                        $current_details_value = $whlModel->getWoHistoryLogByLabor($data['lid']);
                        $current_details_value = json_encode($current_details_value[0]);
                        $updateLabor = $laborModel->updateLabor($data, $data['lid']);
                    } else {
                        $insertData = $laborModel->insertLabor($data);
                    }

                    $whData = array();

                    $whData['woId'] = $data['woId'];
                    $whData['log_type'] = 'Labor';
                    if (isset($data['lid']) && $data['lid'] != '') {
                        $whData['current_value'] = $current_details_value;
                    } else {
                        $whData['current_value'] = '';
                    }
                    if (isset($insertData)) {$data['lid'] = $insertData;}
                    $whData['change_value'] = json_encode($data);
                    $whData['user_id'] = $this->userId;
					$whData['created_at'] = date('Y-m-d H:i:s');
                    $insertWHL = $whlModel->insertHistoryLog($whData);

                    $message['status'] = 'success';
                    $message['msg'] = 'Labor data has been saved successfully.';
                } catch (Exception $e) {
                    $message['status'] = 'error';
                    $message['msg'] = 'Error Occurred during the save labor data';
                }
            }

        } else {
            $message['status'] = 'error';
            $message['msg'] = 'Error Occurred during the save labor data';
        }
        echo json_encode($message);
        exit(0);
    } // close save labor charge data

    public function appGetLabourTabData($data) {

        $companyModel = new Model_Company();
        $nottenant = 1; // this for not listing the tenant user here.
        $users = $companyModel->getUserByBuildingId($data['bId'], $nottenant);
        $blModel = new Model_BillLabor();
        $blList = $blModel->getActiveBillLaborByBId($data['bId']);

        $brModel = new Model_BillRate();
        $brList = $brModel->getActiveBillRateByBId($data['bId']);

        $job_time = '00:00';

        $wpModel = new Model_WoParameter();
        $wpDetails = $wpModel->getWoParameterByBid($data['bId']);

        if ($wpDetails) {
            $time_charge = $wpDetails[0]['time_min_charge'];
            $time_fact = explode(" ", $time_charge);
            $time_fact_measure = trim($time_fact[1]);
            $time_fact_minute = trim($time_fact[0]);
            if ($time_fact_measure == 'Minutes') {$job_time = '00:' . $time_fact[0];} else { $job_time = $time_fact[0] . ':00';}
        }

        /*for labour list var labour_list or emp_id */
        $container = '';
        if ($users) {
            $dataout = '';
            foreach ($users as $key => $value) {
                $dataout['empid'] = $value->uid;
                $dataout['emp_name'] = $value->firstName . ',' . $value->lastName;
                $container['labour_list'][] = $dataout;
            }
        } else {
            $container['labour_list'] = null;
        }
        /*for labour cahrges var = labor_charge*/
        if ($blList) {
            $dataout = '';
            foreach ($blList as $bl) {
                $dataout['blid'] = $bl['blid'];
                $dataout['description'] = $bl['description'];
				$dataout['charge_hour'] = $bl['charge_hour'];
				$dataout['dft_charge'] = 0;
				 if($bl['set_default']=='1'){ 
					$dataout['dft_charge'] = 1;
				 }
                $container['labor_charge'][] = $dataout;
            }
        } else {
            $container['labor_charge'] = null;
        }
        /*for rate charges var = rate_charge*/
        if ($brList) {
            $dataout = '';
            foreach ($brList as $br) { 
                $dataout['brid'] = $br['brid'];
                $dataout['description'] = $br['description'];
				$dataout['dft_rate'] = 0; 
				 if($br['set_default']== 1){ 
					$dataout['dft_rate'] = 1;
				 }
                $container['rate_charge'][] = $dataout;
            }
        } else {
            $container['rate_charge'] = null;
        } 
        /*for job time  var=job_time*/
        $container['job_time'] = $job_time;
        $container['error'] = false;

        print json_encode($container);
        exit(0);
    } // close labor default data
    public function appSaveLabourService($data, $user_id) {
        $message = array();
        $current_details_value = '';
        if ($data['emp_id'] == '' || $data['charge_hour'] == '' || $data['rate_charge'] == '' || $data['job_time'] == '') {
            $message['status'] = 'error';
            $message['msg'] = 'Fill the Form Properly';
        } else {
            try {
				$woModel = new Model_WorkOrder();
				$workOrder = $woModel->getWorkOrder($data['woId']);
				$this->setTimezone($workOrder[0]['building']);
                $laborModel = new Model_Labor();
                //print_r($data);
                $whlModel = new Model_WoHistoryLog();
                if (isset($data['lid']) && $data['lid'] != '') {
                    $current_details_value = $whlModel->getWoHistoryLogByLabor($data['lid']);
                    $current_details_value = json_encode($current_details_value[0]);
                    $updateLabor = $laborModel->updateLabor($data, $data['lid']);
                } else {
                    $insertData = $laborModel->insertLabor($data);
                }

                $whData = array();

                $whData['woId'] = $data['woId'];
                $whData['log_type'] = 'Labor';
                if (isset($data['lid']) && $data['lid'] != '') {
                    $whData['current_value'] = $current_details_value;
                } else {
                    $whData['current_value'] = '';
                }
                if (isset($insertData)) {$data['lid'] = $insertData;}
                $whData['change_value'] = json_encode($data);
                $whData['user_id'] = $user_id;
				$whData['created_at'] = date('Y-m-d H:i:s');
                $insertWHL = $whlModel->insertHistoryLog($whData);

                $message['status'] = 'success';
                $message['msg'] = 'Labor data has been saved successfully.';
            } catch (Exception $e) {
                $message['status'] = 'error';
                $message['msg'] = 'Error Occurred during the save labor data';
            }
        }

        echo json_encode($message);
        exit(0);
    } // close save labor charge data
    public function appDeleteLabourService($data, $userId) {
        $data = $this->_request->getPost();
        if ($data['lid'] != '') {
            try {
                $whlModel = new Model_WoHistoryLog();
                $whData = array();
                $current_details_value = $whlModel->getWoHistoryLogByLabor($data['lid']);
                $whData['woId'] = $data['woId'];
                $whData['log_type'] = 'Labor';
                $whData['current_value'] = json_encode($current_details_value[0]);
                $whData['change_value'] = '';
                $whData['user_id'] = $userId;
                $insertWHL = $whlModel->insertHistoryLog($whData);
                $laborModel = new Model_Labor();
                $deleteLabor = $laborModel->deleteLabor($data['lid']);
                echo 'true';
            } catch (Exception $e) {
                echo 'false';
            }

        }
        exit(0);
    } // close delete labor charge
    public function appGetMaterialTabData($data) {

        $materialModel = new Model_Material();
        $materialList = $materialModel->getActiveMaterialByBId($data['bId']);

        $default_tax = 0;
        $wpModel = new Model_WoParameter();
        $wpDetails = $wpModel->getWoParameterByBid($data['bId']);
        if ($wpDetails) {
            $default_tax = $wpDetails[0]['auto_charge'];
			if($wpDetails[0]['override_markup'] == 1) {
				$default_markup = $wpDetails[0]['dft_markup'];
			} else {
				$default_markup = '';
			}
			$default_closed = $wpDetails[0]['status_closed'];
        }

        $dataout = '';
        $container = '';

        if ($materialList) {
            foreach ($materialList as $ml) {
                $dataout['mid'] = $ml['mid'];
                $dataout['cost'] = stripslashes($ml['cost']);
                $dataout['description'] = stripslashes($ml['description']);
                $dataout['markup'] = stripslashes($ml['markup']);

                $container['material_list'][] = $dataout;
            }
        } else {
            $container['material_list'] = null;
        }
        $container['default_tax'] = $default_tax;
		$container['default_markup'] = $default_markup;
		$container['default_closed'] = $default_closed;
        $container['error'] = false;

        print json_encode($container);
        exit(0);
    }
    public function appDeleteMaterialService($data, $user_id) {

        if ($data['mcId'] != '') {
            try {

                $whlModel = new Model_WoHistoryLog();
                $whData = array();
                $current_details_value = $whlModel->getWoHistoryLogByMaterial($data['mcId']);
                $whData['woId'] = $data['woId'];
                $whData['log_type'] = 'materials';
                $whData['current_value'] = json_encode($current_details_value[0]);
                $whData['change_value'] = '';
                $whData['user_id'] = $user_id;
                $insertWHL = $whlModel->insertHistoryLog($whData);
                $mcModel = new Model_MaterialCharge();
                $deleteMaterial = $mcModel->deleteMaterialCharge($data['mcId']);
                echo 'true';
            } catch (Exception $e) {
                echo 'false';
            }

        }

        exit(0);
    } // close delete material
    public function appSaveMaterialService($data, $user_id) {
        $message = array();
        $current_details_value = '';
        if ($data['material_id'] == '' || $data['cost'] == '' || $data['cost'] == '0' || $data['markup'] == '' || $data['quantity'] == '' || $data['quantity'] == '0') {
            $message['status'] = 'error';
            $message['msg'] = 'Fill the Form Properly';
        } else {
            try {
				$woModel = new Model_WorkOrder();
				$workOrder = $woModel->getWorkOrder($data['woId']);
				$this->setTimezone($workOrder[0]['building']);
                $mcModel = new Model_MaterialCharge();
                $whlModel = new Model_WoHistoryLog();
                if (isset($data['mcId']) && $data['mcId'] != '') {
                    $current_details_value = $whlModel->getWoHistoryLogByMaterial($data['mcId']);
                    $updateMaterial = $mcModel->updateMaterialCharge($data, $data['mcId']);
                    $current_details_value = json_encode($current_details_value[0]);
                } else {
                    $insertData = $mcModel->insertMaterialCharge($data);
                }

                $whData = array();
                $whData['woId'] = $data['woId'];
                $whData['log_type'] = 'materials';
                if (isset($data['mcId']) && $data['mcId'] != '') {
                    $whData['current_value'] = $current_details_value;
                } else {
                    $whData['current_value'] = '';
                }

                if (isset($insertData)) {$data['mcId'] = $insertData;}
                $whData['change_value'] = json_encode($data);
                $whData['user_id'] = $user_id;
				$whData['created_at'] = date('Y-m-d H:i:s');
                $insertWHL = $whlModel->insertHistoryLog($whData);

                $message['status'] = 'success';
                $message['msg'] = 'Material Charge has been saved successfully.';
            } catch (Exception $e) {
                $message['status'] = 'error';
                $message['msg'] = 'Error Occurred during the save material charge';
            }
        }
        echo json_encode($message);
        exit(0);
    } // close save material charge data
	
	 public function getDateAndTimeByID($bid) {
        $buildModel = new Model_Building();
        $build_data = $buildModel->getbuildingbyid($bid);
		$current_date = date('m/d/Y');
        $current_time = date('h:i:s A');
        if ($build_data) {
            $btimezone = $build_data[0]['timezone'];
            if ($btimezone != 0) {
                $tModel = new Model_TimeZone();
                $tzonelist = $tModel->getTimeZoneById($btimezone);
                $time_zone = $tzonelist[0]['time_value'];
                $date = new DateTime(null, new DateTimeZone($time_zone));
				$current_date = $date->format('m/d/Y');
                $current_time = $date->format('h:i:s A');
				$current_time_display = $date->format('h:i:s A T');
            } else {
                $date = new DateTime(null, new DateTimeZone(DEFAULT_TIMEZONE));
				$time_zone = $date->getTimezone()->getName();
				$current_date = $date->format('m/d/y');
                $current_time = $date->format('h:i:s A');
				$current_time_display = $date->format('h:i:s A T');
            }
        }

        echo json_encode(array('date' => $current_date, 'time' => $current_time,'display_time'=>$current_time_display, 'timezone'=>$time_zone,'error'=>false));
        exit(0);
    }
	
	 public function appSendEmailorder($data) {
		
        $tenantMapper = new Model_Tenant();
        $workOrderMapper = new Model_WorkOrder();
        $tenantDetail = $tenantMapper->getTenantById($data['tenantId']);
		
        $tenantInfo = $tenantDetail[0];
        $tenantData = (array) $tenantInfo;
        $tenantData['tenantId'] = $tenantData['id'];

        $sendmail = $workOrderMapper->sendWorkOrderEmail($data['woId'], $tenantData);
		echo json_encode(array('status'=>200, 'error'=>false));
        exit;
    }
	
	public function setTimezone($building_id){
		$timezone_Model	=	new Model_TimeZone();
		$build_model= new Model_Building();
		$tz_build_data = $build_model->getbuildingbyid($building_id);

		if(isset($tz_build_data[0]['timezone']) && $tz_build_data[0]['timezone']!=0){								  
			$timezone_data	=	$timezone_Model->getTimeZone($tz_build_data[0]['timezone']);
			$timezone=$timezone_data[0]['time_value'];			
			date_default_timezone_set($timezone);								
		} else if ($tz_build_data[0]['timezone']==0) {
		           date_default_timezone_set(DEFAULT_TIMEZONE);
		}
	 }
	 
	 public function sendResetInstruction($userDetail) {
	     
	     echo "<pre>";
	  echo "Haseeb"; die;
        try {         
    
            // create view object
            $emial_template = new Zend_View();
            $emial_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/email/');
            // assign valeues
			if($userDetail[0]['passkey']!='' && $userDetail[0]['passkeyStatus']==1 ) {
				$passkey=$userDetail[0]['passkey']; 
			} else { 
				$passkey = uniqid(); 
			}
            $emial_template->assign('firstname', $userDetail[0]['firstName']);
            $emial_template->assign('lastname', $userDetail[0]['lastName']);
            $emial_template->assign('email', $userDetail[0]['email']);
            $emial_template->assign('passkey', $passkey);   
             $bodyText = $emial_template->render('resetPassword.phtml');   
            // create mail object
            $mail = new Zend_Mail();
			
            // render view
            
			//echo $bodyText;exit;
						
            // configure base stuff
            $setModel = new Model_Setting();
		    $setData = $setModel->getSetting();
		    if($setData){
					$setting = $setData[0];
					$mail->setFrom($setting['from_email'],$setting['from_name']);
					if($setting['bcc_email'])
					$mail->addBcc($setting['bcc_email'], $setting['bcc_name']);					
			}else{
				$mail->setFrom('support@visionworkorders.com','Vision Work Order');
			}
  		    $mail->addTo($userDetail[0]['email'], $userDetail[0]['email']);
            $mail->setSubject('Reset password instruction');
            $mail->setBodyHtml($bodyText)->setBodyText($bodyText);
			$mail->addHeader('Content-Type', 'text/html; charset=utf-8');
			$mail->send();

		//  $mail->send();
            
            // set user pass key
            $userModel = new Model_User();
            $userModel->setPasskey($passkey, $userDetail[0]['uid']);
        } catch (Exception $e) {
            echo $e->getMessage(); die;
        }
    }
	// Reset Password
}