<?php
/**
 * Description of Access Controller
 *
 * @author brijesh
 */
class AccessController extends Ve_Controller_Base {
    
    public function init()  {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');  
       
    }
    
    // Call befor any action and check is user login or not
    public function preDispatch()
    {    	
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/index');
        }
        
        $level=(Zend_Auth::getInstance()->getStorage()->read())? Zend_Auth::getInstance()->getStorage()->read()->role_id:'';    	     	
    	$this->userId=Zend_Auth::getInstance()->getStorage()->read()->uid;
    	$this->roleId=Zend_Auth::getInstance()->getStorage()->read()->role_id;
    	$this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
    }	
    
    public function indexAction() {
        $this->_redirect('/access/matrix');
    }
    
    
    public function matrixAction() {
        $accessModel = new Model_Access();
        $plModel = new Model_ParentLocation();
        $roleModel = new Model_Role();
        $arrData = $accessModel->getcompany();
        $plocation = $plModel->getParentLocation();
        $roleData = $roleModel->getRole();
        $this->view->access = $arrData;
        $this->view->plocation = $plocation;
        $this->view->roleData = $roleData;
    }

    public function setaccessAction(){
        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);
        $accessModel = new Model_Access();

        if(count($_POST) > 0){
            $data = $this->getRequest()->getPost();
            if($accessModel->checkAccess($data['role'], $data['location_id'])){ 
                
                if($accessModel->updateAccess($data))
                {
                    echo true;
                    exit();
                }
                else{
                    echo false;
                    exit();
                }
            }
            else{
                if($accessModel->addAccess($data) > 0){
                    echo true;
                    exit();
                }
                else{
                    echo false;
                    exit();
                }
            }
            //var_dump($accessModel->addAccess($data));
        }
        
    }    
    
    
}
