<?php

/**
 * Description of TenantController
 *
 * @author Anuj
 */
class TenantController extends Ve_Controller_Base {

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
        $this->buildingMapper = new Model_Building();
        $this->accessHelper = $this->_helper->access;
        $this->tenant_location = 12;
        $this->trecovery_location = 19;

         //called when object intiatate
         $this->tenantModel = new Model_Tenant();
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

        if(isset($this->userId) && !empty($this->userId)){
            $this->tenantuser = $this->tenantModel->getTenantByUser($this->userId); 
        }
    }

    public function indexAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $roleMapper = new Model_Role();
        $roleDetail = $roleMapper->getRole();

        $modules = new Model_Module();
        $modulesDetail = $modules->getModule();
        $build_ID = $this->_getParam('bid', '');
        //echo $this->cust_id;
        if (empty($build_ID) && isset($_COOKIE['build_cookie']))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        if ($this->roleId != 1) {
            $building = new Model_Building();
            $buildingDetail = $building->getbuildingbyid($build_ID);
        } else
            $buildingDetail = array();
        $this->view->role = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->cust_id = $this->cust_id;
        $this->view->companyListing = array(
            "roles" => $roleDetail,
            "modules" => $modulesDetail,
            "buildings" => $buildingDetail,
        );

        $this->view->acesshelper = $this->accessHelper;
        $this->view->tenant_location = $this->tenant_location;
        $this->view->select_build_id = $build_ID;
    }

    public function checktenantnameAction() {
        $tenantName = $this->_getParam('tenantName');
        $building = $this->_getParam('building');
        $tenantModel = new Model_Tenant();
        $tenantDetail = $tenantModel->checkTenantByName($tenantName, $building);

        if (!empty($tenantDetail))
            echo true;
        else
            echo false;

        exit(0);
    }

    public function checkexisttenantAction() {
        $tenantName = $this->_getParam('tenantName');
        $building = $this->_getParam('building');
        $tId = $this->_getParam('tId');
        $tenantModel = new Model_Tenant();
        $tenantDetail = $tenantModel->checkTenantByName($tenantName, $building, $tId);
        //var_dump($tenantDetail);
        if (!empty($tenantDetail))
            echo 'true';
        else
            echo 'false';

        exit(0);
    }

    public function updatetenantnameAction(){
        $name = $this->_getParam('name');
        $value = $this->_getParam('value');
        $building = $this->_getParam('building');
        $tId = $this->_getParam('pk');
        $tenantModel = new Model_Tenant();
        $tenantDetail = $tenantModel->checkTenantByName($value, $building, $tId);
        //var_dump($tenantDetail);
        if (!empty($tenantDetail))
            echo 'true';
        else {
            $data = array(
                $name => $value,
                'updateddate' => date('Y-m-d H:i:s')
            );
            if ($name != '' && !empty($name)) {
                $res = $tenantModel->updateTenant($data, $tId);
            }
            echo 'false';
        }

        exit(0);
    }

    public function checktenantAction() {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $email = $this->_getParam('email');

        $userModel = new Model_User();
        $userDetail = $userModel->isUserExist($email);
        //echo false;
        if (!empty($userDetail))
            echo true;
        else
            echo false;

        exit();
    }


    public function checktenantinfoAction() {
        $tid = $this->_getParam('tid');
        $tenantModel = new Model_Tenant();
        $userDetail = $tenantModel->checkTenantInfoByTenantGroupId($tid,$this->userId);
       return $userDetail;       
    }


    public function createtenantAction() {
        $data = $this->getRequest()->getPost();
        $tenantModel = new Model_Tenant();
        $userModel = new Model_User();
        $tenantUserModel = new Model_TenantUser();
        //$isExist = $userModel->tenantUserExist($data['email'],5);

        $isExist = $userModel->isUserExist($data['email']);

        if (!$isExist) {
            $buildingData = array();
            //$module = explode(',',$data['modules']);
            if (isset($data['building']) && $data['building'] != '') {
                $building = new Model_Building();
                $buildingDetail = $building->getbuildingbyid($data['building']);
                $buildingData = $buildingDetail[0];
            }
            /* if(isset($module) && count($module)>0){
              $modules = new Model_Module();
              $modulesDetail = $modules->getModuleListing($module);
              } */

            $tenantData['tenantName'] = addslashes($data['tenantName']);
            $tenantData['tenantContact'] = addslashes($data['tenantContact']);
            $tenantData['address1'] = addslashes($data['address1']);
            $tenantData['address2'] = addslashes($data['address2']);
            $tenantData['suite'] = addslashes($data['suite']);
            $tenantData['city'] = addslashes($data['city']);
            $accountmap = new Model_Account();
            $statename = $accountmap->getStatesByCode($data['state']);
            $tenantData['state'] = $statename[0]->state;
            $tenantData['state_code'] = addslashes($data['state']);
            $tenantData['postalCode'] = $data['postalCode'];
            $tenantData['phoneNumber'] = $data['phoneNumber'];
            $tenantData['phoneExt'] = $data['phoneExt'];
            $tenantData['billtoAddress'] = $data['billtoAddress'];
            $tenantData['status'] = $data['status'];
            /* $tenantData['faxNumber'] = $data['faxNumber'];
              $tenantData['attention'] = $data['attention']; */

            $tenantData['updateddate'] = date('Y-m-d H:i:s');

            $tenantData['buildingId'] = $data['building']; //implode(',', $data['building']);
            $tenantData['tenant_number'] = time();
            //$modules = implode(',', $module);


            $detail = array(
                "tenantName" => $data['tenantName'],
                "tenantUserName" => $data['firstName'] . ' ' . $data['lastName'],
                "tenantContact" => $data['tenantContact'],
                "phoneNumber" => $buildingData['phoneNumber'],
                "phoneExt" => $buildingData['phoneExt'],
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
            $userData['firstName'] = addslashes($data['firstName']);
            $userData['lastName'] = addslashes($data['lastName']);
            $userData['Title'] = addslashes($data['title']);
            $userData['phoneNumber'] = $data['officeNumber'];
            $userData['userName'] = $data['email'];
            $userData['password'] = md5($gpass);
            $userData['role_id'] = $data['access']; //tenant manager
            $userData['cust_id'] = $this->cust_id;
            $userData['regDate'] = date('Y-m-d H:i:s');

            $userData['uid'] = $userModel->insertUser($userData);
            $tenantData['userId'] = $userData['uid'];
            $tenant = $tenantModel->insertTenant($tenantData);

            /*             * ****Insert data in tenant user table ****** */
            $tenantUserData['userId'] = $userData['uid'];
            $tenantUserData['tenantId'] = $tenant;
            $tenantUserData['suite_location'] = $data['suite_location'];
            $tenantUserData['cc_enable'] = ($data['access'] == 5) ? 1 : 0; // 1 for tenant admin & 0 for tenant user
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
            /* if(isset($data['modules']) && $data['modules']!='' && isset($data['building'])){
              foreach($modulesDetail as $key => $module) {
              $detail['building'][] = array(
              "building"  =>   $this->getBuildingName($buildingDetail, $data['building']),
              "module"    =>   $module['module_name']
              );
              }
              } */

            if (!empty($userBuildingAccess)) {
                $Model_User_Building_Module = new Model_UserBuildingModule();
                $Model_User_Building_Module->updateBuildingModule($userBuildingAccess);
            }



            if ($tenant > 0) {
                $tuser = new Zend_Session_Namespace('tenant_user');
                $tuser->detail = $detail;
                $res = $this->getWelcomeLetter($detail);
               echo $res['content']; 
            } else {
                $res = false;
            }


           // print_r($res['content']);
        }
        exit();
    }

    public function usersAction() {
        
        
        $companyListing = '';
        $search_array = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $search_array['search_by'] = $data['search_by'];
            $search_array['search_value'] = $data['search_value'];
            $this->view->search = $search_array;
        }
        $msgId = $this->_getParam('msg', 0);
        $tId = $this->_getParam('tId', 0);

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
        foreach ($companyListing as $cl) {
            $buildIds[] = $cl['build_id'];
        }
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        $msg = '';
        if ($msgId == 1) {
            $msg = 'Tenant user has been created successfully.';
        }
        if ($msgId == 2) {
            $msg = 'Tenant user has been updated successfully.';
        }
        if ($msgId == 3) {
            $msg = 'Tenant has been deleted successfully.';
        }
        $tm = new Zend_Session_Namespace('tenant_message');
        if (!isset($tm->msg) && $msgId != 0) {
            $tm->msg = $msg;
            $tparam = ($tId != 0) ? '/tId/' . $tId : '';
            $this->_redirect('/tenant/users/bid/' . $build_ID . '' . $tparam);
        }
        $this->view->companyListing = $companyListing;
        $this->view->custID = $this->cust_id;
        $this->view->roleId = $this->roleId;
        $tenantList = '';
        $tenant = new Model_Tenant();
         
        //if(empty($search_array)){
            if ($this->roleId == '5') {
                $tenantList = $tenant->getTenantById($this->userId);
                $this->view->select_build_id = $build_ID;
            } else {
                if ($build_ID != '') {
                    $tenantList = $tenant->getTenantByBuildingId($build_ID);
                    $this->view->select_build_id = $build_ID;
                } else {
                    if ($companyListing != '') {
                        $tenantList = $tenant->getTenantByBuildingId($companyListing[0]['build_id']);
                        $this->view->select_build_id = $companyListing[0]['build_id'];
                        $build_ID=$companyListing[0]['build_id'];
                    }
                }
            }        
            $nottenant="";
            $show = $this->_getParam('show', '');
           // print_r($show);
           // die;
            if($show==""){
               $show=10; 
            }
            $search_array = array_map("addslashes", $search_array);
            $search_array = array_map("addslashes", $search_array);
            $search_array = array_map("addslashes", $search_array);
            if(!empty($search_array))
            $tenantList = $tenant->gettenantsearchresult($build_ID, $search_array);
            
          
            if($show!='all'){
                $page = $this->_getParam('page', 1);
                $pageObj = new Ve_Paginator();
                $paginator = $pageObj->fetchPageDataResult($tenantList, $page, $show);            
                $this->view->crDetails = $paginator;
                $this->view->tenantList = $paginator;
            }else{
                $this->view->tenantList = $tenantList;
            }
            $this->view->show=$show;
            $this->view->acesshelper = $this->accessHelper;
            $this->view->tenant_location = $this->tenant_location;
            
            $this->view->select_build_id = $build_ID;
            $this->view->tId = $tId;
            $this->view->userId = $this->userId;
    }

    public function tenantoptionsAction() {
        $companyListing = '';
        $search_array = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $search_array['search_by'] = $data['search_by'];
            $search_array['search_value'] = $data['search_value'];
            $this->view->search = $search_array;
        }
        $msgId = $this->_getParam('msg', 0);
        $tId = $this->_getParam('tId', 0);
        $tuid = $this->_getParam('id');
        $bid = $this->_getParam('bid');                  

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
       
        foreach ($companyListing as $cl) {
            $buildIds[] = $cl['build_id'];
        }
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        $msg = '';
        if ($msgId == 1) {
            $msg = 'Tenant user has been created successfully.';
        }
        if ($msgId == 2) {
            $msg = 'Tenant user has been updated successfully.';
        }
        if ($msgId == 3) {
            $msg = 'Tenant has been deleted successfully.';
        }
        $tm = new Zend_Session_Namespace('tenant_message');
        if (!isset($tm->msg) && $msgId != 0) {
            $tm->msg = $msg;
            $tparam = ($tId != 0) ? '/tId/' . $tId : '';
            $this->_redirect('/tenant/users/bid/' . $build_ID . '' . $tparam);
        }
        $this->view->companyListing = $companyListing;
        $this->view->custID = $this->cust_id;
        $this->view->roleId = $this->roleId;
        $tenantList = '';
        $tenant = new Model_Tenant();
         
        //if(empty($search_array)){
            if ($this->roleId == '5') {
                $tenantList = $tenant->getTenantById($this->userId);
                $this->view->select_build_id = $build_ID;
            } else {
                if ($build_ID != '') {
                    $tenantList = $tenant->getTenantByBuildingId($build_ID);
                    $this->view->select_build_id = $build_ID;
                } else {
                    if ($companyListing != '') {
                        $tenantList = $tenant->getTenantByBuildingId($companyListing[0]['build_id']);
                        $this->view->select_build_id = $companyListing[0]['build_id'];
                        $build_ID=$companyListing[0]['build_id'];
                    }
                }
            }        
            $nottenant="";
            $show = $this->_getParam('show', '');
           // print_r($show);
           // die;
            if($show==""){
               $show=10; 
            }
            $search_array = array_map("addslashes", $search_array);
            $search_array = array_map("addslashes", $search_array);
            $search_array = array_map("addslashes", $search_array);
            if(!empty($search_array))
            $tenantList = $tenant->gettenantsearchresult($build_ID, $search_array);
            
            if($show!='all'){
                $page = $this->_getParam('page', 1);
                $pageObj = new Ve_Paginator();
                $paginator = $pageObj->fetchPageDataResult($tenantList, $page, $show);            
                $this->view->crDetails = $paginator;
                $this->view->tenantList = $paginator;
            }else{
                $this->view->tenantList = $tenantList;
            }
            $this->view->show=$show;
            $this->view->acesshelper = $this->accessHelper;
            $this->view->tenant_location = $this->tenant_location;
            $this->view->select_build_id = $build_ID;
            $this->view->tId = $tId;
            $this->view->userId = $this->userId;
        
        


        unset($_COOKIE['by_wonumber']);
        $pmTemplate = new Model_PmTemplate();
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $cust_id = $_SESSION['Zend_Auth']['storage']->cust_id;
        $role_id = $_SESSION['Zend_Auth']['storage']->role_id;
        $companyListing = '';
        $buildingMapper = new Model_Building();
        if ($role_id == '9') {
            $companyListing = $buildingMapper->getCompanyBuilding($cust_id);
        } else {
            $user_build_mod = new Model_UserBuildingModule();

            $buildinglists = $user_build_mod->getUserBuildingIds($user_id);
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
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds))) {
            $build_ID = $_COOKIE['build_cookie'];
        } else {
            setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        }

        if ($companyListing != '') {
            if ($build_ID != '')
                $select_build_id = $build_ID;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }
        $page = $this->_getParam('page', 1);
        $show = $this->_getParam('show', '');
        if($show != ""){
            setcookie('show_limit', $show, 2147483647, '/');
        }else{
           $show =  $_COOKIE['show_limit'];
        }
        if($show==""){
            $show = 5;
        }
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        $workorderList = $pmTemplate->getWorkorderListByEquipment($select_build_id, $data = 0);
        $allEquipment = $pmTemplate->getallEquipmentNameByBuildId($select_build_id);
        $this->view->workorderList = $workorderList;
        
        //Email Template
        $email_group_model = new Model_EmailGroup();
        $emailGroup = $email_group_model->get_email_group_by_building_id_PM($select_build_id);
       
        $this->view->email_group = $emailGroup;
       
        $pageObj = new Ve_Paginator();
        $paginator = $pageObj->fetchPageDataResult($workorderList, $page, $show);
        
        $pmCompleteJobTime = $pmTemplate->getPmCompleteJobTime($select_build_id);
        $this->view->workorderList = $paginator;

        $this->view->allEquipment = $allEquipment;
        $this->view->custID = $cust_id;
        $this->view->userId = $user_id;
        $this->view->page = $page;
        $this->view->show = $show;
        $this->view->pmCompleteJobTime = $pmCompleteJobTime[0];

    }


    public function checktenantuseremailAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $email = $this->_getParam('email');
        $bid = $this->_getParam('bid');
		$tenantModel = new Model_Tenant();
		$tenantDetail = $tenantModel->checkTenantUserEmail($email,$bid);
        // check allow
        $data = array();
        $data['email'] = $email;
        $data['bid'] = $bid;
        $allowtenantDetail = $tenantModel->filterTenentMultiUserList($data);  
       


        $html = '';  
        $html .= '<tr>';                             
        $html .= '<th><strong>User Name</strong></th>';
        $html .= '<th><strong>Email Address</strong></th>';
        $html .= '<th><strong>Building Name</strong></th>';
        $html .= '<th><strong>Location</strong></th>';

         $html .= '</tr>';
        if(isset($tenantDetail[0]) && !empty($tenantDetail)){
       
        $html .= '<tr id="userdetail" class"userdetail" data-userid='.$tenantDetail[0]->UserID.'  data-buildingid='.$tenantDetail[0]->BuildingId.' data-tenantid='.$tenantDetail[0]->TenantId.'>';
        $html .= '<td>'.$tenantDetail[0]->User_First_Name .' '.$tenantDetail[0]->User_Last_Name.'</td>';
        $html .= '<td>'.$tenantDetail[0]->User_EMail.'</td>';
        $html .= '<td>'.$tenantDetail[0]->Building_Name.'</td>';
        $html .= '<td>'.$tenantDetail[0]->User_Suit_Location.'</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>';
            
        $html .= '<div class="tntopt-btn-section">
               <a id="byequipment" type="button" class="btn btn-csttm btn-success group-btn-custom" href="">Cancel</a>
               <a id="addtenantOption" type="button" class="btn btn-csttm btn-success" href="javascript:void(0);" onclick=addTenantOption('.$tenantDetail[0]->BuildingId.','.$tenantDetail[0]->UserID.','.$tenantDetail[0]->TenantId.');>Add</a>';
        }
        $html .= '</td>';
        $html .= '</tr>';

        if(empty($allowtenantDetail)){
          echo $html;
        }
     
	 }


    public function filtertenantoptionAction(){
        $this->_helper->layout()->disableLayout();
        $cust_id = $_SESSION['Zend_Auth']['storage']->cust_id;
        $role_id = $_SESSION['Zend_Auth']['storage']->role_id;
        $companyListing = '';
        $buildingMapper = new Model_Building();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $email = $this->_getParam('email');
        $data = $this->_request->getParams();
		$tenantModel = new Model_Tenant();
		$multiUserList = $tenantModel->filterTenentMultiUserList($data);
      
       // echo json_encode($tenantDetail);
       //$this->view->multiUserList = $multiUserList;
        $this->_helper->viewRenderer('sortmultiuserAction');
       

    }

    
   
    public function checkmultiusersAction(){

     
        $finalResultArr = array();
        $this->_helper->layout()->disableLayout();
        $email = $this->_getParam('email');
        $build_ID = $this->_getParam('bid');
        $tenantModel = new Model_Tenant();
        $data = $this->_request->getParams();
        $data['email'] = $email;
        $data['bid'] = $build_ID;
        $tenantDetail = $tenantModel->filterTenentMultiUserList($data);  
        if(isset($tenantDetail[0])){          
		$tenantDetailArr = $tenantModel->checkTenantUserEmail($email,$build_ID);
           echo json_encode((array)$tenantDetailArr[0]);
        }else{
            echo '0';
        }
       exit();
              
     }

     public function checktenaninfoAction(){
        $finalResultArr = array();
        $this->_helper->layout()->disableLayout();
        $email = $this->_getParam('email');
        $build_ID = $this->_getParam('bid');
        $tenantModel = new Model_Tenant();
        $data = $this->_request->getParams();
        $data['email'] = $email;
        $data['bid'] = $build_ID;
          if($email){          
            $tenantDetailArr = $tenantModel->checkTenantUserEmail($email);
            echo json_encode((array)$tenantDetailArr[0]);
            }else{
                echo '0';
            }
            exit();

     }

 public function sortmultiuserAction(){
    $finalResultArr = array();
    $this->_helper->layout()->disableLayout();
    $data = $this->_request->getParams();
    $email = $this->_getParam('email');
    $tenantModel = new Model_Tenant();
    $tenantDetail = $tenantModel->filterTenentMultiUserList($data);

    setcookie('multiuser_search_email', $email, time() + (86400 / 24), "/");
    

    foreach($tenantDetail as $rec){
        $temp_array = array();
        $temp_array['data']['allowed_user_data'] = $rec;
        $tenantlist =  $tenantModel->getTenatUserByUserid($rec->UserID);
        $temp_array['data']['list'] = $tenantlist;
        $finalResultArr[] = $temp_array;

    }
    $this->view->multiUserList = $tenantDetail;
    $this->view->finalResultArr = $finalResultArr;
    
 }

 public function addtenantusersAction(){
           $this->_helper->layout()->disableLayout();
         
            $tenantUserData = array();
            $tenantUserModel = new Model_TenantUser();
            $bid = $this->_getParam('bid');
            $uid = $this->_getParam('uid');
            $tid = $this->_getParam('tid');
            $ccenable = $this->_getParam('ccenable');
            $suite_location = $this->_getParam('suitelocation');
            $completenotification = $this->_getParam('completenotification');

            $tenantUserData['userId'] = $uid;
            $tenantUserData['tenantId'] = $tid;
            $tenantUserData['suite_location'] =  $suite_location;
            $tenantUserData['cc_enable'] = $ccenable;// 1 for tenant admin & 0 for tenant user
            $tenantUserData['send_as'] = 1; // HTML by default
            $tenantUserData['complete_notification'] = 0; // no by default
            $tenantUserModel->insertTenantUser($tenantUserData);
            
            echo 1;
 }

 public function addtenantoptionAction(){
    //echo 'test';
    $this->_helper->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    $bid = $this->_getParam('bid');
    $uid = $this->_getParam('uid');
    $tid = $this->_getParam('tid');

    $tenentdata = array();
    $tenentdata['UserID'] = (int)$uid;
    $tenentdata['BuidlingID'] = (int)$bid;
    $tenentdata['TenantID'] = (int)$tid;
   // echo $tenentdata['UserID']. '--'.$tenentdata['BuidlingID'].'--'.$tenentdata['TenantID'];
    $tenantOptionModel = new Model_TenantOption();
     // echo $tenentdata['UserID']. '--'.$tenentdata['BuidlingID'].'--'.$tenentdata['TenantID'];
    try{
     $tenantOptionModel->insertTenantOption($tenentdata);
     echo 1;
     exit(0);
    } catch (Exception $e) {
        echo $e->getMessage();  

    }

   

 }

    public function createuserAction() {
        $roleMapper = new Model_Role();
        $roleDetail = $roleMapper->getRole();

        $modules = new Model_Module();
        $modulesDetail = $modules->getModule();
        $buildingMapper = new Model_Building();
        $companyListing = array();
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
        $this->view->select_build_id = $build_ID;
        $this->view->role = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->cust_id = $this->cust_id;
        $this->view->companyListing = array(
            "roles" => $roleDetail,
            "modules" => $modulesDetail,
            "buildings" => $companyListing,
        );
    }

    public function createtenantsusersAction() {
        $data = $this->getRequest()->getPost();
        //print_r($data);
        //exit(0);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $tenantModel = new Model_Tenant();
            $userModel = new Model_User();
            $tenantUserModel = new Model_TenantUser();

            //$isExist = $userModel->tenantUserExist($data['email'],5);
            $isExist = $userModel->isUserExist($data['email']);
            if (!$isExist) {
                $module = explode(',', $data['modules']);
                if (isset($data['building']) && $data['building'] != '') {
                    $building = new Model_Building();
                    $buildingDetail = $building->getbuildingbyid($data['building']);
                }
                if (isset($module) && count($module) > 0) {
                    $modules = new Model_Module();
                    $modulesDetail = $modules->getModuleListing($module);
                }

                $tenantData['buildingId'] = $data['building'];
                $modules = implode(',', $module);
                $roleMapper = new Model_Role();
                $roleDetail = $roleMapper->getRole($data['access']);

                $detail = array(
                    "name" => $data['firstName'] . " " . $data['lastName'],
                    "office_phone" => $data['phoneNumber'],
                    "email" => $data['email'],
                    "username" => $data['userName'],
                    "access" => $roleDetail[0]['title'],
                    "firstName" => $data['firstName'],
                    "lastName" => $data['lastName'],
                );

                $gpass = $this->generateRandomString();
                $detail['gpass'] = $gpass;
                $userData['email'] = $data['email'];
                $userData['firstName'] = $data['firstName'];
                $userData['lastName'] = $data['lastName'];
                $userData['phoneNumber'] = $data['phoneNumber'];
                $userData['userName'] = $data['email'];
                $userData['password'] = md5($gpass);
                $userData['role_id'] = $data['access']; //tenant manager
                $userData['cust_id'] = $this->cust_id;
                $userData['regDate'] = date('Y-m-d H:i:s');
                $userDetail = $userModel->isUserExist($data['email']);
                if (!$userDetail) {
                    $userData['uid'] = $userModel->insertUser($userData);

                    $tenantUserDate['userId'] = $userData['uid'];
                    $tenantUserDate['tenantId'] = $this->userId;
                    $tenantUserModel->insertTenantUser($tenantUserDate);

                    $tenantData['userId'] = $userData['uid'];
                    if ($userData['uid'] > 0) {
                        $mail_flag = 1;
                    }
                }

                $userBuildingAccess = array();
                $detail['building'] = array();
                $userBuildingAccess = array();
                if (isset($data['building']) && $data['building'] != '') {
                    $userBuildingAccess[] = array(
                        "user_id" => $userData['uid'],
                        "building_id" => $data['building'],
                        "modules_id" => $data['modules'],
                        "assigned_date" => date('Y-m-d H:i:s'),
                        "last_update_date" => date('Y-m-d H:i:s'),
                    );
                }
                if (isset($data['modules']) && $data['modules'] != '' && isset($data['building'])) {
                    foreach ($modulesDetail as $key => $module) {
                        $detail['building'][] = array(
                            "building" => $this->getBuildingName($buildingDetail, $data['building']),
                            "module" => $module['module_name']
                        );
                    }
                }

                if (!empty($userBuildingAccess)) {
                    $Model_User_Building_Module = new Model_UserBuildingModule();
                    $Model_User_Building_Module->updateBuildingModule($userBuildingAccess);
                }

                if ($mail_flag == 1) {
                    try {
                        $res = $userModel->sendUserMail($detail);
                        $email_log = new Model_Log();
                        $logData = array();
                        $logData['email_sent_by'] = $this->userId;
                        $logData['userId'] = $userData['uid'];
                        $logData['email'] = $detail['email'];
                        $logData['log_type'] = 'email';
                        $logData['log_message'] = 'New user created by using add new user wizard';

                        if ($res) {
                            $logData['email_status'] = 1;
                            $email_log->insertLog($logData);
                        } else {
                            $logData['email_status'] = 0;
                            $email_log->insertLog($logData);
                        }
                    } catch (Exception $e) {
                        
                    }
                }
                echo json_encode($detail);
            }
        }
        exit(0);
    }

    public function userinfoAction() {
        $companyListing = '';
        $user_build_mod = new Model_UserBuildingModule();
        $buildinglists = $user_build_mod->getUserBuildingIds($this->userId);

        $build_ID = $this->_getParam('bid', '');
        /* if($companyListing!=''){
          if($build_ID!='')
          $select_build_id = $build_ID;
          else
          $select_build_id = $companyListing[0]['build_id'];
          } */
        $select_build_id = $buildinglists[0]['building_id'];
        $tenantUserModel = new Model_TenantUser();
        $tuserlist = $tenantUserModel->getTenantUsers($this->userId, $select_build_id);
        //$this->view->companyListing = $companyListing;
        $this->view->custID = $this->cust_id;
        $this->view->roleId = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->tuserlist = $tuserlist;
        $this->view->select_build_id = $select_build_id;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->user_info_id = 7;
    }

    public function updatetenantAction() {

        $tenantMapper = new Model_Tenant();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $id = $this->getRequest()->getPost('pk');
            $key = $this->getRequest()->getPost('name');
            $value = $this->getRequest()->getPost('value');
            $data = array(
                $key => $value,
                'updateddate' => date('Y-m-d H:i:s')
            );
            if ($key != '' && !empty($key)) {
                $res = $tenantMapper->updateTenant($data, $id);
            }
        }
        exit;
    }

    public function updatetenantuserAction() {

        $tenantMapper = new Model_TenantUser();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $id = $this->getRequest()->getPost('pk');
            $key = $this->getRequest()->getPost('name');
            $value = $this->getRequest()->getPost('value');
            $data = array(
                $key => $value,
                'updateddate' => date('Y-m-d H:i:s')
            );
            if ($key != '' && !empty($key)) {
                $res = $tenantMapper->updateTenantUser($data, $id);
            }
        }
        exit;
    }

    /**
     * Generate Random Password 
     */
    function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /*
     * Get Building Name
     */

    public function getBuildingName($buildingDetail, $building) {
        foreach ($buildingDetail as $key => $data) {
            if ($data['build_id'] == $building) {
                return $data['buildingName'];
            }
        }
    }

    public function sendWelcomeLetterNow($detail) {
        try {
            $mail_data = $this->getWelcomeLetter($detail);
            $build_ID = $detail['bid'];
            $content = $mail_data['content'];
            $subject = $mail_data['subject'];

            $mail = new Zend_Mail('utf-8');
            $mail->addTo($detail['email']);
            $mail->setSubject($subject);
            $setModel = new Model_Setting();
            $setData = $setModel->getSetting();
            
            
            
            if ($setData) {
                $setting = $setData[0];
                $mail->setFrom($setting['from_email'], $setting['from_name']);
                $return_path = new Zend_Mail_Transport_Sendmail('-f' . $setting['from_email']);
                
                //create all user email array 
                $toemail = array();
                $user_build_mod = new Model_User();
                $allusers = $user_build_mod->get_userfullinfoby_buildingId($build_ID);
                if(!empty($allusers)){
                    foreach($allusers as $val){
                        $toemail[] = $val->email;
                    }
                }
                $toemail[] = $setting['bcc_email'];
                if ($setting['bcc_email'])
                    $mail->addBcc($toemail);
            }else {
                $mail->setFrom('no-reply@visionworkorders.com', 'Vision Work Orders');
                $return_path = new Zend_Mail_Transport_Sendmail('-fno-reply@visionworkorders.com');
            }
            Zend_Mail::setDefaultTransport($return_path);

            $mail->setBodyHtml($content);
            $res = $mail->send();
            return $res;
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendwelcomeletterAction() {
        $tuser = new Zend_Session_Namespace('tenant_user');
        $build_ID = $this->_getParam('bid');
        $send_letter = $this->_getParam('send_letter');

        $detail = $tuser->detail;
        if ($detail != '') {
            /* $detail = array(
              "tenantName"    => 'Tenant Name',
              "tenantContact"    => 'Tenant Name',
              "phoneNumber"   => 'Tenant Name',
              "phoneExt"   => 'Tenant Name',
              "email"         => 'Tenant Name',
              "access"        => 'Tenant Manager',
              "address1"      => 'Tenant Name',
              "address2"      => 'Tenant Name',
              "suite"         => 'Tenant Name',
              "city"          => 'Tenant Name',
              "state"         => 'Tenant Name',
              "postalCode"    => 'Tenant Name',

              ); */
            $data = $this->getRequest()->getPost();
            /*             * ***Send Welcome mail **** */
            if (isset($send_letter) && $send_letter == '1') {
                $email_data = $this->getWelcomeLetter($detail);
                try {
                    $mail = new Zend_Mail('utf-8');
                    $content = $email_data['content'];
                    $subject = $email_data['subject'];
                    //create all user email array 
                    $toemail = array();
                    $user_build_mod = new Model_User();
                    $allusers = $user_build_mod->get_userfullinfoby_buildingId($build_ID);
                    if(!empty($allusers)){
                        foreach($allusers as $val){
                            $toemail[] = $val->email;
                        }
                    }
                    $toemail[] = $setting['bcc_email'];
                    // configure base stuff
                    $mail->addTo($detail['email']);
                    //$mail->addTo('brijeshkumar@virtualemployee.com');
                    $mail->setSubject($subject);
                    $setModel = new Model_Setting();
                    $setData = $setModel->getSetting();
                    if ($setData) {
                        $setting = $setData[0];
                        $mail->setFrom($setting['from_email'], $setting['from_name']);
                        $return_path = new Zend_Mail_Transport_Sendmail('-f' . $setting['from_email']);
                        if ($setting['bcc_email'])
                            $mail->addBcc($toemail);
                    }else {
                        $mail->setFrom('no-reply@visionworkorders.com', 'Vision Work Orders');
                        $return_path = new Zend_Mail_Transport_Sendmail('-fno-reply@visionworkorders.com');
                    }
                    Zend_Mail::setDefaultTransport($return_path);

                    $mail->setBodyHtml($content);
                    $res = $mail->send();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                $userModel = new Model_User();
                $userData = $userModel->checkUserEmail($detail['email']);

                $email_log = new Model_Log();
                $logData = array();
                $logData['email_sent_by'] = $this->userId;
                $logData['userId'] = $userData[0]['uid'];
                $logData['log_type'] = 'email';
                $logData['email'] = $detail['email'];
                $logData['log_message'] = 'Sent welcome letter to tenant';

                if ($res) {
                    $logData['email_status'] = 1;
                    $email_log->insertLog($logData);
                } else {
                    $logData['email_status'] = 0;
                    $email_log->insertLog($logData);
                }
            }
        }
        //$this->_redirect('/tenant/users/bid/'.$build_ID);
        $json_data['msg'] = "Mail successfully sent to tenant!";
        $json_data['url'] = '/tenant/users/bid/' . $build_ID;
        echo json_encode($json_data);
        exit;
    }

    /**
     * Get Welcome letter
     */
    public function getWelcomeLetter($detail) {
        $emailMapper = new Model_Email();
        $loadTemplate = $emailMapper->loadEmailTemplate(5);
        if ($loadTemplate) {
            $emailTemplate = $loadTemplate[0];
            $subject = $emailTemplate['email_subject'];
            $content = $emailTemplate['email_content'];
            /*             * ****get Company Name ***** */
            $accoutMapper = new Model_Account();
            $company = $accoutMapper->getcompany($this->cust_id);
            $companyName = $company[0]['companyName'];

            /*
              echo "<pre>";
              print_r($detail);
              print_r($company); die;
             */
            $header_data = $this->getHeaderData($company);
            $footer_data = $this->getFooterData();

            /*             * ****** get User Data *********** */
            $userModel = new Model_User();
            $userDetail = $userModel->getUserInfo($this->userId);
            $userData = $userDetail[0];
            $full_Name = stripslashes($userData->firstName) . ' ' . stripslashes($userData->lastName);
            if ($this->roleId == 5) {
                //$companyName = $full_Name;
            }
            //$roleManager = $userData->role_title;
            $roleManager = 'Company Admin';
            /*             * ***change the key with value in the content *** */
            $currDate = date('F d, Y');

            // Change Email subject

            $subject = str_replace('[[++currDate]]', $currDate, $subject);
            $subject = str_replace('[[++tenantUserName]]', $detail['tenantUserName'], $subject);
            $subject = str_replace('[[++tenantContact]]', $detail['tenantContact'], $subject);
            $subject = str_replace('[[++tenantName]]', $detail['tenantName'], $subject);
            $subject = str_replace('[[++address1]]', $detail['address1'], $subject);
            $subject = str_replace('[[++address2]]', $detail['address2'], $subject);
            $subject = str_replace('[[++city]]', $detail['city'], $subject);
            $subject = str_replace('[[++state]]', $detail['state'], $subject);
            $subject = str_replace('[[++postalCode]]', $detail['postalCode'], $subject);
            $subject = str_replace('[[++siteURL]]', BASEURL, $subject);
            $subject = str_replace('[[++email]]', $detail['email'], $subject);
            $subject = str_replace('[[++username]]', $detail['username'], $subject);
            $subject = str_replace('[[++password]]', $detail['gpass'], $subject);
            $subject = str_replace('[[++phoneNumber]]', $detail['phoneNumber'], $subject);


            $subject = str_replace('[[++dateTime]]', $header_data['date'], $subject);
            $subject = str_replace('[[++costNumber]]', $header_data['corp_account_number'], $subject);

            if ($detail['phoneExt'] != '')
                $subject = str_replace('[[++phoneExt]]', '( ' . $detail['phoneExt'] . ' )', $subject);
            else
                $subject = str_replace('[[++phoneExt]]', '', $subject);
            $subject = str_replace('[[++acCompanyName]]', $companyName, $subject);

            $subject = str_replace('[[++userFullName]]', $full_Name, $subject);
            $subject = str_replace('[[++userRole]]', $roleManager, $subject);

            // End Email subject




            $content = str_replace('[[++currDate]]', $currDate, $content);
            $content = str_replace('[[++tenantUserName]]', $detail['tenantUserName'], $content);
            $content = str_replace('[[++tenantContact]]', $detail['tenantContact'], $content);
            $content = str_replace('[[++tenantName]]', $detail['tenantName'], $content);
            $content = str_replace('[[++address1]]', $detail['address1'], $content);
            $content = str_replace('[[++address2]]', $detail['address2'], $content);
            $content = str_replace('[[++city]]', $detail['city'], $content);
            $content = str_replace('[[++state]]', $detail['state'], $content);
            $content = str_replace('[[++postalCode]]', $detail['postalCode'], $content);
            $content = str_replace('[[++siteURL]]', BASEURL, $content);
            $content = str_replace('[[++email]]', $detail['email'], $content);
            $content = str_replace('[[++username]]', $detail['username'], $content);
            $content = str_replace('[[++password]]', $detail['gpass'], $content);
            $content = str_replace('[[++phoneNumber]]', $detail['phoneNumber'], $content);
///// header 
            $content = str_replace('[[++companyLogo]]', $header_data['building_logo_src'], $content);
            $content = str_replace('[[++voctechLogo]]', $header_data['voctech_logo_src'], $content);
            $content = str_replace('[[++dateTime]]', $header_data['date'], $content);
            $content = str_replace('[[++costNumber]]', $header_data['corp_account_number'], $content);
///// end header
///// Footer 
            $content = str_replace('[[++footerInfo]]', $footer_data['footer_info'], $content);
///// End Footer

            if ($detail['phoneExt'] != '')
                $content = str_replace('[[++phoneExt]]', '( ' . $detail['phoneExt'] . ' )', $content);
            else
                $content = str_replace('[[++phoneExt]]', '', $content);
            $content = str_replace('[[++acCompanyName]]', $companyName, $content);

            $content = str_replace('[[++userFullName]]', $full_Name, $content);
            $content = str_replace('[[++userRole]]', $roleManager, $content);

            return array('content' => $content, 'subject' => $subject);
        } else
            return false;
    }

    public function getHeaderData($company) {
        $uri = BASEURL;
        /*         * *****Get voc-tech logo******* */
        $sdModel = new Model_SystemDefault();
        $sdData = $sdModel->getSystemDefault();
        $emailContent = $sdData[0];
        $voc_logo = $emailContent['voc_logo'];

        if (isset($voc_logo) && !empty($voc_logo)) {
            $voctech_logo_src = '<img src="' . $uri . 'public/images/uploads/' . $voc_logo . '">';
        } else {
            $voctech_logo_src = "";
        }


        /*         * *****Get Company Data******* */

        $accData = $company;
        $aData = $accData[0];

        $building_logo_src = "";

        // Company logo
        if (isset($aData['company_logo']) && !empty($aData['company_logo'])) {
            $building_logo_src = '<img src="' . $uri . 'public/images/clogo/' . $aData['company_logo'] . '">';
        } else {
            //$building_logo_src	=	'<img src="'.$uri.'/public/images/logo.png">';				
            $building_logo_src = '';
        }

        $data['building_logo_src'] = $building_logo_src;
        $data['voctech_logo_src'] = $voctech_logo_src;
        $data['corp_account_number'] = $aData['corp_account_number'];
        $data['date'] = $this->getDateFormat();
        return $data;
    }

    public function getFooterData() {
        $uri = BASEURL;
        /*         * *****Get voc-tech logo******* */
        $sdModel = new Model_SystemDefault();
        $sdData = $sdModel->getSystemDefault();
        $emailContent = $sdData[0];
        $footer_info = $emailContent['footer_info'];
        $emailSubject = $emailContent['subject'];

        $data['footer_info'] = $footer_info;
        //$data['subject']		=	$emailSubject;
        return $data;
    }

    public function getDateFormat($data = null) {
        if ($data == null)
            $data = date("Y-m-d h:i:s");

        return date("Y-m-d h:i:s", strtotime($data));
    }

    /*     * *
     * Show tenant user's detail
     */

    // public function tenantuserAction() {

       
    //  //   print_r($data);
    //  //   die;
    //     $msgId = $this->_getParam('msg', 0);
    //     $msg = '';
    //     if ($msgId == 1) {
    //         $msg = 'Tenant user has been created successfully.';
    //     }

    //     if ($msgId == 2) {
    //         $msg = 'Tenant user has been updated successfully.';
    //     }
    //     if ($msgId == 3) {
    //         $msg = 'Tenant has been deleted successfully.';
    //     }
    //     $tm = new Zend_Session_Namespace('tenant_message');
    //     if (!isset($tm->msg) && $msgId != 0) {
    //         $tm->msg = $msg;
    //         $this->_redirect('/tenant/tenantuser');
    //     }
    //     $tenant = new Model_Tenant();
    //     $tenantuser = array();
    //     $tId = $this->_getParam('company');

    //     $tenantuser =  $tenant->getTenantByUser($this->userId,$tId);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            er = $tenant->getTenantByTenantUser($this->userId);

    //     $tenantcompanyListArr = $tenant->getTenantCompanies($this->userId);
    //     //var_dump($tenantuser);
    //     $this->view->roleId = $this->roleId;
    //     $this->view->tenantuser = $tenantuser[0];
    //     $this->view->tenantcompanyListArr = $tenantcompanyListArr;
    // }
    public function tenantuserAction() {
        $msgId = $this->_getParam('msg', 0);
        $msg = '';
        if ($msgId == 1) {
            $msg = 'Tenant user has been created successfully.';
        }

        if ($msgId == 2) {
            $msg = 'Tenant user has been updated successfully.';
        }
        if ($msgId == 3) {
            $msg = 'Tenant has been deleted successfully.';
        }
        $tm = new Zend_Session_Namespace('tenant_message');
        if (!isset($tm->msg) && $msgId != 0) {
            $tm->msg = $msg;
            $this->_redirect('/tenant/tenantuser');
        }
        $tenant = new Model_Tenant();
        $tId = $this->_getParam('company');
        $tenantCompanyList = $tenant->getTenantCompanies($this->userId);
      
     
        if($tId){
            $set_cookie = setcookie('tenant_company', $tId, time() + (86400 / 24), "/");
            $tenantuser = $tenant->getTenanyUserByTenantGroup($tId);
            $this->view->tId = $tId;
        }
        else
        $tenantuser = $tenant->getTenantByUser($this->userId);
            
        $this->view->tenantGroupListArr = $tenantCompanyList;
        $this->view->roleId = $this->roleId;
       
        $this->view->tenantuser = $tenantuser[0];
        $this->view->tenantusers = $tenantuser;
    }

    /**
     * Load tenant user list
     */
    public function loadtenantuserAction() {
        
        $this->_helper->layout()->disableLayout();
        $tenant = new Model_TenantUser();
        $data = $this->getRequest()->getPost();
        $tenantId = $data['tId']; //50;
        $tenantMapper = new Model_Tenant();

        $tenantData = $tenantMapper->getTenantById($tenantId);
 
        $tenantuser = $tenant->getTenantUsers($tenantId);
   
       
        $modelMapper = new Model_Module();
        $moduleList = $modelMapper->getModule();
       
        $this->view->moduleList = $moduleList;
        $this->view->roleId = $this->roleId;
        $this->view->cust_id = $this->cust_id;
        $this->view->tenantuser = $tenantuser;
        $this->view->tenantData = $tenantData;
        $this->view->tenantId = $tenantId;
        $this->view->userId = $this->userId;
        $this->view->acesshelper = $this->accessHelper;
        $this->view->tenant_location = $this->tenant_location;

    }

    /**
     * Add new user under tenant
     */
    public function addtenanttuserAction() {
        $data = $this->getRequest()->getPost();
        //print_r($data);
        $message = array();
        $gpass = $this->generateRandomString();
        $userData = array();
        //$detail['gpass']    = $gpass;
        $userData['email'] = $data['email'];
        $userData['firstName'] = $data['firstName'];
        $userData['lastName'] = $data['lastName'];
        /* $userData['Title'] = $data['title']; */
        $userData['phoneNumber'] = $data['phone'];
        $userData['userName'] = $data['email'];
        $userData['password'] = md5($gpass);
        $userData['role_id'] = $data['access']; //tenant manager
        $userData['cust_id'] = $this->cust_id;
        $userData['regDate'] = date('Y-m-d H:i:s');
        $userModel = new Model_User();
        $tenantUserModel = new Model_TenantUser();
        $userDetail = $userModel->isUserExist($data['email']);
        //var_dump($userDetail);
        $tenantUserData = array();
        if (isset($data['buildId']) && $data['buildId'] != '') {
            $building = new Model_Building();
            $buildingDetail = $building->getbuildingbyid($data['buildId']);
        }
        $modList = explode(',', $data['modules']);
        if (isset($data['modules']) && count($modList) > 0) {
            $modules = new Model_Module();
            $modulesDetail = $modules->getModuleListing($modList);
        }
        if (!$userDetail) {
            try {
                $userData['uid'] = $userModel->insertUser($userData);
                $tenantUserData['userId'] = $userData['uid'];
                $tenantUserData['tenantId'] = $data['tenantId'];
                $tenantUserModel->insertTenantUser($tenantUserData);

                $tenantData['userId'] = $userData['uid'];
                $userBuildingAccess = array();
                if (isset($data['buildId']) && $data['buildId'] != '') {
                    $userBuildingAccess[] = array(
                        "user_id" => $userData['uid'],
                        "building_id" => $data['buildId'],
                        "modules_id" => $data['modules'],
                        "assigned_date" => date('Y-m-d H:i:s'),
                        "last_update_date" => date('Y-m-d H:i:s'),
                    );
                }
                if (!empty($userBuildingAccess)) {
                    $Model_User_Building_Module = new Model_UserBuildingModule();
                    $Model_User_Building_Module->updateBuildingModule($userBuildingAccess);
                }

                $roleMapper = new Model_Role();
                $roleDetail = $roleMapper->getRole($data['access']);


                $detail = array(
                    "name" => $data['firstName'] . " " . $data['lastName'],
                    "title" => $data['title'],
                    "office_phone" => $data['phoneNumber'],
                    "email" => $data['email'],
                    "username" => $data['userName'],
                    "access" => $roleDetail[0]['title'],
                    "firstName" => $data['firstName'],
                    "lastName" => $data['lastName'],
                    "gpass" => $gpass,
                );

                if (isset($data['modules']) && $data['modules'] != '' && isset($data['buildId'])) {
                    foreach ($modulesDetail as $key => $module) {
                        $detail['building'][] = array(
                            "building" => $this->getBuildingName($buildingDetail, $data['buildId']),
                            "module" => $module['module_name']
                        );
                    }
                }
                if ($userData['uid'] > 0) {
                    $res = $userModel->sendUserMail($detail);
                    $email_log = new Model_Log();
                    $logData = array();
                    $logData['email_sent_by'] = $this->userId;
                    $logData['userId'] = $userData['uid'];
                    $logData['email'] = $detail['email'];
                    $logData['log_type'] = 'email';
                    $logData['log_message'] = 'New user created by using add new user wizard';

                    if ($res) {
                        $logData['email_status'] = 1;
                        $email_log->insertLog($logData);
                    } else {
                        $logData['email_status'] = 0;
                        $email_log->insertLog($logData);
                    }
                }
                $message['status'] = 'success';
                $message['msg'] = 'New User has been created.';
            } catch (Exception $e) {
                $message['status'] = 'error';
                $message['msg'] = 'Some error occurred during create new user.';
            }
        } else {
            $message['status'] = 'email_error';
            $message['msg'] = 'This email id is exists.';
        }
        echo json_encode($message);
        exit(0);
    }

    public function loadedittuserAction() {
        $data = $this->getRequest()->getPost();
        $tuserId = $data['tuserId'];
        $tenantId = $data['tId'];
        $userModel = new Model_User();
        $userDetail = $userModel->getUserById($tuserId);
        $tuser_template = new Zend_View();
        $tuser_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/tenant/');
        $tuser_template->assign('roleId', $this->roleId);
        $tuser_template->assign('cust_id', $this->cust_id);
        $tuser_template->assign('userDetail', $userDetail[0]);
        $tuser_template->assign('tenantId', $tenantId);
        $bodyText = $tuser_template->render('edittuser.phtml');
        echo $bodyText;
        exit(0);
    }

    public function edittuserAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $tuId = $this->_getParam('tuId');
        $locationuser = $this->_getParam('locationuser');       
        $message = array();
        $tenantUserModel = new Model_TenantUser();
        $tenant = new Model_Tenant();
        $tenantData = $tenantUserModel->getTenantUserById($tuId);
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            //print_r($data); die;
            $uid = $data['uid'];
            $userData = array();
            $userData['userName'] = html_entity_decode($data['userName']);
            $userData['email'] = html_entity_decode($data['email']);
            $userData['firstName'] = addslashes($data['firstname']);
            $userData['lastName'] = addslashes($data['lastname']);
            $userData['phoneNumber'] = $data['phone'];
            $userData['role_id'] = $data['access'];
            $userData['regDate'] = date('Y-m-d H:i:s');
            $userData['status'] = $data['status'];
            $userData['note_notification'] = addslashes($data['note_notification']);
            $send_email = 0;
            if (isset($data['auto']) && $data['auto'] == 1) {
                $gpass = $this->generateRandomString();
                $userData['password'] = md5($gpass);
                $data['password'] = $gpass;
                $send_email = 1;
            } else if (!empty($data['password'])) {
                $userData['password'] = md5($data['password']);
                $send_email = 1;
            } else {
                $data['password'] = '';
            }

            if ($tenantData[0]->userName != $data['userName'])
                $send_email = 1;

            if ($tenantData[0]->email != $data['email'])
                $send_email = 1;


            $userModel = new Model_User();
            $userNameDetail = $userModel->checkUserName($data['userName'], $uid);
            $userEmailDetail = $userModel->checkUserEmail($data['email'], $uid);
            if (!$userNameDetail && !$userEmailDetail) {
                try {
                    $userData['uid'] = $userModel->updateUser($userData, $uid);

                    if (isset($_FILES['file']['name'])) {
                        $uploaddir = BASE_PATH . 'public/user_img/';
                        $uploadfile_name = 'WO-' . time() . '-' . basename($_FILES['file']['name']);
                        $uploadfile = $uploaddir . '' . $uploadfile_name;
                        if (!file_exists($uploaddir)) {
                            mkdir($uploaddir, 0777, true);
                        }
                        move_uploaded_file($_FILES["file"]["tmp_name"], $uploadfile);
                        $file_name = $uploadfile_name;
                        try {
                            $userModel->updateUser(array('user_img' => $uploadfile_name), $data['uid']);
                            $message['status'] = 'success';
                            $message['msg'] = 'User edit successfully.';
                        } catch (Exception $e) {
                            $message['status'] = 'error';
                            $message['msg'] = 'Error occurred during user edit.';
                        }
                    }




                    $id = $data['id'];
                    $tenantUserData['suite_location'] = $data['suite_location'];
                    $tenantUserData['cc_enable'] = $data['cc_enable'];
                    $tenantUserData['send_as'] = $data['send_as'];
                    $tenantUserData['complete_notification'] = $data['complete_notification'];


                    if ($data['welcome_letter'] == 1) {
                        $this->sendemailAction(true, $data['uid'], $data['tenantId'], $data['password']);
                    } elseif ($send_email == 1) {
                        $this->sendemailAction(true, $data['uid'], $data['tenantId'], $data['password']);
                    }

                    $tenantUserModel->updateTenantUser($tenantUserData, $id);
                    $build_ID = $data['building'];
                    $tId = $data['tenantId'];
                    if ($this->roleId == 5) {
                        //$this->_redirect('/tenant/tenantuser/msg/2');
                        $json_data['msg'] = "Record successfully updated!";
                        $json_data['url'] = '/tenant/currentusers/msg/2';
                        echo json_encode($json_data);
                        exit;
                    } else
                    //$this->_redirect('/tenant/users/bid/'.$build_ID.'/tId/'.$tId.'/msg/2');
                        $json_data['msg'] = "Record successfully updated!";
                    $json_data['url'] = '/tenant/users/bid/' . $build_ID . '/tId/' . $tId . '/msg/2';
                    echo json_encode($json_data);
                    exit;
                } catch (Exception $e) {
                    $message['msg'] = 'Error occured';
                }
            } else
                $message['msg'] = 'Error occured';
        }
        if ($tenantData) {
            $tuserDetail = $tenantData[0];
            $tenantData = $tenant->getTenantById($tuserDetail->tenantId);
            $this->view->roleId = $this->roleId;
            $this->view->tenantId = $tuserDetail->tenantId;
            $this->view->tenantData = $tenantData[0];
            $this->view->tuserDetial = $tuserDetail;
            $this->view->userId = $this->userId;
           

        } else
            $message['msg'] = 'Invalid Data';
        $this->view->locationuser = $locationuser;
        $this->view->message = $message;
    }

    public function addtuserAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $message = array();
        $tId = $this->_getParam('tId');
        $tenant = new Model_Tenant();
        $tenantData = $tenant->getTenantById($tId);

      

        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            //print_r($data);
            $message = array();
            $gpass = $this->generateRandomString();
            $userData = array();
            //$detail['gpass']    = $gpass;
            $userData['email'] = html_entity_decode($data['email']);
            $userData['firstName'] = addslashes($data['firstname']);
            $userData['lastName'] = addslashes($data['lastname']);
            /* $userData['Title'] = $data['title']; */
            $userData['phoneNumber'] = $data['phone'];
            $userData['userName'] = html_entity_decode($data['email']);
            $userData['password'] = md5($gpass);
            $userData['role_id'] = $data['access']; //tenant manager
            $userData['cust_id'] = $this->cust_id;
            $userData['regDate'] = date('Y-m-d H:i:s');
            $userData['status'] = $data['status'];
            $userModel = new Model_User();
            $tenantUserModel = new Model_TenantUser();
            $userDetail = $userModel->isUserExist($data['email']);
            echo json_encode($userDetail);
        
            if (!$userDetail) {
                try {
                    $userData['uid'] = $userModel->insertUser($userData);
                    $tenantUserData['userId'] = $userData['uid'];
                    $tenantUserData['tenantId'] = $data['tenantId'];
                    $tenantUserData['suite_location'] = $data['suite_location'];
                    $tenantUserData['cc_enable'] = $data['cc_enable'];
                    $tenantUserData['send_as'] = $data['send_as'];
                    $tenantUserData['complete_notification'] = $data['complete_notification'];
                    $tenantUserModel->insertTenantUser($tenantUserData);

                    $tenantData['userId'] = $userData['uid'];

                    $userBuildingAccess = array();
                    $userBuildingAccess[] = array(
                        "user_id" => $userData['uid'],
                        "building_id" => $data['building'],
                        "modules_id" => '0',
                        "assigned_date" => date('Y-m-d H:i:s'),
                        "last_update_date" => date('Y-m-d H:i:s'),
                    );

                    if (!empty($userBuildingAccess)) {
                        $Model_User_Building_Module = new Model_UserBuildingModule();
                        $Model_User_Building_Module->updateBuildingModule($userBuildingAccess);
                    }

                    $buildingData = array();
                    //$module = explode(',',$data['modules']);
                    if (isset($data['building']) && $data['building'] != '') {
                        $building = new Model_Building();
                        $buildingDetail = $building->getbuildingbyid($data['building']);
                        $buildingData = $buildingDetail[0];
                    }
                    $roleMapper = new Model_Role();
                    $roleDetail = $roleMapper->getRole($data['access']);

                    $tdata = (array) $tenantData[0];
                    $detail = array(
                        "tenantName" => stripslashes($tdata['tenantName']),
                        "tenantUserName" => stripslashes($data['firstname']) . ' ' . stripslashes($data['lastname']),
                        "tenantContact" => $tdata['tenantContact'],
                        "phoneNumber" => $buildingData['phoneNumber'],
                        "phoneExt" => $buildingData['phoneExt'],
                        "email" => $data['email'],
                        "username" => $data['email'],
                        "address1" => stripslashes($tdata['address1']),
                        "address2" => stripslashes($tdata['address2']),
                        "suite" => $tdata['suite'],
                        "city" => $tdata['city'],
                        "state" => $tdata['statename'],
                        "postalCode" => $tdata['postalCode'],
                    );
                    $detail['gpass'] = $gpass;
                    $detail['bid']   = $data['building'];
                    $msg = 1;
                    if ($data['welcome_letter'] == 1) {
                        try {
                            $res = $this->sendWelcomeLetterNow($detail);

                            $userLData = $userModel->checkUserEmail($data['email']);

                            $email_log = new Model_Log();
                            $logData = array();
                            $logData['email_sent_by'] = $this->userId;
                            $logData['userId'] = $userLData[0]['uid'];
                            $logData['email'] = $data['email'];
                            $logData['log_type'] = 'email';
                            $logData['log_message'] = 'Sent welcome letter to tenant user in create new tenant user';

                            if ($res) {
                                $logData['email_status'] = 1;
                                $email_log->insertLog($logData);
                            } else {
                                $logData['email_status'] = 0;
                                $email_log->insertLog($logData);
                            }
                        } catch (Exception $e) {
                            echo 'Mail not sending';
                        }
                    }
                    $message['status'] = 'success';
                    $message['msg'] = 'New User has been created.';
                    $build_ID = $tdata['buildingId'];
                    if ($this->roleId == 5) {
                        //$this->_redirect('/tenant/tenantuser/msg/1');
                        $json_data['msg'] = "New User has been created.";
                        //tenant admin 
                        $json_data['url'] = '/tenant/currentusers/msg/1';
                        echo json_encode($json_data);
                        exit;
                    } else
                    //$this->_redirect('/tenant/users/bid/'.$build_ID.'/tId/'.$tId.'/msg/1');
                        $json_data['msg'] = "New User has been created.";
                    $json_data['url'] = '/tenant/users/bid/' . $build_ID . '/tId/' . $tId . '/msg/1';
                    echo json_encode($json_data);
                    exit;
                } catch (Exception $e) {
                    echo $e->getMessage();
                    $message['status'] = 'error';
                    $message['msg'] = 'Some error occurred during create new user.';
                }
            }else if( isset($tenantUserData['tenantId']) && $tenantUserData['tenantId']!= $tId){
               
                $tenantUserData['userId'] = $userData['uid'];
                $tenantUserData['tenantId'] = $data['tenantId'];
                $tenantUserData['suite_location'] = $data['suite_location'];
                $tenantUserData['cc_enable'] = $data['cc_enable'];
                $tenantUserData['send_as'] = $data['send_as'];
                $tenantUserData['complete_notification'] = $data['complete_notification'];
                $tenantUserModel->insertTenantUser($tenantUserData);

            }
            
            else {
                $message['status'] = 'email_error';
                $message['msg'] = 'This email id is exists.';
            }
        }
        $this->view->roleId = $this->roleId;
        $this->view->tenantId = $tId;
        $this->view->tenantData = $tenantData[0];
        $this->view->message = $message;
    }

    public function sendTenantMail($detail) {

        // $config = array('ssl' => 'tls', 'port' => 587, 'auth' => 'login', 'username' => 'anujatazularc@gmail.com', 'password' => '2j86mbday');
        // $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
        try {
            $email_data = $this->getWelcomeLetter($detail);
            $mail = new Zend_Mail('utf-8');
            $mail->addTo($detail['email']);
            //$mail->addTo('brijeshkumar@virtualemployee.com');
            $esubject = $email_data['subject'];
            $econtent = $email_data['content'];
            $mail->setSubject($esubject);
            $setModel = new Model_Setting();
            $setData = $setModel->getSetting();
            if ($setData) {
                $setting = $setData[0];
                $mail->setFrom($setting['from_email'], $setting['from_name']);
                array_push($detail['allemail'],$setting['bcc_email']);
                $return_path = new Zend_Mail_Transport_Sendmail('-f' . $setting['from_email']);
                if ($setting['bcc_email'])
                    $mail->addBcc($detail['allemail']);
            }else {
                $mail->setFrom('no-reply@visionworkorders.com', 'Vision Work Orders');
                $return_path = new Zend_Mail_Transport_Sendmail('-fno-reply@visionworkorders.com');
            }
            Zend_Mail::setDefaultTransport($return_path);

            $mail->setBodyHtml($econtent);
            if ($mail->send())
                return true;
            else
                return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendtenantemailAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->getRequest()->getPost();
        $tenantId = $data['tid'];



        $userModel = new Model_User();
        $tenantUserModel = new Model_TenantUser();
        $tenant = new Model_Tenant();
        $gpass = $this->generateRandomString();
        $tenantData = $tenant->getTenantById($tenantId);
        $userData = $tenantUserModel->getTenantUserById($tenantId);


        $tenantData = $tenant->getTenantById($tenantId);
        $tenantuser = $tenantUserModel->getTenantUsers($tenantId);
        $buildingData = array();
        $building_phoneNumber = '';
        $building_phoneExt = '';
        //$module = explode(',',$data['modules']);
        if (isset($tenantData[0]->buildingId)) {
            $building = new Model_Building();
            $buildingDetail = $building->getbuildingbyid($tenantData[0]->buildingId);
            if ($buildingDetail) {
                $buildingData = $buildingDetail[0];
                $building_phoneNumber = $buildingData['phoneNumber'];
                $building_phoneExt = $buildingData['phoneExt'];
            }
        }

        $i = 0;
        $res = array();

        foreach ($tenantuser as $key => $value) {
            $detail = array(
                "tenantName" => $tenantData[0]->tenantName,
                "tenantContact" => $tenantData[0]->tenantContact,
                "tenantUserName" => $value->firstName . ' ' . $value->lastName,
                "phoneNumber" => $building_phoneNumber,
                "phoneExt" => $building_phoneExt,
                "email" => $value->email,
                "username" => $value->userName,
                //"access"        => 'Tenant Manager',					
                "address1" => $tenantData[0]->address1,
                "address2" => $tenantData[0]->address2,
                "suite" => $tenantData[0]->suite,
                "city" => $tenantData[0]->city,
                "state" => $tenantData[0]->state,
                "postalCode" => $tenantData[0]->postalCode,
            );
            $gpass = $this->generateRandomString();
            $detail['gpass'] = $gpass;
            
            $toemail = array();
            $user_build_mod = new Model_User();
            $allusers = $user_build_mod->get_userfullinfoby_buildingId($tenantData[0]->buildingId);
            if(!empty($allusers)){
                foreach($allusers as $val){
                    $toemail[] = $val->email;
                }
            }
            $detail['allemail'] = $toemail;
            
            $resp = $res[$i++] = $this->sendTenantMail($detail);

            $userModel = new Model_User();
            $userData = $userModel->checkUserEmail($value->email);

            $email_log = new Model_Log();
            $logData = array();
            $logData['email_sent_by'] = $this->userId;
            $logData['userId'] = $userData[0]['uid'];
            $logData['email'] = $value->email;
            $logData['log_type'] = 'email';
            $logData['log_message'] = 'Sent welcome letter to tenant user';

            if ($resp) {
                $logData['email_status'] = 1;
                $email_log->insertLog($logData);
            } else {
                $logData['email_status'] = 0;
                $email_log->insertLog($logData);
            }

            if (count($res) >= 1) {
                $userModel->changePassword($gpass, $value->uid);
            }
        }

        if ($i == count($res))
            echo true;
        else
            false;

        exit();
    }

    public function sendemailAction($welcome_letter = false, $userId = '', $tenantId = '', $password = '') {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->getRequest()->getPost();

        if (!$welcome_letter) {
            $userId = $data['uid'];
            $tenantId = $data['tid'];
        }


        $userModel = new Model_User();
        $tenantUserModel = new Model_TenantUser();
        $tenant = new Model_Tenant();
        $gpass = $this->generateRandomString();
        $tenantData = $tenant->getTenantById($tenantId);
        $userData = $tenantUserModel->getTenantUserById($userId);
        $buildingData = array();
        $building_phoneNumber = '';
        $building_phoneExt = '';
        //$module = explode(',',$data['modules']);
        if (isset($tenantData[0]->buildingId)) {
            $building = new Model_Building();
            $buildingDetail = $building->getbuildingbyid($tenantData[0]->buildingId);
            if ($buildingDetail) {
                $buildingData = $buildingDetail[0];
                $building_phoneNumber = $buildingData['phoneNumber'];
                $building_phoneExt = $buildingData['phoneExt'];
            }
        }

        $detail = array(
            "tenantName" => $tenantData[0]->tenantName,
            "tenantContact" => $tenantData[0]->tenantContact,
            "tenantUserName" => $userData[0]->firstName . ' ' . $userData[0]->lastName,
            "phoneNumber" => $building_phoneNumber,
            "phoneExt" => $building_phoneExt,
            "email" => $userData[0]->email,
            "username" => $userData[0]->userName,
            //"access"        => 'Tenant Manager',					
            "address1" => $tenantData[0]->address1,
            "address2" => $tenantData[0]->address2,
            "suite" => $tenantData[0]->suite,
            "city" => $tenantData[0]->city,
            "state" => $tenantData[0]->state,
            "postalCode" => $tenantData[0]->postalCode,
        );

        if (!empty($password)) {
            $detail['gpass'] = $password;
        } else {
            $detail['gpass'] = $gpass;
        }
        
        $toemail = array();
        $user_build_mod = new Model_User();
        $allusers = $user_build_mod->get_userfullinfoby_buildingId($tenantData[0]->buildingId);
        if(!empty($allusers)){
            foreach($allusers as $val){
                $toemail[] = $val->email;
            }
        }
        $detail['allemail'] = $toemail;
        
        $res = $this->sendTenantMail($detail);

        $userModel = new Model_User();
        $userData = $userModel->checkUserEmail($detail['email']);

        $email_log = new Model_Log();
        $logData = array();
        $logData['email_sent_by'] = $this->userId;
        $logData['userId'] = $userData[0]['uid'];
        $logData['email'] = $detail['email'];
        $logData['log_type'] = 'email';
        $logData['log_message'] = 'Sent welcome letter to tenant user';

        if ($res) {
            $logData['email_status'] = 1;
            $email_log->insertLog($logData);
        } else {
            $logData['email_status'] = 0;
            $email_log->insertLog($logData);
        }

        if ($res && empty($password)) {
            if ($userModel->changePassword($gpass, $userId)) {
                if (!$welcome_letter)
                    echo true;
                else
                    return true;
            } else
                echo false;
        }
        else if ($welcome_letter)
            return true;
        else
            echo false;

        exit();
    }

    public function deletetuserAction() {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $uId = $data['uId'];
            $message = array();
            if (isset($uId) && $uId != '') {
                try {
                    $userMapper = new Model_User();
                    $deleteUser = $userMapper->deleteUser($uId);

                    if ($deleteUser) {
                        $updateData['userStatus'] = '1';
                        $tenantMapper = new Model_Tenant();
                        $tenantMapper->updateTenant($updateData, $data['tId']);
                    }
                    $tm = new Zend_Session_Namespace('tenant_message');
                    $tm->msg = 'Tenant user deleted successfully.';
                    $message['msg'] = 'true';
                } catch (Exception $e) {
                    $message['msg'] = 'false';
                }
            } else {
                $message['msg'] = 'false';
            }
        }

        echo json_encode($message);
        exit(0);
    }

    public function deletetenantAction() {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $tId = $data['tId'];
            $message = array();
            if (isset($tId) && $tId != '') {
                try {
                    $tenantMapper = new Model_Tenant();
                    $deletetenant = $tenantMapper->deleteTenant($tId);

                    $message['msg'] = 'true';
                } catch (Exception $e) {
                    $message['msg'] = 'false';
                }
            } else {
                $message['msg'] = 'false';
            }
        }

        echo json_encode($message);
        exit(0);
    }

    public function tenantrecoveryAction() {

        $tenantMapper = new Model_Tenant();

        $companyListing = '';

        $msgId = $this->_getParam('msg', 0);
        $tId = $this->_getParam('tId', 0);
        $build_ID = $this->_getParam('bid');
        if (empty($build_ID) && isset($_COOKIE['build_cookie']))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        $msg = '';

        if ($msgId == 1) {
            $msg = 'Tenant user has been activated successfully.';
        }


        $tm = new Zend_Session_Namespace('tenant_message');
        if (!isset($tm->msg) && $msgId != 0) {
            $tm->msg = $msg;
            $tparam = ($tId != 0) ? '/tId/' . $tId : '';
            $this->_redirect('/tenant/tenantrecovery/bid/' . $build_ID . '' . $tparam);
        }

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


        $this->view->companyListing = $companyListing;
        $this->view->custID = $this->cust_id;
        $this->view->roleId = $this->roleId;

        $tenantList = '';
        $tenant = new Model_Tenant();


        if ($this->roleId == '5') {
            $tenantList = $tenant->getTenantById($this->userId, true);
            $this->view->select_build_id = $build_ID;
        } else {
            if ($build_ID != '') {
                $tenantList = $tenant->getTenantByBuildingId($build_ID, true);
                $this->view->select_build_id = $build_ID;
            } else {
                if ($companyListing != '') {
                    $tenantList = $tenant->getTenantByBuildingId($companyListing[0]['build_id'], true);
                    $this->view->select_build_id = $companyListing[0]['build_id'];
                }
            }
        }

        $this->view->acesshelper = $this->accessHelper;
        $this->view->trecovery_location = $this->trecovery_location;
        $this->view->tenantList = $tenantList;
        $this->view->tId = $tId;
        $this->view->userId = $this->userId;
    }

   

    public function loadtenantinactiveuserAction() {
        $this->_helper->layout()->disableLayout();
        $tenant = new Model_TenantUser();
        //$tenantuser = $tenant->getTenantUsers($this->userId);
        $data = $this->getRequest()->getPost();
        $userId = $data['tId']; //50;
        $buildId = $data['bId'];
        $tenantMapper = new Model_Tenant();
        $tenantData = $tenantMapper->getTenantById($userId, true);
        $tenantuser = $tenant->getTenantUsers($userId, '', true);

        $modelMapper = new Model_Module();
        $moduleList = $modelMapper->getModule();


        $this->view->roleId = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->cust_id = $this->cust_id;
        $this->view->tenantuser = $tenantuser;
        $this->view->tenantId = $userId;
        $this->view->tenantData = $tenantData;
        $this->view->moduleList = $moduleList;
        $this->view->buildId = $buildId;
        $this->view->acesshelper = $this->accessHelper;
        $this->view->trecovery_location = $this->trecovery_location;
    }

    public function activetenantuserAction() {
        $data = $this->getRequest()->getPost();
        $user = new Model_User();
        $tenant = new Model_Tenant();
        $usersId = $data['tenantUser'];
        $tenantId = $data['main_tenant'];
        $build_ID = $data['buildID'];
        $userData['remove_status'] = 0;

        $i = 0;
        $result = false;
        if (!empty($usersId)) {
            foreach ($usersId as $key => $id) {
                $result[$i++] = $user->updateUser($userData, $id);
            }
        }



        if ($i == count($result)) {
            if ($data['totalUser'] == count($data['tenantUser'])) {
                $tenantData['userStatus'] = 0;
                $tenant->updateTenant($tenantData, $tenantId);
            }
            $this->_redirect('/tenant/tenantrecovery/bid/' . $build_ID . '/tId/' . $tenantId . '/msg/1');
        }
    }

    public function recoveruserAction() {
        $id = $this->_getParam('id');

        $tenant = new Model_Tenant();
        $user = new Model_User();
        $tenantUserModel = new Model_TenantUser();

        $tenantuser = $tenantUserModel->getTenantUsers($id, '', true);
        $data['remove_status'] = '0';
        $res = $tenant->updateTenant($data, $id);
        $res = 1;
        $i = 0;
        if ($res) {
            $result = false;
            if (!empty($tenantuser)) {
                foreach ($tenantuser as $key => $value) {
                    $result[$i++] = $user->updateUser($data, $value->uid);
                    //echo "<pre>"; print_r($value->uid); exit();
                }
            } else {

                $updateData['userStatus'] = '0';
                $tenant->updateTenant($updateData, $id);
                echo true;
                exit(0);
            }
        } else {
            echo false;
            exit(0);
        }

        $updateData['userStatus'] = '0';
        $tenant->updateTenant($updateData, $id);

        if ($i == count($result)) {
            echo true;
            exit(0);
        } else {
            echo false;
            exit(0);
        }
    }

    public function checkusernameAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $userName = $this->_getParam('userName');
        $uid = $this->_getParam('uid');

        $userModel = new Model_User();
        $userDetail = $userModel->checkUserName($userName, $uid);

        if (!empty($userDetail))
            echo true;
        else
            echo false;

        exit();
    }

    public function checkuseremailAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $email = $this->_getParam('email');
        $uid = $this->_getParam('uid');

        $userModel = new Model_User();
        $userDetail = $userModel->checkUserEmail($email, $uid);

        if (!empty($userDetail))
            echo true;
        else
            echo false;

        exit();
    }

    public function createtuserAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->getRequest()->getPost();
        //print_r($data);
        $userModel = new Model_User();
        $tenantUserModel = new Model_TenantUser();
        $isExist = $userModel->isUserExist($data['email']);
        if (!$isExist) {
            try {
                $userData = array();
                $gpass = $this->generateRandomString();
                $detail['gpass'] = $gpass;
                $userData['email'] = $data['email'];
                $userData['firstName'] = addslashes($data['firstName']);
                $userData['lastName'] = addslashes($data['lastName']);
                $userData['phoneNumber'] = $data['phoneNumber'];
                $userData['userName'] = $data['email'];
                $userData['password'] = md5($gpass);
                $userData['role_id'] = 7; //tenant user
                $userData['cust_id'] = $this->cust_id;
                $userData['regDate'] = date('Y-m-d H:i:s');
                $userData['uid'] = $userModel->insertUser($userData);

                $tenantUserData = array();
                $tenantUserData['userId'] = $userData['uid'];
                $tenantUserData['tenantId'] = $data['tenantId'];
                $tenantUserData['suite_location'] = addslashes($data['suite_location']);
                $tenantUserData['cc_enable'] = 0;
                $tenantUserData['send_as'] = 1; // default HTML
                $tenantUserData['complete_notification'] = 0;
                $tenantUserModel->insertTenantUser($tenantUserData);
                $status = 'success';
                $msg = "Tenant User created successfully.";
            } catch (Exception $e) {
                $status = "error";
                $msg = "Error Ocurred During the creation of new user";
            }
        } else {
            $status = 'error';
            $msg = "Email Already exists!!";
        }

        echo json_encode(array('status' => $status, 'msg' => $msg));
        exit(0);
    }

    public function edittenantadminAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $tuId = $this->_getParam('tuId');
        $tenant = new Model_Tenant();
        $tenantUserModel = new Model_TenantUser();
        $tenantData = $tenant->getTenantById($tuId);
        $tenantuser = $tenantUserModel->getTenantUsers($tenantData[0]->id);
        $tenantadmin = $tenant->getTenantByUser($this->userId); 
        $this->view->tenantadmin = $tenantadmin;
        $this->view->tenantData = $tenantData[0];
        $this->view->tenantuser = $tenantuser;
    }

    public function updatetenantinfoAction() {
        $message = array();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $updatedata = array();
            $updatedata['tenantName'] = $data['tenantName'];           
            $updatedata['billtoAddress'] = $data['billtoAddress'];

            if($this->roleId==5){
                $updatedata['tenantContact'] = $data['tenantContact'];
                $updatedata['tenantName'] = $data['tenantName']; 
            }else{
                $updatedata['suite'] = $data['suite_location'];
            }
            try {
                $tenantModel = new Model_Tenant();
                $res = $tenantModel->updateTenant($updatedata, $data['tId']);
                $tenantUserModel = new Model_TenantUser();
                $res = $tenantUserModel->updateMainTenant($data['main_contact'], $data['tId']);
                $message['status'] = 'success';
                $message['msg'] = 'Tenant edit successfully.';
            } catch (Exception $e) {
                $message['status'] = 'error';
                $message['msg'] = 'Error occurred during editing tenant..';
            }
            echo json_encode($message);
        }
        exit(0);
    }

    public function updatemaincontactAction() {
        $tenantMapper = new Model_TenantUser();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {

            $id = $this->getRequest()->getPost('pk');
            $key = $this->getRequest()->getPost('name');
            $value = $this->getRequest()->getPost('value');
            $data = array(
                $key => $value
            );
            if ($key != '' && !empty($key)) {
                $res = $tenantMapper->updateMainTenant($data, $id);
            }
        }
        exit;
    }
    
    public function conavailabilityAction() {
       $tenant = new Model_Tenant();
       $tenantuser = $tenant->getTenantByUser($this->userId);
       $user_build_mod = new Model_UserBuildingModule();
       $buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
       //$buildinglists[0]->building_id;
       $final=$this->getViewAccess($buildinglists[0]["building_id"]); 
       $user_id=$_SESSION['Zend_Auth']['storage']->uid;
       $this->view->userId=$user_id;
       $this->view->viewacess=$final;
       $this->view->roleId = $this->roleId;
       $this->view->bId = $buildinglists[0]["building_id"];   
       $this->view->tenantuser = $tenantuser[0];
       $this->view->month = date('m',strtotime('last Sunday'));
       $this->view->lastdate = date('d',strtotime('last Sunday')); 
      
    }
    
    public function getViewAccess($bid){
        $checkscheduler = new Model_ConferenceSchedule();
        $getscheduler=$checkscheduler->getcrDetailsByBid($bid);        
        foreach($getscheduler as $da){
            $getcs=$checkscheduler->getCrdata($da->schedule_id);
            $data[]=$this->getshowday($getcs[0]->week_days_id);
        }
        foreach($data as $get){
            foreach ($get as $get){
                $final[$get]=$get;
            }
        }
        return $final;
    }

    public function getshowday($id){

        switch($id){
            case 1:
               $ids=array(0,1,2,3,4,5,6);
               break;
            case 2:
               $ids=array(1,2,3,4,5);
               break;            
           case 3:
               $ids=array(0,6);
               break;            
           case 4:
               $ids=array(1,3,5);
               break;            
           case 5:
               $ids=array(2,4);
               break;            
           case 6:
               $ids=array(1);
               break;                    
          case 7:
               $ids=array(2);
               break;                    
           case 8:
               $ids=array(3);
               break;                    
           case 9:
               $ids=array(4);
               break;                    
           case 10:
               $ids=array(5);
               break;                    
           case 11:
               $ids=array(6);
               break;                    
           case 12:
               $ids=array(0);
               break;                  

        }
        return $ids;
    }
    public function createbookingAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $month = $this->_getParam('month', '');
        $build_ID = $this->_getParam('bid', '');
        $year = $this->_getParam('year', '');
        $day = $this->_getParam('day', '');
        $this->view->select_build_id = $build_ID;
        $this->view->month = $month;
        $this->view->year = $year;
        $this->view->requested_date = $year . '/' . $month . '/' . $day;
        $tenant = new Model_Tenant();
        //echo $this->userId;
        $da=$year . '/' . $month . '/' . $day;
        $tenantdetails = $tenant->getTenantByUser($this->userId); 
        $tnuserModel = new Model_TenantUser();
        $tnuserdetail = $tnuserModel->getTenantUsers($tenantdetails[0]->id);

        //$cscheduleMapper = new Model_ConferenceSchedule();
        $cscheduleMapper = new Model_ConferenceSchedule();
        $getvalidday=$cscheduleMapper->getcrvaliddays($build_ID,$da);
        //print_r($getvalidday);
        /*if($_SESSION['Admin_User'][role_id]==7){
          $crDetails = $cscheduleMapper->getcrDetailsByBidTuser($build_ID);  
        }else{
            $crDetails = $cscheduleMapper->getcrDetailsByBid($build_ID);
        }*/
        //$crDetails = $cscheduleMapper->getcrDetailsByBid($build_ID);
        $this->view->fullDate=$da;
        $this->view->crDetails = $getvalidday;
        $this->view->tenantdetails = $tenantdetails;
        $this->view->tnuserdetail = $tnuserdetail;
        
    }


      /*     * *
     * Show tenant coi's detail
     */

    public function coidetailAction() {
      
        $tenant = new Model_Tenant();
        $tenantuser = $tenant->getTenantByUser($this->userId);               
         
        //Restriced others user can noly Tenant Admin can Access it
         if(isset($tenantuser[0]->role_id) && !empty($tenantuser[0]->role_id)){
            if($tenantuser[0]->role_id !=5){
              $this->_redirect('/tenant/noaccess');
            }
        } 

        if(isset($tenantuser[0]->buildingId) && !empty($tenantuser[0]->buildingId)){
            $buildingId = $tenantuser[0]->buildingId;
            $buildingMapper = new Model_Building();
            $getcostcenter = $buildingMapper->getcostcenterByBuildingId($buildingId);   
            $cdModel = new Model_CoiDetails();		
            $coiDetails = $cdModel->getCoidetails($buildingId);
            $data['Building_ID'] = $buildingId;
            $data['uniqueCostCenter'] = $getcostcenter[0]->uniqueCostCenter;				 
        }
        $template = new Model_CioRequirement(); 
        $tempdata= $template->GetAllGeneralRequirment($buildingId);
       //echo '<pre>';
        $tempdatasecond= $template->GetAllAutomobileRequirment($buildingId);
        $templatteumbrella=$template->GetAllUmbrellaRequirment($buildingId);
        $templatteWorkers=$template->GetAllWorkersRequirment($buildingId);      
        // where id = $tenantId 
        $tModel = new Model_Tenant();
        $bs = $tModel->getTenantCoiByBId($buildingId, $this->userId);	
      
        //if(!empty($bs->coi_au_date_to)
       // echo '<pre>';
       // print_r($tenantuser);
        //print_r($bsList);
       // tenantId == 721
        $woCOI = new Model_CioRequirement(); 
        $coilist = $woCOI->getReportByBId($this->select_build_id);								
        
        $this->view->templatedetails = $tempdata;   
        $this->view->templatedetailsseconnd =$tempdatasecond;
        $this->view->templatedetailsthird=$templatteumbrella; 
        $this->view->templatteWorkers=$templatteWorkers;
        $this->view->coiDetails = $coiDetails;
        $this->view->bsCOI = $bs;


    }
    
    public function tenantinfoAction() {
        $tenant = new Model_Tenant();
        $tenantuser = $tenant->getTenantByUser($this->userId); 
        //Restriced others user can noly Tenant Admin can Access it
        if(isset($tenantuser[0]->role_id) && !empty($tenantuser[0]->role_id)){
            if($tenantuser[0]->role_id !=5){
              $this->_redirect('/tenant/noaccess');
            }
        }
        $msgId = $this->_getParam('msg', 0);
        $msg = '';
        if ($msgId == 1) {
            $msg = 'Tenant user has been created successfully.';
        }

        if ($msgId == 2) {
            $msg = 'Tenant user has been updated successfully.';
        }
        if ($msgId == 3) {
            $msg = 'Tenant has been deleted successfully.';
        }
        $tm = new Zend_Session_Namespace('tenant_message');
        if (!isset($tm->msg) && $msgId != 0) {
            $tm->msg = $msg;
            $this->_redirect('/tenant/tenantinfo');
        }
        $tenant = new Model_Tenant();
        $tenantuser = $tenant->getTenantByUser($this->userId);
        //var_dump($tenantuser);

        $this->view->roleId = $this->roleId;
        $this->view->tenantuser = $tenantuser[0];
    }

    /** Tenant admin and Tenant user account setting  */
    public function myaccountsettingAction() {
       
        //Restriced others user can noly Tenant Admin can Access it
        if(isset($this->tenantuser[0]->role_id) && !empty($this->tenantuser[0]->role_id)){
            if($this->tenantuser[0]->role_id ==5 || $this->tenantuser[0]->role_id ==7){               
                $this->view->roleId = $this->roleId;
                $this->view->tenantuser = $this->tenantuser[0];
                $sendMapper = new Model_SendAs();
                $sendDetail = $sendMapper->getSendAs();
                $this->view->sendDetail = $sendDetail;
                 // updated userName and email
                 if(Zend_Auth::getInstance()->getStorage()->read()->userName !=$this->tenantuser[0]->userName){
                    $this->_helper->_redirector->gotoUrl('/logout');
                }
                if(Zend_Auth::getInstance()->getStorage()->read()->userName !=$this->tenantuser[0]->userName){
                    $this->_helper->_redirector->gotoUrl('/logout');
                }
            }
            else{
                $this->_redirect('/tenant/noaccess');
            }
        }else{         
                $this->_redirect('/tenant/noaccess');
           
        }
        
        $msgId = $this->_getParam('msg', 0);
        $msg = '';
        if ($msgId == 1) {
            $msg = 'Tenant user has been created successfully.';
        }

        if ($msgId == 2) {
            $msg = 'Tenant user has been updated successfully.';
        }
        if ($msgId == 3) {
            $msg = 'Tenant has been deleted successfully.';
        }
        $tm = new Zend_Session_Namespace('tenant_message');
        if (!isset($tm->msg) && $msgId != 0) {
            $tm->msg = $msg;
            $this->_redirect('/tenant/tenantinfo');
        }     
        
    }

    public function noaccessAction() {
    }
 
    // update coi by Tenant Admin
    public function editttenantserviceAction(){

		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		//$this->view->bsid= $data['bsid'];
		$this->view->bId= $data['cid'];
		$cModel = new Model_CoiList();
        $serviceData = $cModel->geteditcoiList($data['cid']);
		$this->view->serviceData = $serviceData;
		
	}// close edit service


    // add update coi by Tenant Admin
    public function updateserviceAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();         

			 $message = array();		 
			 
			 if($data['coi_au_tenant_id']== '' || $data['coi_au_date_to']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';				 
			 }else{
				    $cModel = new Model_CoiList();
                    $fileData = $cModel->getcoiList($data['coi_au_tenant_id']);	
            				
                     //	PDF upload Code Here				
                    $filename = basename($_FILES['file']['name']['equipmentmenual']);
					if(!empty($filename)){
					$uploaddir = IMAGE_UPLOAD_DIR . '/public/coi/';
					
					//delete PDF from the folder Code Here
					$file_name = $fileData[0]['coi_au_pdf_upload'];
                    $uploadfile = $uploaddir . '' . $file_name;						
                    if (file_exists($uploadfile)) {
                        unlink($uploadfile);
                    }					
					$fileNewName = $data['tenant_Id'].".".pathinfo($filename, PATHINFO_EXTENSION);							
					$da = move_uploaded_file($_FILES['file']['tmp_name']['equipmentmenual'], $uploaddir . '' . $fileNewName);
                    $data['coi_au_pdf_upload'] = $fileNewName;
				    }else{
						$data['coi_au_pdf_upload'] = $fileData[0]['coi_au_pdf_upload'];
					}			
				    $data['coi_au_date_to'] = date("Y-m-d", strtotime($data['coi_au_date_to']));
				                    
					 try{						
						 $submitBuildingService = $cModel->updateBuildService($data,$data['coi_au_tenant_id']);
                         //send email to account manager
						 $this->sendcoiAccountManagerMail($data,$uploadfile);
						 $message['status'] = 'success';
				         $message['msg']='Coi List Updated successfully.';
					 }catch(Exception $e){
					    $message['status'] = 'error';
				        $message['msg']='Error Occurred during the update Coi List';
					 }
				 
			 }
			 
			echo json_encode($message);
		 }
		 exit(0);
	}// close update service

    //When add edit coid by Tenant's Admin after that send Email to building Contact Manager

    public function sendcoiAccountManagerMail($data,$uploadfile){
        $message = array();
        $tenantUserModel = new Model_TenantUser();
        $tModel = new Model_Tenant();
        $tenantuser = $tModel->getTenantByUser($this->userId);
        $tenantData = array();
        //required tenant info 
       
        $tenantData['tenantName'] = $tenantuser[0]->tenantName;
        $tenantData['firstName'] = $tenantuser[0]->firstName;
        $tenantData['lastName'] = $tenantuser[0]->lastName;
        $tenantData['userName'] = $tenantuser[0]->userName;
        $tenantData['email'] = $tenantuser[0]->email;
        $tenantData['buildingId'] = $tenantuser[0]->buildingId;
        $tenantData['tenantId'] = $tenantuser[0]->tenantId;
        $tenantData['firstnamelastname'] = $tenantuser[0]->firstname." ".$tenantuser[0]->lastname;
        $tenantData['email'] = $tenantData[0]->email;					
        $tenantData['address1'] = $tenantuser[0]->address1;
        $tenantData['address2'] = $tenantuser[0]->address2;
        $tenantData['suite'] = $tenantuser[0]->suite;
        $tenantData['city'] = $tenantuser[0]->city;
        $tenantData['state'] = $tenantuser[0]->state;
        $tenantData['postalCode'] = $tenantuser[0]->postalCode; 

        
        $companyModel = new Model_Company();
        //Building info
        $buildingId = $tenantuser[0]->buildingId;
        $buildingMapper=new  Model_Building();
        $buildDataArr = array();
		$buildData = $buildingMapper->getbuildingbyid($buildingId);
        $buildDataArr['build_id']= $buildData[0]['build_id'];
        $buildDataArr['cust_id']= $buildData[0]['cust_id'];
        $buildDataArr['buildingName']= $buildData[0]['buildingName'];       

        // Tenant's coi detail 
        $bscoiArr = $tModel->getTenantCoiByBId($buildingId, $this->userId);	
        $tenantData['coi_au_pdf_upload'] = $bscoiArr[0]->coi_au_pdf_upload;

        //Property Manager list Role = 4 
        $propetyMangerList = $companyModel->getUserBuildingUserByRoleId($buildingId, $nottenant=true,$role_id=4);   
        // Email subject Building Name | Tenant Name | updated COI
        if(!empty($propetyMangerList)){
        foreach($propetyMangerList as $propertymanger){
           $this->sentCoiUpdatedEmail($buildDataArr,$tenantData,$propertymanger);
          
        }
        }
              
    }

    public function sentCoiUpdatedEmail($buildData,$tenantData,$propertymanger,$htmlDocId=''){
       
        $message = array();      
        if(isset($tenantData['buildingId']) && !empty($tenantData['buildingId'])){
            $buildingId = $tenantData['buildingId'];
            $buildingMapper = new Model_Building();
            $userModel = new Model_User();
            $nottenant = 1;
            $companyModel = new Model_Company();
            $getcostcenter = $buildingMapper->getcostcenterByBuildingId($buildingId);   
            $cdModel = new Model_CoiDetails();		
            $coiDetails = $cdModel->getCoidetails($buildingId);
            $data['Building_ID'] = $buildingId;
            $data['uniqueCostCenter'] = $getcostcenter[0]->uniqueCostCenter;
            $emailMapper = new Model_Email();
            if($htmlDocId == '') {
                  $htmlDocId = 67; // email template id
            }
            $loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
             if($loadTemplate){  
                 
              /******get Company Name ******/
            $currDate = date('F d, Y');

			$accoutMapper = new Model_Account();
			$company = $accoutMapper->getcompany($this->cust_id);
			$companyName = $company[0]['companyName'];

            $header_data = $this->getHeaderData($company);
            $footer_data = $this->getFooterData();
            // $footer_data	=	$this->getFooterData();
            $emailContent = $loadTemplate[0];
            $emailSubject = $emailContent['email_subject'];
            $emailSubject = str_replace('[[++buildingName]]', $buildData['buildingName'], $emailSubject);
            $emailSubject = str_replace('[[++tenantname]', $tenantData['tenantName'], $emailSubject);
            //$emailSubject = str_replace('[[++updatecoi]]', $header_data['building_logo_src'], $emailBody);
            // End Email subject
            $content = $emailContent['email_content'];
            
            
            ///// header 
            $content = str_replace('[[++companyLogo]]', $header_data['building_logo_src'], $content);
            $content = str_replace('[[++voctechLogo]]', $header_data['voctech_logo_src'], $content);
            $content = str_replace('[[++currDate]]', $header_data['date'], $content);
            $content = str_replace('[[++costNumber]]', $header_data['corp_account_number'], $content);
            ///// end header
			
            $content = str_replace('[[++currDate]]', $currDate, $content);
			$content = str_replace('[[++companyName]]', $companyName, $content);
			
			 $content = str_replace('[[++currDate]]', $currDate, $content);
			$content = str_replace('[[++companyName]]', $companyName, $content);
            $content = str_replace('[[++buildingName]]', $buildData['buildingName'], $content);
            $content = str_replace('[[++tenantName]]', $tenantData['tenantName'], $content);
            $content = str_replace('[[++firstnamelastname]]', $tenantData['firstName'].' '. $tenantData['lastName'], $content);
           
			

			
		//	$content = str_replace('[[++Status]]', $detail['status'], $content);
		//	$content = str_replace('[[++ExpDate]]', $detail['expirationDate'], $content);
            ///// Footer 
            $content = str_replace('[[++footerInfo]]', $footer_data['footer_info'], $content);
           
            ///// End Footer
            // ///// End Footer
			// Email subject start
            $mail = new Zend_Mail('utf-8');							
			$mail->addTo($propertymanger->email);
			$mail->addTo('dadhikuriyal@teckvalley.com');
			$mail->setSubject($emailSubject);
            $setModel = new Model_Setting();
            $setData = $setModel->getSetting();
            if($setData){
            	$setting = $setData[0];
            	$mail->setFrom($setting['from_email'],$setting['from_name']);
            	$return_path = new Zend_Mail_Transport_Sendmail('-f'.$setting['from_email']);
            // 	if($setting['bcc_email'])
            // 	$mail->addBcc($setting['bcc_email'], $setting['bcc_name']);
            }else{
            //	$mail->setFrom('support@visionworkorders.com','Vision Work Orders');
            //	$return_path = new Zend_Mail_Transport_Sendmail('-fsupport@visionworkorders.com');
            }


               // $mail->setFrom($setting['from_email'],$setting['from_name']);
				//Zend_Mail::setDefaultTransport($return_path);
                $mail->setBodyHtml($content);
                $filename = $tenantData['coi_au_pdf_upload'];           
 
                // // pdf attachement
                $uploaddir = IMAGE_UPLOAD_DIR . '/public/coi/';    
                
                $content = file_get_contents($uploaddir . '' . $filename); // e.g. ("attachment/abc.pdf")
                $attachment = new Zend_Mime_Part($content);
                $attachment->type = 'application/pdf';
                $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                $attachment->encoding = Zend_Mime::ENCODING_BASE64;
                $attachment->filename = $filename; // name of file
                $mail->addAttachment($attachment);
                $res = $mail->send();	
                return $res;

			
            }				 
        }
    }

    public function edittenantmyaccountinfoAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $tuId = $this->_getParam('tuId');
        $tenant = new Model_Tenant();
        $tenantUserModel = new Model_TenantUser();
        $tenantData = $tenant->getTenantById($tuId);
        $tenantuser = $tenantUserModel->getTenantUsers($tenantData[0]->id);
        $tenantadmin = $tenant->getTenantByUser($this->userId); 
        $this->view->tenantadmin = $tenantadmin;
        $this->view->tenantData = $tenantData[0];
        $this->view->tenantuser = $tenantuser;
    }

    public function edittenantadminaccountinfoAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $tuId = $this->_getParam('tuId');
        $message = array();
        $tenantUserModel = new Model_TenantUser();
        $tenant = new Model_Tenant();
        $tenantData = $tenantUserModel->getTenantUserById($tuId);
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            //print_r($data); die;
            $uid = $data['uid'];
            $userData = array();
            $userData['userName'] = html_entity_decode($data['userName']);
            $userData['email'] = html_entity_decode($data['email']);
            $userData['firstName'] = addslashes($data['firstname']);
            $userData['lastName'] = addslashes($data['lastname']);
          //  $userData['suite_location'] = $data['suite_location'];
            $userData['phoneNumber'] = $data['phone'];
            //$userData['role_id'] = $data['access'];
            $userData['regDate'] = date('Y-m-d H:i:s');           
            $userData['note_notification'] = addslashes($data['note_notification']);
            $send_email = 0;
            if (isset($data['auto']) && $data['auto'] == 1) {
                $gpass = $this->generateRandomString();
                $userData['password'] = md5($gpass);
                $data['password'] = $gpass;
                $send_email = 1;
            } else if (!empty($data['password'])) {
                $userData['password'] = md5($data['password']);
                $send_email = 1;
            } else {
                $data['password'] = '';
            }

            if ($tenantData[0]->userName != $data['userName'])
                $send_email = 1;

            if ($tenantData[0]->email != $data['email'])
                $send_email = 1;

            $userModel = new Model_User();
            $userNameDetail = $userModel->checkUserName($data['userName'], $uid);
            $userEmailDetail = $userModel->checkUserEmail($data['email'], $uid);
            if (!$userNameDetail && !$userEmailDetail) {
                try {
                  
                    $userData['uid'] = $userModel->updateUser($userData, $uid);

                  
                    if (isset($_FILES['file']['name'])) {
                        $uploaddir = BASE_PATH . 'public/user_img/';
                        $uploadfile_name = 'WO-' . time() . '-' . basename($_FILES['file']['name']);
                        $uploadfile = $uploaddir . '' . $uploadfile_name;
                        if (!file_exists($uploaddir)) {
                            mkdir($uploaddir, 0777, true);
                        }
                        move_uploaded_file($_FILES["file"]["tmp_name"], $uploadfile);
                        $file_name = $uploadfile_name;
                        try {
                            $userModel->updateUser(array('user_img' => $uploadfile_name), $data['uid']);
                            $message['status'] = 'success';
                            $message['msg'] = 'User edit successfully.';
                        } catch (Exception $e) {
                            $message['status'] = 'error';
                            $message['msg'] = 'Error occurred during user edit.';
                        }
                    }


                    $id = $data['id'];
                    $tenantUserData['suite_location'] = $data['suite_location'];
                    if ($this->roleId == 5) {
                     $tenantUserData['cc_enable'] = $data['cc_enable'];
                    }
                    $tenantUserData['send_as'] = $data['send_as'];
                    $tenantUserData['complete_notification'] = $data['complete_notification'];


                    if ($data['welcome_letter'] == 1) {
                        $this->sendemailAction(true, $data['uid'], $data['tenantId'], $data['password']);
                    } elseif ($send_email == 1) {
                        $this->sendemailAction(true, $data['uid'], $data['tenantId'], $data['password']);
                    }

                    $tenantUserModel->updateTenantUser($tenantUserData, $id);
                    $build_ID = $data['building'];
                    $tId = $data['tenantId'];
                    if ($this->roleId == 5 || $this->roleId == 7) {
                        //$this->_redirect('/tenant/tenantuser/msg/2');
                        $json_data['msg'] = "Record successfully updated!";
                        $json_data['url'] = '/tenant/myaccountsetting/msg/2';
                        echo json_encode($json_data);
                        exit;
                    } else
                    //$this->_redirect('/tenant/users/bid/'.$build_ID.'/tId/'.$tId.'/msg/2');
                        $json_data['msg'] = "Record successfully updated!";
                    $json_data['url'] = '/tenant/users/myaccountsetting/' . $build_ID . '/tId/' . $tId . '/msg/2';
                    echo json_encode($json_data);
                    exit;
                } catch (Exception $e) {
                    $message['msg'] = 'Error occured';
                }
            } else
                $message['msg'] = 'Error occured';
        }
        if ($tenantData) {
            $tuserDetail = $tenantData[0];
            $tenantData = $tenant->getTenantById($tuserDetail->tenantId);
            $this->view->roleId = $this->roleId;
            $this->view->tenantId = $tuserDetail->tenantId;
            $this->view->tenantData = $tenantData[0];
            $this->view->tuserDetial = $tuserDetail;
            $this->view->tenantuser = $this->tenantuser[0];
        } else
            $message['msg'] = 'Invalid Data';
        $this->view->message = $message;
        
    }

    public function currentusersAction(){
      
		if( isset($this->tenantuser) && !empty($this->tenantuser) ){
            $tenantId = $this->tenantuser[0]->id;
            $userId = $this->tenantuser[0]->userId;
            $buildingId = $this->tenantuser[0]->buildingId;
            $uinfo = $this->tenantuser[0];
            $tenant = new Model_Tenant();
            $tenantuser = new Model_TenantUser();
            $tenantuser = $tenantuser->getTenantUsers($uinfo->id,$buildingId);          
            $tenantAdminList =  $tenant->getAllTenantsByBuildingId($buildingId,5,$tenantId); 
            $tenantUserList = $tenant->getAllTenantsByBuildingId($buildingId,7,$tenantId); 
            $sendMapper = new Model_SendAs();
            $sendDetail = $sendMapper->getSendAs();
            $send_data = array();
            foreach ($sendDetail as $sd) {
                $send_data[$sd['sid']] = $sd['title'];
            }
           $this->view->tenantAdminList = $tenantAdminList;           
           $this->view->tenantUserList = $tenantUserList;
           $this->view->send_data = $send_data;
           $this->view->tenantId  = $tenantId;
           $this->view->userId  = $userId;
            if(Zend_Auth::getInstance()->getStorage()->read()->userName !=$this->tenantuser[0]->userName){
                $this->_helper->_redirector->gotoUrl('/logout');
            }
            if(Zend_Auth::getInstance()->getStorage()->read()->userName !=$this->tenantuser[0]->userName){
                $this->_helper->_redirector->gotoUrl('/logout');
            }

          
        }else{
            $this->_redirect('/tenant/noaccess');
        }
      
    }

    public function removetenantlocationAction() {
        $data = $this->getRequest()->getPost();
        $user = new Model_User();
        $tenant = new Model_Tenant();
        $tenantuser = new Model_TenantUser();
        $tuusersId = (int)$data['tuId']; 
        $uId = (int)$data['uId']; 
        

              
        $userData['is_location_removed'] = '1';
       
        if(isset($tuusersId) && !empty($tuusersId)){
         // $tenantuser->updateTenantUser($userData,$tuusersId);
          $tenantuser->updateTenantUser($userData, $tuusersId);
          $json_data = array();
            $json_data['msg'] = "Record successfully de!";
            $json_data['url'] = '/tenant/myaccountsetting/msg/2';
            echo json_encode($json_data);
            exit;
       }

             
    }

    public function recoverytenantlocationAction() {
        $data = $this->getRequest()->getPost();
        $user = new Model_User();
        $tenant = new Model_Tenant();
        $tenantuser = new Model_TenantUser();
        $tuusersId = (int)$data['tuId'];       
        $userData['is_location_removed'] = '0';
        $uId = (int)$data['uId']; 
       
        if(isset($tuusersId) && !empty($tuusersId)){
            $tenantuser->updateTenantUser($userData, $tuusersId);
          $json_data = array();
            $json_data['msg'] = "Record successfully de!";
            $json_data['url'] = '/tenant/myaccountsetting/msg/2';
            echo json_encode($json_data);
            exit;
       }
             
    }

    public function updatetenantsuitAction() {
        $data = $this->getRequest()->getPost();
        $user = new Model_User();
        $tenant = new Model_Tenant();
        $usersId = $data['userid'];
        $tenantId = $data['tenantid'];
        $tenanuserid = $data['tenanuserid'];
        $build_ID = $data['buildID'];

             
    }

    public function getmultiluserbylocationAction(){
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getPost();
        $tenantUserModel = new Model_TenantUser();        
        $json_data = $tenantUserModel->getMutiluserInfo($data);
        if($json_data){
          echo '1';
        }else{
            echo '0';
        }

        exit;
         
    }

    public function resettenantoptionfilterAction(){
       // multiuser_search_email
        if (isset($_COOKIE['multiuser_search_email'])) {
            unset($_COOKIE['multiuser_search_email']); 
            setcookie('multiuser_search_email', '', -1, '/');           
        }         
        $this->_redirect('/tenant/tenantoptions');    

    }

// update tenant menu version

public function updatemenuversionAction(){   
    $data = $this->getRequest()->getPost();

        $tenantversion = $this->_getParam('tenantversion');      
        if(isset($tenantversion)){            
            setcookie('tenant_version', $tenantversion, time() + (86400 / 24), "/");
        }  
        echo $tenantversion;    
        exit();
       

    }

    // update tenant company version

    public function updattenantcompanyAction(){   
        $data = $this->getRequest()->getPost();

            $tenant_company = $this->_getParam('tenant_company');      
            if(isset($tenant_company)){            
                setcookie('tenant_company', $tenant_company, time() + (86400 / 24), "/");
            }  
            echo $tenant_company;    
            exit();
        

        }


}




?>
