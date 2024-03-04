<?php

class DashboardController extends Ve_Controller_Base {

    private $userId = '';
    private $roleId = '';

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
        $this->accessHelper = $this->_helper->access;
        $this->dline_location = 1;
        $this->ddetail_location = 2;
        $this->createnew_location = 3;
        $this->closewo_location = 4;
    }

    // Call befor any action and check is user login or not
    public function preDispatch() {
        if (!Zend_Auth::getInstance()->hasIdentity())
            $this->_redirect('/index');
        $level = (Zend_Auth::getInstance()->getStorage()->read()) ? Zend_Auth::getInstance()->getStorage()->read()->role_id : '';
        /*         * if($level==md5('D')){
          Zend_Auth::getInstance()->clearIdentity();
          $this->_redirect('sales');
          }else if($level==md5('A')) {
          $this->_redirect('site');
          } */

        $this->userId = Zend_Auth::getInstance()->getStorage()->read()->uid;
        $this->roleId = Zend_Auth::getInstance()->getStorage()->read()->role_id;
        $this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
        if (!isset($_COOKIE['build_cookie'])) {
            $_COOKIE['build_cookie'] = '';
        }
    }

    public function indexAction() {

        if ($this->roleId == 1) {
            $message = 'You are in administration area.';
        } else if ($this->roleId == 9) {
            $accountMapper = new Model_Account();
            $companyDetail = $accountMapper->getcompany($this->cust_id);
            $companyInfo = $companyDetail[0];
            $message = 'You are in <b>' . $companyInfo['companyName'] . '</b> company administration area.';
        } else {
            $roleMapper = new Model_Role();
            $roleDetail = $roleMapper->getRole($this->roleId);
            $rolenfo = $roleDetail[0];
            $message = 'You are in building\'s <b>' . $rolenfo['title'] . '</b> area.';
        }
        $this->view->message = $message;
        $noredirect = array(1, 5, 7);
        if (!in_array($this->roleId, $noredirect)) {
            $this->_redirect('/dashboard/workorder');
        }
    }
    
    

    public function workorderAction() {
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

        $page = $this->_getParam('page', 1);
        $order = $this->_getParam('order', 'woId');
        $dir = $this->_getParam('dir', 'DESC');
        
        $wolist = '';
        $build_ID = $this->_getParam('bid', '');
        $select_build_id = $build_ID;
        /*         * *******set building in cookie ********* */
        $buildIds = array();
        foreach ($companyListing as $cl) {
            $buildIds[] = $cl['build_id'];
        }
        if ($build_ID == 'all') {
            $set_cookie = setcookie('build_cookie', '', time() + (86400 / 24), "/");
        }
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds))) {
            $build_ID = $_COOKIE['build_cookie'];
        } else {
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
            if ($build_ID == 'all') {
                $build_ID = '';
                $set_cookie = setcookie('build_cookie', '', time() + (86400 / 24), "/");
            }
        }

        //$_COOKIE['build_cookie'];

        $woModel = new Model_WorkOrder();
        $search_array = array();

        if (isset($_REQUEST['submitform'])) {
            if (isset($_POST['search_chkboxstatus']) && $_POST['search_chkboxstatus'] != '') {
                $search_array['search_status'] = $_POST['search_chkboxstatus'];

                setcookie('search_chkboxstatus', serialize($_POST['search_chkboxstatus']), 2147483647, '/');
                
            } else {
                setcookie('search_chkboxstatus', '', 2147483647, '/');
                setcookie('show_limit', serialize($_POST['show_limit']), 2147483647, '/');
            }
        } elseif (!isset($search_array['search_chkboxstatus']) && isset($_COOKIE['search_chkboxstatus'])) {
            $search_array['search_status'] = unserialize($_COOKIE['search_chkboxstatus']);
            
        }
        
        $show = $this->_getParam('show', '');
        if($show != ""){
            setcookie('show_limit', $show, 2147483647, '/');
        }else{
           $show =  $_COOKIE['show_limit'];
        }
        
//        if($show == 'all'){
//            $show = 1000;
//        }
        //echo $show;
        if(unserialize($show)){
            $show =  unserialize($show);
        }
        //if(!is_int($show) || $show==""){
        if($show==""){
            $show = 10;
        }

        $category_name = $this->_getParam('category_name', '');
        if ($category_name != '') {
            $search_array['category_name'] = addslashes($category_name);
            $this->view->category_name = $category_name;
        }

        $tenant_name = $this->_getParam('tenant_name', '');
        if ($tenant_name != '') {
            $search_array['tenant_name'] = addslashes($tenant_name);
            $this->view->tenant_name = $tenant_name;
        }

        $search_wo = $this->_getParam('search_wo', '');
        if ($search_wo != '') {
            $search_array['search_wo'] = $search_wo;
            $this->view->search_wo = $search_wo;
        }

        $from_date = $this->_getParam('from_date', '');
        if ($from_date != '') {
            $search_array['from_date'] = date("Y-m-d", strtotime($from_date));
            $this->view->from_date = date("Y-m-d", strtotime($from_date));
        }

        $to_date = $this->_getParam('to_date', '');
        if ($to_date != '') {
            $search_array['to_date'] = date("Y-m-d", strtotime($to_date));
            $this->view->to_date = date("Y-m-d", strtotime($to_date));
        }


        if ($companyListing != '') {


            /* if($this->roleId=='9'){ */
            if ($build_ID == '') {
                $buildIds = array();
                foreach ($companyListing as $cl) {
                    $buildIds[] = $cl['build_id'];
                }
                $wolist = $woModel->getWorkOrderByBuilIds($buildIds, $order, $dir, $search_array,$page, $show);
                $wolistcount = $woModel->getWorkOrderByBuilIdsNew($buildIds, $order, $dir, $search_array);
            } else {

                $wolist = $woModel->getBuildingWorkOrder($build_ID, $order, $dir, $search_array,$page, $show);
                $wolistcount = $woModel->getBuildingWorkOrderNew($build_ID, $order, $dir, $search_array);
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
              } */
        }
        // from here
        $total_rows = $wolistcount;
        $no_of_records_per_page = $show;
        $total_pages = ceil($total_rows / $no_of_records_per_page);
        $this->view->total_pages = $total_pages;
        $this->view->pageno = $page;
        $this->view->records = sizeof($wolist);
        
        
        $pageObj = new Ve_Paginator();
        //$paginator = $pageObj->fetchPageDataResult($wolist, $page, $show);
        $paginatorNew = $pageObj->fetchPageDataResultNew($wolist, $wolistcount, $page, $show);
        //$lastactivity = $woModel->get_last_activity();
        $this->view->page = $page;
        $view_type = $this->_getParam('view_type', 'line');
        $this->view->custID = $this->cust_id;
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $build_ID;
        $this->view->wolist = $paginatorNew;//$paginator;
        $this->view->order = $order;
        $this->view->dir = $dir;
        $this->view->view_type = $view_type;
        $this->view->roleId = $this->roleId;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->dline_location = $this->dline_location;
        $this->view->ddetail_location = $this->ddetail_location;
        $this->view->createnew_location = $this->createnew_location;
        $this->view->closewo_location = $this->closewo_location;
        $this->view->userId = $this->userId;
        // refresh outo load  
        $getsult = $this->gettotalAction();       
        $this->view->count = $getsult;//$getsult['count'];
        $this->view->show = $show;
        if (isset($_REQUEST['submitform'])) {
            if (isset($_POST['search_chkboxstatus']) && $_POST['search_chkboxstatus'] != '') {
                $this->view->statusCookieDetails = $_POST['search_chkboxstatus'];
            }
        } else {

            $statusCookie = new Zend_Controller_Request_Http();
            $statusCookieDetails = $statusCookie->getCookie('search_chkboxstatus');
            $statusCookieDetails = unserialize($statusCookieDetails);
            if ($statusCookieDetails != '') {
                $this->view->statusCookieDetails = $statusCookieDetails;
            }
        }
        $adminNamespace = new Zend_Session_Namespace('Admin_User');
        $this->view->admin_role_id = $adminNamespace->role_id;
    }
    
    
    public function gettotalAction(){
        $woModel = new Model_WorkOrder();        
        $lastactivity = $woModel->get_last_activity();
        $lastactivity1 = $woModel->get_last_activity1();
        $lastactivity2 = $woModel->get_last_activity2();
        $total = count($lastactivity)+count($lastactivity1)+count($lastactivity2);
        return $total;
    } 
    
    
    public function checkstatusAction(){
        
        $total = $this->gettotalAction();
        $totalad = array("count"=>$total);
        //print_r($total);
        echo  json_encode($totalad);
        die;
    }
    
    public function reloadworkorderAction() {
        $this->_helper->getHelper('layout')->disableLayout();  
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

        $page = $this->_getParam('page', 1);
        $order = $this->_getParam('order', 'woId');
        $dir = $this->_getParam('dir', 'DESC');
        $wolist = '';
        $show =  $_COOKIE['show_limit'];
        $build_ID = $this->_getParam('bid', '');
        $select_build_id = $build_ID;
        /*         * *******set building in cookie ********* */
        $buildIds = array();
        foreach ($companyListing as $cl) {
            $buildIds[] = $cl['build_id'];
        }
        if ($build_ID == 'all') {
            $set_cookie = setcookie('build_cookie', '', time() + (86400 / 24), "/");
        }
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds))) {
            $build_ID = $_COOKIE['build_cookie'];
        } else {
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
            if ($build_ID == 'all') {
                $build_ID = '';
                $set_cookie = setcookie('build_cookie', '', time() + (86400 / 24), "/");
            }
        }

        /* Show list of workorder */
        $show = $this->_getParam('show', '');
        if($show != ""){
            setcookie('show_limit', $show, 2147483647, '/');
        }else{
           $show =  $_COOKIE['show_limit'];
        }
        
        /*if($show == 'all'){
            $show = 1000;
        }*/
        if(unserialize($show)){
            $show =  unserialize($show);
        }
        
        if($show==""){
            $show = 10;
        }
        //$_COOKIE['build_cookie'];

        $woModel = new Model_WorkOrder();

        $search_array = array();

        if (isset($_REQUEST['submitform'])) {
            if (isset($_POST['search_chkboxstatus']) && $_POST['search_chkboxstatus'] != '') {
                $search_array['search_status'] = $_POST['search_chkboxstatus'];

                setcookie('search_chkboxstatus', serialize($_POST['search_chkboxstatus']), 2147483647, '/');
            } else {
                setcookie('search_chkboxstatus', '', 2147483647, '/');
            }
        } elseif (!isset($search_array['search_chkboxstatus']) && isset($_COOKIE['search_chkboxstatus'])) {

            $search_array['search_status'] = unserialize($_COOKIE['search_chkboxstatus']);
        }
        $category_name = $this->_getParam('category_name', '');
        if ($category_name != '') {
            $search_array['category_name'] = addslashes($category_name);
            $this->view->category_name = $category_name;
        }

        $tenant_name = $this->_getParam('tenant_name', '');
        if ($tenant_name != '') {
            $search_array['tenant_name'] = addslashes($tenant_name);
            $this->view->tenant_name = $tenant_name;
        }

        $search_wo = $this->_getParam('search_wo', '');
        if ($search_wo != '') {
            $search_array['search_wo'] = $search_wo;
            $this->view->search_wo = $search_wo;
        }

        $from_date = $this->_getParam('from_date', '');
        if ($from_date != '') {
            $search_array['from_date'] = date("Y-m-d", strtotime($from_date));
            $this->view->from_date = date("Y-m-d", strtotime($from_date));
        }

        $to_date = $this->_getParam('to_date', '');
        if ($to_date != '') {
            $search_array['to_date'] = date("Y-m-d", strtotime($to_date));
            $this->view->to_date = date("Y-m-d", strtotime($to_date));
        }
        if ($companyListing != '') {
            /* if($this->roleId=='9'){ */
            if ($build_ID == '') {
                $buildIds = array();
                foreach ($companyListing as $cl) {
                    $buildIds[] = $cl['build_id'];
                }
                $wolist = $woModel->getWorkOrderByBuilIds($buildIds, $order, $dir, $search_array, $page, $show);
                $wolistcount = $woModel->getWorkOrderByBuilIdsNew($buildIds, $order, $dir, $search_array);
            } else {

                $wolist = $woModel->getBuildingWorkOrder($build_ID, $order, $dir, $search_array, $page, $show );
                $wolistcount = $woModel->getBuildingWorkOrderNew($build_ID, $order, $dir, $search_array);
            }
        }
        // from here
        $total_rows = $wolistcount;
        $no_of_records_per_page = $show;
        $total_pages = ceil($total_rows / $no_of_records_per_page);
        $this->view->total_pages = $total_pages;
        $this->view->pageno = $page;
        $this->view->records = sizeof($wolist);
        
        $pageObj = new Ve_Paginator();
        //$paginator = $pageObj->fetchPageDataResult($wolist, $page, $show);
        $paginatorNew = $pageObj->fetchPageDataResultNew($wolist, $wolistcount, $page, $show);
        //$lastactivity = $woModel->get_last_activity();
        $this->view->page = $page;
        $view_type = $this->_getParam('view_type', 'line');
        $this->view->custID = $this->cust_id;
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $build_ID;
        $this->view->wolist = $paginatorNew;//$paginator;
        $this->view->order = $order;
        $this->view->dir = $dir;
        $this->view->view_type = $view_type;
        $this->view->roleId = $this->roleId;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->dline_location = $this->dline_location;
        $this->view->ddetail_location = $this->ddetail_location;
        $this->view->createnew_location = $this->createnew_location;
        $this->view->closewo_location = $this->closewo_location;
        $this->view->userId = $this->userId;
        // refresh outo load  
        $getsult = $this->gettotalAction();
       
        $this->view->count = $getsult;//$getsult['count'];
        if (isset($_REQUEST['submitform'])) {
            if (isset($_POST['search_chkboxstatus']) && $_POST['search_chkboxstatus'] != '') {
                $this->view->statusCookieDetails = $_POST['search_chkboxstatus'];
            }
        } else {

            //$statusCookie = new Zend_Controller_Request_Http();
            //$statusCookieDetails = $statusCookie->getCookie('search_chkboxstatus');
            //$statusCookieDetails = unserialize($statusCookieDetails);
            //if ($statusCookieDetails != '') {
              //  $this->view->statusCookieDetails = $statusCookieDetails;
            //}
        }
        $adminNamespace = new Zend_Session_Namespace('Admin_User');
        $this->view->admin_role_id = $adminNamespace->role_id;
    }

    public function orderdetailAction() { 
        //makes disable layout
        $this->_helper->getHelper('layout')->disableLayout();
        $woId = $this->_getParam('woId', '');
        $woModel = new Model_WorkOrder();
        $wodetail = $woModel->getWorkOrderInfo($woId);
        $this->view->woData = $wodetail[0];
        $this->view->roleId = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->dline_location = $this->dline_location;
        $adminNamespace = new Zend_Session_Namespace('Admin_User');
        $this->view->admin_role_id = $adminNamespace->role_id;
    }

    public function updateorderAction() {
        $data = $this->getRequest()->getPost();
        if (isset($data['work_order_id'])) {
            $woId = $data['work_order_id'];
            /******************* set timezone through building id *************************** */
            $wpModel = new Model_WorkOrder();
            $wo_building_data = $wpModel->getWorkOrder($woId);
            $this->setTimezone($wo_building_data[0]['building']);
            /******************* end - set timezone through building id *************************** */

            $wpModel = new Model_WorkOrderUpdate();
            // reset work order update
            $resetCurrent = $wpModel->updateWorkOrderByWoId(array('current_update' => 0), $woId);
            if ($data['insert_schedule'] == '1') {
                $schModel = new Model_Schedule();
                $schdata = array();
                $schdata['priority_id'] = $data['priority'];
                $schdata['length'] = $data['length'];
                $schdata['Time'] = $data['Time'];
                $schdata['start_status'] = $data['order_status'];
                $schdata['end_status'] = 2;
                $schdata['access_days'] = 1;
                $schdata['status'] = 1;
                $schdata['created_by'] = $this->userId;
                $schdata['created_date'] = date('Y-m-d');
                $insertData = $schModel->insertSchedule($schdata);
            }
            $wpData = array();
            $wpData['wo_id'] = $woId;
            $wpData['internal_note'] = '';
            $wpData['wo_status'] = $data['order_status'];
            $wpData['current_update'] = 1;
            $wpData['user_id'] = $this->userId;
            $wpData['created_at'] = date('Y-m-d H:i:s');
            if ($data['order_status'] == 7) {
                $wparaModel = new Model_WoParameter();
                $wpDetails = $wparaModel->getWoParameterByBid($wo_building_data[0]['building']);
                $wpDetails = ($wpDetails) ? $wpDetails[0] : '';
                $wpData['billable_opt'] = $wpDetails['billable'];
            }
            $insertWp = $wpModel->insertWorkOrderUpdate($wpData);

            /********* History Log *********/
            $whlModel = new Model_WoHistoryLog();
            $whData = array();
            $whData['woId'] = $woId;
            $whData['log_type'] = 'status';
            $whData['current_value'] = $data['current_wstatus'];
            $whData['change_value'] = $data['order_status'];
            $whData['user_id'] = $this->userId;
            $whData['created_at'] = date('Y-m-d H:i:s');
            $insertWHL = $whlModel->insertHistoryLog($whData);

            // update work order schedule
            $schModel = new Model_Schedule();
            $schData = $schModel->getSchdeuleByCurrWoStatus($woId, $data['order_status']);
            if ($data['current_wstatus'] == 1) {
                $this->sendOnlyBadge($woId);
            }
            if ($schData) {
                $wstModel = new Model_WoScheduleStatus();
                // fetch current status work order
                $wsDetail = $wstModel->getCurrentWs($woId);
                foreach ($wsDetail as $wsd) {
                    $wsUpdate = $wstModel->updateWoSchedule(array('current_status' => 0), $wsd['wssId']);
                }
                $wss_data = array();
                $wss_data['worder_id'] = $woId;
                $wss_data['schedule_id'] = $schData[0]->id;
                $wss_data['priority_id'] = $schData[0]->priority_id;
                $wss_data['status'] = 1;
                //$wss_data['ckey']= md5(time());
                $wss_data['current_status'] = 1;
                $wss_data['created_at'] = date('Y-m-d H:i:s');
                $ws_insert = $wstModel->insertWoSchedule($wss_data);
                if ($data['order_status'] == '6') {
                    $wparaModel = new Model_WoParameter();
                    $woModel = new Model_WorkOrder();
                    $wo_building_data = $wo_building_data[0];
                    $master_internal_work_order = $wo_building_data['master_internal_work_order'];
                    if ($wo_building_data['building'] != '') {
                        $wpDetails = $wparaModel->getWoParameterByBid($wo_building_data['building']);
                        $wpDetails = ($wpDetails) ? $wpDetails[0] : '';
                    }
                    /*                     * ***** sent email to tenant and account user ******* */
                    if ($wpDetails['email_tenant'] == '1' && $master_internal_work_order != '1') {
                        $sendTenantMail = $woModel->sendClosedNotification($woId, $this->userId, 'users');
                    } else {
                        $sendTenantMail = $woModel->sendClosedNotification($woId, $this->userId, 'users', 1);
                    }
                } else if ($data['order_status'] == '7' && $data['current_wstatus'] != 6) {
                    $wparaModel = new Model_WoParameter();
                    $woModel = new Model_WorkOrder();
                    $wo_building_data = $wo_building_data[0];
                    $master_internal_work_order = $wo_building_data['master_internal_work_order'];
                    if ($wo_building_data['building'] != '') {
                        $wpDetails = $wparaModel->getWoParameterByBid($wo_building_data['building']);
                        $wpDetails = ($wpDetails) ? $wpDetails[0] : '';
                    }
                    /*                     * ***** sent email to tenant and account user ******* */
                    if ($wpDetails['email_tenant'] == '1' && $master_internal_work_order != '1') {
                        $sendTenantMail = $woModel->sendClosedNotification($woId, $this->userId, 'users');
                    } else {
                        $sendTenantMail = $woModel->sendClosedNotification($woId, $this->userId, 'users', 1);
                    }
                }
            }
        }
        header("Location: {$_SERVER['HTTP_REFERER']}/#$woId");
        exit(0);
        //$this->_redirect("/dashboard/workorder/#$woId");
    }

    public function setTimezone($building_id) {
        $timezone_Model = new Model_TimeZone();
        $build_model = new Model_Building();
        $tz_build_data = $build_model->getbuildingbyid($building_id);

        if (isset($tz_build_data[0]['timezone']) && $tz_build_data[0]['timezone'] != 0) {
            $timezone_data = $timezone_Model->getTimeZone($tz_build_data[0]['timezone']);
            $timezone = $timezone_data[0]['time_value'];
            date_default_timezone_set($timezone);
        } else if ($tz_build_data[0]['timezone'] == 0) {
            date_default_timezone_set(DEFAULT_TIMEZONE);
        }
    }

    public function updateajaxorderAction() {

        $data = $this->getRequest()->getPost();
        if (isset($data['woId'])) {
            try {
                $woId = $data['woId'];

                /*                 * ***************** set timezone through building id *************************** */
                $wpModel = new Model_WorkOrder();
                $wo_building_data = $wpModel->getWorkOrder($woId);
                $this->setTimezone($wo_building_data[0]['building']);
                /*                 * ***************** end - set timezone through building id *************************** */

                $wpModel = new Model_WorkOrderUpdate();
                // reset work order update
                $resetCurrent = $wpModel->updateWorkOrderByWoId(array('current_update' => 0), $woId);
                // insert schedule status
                if ($data['insert_schedule'] == '1') {
                    $schModel = new Model_Schedule();
                    $schdata = array();
                    $schdata['priority_id'] = $data['priority'];
                    $schdata['length'] = $data['slength'];
                    $schdata['Time'] = $data['time'];
                    $schdata['start_status'] = $data['order_status'];
                    $schdata['end_status'] = 2;
                    $schdata['access_days'] = 1;
                    $schdata['status'] = 1;
                    $schdata['created_by'] = $this->userId;
                    $schdata['created_date'] = date('Y-m-d');
                    $insertData = $schModel->insertSchedule($schdata);
                }
                $wpData = array();
                $wpData['wo_id'] = $woId;
                $wpData['internal_note'] = '';
                $wpData['wo_status'] = $data['order_status'];
                $wpData['current_update'] = 1;
                $wpData['user_id'] = $this->userId;
                $wpData['created_at'] = date('Y-m-d H:i:s');
                if ($data['order_status'] == 7) {
                    $wparaModel = new Model_WoParameter();
                    $wpDetails = $wparaModel->getWoParameterByBid($wo_building_data[0]['building']);
                    $wpDetails = ($wpDetails) ? $wpDetails[0] : '';
                    $wpData['billable_opt'] = $wpDetails['billable'];
                }
                $insertWp = $wpModel->insertWorkOrderUpdate($wpData);

                /*                 * *******History Log ******** */
                $whlModel = new Model_WoHistoryLog();
                $whData = array();
                $whData['woId'] = $woId;
                $whData['log_type'] = 'status';
                $whData['current_value'] = $data['current_status'];
                $whData['change_value'] = $data['order_status'];
                $whData['user_id'] = $this->userId;
                $whData['created_at'] = date('Y-m-d H:i:s');
                $insertWHL = $whlModel->insertHistoryLog($whData);
                if ($data['current_status'] == 1) {
                    $this->sendOnlyBadge($woId);
                }
                // update work order schedule
                $schModel = new Model_Schedule();
                $schData = $schModel->getSchdeuleByCurrWoStatus($woId, $data['order_status']);
                if ($schData) {
                    $wstModel = new Model_WoScheduleStatus();
                    // fetch current status work order
                    $wsDetail = $wstModel->getCurrentWs($woId);
                    foreach ($wsDetail as $wsd) {
                        $wsUpdate = $wstModel->updateWoSchedule(array('current_status' => 0), $wsd['wssId']);
                    }
                    $wss_data = array();
                    $wss_data['worder_id'] = $woId;
                    $wss_data['schedule_id'] = $schData[0]->id;
                    $wss_data['priority_id'] = $schData[0]->priority_id;
                    $wss_data['status'] = 1;
                    //$wss_data['ckey']= md5(time());
                    $wss_data['current_status'] = 1;
                    $wss_data['created_at'] = date('Y-m-d H:i:s');
                    $ws_insert = $wstModel->insertWoSchedule($wss_data);
                    $woModel = new Model_WorkOrder();
                    if ($data['order_status'] == '6') {
                        $wparaModel = new Model_WoParameter();
                        $woModel = new Model_WorkOrder();
                        $wo_building_data = $wo_building_data[0];
                        $master_internal_work_order = $wo_building_data['master_internal_work_order'];
                        if ($wo_building_data['building'] != '') {
                            $wpDetails = $wparaModel->getWoParameterByBid($wo_building_data['building']);
                            $wpDetails = ($wpDetails) ? $wpDetails[0] : '';
                        }
                        /*                         * ***** sent email to tenant ******* */
                        if ($wpDetails['email_tenant'] == '1' && $master_internal_work_order != '1') {
                            $sendTenantMail = $woModel->sendClosedNotification($woId, $this->userId, 'users');
                        } else {
                            $sendTenantMail = $woModel->sendClosedNotification($woId, $this->userId, 'users', 1);
                        }
                    } else if ($data['order_status'] == '7' && $data['current_status'] != '6') {
                        $wparaModel = new Model_WoParameter();
                        $woModel = new Model_WorkOrder();
                        $wo_building_data = $wo_building_data[0];
                        $master_internal_work_order = $wo_building_data['master_internal_work_order'];
                        if ($wo_building_data['building'] != '') {
                            $wpDetails = $wparaModel->getWoParameterByBid($wo_building_data['building']);
                            $wpDetails = ($wpDetails) ? $wpDetails[0] : '';
                        }
                        /*                         * ***** sent email to tenant ******* */
                        if ($wpDetails['email_tenant'] == '1' && $master_internal_work_order != '1') {
                            $sendTenantMail = $woModel->sendClosedNotification($woId, $this->userId, 'users');
                        } else {
                            $sendTenantMail = $woModel->sendClosedNotification($woId, $this->userId, 'users', 1);
                        }
                    }
                }
                echo 'true';
            } catch (Exception $e) {
                //echo $e->getMessage();
                echo 'false';
            }
        }
        exit(0);
    }

    public function createworkorderAction() {
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

        $buildIds = array();
        foreach ($companyListing as $cl) {
            $buildIds[] = $cl['build_id'];
        }
        $build_ID = $this->_getParam('bid', '');
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else {
            $build_ID = ($build_ID == 'all') ? '' : $build_ID;
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        }
        $select_build_id = $build_ID;
        $submit_key = rand(10, 1000);
        $formKey = new Zend_Session_Namespace('formkey');
        $this->view->select_build_id = $select_build_id;
        $formKey->submit_key = $submit_key;
        $this->view->form_key = $submit_key;
        $this->view->companyListing = $companyListing;

        $this->view->roleId = $this->roleId;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->createnew_location = $this->createnew_location;
        $this->view->userId = $this->userId;
        $this->view->custID = $this->cust_id;
    }

    public function saveworkorderAction() {
        $data = $this->getRequest()->getPost();
        if (isset($data) && $data['building'] != '') {
            $form_key = $data['form_key'];
            $smsg = new Zend_Session_Namespace('message');
            $smsg->success_message = '';
            $smsg->error_message = '';
            $formKey = new Zend_Session_Namespace('formkey');
            $session_form_key = $formKey->submit_key;
            if ($form_key != $session_form_key) {
                $smsg->error_message = 'Invalid Form Key';
                $this->_redirect('/dashboard/createworkorder');
            } else {
                $formKey->submit_key = '';
            }

            /*             * ********check form validation ********* */
            $form_valid = true;
            if ($data['building'] == '' || $data['tenant'] == '' || $data['create_user'] == '' || $data['category'] == '' || $data['work_order_request'] == '') {
                $form_valid = false;
                $smsg->error_message = 'Form is not properly filled.';
                $this->_redirect('/dashboard/createworkorder');
            }
            if ($_FILES['wo_file']['name'] != '') {
                $filetype = array("application/pdf", "image/gif", "image/jpg", "image/jpeg", "image/png");

                if ($_FILES['wo_file']['size'] > 5242880 || !in_array($_FILES['wo_file']['type'], $filetype)) {
                    $form_valid = false;
                    $smsg->error_message = 'Form is not properly filled.';
                    $this->_redirect('/dashboard/createworkorder');
                }
            }

            if ($form_valid == true) {
                try {
                    $woModel = new Model_WorkOrder();
                    $insertData = array();
                    /*                     * ***************** set timezone through building id *************************** */

                    $this->setTimezone($data['building']);
                    /*                     * ***************** end - set timezone through building id *************************** */
                    $insertData['tenant'] = $data['tenant'];
                    $insertData['building'] = $data['building'];
                    $insertData['suite_location'] = $data['suite_location'];
                    $insertData['suite_location2'] = $data['suite_location'];
                    $insertData['create_user'] = $data['create_user'];
                    $insertData['date_requested'] = date("Y-m-d", strtotime($data['date_requested']));
                    $insertData['time_requested'] = $data['time_requested'];
                    //$insertData['priority'] = $data['priority'];
                    $insertData['category'] = $data['category'];
                    $insertData['internal_work_order'] = 1;
                    if (isset($data['m_inter_workorder']) && $data['m_inter_workorder'] != '') {
                        $insertData['master_internal_work_order'] = $data['m_inter_workorder'];
                    } else {
                        $insertData['master_internal_work_order'] = 0;
                    }
                    $insertData['work_order_request'] = $data['work_order_request'];
                    $insertData['created_at'] = date('Y-m-d H:i:s');
                    $woId = $woModel->insertWorkOrder($insertData);

                    $wpModel = new Model_WorkOrderUpdate();
                    $wpData = array();
                    $wpData['wo_id'] = $woId;
                    $wpData['wo_request'] = '';

                    $wpData['wo_status'] = 1;
                    $wpData['current_update'] = 1;
                    $wpData['created_at'] = date('Y-m-d H:i:s');

                    if ($data['internal_note'] != '') {
                        $dataNotes['user_id'] = $this->userId;
                        $wnModel = new Model_WoNote();
                        $dataNotes['note_date'] = date('Y-m-d');
                        $dataNotes['note'] = $data['internal_note'];
                        $dataNotes['woId'] = $woId;
                        $dataNotes['internal'] = 1;
                        $dataNotes['created_at'] = date('Y-m-d H:i:s');
                        $insertDataNotes = $wnModel->insertWoNote($dataNotes);

                        $wpData['internal_note'] = $insertDataNotes;
                    }

                    $insertWp = $wpModel->insertWorkOrderUpdate($wpData);
                    /*                     * ***********upload file*********** */




                    if ($_FILES['wo_file']['name'] != '') {
                        $uploaddir = BASE_PATH . 'public/work_order/';
                        $uploadfile_name = 'WO-' . time() . '-' . basename($_FILES['wo_file']['name']);
                        $uploadfile = $uploaddir . '' . $uploadfile_name;
                        if (!file_exists($uploaddir)) {
                            mkdir($uploaddir, 0777, true);
                        }
                        move_uploaded_file($_FILES["wo_file"]["tmp_name"], $uploadfile);
                        $fileTitle = explode('.', basename($_FILES['wo_file']['name']));
                        $file_title = $fileTitle[0];

                        $file_name = $uploadfile_name;
                        $insertFData = array();
                        $insertFData['woId'] = $woId;
                        $insertFData['file_title'] = $file_title;
                        $insertFData['file_name'] = $file_name;
                        try {
                            $fileModel = new Model_WoFiles();
                            $insertRecord = $fileModel->insertWoFile($insertFData);
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                    }

                    /*                     * ******* update work order number ******** */
                    if ($data['building'] != '') {
                        $lastWoNum = $woModel->getMaxWoNumber($data['building']);
                        $update_wo_num = '';
                        if ($lastWoNum) {
                            $update_wo_num = $lastWoNum + 1;
                        } else {
                            $update_wo_num = 1001;
                        }
                        try {
                            $update_num = $woModel->updateWorkOrder(array('wo_number' => $update_wo_num), $woId);
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                    $insertData['woId'] = $woId;
                    $priorityMapper = new Model_Priority();
                    $categoryMapper = new Model_Category();
                    $buildingMapper = new Model_Building();
                    $userMapper = new Model_User();
                    $tenantMapper = new Model_Tenant();
                    $catDetail = $categoryMapper->getCategoryName($data['category']);
                    $priorityDetail = $priorityMapper->getPriorityByCategory($data['category']);
                    $buildingDetail = $buildingMapper->getbuildingbyid($data['building']);
                    $tenantDetail = $tenantMapper->getTenantById($data['tenant']);
                    $tenantInfo = $tenantDetail[0];
                    $tenantData = (array) $tenantInfo;
                    $tenantData['tenantId'] = $tenantData ['id'];
                    if ($priorityDetail) {

                        $prioritData = (array) $priorityDetail[0];
                        $priorityName = $prioritData['priorityName'];
                        $pid = $prioritData['pid'];
                    } else {
                        $priorityName = 'Not Assigned';
                        $pid = 0;
                    }
                    /*                     * ********* Insert work order schedule *********** */
                    $schedule_id = 0;
                    $psModel = new Model_Schedule();
                    $startId = 1; // for start schedule status;
                    $psData = $psModel->getWoSchedule($pid, $startId);
                    if ($psData) {
                        $schedule_id = $psData[0]['id'];
                    }
                    $wssModel = new Model_WoScheduleStatus();
                    $wss_data = array();
                    $wss_data['worder_id'] = $woId;
                    $wss_data['schedule_id'] = $schedule_id;
                    $wss_data['priority_id'] = $pid;
                    $wss_data['status'] = 1;
                    $wss_data['ckey'] = md5(time());
                    $wss_data['current_status'] = 1;
                    $wss_data['created_at'] = date('Y-m-d H:i:s');
                    $ws_insert = $wssModel->insertWoSchedule($wss_data);

                    /*                     * **** send work order mail****** */
                    if (isset($data['m_inter_workorder']) && $data['m_inter_workorder'] != '') {
                        // if internal work order only
                    } else {
                        $sendmail = $woModel->sendWorkOrderEmail($woId, $tenantData);
                    }

                    //Send Push Notification
                    $pushDetails['bid'] = $buildingDetail[0]['build_id'];
                    $pushDetails['buildingName'] = $buildingDetail[0]['buildingName'];
                    $pushDetails['categoryName'] = $catDetail[0]['categoryName'];
                    $pushDetails['workorder'] = $update_wo_num;
                    $pushDetails['tenantName'] = $tenantInfo->tenantName;
                    $pushDetails['work_order_request'] = $data['work_order_request'];
                    $this->sendPushNotification($pushDetails);
                    //End Push Notification




                    /*                     * *********display print work order ******** */
                    $this->view->buildingDetail = $buildingDetail[0];
                    $this->view->workOrder = $insertData;
                    $this->view->cust_id = $this->cust_id;
                    $this->view->tenantInfo = $tenantData;
                    $this->view->categoryName = $catDetail[0]['categoryName'];
                    $this->view->priorityName = $priorityName;
                    $messsg = 'Work Order Request Saved Successfully.';
                    $smsg->success_message = $messsg;
                    if (isset($data['save_new']) && $data['save_new'] != '') {
                        $this->_redirect('/dashboard/createworkorder');
                    } else {
                        $this->_redirect('/dashboard/workorder');
                    }
                } catch (Exception $e) {
                    $this->view->msg = $e->getMessage();
                    $smsg->error_message = 'Error Occurred during save work order';
                    $this->_redirect('/dashboard/createworkorder');
                }
            }
        } else {
            $this->_redirect('/dashboard/createworkorder');
        }
    }

    public function sendOnlyBadge($woId) {

        $userMapper = new Model_User();
        $woModel = new Model_WorkOrder();
        $bid = $woModel->getBuildingbyworkorder($woId);
        $userData = $userMapper->getAllAccountUserOfBuilding($bid[0]->building);
        $userList = array();
        $androidPushIdList = array();
        $iosPushIdList = array();
        $status = array('1');
        $badges = array();
        if ($userData != '') {
            foreach ($userData as $userDataVal) {
                $userList[] = $userDataVal->uid;
                // Fetch all new work order of user
                $res = $woModel->getWorkorderbystatus($userDataVal->uid, $status);
                $badges[$userDataVal->uid] = $res[0]->count_workorder;
                // End Fetch new work order
            }
            $pushModel = new Model_PushNotification();
            $pushDetails = $pushModel->getPushId($userList);
            print_r($pushDetails);
            die;
            if ($pushDetails) {
                foreach ($pushDetails as $pushVal) {

                    // Start Android
                    if ($pushVal['device'] == 1) {
                        $registrationIds = $pushVal['push_id'];
                        $badgescount = $badges[$pushVal['user_id']];
                        //$pushModel->updatePushId(array('badges'=>$$badgescount), $registrationIds);
                        $msg = array
                            (
                            'sound' => 1,
                            'largeIcon' => 'small_icon',
                            'smallIcon' => 'small_icon',
                            'badge' => $badgescount,
                        );
                        $fields = array
                            (
                            'registration_ids' => array($registrationIds),
                            'data' => $msg,
                        );

                        $headers = array
                            (
                            'Authorization: key=' . API_ACCESS_KEY,
                            'Content-Type: application/json'
                        );
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                        $result = curl_exec($ch);

                        curl_close($ch);
                    }
                    // End Android
                    //Start IOS
                    if ($pushVal['device'] == 2) {

                        $registrationIds = $pushVal['push_id'];
                        $tToken[0] = $registrationIds;
                        $badgescount = $badges[$pushVal['user_id']];


                        $tHost = T_HOST;
                        $tPort = 2195;
                        $tCert = PEM_URL.'ck2.pem';
                        $tPassphrase = '1choc3747*1';
                        //$tAlert = 'Category- '.$userDetails['categoryName'] .', Building- '.$userDetails['buildingName'];
                        $tBadge = intval($badgescount);

                        $tPayload = 'APNS Message Handled by LiveCode';
                        $tBody = array();
                        $tBody['aps'] = array('badge' => $tBadge,);
                        $tBody ['payload'] = $tPayload;
                        $tBody = json_encode($tBody);
                        $tContext = stream_context_create();
                        stream_context_set_option($tContext, 'ssl', 'local_cert', $tCert);
                        //stream_context_set_option($tContext, 'ssl', 'passphrase', $tPassphrase);
                        $tSocket = stream_socket_client($tHost . ':' . $tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $tContext);
                        //if (!$tSocket)
                         //   exit();
                        //exit ("APNS Connection Failed: $error $errstr" . PHP_EOL);


                        $tMsg = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $tToken[0])) . pack('n', strlen($tBody)) . $tBody;
                        $tResult = fwrite($tSocket, $tMsg, strlen($tMsg));

                        //if ($tResult)
                        //echo 'Delivered Message to APNS' . PHP_EOL;
                        //else
                        //echo 'Could not Deliver Message to APNS' . PHP_EOL;
// Close the Connection to the Server.
                        fclose($tSocket);
                        //die;
                    }
                    // END IOS
                }
            }
        }
    }

    public function sendPushNotification($userDetails) {
        $userMapper = new Model_User();
        $woModel = new Model_WorkOrder();
        $userData = $userMapper->getAllAccountUserOfBuilding($userDetails['bid']);
        $userList = array();
        $androidPushIdList = array();
        $iosPushIdList = array();
        $status = array('1');
        $badges = array();
        if ($userData != '') {
            foreach ($userData as $userDataVal) {
                $userList[] = $userDataVal->uid;
                // Fetch all new work order of user
                $res = $woModel->getWorkorderbystatus($userDataVal->uid, $status);
                $badges[$userDataVal->uid] = $res[0]->count_workorder;
                // End Fetch new work order
            }
            $pushModel = new Model_PushNotification();
            $pushDetails = $pushModel->getPushId($userList);



            foreach ($pushDetails as $pushVal) {

                // Start Android
                if ($pushVal['device'] == 1) {
                    $registrationIds = $pushVal['push_id'];
                    $badgescount = $badges[$pushVal['user_id']];
                    $rand = rand(1, 100);
                    $rand = $rand + $badgescount;
                    $msg1 = $userDetails['buildingName'] . " - " . $userDetails['tenantName'];
                    $msg2 = $userDetails['workorder'] . " - " . $userDetails['categoryName'];
                    $msg3 = $userDetails['work_order_request'];
                    if (strlen($msg1) >= 48) {
                        $msg1 = $userDetails['buildingName'] . " - " . $userDetails['tenantName'];
                        $msg1 = substr($msg1, 0, 45);
                        $msg1 = $msg1 . '...';
                    }
                    if (strlen($msg2) >= 48) {
                        $msg2 = $userDetails['workorder'] . " - " . $userDetails['categoryName'];
                        $msg2 = substr($msg2, 0, 45);
                        $msg2 = $msg2 . '...';
                    }
                    if (strlen($msg3) >= 48) {
                        $msg3 = $userDetails['work_order_request'];
                        $msg3 = substr($msg3, 0, 45);
                        $msg3 = $msg3 . '...';
                    }


                    //$pushModel->updatePushId(array('badges'=>$$badgescount), $registrationIds);
                    $msg = array
                        (
                        //'message' 	=> 'Category- '.$userDetails['categoryName'] .', Building- '.$userDetails['buildingName'],
                        //'message' 	=> 'Work order '. $userDetails['workorder']. ' has been created',
                        'message' => $msg1 . "\n" . $msg2 . "\n" . $msg3,
                        //'title'		=> 'Work order '. $userDetails['workorder']. ' has been created',
                        'title' => 'Vision Work Order',
                        //'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
                        'vibrate' => 1,
                        'sound' => 1,
                        'largeIcon' => 'small_icon',
                        'smallIcon' => 'small_icon',
                        'badge' => $badgescount,
                        "notId" => $rand
                    );
                    $fields = array
                        (
                        'registration_ids' => array($registrationIds),
                        'data' => $msg,
                    );

                    $headers = array
                        (
                        'Authorization: key=' . API_ACCESS_KEY,
                        'Content-Type: application/json'
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    $result = curl_exec($ch);
                    curl_close($ch);
                }
                // End Android
                //Start IOS
                if ($pushVal['device'] == 2) {

                    $msg1 = $userDetails['buildingName'] . " - " . $userDetails['tenantName'];
                    $msg2 = $userDetails['workorder'] . " - " . $userDetails['categoryName'];
                    $msg3 = $userDetails['work_order_request'];
                    if (strlen($msg1) >= 40) {
                        $msg1 = $userDetails['buildingName'] . " - " . $userDetails['tenantName'];
                        $msg1 = substr($msg1, 0, 37);
                        $msg1 = $msg1 . '...';
                    }
                    if (strlen($msg2) >= 40) {
                        $msg2 = $userDetails['workorder'] . " - " . $userDetails['categoryName'];
                        $msg2 = substr($msg2, 0, 37);
                        $msg2 = $msg2 . '...';
                    }
                    if (strlen($msg3) >= 40) {
                        $msg3 = $userDetails['work_order_request'];
                        $msg3 = substr($msg3, 0, 37);
                        $msg3 = $msg3 . '...';
                    }


                    $registrationIds = $pushVal['push_id'];
                    $tToken[0] = $registrationIds;
                    $badgescount = $badges[$pushVal['user_id']];


                    $tHost = T_HOST;
                    $tPort = 2195;
                    $tCert = PEM_URL.'ck2.pem';
                    $tPassphrase = '1choc3747*1';
                    //$tAlert = 'Category- '.$userDetails['categoryName'] .', Building- '.$userDetails['buildingName'];
                    $tAlert = $msg1 . "\n" . $msg2 . "\n" . $msg3;
                    $tBadge = intval($badgescount);
                    $tSound = 'default';
                    $tPayload = 'APNS Message Handled by LiveCode';
                    $tBody = array();
                    $tBody['aps'] = array('alert' => $tAlert, 'badge' => $tBadge, 'sound' => $tSound,);
                    $tBody ['payload'] = $tPayload;
                    $tBody = json_encode($tBody);
                    $tContext = stream_context_create();
                    stream_context_set_option($tContext, 'ssl', 'local_cert', $tCert);
                    stream_context_set_option($tContext, 'ssl', 'passphrase', $tPassphrase);
                    $tSocket = stream_socket_client($tHost . ':' . $tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $tContext);
                    //if (!$tSocket)
                    //    exit();
                    //exit ("APNS Connection Failed: $error $errstr" . PHP_EOL);


                    $tMsg = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $tToken[0])) . pack('n', strlen($tBody)) . $tBody;
                    $tResult = fwrite($tSocket, $tMsg, strlen($tMsg));

                    //if ($tResult)
                    //echo 'Delivered Message to APNS' . PHP_EOL;
                    //else
                    //	echo 'Could not Deliver Message to APNS' . PHP_EOL;
// Close the Connection to the Server.
                    fclose($tSocket);
                }
                // END IOS
            }
        }
    }

    public function selecttenantAction() {
        //makes disable layout
        $this->_helper->getHelper('layout')->disableLayout();
        $bid = $this->_getParam('bid', '');
        $tnModel = new Model_Tenant();
        $tndetail = $tnModel->getTenantByBuildingId($bid);
        $this->view->tnData = $tndetail;
    }

    public function selectcategoryAction() {
        $this->_helper->getHelper('layout')->disableLayout();
        $bid = $this->_getParam('bid', '');
        $catModel = new Model_Category();
        $catdetail = $catModel->getBuildingCategoryList($bid);
        $this->view->catData = $catdetail;
    }

    public function selecttnuserAction() {
        $this->_helper->getHelper('layout')->disableLayout();
        $tId = $this->_getParam('tId', '');
        $tnuserModel = new Model_TenantUser();
        $tnuserdetail = $tnuserModel->getTenantUsers($tId);
        $tnModel = new Model_Tenant();
        $tndetail = $tnModel->getTenantById($tId);
        $this->view->tnuserData = $tnuserdetail;
        $this->view->tnData = $tndetail[0];
    }

    public function timezoneAction() {
        $this->_helper->getHelper('layout')->disableLayout();
        $bid = $this->_getParam('bid', '');
        $this->view->bid = $bid;
    }

    public function changecatAction() {
        $data = $this->getRequest()->getPost();
        $smsg = new Zend_Session_Namespace('message');
        $smsg->success_message = '';
        $smsg->error_message = '';
        if (isset($data['woId'])) {
            $curr_cat = $data['curr_cat'];
            $change_cat = $data['change_cat'];
            $woId = $data['woId'];


            $woModel = new Model_WorkOrder();
            $workOrder = $woModel->getWorkOrder($data['woId']);
            $this->setTimezone($workOrder[0]['building']);
            try {
                $whlModel = new Model_WoHistoryLog();
                $whData = array();
                $whData['woId'] = $data['woId'];
                $whData['log_type'] = 'category';
                $whData['current_value'] = $curr_cat;
                $whData['change_value'] = $change_cat;
                $whData['user_id'] = $this->userId;
                $whData['created_at'] = date('Y-m-d H:i:s');
                $insertWHL = $whlModel->insertHistoryLog($whData);

                $wData = array();
                $wData['category'] = $change_cat;

                $updateWO = $woModel->updateWorkOrder($wData, $data['woId']);
                $messsg = 'Category Successfully Re-assigned.';
                $smsg->success_message = $messsg;
                echo 'true';
            } catch (Exception $e) {
                $smsg->error_message = 'Error Occurred during re-assign category in work order';
                echo 'false';
            }
        }
        exit(0);
    }

    public function acknowledgeworkorderAction() {
        $data = $this->getRequest()->getPost();
        exit(0);
    }

    public function addnoteAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getPost();
        $this->view->woId = $data['woId'];
        $this->view->cust_id = $this->cust_id;
    }

    /* close add note form */

    public function editnoteAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getPost();
        $this->view->wnId = $data['wnId'];
        if ($data['wnId'] != '') {
            $wnModel = new Model_WoNote();
            $this->view->wnData = $wnModel->getWoNote($data['wnId']);
        } else {
            echo 'Invalid data';
            exit(0);
        }
    }

    /* close edit note form */
}

?>
