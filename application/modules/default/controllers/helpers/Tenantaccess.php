<?php
class Zend_Controller_Action_Helper_Tenantaccess extends Zend_Controller_Action_Helper_Abstract{

	public function checkTenantaccess(){

		// allow role 5 and 7 only 
		 $roleId = Zend_Auth::getInstance()->getStorage()->read()->role_id;
         if($roleId == 5 || $roleId==7){
			$userId = Zend_Auth::getInstance()->getStorage()->read()->uid;
			$tenant = new Model_Tenant();
			$tenantuser = $tenant->getTenantByUser($userId);		
			
			$tenantCompanyList = $tenant->getTenantCompanies($userId);
			return $tenantCompanyList;
		
		}
		
	
	} 	

}