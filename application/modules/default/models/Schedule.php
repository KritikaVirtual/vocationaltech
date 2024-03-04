<?php
/**
 * Description of Schedule
 *
 * @author ivtidai
 */
class Model_Schedule extends Zend_Db_Table_Abstract  {
    protected $_name = 'priority_schedule';   
    protected $_tab_role = 'priority_schedule';   
    public $_errorMessage='';
    
    public function getCerrentId() {
        $res = $this->fetchAll(
            $this->select()->from($this, array(
                new Zend_Db_Expr('max(id) as schedule_id')
            ))
        );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false;
    }
    
    /* Save Schedule */
    public function insertSchedule($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Schedule */
    public function updateSchedule($data, $id) { 
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('id = ?', $id);	
        unset($data['id']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getSchedule($pid = "", $id="",$dir='') {
        try {  
            $select = $this->select();
            $order = ($dir=='')?'id ASC':'';
			$order = ($order=='')?'start_status '.$dir:$order;
            if(!empty($pid)) {
                $select = $select->where('priority_id = ?', $pid);
            }
            if(!empty($id)) {
                $select = $select->where('id = ?', $id);
            }
            $select= $select->order($order);            
            $res=$this->fetchAll($select);
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        } catch (Exception $e) {
            
            print_r($e->getMessage()); die;
        }
    }
    
    public function getScheduleById($sid){
		try {
			if(!empty($sid)) { 				
            $select = $this->select();
            $select = $select->where('id = ?', $sid);            
            $res=$this->fetchAll($select);
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
            }else
             return false;
        } catch (Exception $e) {
            
            print_r($e->getMessage()); die;
        }
	}
    
    public function deleteSchedule($id = "") {
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('id = ?', $id);	
        try {
            $this->delete($where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function deletePrioritySchedule($pid){
		$where = $this->getAdapter()->quoteInto('priority_id = ?', $pid);	
        try {
            $this->delete($where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
	}
	
	public function getWoSchedule($pid,$startId){
		$db = Zend_Db_Table::getDefaultAdapter();	
		try {  
            $select = $db->select()
			->from(array('psc' => 'priority_schedule'))	
			 ->joinLeft(array('ps' => 'priority'), 'ps.pid = psc.priority_id',array('priority_status'=>'ps.status'));
            if(!empty($pid)) {
                $select = $select->where('psc.priority_id = ?', $pid);
            }
            if(!empty($startId)) {
                $select = $select->where('psc.start_status = ?', $startId);
            }
            $res=$db->fetchAll($select);
			$res=json_encode($res);
			$res=json_decode($res, true);
            return ($res && sizeof($res)>0)? $res : false ;
        } catch (Exception $e) {
            
            print_r($e->getMessage()); die;
        }
	}
	
	/**********Get Next Schedule**********/
	
        public function getScheduleData($sId){
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
				 ->from(array('ps' => 'priority_schedule'))
				 //->joinInner(array('psc' => 'priority_schedule'), 'ps.start_status = psc.end_status AND ps.priority_id = psc.priority_id',array('last_schedule_id'=>'psc.id'))
				 //->where('psc.status = ?', '0')				 
				 ->where('ps.id = ?', $sId);
                
			$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;
	}
        
	public function getNextSchedule($sId){
		$db = Zend_Db_Table::getDefaultAdapter(); 
	echo 	$select = $db->select()
				 ->from(array('ps' => 'priority_schedule'))
				 ->joinInner(array('psc' => 'priority_schedule'), 'ps.start_status = psc.end_status AND ps.priority_id = psc.priority_id',array('last_schedule_id'=>'psc.id'))
				 ->where('psc.status = ?', '0')				 
				 ->where('psc.id = ?', $sId);
           die;     
			$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;
	}
	
	public function getSchdeuleByCurrWoStatus($woId,$status){
		if(!empty($woId)){
			$db = Zend_Db_Table::getDefaultAdapter(); 
		    $select = $db->select()
				 ->from(array('ps' => 'priority_schedule'))
				 ->joinInner(array('cat' => 'category'), 'ps.priority_id = cat.prioritySchedule',array('cat.cat_id'))
				 ->joinInner(array('wo' => 'work_order'), 'cat.cat_id = wo.category',array('wo.woId'))
				 ->where('wo.woId = ?', $woId)				 
				 ->where('ps.start_status = ?', $status);			 				 
			$res = 	 $db->fetchAll($select);
            return ($res && sizeof($res)>0)? $res : false ;
		}else
		return false;
	}
	
	public function changePrioritySchedule($pid, $scheduledata) {
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
		->from(array('ws' => 'priority_schedule'),array('id'))
		->where('ws.priority_id = ?', $pid); 
		$scheduleid = $db->fetchAll($select);
			foreach($scheduleid as $value) {
				$this->updateSchedule($scheduledata, $value -> id);
			}
	}
}

