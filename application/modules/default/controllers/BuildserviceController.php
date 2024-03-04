<?php

/**
 * Description of Build Service Controller
 *
 * @author Brijesh Kumar
 */
class BuildserviceController extends Ve_Controller_Base {
    
    public function init()  {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');  
       $this->notesModel = new Model_Notes();
       $this->nm = new Zend_Session_Namespace('bserv_message');
       $this->accessHelper = $this->_helper->access;
       $this->bservice_location = 18;  
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
    
    
    public function indexAction(){
        $search_array = array();
        if($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $search_array['search_by'] = $data['search_by'];
            $search_array['search_value'] = $data['search_value'];
            $this->view->search = $search_array;
        }
        $companyListing ='';
        $buildingMapper=new  Model_Building();
        if($this->roleId=='9'){
            $companyListing = $buildingMapper->getCompanyBuilding($this->cust_id);
        }else{
            $user_build_mod = new Model_UserBuildingModule();
            $buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
            if($buildinglists){
                $build_id_array = array();
                foreach($buildinglists as $buildlist)
                    $build_id_array[] = $buildlist['building_id'];
                    $companyListing = $buildingMapper->getBuildingList($build_id_array);			
            }
        }
        foreach($companyListing as $cl){
            $buildIds[] = $cl['build_id'];
        }

        $select_build_id = $this->_getParam('bid','');		
        if(empty($select_build_id) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $select_build_id = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie',$select_build_id,time() + (86400/24), "/");
        if($companyListing!=''){
            if($select_build_id!='')
                $select_build_id = $select_build_id;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }

        // echo 'hello'.$select_build_id;
        $order = $this->_getParam('order','service_name');	
        $dir = $this->_getParam('dir','ASC');				
        $this->view->order = $order;
        $this->view->dir = $dir;
        $bsModel = new Model_BuildService();
        if($order!='' && $dir!=''){
            $sorting_order = $order.' '.$dir;
        }else{
            $sorting_order = '';  
        }
        $page = $this->_getParam('page', 1);
        $pageObj = new Ve_Paginator();
        $show = $this->_getParam('show', '');
        if($show==""){
           $show=15; 
        }
        $search_array = array_map("addslashes", $search_array);
        $search_array = array_map("addslashes", $search_array);
        $search_array = array_map("addslashes", $search_array);
        //$order ='';
        $bsList = $bsModel->getBuildServiceByBId($select_build_id, $sorting_order, $search_array);

        if($show!='all'){            
            $bsList = $pageObj->fetchPageDataResult($bsList, $page, $show);      
        }else{
            $bsList = $pageObj->fetchPageDataResult($bsList, $page, $show);
        }
        
        $this->view->show=$show;
        $this->view->bsList = $bsList;
        $this->view->custID = $this->cust_id; 
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        $this->view->roleId = $this->roleId;
        $this->view->acessHelper = $this->accessHelper;        
        $this->view->bservice_location = $this->bservice_location;
        $this->view->userId = $this->userId;
    }// close index
	
	public function addserviceAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		$this->view->bId= $data['bId'];
		
	}// close add service
	
	public function saveserviceAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 //print_r($data);
			 if($data['service_name']=='' || $data['unit_measure']=='' || $data['cost']=='' || $data['minimum']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';				 
			 }else{
				 $bsModel = new Model_BuildService();
				 $serviceName = $bsModel->checkServiceName($data['service_name'],$data['building']);			
				 
				 if(!$serviceName){
					 
					 try{
						 $submitBuildingService = $bsModel->insertBuildService($data);
						 $message['status'] = 'success';
				         $message['msg']='Building Service save successfully.';
					 }catch(Exception $e){
					    $message['status'] = 'error';
				        $message['msg']='Error Occurred during the save labor charge';
					 }
				 }else{
				   $message['status'] = 'error';
				   $message['msg']='This Service Name already in use.';
				 }
			 }
			 
			echo json_encode($message);
		 }
		 exit(0);
	}// close save service
	
	public function editserviceAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		$this->view->bsid= $data['bsid'];
		$this->view->bId= $data['bId'];
		$bsModel = new Model_BuildService();
		$serviceData = $bsModel->getBuildService($data['bsid']);
		$this->view->serviceData = $serviceData;
		
	}// close edit service
	
	public function updateserviceAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 //print_r($data);
			 if($data['service_name']=='' || $data['unit_measure']=='' || $data['cost']=='' || $data['minimum']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';				 
			 }else{
				 $bsModel = new Model_BuildService();
				 $serviceName = $bsModel->checkServiceName($data['service_name'],$data['building'],$data['bsid']);			
				 
				 if(!$serviceName){
					 
					 try{
						 $submitBuildingService = $bsModel->updateBuildService($data,$data['bsid']);
						 $message['status'] = 'success';
				         $message['msg']='Building Service save successfully.';
					 }catch(Exception $e){
					    $message['status'] = 'error';
				        $message['msg']='Error Occurred during the save building service';
					 }
				 }else{
				   $message['status'] = 'error';
				   $message['msg']='This service name already in use.';
				 }
			 }
			 
			echo json_encode($message);
		 }
		 exit(0);
	}// close update service
	
	public function deletebserviceAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 if($data['bsid']!=''){
				 try{
				     $bsModel = new Model_BuildService();
				     $deleteLabor = $bsModel->deleteBuildService($data['bsid']);
				     echo 'true';				     
				 }catch(Exception $e){
					 echo 'false';
				 }
				 
			 }
		 }
		exit(0); 
	}// close delete service
	
	public function servicetemplateAction(){
		$this->_helper->layout()->disableLayout();
		$cust_id = $this->cust_id;
		$data = $this->getRequest()->getPost();
		$this->view->bId= $data['bId'];
		$bsModel = new Model_BuildService();
		$bsList = $bsModel->getBuildServiceByCompany($this->cust_id,$data['bId']);
		$this->view->bsList = $bsList;
		
	}// close service template
	
	public function loadservicetemplateAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		$this->view->bsid= $data['bsid'];
		$laborData = '';
		//$this->view->bId= $data['bId'];
		if($data['bsid']!=''){
			$bsModel = new Model_BuildService();
			$serviceData = $bsModel->getBuildService($data['bsid']);
		}else{
			$serviceData =false;
		}
		$this->view->serviceData = $serviceData;
	}// close load service template
	
}    
