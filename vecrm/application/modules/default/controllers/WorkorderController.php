<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Work Order
 *
 * @author Brijesh
 */
class WorkorderController extends Ve_Controller_Base {
    
    public function init()  {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');
       $this->buildingMapper=new  Model_Building();        
       $this->woMapper = new Model_WorkOrder();
       $this->accessHelper = $this->_helper->access; 
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
        $this->email = Zend_Auth::getInstance()->getStorage()->read()->email;
    }	
 
    /**
     * show list of work order
     * 
     */ 
    public function indexAction(){
		 if($this->roleId=='5' || $this->roleId=='7'){
		 $tenant = new Model_Tenant();
		 //$tenantuser = $tenant->getTenantById($this->userId);
		 $tenantData = $tenant->getTenantByUser($this->userId);
		 $tenantInfo = $tenantData[0];
		 $page=$this->_getParam('page',1);
		 $order=$this->_getParam('order','woId');
		 $dir=$this->_getParam('dir','DESC');
		 if($this->roleId=='7')		 
		 $wolist = $this->woMapper->getTenantWorkOrder($tenantInfo->tenantId,$order,$dir,$this->userId);
		 else
		 $wolist = $this->woMapper->getTenantWorkOrder($tenantInfo->tenantId,$order,$dir);
		 $pageObj=new Ve_Paginator();
		 $paginator=$pageObj->fetchPageDataResult($wolist,$page,10);		 
		 $this->view->page = $page;
		 $this->view->roleId = $this->roleId;
		 //$this->view->tenantuser = $tenantuser[0];
		 $this->view->wolist = $paginator;
		 $this->view->order = $order;
		 $this->view->dir = $dir;
		 }
	   $this->view->roleId = $this->roleId;
	 }
   
   /***********
    * create work order action
    */
    
    public function createworkorderAction(){		
		$buildingList ='';
		if($this->roleId=='5' || $this->roleId=='7'){
		   /*******get building id of tenant ******/
		   $tenant = new Model_Tenant();
		   $priorityMapper = new Model_Priority();
		   $categoryMapper = new Model_Category();
		   $tenantData = $tenant->getTenantByUser($this->userId);
		   $buildId = $tenantData[0]->buildingId;		   
		   $priorityDetail = $priorityMapper->getBuildingPriorityList($buildId);
		   $categoryDetail = $categoryMapper->getBuildingCategoryList($buildId);		   
		   $userlistArray = array();
		   if($this->roleId=='5'){
			   $tenantId = $tenantData[0]->tenantId;
			   $tUserModel = new Model_TenantUser();
			   $userlistArray = $tUserModel->getTenantUsers($tenantId);
		   }else{
			  $userlistArray =  array((object)array('uid'=>$this->userId,'email'=>$this->email));
		   }				 
		   $submit_key = rand(10,1000);
		   $formKey = new Zend_Session_Namespace('formkey');
		   $formKey->submit_key = $submit_key;
		   $this->view->buildingList = $buildingList;
		   $this->view->tenantData = (array)$tenantData[0];
		   $this->view->priorityDetail = $priorityDetail;
		   $this->view->categoryDetail = $categoryDetail;
		   $this->view->form_key = $submit_key;
		   $this->view->userlistArray = $userlistArray;
	   }
	   $this->view->roleId = $this->roleId;
	}
	
	/**
	 * fetch worked order form with
	 * respect to building id.
	 */ 
	public function ajaxworkorderAction(){
		$data = $this->getRequest()->getPost();		
		if($this->getRequest()->getMethod() == 'POST'){
			$buildId = $data['buildId'];
			$priorityMapper = new Model_Priority();
			$categoryMapper = new Model_Category();
			$buildingMapper = new Model_Building();
			$tenantMapper = new Model_Tenant();
			$priorityDetail = $priorityMapper->getBuildingPriorityList($buildId);
			$categoryDetail = $categoryMapper->getBuildingCategoryList($buildId);			
			$buildingDetail = $buildingMapper->getbuildingbyid($buildId);
			$tenantDetail     = $tenantMapper->getTenantById($this->userId);			
			$tenantInfo = $tenantDetail[0];
			$tenantData       = (array)$tenantInfo;//->toArray();
			$workorder_template = new Zend_View();
			$workorder_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/workorder/');
			$workorder_template->assign('categoryDetail', $categoryDetail);
			$workorder_template->assign('priorityDetail', $priorityDetail);
			$workorder_template->assign('buildingDetail', $buildingDetail[0]);
			$workorder_template->assign('tenantData', $tenantData);			
			$bodyText = $workorder_template->render('ajaxworkorder.phtml');        
			echo $bodyText;
		}
        exit(0);
	}
	
	/**
	 * Save worke order
	 */ 
	public function saveworkorderAction(){
		$data = $this->getRequest()->getPost();		
		if(isset($data) && $data['building']!=''){
			$form_key = $data['form_key'];
			$smsg = new Zend_Session_Namespace('message');
		    $smsg->success_message = '';
		    $smsg->error_message = '';
			$formKey = new Zend_Session_Namespace('formkey');
		    $session_form_key = $formKey->submit_key;
			if($form_key!=$session_form_key){
				$smsg->error_message = 'Invalid Form Key';
				$this->_redirect('/workorder/createworkorder');
			}else{
				$formKey->submit_key='';
			}
			
			/**********check form validation **********/
			$form_valid = true;
			if($data['building']=='' || $data['tenant']=='' || $data['create_user']=='' || $data['category']=='' || $data['work_order_request']==''){
				$form_valid = false;
				$smsg->error_message = 'Form is not properly filled.';
				$this->_redirect('/workorder/createworkorder');
			}
			
			if($form_valid==true){
				try{
					$insertData = array();
					
					$insertData['tenant'] = $data['tenant'];
					$insertData['building'] = $data['building'];
					$insertData['suite_location'] = $data['suite_location'];
					$insertData['suite_location2'] = $data['suite_location2'];
					$insertData['create_user'] = $data['create_user'];
					$insertData['date_requested'] = $data['date_requested'];
					$insertData['time_requested'] = $data['time_requested'];
					//$insertData['priority'] = $data['priority'];
					$insertData['category'] = $data['category'];
					$insertData['internal_work_order'] = $data['internal_work_order'];
					$insertData['work_order_request'] = $data['work_order_request'];
					$insertData['user_id'] = $this->userId;				
					$woId = $this->woMapper->insertWorkOrder($insertData);
					
					$wpModel = new Model_WorkOrderUpdate();
					$wpData = array();
					$wpData['wo_id'] = $woId;
					$wpData['wo_request'] = $data['work_order_request'];
					$wpData['wo_status'] = 1;
					$wpData['current_update']=1;
					$wpData['created_at'] = date('Y-m-d H:i:s');
					$insertWp = $wpModel->insertWorkOrderUpdate($wpData);
					
					/*************upload file************/
					if($_FILES['wo_file']['name']!=''){						
							$uploaddir = BASE_PATH.'public/work_order/';
							$uploadfile_name = 'Work-order-'.time().'-'. basename($_FILES['wo_file']['name']);                        
							$uploadfile = $uploaddir.''.$uploadfile_name;                        
							if (!file_exists($uploaddir)) {
								mkdir($uploaddir, 0777, true);
							}
							move_uploaded_file($_FILES["wo_file"]["tmp_name"], $uploadfile);						
							$fileTitle = explode('.',basename($_FILES['wo_file']['name']));
							$file_title = $fileTitle[0];
							
							$file_name = $uploadfile_name;
							$insertFData = array();
							$insertFData['woId'] = $woId;
							$insertFData['file_title'] = $file_title;
							$insertFData['file_name'] = $file_name;
							try{
								$fileModel = new Model_WoFiles();
								$insertRecord = $fileModel->insertWoFile($insertFData);															
							}catch(Exception $e){
								echo $e->getMessage();
							}
						}
					
					/********* update work order number *********/
					$wo_number ='';
					if($data['building']!=''){
						$lastWoNum = $this->woMapper->getMaxWoNumber($data['building']);
						$update_wo_num = '';
						if($lastWoNum){
							$update_wo_num = $lastWoNum+1;
						}else{
							$update_wo_num = 1001;
						}
						try{
							$update_num = $this->woMapper->updateWorkOrder(array('wo_number'=>$update_wo_num),$woId);
							$wo_number = $update_wo_num;
						}catch(Exception $e){
							echo $e->getMessage();
						}
						
					}
					$insertData['woId'] = $woId;
					$insertData['wo_number'] = $wo_number;
					$priorityMapper = new Model_Priority();
					$categoryMapper = new Model_Category();
					$buildingMapper = new Model_Building();
					$accoutMapper = new Model_Account();
					$userMapper     = new Model_User();
					$tenantMapper = new Model_Tenant();
					$catDetail = $categoryMapper->getCategoryName($data['category']);
					$priorityDetail = $priorityMapper->getPriorityByCategory($data['category']);
					$buildingDetail = $buildingMapper->getbuildingbyid($data['building']);				
					$tenantDetail     = $tenantMapper->getTenantByUser($this->userId);
					$accountDetail = $accoutMapper->getcompany($buildingDetail[0]['cust_id']);			
					$tenantInfo = $tenantDetail[0];
					$tenantData       = (array)$tenantInfo;
					if($priorityDetail){
						
						$prioritData = (array)$priorityDetail[0];
						$priorityName=  $prioritData['priorityName'];
						$pid = $prioritData['pid'];
					}else{
					 $priorityName = 'Not Assigned';
					 $pid =0;
					}
					/*********** Insert work order schedule ************/
					$schedule_id=0;
					$psModel = new Model_Schedule();
					$startId =1;// for start schedule status;
					$psData = $psModel->getWoSchedule($pid,$startId);
					if($psData){
						$schedule_id = $psData[0]['id'];
					}
					$wssModel = new Model_WoScheduleStatus();
					$wss_data = array();
					$wss_data['worder_id']=	$woId;
					$wss_data['schedule_id']= $schedule_id;
					$wss_data['priority_id']= $pid;
					$wss_data['status']= 1;
					$wss_data['ckey']= md5(time());
					$wss_data['current_status']= 1;
					$wss_data['created_at'] = date('Y-m-d H:i:s');
					$ws_insert = $wssModel->insertWoSchedule($wss_data);			
					/****** send work order mail*******/
					$sendmail = $this->woMapper->sendWorkOrderEmail($woId,$tenantData);
					/***********display print work order *********/				
					$this->view->buildingDetail = $buildingDetail[0];
					$this->view->workOrder = $insertData;
					$this->view->cust_id = $this->cust_id;				
					$this->view->tenantInfo = $tenantData;
					$this->view->categoryName = $catDetail[0]['categoryName'];
					$this->view->priorityName  = $priorityName;
					$this->view->companyName  = $accountDetail[0]['companyName'];
					$this->view->msg = 'Work Order Request Saved Successfully.';
				}catch(Exception $e){
					$this->view->msg = $e->getMessage();
				}
			}	
	    }else{
			$this->_redirect('/workorder/createworkorder');
		}  
	}
	
	/**
	 *print work order 
	 */
	 public function printworkorderAction(){		 
		 $this->_helper->layout()->disableLayout();         
		 $woId = $page=$this->_getParam('woId',0);		
		 $workOrder = $this->woMapper->getWorkOrder($woId);
		 
		 if($workOrder){
		    $workOrderData = $workOrder[0];
		   
		    
		    $buildingMapper = new Model_Building();
		    $accoutMapper = new Model_Account();
		    $userMapper     = new Model_User();
		    $tenantMapper = new Model_Tenant();
		    $categoryMapper = new Model_Category();
		    $priorityMapper = new Model_Priority();
		    $catDetail = $categoryMapper->getCategoryName($workOrderData['category']);
		    $priorityDetail = $priorityMapper->getPriorityByCategory($workOrderData['category']);	   
		    $buildingDetail = $buildingMapper->getbuildingbyid($workOrderData['building']);
			$tenantDetail     = $tenantMapper->getTenantById($workOrderData['tenant']);						
			$tenantInfo = $tenantDetail[0];
			
			$accountDetail = $accoutMapper->getcompany($buildingDetail[0]['cust_id']);
			
			$tenantData       = (array)$tenantInfo;
			if($priorityDetail){
				$prioritData = (array)$priorityDetail[0];
				$priorityName=  $prioritData['priorityName'];
			}else
			$priorityName = 'Not Assigned';
			$this->view->buildingDetail = $buildingDetail[0];
			$this->view->workOrder = $workOrderData;
			$this->view->cust_id = $this->cust_id;			
			$this->view->tenantInfo = $tenantData;
			$this->view->categoryName = $catDetail[0]['categoryName'];
			$this->view->priorityName  = $priorityName;
			$this->view->companyName  = $accountDetail[0]['companyName'];		   
	    }else{
			echo 'Invalid data to print';
			exit;
		}
	 } 
	 
	 /**
	 *view  work order 
	 */
	 public function viewAction(){		 
		 //$this->_helper->layout()->disableLayout();         
		 $woId = $page=$this->_getParam('woId',0);		
		 $workOrder = $this->woMapper->getWorkOrder($woId);
		 
		 if($workOrder){
		    $workOrderData = $workOrder[0];
		   
		    
		    $buildingMapper = new Model_Building();
		    $accoutMapper = new Model_Account();
		    $userMapper     = new Model_User();
		    $tenantMapper = new Model_Tenant();
		    $categoryMapper = new Model_Category();
		    $priorityMapper = new Model_Priority();
		    $catDetail = $categoryMapper->getCategoryName($workOrderData['category']);
		    $priorityDetail = $priorityMapper->getPriorityByCategory($workOrderData['category']);	   
		    $buildingDetail = $buildingMapper->getbuildingbyid($workOrderData['building']);
			$tenantDetail     = $tenantMapper->getTenantById($workOrderData['tenant']);						
			$tenantInfo = $tenantDetail[0];
			$accountDetail = $accoutMapper->getcompany($buildingDetail[0]['cust_id']);
			
			$tenantData       = (array)$tenantInfo;
			if($priorityDetail){
				$prioritData = (array)$priorityDetail[0];
				$priorityName=  $prioritData['priorityName'];
			}else
			$priorityName = 'Not Assigned';
			$this->view->buildingDetail = $buildingDetail[0];
			$this->view->workOrder = $workOrderData;
			$this->view->cust_id = $this->cust_id;			
			$this->view->tenantInfo = $tenantData;
			$this->view->categoryName = $catDetail[0]['categoryName'];
			$this->view->priorityName  = $priorityName;	
			$this->view->companyName  = $accountDetail[0]['companyName'];	   
	    }else{
			echo 'Invalid data to print';
			exit;
		}
	 } 
	
	 
	 
	 /**
	  * Edit work order
	  * 
	  */
	  
	  public function editorderAction(){
		  $woId =$this->_getParam('woId','');
		  if($woId!=''&& !empty($woId)){
			$workOrder = $this->woMapper->getWorkOrder($woId);
			$building = $workOrder[0]['building'];
			$priorityMapper = new Model_Priority();
			$categoryMapper = new Model_Category();
			$buildingMapper = new Model_Building();
			$tenantMapper = new Model_Tenant();
			$priorityDetail = $priorityMapper->getBuildingPriorityList($building);
			$categoryDetail = $categoryMapper->getBuildingCategoryList($building);			
			$buildingDetail = $buildingMapper->getbuildingbyid($building);
			$tenantDetail     = $tenantMapper->getTenantByUser($this->userId);			
			$tenantInfo = $tenantDetail[0];
			$tenantData       = (array)$tenantInfo;//->toArray();
			$this->view->buildingDetail = $buildingDetail[0];
			$this->view->categoryDetail = $categoryDetail;
			$this->view->priorityDetail = $priorityDetail;
			$this->view->tenantData = $tenantData;
			$this->view->workOrder = $workOrder[0];
			$this->view->woId = $woId;
		  }else{
			  echo 'Bad Request';
			 $this->_redirect('/workorder'); 
		  }
	  } 
	  
	  
	  /***
	   * Update Work order
	   */ 
	   public function updateorderAction(){
		   $data = $this->getRequest()->getPost();
		   if(isset($data) && count($data)>0 && $data['wo_id']!=''){
			   $updateData = array();
			   $updateData['date_requested'] = $data['date_requested'];
			   $updateData['time_requested'] = $data['hour'].':'.$data['minute'].' '.$data['am_pm'];;
			   $updateData['priority'] = $data['priority'];
			   $updateData['category'] = $data['category'];
			   $updateData['work_status'] = $data['work_status'];
			   $updateData['work_order_request'] = $data['work_order_request'];			  
			   try{
				   $workOrder = $this->woMapper->updateWorkOrder($updateData,$data['wo_id']);
			   }catch(Exception $e){
				   echo $e->getMessage();
			   }
		   }
		   $this->_redirect('/workorder'); 
	   }
	   
	   /**
	    * Delete work order
	    */
	    
	    public function deleteorderAction(){
			$woId =$this->_getParam('woId','');
		  if($woId!=''&& !empty($woId)){
			  $workOrder = $this->woMapper->deleteWorkOrder($woId);
		  }else{
			  echo 'Bad Request';
			 
		  }
		   $this->_redirect('/workorder');		   		   
		}
		
		/******for testing email flow****/
		
		public function emailorderAction(){
			$woId = 1022;
			$tenantMapper = new Model_Tenant();
			$tenantDetail     = $tenantMapper->getTenantByUser($this->userId);			
			$tenantInfo = $tenantDetail[0];
			$tenantData       = (array)$tenantInfo;//->toArray();
			$sendmail = $this->woMapper->sendWorkOrderEmail($woId,$tenantData);
			exit;
		}		
		
}

?>
