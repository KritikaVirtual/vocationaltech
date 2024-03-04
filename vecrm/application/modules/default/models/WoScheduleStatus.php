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
					 ->joinInner(array('ps' => 'priority_schedule'), 'ps.id = ws.schedule_id',array('Time','length'))
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
			  }else
			   return false;   
		      
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
					 ->joinInner(array('ps' => 'priority_schedule'), 'ps.id = ws.schedule_id',array('start_status','Time','length'))
					 ->joinInner(array('len' => 'length'), 'ps.length = len.lID',array('length_title'=>'len.title'))
					 ->where('ps.status = ?', '1')				 
					 ->where('ws.worder_id = ?', $woId);				 				 
				$res = 	 $db->fetchAll($select);
			 return ($res && sizeof($res)>0)? $res : false ;
	}    
   
    public function getCurrentWorkSchedule($woId){		    
            
             $db = Zend_Db_Table::getDefaultAdapter(); 
			 $select = $db->select()
					 ->from(array('ws' => 'wo_schedule_status'))
					 ->joinInner(array('ps' => 'priority_schedule'), 'ps.id = ws.schedule_id',array('Time','length'))
					 ->where('ps.status = ?', '1')				 
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
	
	public function getWorkOrderReminder(){
		//mail('brijeshkumar@virtualemployee.com','test Subject','This is test mail to checking cron');
		$currentSchedule = $this->getCurrentWoSchedule();				
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
				 $reminderStatus = $this->getReminderStatus($timeDiff,$time,$curSch->length);
				if($reminderStatus){
					try{
					   $chData = array();
					   $chData['reminder'] = $curSch->reminder+1;				   
					   $chData['updated_at'] = date('Y-m-d H:i:s');
					   $saveReminder = $this->updateWoSchedule($chData,$curSch->wssId);
					   
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
}

