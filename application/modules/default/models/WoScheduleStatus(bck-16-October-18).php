<?php
/**
 * Description of Work order Schedule Status
 *
 * @author Brijesh Kumar
 */
class Model_WoScheduleStatus extends Zend_Db_Table_Abstract {

   protected $_name = 'wo_schedule_status';   
   protected $_tab_role = 'wo_schedule_status';  
   public $_errorMessage='';
   
  public function insertWoSchedule($data) {				
        try{            
            $this->_errorMessage="";    	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
     public function updateWoSchedule($data,$wssId) {
		 $this->_errorMessage=""; 			
        try{ 
			if(isset($wssId) && !empty($wssId)){ 
			 $where = $this->getAdapter()->quoteInto('wssId = ?', $wssId);
			 $this->update($data,$where);				   	
			 return true;
		   }else{
		     return false;		
		   }
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    
    public function getWoSchedule($woId,$sId){
		if(!empty($woId)){
			$select = $this->select();
            $select = $select->where( 'worder_id = ? ', $woId );  
            $select = $select->where( 'schedule_id = ? ', $sId );
            $select = $select->where( 'status = ? ', '1' );           
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
    
    public function getCurrentWoSchedule(){		    
            
             $db = Zend_Db_Table::getDefaultAdapter(); 
			 $select = $db->select()
					 ->from(array('ws' => 'wo_schedule_status'))
					 ->joinInner(array('ps' => 'priority_schedule'), 'ps.id = ws.schedule_id',array('Time','length','email_status','start_time_active','end_time_active','all_day_active'))
					 ->joinLeft(array('pp' => 'priority'), 'ws.priority_id = pp.pid',array('parent_priority_status'=>'pp.status','parent_email_status'=>'pp.email_status'))
					 ->where('ps.status = ?', '1')				 
					 ->where('ws.current_status = ?', '1');				 				 
				$res = 	 $db->fetchAll($select);
			 return ($res && sizeof($res)>0)? $res : false ;
	} 
	
	
	public function getReminderStatus($diff, $time, $length){ 
		      $time_factor = array('1'=>60,'2'=>3600,'3'=>86400,'4'=>604800,'5'=>2592000,'6'=>31536000);
		      //echo $time_factor[$length];
		      if(isset($time_factor[$length])){
				  $cal_time = intval($diff/$time_factor[$length]); 
				  return ($cal_time>=$time)?true:false;
			  }else return false;   
		      
	}
        
        
    public function getCurrentWs($woId){
		if(!empty($woId)){
			$select = $this->select();
            $select = $select->where( 'worder_id = ? ', $woId );       
            $select = $select->where( 'current_status = ? ', '1' );           
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
	
	public function getWoAllStatus($woId){
		$db = Zend_Db_Table::getDefaultAdapter(); 
			 $select = $db->select()
					 ->from(array('ws' => 'wo_schedule_status'))
					 ->joinInner(array('ps' => 'priority_schedule'), 'ps.id = ws.schedule_id',array('start_status','Time','length','email_status'))
					 ->joinInner(array('len' => 'length'), 'ps.length = len.lID',array('length_title'=>'len.title'))
					 ->joinLeft(array('pp' => 'priority'), 'ws.priority_id = pp.pid',array('parent_priority_status'=>'pp.status','parent_email_status'=>'pp.email_status'))
					 ->where('ps.status = ?', '1')	
					 ->where('pp.status = ?', '1')					 
					 ->where('ws.worder_id = ?', $woId);		 				 
				$res = 	 $db->fetchAll($select);
				
			 return ($res && sizeof($res)>0)? $res : false ;
	}    
   
    public function getCurrentWorkSchedule($woId){		    
            
             $db = Zend_Db_Table::getDefaultAdapter(); 
			 $select = $db->select()
					 ->from(array('ws' => 'wo_schedule_status'))
					 ->joinInner(array('ps' => 'priority_schedule'), 'ps.id = ws.schedule_id',array('Time','length'))				 
					 ->where('ws.current_status = ?', '1')
					 ->where('ws.worder_id = ?', $woId);				 				 
				$res = 	 $db->fetchAll($select);
			 return ($res && sizeof($res)>0)? $res : false ;
	} 
	
	public function getCalculateTime($time, $length){ 
		      $time_factor = array('1'=>60,'2'=>3600,'3'=>86400,'4'=>604800,'5'=>2592000,'6'=>31536000);
		      $cal_time = intval($time*$time_factor[$length]);
			   return $cal_time;
			   //return strtotime("$cal_time seconds");
		        
		      
	}
	 public function setTimezone($building_id){
		$timezone_Model	=new Model_TimeZone();
		$build_model= new Model_Building();
		$tz_build_data = $build_model->getbuildingbyid($building_id);

		if(isset($tz_build_data[0]['timezone']) && $tz_build_data[0]['timezone']!=0){								  
			$timezone_data	=$timezone_Model->getTimeZone($tz_build_data[0]['timezone']);
			$timezone=$timezone_data[0]['time_value'];			
			date_default_timezone_set($timezone);								
		} else if ($tz_build_data[0]['timezone']==0) {
		           date_default_timezone_set(DEFAULT_TIMEZONE);
		}
	 }
         
	public function getWorkOrderReminder(){
		//mail('brijeshkumar@virtualemployee.com','test Subject','This is test mail to checking cron');
		$chk_remminder=array(6,7);
		$currentSchedule = $this->getCurrentWoSchedule();	//echo '<pre>';	 print_r($currentSchedule);
//                    $uploaddir = BASE_PATH . 'vecrm/cron/';
//                    $file1 = $uploaddir."check.txt";
//                    $fp = fopen( $file1, "a" );
                    
		if($currentSchedule){
                    $lenModel = new Model_Length();
                    $lenList = $lenModel->getLength();			
                    $length = array();
                    if(!empty($lenList)){
                        foreach($lenList as $ll){
                            $length[$ll['lID']]=$ll['title'];
                        }
                    }
                    
                    
                    /* logfile section */
                    $msg = "";
                    $woModel = new Model_WorkOrder();
                   echo  $uploaddir = BASE_PATH . 'cron/';
                    $file1 = $uploaddir."activity.txt";
                    $fp = fopen( $file1, "a" );
                    if (!empty($currentSchedule)) {
                        foreach($currentSchedule as $curSch){
                            if ($curSch->parent_priority_status == 1 && $curSch->email_status == 1 && $curSch->parent_email_status == 1){

                                /******************* set timezone through building id *************************** */
                                $wo_building_data = $woModel->getWorkOrder($curSch->worder_id);
                                $build = new Model_Building();
                                $build_info = $build->getbuildingbyid($wo_building_data[0]['building']);
                                //print_r($build_info);
                                $accountModel = new Model_Account();
                                $accoundDetail = $accountModel->getCompanyByBuilding($wo_building_data[0]['building']);
                                     
                                fputs( $fp, "\r\n"."Company Name:".$accoundDetail[0]->companyName."|| Building Name:".$build_info[0]['buildingName']."||".date("Y-m-d,H:i:s"));
                                //fputs( $fp, "\r\n"."sdfsdfs"."\r\n");
                               
                                //$msg .= "Company Name:".$accoundDetail[0]->companyName."|| Building Name:".$build_info[0]['buildingName']."||".date("Y-m-d,H:i:s")."\n";
                                $this->setTimezone($wo_building_data[0]['building']);
                                $emailsend = false;
                                $current_time = date("h:iA");
                                $start_time_active = $curSch->start_time_active;
                                $end_time_active = $curSch->end_time_active;
                                $current_time = DateTime::createFromFormat('H:i a', $current_time);
                                $start_time_active = DateTime::createFromFormat('H:i a', $start_time_active);
                                $end_time_active = DateTime::createFromFormat('H:i a', $end_time_active);
                                /* @var $curSch type */
                                if ($curSch->all_day_active == 1) {
                                    $emailsend = true;
                                } else if ($current_time > $start_time_active && $current_time < $end_time_active) {
                                    $emailsend = true;
                                }

                                /******************* end - set timezone through building id *************************** */
                                if ($emailsend) {
                                    $currTime = time();
                                    $wou_status = new Model_WorkOrderUpdate();
                                    $status_data = $wou_status->getWoStatus($curSch->worder_id);
                                    $status_details = $status_data['status'];

                                    if (!in_array($status_data['status_id'], $chk_remminder)) {
                                        /*********** get schedule data ********** */ 
                                        if ($curSch->wssId)
                                        {
                                            $schData = '';
                                        }
                                        $schModel = new Model_Schedule();
                                        $schDetail = $schModel->getScheduleById($curSch->schedule_id);
                                        if ($schDetail) {
                                            $schData = $schDetail[0];
                                        }
                                        $lastTime = '';
                                        if ($curSch->updated_at != '0000-00-00 00:00:00') {
                                            $lastTime = $curSch->updated_at;
                                        } else {
                                            $lastTime = $curSch->created_at;
                                        }
                                        $timeDiff = $currTime - strtotime($lastTime);
                                        $time = $curSch->Time;
                                        $reminderStatus = $this->getReminderStatus($timeDiff, $time, $curSch->length);
                                        if ($reminderStatus) {
                                            try {
                                                $chData = array();
                                                $chData['reminder'] = $curSch->reminder + 1;
                                                $chData['updated_at'] = date('Y-m-d H:i:s');
                                                $saveReminder = $this->updateWoSchedule($chData, $curSch->wssId);

                                                //echo $notificationMail = $woModel->sendReminderNotification($curSch->worder_id,$curSch->schedule_id);
                                                if ($schData['start_status'] != '1') {
                                                    //echo 'remind'.$schData['start_status'];
                                                    $notificationMail = $woModel->sendReminderNotification($curSch->worder_id, $curSch->schedule_id);
                                                    //echo "<hr>";
                                                    //echo $notificationMail;
                                                    die;
                                                } else {
                                                    //echo 'work order';
                                                    $tenantMapper = new Model_Tenant();
                                                    $tenantDetail = $tenantMapper->getTenantByWoId($curSch->worder_id);
                                                    $tenantInfo = $tenantDetail[0];
                                                    $tenantData = (array) $tenantInfo;
                                                    $notificationMail = $woModel->resendWorkOrderEmail($curSch->worder_id, $tenantData);
                                                }
                                            } catch (Exception $e) {
                                                 $e->getMessage();
                                            }
                                        }
                                        //echo '<br/>';
                                    }
                                }
                            }
                        }
                    }
                     fclose( $fp );
                    
//                    fputs( $fp, $msg );
//                    fclose( $fp );
                }	
                exit;
            }
}

