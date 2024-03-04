<?php
/**
 * Model of Parent Location
 *
 * @author brijesh
 */
class Model_ParentLocation extends Zend_Db_Table_Abstract {

   protected $_name = 'parent_location';  
   public $_errorMessage='';
   
   /* Get all parent location */
    public function getParentLocation($pl_id = "") {
        $select = $this->select()->where('status=?','1') ;
        
        if(!empty($pl_id)){
            $select = $select->where( 'pl_id = ? ', $pl_id );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
    
        
   
}
