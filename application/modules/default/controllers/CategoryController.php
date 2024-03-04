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

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
        $this->accessHelper = $this->_helper->access;
        $this->category_location = 13;
        $this->crecovery_location = 20;
    }

    // Call befor any action and check is user login or not
    public function preDispatch() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/index');
        }

        $level = (Zend_Auth::getInstance()->getStorage()->read()) ? Zend_Auth::getInstance()->getStorage()->read()->role_id : '';
        $this->userId = Zend_Auth::getInstance()->getStorage()->read()->uid;
        $this->roleId = Zend_Auth::getInstance()->getStorage()->read()->role_id;
        $this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
        $this->per_page = 15;
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

    public function indexAction() {
        $companyListing = '';
        $buildingMapper = new Model_Building();
        //print_r($_POST);
        $search_array = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $search_array['search_by'] = $data['search_by'];
            $search_array['search_value'] = $data['search_value'];
            $this->view->search = $search_array;
        }
        if ($this->roleId == '9') {
            $companyListing = $buildingMapper->getCompanyBuilding($this->cust_id);
        } else {
            $user_build_mod = new Model_UserBuildingModule();

            $buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
            if ($buildinglists) {
                $build_id_array = array();
                foreach ($buildinglists as $buildlist)
                    $build_id_array[] = $buildlist['building_id'];
                $companyListing = $buildingMapper->getBuildingList($build_id_array);
            }
        }
        foreach ($companyListing as $cl) {
            $buildIds[] = $cl['build_id'];
        }
        $build_ID = $this->_getParam('bid', '');
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");

        if ($companyListing != '') {
            if ($build_ID != '')
                $select_build_id = $build_ID;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }
        
        $page = $this->_getParam('page', 1);
        $pageObj = new Ve_Paginator();
        $show = $this->_getParam('show', '');
        $type = $this->_getParam('type', '');
        
        if($show==""){
           $show=10; 
        }
        $search_array = array_map("addslashes", $search_array);
        $search_array = array_map("addslashes", $search_array);
        $search_array = array_map("addslashes", $search_array);
        
        $cn_order = $this->_getParam('cn_order', 'ASC');
        $prd_order = $this->_getParam('prd_order', 'ASC');
        /*         * ** fetch building category *** */
        $categoryMapper = new Model_Category();
        if(!empty($search_array)){
             //$tenantList = $tenant->gettenantsearchresult($build_ID, $search_array);
            $categoryDetail = $categoryMapper->getBuildingCategoryList($select_build_id, $cn_order, $search_array); 
        }else{
            $categoryDetail = $categoryMapper->getBuildingCategoryList($select_build_id, $cn_order); 
        }
        if($show=='all'){
            $category_paginator = $pageObj->fetchPageDataResult($categoryDetail, $page, $show);     
        }else{
            $category_paginator = $pageObj->fetchPageDataResult($categoryDetail, $page, $show);      
        }
       // print_r($category_paginator);
        //die;
        
        /*         * ***  fetch building priority ***** */
        $priorityMapper = new Model_Priority();
        
        $priorityDetail = $priorityMapper->getBuildingPriorityList($select_build_id, $prd_order);
        
        
        if($show=='all'){
            $priority_paginator = $pageObj->fetchPageDataResult($priorityDetail, $page, $show);     
        }else{
            $priority_paginator = $pageObj->fetchPageDataResult($priorityDetail, $page, $show);      
        }
        
        //$priority_paginator = $pageObj->fetchPageDataResult($priorityDetail, $page, $this->per_page);
        
        $this->view->show=$show;
        $this->view->priorityDetail_imp = $priorityDetail;
        $this->view->companyListing = $companyListing;
        $this->view->categoryDetail = $category_paginator;
        $this->view->priorityDetail = $priority_paginator;
        $this->view->page = $page;
        $this->view->custID = $this->cust_id;
        $this->view->cn_order = $cn_order;
        $this->view->prd_order = $prd_order;
        $this->view->select_build_id = $select_build_id;
        $this->view->roleId = $this->roleId;
        $this->view->acessHelper = $this->accessHelper;
        //to set the access of Category Information
        $this->view->category_location = $this->category_location;
        $this->view->userId = $this->userId;
        $this->view->type = $type;
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
        if (empty($id)) {
            $id = 1;
        } else {
            $id += 1;
        }
        $priorityDetail = array();
        if (isset($param['pid'])) {
            $priorityDetail = $priorityMapper->getAllPriority($param['pid']);
        }

        $data = $this->getRequest()->getPost();
        if ($this->getRequest()->getMethod() == 'POST' && $data['actionType'] != "addNew") {
            $data = $this->getRequest()->getPost();
            $data['data']['created_by'] = $this->userId;
            $data['data']['created_date'] = date("Y-m-d");
            if ($data['actionType'] == 'insert') {
                $res = $priorityMapper->insertPriority($data['data']);
                $data['data']['pid'] = $res;
            } else if ($data['actionType'] == 'edit') {
                $res = $priorityMapper->updatePriority($data['data'], $data['data']['pid']);
            }
            echo json_encode($data);
            exit(0);
        }
        $buildingId = "";
        if (isset($data['building_id'])) {
            $buildingId = $data['building_id'];
        }

        $this->view->id = $id;
        $this->view->priorityDetail = $priorityDetail;
        $this->view->building_id = $buildingId;
    }

    public function savepriorityAction() {
        $data = $this->getRequest()->getPost();
        //print_r($data);
        //exit;		
        $show_result = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data['created_by'] = $this->userId;
            $data['created_date'] = date("Y-m-d");
            try {
                $priorityMapper = new Model_Priority();
                $res = $priorityMapper->insertPriority($data);
                $show_result['status'] = 'success';
                $show_result['message'] = 'New Priority added successfully.';
            } catch (Exception $e) {
                $show_result['status'] = 'error';
                $show_result['message'] = $e->getMessage();
            }
            echo json_encode($show_result);
            exit;
        }
        exit(0);
    }

    public function editpriorityAction() {
        $data = $this->getRequest()->getPost();
        //print_r($data);
        //exit;		
        $show_result = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            try {
                if ($data['pid'] != '') {
                    $pid = $data['pid'];
                    unset($data['pid']);
                    $priorityName = $data['priorityName'];
                    $building_id = $data['building_id'];
                    $priorityMapper = new Model_Priority();
                    $priorityData = $priorityMapper->getPrioritByName($priorityName, $building_id, $pid);
                    if ($priorityData) {
                        $data['status'] = 'priority_error';
                        $data['message'] = 'Priority Name already in use.';
                        echo json_encode($data);
                        exit;
                    }
                    $priorityMapper = new Model_Priority();
                    $res = $priorityMapper->updatePriority($data, $pid);
                    $scheduleMapper = new Model_Schedule();
                    $scheduledata = array('status' => $data['status'], 'email_status' => $data['email_status']);
                    $data1 = $scheduleMapper->changePrioritySchedule($pid, $scheduledata);

                    $show_result['status'] = 'success';
                    $show_result['message'] = 'Priority has been updated successfully.';
                } else {
                    $show_result['status'] = 'error';
                    $show_result['message'] = 'Some issues are coming to update the record.';
                }
            } catch (Exception $e) {
                $show_result['status'] = 'error';
                $show_result['message'] = $e->getMessage();
            }
            echo json_encode($show_result);
            exit;
        }
        exit(0);
    }

    public function addpriorityscheduleAction() {
        $this->_helper->layout()->disableLayout();
        $scheduleMapper = new Model_Schedule();
        $data = $this->getRequest()->getPost();
        if ($this->getRequest()->getMethod() == 'POST' && $data['actionType'] != 'addNew') {
            $data['data']['created_by'] = $this->userId;
            if ($data['actionType'] == 'insert') {
                $data['data']['created_date'] = date("Y-m-d");
                $res = $scheduleMapper->insertSchedule($data['data']);
            } else if ($data['actionType'] == 'edit') {
                $res = $scheduleMapper->updateSchedule($data['data'], $data['data']['id']);
            }
            //echo json_encode($data);
            //exit(0);
            $this->_redirect('/category');
        } else {
            $param = $this->getRequest()->getParams();
            $pid = "";

            $scheduleDetail = array();
            if (isset($param['id'])) {
                $scheduleDetail = $scheduleMapper->getSchedule("", $param['id']);
            }
            if (isset($scheduleDetail[0]['priority_id'])) {
                $pid = $scheduleDetail[0]['priority_id'];
            } else {
                $pid = $param['pid'];
            }

            $priorityMapper = new Model_Priority();
            $priorityDetail = $priorityMapper->getAllPriority($pid);

            $scheduleMapper = new Model_Schedule();
            $id = $scheduleMapper->getCerrentId();
            $id = $id[0]['schedule_id'];
            if (empty($id)) {
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
        if (isset($param['pid'])) {
            $priorityId = $param['pid'];
            $catMapper = new Model_Category();
            $categoryData = $catMapper->getPriorityCategory($priorityId);
            if ($categoryData) {
                $data['status'] = 'error';
                $data['message'] = 'Due to active categories, System is unable to delete this priority.';
                echo json_encode($data);
                exit;
            }
            $priorityMapper = new Model_Priority();
            $resultDel = $priorityMapper->deletePreority($param['pid']);
            if ($resultDel) {
                $data['status'] = 'success';
                $data['message'] = 'deleted successfully';
                echo json_encode($data);
                exit;
            } else {
                $data['status'] = 'error';
                $data['message'] = 'exception occured';
                echo json_encode($data);
                exit;
            }
        }
        exit(0);
    }

    public function deletepriorityscheduleAction() {
        $param = $this->getRequest()->getParams();
        if (isset($param['id'])) {
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
        if (isset($data['building_id'])) {
            $buildingId = $data['building_id'];
        }
        $priorityMapper = new Model_Priority();
        $priorityDetail = $priorityMapper->getAllPriorityByBuildId($buildingId);
        $param = $this->getRequest()->getParams();
        $categoryDetail = array();
        if (isset($data['cid'])) {
            $categoryDetail = $categoryMapper->getAllCategory($data['cid']);
        }

        $companyModel = new Model_Company();
        $nottenant = 1; // this for not listing the tenant user here.
        $users = $companyModel->getUserByBuildingId($buildingId, $nottenant);

        $egroupModel = new Model_EmailGroup();
        $groupList = $egroupModel->get_email_group_by_building_id($buildingId, 0);

        $tenantModel = new Model_Tenant();
        $tenantList = $tenantModel->getTenantByBuildingId($buildingId);

        $this->view->categoryDetail = array(
            "priorityDetail" => $priorityDetail,
            "categoryDetail" => $categoryDetail,
            "building_id" => $buildingId,
            "groupList" => $groupList,
            "userList" => $users,
            "tenantList" => $tenantList,
        );
        $this->view->roleId = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->acessHelper = $this->accessHelper;
        //to set the access of Category Information
        $this->view->category_location = $this->category_location;
    }

    public function updatecatfieldAction() {

        $categoryMapper = new Model_Category();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $cid = $this->getRequest()->getPost('pk');
            $key = $this->getRequest()->getPost('name');
            $value = $this->getRequest()->getPost('value');
            $data = array(
                $key => $value,
                'updated_date' => date('Y-m-d H:i:s')
            );
            if ($key != '' && !empty($key)) {
                $res = $categoryMapper->updateCategory($data, $cid);
            }
        }
        exit;
    }

    public function addcategoryAction() {
        $categoryMapper = new Model_Category();
        $data = $this->getRequest()->getPost();
        $buildingId = "";
        if (isset($data['building_id'])) {
            $buildingId = $data['building_id'];

            $priorityMapper = new Model_Priority();
            $priorityDetail = $priorityMapper->getAllPriorityByBuildId($buildingId);
            $param = $this->getRequest()->getParams();
            $categoryDetail = array();
            if (isset($param['id'])) {
                $categoryDetail = $categoryMapper->getAllCategory($param['id']);
            }
            $id = $categoryMapper->getCerrentId();
            $id = $id[0]['cat_id'];
            if (empty($id)) {
                $id = 1;
            } else {
                $id += 1;
            }


            $companyModel = new Model_Company();
            $nottenant = 1; // this for not listing the tenant user here.
            $users = $companyModel->getUserByBuildingId($buildingId, $nottenant);

            $egroupModel = new Model_EmailGroup();
            $groupList = $egroupModel->get_email_group_by_building_id($buildingId, 0);
            $tenantModel = new Model_Tenant();
            $tenantList = $tenantModel->getTenantByBuildingId($buildingId);
            $category_template = new Zend_View();
            $category_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/category/');
            $category_template->assign('categoryDetail', array(
                "priorityDetail" => $priorityDetail,
                "categoryDetail" => $categoryDetail,
                "id" => $id,
                "building_id" => $buildingId,
                "groupList" => $groupList,
                "userList" => $users,
                "tenantList" => $tenantList,
                    )
            );
            $bodyText = $category_template->render('addcategory.phtml');
            // print_r($bodyText); die;
            echo $bodyText;
        } else
            echo '<div class="category_block_main add_cate_main_block">
<div class="schedule-form category_block">Building is not assigned.</div></div>';
        exit(0);
    }

    public function createcatAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $categoryMapper = new Model_Category();
        $data = $this->getRequest()->getPost();
        $data['createdBy'] = $this->userId;
        $data['created_date'] = date("Y-m-d");

        $res = $categoryMapper->checkname($data['categoryName'], $data['building_id']);

        if ($res) {
            echo 3;
            exit(0);
        }

        if ($categoryMapper->insertCategory($data)) {
            echo true;
            exit(0);
        } else {
            echo false;
            exit(0);
        }
    }

    public function editcatAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $categoryMapper = new Model_Category();
        $data = $this->getRequest()->getPost();

        /* $res = $categoryMapper->checkname($data['categoryName'], $data['building_id'], $data['cat_id']);

          if($res){
          echo 3;
          exit(0);
          } */

        if ($categoryMapper->updateCategory($data, $data['cat_id'])) {
            echo true;
            exit(0);
        } else {
            echo false;
            exit(0);
        }
    }

    public function deletecatAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $categoryMapper = new Model_Category();
        $data = $this->getRequest()->getPost();

        if ($categoryMapper->deleteCategory($data['cat_id'])) {
            echo true;
            exit(0);
        } else {
            echo false;
            exit(0);
        }
    }

    public function recovercategoryAction() {
        $companyListing = '';
        $buildingMapper = new Model_Building();
        if ($this->roleId == '9') {
            $companyListing = $buildingMapper->getCompanyBuilding($this->cust_id);
        } else {
            $user_build_mod = new Model_UserBuildingModule();

            $buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
            if ($buildinglists) {
                $build_id_array = array();
                foreach ($buildinglists as $buildlist)
                    $build_id_array[] = $buildlist['building_id'];
                $companyListing = $buildingMapper->getBuildingList($build_id_array);
            }
        }
        $build_ID = $this->_getParam('bid', '');
        $buildIds = array();
        foreach ($companyListing as $cl) {
            $buildIds[] = $cl['build_id'];
        }
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");

        if ($companyListing != '') {
            if ($build_ID != '')
                $select_build_id = $build_ID;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }
        $page = $this->_getParam('page', 1);
        $pageObj = new Ve_Paginator();
        $categoryMapper = new Model_Category();
        $recCatList = $categoryMapper->getRecoverCategory($select_build_id);
        $recCatList = $pageObj->fetchPageDataResult($recCatList, $page, $this->per_page);
        $this->view->companyListing = $companyListing;
        $this->view->categoryDetail = $recCatList;
        $this->view->page = $page;
        $this->view->custID = $this->cust_id;
        $this->view->roleId = $this->roleId;
        $this->view->select_build_id = $select_build_id;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->crecovery_location = $this->crecovery_location;
        $this->view->userId = $this->userId;
    }

    public function activatecategoryAction() {
        $cId = $this->_getParam('cId', 0);
        $msg = '';
        if ($cId != 0) {
            $categoryMapper = new Model_Category();
            try {
                $activateCat = $categoryMapper->activateCategory($cId);
                $msg = 'Category recover successfully!!';
            } catch (Exception $e) {
                $msg = 'Some error occurred during recover category!!';
            }
        } else
            $msg = 'Invalid Category';
        $activate_category = new Zend_Session_Namespace('activate_category');
        $activate_category->msg = $msg;

        $this->_redirect('/category/recovercategory');
        exit(0);
    }

    function getPriorityName($priority, $id) {
        if ($id == -1) {
            return "Not Assigned";
        }
        if ($id == 0) {
            return "Default";
        }

        foreach ($priority as $key => $data) {
            if ($data['pid'] == $id) {
                return $data['priorityName'];
            }
        }
    }

    public function deletecategoryAction() {
        $param = $this->getRequest()->getParams();
        if (isset($param['id'])) {
            $categoryMapper = new Model_Category();
            $categoryMapper->deleteCategory($param['id']);
        }
        $this->_redirect('/category');
        exit(0);
    }

    public function showprioritylistAction() {
        $param = $this->getRequest()->getParams();
        if (isset($param['buildingId'])) {
            /*             * ***  fetch building priority ***** */
            $page = isset($param['page']) ? $param['page'] : 1;
            $order = $param['order'];
            $pageObj = new Ve_Paginator();
            $buildId = $param['buildingId'];
            $priorityMapper = new Model_Priority();
            $dir = ($order != 'default') ? $order : '';
            $priorityDetail = $priorityMapper->getBuildingPriorityList($buildId, $dir);
            $priority_paginator = $pageObj->fetchPageDataResult($priorityDetail, $page, $this->per_page);
            $category_template = new Zend_View();
            $category_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/category/');
            $category_template->assign('priorityDetail', $priority_paginator);
            $category_template->assign('page', $page);
            $category_template->assign('order', $order);
            $category_template->assign('select_build_id', $buildId);
            $bodyText = $category_template->render('prioritylist.phtml');
            $paging_html = '';
            if (count($priorityDetail) > 0 && !empty($priorityDetail)) {
                $paging_html .= '<tr><td colspan="5">';
                $paging_html .= Zend_View_Helper_PaginationControl::setDefaultViewPartial($priority_paginator, 'Sliding', 'priority_pagination.phtml');
                $paging_html .= '</td></tr>';
            }
            //echo $bodyText;
            $bodyText = $bodyText . '' . $paging_html;
            $data = array('status' => 'success', 'content' => $bodyText);
            echo json_encode($data);
            exit;
        }
        exit(0);
    }

    public function showpriorityschedulelistAction() {
        $param = $this->getRequest()->getParams();
        $this->_helper->layout()->disableLayout();
        if (isset($param['pid'])) {
            /*             * ***  fetch priority schedule***** */
            $pid = $param['pid'];
            $order = $param['order'];
            $dir = ($order != 'default') ? $order : '';
            $scheduleMapper = new Model_Schedule();
            $scheduleDetail = $scheduleMapper->getSchedule($pid, '', $dir);
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
            $this->view->roleId = $this->roleId;
            $this->view->userId = $this->userId;
            $this->view->scheduleDetail = $scheduleDetail;
            $this->view->priority_id = $pid;
            $this->view->status_list = $status_list;
            $this->view->order = $order;
            $this->view->length_list = $length_list;
            $this->view->wd_list = $wd_list;
            $this->view->priorityName = $priData['priorityName'];
            $this->view->priorityStatus = $priData['status'];
            $this->view->email_status = $priData['email_status'];

            $this->view->acessHelper = $this->accessHelper;
            $this->view->category_location = $this->category_location;
        }
    }

    public function showcategorylistAction() {
        $param = $this->getRequest()->getParams();
        $roleId = $this->roleId;
        $userId = $this->userId;
        if (isset($param['buildingId'])) {
            /*             * ***  fetch building priority ***** */
            $buildId = $param['buildingId'];

            /*             * ***  fetch building priority ***** */
            $page = isset($param['page']) ? $param['page'] : 1;
            $pageObj = new Ve_Paginator();
            $priorityMapper = new Model_Priority();
            $priorityDetail = $priorityMapper->getBuildingPriorityList($buildId);
            $cn_order = isset($param['cn_order']) ? $param['cn_order'] : 'ASC';
            $categoryMapper = new Model_Category();
            $categoryDetail = $categoryMapper->getBuildingCategoryList($buildId, $cn_order);
            $category_paginator = $pageObj->fetchPageDataResult($categoryDetail, $page, $this->per_page);
            $category_template = new Zend_View();
            $category_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/category/');
            $category_template->assign('categoryDetail', $category_paginator);
            $category_template->assign('select_build_id', $buildId);
            $category_template->assign('roleId', $roleId);
            $category_template->assign('userId', $userId);
            $category_template->assign('page', $page);
            $category_template->assign('cn_order', $cn_order);
            $category_template->assign('priorityDetail', $priorityDetail);
            $bodyText = $category_template->render('categorylist.phtml');


            //echo $bodyText;
            $paging_html = '';
            if (count($categoryDetail) > 0 && !empty($categoryDetail)) {
                $paging_html .= '<tr><td colspan="4">';
                $paging_html .= Zend_View_Helper_PaginationControl::paginationControl($category_paginator, 'Sliding', 'category_pagination.phtml');
                $paging_html .= '</td></tr>';
            }
            //echo $bodyText;
            $bodyText = $bodyText . '' . $paging_html;
            $data = array('status' => 'success', 'content' => $bodyText);
            echo json_encode($data);
            exit;
        }
        exit(0);
    }

    public function savescheduleAction() {
        $data = $this->getRequest()->getPost();
        //print_r($data);
        //exit;		
        $show_result = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data['created_by'] = $this->userId;
            $data['created_date'] = date("Y-m-d");
            try {
                $scheduleMapper = new Model_Schedule();
                $res = $scheduleMapper->insertSchedule($data);
                $show_result['status'] = 'success';
                $show_result['message'] = 'New Priority Schedule added successfully.';
            } catch (Exception $e) {
                $show_result['status'] = 'error';
                $show_result['message'] = $e->getMessage();
            }
            echo json_encode($show_result);
            exit;
        }
        exit(0);
    }

    public function editscheduleAction() {
        $data = $this->getRequest()->getPost();
        //print_r($data);
        //exit;		
        $show_result = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            try {
                if ($data['id'] != '') {
                    $sid = $data['id'];
                    unset($data['id']);
                    $scheduleMapper = new Model_Schedule();
                    $res = $scheduleMapper->updateSchedule($data, $sid);
                    $show_result['status'] = 'success';
                    $show_result['message'] = 'Schedule has been updated successfully.';
                } else {
                    $show_result['status'] = 'error';
                    $show_result['message'] = 'Some issues are coming to update the record.';
                }
            } catch (Exception $e) {
                $show_result['status'] = 'error';
                $show_result['message'] = $e->getMessage();
            }
            echo json_encode($show_result);
            exit;
        }
        exit(0);
    }

    public function deletescheduleAction() {
        $param = $this->getRequest()->getParams();
        if (isset($param['id'])) {
            $scheduleMapper = new Model_Schedule();
            $resultDel = $scheduleMapper->deleteSchedule($param['id']);
            if ($resultDel) {
                $data['status'] = 'success';
                $data['message'] = 'deleted successfully';
                echo json_encode($data);
                exit;
            } else {
                $data['status'] = 'error';
                $data['message'] = 'exception occured';
                echo json_encode($data);
                exit;
            }
        }
        exit(0);
    }

    public function checkpriorityAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $param = $this->getRequest()->getParams();
            $priorityName = $param['priorityName'];
            $building_id = $param['building_id'];
            $priorityMapper = new Model_Priority();
            $priorityData = $priorityMapper->getPrioritByName($priorityName, $building_id);
            if ($priorityData)
                echo 'true';
            else
                echo 'false';
        }
        exit(0);
    }

    public function iscategorydeletableAction() {
        $data = $this->getRequest()->getPost();

        if ($data['cid']) {
            $category_model = new Model_Category();
            $category_data = $category_model->isCategoryDeletable($data['cid']);
            echo json_encode($category_data);
            exit;
        }
    }

    public function isprioritydeletableAction() {
        $data = $this->getRequest()->getPost();

        if ($data['pid']) {
            $category_model = new Model_Category();
            $category_data = $category_model->isPriorityDeletable($data['pid']);
            echo json_encode($category_data);
            exit;
        }
    }

    public function isscheduledeletableAction() {
        $data = $this->getRequest()->getPost();

        if ($data['sid']) {
            $category_model = new Model_Category();
            $category_data = $category_model->isScheduleDeletable($data['sid']);
            echo json_encode($category_data);
            exit;
        }
    }

    public function priorityformAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->getRequest()->getParams();
        $building_id = $param['building_id'];
        $this->view->select_build_id = $building_id;
    }

    public function createprioritytemplateAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $build_ID = $this->_getParam('bid', '');
        $this->view->build_id = $build_ID;
        $this->view->cust_id = $this->cust_id;
    }

    public function saveprioritytemplateAction() {
        $data = $this->getRequest()->getPost();
        $show_result = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            try {
                $priorityMapper = new Model_Priority();
                $scheduleMapper = new Model_Schedule();
                $prioritydata = $priorityMapper->getAllPriority($data['pid']);
                if ($data['over_write_priority'] == 0) {
                    $insertdata = $prioritydata[0];
                    $insertdatatemplate['priorityName'] = $data['new_priority_name'];
                    $insertdatatemplate['building_id'] = $data['building_id'];
                    $insertdatatemplate['priorityDescription'] = $insertdata['priorityDescription'];
                    $insertdatatemplate['status'] = $insertdata['status'];
                    $insertdatatemplate['email_status'] = $insertdata['email_status'];
                    $insertdatatemplate['import_template'] = 1;
                    $insertdatatemplate['created_by'] = $this->userId;
                    $insertdatatemplate['created_date'] = date("Y-m-d");
                    $res = $priorityMapper->insertPriority($insertdatatemplate);
                    $scheduleData = $scheduleMapper->getSchedule($data['pid']);
                    foreach ($scheduleData as $value) {
                        $value['priority_id'] = $res;
                        unset($value['id']);
                        $res1 = $scheduleMapper->insertSchedule($value);
                    }
                    $show_result['status'] = 'success';
                    $show_result['message'] = 'New Priority added successfully.';
                } else {
                    $insertdata = $prioritydata[0];
                    $insertdata['building_id'] = $data['building_id'];
                    $insertdata['priorityName'] = $data['existing_priority_name'];
                    $insertdata['import_template'] = 1;
                    $insertdata['global_template'] = 0;
                    $res = $priorityMapper->updatePriority($insertdata, $data['existing_priority']);
                    $scheduleData = $scheduleMapper->getSchedule($data['pid']);
                    $deleteschedule = $scheduleMapper->deletePrioritySchedule($data['existing_priority']);
                    foreach ($scheduleData as $value) {
                        $value['priority_id'] = $data['existing_priority'];
                        unset($value['id']);
                        $res1 = $scheduleMapper->insertSchedule($value);
                    }
                }
                $show_result['status'] = 'success';
                $show_result['message'] = 'New Priority added successfully.';
            } catch (Exception $e) {
                $show_result['status'] = 'error';
                $show_result['message'] = $e->getMessage();
            }
            echo json_encode($show_result);
            exit;
        }
        exit(0);
    }

    public function showeditpriorityAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            if ($data['pid'] != '') {
                try {
                    $priorityMapper = new Model_Priority();
                    $priorityDetails = $priorityMapper->getAllPriority($data['pid']);
                    $this->view->priorityDetails = $priorityDetails[0];
                } catch (Exception $e) {
                    $show_result['status'] = 'error';
                    $show_result['message'] = $e->getMessage();
                }
            }
        }
    }

    public function showeditscheduleAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            try {
                $scheduleMapper = new Model_Schedule();
                $scheduleDetails = $scheduleMapper->getScheduleById($data['sid']);
                $this->view->scheduleDetails = $scheduleDetails[0];
                $priorityMapper = new Model_Priority();
                $priorityDetails = $priorityMapper->getAllPriority($scheduleDetails[0]['priority_id']);
                $this->view->priorityDetails = $priorityDetails[0];
                $ssMapper = new Model_ScheduleStatus();
                $status_list = $ssMapper->getScheduleStatus();
                $this->view->status_list = $status_list;

                $wdMapper = new Model_WeekDays();
                $wd_list = $wdMapper->getWeekDays();
                $this->view->wd_list = $wd_list;

                $lengthMapper = new Model_Length();
                $length_list = $lengthMapper->getLength();
                $this->view->length_list = $length_list;
            } catch (Exception $e) {
                
            }
        }
    }

    public function createcategorytemplateAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $build_ID = $this->_getParam('bid', '');
        $this->view->build_id = $build_ID;
        $this->view->cust_id = $this->cust_id;

        $categoryMapper = new Model_Category();
        $data = $this->getRequest()->getPost();
        $buildingId = "";
        if ($build_ID != '') {
            $param = $this->getRequest()->getParams();
            $categoryDetail = array();
            if (isset($param['id'])) {
                $categoryDetail = $categoryMapper->getAllCategory($param['id']);
            }

            $priorityMapper = new Model_Priority();
            $priorityDetail = $priorityMapper->getAllPriorityByBuildId($build_ID);

            $id = $categoryMapper->getCerrentId();
            $id = $id[0]['cat_id'];
            if (empty($id)) {
                $id = 1;
            } else {
                $id += 1;
            }


            $companyModel = new Model_Company();
            $nottenant = 1; // this for not listing the tenant user here.
            $users = $companyModel->getUserByBuildingId($build_ID, $nottenant);

            $egroupModel = new Model_EmailGroup();
            $groupList = $egroupModel->get_email_group_by_building_id($build_ID, 0);
            $tenantModel = new Model_Tenant();
            $tenantList = $tenantModel->getTenantByBuildingId($build_ID);
            $this->view->priorityDetail = $priorityDetail;
            $this->view->id = $id;
            $this->view->groupList = $groupList;
            $this->view->userList = $users;
            $this->view->tenantList = $tenantList;
        }
    }

    public function getcategoryfortemplateAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $category_model = new Model_Category();
            $categoryDetails = $category_model->getCategoryForTemplate($data['cid']);
            $categoryDetails = $categoryDetails[0];
            if ($categoryDetails->account_user != '') {
                $user_model = new Model_User();
                $userDetails = $user_model->getmultipleUsers($categoryDetails->account_user);
                $categoryDetails->userDetails = $userDetails;

                $group_model = new Model_EmailGroup();
                $groupDetails = $group_model->getMultipleGroup($categoryDetails->send_email);
                $categoryDetails->groupDetails = $groupDetails;

                $tenant_model = new Model_Tenant();
                $tenantDetails = $tenant_model->getMultipleTenants($categoryDetails->include_exclude);
                $categoryDetails->tenantDetails = $tenantDetails;
            }
            echo json_encode($categoryDetails);
            exit(0);
        }
    }

    public function importtenant($data) {
        $data = json_encode($data);
        $data = json_decode($data, true);
        $tenantModel = new Model_Tenant();
        $userModel = new Model_User();
        $tenantUserModel = new Model_TenantUser();
        //$isExist = $userModel->tenantUserExist($data['email'],5);
        // $isExist = $userModel->isUserExist($data['email']);
        $isExist = false;
        if (!$isExist) {
            $buildingData = array();
            //$module = explode(',',$data['modules']);
            if (isset($data['building']) && $data['building'] != '') {
                $building = new Model_Building();
                $buildingDetail = $building->getbuildingbyid($data['building']);
                $buildingData = $buildingDetail[0];
            }


            $tenantData['tenantName'] = $data['tenantName'];
            $tenantData['tenantContact'] = $data['tenantContact'];
            $tenantData['address1'] = $data['address1'];
            $tenantData['address2'] = $data['address2'];
            $tenantData['suite'] = $data['suite'];
            $tenantData['city'] = $data['city'];
            $accountmap = new Model_Account();
            $statename = $accountmap->getStatesByCode($data['state_code']);
            $tenantData['state'] = $statename[0]->state;
            $tenantData['state_code'] = $data['state'];
            $tenantData['postalCode'] = $data['postalCode'];
            $tenantData['phoneNumber'] = $data['phoneNumber'];
            $tenantData['phoneExt'] = $data['phoneExt'];
            $tenantData['billtoAddress'] = $data['billtoAddress'];
            $tenantData['status'] = $data['status'];
            $tenantData['imported_from'] = $data['imported_from'];

            $tenantData['updateddate'] = date('Y-m-d H:i:s');
            $tenantData['buildingId'] = $data['buildingId']; //implode(',', $data['building']);
            $tenantData['tenant_number'] = time();
            //$modules = implode(',', $module);


            $detail = array(
                "tenantName" => $data['tenantName'],
                "tenantUserName" => $data['firstname'] . ' ' . $data['lastname'],
                "tenantContact" => $data['tenantContact'],
                "phoneNumber" => $data['phonenumber'],
                "phoneExt" => $data['phoneExt'],
                "email" => $data['email'],
                "username" => $data['email'],
                "access" => 'Tenant Manager',
                "address1" => $data['address1'],
                "address2" => $data['address2'],
                "suite" => $data['suite'],
                "city" => $data['city'],
                "state" => $statename[0]->state,
                "postalCode" => $data['postalCode'],
            );


            $gpass = $this->generateRandomString();
            $detail['gpass'] = $gpass;
            $userData['email'] = $data['email'];
            $userData['firstName'] = $data['firstname'];
            $userData['lastName'] = $data['lastname'];
            $userData['Title'] = $data['tenantName'];
            $userData['phoneNumber'] = $data['phonenumber'];
            $userData['userName'] = $data['email'];
            $userData['password'] = md5($gpass);
            $userData['role_id'] = $data['role_id']; //tenant manager
            $userData['cust_id'] = $this->cust_id;
            $userData['regDate'] = date('Y-m-d H:i:s');


            $userData['uid'] = $userModel->insertUser($userData);
            $tenantData['userId'] = $userData['uid'];
            $tenant = $tenantModel->insertTenant($tenantData);

            /*             * ****Insert data in tenant user table ****** */
            $tenantUserData['userId'] = $userData['uid'];
            $tenantUserData['tenantId'] = $tenant;
            $tenantUserData['suite_location'] = $data['suite'];
            $tenantUserData['cc_enable'] = 1; // 1 for tenant admin & 0 for tenant user
            $tenantUserData['send_as'] = 1; // HTML by default
            $tenantUserData['complete_notification'] = 0; // no by default
            $tenantUserModel->insertTenantUser($tenantUserData);

            $userBuildingAccess = array();
            $detail['building'] = array();
            $userBuildingAccess = array();
            if (isset($data['building']) && $data['building'] != '') {
                $userBuildingAccess[] = array(
                    "user_id" => $userData['uid'],
                    "building_id" => $data['building'],
                    "modules_id" => '0',
                    "assigned_date" => date('Y-m-d H:i:s'),
                    "last_update_date" => date('Y-m-d H:i:s'),
                );
            }


            if (!empty($userBuildingAccess)) {
                $Model_User_Building_Module = new Model_UserBuildingModule();
                $Model_User_Building_Module->updateBuildingModule($userBuildingAccess);
            }
        }

        return $tenant;
    }

    function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public function importcategoryAction() {

        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $data = array_map('trim', $data);
            $emailuser = array();
            $importtenant = array();
            $include_exclude = array();
            $importedfrom = array();
            $preimported = array();


            /* import tenant start  */
            $newtenantusers = $data['include_exclude'];
            $newtenantusers = explode(',', $newtenantusers);

            $tenantModel = new Model_Tenant();
            $tenantcount = count($newtenantusers);

            // get all exist in current building
            $oldtenantdetails = $tenantModel->getTenantByBuildingId($data['building_id']);
            $oldtenantdetails = $oldtenantdetails;
            $oldtenantusers = array();
            foreach ($oldtenantdetails as $oldtenantdetailsvalue) {
                $oldtenantusers[] = $oldtenantdetailsvalue->id;
                $importedfrom[] = $oldtenantdetailsvalue->imported_from;
                $preimported[$oldtenantdetailsvalue->id] = $oldtenantdetailsvalue->imported_from;
            }
            // check tenant needs to import
            $importtenants = array_diff($newtenantusers, $oldtenantusers);
            $importtenants = array_diff($importtenants, $importedfrom);
            if (count($importtenants) == 0) {
                foreach ($preimported as $key => $preimportedvalue) {
                    if ($preimportedvalue != 0 && (in_array($preimportedvalue, $newtenantusers))) {
                        $include_exclude[] = $key;
                    }
                }
            } else {

                // change name if name already exist
                for ($i = 0; $i < count($importtenants); $i++) {
                    if ($importtenants[$i] != '') {
                        $importtenantsetails = $tenantModel->getTenantById($importtenants[$i]);
                        $importtenantsetails[0]->buildingId = $data['building_id'];
                        $importtenantsetails[0]->imported_from = $importtenants[$i];

                        $tenantDetail = $tenantModel->checkTenantByName($importtenantsetails[0]->tenantName, $data['building_id']);
                        if (!empty($tenantDetail)) {
                            $order = $this->_getParam('order', 'id');
                            $dir = $this->_getParam('dir', 'DESC');
                            $orderby = $order . ' ' . $dir;
                            $tenantDataDuplicate = $tenantModel->getTenantByNameDuplicate($importtenantsetails[0]->tenantName . '_imported_', $building_id, $orderby);

                            if ($tenantDataDuplicate != '') {
                                $tenantName = $tenantDataDuplicate[0]['tenantName'];
                                $replace_post = strpos($tenantName, "imported_");
                                if ($replace_post !== false) {
                                    $replace_renamet = substr($tenantName, $replace_post);
                                    $explodet = explode('_', $replace_renamet);
                                    $tenantName = $importtenantsetails[0]->tenantName . '_imported_' . ($explodet[1] + 1);
                                    $importtenantsetails[0]->tenantName = $tenantName;
                                }
                            } else {
                                $tenantName = $importtenantsetails[0]->tenantName . "_imported_1";
                                $importtenantsetails[0]->tenantName = $tenantName;
                            }
                        }
                        $include_exclude[$i] = $this->importtenant($importtenantsetails[0]);
                    }
                }
            }

            /* End import tenant */


            /* insert email group start */

            $email_group_model = new Model_EmailGroup();
            $email_group_user_model = new Model_EmailGroupUsers();

            //get all emails group existing in building

            $old_email_group = $email_group_model->get_email_group_by_building_id($data['building_id']);
            $old_email_group_id = array();
            foreach ($old_email_group as $old_email_groupvalue) {

                if ($old_email_groupvalue['imported_from'] != 0) {
                    $old_email_group_id[$old_email_groupvalue['id']] = $old_email_groupvalue['imported_from'];
                }
            }
            $explode_send_email = explode(',', $data['send_email']);
            $explode_send_email_insert = $explode_send_email;

            // check tenants need to import

            $send_email_count = count($explode_send_email);
            for ($i = 0; $i < $send_email_count; $i++) {
                if (in_array($explode_send_email[$i], $old_email_group_id)) {
                    $explode_send_email_insert[$i] = array_search($explode_send_email[$i], $old_email_group_id);
                } else {

                    $emailgrpsdetails = $email_group_model->getGroups($explode_send_email[$i]);
                    $groupExist = $email_group_model->validateGroupName($emailgrpsdetails[0]['group_name'], $data['building_id']);
                    if ($groupExist != false) {
                        $order = $this->_getParam('order', 'id');
                        $dir = $this->_getParam('dir', 'DESC');
                        $orderby = $order . ' ' . $dir;
                        $gruopDataDuplicate = $email_group_model->getGruopByNameDuplicate($emailgrpsdetails[0]['group_name'] . '_imported_', $data['building_id'], $orderby);
                        //print_r($priorityDataDuplicate[0]['priorityName']);

                        if ($gruopDataDuplicate != '') {
                            $groupName = $emailgrpsdetails[0]['group_name'];
                            $replace_posg = strpos($groupName, "imported_");
                            if ($replace_posg !== false) {
                                $replace_renameg = substr($groupName, $replace_posg);
                                $explodeg = explode('_', $replace_renameg);
                                $groupName = $emailgrpsdetails[0]['group_name'] . '_imported_' . ($explodeg[1] + 1);
                                ;
                            }
                        } else {
                            $groupName = $emailgrpsdetails[0]['group_name'] . "_imported_1";
                        }
                    }

                    $emailgrpsdetails = $emailgrpsdetails[0];
                    $emailgrpsdetails['building_id'] = $data['building_id'];
                    $emailgrpsdetails['updated_date'] = date('Y-m-d H:i:s');
                    $emailgrpsdetails['created_by'] = $this->userId;
                    $emailgrpsdetails['imported_from'] = $explode_send_email[$i];
                    $emailgrpsusrdetails = $email_group_user_model->getUsersByGid($emailgrpsdetails['id']);

                    unset($emailgrpsdetails['id']);
                    unset($emailgrpsdetails['created_date']);

                    $group_id = $email_group_model->saveEmailGroup($emailgrpsdetails);
                    $explode_send_email_insert[$i] = $group_id;
                    $countemailgrps = count($emailgrpsusrdetails);
                    for ($k = 0; $k < $countemailgrps; $k++) {
                        $emailgrpsusrdata['group_id'] = $group_id;
                        $emailgrpsusrdata['send_as'] = $emailgrpsusrdetails[$k]['send_as'];
                        $emailgrpsusrdata['days_of_the_week'] = $emailgrpsusrdetails[$k]['days_of_week'];
                        $emailgrpsusrdata['complete_notification'] = $emailgrpsusrdetails[$k]['complete_notification'];
                        $emailgrpsusrdata['to_select_list'] = array('user' => $emailgrpsusrdetails[$k]['user_id']);
                        $res = $email_group_user_model->saveEmailGroupUsers($emailgrpsusrdata, $group_id);
                        $emailuser[] = $emailgrpsusrdetails[$k]['user_id'];
                    }
                }
            }

            // insert account users
            $newusers = explode(',', $data['account_user']);
            $newusers = array_merge($emailuser, $newusers);
            $companyModel = new Model_Company();
            $nottenant = 1;
            $users = $companyModel->getUserByBuildingId($data['building_id'], $nottenant);
            $oldusers = explode(',', $users[0]->user_id);
            $importusers = array_diff($newusers, $oldusers);
            $countusersupdate = count($importusers);
            $userModel = new Model_User();
            $modules = new Model_Module();
            $modulesDetail = $modules->getModule();

            for ($i = 0; $i < $countusersupdate; $i++) {
                $Model_User_Building_Module = new Model_UserBuildingModule();
                if ($importusers[$i] != '') {
                    $isUserExist = $Model_User_Building_Module->getUserBuild($importusers[$i], $data['building_id']);

                    if ($isUserExist == false) {
                        $Model_User_Building_Module->insertModuleAcsess(array('user_id' => $importusers[$i], 'building_id' => $data['building_id'], 'modules_id' => $modulesDetail[0]['module_id'], 'assigned_date' => date('Y-m-d H:i:s'), 'last_update_date' => date('Y-m-d H:i:s')));
                    }
                }
                //$Model_User_Building_Module ->insertModuleAcsess(array('user_id'=>$importusers[$i], 'building_id' =>$data['building_id'], 'modules_id'=>$modulesDetail[0]['module_id']));
            }

            //insert priority
            $priorityName = $data['priorityScheduletext'];
            $building_id = $data['building_id'];
            $priorityMapper = new Model_Priority();
            $priorityData = $priorityMapper->getPrioritByName($priorityName, $building_id);

            // for duplicate name
            if ($priorityData) {
                $order = $this->_getParam('order', 'pid');
                $dir = $this->_getParam('dir', 'DESC');
                $orderby = $order . ' ' . $dir;
                $priorityDataDuplicate = $priorityMapper->getPrioritByNameDuplicate($priorityName . '_imported_', $building_id, $orderby);
                //print_r($priorityDataDuplicate[0]['priorityName']);

                if ($priorityDataDuplicate != '') {
                    $priorityName = $priorityDataDuplicate[0]['priorityName'];
                    $replace_pos = strpos($priorityName, "imported_");
                    if ($replace_pos !== false) {
                        $replace_rename = substr($priorityName, $replace_pos);
                        $explode = explode('_', $replace_rename);
                        $priorityName = $data['priorityScheduletext'] . '_imported_' . ($explode[1] + 1);
                        ;
                    }
                } else {
                    $priorityName = $data['priorityScheduletext'] . "_imported_1";
                }
            }
            $scheduleMapper = new Model_Schedule();
            $prioritydata = $priorityMapper->getAllPriority($data['prioritySchedule']);
            $allpriorityData = $priorityMapper->getAllPriorityByBuildId($data['building_id']);
            $inserpriority = '';
            foreach ($allpriorityData as $allpriorityDataValue) {
                if ($allpriorityDataValue['import_from'] == $data['prioritySchedule']) {
                    $inserpriority = $allpriorityDataValue['pid'];
                }
            }

            if ($inserpriority == '') {
                $insertdata = $prioritydata[0];
                $insertdatatemplate['priorityName'] = $priorityName;
                $insertdatatemplate['building_id'] = $data['building_id'];
                $insertdatatemplate['priorityDescription'] = $insertdata['priorityDescription'];
                $insertdatatemplate['status'] = $insertdata['status'];
                $insertdatatemplate['email_status'] = $insertdata['email_status'];
                $insertdatatemplate['import_template'] = 1;
                $insertdatatemplate['global_template'] = 0;
                $insertdatatemplate['created_by'] = $this->userId;
                $insertdatatemplate['import_from'] = $data['prioritySchedule'];
                $insertdatatemplate['created_date'] = date("Y-m-d");
                $inserpriority = $priorityMapper->insertPriority($insertdatatemplate);
                $scheduleData = $scheduleMapper->getSchedule($data['prioritySchedule']);
                foreach ($scheduleData as $value) {
                    $value['priority_id'] = $inserpriority;
                    unset($value['id']);
                    $res1 = $scheduleMapper->insertSchedule($value);
                }
            }

            $categoryMapper = new Model_Category();
            $res = $categoryMapper->checkname($data['categoryNameText'], $data['building_id']);
            $categoryName = $data['categoryNameText'];
            if ($res) {
                $order = $this->_getParam('order', 'cat_id');
                $dir = $this->_getParam('dir', 'DESC');
                $orderby = $order . ' ' . $dir;
                $categoryDataDuplicate = $categoryMapper->getCategoryByNameDuplicate($data['categoryNameText'] . '_imported_', $data['building_id'], $orderby);
                //print_r($priorityDataDuplicate[0]['priorityName']);

                if ($categoryDataDuplicate != '') {
                    $categoryName = $categoryDataDuplicate[0]['categoryName'];
                    $replace_posc = strpos($categoryName, "imported_");
                    if ($replace_posc !== false) {
                        $replace_renamec = substr($categoryName, $replace_posc);
                        $explodec = explode('_', $replace_renamec);
                        $categoryName = $data['categoryNameText'] . '_imported_' . ($explodec[1] + 1);
                        ;
                    }
                } else {

                    $categoryName = $data['categoryNameText'] . "_imported_1";
                }
            }

            $data['createdBy'] = $this->userId;
            $data['include_exclude'] = implode(',', $include_exclude);
            $data['send_email'] = implode(',', $explode_send_email_insert);
            $data['created_date'] = date("Y-m-d");
            $data['categoryName'] = $categoryName;
            $data['prioritySchedule'] = $inserpriority;
            $data['import_template'] = 1;

            unset($data['global_template']);
            unset($data['priorityScheduletext']);
            unset($data['categoryNameText']);

            $categoryMapper->insertCategory($data);
        }
        echo json_encode(array('true' => 'true'));
        exit(0);
    }

}

?>
