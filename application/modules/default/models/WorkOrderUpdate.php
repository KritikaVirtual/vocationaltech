<?php
/**
 * Description of Work order update
 *
 * @author brijesh
 */
class Model_WorkOrderUpdate extends Zend_Db_Table_Abstract {

   protected $_name = 'work_order_update';   
   protected $_tab_role = 'work_order_update';
   public $_errorMessage='';
   
   /* Get all work order update */
    public function getWoUpdate($woId = "") {       
        $select = $this->select();
        if(!empty($woId)){
			
            $select = $select->where( 'wo_id = ? ', $woId );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    }
    
    public function getCurrentWoUpdate($woId){
		 if(!empty($woId)){
			  $select = $this->select();
              $select = $select->where( 'wo_id = ? ', $woId );
              $select = $select->where( 'current_update = ? ', '1' );
              $res = $this->fetchAll( $select );   
              //echo $select;
             // die;
              return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
         return false;
	}   
   
   public function insertWorkOrderUpdate($data) {				
        try{            
            $this->_errorMessage="";    	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    public function updateWorkOrderByWoId($data,$woId) {
		 $this->_errorMessage=""; 			
        try{ 
			if(isset($woId) && !empty($woId)){ 
			 $where = $this->getAdapter()->quoteInto('wo_id = ?', $woId);
			 $this->update($data,$where);				   	
			 return true;
		   }else{
		     return false;		
		   }
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    public function getWoStatus($woId) {       
        if(!empty($woId)){			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('wou' => 'work_order_update'))
			->joinInner(array('ss' =>'schedule_status'),'ss.ssID = wou.wo_status',array('title'))
			->where( 'wo_id = ? ', $woId )->where( 'current_update = ? ',1);        		
			$res = $db->fetchRow( $select );
		}
        
		if(count(array($res))){
			$data=array("status_id"=>$res->wo_status,"status"=>$res->title);
			return $data;
		}else{
			return false;
		}

    }
	public function searchWorkOrderById($building_id, $workorder) {
	
	$db = Zend_Db_Table::getDefaultAdapter();
	$orderBy ='wo.wo_number ASC';
	if($workorder=='') {
	    $select = $db->select()
		->from(array('wo' => 'work_order'),array('wo_number'))
		->joinInner(array('up' => 'work_order_update'),'up.wo_id = wo.woId',array('wo_status'))
		 ->where('up.wo_status!=?',7)
		 ->where('up.current_update=?',1)
		 ->where('wo.building=?',$building_id);
		  $select = $select->order(array($orderBy));
         
		 
		$res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
	}else {
		 $select = $db->select()
		->from(array('wo' => 'work_order'),array('wo_number'))
		->joinInner(array('up' => 'work_order_update'),'up.wo_id = wo.woId',array('wo_status'))
		 ->where('up.wo_status!=?',7)
		 ->where('up.current_update=?',1)
		 ->where('wo.building=?',$building_id)
         ->where('wo.wo_number Like "%'.$workorder.'%"');
		 $select = $select->order(array($orderBy)); 
		$res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
	}	
		
	}
}
