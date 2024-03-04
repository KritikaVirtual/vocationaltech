<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CategoryController
 *
 * @author ivtidai
 */
class CategoryController extends Ve_Controller_Base {
    
    public function init()  {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout'); 
       $this->accessHelper = $this->_helper->access;
       $this->category_location = 13; 
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
        $this->per_page = 5;
    }	
    
   /* public function indexAction() {
        $priorityMapper = new Model_Priority();
        $priorityList = $priorityMapper->getAllPriority();
        
        $priorityMapper = new Model_Priority();
        $priorityDetail = $priorityMapper->getAllPriority();
        
        $categoryMapper = new Model_Category();
        $categoryDetail = $categoryMapper->getAllCategory();        
    
        $buildingMapper=new  Model_Building();
        if($this->cust_id!=0)
         $companyListing = $buildingMapper->getCompanyBuilding($this->cust_id,$this->userId);
         else
           $companyListing = $buildingMapper->getbuilding();
        
        $this->view->priorityDetail = array(
            "list"              => $priorityList,
            "categoryDetail"    => $categoryDetail,
            "buildingList"      => $companyListing,
            "priorityDetail"    => $priorityDetail,
        );
        
        
        
        $this->view->controller = $this;
    } */   
    
    public function indexAction(){
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
		if(empty($build_ID) && isset($_COOKIE['build_cookie']))
		$build_ID = $_COOKIE['build_cookie'];
		else
		 $set_cookie = setcookie('build_cookie',$build_ID,time() + (86400/24), "/");		 
		
		if($companyListing!=''){
			if($build_ID!='')
			$select_build_id = $build_ID;
			 else
			$select_build_id = $companyListing[0]['build_id'];
	   }
		$page=$this->_getParam('page',1);		
		$pageObj=new Ve_Paginator();		
		
		
		/**** fetch building category ****/
		$categoryMapper = new Model_Category();
        $categoryDetail = $categoryMapper->getBuildingCategoryList($select_build_id);
        $category_paginator = $pageObj->fetchPageDataResult($categoryDetail,$page,$this->per_page);
        /*****  fetch building priority ******/
        $priorityMapper = new Model_Priority();
        $priorityDetail = $priorityMapper->getBuildingPriorityList($select_build_id);
        $priority_paginator = $pageObj->fetchPageDataResult($priorityDetail,$page,$this->per_page);
		$this->view->companyListing = $companyListing;
		$this->view->categoryDetail = $category_paginator;
		$this->view->priorityDetail = $priority_paginator;
		$this->view->page = $page;
		$this->view->custID = $this->cust_id;
		
		$this->view->select_build_id = $select_build_id;
		
        $this->view->roleId     = $this->roleId;
        $this->view->acessHelper = $this->accessHelper;
        //to set the access of Category Information
        $this->view->category_location = $this->category_location;

	}
  
    public function getSchedule($pid) {
        $scheduleMapper = new Model_Schedule();
        return $scheduleMapper->getSchedule($pid);
    }
    
    
    public function addpriorityAction() {
        
        $param = $this->getRequest()->getParams();
        $this->_helper->layout()->disableLayout();
        
        
        $priorityMapper = new Model_Priority();
        $id = $priorityMapper->getCerrentId();
        $id = $id[0]['pid'];
        if(empty($id)) {
            $id = 1;
        } else {
            $id += 1;
        }
        $priorityDetail = array();
        if(isset($param['pid'])) {
            $priorityDetail = $priorityMapper->getAllPriority($param['pid']);
        }
        
        $data = $this->getRequest()->getPost();
        if($this->getRequest()->getMethod() == 'POST' && $data['actionType'] != "addNew") {  
            $data = $this->getRequest()->getPost(); 
            $data['data']['created_by'] = $this->userId;
            $data['data']['created_date'] = date("Y-m-d");
            if ( $data['actionType'] == 'insert' ) {
                $res = $priorityMapper->insertPriority($data['data']);                
                $data['data']['pid'] = $res;                
                
            } else if($data['actionType'] == 'edit') {
                $res = $priorityMapper->updatePriority($data['data'], $data['data']['pid']);
            }            
            echo json_encode($data);
            exit(0);
        }        
        $buildingId = "";
        if(isset($data['building_id'])) {
            $buildingId = $data['building_id'];
        }     
        
        $this->view->id = $id;
        $this->view->priorityDetail = $priorityDetail;
        $this->view->building_id = $buildingId;
        
        
    }
    
    public function savepriorityAction(){
		$data = $this->getRequest()->getPost();
		//print_r($data);
		//exit;		
		$show_result = array();
		if($this->getRequest()->getMethod() == 'POST'){
		  $data['created_by'] = $this->userId;
		  $data['created_date'] = date("Y-m-d");
		  try{
			  $priorityMapper = new Model_Priority();
			  $res = $priorityMapper->insertPriority($data);
			  $show_result['status'] = 'success';
			  $show_result['message'] = 'New Priority added successfully.';
		  }catch(Exception $e){
			  $show_result['status']='error';
			  $show_result['message']=$e->getMessage();
		  }
		  echo json_encode($show_result);exit;
		}
		exit(0);
	}
	
	public function editpriorityAction(){
		$data = $this->getRequest()->getPost();
		//print_r($data);
		//exit;		
		$show_result = array();
		if($this->getRequest()->getMethod() == 'POST'){		 
		  try{
			  if($data['pid']!=''){
				  $pid = $data['pid'];
				  unset($data['pid']);
				  $priorityName = $data['priorityName'];
			      $building_id = $data['building_id'];
			      $priorityMapper = new Model_Priority();
                  $priorityData = $priorityMapper->getPrioritByName($priorityName,$building_id,$pid);
                  if($priorityData)
					{
						$data['status']  = 'priority_error';
						$data['message'] = 'Priority Name already in use.';
						echo json_encode($data);exit;
					}
				  $priorityMapper = new Model_Priority();
				  $res = $priorityMapper->updatePriority($data,$pid);
				  $show_result['status'] = 'success';
				  $show_result['message'] = 'Priority has been updated successfully.';
		     }else{
				 $show_result['status'] = 'error';
				 $show_result['message'] = 'Some issues are coming to update the record.';
			 }
		  }catch(Exception $e){
			  $show_result['status']='error';
			  $show_result['message']=$e->getMessage();
		  }
		   echo json_encode($show_result);exit;
		}
		exit(0);
	}
    public function addpriorityscheduleAction() {
		$this->_helper->layout()->disableLayout();
        $scheduleMapper = new Model_Schedule();
        $data = $this->getRequest()->getPost();
        if($this->getRequest()->getMethod() == 'POST' && $data['actionType'] != 'addNew') {            
            $data['data']['created_by'] = $this->userId;
            if ( $data['actionType'] == 'insert' ) {
                $data['data']['created_date'] = date("Y-m-d");
                $res = $scheduleMapper->insertSchedule($data['data']);
            } else if($data['actionType'] == 'edit') {
                $res = $scheduleMapper->updateSchedule($data['data'], $data['data']['id']);
            }
            //echo json_encode($data);
            //exit(0);
            $this->_redirect('/category');
        } else {
            $param = $this->getRequest()->getParams();
            $pid = "";

            $scheduleDetail = array();
            if(isset($param['id'])) {
                $scheduleDetail = $scheduleMapper->getSchedule("", $param['id']);
            }
            if(isset($scheduleDetail[0]['priority_id'])) {
                 $pid = $scheduleDetail[0]['priority_id'];
            } else {
                 $pid = $param['pid'];
            }

            $priorityMapper = new Model_Priority();
            $priorityDetail = $priorityMapper->getAllPriority( $pid);

            $scheduleMapper = new Model_Schedule();
            $id = $scheduleMapper->getCerrentId();
            $id = $id[0]['schedule_id'];
            if(empty($id)) {
                $id = 1;
            } else {
                $id += 1;
            }           
           
            
            $this->view->id = $id;
			$this->view->priorityDetail = $priorityDetail;
			$this->view->scheduleDetail = $scheduleDetail;
        }  
        
    }
    
    
    public function deletepriorityAction() {
        $param = $this->getRequest()->getParams();
        if(isset($param['pid'])) {			
			$priorityId = $param['pid'];
			$catMapper = new Model_Category();
			$categoryData = $catMapper->getPriorityCategory($priorityId);			
			if($categoryData){
				$data['status']  = 'error';
				$data['message'] = 'Due to active categories, System is unable to delete this priority.';
				 echo json_encode($data);exit;
			}
            $priorityMapper = new Model_Priority();
            $resultDel = $priorityMapper->deletePreority($param['pid']);
            if($resultDel)
			 {
				 $data['status']  = 'success';
				 $data['message'] = 'deleted successfully';
				 echo json_encode($data);exit;
			 }else{
				 $data['status']  = 'error';
				 $data['message'] = 'exception occured';
				 echo json_encode($data);exit;
			 }
        }
        exit(0);
    }
    
    public function deletepriorityscheduleAction() {
        $param = $this->getRequest()->getParams();
        if(isset($param['id'])) {
            $scheduleMapper = new Model_Schedule();
            $scheduleMapper->deleteSchedule($param['id']);
        }
        $this->_redirect('/category');
        exit(0);
    }
    
     public function editcategoryAction() {        
        $categoryMapper = new Model_Category(); 
        $this->_helper->layout()->disableLayout();     
        $data = $this->getRequest()->getPost();
            $buildingId = "";
            if(isset($data['building_id'])) {
                $buildingId = $data['building_id'];
            }
            $priorityMapper = new Model_Priority();
            $priorityDetail = $priorityMapper->getAllPriorityByBuildId($buildingId);
            $param = $this->getRequest()->getParams();
            $categoryDetail = array();
            if(isset($data['cid'])) {
                $categoryDetail = $categoryMapper->getAllCategory($data['cid']);
            }      
            
            $companyModel = new Model_Company();
			$nottenant = 1; // this for not listing the tenant user here.
			$users = $companyModel->getUserByBuildingId($buildingId,$nottenant);
		    
		    $egroupModel = new Model_EmailGroup();
		    $groupList = $egroupModel->get_email_group_by_building_id($buildingId);
		    
		    $tenantModel = new Model_Tenant();
		    $tenantList = $tenantModel->getTenantByBuildingId($buildingId);
            
           $this->view->categoryDetail = array(
                    "priorityDetail"  => $priorityDetail,
                    "categoryDetail"  => $categoryDetail,                    
                    "building_id"     => $buildingId,
                    "groupList"     => $groupList,
                    "userList"      => $users,
                    "tenantList"   => $tenantList,
                );
        $this->view->roleId     = $this->roleId;
        $this->view->acessHelper = $this->accessHelper;
        //to set the access of Category Information
        $this->view->category_location = $this->category_location;    
        
    }
    
    public function updatecatfieldAction(){

        $categoryMapper = new Model_Category();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $cid = $this->getRequest()->getPost('pk');
            $key = $this->getRequest()->getPost('name');
            $value = $this->getRequest()->getPost('value');
            $data= array(
                    $key => $value,
                    'updated_date'=> date('Y-m-d H:i:s')
                );
           if($key!='' && !empty($key)){        
               $res = $categoryMapper->updateCategory($data,$cid);
            }
        }
        exit;
    }
    
    public function addcategoryAction() {        
        $categoryMapper = new Model_Category();      
        $data = $this->getRequest()->getPost();
            $buildingId = "";
            if(isset($data['building_id'])) {
                $buildingId = $data['building_id'];
            
            $priorityMapper = new Model_Priority();
            $priorityDetail = $priorityMapper->getAllPriorityByBuildId($buildingId);
            $param = $this->getRequest()->getParams();
            $categoryDetail = array();
            if(isset($param['id'])) {
                $categoryDetail = $categoryMapper->getAllCategory($param['id']);
            }            
            $id = $categoryMapper->getCerrentId();
            $id = $id[0]['cat_id'];
            if(empty($id)) {
                $id = 1;
            } else {
                $id += 1;
            }
             
            
            $companyModel = new Model_Company();
		    $nottenant = 1; // this for not listing the tenant user here.
		    $users = $companyModel->getUserByBuildingId($buildingId,$nottenant);
		    
		    $egroupModel = new Model_EmailGroup();
		    $groupList = $egroupModel->get_email_group_by_building_id($buildingId);
		    $tenantModel = new Model_Tenant();
		    $tenantList = $tenantModel->getTenantByBuildingId($buildingId);
            $category_template = new Zend_View();
            $category_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/category/');
            $category_template->assign('categoryDetail', array(
                    "priorityDetail"  => $priorityDetail,
                    "categoryDetail"  => $categoryDetail,
                    "id"              => $id,
                    "building_id"     => $buildingId,
                    "groupList"     => $groupList,
                    "userList"      => $users,
                    "tenantList"   => $tenantList,
                )
            );
            $bodyText = $category_template->render('addcategory.phtml');
           // print_r($bodyText); die;
            echo $bodyText;
			}else
			echo '<div class="category_block_main add_cate_main_block">
<div class="schedule-form category_block">Building is not assigned.</div></div>';
            exit(0);
        
    }


    public function createcatAction(){
        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);        
        $categoryMapper = new Model_Category();
        $data = $this->getRequest()->getPost();
        $data['createdBy'] = $this->userId;
        $data['created_date'] = date("Y-m-d");
        $res = $categoryMapper->checkname($data['categoryName'], $data['building_id']);

        if($res){
            echo 3;
            exit(0);
        }

        if($categoryMapper->insertCategory($data)){
            echo true;
            exit(0);
        }
        else{
            echo false;
            exit(0);
        }
        
        
    }

    public function editcatAction(){
        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);
        $categoryMapper = new Model_Category();
        $data = $this->getRequest()->getPost();       
        
        /*$res = $categoryMapper->checkname($data['categoryName'], $data['building_id'], $data['cat_id']);

         if($res){
            echo 3;
            exit(0);
        }*/

        if($categoryMapper->updateCategory($data,$data['cat_id'])){
            echo true;
            exit(0);
        }
        else{
            echo false;
            exit(0);
        }
        
        
    }

    public function deletecatAction(){
        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);
        $categoryMapper = new Model_Category();
        $data = $this->getRequest()->getPost();
        
        if($categoryMapper->deleteCategory($data['cat_id'])){
            echo true;
            exit(0);
        }
        else{
            echo false;
            exit(0);
        }        
    }
    
    public function recovercategoryAction(){
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
		else
		 $set_cookie = setcookie('build_cookie',$build_ID,time() + (86400/24), "/");
		
		if($companyListing!=''){
			if($build_ID!='')
			$select_build_id = $build_ID;
			 else
			$select_build_id = $companyListing[0]['build_id'];
	   }
		$page=$this->_getParam('page',1);		
		$pageObj=new Ve_Paginator();
		$categoryMapper = new Model_Category();
		$recCatList = $categoryMapper->getRecoverCategory($select_build_id);
		$recCatList = $pageObj->fetchPageDataResult($recCatList,$page,$this->per_page);
		$this->view->companyListing = $companyListing;
		$this->view->categoryDetail = $recCatList;		
		$this->view->page = $page;
		$this->view->custID = $this->cust_id;
		$this->view->roleId     = $this->roleId;
		$this->view->select_build_id = $select_build_id;
	}
	
	public function activatecategoryAction(){
		$cId=$this->_getParam('cId',0);
		$msg = '';
		if($cId!=0){
			$categoryMapper = new Model_Category();
			try{
				$activateCat = $categoryMapper->activateCategory($cId);
				$msg = 'Category recover successfully!!';
			}catch(Exception $e){
				$msg = 'Some error occurred during recover category!!';
			}
		}else
		 $msg = 'Invalid Category';
		 $activate_category = new Zend_Session_Namespace('activate_category');
		 $activate_category->msg = $msg;
		 
		$this->_redirect('/category/recovercategory');
        exit(0);
	}
    
    function getPriorityName($priority, $id) {
        if($id == -1) {
            return "Not Assigned";
        }
        if($id == 0) {
            return "Default";
        }
        
        foreach($priority as $key => $data) {
            if($data['pid'] == $id) {
                return $data['priorityName'];
            }
        }
    }
    
    public function deletecategoryAction() {
        $param = $this->getRequest()->getParams();
        if(isset($param['id'])) {
            $categoryMapper = new Model_Category();
            $categoryMapper->deleteCategory($param['id']);
        }
        $this->_redirect('/category');
        exit(0);
    }
    
    public function showprioritylistAction(){
		$param = $this->getRequest()->getParams();
		if(isset($param['buildingId'])){
			 /*****  fetch building priority ******/
		  $page = isset($param['page'])?$param['page']:1;
		  $order = $param['order'];		
		   $pageObj=new Ve_Paginator();
		  $buildId =  $param['buildingId'];
          $priorityMapper = new Model_Priority();
          $dir = ($order!='default')?$order:'';
          $priorityDetail = $priorityMapper->getBuildingPriorityList($buildId,$dir);
          $priority_paginator = $pageObj->fetchPageDataResult($priorityDetail,$page,$this->per_page);
			$category_template = new Zend_View();
            $category_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/category/');
            $category_template->assign('priorityDetail', $priority_paginator);
            $category_template->assign('page', $page);
            $category_template->assign('order', $order);
            $category_template->assign('select_build_id', $buildId);
            $bodyText = $category_template->render('prioritylist.phtml');
            $paging_html='';
             if(count($priorityDetail)>0 && !empty($priorityDetail)){
			$paging_html .= '<tr><td colspan="5">';
			$paging_html .= Zend_View_Helper_PaginationControl::paginationControl($priority_paginator, 'Sliding', 'priority_pagination.phtml'); 
			 $paging_html .= '</td></tr>';
			 }
            //echo $bodyText;
            $bodyText = $bodyText.''.$paging_html;
            $data = array('status'=>'success','content'=>$bodyText);
            echo json_encode($data);exit;
		}
		exit(0);
	}
	
	public function showpriorityschedulelistAction(){
		$param = $this->getRequest()->getParams();
		$this->_helper->layout()->disableLayout();
		if(isset($param['pid'])){
			 /*****  fetch priority schedule******/
		  $pid =  $param['pid'];
		  $order = $param['order'];
		  $dir = ($order!='default')?$order:'';
          $scheduleMapper = new Model_Schedule();
          $scheduleDetail = $scheduleMapper->getSchedule($pid,'',$dir);
          $priorityMapper = new Model_Priority();
          $priorityData = $priorityMapper->getPriorityName($pid);
          
          $ssMapper = new Model_ScheduleStatus();
          $status_list = $ssMapper->getScheduleStatus();
          
          $wdMapper = new Model_WeekDays();
          $wd_list = $wdMapper->getWeekDays();
          
          $lengthMapper = new Model_Length();
          $length_list = $lengthMapper->getLength();
          //print_r($priorityData);
          $priData = $priorityData[0];
          $this->view->roleId     = $this->roleId;
		  $this->view->scheduleDetail = $scheduleDetail;
          $this->view->priority_id = $pid;
          $this->view->status_list = $status_list;
          $this->view->order = $order;
          $this->view->length_list = $length_list;
          $this->view->wd_list = $wd_list; 
          $this->view->priorityName = $priData['priorityName'];
          
          $this->view->acessHelper = $this->accessHelper;
          $this->view->category_location = $this->category_location;       
           
		}
		
	}


    public function showcategorylistAction(){		
        $param = $this->getRequest()->getParams();
        $roleId = $this->roleId;
        if(isset($param['buildingId'])){
             /*****  fetch building priority ******/
          $buildId =  $param['buildingId'];

          /*****  fetch building priority ******/
          $page = isset($param['page'])?$param['page']:1;	
		   $pageObj=new Ve_Paginator();
        $priorityMapper = new Model_Priority();
        $priorityDetail = $priorityMapper->getBuildingPriorityList($buildId);

          $categoryMapper = new Model_Category();
          $categoryDetail = $categoryMapper->getBuildingCategoryList($buildId);
          $category_paginator = $pageObj->fetchPageDataResult($categoryDetail,$page,$this->per_page);
            $category_template = new Zend_View();
            $category_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/category/');
            $category_template->assign('categoryDetail', $category_paginator);
            $category_template->assign('select_build_id', $buildId);
            $category_template->assign('roleId', $roleId);
            $category_template->assign('page', $page);
            $category_template->assign('priorityDetail', $priorityDetail);
            $bodyText = $category_template->render('categorylist.phtml');

          
            //echo $bodyText;
             $paging_html='';
             if(count($categoryDetail)>0 && !empty($categoryDetail)){
			$paging_html .= '<tr><td colspan="4">';
			$paging_html .= Zend_View_Helper_PaginationControl::paginationControl($category_paginator, 'Sliding', 'category_pagination.phtml'); 
			 $paging_html .= '</td></tr>';
			 }
            //echo $bodyText;
            $bodyText = $bodyText.''.$paging_html;
            $data = array('status'=>'success','content'=>$bodyText);
            echo json_encode($data);exit;
        }
        exit(0);
    }
    public function savescheduleAction(){
		$data = $this->getRequest()->getPost();
		//print_r($data);
		//exit;		
		$show_result = array();
		if($this->getRequest()->getMethod() == 'POST'){
		  $data['created_by'] = $this->userId;
		  $data['created_date'] = date("Y-m-d");
		  try{
			  $scheduleMapper = new Model_Schedule();
			  $res = $scheduleMapper->insertSchedule($data);
			  $show_result['status'] = 'success';
			  $show_result['message'] = 'New Priority Schedule added successfully.';
		  }catch(Exception $e){
			  $show_result['status']='error';
			  $show_result['message']=$e->getMessage();
		  }
		  echo json_encode($show_result);exit;
		}
		exit(0);
	}
	
	public function editscheduleAction(){
		$data = $this->getRequest()->getPost();
		//print_r($data);
		//exit;		
		$show_result = array();
		if($this->getRequest()->getMethod() == 'POST'){		 
		  try{
			  if($data['id']!=''){
				  $sid = $data['id'];
				  unset($data['id']);
				  $scheduleMapper = new Model_Schedule();
				  $res = $scheduleMapper->updateSchedule($data, $sid);
				  $show_result['status'] = 'success';
				  $show_result['message'] = 'Schedule has been updated successfully.';
		     }else{
				 $show_result['status'] = 'error';
				 $show_result['message'] = 'Some issues are coming to update the record.';
			 }
		  }catch(Exception $e){
			  $show_result['status']='error';
			  $show_result['message']=$e->getMessage();
		  }
		   echo json_encode($show_result);exit;
		}
		exit(0);
	}
	
	public function deletescheduleAction() {
        $param = $this->getRequest()->getParams();
        if(isset($param['id'])) {
            $scheduleMapper = new Model_Schedule();
            $resultDel= $scheduleMapper->deleteSchedule($param['id']);
            if($resultDel)
			 {
				 $data['status']  = 'success';
				 $data['message'] = 'deleted successfully';
				 echo json_encode($data);exit;
			 }else{
				 $data['status']  = 'error';
				 $data['message'] = 'exception occured';
				 echo json_encode($data);exit;
			 }
        }
        exit(0);
    }
    
    public function checkpriorityAction(){
		 $this->_helper->layout()->disableLayout();
		 $this->_helper->viewRenderer->setNoRender(true);
		 if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			$param = $this->getRequest()->getParams();
			$priorityName = $param['priorityName'];
			$building_id = $param['building_id'];
			$priorityMapper = new Model_Priority();
            $priorityData = $priorityMapper->getPrioritByName($priorityName,$building_id);            			 
			 if($priorityData)
			    echo 'true';
			  else
			    echo 'false';
		 }
		 exit(0);
	}
}

?>
