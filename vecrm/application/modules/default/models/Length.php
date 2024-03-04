<?php
/**
 * Description of Role
 *
 * @author ivtidai
 */
class Model_Length extends Zend_Db_Table_Abstract {

   protected $_name = 'length';   
   protected $_tab_role = 'length';  
   public $_errorMessage='';
   
   /* Get all users/staff detail */
    public function getLength($lID = "") {
        $select = $this->select()->where('status=?','1') ;
        
        if(!empty($lID)){
            $select = $select->where( 'lID = ? ', $lID );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
    
        
   
}
