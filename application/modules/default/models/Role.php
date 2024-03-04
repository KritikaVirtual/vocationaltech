<?php
/**
 * Description of Role
 *
 * @author ivtidai
 */
class Model_Role extends Zend_Db_Table_Abstract {

   protected $_name = 'role';   
   protected $_tab_role = 'role';   
   public $_errorMessage='';
   
   /* Get all users/staff detail */
    public function getRole($roleId = "") {
        $select = $this->select()->where('status=?','1') ;
        
        if(!empty($roleId)){
            $select = $select->where( 'roleID = ? ', $roleId );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
    
        
   
}
