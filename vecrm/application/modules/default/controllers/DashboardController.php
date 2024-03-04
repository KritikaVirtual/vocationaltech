<?php

class DashboardController extends Ve_Controller_Base
{

	private $userId='';
	private $roleId='';
	public function init() 
    {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');  
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
    	$this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
    	if(!isset($_COOKIE['build_cookie'])){
			$_COOKIE['build_cookie']='';
	      }
    }	
	
	
 
	
	
	public function indexAction()
    {
    	
 		if($this->roleId==1)
		{
		$message='You are in administration area.';
			
		}else if($this->roleId==9)
		{
			
			
			$accountMapper = new Model_Account();
			$companyDetail = $accountMapper->getcompany($this->cust_id);			
			$companyInfo = $companyDetail[0];
			
			$message = 'You are in <b>'.$companyInfo['companyName'].'</b> company administration area.';
		}
		else
		{
			
		$roleMapper = new Model_Role();
		$roleDetail = $roleMapper->getRole($this->roleId);			
		$rolenfo = $roleDetail[0];	
		$message='You are in building\'s <b>'.$rolenfo['title'].'</b> area.';
	
		}
       
         $this->view->message =$message;
		 $noredirect =array(1,5,7);
         if(!in_array($this->roleId,$noredirect)){
			 $this->_redirect('/dashboard/workorder');
		 }
    }
    
    
    public function workorderAction(){		
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
	     
	    $page=$this->_getParam('page',1);
		$order=$this->_getParam('order','woId');
		$dir=$this->_getParam('dir','DESC'); 
	    $wolist = '';
		$build_ID = $this->_getParam('bid','');
		$select_build_id =$build_ID;
		/*********set building in cookie **********/
		$set_cookie = setcookie('build_cookie',$build_ID,time() + (86400/24), "/");
		
		//$_COOKIE['build_cookie'];
		
		$woModel = new Model_WorkOrder();
		
		
		$search_array = array();
		if(isset($_REQUEST['search_status']) && $_REQUEST['search_status']!='')
		 $search_array['search_status'] = $_REQUEST['search_status'];
		 
		 if(isset($_REQUEST['category_name']) && $_REQUEST['category_name']!='')
		 $search_array['category_name'] = $_REQUEST['category_name'];
		 
		 if(isset($_REQUEST['tenant_name']) && $_REQUEST['tenant_name']!='')
		 $search_array['tenant_name'] = $_REQUEST['tenant_name'];
		 
		 if(isset($_REQUEST['search_wo']) && $_REQUEST['search_wo']!='')
		 $search_array['search_wo'] = $_REQUEST['search_wo'];
		 
		 if(isset($_REQUEST['from_date']) && $_REQUEST['from_date']!='')
		 $search_array['from_date'] = date("Y-m-d",strtotime($_REQUEST['from_date']));
		 
		 if(isset($_REQUEST['to_date']) && $_REQUEST['to_date']!='')
		 $search_array['to_date'] = date("Y-m-d",strtotime($_REQUEST['to_date']));
		 
		
		if($companyListing!=''){
			/*if($this->roleId=='9'){*/
				if($build_ID==''){
					$buildIds = array();
					 foreach($companyListing as $cl){
						 $buildIds[] = $cl['build_id'];
					 }		 
					  $wolist = $woModel->getWorkOrderByBuilIds($buildIds,$order,$dir,$search_array);
				  }else{
					  $wolist = $woModel->getBuildingWorkOrder($build_ID,$order,$dir,$search_array);
				  }
		   /* }else{
				//$eguModel = new Model_EmailGroupUsers();
				//$egulist =  $eguModel->getGroupIdByUser($this->userId);
				//$eg_array = array();
				
				$catIds = array();
				$catModel = new Model_Category();
				$catlist = $catModel->getCategoryByUser($this->userId,$build_ID);
				foreach($catlist as $cl){
					$catIds[] = $cl['cat_id'];
				}
				
				$catEmaillist =  $catModel->getCategoryByEmailUser($this->userId,$build_ID);
				
				foreach($catEmaillist as $cel){
					if(!in_array($cel->cat_id,$catIds)){
						$catIds[] = $cel->cat_id;
					}
				}
						
				$wolist = $woModel->getWorkOrderByCatIds($catIds);
			}*/		  
		 }
		 $pageObj=new Ve_Paginator();
		 $paginator = $pageObj->fetchPageDataResult($wolist,$page,10);		 
		 $this->view->page = $page;
		 $view_type = $this->_getParam('view_type','line');
		 $this->view->custID = $this->cust_id;
		 $this->view->companyListing = $companyListing;
		 $this->view->select_build_id = $select_build_id;
		 $this->view->wolist = $paginator;
		 $this->view->order = $order;
		 $this->view->dir = $dir;
		 $this->view->view_type = $view_type;
	}
	
	public function orderdetailAction(){		
        //makes disable layout
	    $this->_helper->getHelper('layout')->disableLayout();
		$woId = $this->_getParam('woId','');
		$woModel = new Model_WorkOrder();
		$wodetail = $woModel->getWorkOrderInfo($woId);
		$this->view->woData = $wodetail[0];
	}
	 
	 
	 public function updateorderAction(){
		   $data = $this->getRequest()->getPost();
		   if(isset($data['work_order_id'])){
					$woId = $data['work_order_id'];
					$wpModel = new Model_WorkOrderUpdate();					    		   			
					// reset work order update
					$resetCurrent = $wpModel->updateWorkOrderByWoId(array('current_update'=>0),$woId);
					if($data['insert_schedule']=='1'){
						$schModel = new Model_Schedule();
						$schdata = array();
						$schdata['priority_id'] = $data['priority'];
						$schdata['length'] = $data['length'];
						$schdata['Time'] = $data['Time'];
						$schdata['start_status'] = $data['order_status'];
						$schdata['end_status'] =2;
						$schdata['access_days'] =1;
						$schdata['status'] =1;
						$schdata['created_by'] = $this->userId;
						$schdata['created_date'] = date('Y-m-d');
						$insertData = $schModel->insertSchedule($schdata); 
					}
					$wpData = array();
					$wpData['wo_id'] = $woId;
					$wpData['internal_note'] = $data['internal_note'];
					$wpData['wo_status'] = $data['order_status'];
					$wpData['current_update']=1;
					$wpData['user_id']=$this->userId;
					$wpData['created_at'] = date('Y-m-d H:i:s');
					$insertWp = $wpModel->insertWorkOrderUpdate($wpData);
					
					/*********History Log *********/
				    $whlModel = new Model_WoHistoryLog();		   
				    $whData= array();
				    $whData['woId']=$woId;
				    $whData['log_type']='status';
				    $whData['current_value']=$data['current_wstatus'];
				    $whData['change_value']=$data['order_status'];
				    $whData['user_id']=$this->userId;
				    $insertWHL = $whlModel->insertHistoryLog($whData);
					
					// update work order schedule
					$schModel = new Model_Schedule();
					$schData = $schModel->getSchdeuleByCurrWoStatus($woId,$data['order_status']);
		    if($schData){
				   $wstModel = new Model_WoScheduleStatus();				       
				   // fetch current status work order
				   $wsDetail = $wstModel->getCurrentWs($woId);
				   foreach($wsDetail as $wsd){
					   $wsUpdate = $wstModel->updateWoSchedule(array('current_status'=>0),$wsd['wssId']);
				   }
				   $wss_data = array();
				   $wss_data['worder_id']=	$woId;
				   $wss_data['schedule_id']= $schData[0]->id;
				   $wss_data['priority_id']= $schData[0]->priority_id;
				   $wss_data['status']= 1;
				   //$wss_data['ckey']= md5(time());
				   $wss_data['current_status']= 1;
				   $wss_data['created_at'] = date('Y-m-d H:i:s');
				   $ws_insert = $wstModel->insertWoSchedule($wss_data);
			}
		}
		$this->_redirect('/dashboard/workorder');
	 }
	 
	 public function updateajaxorderAction(){
		 $data = $this->getRequest()->getPost();
		   if(isset($data['woId'])){
			       try{
					$woId = $data['woId'];
					$wpModel = new Model_WorkOrderUpdate();					    		   			
					// reset work order update
					$resetCurrent = $wpModel->updateWorkOrderByWoId(array('current_update'=>0),$woId);
					// insert schedule status
					if($data['insert_schedule']=='1'){
						$schModel = new Model_Schedule();
						$schdata = array();
						$schdata['priority_id'] = $data['priority'];
						$schdata['length'] = $data['slength'];
						$schdata['Time'] = $data['time'];
						$schdata['start_status'] = $data['order_status'];
						$schdata['end_status'] =2;
						$schdata['access_days'] =1;
						$schdata['status'] =1;
						$schdata['created_by'] = $this->userId;
						$schdata['created_date'] = date('Y-m-d');
						$insertData = $schModel->insertSchedule($schdata); 
					}
					$wpData = array();
					$wpData['wo_id'] = $woId;
					$wpData['internal_note'] = $data['internal_note'];
					$wpData['wo_status'] = $data['order_status'];
					$wpData['current_update']=1;
					$wpData['user_id']=$this->userId;
					$wpData['created_at'] = date('Y-m-d H:i:s');
					$insertWp = $wpModel->insertWorkOrderUpdate($wpData);
					
					/*********History Log *********/
				    $whlModel = new Model_WoHistoryLog();		   
				    $whData= array();
				    $whData['woId']=$woId;
				    $whData['log_type']='status';
				    $whData['current_value']=$data['current_status'];
				    $whData['change_value']=$data['order_status'];
				    $whData['user_id']=$this->userId;
				    $insertWHL = $whlModel->insertHistoryLog($whData);
				    
					// update work order schedule
					$schModel = new Model_Schedule();
					$schData = $schModel->getSchdeuleByCurrWoStatus($woId,$data['order_status']);
					if($schData){
						   $wstModel = new Model_WoScheduleStatus();				       
						   // fetch current status work order
						   $wsDetail = $wstModel->getCurrentWs($woId);
						   foreach($wsDetail as $wsd){
							   $wsUpdate = $wstModel->updateWoSchedule(array('current_status'=>0),$wsd['wssId']);
						   }
						   $wss_data = array();
						   $wss_data['worder_id']=	$woId;
						   $wss_data['schedule_id']= $schData[0]->id;
						   $wss_data['priority_id']= $schData[0]->priority_id;
						   $wss_data['status']= 1;
						   //$wss_data['ckey']= md5(time());
						   $wss_data['current_status']= 1;
						   $wss_data['created_at'] = date('Y-m-d H:i:s');
						   $ws_insert = $wstModel->insertWoSchedule($wss_data);
					}
				 echo 'true';	
				}catch(Exception $e){
					//echo $e->getMessage();
					echo 'false';
					
				}	
		   }
		   exit(0);
	 }
	 
	 public function createworkorderAction(){
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
	     
	     $build_ID = $this->_getParam('bid','');
	     if(empty($build_ID))
			$build_ID = $_COOKIE['build_cookie'];
			else{
			$build_ID = ($build_ID=='all')?'':$build_ID;
			 $set_cookie = setcookie('build_cookie',$build_ID,time() + (86400/24), "/");
		 }
		 $select_build_id =$build_ID;
	     $submit_key = rand(10,1000);
		 $formKey = new Zend_Session_Namespace('formkey');
		 $this->view->select_build_id = $select_build_id;
		 $formKey->submit_key = $submit_key;
		 $this->view->form_key = $submit_key;
	     $this->view->companyListing = $companyListing;
	 }
	 
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
				$this->_redirect('/dashboard/createworkorder');
			}else{
				$formKey->submit_key='';
			}
			
			/**********check form validation **********/
			$form_valid = true;
			if($data['building']=='' || $data['tenant']=='' || $data['create_user']=='' || $data['category']=='' || $data['work_order_request']==''){
				$form_valid = false;
				$smsg->error_message = 'Form is not properly filled.';
				$this->_redirect('/dashboard/createworkorder');
			}
			
			if($form_valid==true){
				try{
					$woModel = new Model_WorkOrder();
					$insertData = array();
					
					$insertData['tenant'] = $data['tenant'];
					$insertData['building'] = $data['building'];
					$insertData['suite_location'] = $data['suite_location'];
					$insertData['suite_location2'] = $data['suite_location'];
					$insertData['create_user'] = $data['create_user'];
					$insertData['date_requested'] = date("Y-m-d",strtotime($data['date_requested']));
					$insertData['time_requested'] = $data['time_requested'];
					//$insertData['priority'] = $data['priority'];
					$insertData['category'] = $data['category'];
					$insertData['internal_work_order'] = 1;
					$insertData['work_order_request'] = $data['work_order_request'];
					$insertData['user_id'] = $this->userId;				
					$woId = $woModel->insertWorkOrder($insertData);
					
					$wpModel = new Model_WorkOrderUpdate();
					$wpData = array();
					$wpData['wo_id'] = $woId;
					$wpData['wo_request'] = '';
					$wpData['internal_note'] = $data['internal_note'];
					$wpData['wo_status'] = 1;
					$wpData['current_update']=1;
					$wpData['created_at'] = date('Y-m-d H:i:s');
					$insertWp = $wpModel->insertWorkOrderUpdate($wpData);
					/*************upload file************/
					if($_FILES['wo_file']['name']!=''){						
							$uploaddir = BASE_PATH.'public/work_order/';
							$uploadfile_name = 'WO-'.time().'-'. basename($_FILES['wo_file']['name']);                        
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
					if($data['building']!=''){
						$lastWoNum = $woModel->getMaxWoNumber($data['building']);
						$update_wo_num = '';
						if($lastWoNum){
							$update_wo_num = $lastWoNum+1;
						}else{
							$update_wo_num = 1001;
						}
						try{
							$update_num = $woModel->updateWorkOrder(array('wo_number'=>$update_wo_num),$woId);
						}catch(Exception $e){
							echo $e->getMessage();
						}
						
					}
					$insertData['woId'] = $woId;
					$priorityMapper = new Model_Priority();
					$categoryMapper = new Model_Category();
					$buildingMapper = new Model_Building();
					$userMapper     = new Model_User();
					$tenantMapper = new Model_Tenant();
					$catDetail = $categoryMapper->getCategoryName($data['category']);
					$priorityDetail = $priorityMapper->getPriorityByCategory($data['category']);
					$buildingDetail = $buildingMapper->getbuildingbyid($data['building']);				
					$tenantDetail     = $tenantMapper->getTenantById($data['tenant']);			
					$tenantInfo = $tenantDetail[0];
					$tenantData       = (array)$tenantInfo;
					$tenantData['tenantId'] = $tenantData ['id'];
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
					$sendmail = $woModel->sendWorkOrderEmail($woId,$tenantData);
					/***********display print work order *********/				
					$this->view->buildingDetail = $buildingDetail[0];
					$this->view->workOrder = $insertData;
					$this->view->cust_id = $this->cust_id;				
					$this->view->tenantInfo = $tenantData;
					$this->view->categoryName = $catDetail[0]['categoryName'];
					$this->view->priorityName  = $priorityName;
					$messsg = 'Work Order Request Saved Successfully.';
					$smsg->success_message = $messsg;
					if(isset($data['save_new']) && $data['save_new']!=''){
						$this->_redirect('/dashboard/createworkorder');
					}else{
						$this->_redirect('/dashboard/workorder');
					}
				}catch(Exception $e){
					$this->view->msg = $e->getMessage();
					$smsg->error_message = 'Error Occurred during save work order';
					$this->_redirect('/dashboard/createworkorder');
				}
			}	
	    }else{
			$this->_redirect('/dashboard/createworkorder');
		}  
	}
	
	 public function selecttenantAction(){
			 //makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			$bid = $this->_getParam('bid','');
			$tnModel = new Model_Tenant();
			$tndetail = $tnModel->getTenantByBuildingId($bid);
			$this->view->tnData = $tndetail;
	 }
	 
	 public function selectcategoryAction(){
			$this->_helper->getHelper('layout')->disableLayout();
			$bid = $this->_getParam('bid','');
			$catModel = new Model_Category();
			$catdetail = $catModel->getBuildingCategoryList($bid);
			$this->view->catData = $catdetail;
	 }
	 
	 public function selecttnuserAction(){
			$this->_helper->getHelper('layout')->disableLayout();
			$tId = $this->_getParam('tId','');
			$tnuserModel = new Model_TenantUser();
			$tnuserdetail = $tnuserModel->getTenantUsers($tId);
			$tnModel = new Model_Tenant();
			$tndetail = $tnModel->getTenantById($tId);
			$this->view->tnuserData = $tnuserdetail;
			$this->view->tnData = $tndetail[0];
	 }
	 
	 public function timezoneAction(){
		    $this->_helper->getHelper('layout')->disableLayout();
			$bid = $this->_getParam('bid','');			
			$this->view->bid = $bid;
	 }
	 
	 
	 public function changecatAction(){
		    $data = $this->getRequest()->getPost();
		    $smsg = new Zend_Session_Namespace('message');
		    $smsg->success_message = '';
		    $smsg->error_message = '';
		   if(isset($data['woId'])){
			   $curr_cat = $data['curr_cat'];
			   $change_cat = $data['change_cat'];
			   $woId = $data['woId'];
			   
			  
			   $woModel = new Model_WorkOrder();
			   try{	
				   $whlModel = new Model_WoHistoryLog();		   
				   $whData= array();
				   $whData['woId']=$data['woId'];
				   $whData['log_type']='category';
				   $whData['current_value']=$curr_cat;
				   $whData['change_value']=$change_cat;
				   $whData['user_id']=$this->userId;
				   $insertWHL = $whlModel->insertHistoryLog($whData);
				   
				   $wData = array();
				   $wData['category'] = $change_cat;
				   
				   $updateWO = $woModel->updateWorkOrder($wData,$data['woId']);
				   $messsg = 'Category Successfully Re-assigned.';
				   $smsg->success_message = $messsg;
				   echo 'true';
		       }catch(Exception $e){
				   $smsg->error_message = 'Error Occurred during re-assign category in work order';
				   echo 'false';
			   }
			   
			   
		   }
		   exit(0);
	 }
		
	
	
}	     
 ?>
