<?php

class CompanyController extends Ve_Controller_Base {

    private $userId = '';
    private $roleId = '';
    private $accountMapper = '';
    private $buildingMapper = '';
    private $cust_id = '';
    private $userMapper = '';

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
        $this->accountMapper = new Model_Account();
        $this->buildingMapper = new Model_Building();
        $this->userMapper = new Model_User();
        $this->accessHelper = $this->_helper->access;
    }

    // Call befor any action and check is user login or not
    public function preDispatch() {
        if (!Zend_Auth::getInstance()->hasIdentity())
            $this->_redirect('/index');
        $level = (Zend_Auth::getInstance()->getStorage()->read()) ? Zend_Auth::getInstance()->getStorage()->read()->role_id : '';
        $this->userId = Zend_Auth::getInstance()->getStorage()->read()->uid;
        $this->roleId = Zend_Auth::getInstance()->getStorage()->read()->role_id;
        $this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
        $this->per_page = 15;
    }

    public function indexAction() {
        $build_ID = $this->_getParam('bid', '');
        $select_build_id = $build_ID;
        /*         * *******set building in cookie ********* */

        if (empty($select_build_id) && isset($_COOKIE['build_cookie']))
            $select_build_id = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $select_build_id, time() + (86400 / 24), "/");
        $companyListing = '';
        if ($this->roleId == '9') {
            $companyListing = $this->buildingMapper->getCompanyBuilding($this->cust_id);
        } else {
            $user_build_mod = new Model_UserBuildingModule();
            $buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
            if ($buildinglists) {
                $build_id_array = array();
                foreach ($buildinglists as $buildlist)
                    $build_id_array[] = $buildlist['building_id'];
                $companyListing = $this->buildingMapper->getBuildingList($build_id_array);
                //print_r($build_data);
            }
        }
        $this->view->companyListing = $companyListing;
        $this->view->custID = $this->cust_id;
        $this->view->roleId = $this->roleId;
        $this->view->select_build_id = $select_build_id;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->userId = $this->userId;

        //to set the access of Building Information
        $this->view->building_location_id = 6;
    }

    public function updateinfoAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') { // Address Field update
            if ($this->getRequest()->getPost('address')) {
                $build_id = $this->getRequest()->getPost('buildingID');
                $address = $this->getRequest()->getPost('address');
                $data = array(
                    'address' => $address,
                );
            }
            // Address2 Field update
            elseif ($this->getRequest()->getPost('address2')) {
                $build_id = $this->getRequest()->getPost('buildingID');
                $address2 = $this->getRequest()->getPost('address2');
                $data = array(
                    'address2' => $address2,
                );
            }
            // Suite Field update
            elseif ($this->getRequest()->getPost('suite')) {
                $build_id = $this->getRequest()->getPost('buildingID');
                $suite = $this->getRequest()->getPost('suite');
                $data = array(
                    'suite' => $suite,
                );
            }
            // City Field update
            elseif ($this->getRequest()->getPost('city')) {
                $build_id = $this->getRequest()->getPost('buildingID');
                $city = $this->getRequest()->getPost('city');
                $data = array(
                    'city' => $city,
                );
            }
            // State Field Updated
            elseif ($this->getRequest()->getPost('state')) {
                $build_id = $this->getRequest()->getPost('buildingID');
                $state = $this->getRequest()->getPost('state');
                $data = array(
                    'state' => $state,
                );
            }
            // Postal Code field update
            elseif ($this->getRequest()->getPost('postalCode')) {
                $build_id = $this->getRequest()->getPost('buildingID');
                $postalCode = $this->getRequest()->getPost('postalCode');
                $data = array(
                    'postalCode' => $postalCode,
                );
            }
            // Phone Number field Updated.
            elseif ($this->getRequest()->getPost('phoneNumber')) {
                $build_id = $this->getRequest()->getPost('buildingID');
                $phoneNumber = $this->getRequest()->getPost('phoneNumber');
                $data = array(
                    'phoneNumber' => $phoneNumber,
                );
            }
            // Fax Number Field Updated
            elseif ($this->getRequest()->getPost('faxNumber')) {
                $build_id = $this->getRequest()->getPost('buildingID');
                $faxNumber = $this->getRequest()->getPost('faxNumber');
                $data = array(
                    'faxNumber' => $faxNumber,
                );
            }
            // Attention Field Updated
            elseif ($this->getRequest()->getPost('attention')) {
                $build_id = $this->getRequest()->getPost('buildingID');
                $attention = $this->getRequest()->getPost('attention');
                $data = array(
                    'attention' => $attention,
                );
            }

            $res = $this->buildingMapper->updateBuilding($data, $build_id);
            if ($res) {
                $data['status'] = 'success';
                $data['message'] = 'Updated successfully';
                echo json_encode($data);
                exit;
            } else {
                $data['status'] = 'error';
                $data['message'] = 'exception occured';
                echo json_encode($data);
                exit;
            }
        }
    }

    public function updatebuildingAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $build_id = $this->getRequest()->getPost('pk');
            $key = $this->getRequest()->getPost('name');
            $value = $this->getRequest()->getPost('value');
            $data = array(
                $key => $value,
            );
            if ($key != '' && !empty($key)) {
                $res = $this->buildingMapper->updateBuilding($data, $build_id);
            }
        }
        exit;
    }

    public function usersAction() {


        $search_array = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $search_array['search_by'] = $data['search_by'];
            $search_array['search_value'] = $data['search_value'];
            $this->view->search = $search_array;
        }

        $user_order = $this->_getParam('user_order', 'ASC');
        $user_dir = $this->_getParam('user_dir', 'lastName');
        $this->view->user_order = $user_order;
        $this->view->user_dir = $user_dir;
        $companyListing = '';
        if ($this->roleId == '9') {
            $companyListing = $this->buildingMapper->getCompanyBuilding($this->cust_id);
        } else {
            $user_build_mod = new Model_UserBuildingModule();
            $buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
            if ($buildinglists) {
                $build_id_array = array();
                foreach ($buildinglists as $buildlist)
                    $build_id_array[] = $buildlist['building_id'];
                $companyListing = $this->buildingMapper->getBuildingList($build_id_array);
            }
        }
        $build_ID = $this->_getParam('bid', '');

        if (empty($build_ID) && (isset($_COOKIE['build_cookie'])))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");

        if ($companyListing != '') {
            if ($build_ID != '')
                $select_build_id = $build_ID;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }
        $nottenant = 1;
        $companyModel = new Model_Company();
        $pageObj = new Ve_Paginator();
        $search_array = array_map("addslashes", $search_array);
        $search_array = array_map("addslashes", $search_array);
        $search_array = array_map("addslashes", $search_array);
        $userList = $companyModel->getUserByBuildingId($select_build_id, $nottenant, $user_order, $user_dir, $search_array);
        $userListcount = $companyModel->getUserByBuildingId($select_build_id, $nottenant);
        $page = $this->_getParam('page', 1);
        $category_paginator = $pageObj->fetchPageDataResult($userList, $page, $this->per_page);
        $this->view->companyListing = $companyListing;
        $this->view->custID = $this->cust_id;
        $this->view->roleId = $this->roleId;
        $this->view->select_build_id = $select_build_id;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->userId = $this->userId;
        //to set the access of Building Information
        $this->view->user_info_id = 7;
        $this->view->userList = $category_paginator;
        $this->view->userCount = count($userListcount);
    }

    public function findcompanynameAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $companyName = $this->getRequest()->getPost('companyName');

            $company_result = $this->accountMapper->findCompanyName($companyName);
            if ($company_result && count($company_result) > 0) {
                echo "found";
            }
        }
    }

    public function findaccountnumberAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $accountNumber = $this->getRequest()->getPost('accountNumber');

            $company_result = $this->accountMapper->findAccountNumber($accountNumber);
            if ($company_result && count($company_result) > 0) {
                echo "found";
            }
        }
    }

    public function updateuserAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $user_id = $this->getRequest()->getPost('pk');
            $key = $this->getRequest()->getPost('name');
            $value = $this->getRequest()->getPost('value');
            $data = array(
                $key => $value,
            );
            if ($key != '' && !empty($key)) {
                $res = $this->userMapper->updateUser($data, $user_id);
            }
        }
        exit;
    }

    public function deletebuildinguserAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $userId = $data['userId'];
            $buildId = $data['buildId'];
            $groupIds = $data['group_Ids'];
            if (!empty($userId) && !empty($buildId)) {
                $userBuilding = new Model_UserBuildingModule();
                $userGroupModel = new Model_EmailGroupUsers();
                $categoryModel = new Model_Category();
                try {
                    $deleteBuildingUser = $userBuilding->deleteBuildingUser($userId, $buildId);
                    $groupid_array = explode(',', $groupIds);
                    if (count($groupid_array) > 0) {
                        foreach ($groupid_array as $gpId) {
                            $deletGroupUse = $userGroupModel->deleteEmailUser($userId, $gpId);
                        }
                    }

                    /*                     * **********delete user from category************ */
                    $categoryList = $categoryModel->getCategoryByUser($userId, $buildId);
                    //var_dump($categoryList);
                    if ($categoryList) {
                        foreach ($categoryList as $cat) {
                            $updateCat = array();
                            $acuserlist = explode(",", $cat['account_user']);
                            $ackey = array_search($userId, $acuserlist);
                            unset($acuserlist[$ackey]);
                            $acuser = implode(",", $acuserlist);
                            $updateCat['account_user'] = $acuser;

                            $updateCat = $categoryModel->updateCategory($updateCat, $cat['cat_id']);
                        }
                    }
                    $msg = 'true';
                } catch (Exception $e) {
                    $msg = 'error';
                }
            } else
                $msg = 'error';
            echo json_encode(array('msg' => $msg));
        }
        exit;
    }

    function setbuildingcookieAction() {
        $build_ID = $this->_getParam('bId', '');
        if ($build_ID != '') {
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        }
        exit(0);
    }

    function edituserAction() {
        $this->_helper->layout->setLayout('popuplayout');
        $uid = $this->_getParam('uid', '');
        $build_ID = $this->_getParam('bid', '');
        $userModel = new Model_User();
        $nottenant = 1;
        $companyModel = new Model_Company();
        $userList = $companyModel->getUserByBuildingId($build_ID, $nottenant);
        $userDetails = $userModel->getUserById($uid);
        $roleMapper = new Model_Role();
        $roleDetail = $roleMapper->getRole();
        $this->view->build_id = $build_ID;
        $this->view->uid = $uid;
        $this->view->building_location_id = 6;
        $this->view->roleDetail = $roleDetail;
        $this->view->userDetails = $userDetails;
        $this->view->userCount = count($userList);
  
    }

    function updateaccountuserAction() {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $password = false;
            $gpass = '';
            $build_id = $data['bid'];
            unset($data['bid']);
            if ($data['auto_generate'] == 1) {
                $gpass = $this->generateRandomString();
                $detail['gpass'] = $gpass;
                $data['password'] = md5($gpass);
                $password = true;
                unset($data['resetPassword']);
                unset($data['confirmPass']);
            } else if ($data['resetPassword'] == '' && $data['confirmPass'] == '') {
                unset($data['resetPassword']);
                unset($data['confirmPass']);
            } else {
                $data['password'] = md5($data['resetPassword']);
                $gpass = $data['resetPassword'];
                $password = true;
                unset($data['resetPassword']);
                unset($data['confirmPass']);
            }
            unset($data['auto_generate']);
            if (!empty($data['uid'])) {
                $userModel = new Model_User();
                $userDetails = $userModel->getUserById($data['uid']);
                $data = array_map("addslashes", $data);
                if ($data['role_id'] == '') {
                    unset($data['role_id']);
                } else {
                    $customeAccessmodel = new Model_UserAccess();
                    $deleteresult = $customeAccessmodel->removeUserCustomAccess($data['uid']);
                }
                $userModel->updateUser($data, $data['uid']);
                $message['status'] = 'success';
                $message['msg'] = 'User edit successfully.';
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
                echo json_encode($message);
            }

            if ($userDetails[0]->userName != $data['userName'] || ($password)) {

                $roleModel = new Model_Role();
                $roleDetails = $roleModel->getRole($userDetails[0]->role_id);
                $buildingIdArray = array();
                $userModel = new Model_User();
                $building = new Model_Building();
                $userDetails = $userDetails[0];
                
                $buldingArray = array('building' => $build_id);
                if ($userDetails != '') {
                    
                    $bd = $building->getbuildingbyid($build_id);
                    $build = $bd[0];
                   
                    $buildingDetail = $building->getBuildingList($buldingArray);
                    //print_r($buildingDetail);
                    //die();
                    $detail = array(
                        "name" => $userDetails->firstName . " " . $userDetails->lastName,
                        "title" => $userDetails->Title,
                        "office_phone" => $userDetails->phoneNumber,
                        "email" => $userDetails->email,
                        "username" => $data['userName'],
                        "userPassowd" => "**************",
                        "access" => $roleDetails[0]['title'],
                        "firstName" => $userDetails->firstName,
                        "lastName" => $userDetails->lastName,
                        "fullname" => $userDetails->firstName . " " . $userDetails->lastName,
                        "userRole" => $roleDetails[0]['title'],
                        "userEmail" => $userDetails->email,
                    );

                    $detail['buildingPhoneNumber'] = $build['phoneNumber'];
                    $detail['buildingAddress1'] = $build['address'];
                    $detail['buildingAddress2'] = $build['address2'];
                    $detail['buildingCity'] = $build['city'];
                    $detail['buildingState'] = $build['state'];
                    $detail['buildingPostalCode'] = $build['postalCode'];
                    
                    $detail['gpass'] = $gpass;
                    foreach ($buldingArray as $building) {
                        $detail['building'][] = array(
                            "building" => $this->getBuildingName($buildingDetail, $building)
                        );
                    }
                    //print_r($detail); die; 

                    if ($userDetails->userName != $data['userName']) {
                        $res = $userModel->sendUserMail($detail, 22);
                    }

                    if ($password) {
                        $res = $userModel->sendUserMail($detail, 23);
                    }
                }
            }
        }
        exit(0);
    }

    public function sendemailAction() {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $message = array();
            $data = $this->getRequest()->getPost();

            $buildingIdArray = array();
            $userModel = new Model_User();
            $building = new Model_Building();

            $userDetails = $userModel->getUserById($data['uid']);

            $userDetails = $userDetails[0];
            $roleModel = new Model_Role();
            $roleDetails = $roleModel->getRole($userDetails->role_id);

            $buldingArray = array('building' => $data['bid']);
            /* 	$userBuildModule = new Model_UserBuildingModule();
              $allUserBuilding = $userBuildModule->getBuildingByUserId($userDetails->uid);
              foreach($allUserBuilding as $allUserBuildingValue) {
              $buildingIdArray[] = $allUserBuildingValue['building_id'];
              }
              print_r($buildingIdArray); die; */
            if ($userDetails != '') {

                $buildingDetail = $building->getBuildingList($buldingArray);
               // print_r($buildingDetail);
                
                $getbuildingDetails = $building->getbuildingbyid($buildingDetail[0]['build_id']);
                $detail = array(
                    "name" => $userDetails->firstName . " " . $userDetails->lastName,
                    "title" => $userDetails->Title,
                    "office_phone" => $userDetails->phoneNumber,
                    "email" => $userDetails->email,
                    "username" => $userDetails->userName,
                    "userPassowd" => "**************",
                    "access" => $roleDetails[0]['title'],
                    "firstName" => $userDetails->firstName,
                    "lastName" => $userDetails->lastName,
                    "fullname" => $userDetails->firstName . " " . $userDetails->lastName,
                    'title'    =>$userDetails->Title,
                );
                $getbDetails = $getbuildingDetails[0];
                $detail['buildingPhoneNumber'] = $getbDetails['phoneNumber'];
                $detail['buildingAddress1'] =$getbDetails['billAddress'];
                $detail['buildingAddress2'] =$getbDetails['billAddress2'];
                $detail['buildingCity'] =$getbDetails['billcity'];
                $detail['buildingState'] =$getbDetails['billState'];
                $detail['buildingPostalCode'] =$getbDetails['billPostalCode'];

                $gpass = $this->generateRandomString();
                $detail['gpass'] = $gpass;
                foreach ($buldingArray as $building) {
                    $detail['building'][] = array(
                        "building" => $this->getBuildingName($buildingDetail, $building)
                    );
                }

                $userModel->updateUser(array('password' => md5($gpass)), $userDetails->uid);
                //print_r($detail); die;

                try {
                    $res = $userModel->sendUserMail($detail, 21);
                    $message['status'] = 'success';
                    $message['msg'] = 'Notification email successfully sent.';
                } catch (Exception $e) {
                    $message['status'] = 'error';
                    $message['msg'] = 'Error occured during sending welcome letter..';
                }
            }
            echo json_encode($message);
        }
        exit(0);
    }

    function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public function getBuildingName($buildingDetail, $building) {
        foreach ($buildingDetail as $key => $data) {
            if ($data['build_id'] == $building) {
                return $data['buildingName'];
            }
        }
    }

    public function useraccessAction() {
        $this->_helper->layout()->disableLayout();
        $uid = $this->_getParam('uid', '');
        $bid = $this->_getParam('bid', '');

        $userModel = new Model_User();
        $userDetails = $userModel->getUserById($uid);
        $plModel = new Model_ParentLocation();
        $plocation = $plModel->getParentLocation();
        $this->view->plocation = $plocation;
        $this->view->uid = $uid;
        $accessModel = new Model_Access();
        $accessDetails = $accessModel->getUserAccess($userDetails[0]->role_id);
        $uaccessModel = new Model_UserAccess();
        $accessCustomDetails = $uaccessModel->getUserCustomAccess($uid);
        $as=$userModel->getmoduleOfBuilding($bid);
        $acs=count($as);
        //$this->view->access=2;
        if($acs==1){
            $accessDetails=$this->removeModuleMatrix($accessDetails,$as);
            
        }
        $this->view->access=$acs;
        //$this->view->build_id=$acs;
        $this->view->accessDetails = $accessDetails;
        $this->view->accessCustomDetails = $accessCustomDetails;
    }
    
    public function removeModuleMatrix($accessDetails,$acs){
        //$permition=$acs[0]->module_id;
        $accedat1=array();
        $i=1;
        
            foreach($accessDetails as $gdata){
                if($gdata->location_id =='24' || $gdata->location_id =='25' || $gdata->location_id =='26'){
                    //echo "sanjay";
                    continue;
                }
                $accedat=array();
                $accedat['is_access']=$gdata->is_access;
                $accedat['is_read']=$gdata->is_read;
                $accedat['is_write']=$gdata->is_write;
                $accedat['location_id']=$gdata->location_id;
                $accedat['name']=$gdata->name;
                $accedat1[]= (object) $accedat;             
                
            }            
        return $accedat1;
    }
    
    public function saveuseraccessAction() {

        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $message = '';
            foreach ($data as $key => $useraccess) {
                $insertData = $useraccess;
                $insertData['location_id'] = $key;
                $uaccessModel = new Model_UserAccess();
                $accessDetails = $uaccessModel->isUserAccessExist($insertData['user_id'], $insertData['location_id']);
                if ($accessDetails) {
                    try {
                        $uaccessModel->updateUserAccess($insertData, $insertData['user_id'], $insertData['location_id']);
                        $message['status'] = 'success';
                        $message['msg'] = 'Access matrix save successfully.';
                    } catch (Exception $e) {
                        $message['status'] = 'error';
                    }
                } else {
                    try {
                        $uaccessModel->insertUserAccess($insertData);
                        $message['status'] = 'success';
                        $message['msg'] = 'Access matrix save successfully.';
                    } catch (Exception $e) {
                        $message['status'] = 'error';
                    }
                }
            }
            echo json_encode($message);
            exit(0);
        }
    }
    
    public function logouttimeAction(){
        
        $data = $this->getRequest()->getPost();
        $uid = $data['uid'];
        $time = $data['time'];
        $message=array();
//        die("sanjay");
        if(!empty($data)){
            $user = new Model_User();
            $status=$user->getupdatelogouttime($uid,$time);
            if($status){
                $message['status'] = 'success';
                $message['msg'] = 'Save successfully.';
            }else{
                $message['status'] = 'error';
                $message['msg'] = 'Not save successfully.';
            }
        }
        echo json_encode($message);
        exit(0);
        //$user = new Model_User();
        
        
        
    }

}
