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
}
