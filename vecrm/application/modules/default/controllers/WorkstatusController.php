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
		$msg ='';
		$wSchedule = $this->wstModel->getWoSchedule($woId,$sId);			
		if(($wSchedule) &&  $ckey!='0'){
		  $wsData = $wSchedule[0];
		  // change current schedule and assign new schedule
		  if(($wsData['ckey']==$ckey) && ($wsData['current_status']=='1')){			  
			  try{
				   // change current schedule
				   $chData = array();
				   $chData['current_status'] = 0;
				   $chData['ckey'] = '';
				   $chData['updated_at'] = date('Y-m-d H:i:s');
				   $changeSchedule = $this->wstModel->updateWoSchedule($chData,$wsData['wssId']);
				   
				   // insert next schedule
				   $schModel = new Model_Schedule();
				   $schData = $schModel->getNextSchedule($sId);
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
						$wpData['wo_status'] = $schData[0]->start_status;
						$wpData['current_update']=1;
						$wpData['created_at'] = date('Y-m-d H:i:s');
						$insertWp = $wpModel->insertWorkOrderUpdate($wpData);
				   }
				   $msg = true;
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
