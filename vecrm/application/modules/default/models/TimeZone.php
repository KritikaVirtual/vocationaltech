<?php
/**
 * Description of time zone
 *
 * @author brijesh
 */
class Model_TimeZone extends Zend_Db_Table_Abstract {

   protected $_name = 'time_zone';   
   protected $_tab_role = 'time_zone';  
   public $_errorMessage='';
   
   /* Get list of time zone */
    public function getTimeZone($id="") {       
        
        $select = $this->select();
        if(!empty($id)){       
			$select = $select->where('id=?',$id);
		}
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    }
    
    public function getTimeZoneById($id) { 
        
        
        if(!empty($id)){ 
			$select = $this->select();      
			$select = $select->where('id=?',$id);
			$res = $this->fetchAll( $select );
        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
		}else
		return false;
        

    }  
    
    
        
   
}
