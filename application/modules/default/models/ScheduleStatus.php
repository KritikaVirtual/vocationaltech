<?php
/**
 * Description of Role
 *
 * @author ivtidai
 */
class Model_ScheduleStatus extends Zend_Db_Table_Abstract {

   protected $_name = 'schedule_status';   
   protected $_tab_role = 'schedule_status';  
   public $_errorMessage='';
   
   /* Get all users/staff detail */
    public function getScheduleStatus($ssID = "") {
        $select = $this->select()->where('status=?','1') ;
        
        if(!empty($ssID)){
            $select = $select->where( 'ssID = ? ', $ssID );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
    
        
   
}
