<?php
class Zend_Controller_Action_Helper_Access extends Zend_Controller_Action_Helper_Abstract{

	public function checkAccess($role_id, $location_id){
		$accessModel = new Model_Access();		
		return $accessModel->getUserAccessForModule($role_id,$location_id);
	} 	

}