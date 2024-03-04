<?php
/**
 * Description of work status
 *
 * @author brijesh
 */
class Model_WorkStatus extends Zend_Db_Table_Abstract {

   protected $_name = 'work_status';   
   protected $_tab_role = 'work_status';  
   public $_errorMessage='';
   
   /* Get all users/staff detail */
    public function getWorkStatus($wsID = "") {       
        
        $select = $this->select();
        if(!empty($wsID)){
            $select = $select->where( 'id = ? ', $wsID );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
    
        
   
}
