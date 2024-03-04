<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmailsController
 *
 * @author Anuj Kumar
 */
class EmailsController extends Ve_Controller_Base {

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
        $this->accessHelper = $this->_helper->access;
        $this->dist_location = 14;
        $this->etemplate_location = 21;
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
    }

    // to list the templates
    public function listAction() {        
        
        $companyListing = '';
        $select_build_id = "";
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
        if(!empty($companyListing))
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
        $this->view->companyListing=$companyListing;
        $this->view->select_build_id=$select_build_id;
        $msgId = $this->_getParam('msg', 0);
        $page = $this->_getParam('page', 1);
        //var_dump($this->_getParams());
        $msg = '';
        if ($msgId == 1) {
            $msg = 'Email template save successfully.';
        }

        if ($msgId == 2) {
            $msg = 'Error occurred.';
        }

        if ($msgId == 3) {
            $msg = 'Bad request.';
        }

        if ($msgId == 4) {
            $msg = 'Email template deleted successfully.';
        }

        $em = new Zend_Session_Namespace('etemp_message');
        if (!isset($em->msg)) {
            $em->msg = $msg;
            if (isset($page) && $page != 1)
                $this->_redirect('/emails/list/page/' . $page);
            else
                $this->_redirect('/emails/list');
        }
        $emailModel = new Model_Email();
        $eid = '';
        if($this->roleId==1){
            $emailData = $emailModel->loadEmailTemplate($eid, $this->userId);
        }else{
            $emailData = $emailModel->loadEmailTemplate($eid, "", 8 ,$select_build_id);
        }
        
        $pageObj = new Ve_Paginator();
        $paginator = $pageObj->fetchPageDataResult($emailData, $page, 15);
        $this->view->emailData = $paginator;
        $this->view->roleId = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->etemplate_location = $this->etemplate_location;
    }

    public function logAction() {

        if ($this->userId == 1 && $_SERVER['REMOTE_ADDR'] == '115.112.129.194') {
            $email = $this->_getParam('email');

            if (!empty($email)) {
                $logModel = new Model_Log();
                $result = $logModel->selectLogByEmail($email);
                $this->view->logList = $result;
                $this->view->email = $email;
            }
        } else {
            $this->_redirect('/list');
        }
    }

    public function createAction() {
        
        $roleMapper = new Model_Role();
        $roleDetail = $roleMapper->getRole();
        $build_ID = $this->_getParam('bid', '');
        $this->view->roleDetails = $roleDetail;
        $this->view->roleId = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->build_id = $build_ID;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->etemplate_location = $this->etemplate_location;
    }

    /*     * *
     * Save Email template
     */

    public function saveemailtempAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->getRequest()->getPost();
        try {
            $inserData = array();
            $inserData['email_title'] = $data['title'];
            $inserData['email_subject'] = $data['subject'];
            $inserData['system_generated'] = $data['system_generated'];
            $inserData['email_content'] = $data['email_content'];
            $inserData['email_location'] = ($data['email_location']==""?0:$data['email_location']);
            $inserData['type'] = ($data['type']==""?0:$data['type']);
            $inserData['user_id'] = $this->userId; 
            $inserData["build_id"]=$data['build_id'];
            
            //print_r($inserData);
            
            //die();
            $emailModel = new Model_Email();
            if (isset($data['role_id'])) {
                if ($data['role_id'] == 4 || $data['role_id'] == 5) {
                    $emailModel->disableEmail(array('status' => 0), $this->userId, $data['email_location']);
                }
            }
            $res = $emailModel->insertEmail($inserData);
            $msg = 1;
        } catch (Exception $e) {
            $msg = 2;
        }
        if ($data['email_location'] == 9) {
            $this->_redirect('/conference/conflist/msg/' . $msg);
        } else {
            $this->_redirect('/emails/list/msg/' . $msg);
        }
    }

    /*     * *
     * Edit Email template
     */

    public function editemailAction() {
        $query = $this->_request->getParams();

        if (isset($query['id'])) {
            $emailModel = new Model_Email();
            $emailData = $emailModel->loadEmailTemplate($query['id']);
            $this->view->emailData = $emailData[0];
            $roleMapper = new Model_Role();
            $roleDetail = $roleMapper->getRole();
            $this->view->roleDetails = $roleDetail;
        } else {
            $this->_redirect('/emails/list');
        }
        $this->view->roleId = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->etemplate_location = $this->etemplate_location;
    }

    /*     * *
     * Update Email template
     */

    public function updateemailAction() {
        $data = $this->getRequest()->getPost();
        $msg = 0;
        if (isset($data)) {
            $eid = $data['email_id'];
            unset($data['email_id']);
            try {
                $updateData = array();
                $updateData['email_title'] = $data['title'];
                $updateData['email_subject'] = $data['subject'];
                $updateData['email_content'] = $data['email_content'];
                $updateData['status'] = $data['status'];
                $emailModel = new Model_Email();
                if (isset($data['role_id'])) {
                    if ($data['role_id'] == 4 || $data['role_id'] == 5) {
                        $updateData['status'] = $data['status'];
                        $emailModel->disableEmail(array('status' => 0), $this->userId, $data['email_location']);
                    }
                }
                $res = $emailModel->updateEmail($updateData, $eid);
                $msg = 1;
            } catch (Exception $e) {
                $msg = 2;
            }
        } else {
            $msg = 3;
        }
        if($data['email_location'] == 9 && $data['role_id'] != 1) {
            $this->_redirect('/conference/conflist/msg/' . $msg);
        } else {
            $this->_redirect('/emails/list/msg/' . $msg);
        }
    }

    /**
     * Delete email content
     */
    public function deleteemailAction() {
        $query = $this->_request->getParams();
        $msg = 0;
        if (isset($query['id'])) {
            try {
                $emailModel = new Model_Email();
                $res = $emailModel->deleteEmail($query['id']);
                $msg = ($res) ? 4 : 2;
            } catch (Exception $e) {
                $msg = 2;
            }
        } else {
            $msg = 3;
        }
        if ($query['email_location'] == 9) {
            $this->_redirect('/conference/conflist/msg/' . $msg);
        } else {
            $this->_redirect('/emails/list/msg/' . $msg);
        }
    }
    
     /**
     * Delete email content
     */
    public function deleteAction() {
        $query = $this->_request->getParams();
        echo $id=$this->_getParam('id', '');
        print_r($query);
        die;
        $msg = 0;
        if (isset($query['id'])) {
            try {
                $emailModel = new Model_Email();
                $res = $emailModel->deleteEmail($id);
                $msg = ($res) ? 4 : 2;
            } catch (Exception $e) {
                $msg = 2;
            }
        } else {
            $msg = 3;
        }
        if ($query['email_location'] == 9) {
            $this->_redirect('/conference/conflist/msg/' . $msg);
        } else {
            $this->_redirect('/emails/list/msg/' . $msg);
        }
    }

    /**
     * open send email Form
     */
    public function sendAction() {

        $emailModel = new Model_Email();
        $userModel = new Model_User();
        $data = $this->getRequest()->getPost();
        $user_data = $userModel->getAllUserOfBuildingAdmin($this->userId);
        $msg = '';
        $error = '';
        $sef = new Zend_Session_Namespace('send_email_form');
        if (isset($data) && isset($data['send_user'])) {
            //echo 'hello';
            $formKey = $sef->form_key;
            if ($formKey == $data['form_key']) {
                try {
                    if (isset($data['email_template'])) {
                        $loadTemplate = $emailModel->loadEmailTemplate($data['email_template']);
                        $email_Template = $loadTemplate[0];
                        $email_subject = $email_Template['email_subject'];
                        $email_content = $email_Template['email_content'];
                        $send_email = '';
                        if (isset($data['send_all'])) {
                            if ($user_data) {
                                foreach ($user_data as $ud) {
                                    $send_email = (($send_email) ? ',' : '') . $ud->email;
                                }
                            }
                        } else {
                            if (isset($data['building_user']) && $data['building_user'] != '') {
                                $buildEmail = explode(',', $data['building_user']);
                                foreach ($buildEmail as $bem)
                                    $send_email = (($send_email) ? ',' : '') . $bem;
                            }
                        }
                        $mail = new Zend_Mail('utf-8');
                        // configure base stuff
                        if (isset($data['send_user']) && $data['send_user'] != '') {
                            $emails = explode(',', $data['send_user']);
                            foreach ($emails as $em)
                                $send_email = (($send_email) ? ',' : '') . $em;
                        }

                        $from_name = Zend_Auth::getInstance()->getStorage()->read()->firstName . ' ' . Zend_Auth::getInstance()->getStorage()->read()->lastName;
                        $from_email = Zend_Auth::getInstance()->getStorage()->read()->email;
                        $mail->addTo($send_email);
                        $mail->setSubject($email_subject);
                        $mail->setFrom($from_email, $from_name);
                        $mail->setBodyHtml($email_content);
                        $mail->send();
                        $msg = 'Email send successfully.';
                    } else
                        $error = 'Bad request';
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            } else {
                $error = 'Invalid form key';
            }
        }

        $sef->form_key = time();
        $emailData = $emailModel->loadEmailTemplate();
        $this->view->emailData = $emailData;
        $this->view->roleId = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->form_key = $sef->form_key;
        $this->view->msg = $msg;
        $this->view->error = $error;
        $this->view->user_data = $user_data;
        $this->view->roleId = $this->roleId;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->etemplate_location = $this->etemplate_location;
    }

    public function consoleAction() {
        $msgId = $this->_getParam('msg');
        $gid = $this->_getParam('gid');
        $search_array = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $search_array['search_by'] = $data['search_by'];
            $search_array['search_value'] = $data['search_value'];
            $this->view->search = $search_array;
        }

        $tm = new Zend_Session_Namespace('email_message');
        if ($msgId == 1) {
            $tm->msg = 'Email user updated successfully.';
        }

        if ($msgId == 2) {
            $tm->msg = 'Email Group updated successfully.';
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

        //$userBuilding = $buildingMapper->getCompanyBuilding($this->cust_id);

        $select_build_id = $this->_getParam('bid', '');

        foreach ($companyListing as $cl) {
            $buildIds[] = $cl['build_id'];
        }

        if (empty($select_build_id) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $select_build_id = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $select_build_id, time() + (86400 / 24), "/");

        if ($companyListing != '') {
            if ($select_build_id != '')
                $select_build_id = $select_build_id;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }

        $page = $this->_getParam('page', 1);
        $pageObj = new Ve_Paginator();
        $show = $this->_getParam('show', '');
        if($show==""){
           $show=10; 
        }
        $search_array = array_map("addslashes", $search_array);
        $search_array = array_map("addslashes", $search_array);
        $search_array = array_map("addslashes", $search_array);
        
        $email_group_model = new Model_EmailGroup();
        $emailGroup = $email_group_model->get_email_group_by_building_id($select_build_id);

        if(!empty($search_array)){
            $emailGroup = $email_group_model->get_email_group_by_building_id_searching($select_build_id,"1",$search_array);
        }
        if($show=='all'){
            $emailGroup = $pageObj->fetchPageDataResult($emailGroup, $page, $show);      
        }else{
            $emailGroup = $pageObj->fetchPageDataResult($emailGroup, $page, $show);      
        }
        
        $this->view->show=$show;
        $this->view->companyListing = $companyListing;
        $this->view->custID = $this->cust_id;
        $this->view->roleId = $this->roleId;
        $this->view->select_build_id = $select_build_id;

        if ($gid)
            $this->view->groupid = $gid;

        $this->view->email_group = $emailGroup;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->dist_location = $this->dist_location;
        $this->view->userId = $this->userId;
    }

    public function addemailgroupAction() {
        $this->_helper->layout()->setLayout('popuplayout');

        $building_id = $this->_getParam('bid');

        $send_as = new Model_SendAs();

        $week_days = new Model_WeekDays();

        $user_model = new Model_User();
        //$users = $user_model->getAllUserOfBuilding($building_id);

        $companyModel = new Model_Company();
        $nottenant = 1; // this for not listing the tenant user here.
        $users = $companyModel->getUserByBuildingId($building_id, $nottenant);

        $this->view->days_of_the_week = $week_days->getWeekDays();
        $this->view->send_as = $send_as->getData();
        $this->view->bid = $building_id;
        $this->view->userList = $users;
    }

    public function loademailgroupuserAction() {

        $data = $this->getRequest()->getPost();
        $this->_helper->layout()->disableLayout();
        $groupId = $data['gid'];
        $buildingId = $data['bid'];
        $email_group_model = new Model_EmailGroup();
        $email_group_user_model = new Model_EmailGroupUsers();

        $users = $email_group_user_model->getGroupUsers($groupId);

        $this->view->roleId = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->cust_id = $this->cust_id;
        $this->view->group_id = $groupId;
        $this->view->groupusers = $users;
        $this->view->buildingId = $buildingId;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->dist_location = $this->dist_location;
    }

    public function deletetuserfromgroupAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $message = array();

        $data = $this->getRequest()->getPost();

        $id = $data['id'];
        $gid = $data['gid'];

        $email_group_user_model = new Model_EmailGroupUsers();

        $res = $email_group_user_model->deleteEmailUser($id, $gid);
        if ($res)
            echo true;
        else
            echo false;

        exit(0);
    }

    public function deleteemailgroupAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->getRequest()->getPost();
        $groupId = $data['gid'];
        $buildingId = $data['bid'];

        $groupdata['status'] = '0';

        $email_group_model = new Model_EmailGroup();
        $res = $email_group_model->updateGroup($groupdata, $groupId);

        if ($res)
            echo true;
        else
            echo false;
    }

    public function editemailgroupAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $building_id = $this->_getParam('buildingId');
        $groupId = $this->_getParam('gid');

        if ($this->getRequest()->getMethod() == 'POST') {

            $data = $this->getRequest()->getPost();

            $old_user = $data['old_user'];
            //print_r($old_user);			
            $selected_user = $data['to_select_list'];
            //print_r($selected_user);
            $i = 0;
            $j = 0;

            $edit = array();
            $add = array();
            $delete = array();
            foreach ($selected_user as $key => $value) {
                if (in_array($value, $old_user)) {
                    $edit[$i] = $value;
                    $i++;
                } else {
                    $add[$j] = $value;
                    $j++;
                }
            }


            $gid = $data['gid'];
            $i = 0;
            foreach ($old_user as $key => $value) {
                if (!in_array($value, $selected_user)) {
                    $delete[$i] = $value;
                    $i++;
                }
            }

            $groupData['group_name'] = $data['group'];
            if ($data['is_default'] != '1') {
                $groupData['active'] = $data['active'];
                $groupUserData['days_of_week'] = $data['days_of_week'];
            } else {
                $groupData['active'] = 1;
                $groupUserData['days_of_week'] = 1;
            }
            $groupData['updated_date'] = date('Y-m-d H:i:s');

            $groupUserData['send_as'] = $data['send_as'];
            $groupUserData['complete_notification'] = $data['complete_notification'];
            $groupUserData['updated_date'] = date('Y-m-d H:i:s');
            $groupUserData['group_id'] = $gid;

            if (isset($data['send_as']))
                $groupData['send_as'] = $data['send_as'];
            if (isset($data['complete_notification']))
                $groupData['complete_notification'] = $data['complete_notification'];
            if (isset($data['days_of_week']))
                $groupData['days_of_week'] = $data['days_of_week'];

            $email_group_model = new Model_EmailGroup();
            $email_group_user_model = new Model_EmailGroupUsers();



            if (!empty($add)) {
                foreach ($add as $key => $value) {
                    $groupUserData['user_id'] = $value;
                    $res = $email_group_user_model->saveGroupUsers($groupUserData);
                    unset($groupUserData['user_id']);
                }
            }

            if (!empty($delete)) {
                foreach ($delete as $key => $value) {
                    $groupUserData['user_id'] = $value;
                    $res = $email_group_user_model->deleteEmailUser($value, $gid);
                    unset($groupUserData['user_id']);
                }
            }

            if (!empty($edit)) {

                $res = $email_group_user_model->updateByGroup($groupUserData, $gid);
            }


            if (!empty($groupData)) {
                $res = $email_group_model->updateGroup($groupData, $gid);
            }

            if ($res) {
                //$this->_redirect('/emails/console/gid/'.$gid.'/bid/'.$data['bid'].'/msg/2'); 
                $json_data['msg'] = "Record Successfully Updated";
                $json_data['url'] = '/emails/console';
                echo json_encode($json_data);
                exit;
            }
        } else {
            $send_as = new Model_SendAs();

            $week_days = new Model_WeekDays();

            $user_model = new Model_User();

            $email_group_model = new Model_EmailGroup();
            $email_group_user_model = new Model_EmailGroupUsers();


            $emailGroups = $email_group_model->getGroups($groupId);


            $emailUsers = $email_group_user_model->getGroupUsers($groupId);

            /* $emailGroups['0']['send_as']= $emailUsers['0']->send_as;
              $emailGroups['0']['days_of_week']= $emailUsers['0']->days_of_week;
              $emailGroups['0']['complete_notification']= $emailUsers['0']->complete_notification; */

            //$users = $user_model->getAllAccountUserOfBuilding($building_id);

            $companyModel = new Model_Company();
            $nottenant = 1; // this for not listing the tenant user here.
            $users = $companyModel->getUserByBuildingId($building_id, $nottenant);
            $group_active = 'true';
            $categoryModel = new Model_Category();
            $catlist = $email_group_model->getCategoryByGroupId($groupId);

            if ($catlist) {
                $group_active = 'false';
            }

            $this->view->days_of_the_week = $week_days->getWeekDays();
            $this->view->send_as = $send_as->getData();
            $this->view->bid = $building_id;
            $this->view->userList = $users;
            $this->view->selectedUsers = $emailUsers;
            $this->view->group_active = $group_active;
            $this->view->emailGroups = $emailGroups['0'];
        }
    }

    public function editemailuserAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $email_group_user_id = base64_decode($this->_getParam('uid'));
        $email_group_user_model = new Model_EmailGroupUsers();
        $buildingId = $this->_getParam('buildingId');

        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $groupId = $data['groupId'];
            $userData['days_of_week'] = $data['days_of_week'];
            $userData['send_as'] = $data['send_as'];
            $userData['complete_notification'] = $data['complete_notification'];
            if ($email_group_user_model->updateEmailGroupUser($userData, $data['id'])) {
                //$this->_redirect('/emails/console/gid/'.$groupId.'/bid/'.$data['buildingId'].'/msg/1');
                $json_data['msg'] = "Record Successfully Updated";
                $json_data['url'] = '/emails/console/gid/' . $groupId . '/bid/' . $data['buildingId'] . '/msg/1';
                echo json_encode($json_data);
                exit;
            }
        } else {
            $userData = $email_group_user_model->getUsers($email_group_user_id);

            $this->view->userDetial = $userData['0'];
            $this->view->buildingId = $buildingId;
        }
    }

    public function saveusersAction() {

        $data = $this->getRequest()->getPost();
        $email_group_model = new Model_EmailGroup();
        $email_group_user_model = new Model_EmailGroupUsers();

        $email_group_data['group_name'] = $data['group'];
        $email_group_data['building_id'] = $data['bid'];
        $email_group_data['created_by'] = $this->userId;
        $email_group_data['active'] = $data['active'];
        $email_group_data['action'] = $data['group'];
        $email_group_data['updated_date'] = date('Y-m-d H:i:s');

        $group_id = $email_group_model->saveEmailGroup($email_group_data);
        $res = false;
        if ($group_id > 0) {
            $res = $email_group_user_model->saveEmailGroupUsers($data, $group_id);
        }

        if ($res) {
            //$this->_redirect('/emails/console/bid/'.$email_group_data['building_id']);
            $json_data['msg'] = "Record Successfully Updated";
            $json_data['url'] = '/emails/console';
            echo json_encode($json_data);
            exit;
        }
    }

    public function checkgroupAction() {
        $query = $this->_request->getParams();
        $email_group_model = new Model_EmailGroup();

        $groupExist = $email_group_model->validateGroupName($query['group_name'], $query['building']);
        if ($groupExist != false) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit;
    }

    public function checkeditgroupAction() {
        $query = $this->_request->getParams();
        $email_group_model = new Model_EmailGroup();

        $groupExist = $email_group_model->validateGroupName($query['group_name'], $query['building'], $query['gid']);
        if ($groupExist != false) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit;
    }

}

?>
