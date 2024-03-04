<?php
class BuildingController extends Zend_Controller_Action
{
	private $userId='';
	private $roleId='';
	public function init() 
    {
       parent::init();
       $this->_helper->layout()->setLayout('homelayout');  
    }
	// Call befor any action and check is user login or not
    public function preDispatch()
    {    	
		if (!Zend_Auth::getInstance()->hasIdentity()) $this->_redirect('/index');
		 $level=(Zend_Auth::getInstance()->getStorage()->read())?Zend_Auth::getInstance()->getStorage()->read()->role_id:'';    	 
    	/**if($level==md5('D')){
    		Zend_Auth::getInstance()->clearIdentity();
			$this->_redirect('sales');
    	}else if($level==md5('A')) {
			$this->_redirect('site');
		}*/
    	$this->userId=Zend_Auth::getInstance()->getStorage()->read()->uid;
    	$this->roleId=Zend_Auth::getInstance()->getStorage()->read()->role_id;
    }	
	public function indexAction()
    {
		
    }
	public function registrationAction()
	{
	
	}	
}
?>