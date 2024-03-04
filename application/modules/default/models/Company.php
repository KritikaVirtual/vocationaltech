<?php 
class Model_Company extends Zend_Db_Table_Abstract {

   protected $_name = 'user_building_module_access';   
   protected $_tab_role = 'users';   
   public $_errorMessage='';
   
    /* Get all users/staff detail */
    public function getUserByBuildingId($bid,$nottenant=0,$userorder='',$userordir='',$search = array()) {
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
				 ->from(array('ub' => 'user_building_module_access'), array('*'))
				 ->joinInner(array('u' => 'users'), 'ub.user_id = u.uid')
				 ->where('ub.building_id = ?', $bid)
				 ->where('ub.status = ?', 1);
				 if(isset($search['search_by']) && $search['search_by']=='first_name'){
					$select = $select->where( "u.firstName Like'%$search[search_value]%'" );
				 }
				 if(isset($search['search_by']) && $search['search_by']=='last_name'){
					$select = $select->where( "u.lastName Like'%$search[search_value]%'" );
				 }
				 if(isset($search['search_by']) && $search['search_by']=='email'){
					$select = $select->where( "u.email Like'%$search[search_value]%'" );
				 }
	   if($nottenant){
		   $select = $select->where('u.role_id != ?', '5');
		   $select = $select->where('u.role_id != ?', '7');
	   }	
			if($userorder!='' && $userordir!='' ) {   
				$select->order("u.$userordir $userorder"); 
			} else {
				$select->order('u.firstName ASC');
			} //echo $select; die;
			$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;	
    }
	
	/* Get building users detail */
    public function getUserBuildingUserByRoleId($bid,$nottenant=0,$role_id) {
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
				 ->from(array('ub' => 'user_building_module_access'), array('*'))
				 ->joinInner(array('u' => 'users'), 'ub.user_id = u.uid')
				 ->where('ub.building_id = ?', $bid)
				 ->where('ub.status = ?', 1);
				 if(isset($search['search_by']) && $search['search_by']=='first_name'){
					$select = $select->where( "u.firstName Like'%$search[search_value]%'" );
				 }
				 if(isset($search['search_by']) && $search['search_by']=='last_name'){
					$select = $select->where( "u.lastName Like'%$search[search_value]%'" );
				 }
				 if(isset($search['search_by']) && $search['search_by']=='email'){
					$select = $select->where( "u.email Like'%$search[search_value]%'" );
				 }
	   if($nottenant){
		   $select = $select->where('u.role_id != ?', '5');
		   $select = $select->where('u.role_id != ?', '7');
	   }	
	   $select = $select->where('u.role_id=?', $role_id);
			$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;	
    }
	
	
}
