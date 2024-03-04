<?php
/**
 * Description of Priority
 *
 * @author ivtidai
 */
class Model_Priority extends Zend_Db_Table_Abstract  {
    protected $_name = 'priority';   
    protected $_tab_role = 'priority';   
    public $_errorMessage='';
    
    public function getCerrentId() {
        $res = $this->fetchAll(
            $this->select()->from($this, array(
                new Zend_Db_Expr('max(pid) as pid')
            ))
        );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false;
    }
    
    /* Save priority */
    public function insertPriority($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update priority */
    public function updatePriority($data, $id) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('pid = ?', $id);	
        unset($data['pid']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getAllPriority($pid = "") {
        try {  
            $select = $this->select();
            if(!empty($pid)) {
                $select = $select->where('pid = ?', $pid);
            } 
            $res=$this->fetchAll($select);
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        } catch (Exception $e) {
            
            print_r($e->getMessage()); die;
        }
    }

    public function getAllPriorityByBuildId($bid = "") {
        try {  
            $select = $this->select();
            if(!empty($bid)) {
                $select = $select->where('building_id = ?', $bid);
            } 
            $res=$this->fetchAll($select);
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        } catch (Exception $e) {
            
            print_r($e->getMessage()); die;
        }
    }
    
    public function deletePreority($pid) {
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('pid = ?', $pid);	
        try {
			$catMapper = new Model_Category();
			$categoryData = $catMapper->deletePriorityCategory($pid);
			
			$scheduleMapper = new Model_Schedule();			
            $scheduleDetail = $scheduleMapper->deletePrioritySchedule($pid);
            $this->delete($where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
     public function getBuildingPriorityList($buildingId,$dir=''){
		try { 
			$res='';
			if(!empty($buildingId)) {
				$order = ($dir=='')?'pid ASC':'';
				$order = ($order=='')?'priorityName '.$dir:$order; 
				$select = $this->select();            
				$select = $select->where('building_id = ?', $buildingId);
				$select=  $select->order($order);             
				$res=$this->fetchAll($select);
		    }
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        } catch (Exception $e) {
            
            print_r($e->getMessage()); die;
        }
	}
	
	public function getPriorityName($pid){
		try{
			if(!empty($pid)){
				$select = $this->select('priorityName');
				$select = $select->where('pid = ?', $pid);
				$res=$this->fetchAll($select);
               return ($res && sizeof($res)>0)? $res->toArray() : false ;
			}
		}catch(Exception $e) {            
            print_r($e->getMessage()); die;
		}
	}
	
	/*
        * check building by name
        */
        public function getPrioritByName($priorityName,$buildId,$pid=''){
			if($priorityName!='' && !empty($priorityName)){
				$select=$this->select()->where('priorityName=?',$priorityName) ;
				if($buildId!=''){$select=$select->where('building_id=?',$buildId);}
				if($pid!=''){$select=$select->where('pid<>?',$pid);}				
				$res=$this->fetchAll($select);
				return ($res && sizeof($res)>0)? $res->toArray() : false;
			}
			
		}
		
		public function getPriorityByCategory($catId){
			if(!empty($catId)){
				$db = Zend_Db_Table::getDefaultAdapter(); 
				$select = $db->select()
						 ->from(array('pr' => 'priority'))
						 ->joinInner(array('cat' => 'category'), 'cat.prioritySchedule = pr.pid',array('cat_id'=>'cat.cat_id'))						
						 ->where('cat.cat_id = ?', $catId);				 				 
					$res = 	 $db->fetchAll($select);
				return ($res && sizeof($res)>0)? $res : false ;
			}else{
				return ($res && sizeof($res)>0)? $res : false ;
			}
		}		
		
}
