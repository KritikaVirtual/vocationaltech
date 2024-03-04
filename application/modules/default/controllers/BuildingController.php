<?php

class BuildingController extends Ve_Controller_Base {

    private $userId = '';
    private $roleId = '';

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
    }

    // Call befor any action and check is user login or not
    public function preDispatch() {
        if (!Zend_Auth::getInstance()->hasIdentity())
            $this->_redirect('/index');
        $level = (Zend_Auth::getInstance()->getStorage()->read()) ? Zend_Auth::getInstance()->getStorage()->read()->role_id : '';
        $this->userId = Zend_Auth::getInstance()->getStorage()->read()->uid;
        $this->roleId = Zend_Auth::getInstance()->getStorage()->read()->role_id;
    }

    public function indexAction() {
        $accountMapper = new Model_Account();
        $page = $this->_getParam('page', 1);
        $pageObj = new Ve_Paginator();
        $order = $this->_getParam('order', 'cust_id');
        $dir = $this->_getParam('dir', 'desc');
        $companyList = $accountMapper->getCompanyList($order, $dir);
        $paginator = $pageObj->fetchPageDataResult($companyList, $page, 10);
        $this->view->companyListing = $paginator;
        $this->view->order = $order;
        $this->view->dir = $dir;
    }

    public function showbuildinglistAction() {
        $data = array();
        $this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        //$this->_helper->getHelper('layout')->disableLayout();
        $buildrecord = false;
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $cust_id = (int) $this->getRequest()->getPost('companyID');
            $buildingMapper = new Model_Building();
            $buildList = $buildingMapper->getbuilding($cust_id);
            $this->view->cust_id = $cust_id;
            $this->view->buildList = $buildList;
            $buildrecord = true;
        }
        $this->view->buildrecord = $buildrecord;
    }

    public function addbuildingAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $today = date("Y-m-d H:i:s");
            $accountNumber = $this->getRequest()->getPost('accountNumber');
            $datacontent = array();
            $datacontent['cust_id'] = $this->getRequest()->getPost('companyID');
            $datacontent['user_id'] = $this->userId;
            $datacontent['buildingName'] = $this->getRequest()->getPost('buildingName');
            $datacontent['address'] = $this->getRequest()->getPost('address');
            $datacontent['address2'] = $this->getRequest()->getPost('address2');
            $datacontent['city'] = $this->getRequest()->getPost('city');
            $datacontent['state'] = $this->getRequest()->getPost('state');
            $datacontent['postalCode'] = $this->getRequest()->getPost('postalCode');
            $datacontent['phoneNumber'] = $this->getRequest()->getPost('phoneNumber');
            $datacontent['phoneExt'] = $this->getRequest()->getPost('phoneExt');
            $datacontent['faxNumber'] = $this->getRequest()->getPost('faxNumber');

            //billing fields
            $datacontent['billCompanyName'] = @$this->getRequest()->getPost('buildingName');
            $datacontent['billAddress'] = @$this->getRequest()->getPost('address');
            $datacontent['billAddress2'] = @$this->getRequest()->getPost('address2');
            $datacontent['billSuite'] = '';
            $datacontent['billCity'] = @$this->getRequest()->getPost('city');
            $datacontent['billState'] = @$this->getRequest()->getPost('state');
            $datacontent['billpostalCode'] = @$this->getRequest()->getPost('postalCode');
            $datacontent['billPhone'] = @$this->getRequest()->getPost('phoneNumber');
            $datacontent['billPhoneExt'] = @$this->getRequest()->getPost('phoneExt');
            $datacontent['billFax'] = @$this->getRequest()->getPost('faxNumber');
            $datacontent['attention'] = '';

            $datacontent['uniqueCostCenter'] = $this->getRequest()->getPost('costCenter');
            $datacontent['dateCreated'] = $today;
            $datacontent['status'] = $this->getRequest()->getPost('status');

            $buildingMapper = new Model_Building();
            $ress = $buildingMapper->addBuilding($datacontent);
            //	$data['status']  = 'success';
            $data['message'] = 'New Building has been created successfully.';
        } else {
            //    $data['status']  = 'error';
            $data['message'] = 'you are doing bad request';
        }
        echo $data['message'];
        exit();
    }

    public function deletebuildingAction() {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $buildingMapper = new Model_Building();
            $buildID = $this->_request->getPost('buildID');
            $resultDel = $buildingMapper->deleteBuilding($buildID);
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
    }

    public function editbuildingAction() {
        $this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $search_array = array();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $buildingID = (int) $this->getRequest()->getPost('buildingID');
            $buildingMapper = new Model_Building();
            $resultBuild = $buildingMapper->getbuildingbyid($buildingID);
            $cust_id = $resultBuild[0]['cust_id'];
            $accountMapr = new Model_Account();
            $accres = $accountMapr->getcompany($cust_id);
            $tModel = new Model_TimeZone();
            $tzonelist = $tModel->getTimeZone();
            $corp_account_number = $accres[0]['corp_account_number'];
            $moduleMapper = new Model_Module();
            $moduleList = $moduleMapper->getModuleListing(array(), $search_array);
            $buildingAccessMapper = new Model_UserBuildingModule();
            $attachModule = $buildingAccessMapper->getModuleByBuildingId1($buildingID);
            $this->view->attachModule = $attachModule;
            $this->view->moduleList = $moduleList;
            $this->view->resultBuild = $resultBuild[0];
            $this->view->corp_account_number = $corp_account_number;
            $this->view->cust_id = $cust_id;
            $this->view->states = $accountMapr->getStates();
        }
    }

    public function updatebuildingAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $today = date("Y-m-d H:i:s");
            $accountNumber = $this->getRequest()->getPost('accountNumber');
            $datacontent = array();
            $buildingID = $this->getRequest()->getPost('buildingID');
            $datacontent['cust_id'] = $this->getRequest()->getPost('companyID');
            $datacontent['user_id'] = $this->userId;
            $datacontent['buildingName'] = $this->getRequest()->getPost('buildingName');
            $datacontent['address'] = $this->getRequest()->getPost('address');
            $datacontent['address2'] = $this->getRequest()->getPost('address2');
            $datacontent['city'] = $this->getRequest()->getPost('city');
            $accountmap = new Model_Account();
            $statename = $accountmap->getStatesByCode($this->getRequest()->getPost('state'));
            $datacontent['state'] = $statename[0]->state;
            $datacontent['state_code'] = $this->getRequest()->getPost('state');
            $datacontent['postalCode'] = $this->getRequest()->getPost('postalCode');
            $datacontent['phoneNumber'] = $this->getRequest()->getPost('phoneNumber');
            $datacontent['phoneExt'] = $this->getRequest()->getPost('phoneExt');
            $datacontent['faxNumber'] = $this->getRequest()->getPost('faxNumber');
            $datacontent['timezone'] = $this->getRequest()->getPost('timezone');
            $datacontent['uniqueCostCenter'] = $this->getRequest()->getPost('costCenter');
//			$datacontent['dateCreated'] 	= $today;
            $datacontent['status'] = $this->getRequest()->getPost('status');
            $datacontentmodule = rtrim($this->getRequest()->getPost('module_id'), ',');
            
            // Coi requirement data Insert from here code
			
			if (stripos(json_encode($datacontentmodule),'4') !== false)
			{	
			    $coidetails = new Model_CioTemplate();
			    $coidetailList = $coidetails->GetAllData();
			    if(!empty($coidetailList)){
                    $coidetails = new Model_CioRequirement();
			        $checktherequiremtdata = $coidetails->CheckByBuildingID($buildingID);
      				if(empty($checktherequiremtdata)){
					    foreach($coidetailList as $Lcoi){
					    $datacoi = array();					  
					    $datacoi['Building_ID'] = $buildingID;
					    $datacoi['uniqueCostCenter'] = $this->getRequest()->getPost('costCenter');
					    $datacoi['coi_vt_default_ID'] = $Lcoi->coi_vt_default_ID;
					    $datacoi['coi_au_defaults_Tenant'] = $Lcoi->coi_vt_defaults_Tenant;
					    $datacoi['coi_au_defaults_Vendor'] = $Lcoi->coi_vt_defaults_Vendor;
					  
			            $coidetailList = $coidetails->insertCoirequirement($datacoi);
				         }
			        } 
			    } 			    			   
            }
           // Coi requirement data Insert from here code
            
            $buildingMapper = new Model_Building();
            $buildData = $buildingMapper->getBuildingByName($datacontent['buildingName'], $datacontent['cust_id'], $buildingID);
            if ($buildData) {
                $data['status'] = 'build_error';
                $data['message'] = 'Building Name already in use.';
                echo json_encode($data);
                exit;
            }

            $ress = $buildingMapper->updateBuilding($datacontent, $buildingID);
            $datacontentmodule = explode(',', $datacontentmodule);
            $userBuildingAccess = array();
            $userBuildingAccess = array(
                "building_id" => $buildingID,
                "module_id" => $datacontentmodule,
                "last_update_date" => date('Y-m-d H:i:s'),
                "assigned_date" => date('Y-m-d H:i:s'),
            );

            if (!empty($userBuildingAccess)) {
                $Model_User_Building_Module = new Model_UserBuildingModule();
                $Model_User_Building_Module->updateOnlyBuildingModule($userBuildingAccess);
            }

            /*             * ******* update tenant status ********** */
            $tenantData = array();
            $tenantData['status'] = $this->getRequest()->getPost('status');
            $tenantModel = new Model_Tenant();
            $updateTenant = $tenantModel->updateTStatusByBid($tenantData, $buildingID);
            //$tenantData['status'] = 
            $data['status'] = 'success';
            $data['message'] = 'Building has been updated successfully.';
        } else {
            $data['status'] = 'error';
            $data['message'] = 'you are doing bad request';
        }
        echo json_encode($data);
        exit;
    }

    public function registrationAction() {
        $search_array = array();
        $moduleMapper = new Model_Module();

        $roleMapper = new Model_Role();

        $accountMapper = new Model_Account();
        $moduleList = $moduleMapper->getModuleListing(array(), $search_array);

        $this->view->moduleList = $moduleList;
        $this->view->companyList = $accountMapper->getcompany();
        $this->view->moduleList = $moduleMapper->getModule();
        $this->view->roleList = $roleMapper->getRole();
        $this->view->states = $accountMapper->getStates();
    }

    /*
     * Register new building
     */

    public function registernewbuildingAction() {
        $moduleMapper = new Model_Module();
        $roleMapper = new Model_Role();
        $userMapper = new Model_User();
        $this->view->moduleList = $moduleMapper->getModule();
        $this->view->roleList = $roleMapper->getRole();
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $message = array();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {


            //exit;


            $buildingMapper = new Model_Building();
            $companyID = $this->_request->getPost('cust_id');
            $buildingPerinfo = $this->_request->getPost('buildingPerinfo');
            parse_str($buildingPerinfo, $buildingPerData);
            $billinginfo = $this->_request->getPost('billinginfo');
            parse_str($billinginfo, $billingdata);
            $adminuserinfo = $this->_request->getPost('adminuserinfo');
            parse_str($adminuserinfo, $adminuserdata);
            $check_email = $adminuserdata['adminEmailAddress'];
            $check_company = $buildingPerData['cust_id'];

            $userDetail = $userMapper->isUserExist($check_email);
            if ($userDetail != false) {
                $user_comp = $userDetail[0]['cust_id'];
                $roleId = $userDetail[0]['role_id'];
                $notallow = array(1, 5, 7, 9);
                if ($user_comp != $check_company || $user_comp == 0 || in_array($roleId, $notallow)) {
                    $message['status'] = 'email_error';
                    $message['message'] = 'Email Already Use for Another Company.';
                    echo json_encode($message);
                    exit;
                }
            }
            if ($buildingPerData['cust_id'] != '' && $buildingPerData['buildingName'] != '') {
                $data['cust_id'] = @$buildingPerData['cust_id'];
                $data['user_id'] = $this->userId;
                $data['buildingName'] = @$buildingPerData['buildingName'];
                $data['address'] = @$buildingPerData['address'];
                $data['address2'] = @$buildingPerData['address2'];
                $data['city'] = @$buildingPerData['city'];
                $accountmap = new Model_Account();
                $statename = $accountmap->getStatesByCode($buildingPerData['state']);
                $data['state'] = @$statename[0]->state;
                $data['state_code'] = @$buildingPerData['state'];
                $data['postalCode'] = @$buildingPerData['postalCode'];
                $data['phoneNumber'] = @$buildingPerData['phoneNumber'];
                $data['phoneExt'] = @$buildingPerData['ext'];
                $data['faxNumber'] = @$buildingPerData['faxNumber'];
                $data['timezone'] = @$buildingPerData['timezone'];
                $data['status'] = @$buildingPerData['status'];
                $data['uniqueCostCenter'] = @$buildingPerData['uniqueCostCenter'];
                //billing fields
                $data['billCompanyName'] = @$billingdata['billCName'];
                $data['billAddress'] = @$billingdata['billaddress'];
                $data['billAddress2'] = @$billingdata['billaddress2'];
                $data['billSuite'] = @$billingdata['billsuite'];
                $data['billCity'] = @$billingdata['billcity'];
                $statename = $accountmap->getStatesByCode($billingdata['billstate']);
                $data['billState'] = @$statename[0]->state;
                $data['billState_code'] = @$billingdata['billstate'];
                $data['billpostalCode'] = @$billingdata['billpostalCode'];
                $data['billPhone'] = @$billingdata['billphoneNumber'];
                $data['billPhoneExt'] = @$billingdata['billPhoneExt'];
                $data['billFax'] = @$billingdata['billfaxNumber'];
                $data['attention'] = @$billingdata['attention'];

                //$data['uniqueCostCenter']=time();
                //echo '<pre>';print_r($data); die;
                $buildingId = $buildingMapper->addBuilding($data);

                //add by durgesh for building id
                $coidetails = new Model_CioTemplate();
			    $coidetailList = $coidetails->GetAllData();
			    if(!empty($coidetailList)){
                    $coidetailss = new Model_CioRequirement();
					foreach($coidetailList as $Lcoi){
                        $datacoi = array();					  
                        $datacoi['Building_ID'] = $buildingId;
                        $datacoi['uniqueCostCenter'] = $data['uniqueCostCenter'];
                        $datacoi['coi_vt_default_ID'] = $Lcoi->coi_vt_default_ID;
                        $datacoi['coi_au_defaults_Tenant'] = $Lcoi->coi_vt_defaults_Tenant;
                        $datacoi['coi_au_defaults_Vendor'] = $Lcoi->coi_vt_defaults_Vendor;
                      
                        $coidetailList = $coidetailss->insertCoirequirement($datacoi);
                    }
			    }

                //create admin user
                $userDataArray = array();
                $userDataArray['uid'] = @$adminuserdata['uid'];
                $userDataArray['cust_id'] = @$buildingPerData['cust_id'];
                $userDataArray['firstName'] = @$adminuserdata['fname'];
                $userDataArray['lastName'] = @$adminuserdata['lname'];
                $userDataArray['email'] = @$adminuserdata['adminEmailAddress'];
                $userDataArray['userName'] = @$adminuserdata['adminEmailAddress'];
                $userDataArray['password'] = md5($adminuserdata['uConfPass']);
                $userDataArray['role_id'] = @$adminuserdata['accesslevel'];
                $userDataArray['phoneNumber'] = @$adminuserdata['phone'];
                $userDataArray['phoneExt'] = @$adminuserdata['aphoneExt'];

                /* $this->createCategoryNPriority($buildingId); */

                $roleDetail = $roleMapper->getRole($userDataArray['role_id']);
                $userDetail = $userMapper->isUserExist($userDataArray['email']);
                if (!$userDetail) {
                    $detail = array(
                        "name" => $userDataArray['firstName'] . " " . $userDataArray['lastName'],
                        "email" => $userDataArray['email'],
                        "access" => $roleDetail[0]['title'],
                        "firstName" => $userDataArray['firstName'],
                        "lastName" => $userDataArray['lastName'],
                        "username" => $userDataArray['userName'],
                        'build_id' => $buildingId,
                        'cust_id' => $buildingPerData['cust_id']
                    );
                    if (isset($adminuserdata['auto_generate']) && $adminuserdata['auto_generate'] != '') {
                        $gpass = $this->generateRandomString();
                        $detail['gpass'] = $gpass;
                        $userDataArray['password'] = md5($gpass);
                    } else {
                        $detail['gpass'] = $adminuserdata['uConfPass'];
                    }
                    $userDataArray['uid'] = $userMapper->insertUser($userDataArray);
                } else {
                    $userData = $userDetail[0];
                    $detail = array(
                        "name" => $userData['firstName'] . " " . $userData['lastName'],
                        "email" => $userData['email'],
                        "access" => $roleDetail[0]['title'],
                        "firstName" => $userData['firstName'],
                        "lastName" => $userData['lastName'],
                        "username" => $userData['userName'],
                        'build_id' => $buildingId,
                        'cust_id' => $buildingPerData['cust_id']
                    );
                    $gpass = $this->generateRandomString();
                    $detail['gpass'] = "Use your previous set password for this user.<br/>(if you do not remember your password please use Forgot your password option.)";

                    $userDataArray['uid'] = $userDetail[0]['uid'];
                    $updateuserData = array('cust_id' => @$buildingPerData['cust_id']);
                    $userMapper->updateUser($updateuserData, $userData['uid']);
                }
                try {
                    $res = $this->sendBuildingWelocmeMail($detail); // sending email
                    $email_log = new Model_Log();
                    $logData = array();
                    $logData['email_sent_by'] = $this->userId;
                    $logData['userId'] = ($userDataArray['uid']) ? $userDataArray['uid'] : $userDetail[0]['uid'];
                    $logData['email'] = $detail['email'];
                    $logData['log_type'] = 'email';
                    $logData['log_message'] = 'New user created by using new building set up wizard';

                    if ($res) {
                        $logData['email_status'] = 1;
                        $email_log->insertLog($logData);
                    } else {
                        $logData['email_status'] = 0;
                        $email_log->insertLog($logData);
                    }
                } catch (Exception $e) {
                    
                }
                /* $this->createEmailGroups($userDataArray['uid'],$buildingId); */
                $this->createCategoryNPriority($buildingId, $userDataArray['uid']);


                $userBuildingAccess = array();
                $userBuildingAccess[] = array(
                    "user_id" => $userDataArray['uid'],
                    "building_id" => $buildingId,
                    "modules_id" => implode(",", @$adminuserdata['modules']),
                    "assigned_date" => date('Y-m-d H:i:s'),
                    "last_update_date" => date('Y-m-d H:i:s'),
                );

                if (!empty($userBuildingAccess)) {
                    $Model_User_Building_Module = new Model_UserBuildingModule();
                    $Model_User_Building_Module->updateBuildingModule($userBuildingAccess);
                }

                $datacontentmodule = $adminuserdata['modules'];
                $userBuildingAccessmod = array();
                $userBuildingAccessmod = array(
                    "building_id" => $buildingId,
                    "module_id" => $datacontentmodule,
                    "last_update_date" => date('Y-m-d H:i:s'),
                    "assigned_date" => date('Y-m-d H:i:s'),
                );

                if (!empty($userBuildingAccessmod)) {
                    $Model_User_Building_Module = new Model_UserBuildingModule();
                    $Model_User_Building_Module->updateOnlyBuildingModule($userBuildingAccessmod);
                }


                $this->createwoParameter($buildingId);
                $this->createLaborNRateCharge($buildingId);
                $this->createConferenceSchedule($buildingId);
                $message = array();
                $message['status'] = 'success';
                $message['message'] = 'Building has been created successfully.';
                echo json_encode($message);
                exit;
            } else {

                $message = array();
                $message['status'] = 'error';
                $message['message'] = 'Please fill the form properly.';
                echo json_encode($message);
                exit;
            }
        }
    }

    public function createConferenceSchedule($bid = '') {
        $cscheduleMapper = new Model_ConferenceSchedule();
        if ($bid != '') {
            try {
                $data = array();
                $data['schedule_name'] = 'Default';
                $data['week_days_id'] = CONWEEKDAYID;
                $data['start_time'] = CSTARTTIME;
                $data['end_time'] = CENDTIME;
                $data['building_id'] = $bid;
                $data['default'] = 1;
                $result = $cscheduleMapper->insertCSchedule($data);
            } catch (Exception $e) {
                
            }
        }
    }

    /*     * ****** Create default Email Groups******* */

    public function createEmailGroups($uid, $bid) {
        $emailGroupArray = array();
        $emailGroupUserArray = array();

        $email_group_model = new Model_EmailGroup();
        $email_group_user_model = new Model_EmailGroupUsers();

        $emailGroupArray['group_name'] = 'Default';
        $emailGroupArray['building_id'] = $bid;
        $emailGroupArray['created_by'] = $this->userId;
        $emailGroupArray['is_default'] = 1;
        $emailGroupArray['active'] = 1;
        $emailGroupArray['action'] = 1;
        $emailGroupArray['status'] = 1;
        $emailGroupArray['updated_date'] = date('Y-m-d H:i:s');

        $group_id = $email_group_model->saveEmailGroup($emailGroupArray);

        if ($group_id > 0) {
            $emailGroupUserArray['send_as'] = 1;
            $emailGroupUserArray['days_of_the_week'] = 1;
            $emailGroupUserArray['complete_notification'] = 0;
            $emailGroupUserArray['updated_date'] = date('Y-m-d H:i:s');

            $emailGroupUserArray['to_select_list'][0] = $uid;

            $email_group_user_model->saveEmailGroupUsers($emailGroupUserArray, $group_id);
        }

        return $group_id;
    }

    /*
     * Create default category & priority
     */

    public function createCategoryNPriority($bid, $uid) {
        $group_id = $this->createEmailGroups($uid, $bid);
        $uid = $this->userId;
        $tdydate = date("Y-m-d");
        $priortyModel = new Model_Priority();
        $scheduleModel = new Model_Schedule();

        $priortyData = array(
            array('priorityName' => '100 - High', 'priorityDescription' => 'High Priority', 'status' => 1, 'building_id' => $bid, 'created_by' => $uid, 'created_date' => $tdydate),
            array('priorityName' => '200 - Normal', 'priorityDescription' => 'Normal Priority (Default)', 'status' => 1, 'building_id' => $bid, 'created_by' => $uid, 'created_date' => $tdydate),
            array('priorityName' => '300 - Low', 'priorityDescription' => 'Low Priority', 'status' => 1, 'building_id' => $bid, 'created_by' => $uid, 'created_date' => $tdydate)
        );
        $highscheduleData = array(
            array('start_status' => '1', 'end_status' => '2', 'Time' => 10, 'length' => 1, 'access_days' => '1', 'status' => 1, 'created_by' => $uid, 'created_date' => $tdydate),
            array('start_status' => '2', 'end_status' => '7', 'Time' => 30, 'length' => 1, 'access_days' => '1', 'status' => 1, 'created_by' => $uid, 'created_date' => $tdydate),
            array('start_status' => '7', 'end_status' => '6', 'Time' => 1, 'length' => 4, 'access_days' => '1', 'status' => 1, 'created_by' => $uid, 'created_date' => $tdydate),
        );
        $normalscheduleData = array(
            array('start_status' => '1', 'end_status' => '2', 'Time' => 15, 'length' => 1, 'access_days' => '1', 'status' => 1, 'created_by' => $uid, 'created_date' => $tdydate),
            array('start_status' => '2', 'end_status' => '7', 'Time' => 4, 'length' => 2, 'access_days' => '1', 'status' => 1, 'created_by' => $uid, 'created_date' => $tdydate),
            array('start_status' => '7', 'end_status' => '6', 'Time' => 1, 'length' => 4, 'access_days' => '1', 'status' => 1, 'created_by' => $uid, 'created_date' => $tdydate),
        );
        $lowscheduleData = array(
            array('start_status' => '1', 'end_status' => '2', 'Time' => 15, 'length' => 1, 'access_days' => '1', 'status' => 1, 'created_by' => $uid, 'created_date' => $tdydate),
            array('start_status' => '2', 'end_status' => '7', 'Time' => 1, 'length' => 4, 'access_days' => '1', 'status' => 1, 'created_by' => $uid, 'created_date' => $tdydate),
            array('start_status' => '7', 'end_status' => '6', 'Time' => 1, 'length' => 5, 'access_days' => '1', 'status' => 1, 'created_by' => $uid, 'created_date' => $tdydate),
        );
        /*         * ***** insert priority ****** */
        $nor_pid = '';
        foreach ($priortyData as $pdata) {
            $pid = $priortyModel->insertPriority($pdata);
            if ($pdata['priorityName'] == '200 - Normal')
                $nor_pid = $pid;
            /*             * ***** insert schedule ****** */
            $scheduleData = array();
            if ($pdata['priorityName'] == '100 - High')
                $scheduleData = $highscheduleData;
            else if ($pdata['priorityName'] == '200 - Normal')
                $scheduleData = $normalscheduleData;
            else
                $scheduleData = $lowscheduleData;
            foreach ($scheduleData as $sdata) {
                $sdata['priority_id'] = $pid;
                $sid = $scheduleModel->insertSchedule($sdata);
            }
        }
        $categoryData = array(
            array('categoryName' => 'Badges', 'prioritySchedule' => $nor_pid, 'send_email' => $group_id, 'status' => 1, 'createdBy' => $uid, 'created_date' => $tdydate, 'building_id' => $bid),
            array('categoryName' => 'Cleaning', 'prioritySchedule' => $nor_pid, 'send_email' => $group_id, 'status' => 1, 'createdBy' => $uid, 'created_date' => $tdydate, 'building_id' => $bid),
            array('categoryName' => 'Electrical', 'prioritySchedule' => $nor_pid, 'send_email' => $group_id, 'status' => 1, 'createdBy' => $uid, 'created_date' => $tdydate, 'building_id' => $bid),
            array('categoryName' => 'Hot/Cold Call', 'prioritySchedule' => $nor_pid, 'send_email' => $group_id, 'status' => 1, 'createdBy' => $uid, 'created_date' => $tdydate, 'building_id' => $bid),
            array('categoryName' => 'Lighting', 'prioritySchedule' => $nor_pid, 'send_email' => $group_id, 'status' => 1, 'createdBy' => $uid, 'created_date' => $tdydate, 'building_id' => $bid),
            array('categoryName' => 'Miscellaneous', 'prioritySchedule' => $nor_pid, 'send_email' => $group_id, 'status' => 1, 'createdBy' => $uid, 'created_date' => $tdydate, 'building_id' => $bid),
            array('categoryName' => 'Plumbing', 'prioritySchedule' => $nor_pid, 'send_email' => $group_id, 'status' => 1, 'createdBy' => $uid, 'created_date' => $tdydate, 'building_id' => $bid),
            array('categoryName' => 'Security', 'prioritySchedule' => $nor_pid, 'send_email' => $group_id, 'status' => 1, 'createdBy' => $uid, 'created_date' => $tdydate, 'building_id' => $bid),
            array('categoryName' => 'Trash Removal', 'prioritySchedule' => $nor_pid, 'send_email' => $group_id, 'status' => 1, 'createdBy' => $uid, 'created_date' => $tdydate, 'building_id' => $bid)
        );
        $categoryMapper = new Model_Category();
        foreach ($categoryData as $data) {
            $cId = $categoryMapper->insertCategory($data);
        }
    }

    public function createwoParameter($bid) {
        $wpModel = new Model_WoParameter();
        $default_data = array('building' => $bid, 'status_closed' => '1', 'billable' => '', 'inc_tnt_rqt' => '', 'email_tenant' => '',
            'sale_tax' => '0', 'auto_charge' => '0', 'dft_markup' => '15', 'override_markup' => '0',
            'time_in_start' => '8:00 AM', 'time_in_incmt' => '30 Minutes', 'time_min_charge' => '30 Minutes');
        $insertData = $wpModel->insertWoParameter($default_data);
    }

    public function createdefaultAction() {
        $this->createLaborNRateCharge(61);
        exit(0);
    }

    public function createLaborNRateCharge($bid) {
        $companyModel = new Model_Company();
        $nottenant = 1; // this for not listing the tenant user here.
        $users = $companyModel->getUserByBuildingId($bid, $nottenant);
        $assign_user = '';
        if ($users) {
            foreach ($users as $user) {
                $assign_user .= ($assign_user != '') ? ',' : '';
                $assign_user .= $user->uid;
            }
        }
        try {
            /* insert default labor charge */
            $blModel = new Model_BillLabor();
            $default_labor = array('building' => $bid, 'description' => 'General Labor', 'status' => '1', 'charge_hour' => '75.00',
                'global_template' => '0', 'assign_to' => $assign_user, 'assign_default' => '1',
                'set_default' => '1'
            );

            $submitLaborCharge = $blModel->insertBillLabor($default_labor);

            /* insert default rate charge */
            $brModel = new Model_BillRate();
            $default_rate1 = array('building' => $bid, 'rate_name' => 'ST', 'description' => 'Straight Time – General Labor',
                'status' => '1', 'multiplier' => '1', 'global_template' => '0', 'set_default' => '1'
            );
            $saveRate1 = $brModel->insertBillRate($default_rate1);
            $default_rate2 = array('building' => $bid, 'rate_name' => 'OT', 'description' => 'Over Time – General Labor',
                'status' => '1', 'multiplier' => '1.5', 'global_template' => '0', 'set_default' => '0'
            );
            $saveRate2 = $brModel->insertBillRate($default_rate2);
        } catch (Exception $e) {
            $error = 'error';
        }
    }

    /*
     * check building 
     */

    public function checkbuildingAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $buildingMapper = new Model_Building();
            $buildingName = $this->_request->getPost('buildingName');
            $cust_id = $this->_request->getPost('cust_id');
            $buildData = $buildingMapper->getBuildingByName($buildingName, $cust_id);
            if ($buildData)
                echo 'true';
            else
                echo 'false';
        }
        exit(0);
    }

    public function sendBuildingWelocmeMail($detail) {
        //$emailMapper = new Model_Email();
        $uri = BASEURL;
        $htmlDocId = 11; // email template id
        $sdModel = new Model_SystemDefault();
        $sdData = $sdModel->getSystemDefault();
        //$loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
        if ($sdData) {
            $emailContent = $sdData[0];
            $emailSubject = $emailContent['subject'];
            $emailBody = $emailContent['mail_data'];
            $support_email = $emailContent['support_email'];
            $footer_info = $emailContent['footer_info'];
            $from = $emailContent['from'];
            $voc_logo = $emailContent['voc_logo'];

            $build_id = $detail['build_id'];
            $cust_id = $detail['cust_id'];

            /*             * ****Get Building data***** */
            $buildingModel = new Model_Building();
            $buildData = $buildingModel->getbuildingbyid($build_id);
            $bData = $buildData[0];

            /*             * *****Get Company Data******* */
            $accModel = new Model_Account();
            $accData = $accModel->getcompany($cust_id);
            $aData = $accData[0];

            $building_logo_src = "";

            // Company logo
            if (isset($aData['company_logo']) && !empty($aData['company_logo'])) {
                $building_logo_src = '<img src="' . $uri . '/public/images/clogo/' . $aData['company_logo'] . '">';
            } else {
                //$building_logo_src	=	'<img src="'.$uri.'/public/images/clogo/nologo.png">';				
                $building_logo_src = '';
            }

            if (isset($voc_logo) && !empty($voc_logo)) {
                $voctech_logo_src = '<img src="' . $uri . '/public/images/uploads/' . $voc_logo . '">';
            } else {
                $voctech_logo_src = "";
            }


            $emailSubject = str_replace('[[++buildingName]]', $bData['buildingName'], $emailSubject);

            $emailBody = str_replace('[[++companyLogo]]', $building_logo_src, $emailBody);
            $emailBody = str_replace('[[++voctechLogo]]', $voctech_logo_src, $emailBody);
            $emailBody = str_replace('[[++dateTime]]', date('m/d/Y h:i a'), $emailBody);
            $emailBody = str_replace('[[++costNumber]]', $aData['corp_account_number'], $emailBody);
            $emailBody = str_replace('[[++supportemailaddress]]', $support_email, $emailBody);
            $emailBody = str_replace('[[++companyName]]', $aData['companyName'], $emailBody);
            $emailBody = str_replace('[[++buildingName]]', $bData['buildingName'], $emailBody);
            $emailBody = str_replace('[[++address1]]', $bData['address'], $emailBody);
            $emailBody = str_replace('[[++address2]]', $bData['address2'], $emailBody);
            $emailBody = str_replace('[[++city]]', $bData['statename'], $emailBody);
            $emailBody = str_replace('[[++state]]', $bData['state'], $emailBody);
            $emailBody = str_replace('[[++postalCode]]', $bData['postalCode'], $emailBody);

            $emailBody = str_replace('[[++billCompanyName]]', $bData['billCompanyName'], $emailBody);
            $emailBody = str_replace('[[++billAddress1]]', $bData['billAddress'], $emailBody);
            $emailBody = str_replace('[[++billAddress2]]', $bData['billAddress2'], $emailBody);
            $emailBody = str_replace('[[++billCity]]', $bData['billcity'], $emailBody);
            $emailBody = str_replace('[[++billState]]', $bData['billState'], $emailBody);
            $emailBody = str_replace('[[++billpostalCode]]', $bData['billPostalCode'], $emailBody);
            $emailBody = str_replace('[[++attention]]', $bData['attention'], $emailBody);
            if ($bData['phoneExt'] != '' && $bData['phoneExt'] != '0') {
                $emailBody = str_replace('[[++phoneExt]]', '( ' . $bData['phoneExt'] . ' )', $emailBody);
            }
            $emailBody = str_replace('[[++phone]]', $bData['phoneNumber'], $emailBody);
            $emailBody = str_replace('[[++faxNumber]]', $bData['faxNumber'], $emailBody);
            $emailBody = str_replace('[[++userEmail]]', $detail['email'], $emailBody);

            $emailBody = str_replace('[[++username]]', $detail['username'], $emailBody);
            $emailBody = str_replace('[[++password]]', $detail['gpass'], $emailBody);
            $emailBody = str_replace('[[++role]]', $detail['access'], $emailBody);
            $weblink = "<a href='" . BASEURL . "'>" . BASEURL . "</a>";
            $emailBody = str_replace('[[++siteURL]]', $weblink, $emailBody);
            $emailBody = str_replace('[[++footerInfo]]', $footer_info, $emailBody);


            /* $mailData = '<table width="100%">';
              if($voc_logo!=''){
              $img_path= BASEURL.'/public/images/uploads/'.$voc_logo;
              $mailData .= '<tr><td><img src="'.$img_path.'" width="720"/></td></tr>';
              }
              $mailData .= '<tr><th bgcolor="silver">'.$emailSubject.'</th></tr>';
              $mailData .= '<tr><td>'.$emailBody.'</td></tr>';
              //$mailData .= '<tr><th bgcolor="silver" style="text-align:left;">'.$footer_info.'</th></tr>';
              $mailData .= '</table>';
             */
            $setModel = new Model_Setting();
            $setData = $setModel->getSetting();


            $mail = new Zend_Mail('utf-8');

            $mail->addTo($detail['email'], $detail['firstName'] . '' . $detail['lastName']);
            $mail->setSubject($emailSubject);
            $mail->setFrom($support_email, $from);
            if ($setData) {
                $setting = $setData[0];
                if ($setting['bcc_email'])
                    $mail->addBcc($setting['bcc_email'], $setting['bcc_name']);
            }
            $mail->setBodyHtml($emailBody);
            $res = $mail->send();
            return $res;
        }
    }

    /*
     * check costcenter 
     */

    public function checkcostcenterAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $buildingMapper = new Model_Building();
            $cost = $this->_request->getPost('cost');
            $buildData = $buildingMapper->getbuildingByCostCenter($cost);
            if ($buildData)
                echo 'true';
            else
                echo 'false';
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

}

?>
