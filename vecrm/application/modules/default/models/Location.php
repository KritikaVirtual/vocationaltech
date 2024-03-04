<?php
/**
 * Model of Location table
 *
 * @author brijesh
 */
class Model_Location extends Zend_Db_Table_Abstract {

   protected $_name = 'location';  
   public $_errorMessage='';
   
   /* Get all location detail */
    public function getLocation($id = "") {
        $select = $this->select();
                
        if(!empty($id)){
            $select = $select->where( 'id = ? ', $id );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    }
    
    public function getLocationByParent($parent_id){
		if(!empty($parent_id)){
			$select = $this->select();
			$select = $select->where( 'parent_id = ? ', $parent_id );
			$res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
		}else
		return false;
	}
    
    
        
   
}
