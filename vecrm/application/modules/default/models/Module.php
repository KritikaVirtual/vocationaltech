<?php
/**
 * Description of module
 *
 * @author ivtidai
 */
class Model_Module extends Zend_Db_Table_Abstract  {
   protected $_name = 'modules';   
   protected $_tab_role = 'modules';   
   public $_errorMessage='';
   
   /* Get all users/staff detail */
    public function getModule() {
        $select=$this->select();				
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
    
    public function getModuleListing($moduleids = array()) {
        $select=$this->select();	
        if( !empty ( $moduleids ) ) {
            $select = $select->where( 'module_id in ('.implode(",", $moduleids).')');
        }	
        $res = $this->fetchAll($select);

        return ($res && sizeof($res)>0)? $res->toArray() : false ;
    }
}
