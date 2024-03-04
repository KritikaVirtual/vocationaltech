<?php 
class Model_Company extends Zend_Db_Table_Abstract {

   protected $_name = 'user_building_module_access';   
   protected $_tab_role = 'users';   
   public $_errorMessage='';
   
    /* Get all users/staff detail */
    public function getUserByBuildingId($bid,$nottenant=0) {
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
				 ->from(array('ub' => 'user_building_module_access'), array('*'))
				 ->joinInner(array('u' => 'users'), 'ub.user_id = u.uid')
				 ->where('ub.building_id = ?', $bid)
				 ->where('ub.status = ?', 1);
	   if($nottenant){
		   $select = $select->where('u.role_id != ?', '5');
		   $select = $select->where('u.role_id != ?', '7');
	   }			 
	   		$select->order('u.firstName ASC');
			$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;	
    }
	
	
}
