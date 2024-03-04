<?php
/**
 * Description of Base Controller
 *
 * @author Brijesh
 */
class Ve_Controller_Base extends Zend_Controller_Action {
	
	public function init()
    {
        parent::init();
        $auth = Zend_Auth::getInstance();
        if(Zend_Auth::getInstance()->hasIdentity()){
			$userId = Zend_Auth::getInstance()->getStorage()->read()->uid;
			$logginSession = Zend_Auth::getInstance()->getStorage()->read()->login_session;
			$userModel = new Model_User();
			$userData = $userModel->getUserById($userId);
			if($userData){
				$userInfo = $userData[0];
				if($logginSession==$userInfo->login_session){
					if($userInfo->status=='0' || $userInfo->remove_status=='1'){
						//$this->redirect('/default/index/logout');
						//$this->_helper->_redirector->gotoUrl('/index/logout');
						header('Location: '.BASEURL.'index/logout');
					}
				}else{
					//$this->redirect('/default/index/logout');
					header('Location: '.BASEURL.'index/logout');
				}
			}else{
				//$this->redirect('/default/index/logout');
				header('Location: '.BASEURL.'index/logout');
			}
		}
    }
}
