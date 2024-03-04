<?php

/**
 * Description of Vendor
 *
 * @author Brijesh
 */
class VendorController extends Ve_Controller_Base {

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
        $this->vendorModel = new Model_Vendor();
        $this->vm = new Zend_Session_Namespace('vendor_message');
        $this->accessHelper = $this->_helper->access;
        $this->vendor_location = 15;
        $this->vrecovery_location = 35;
    }

// close of init function

    public function preDispatch() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/index');
        }

        $level = (Zend_Auth::getInstance()->getStorage()->read()) ? Zend_Auth::getInstance()->getStorage()->read()->role_id : '';
        $this->userId = Zend_Auth::getInstance()->getStorage()->read()->uid;
        $this->roleId = Zend_Auth::getInstance()->getStorage()->read()->role_id;
        $this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
        $this->email = Zend_Auth::getInstance()->getStorage()->read()->email;
    }

    public function indexAction() {


        $order = $this->_getParam('order', 'company_name');
        $dir = $this->_getParam('dir', 'ASC');

        $buildingMapper = new Model_Building();
        $search_array = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            if ($data['search_by'] == "services") {
                $search_array['search_by'] = $data['search_by'];
                $search_array['search_value'] = $data['service_value'];
            } else {
                $search_array['search_by'] = $data['search_by'];
                $search_array['search_value'] = $data['search_value'];
            }
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

        $build_ID = $this->_getParam('bid', '');
        foreach ($companyListing as $cl) {
            $buildIds[] = $cl['build_id'];
        }
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");



        $vendorList = false;
        $select_bid = '';
        if ($build_ID != '') {
            $select_bid = $build_ID;
        } else {
            if ($companyListing != '') {
                $select_bid = $companyListing[0]['build_id'];
            }
        }
        if (!empty($select_bid)) {
            $search_array = array_map("addslashes", $search_array);
            $search_array = array_map("addslashes", $search_array);
            $search_array = array_map("addslashes", $search_array);
            $vendorList = $this->vendorModel->getVendorByBid($select_bid, $order, $dir, $search_array);
        }
        $page = $this->_getParam('page', 1);
        $pageObj = new Ve_Paginator();
        $show = $this->_getParam('show', '');
        if($show==""){
           $show=4; 
        }
        if($show=='all'){
            $vendorList_paginator = $pageObj->fetchPageDataResult($vendorList, $page, $show);        
        }else{
            $vendorList_paginator = $pageObj->fetchPageDataResult($vendorList, $page, $show);      
        }
        $this->view->show=$show;
        $vid = $this->_getParam('vid', '');
        $this->view->vendorList = $vendorList_paginator;
        $this->view->select_build_id = $select_bid;
        $this->view->companyListing = $companyListing;
        $this->view->custID = $this->cust_id;
        $this->view->roleId = $this->roleId;
        $this->view->vm = $this->vm;
        $this->view->vId = $vid;
        $this->view->search = $search_array;
        $this->view->order = $order;
        $this->view->dir = $dir;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->vendor_location = $this->vendor_location;
        $this->view->userId = $this->userId;
    }

    public function createvendorAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $submit_flag = true;
            if ($data['company_name'] == '' || $data['first_name'] == '' || $data['services'] == '') {
                $submit_flag = false;
            }

            if ($submit_flag) {
                $vData = array();
                $vData['company_name'] = addslashes($data['company_name']);
                $vData['first_name'] = $data['first_name'];
                $vData['last_name'] = $data['last_name'];
                $vData['services'] = $data['services'];
                $vData['contact_type'] = $data['contact_type'];
                $vData['phone_number'] = $data['phone_number'];
                $vData['cell_number'] = $data['cell_number'];
                $vData['email'] = $data['email'];
                $vData['account_number'] = $data['account_number'];
                $vData['address1'] = $data['address1'];
                $vData['address2'] = $data['address2'];
                $vData['city'] = $data['city'];
                $accountmap = new Model_Account();
                $statename = $accountmap->getStatesByCode($data['state']);
                $vData['state_code'] = $data['state'];
                $vData['state'] = $statename[0]->state;
                $vData['postal_code'] = $data['postal_code'];
                $vData['emergency_contact'] = $data['emergency_contact'];
                $vData['buildingId'] = $data['buildingId'];
                $vData['global_template'] = $data['global_template'];
                try {
                    $insertVendor = $this->vendorModel->insertVendor($vData);
                    $this->vm->success = "Vendor Successfully Created!";
                    //$this->_redirect('/vendor/index/bid/'.$data['buildingId']);
                    $json_data['msg'] = $this->vm->success;
                    $json_data['url'] = '/vendor/index/bid/' . $data['buildingId'];
                    echo json_encode($json_data);
                    exit;
                } catch (Exception $e) {
                    echo $e->getMessage();
                    $this->vm->error = "Error Occurred During Creation of Vendor!";
                }
            } else {
                $build_ID = $data['buildingId'];
                $this->view->build_id = $build_ID;
                $this->vm->error = "Fill all the required field.";
            }
        } else {
            $redirectpage = $this->_getParam('redirect');
            if (isset($redirectpage)) {
                $this->view->redirectpage = $this->_getParam('redirect');
            }
            $build_ID = $this->_getParam('bid', '');
            $this->view->build_id = $build_ID;
        }
        $accountMapper = new Model_Account();
        $this->view->states = $accountMapper->getStates();
    }

// close create vendor method

    public function updatevendorAction() {

        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $id = $this->getRequest()->getPost('pk');
            $key = $this->getRequest()->getPost('name');
            $value = $this->getRequest()->getPost('value');
            $value = addslashes($value);
            if ($key == 'state_code') {
                $accountmap = new Model_Account();
                $statename = $accountmap->getStatesByCode($value);
                $data = array(
                    $key => $value,
                    'state' => $statename[0]->state,
                    'updated_at' => date('Y-m-d H:i:s')
                );
            } else {
                $data = array(
                    $key => $value,
                    'updated_at' => date('Y-m-d H:i:s')
                );
            }
            if ($key != '' && !empty($key)) {
                $res = $this->vendorModel->updateVendor($data, $id);
            }
        }
        exit;
    }

// close update vendor method

    public function updatevendornameAction() {


        $name = $this->_getParam('name');
        $value = $this->_getParam('value');
        $building = $this->_getParam('building');

        $vid = $this->_getParam('pk');
        $vendorDetail = $this->vendorModel->checkVendorByName($value, $building, $vid);
        //var_dump($tenantDetail);
        if (!empty($vendorDetail))
            echo 'true';
        else {
            $value = addslashes($value);
            $data = array(
                $name => $value,
                'updated_at' => date('Y-m-d H:i:s')
            );
            if ($name != '' && !empty($name)) {
                $res = $this->vendorModel->updateVendor($data, $vid);
            }
            echo 'false';
        }

        exit(0);
    }

// close update vendor name field

    public function loadvendorAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getPost();
        if (isset($data['vid']) && $data['vid'] != '') {
            $vdata = $this->vendorModel->getVendor($data['vid']);
            $vdata[0] = array_map("stripslashes", $vdata[0]);
            $this->view->vdata = $vdata[0];
        } else {
            echo 'Error in the vendor id.';
        }
        $accountMapper = new Model_Account();
        $this->view->states = $accountMapper->getStates();

        $this->view->roleId = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->vendor_location = $this->vendor_location;
    }

// close show vendor method

    public function checkvendorAction() {
        $cname = $this->_getParam('cname');
        $bid = $this->_getParam('bid');
        if ($cname != '' && $bid != '') {
            $vendorDetail = $this->vendorModel->checkVendorByName($cname, $bid);
            if (!empty($vendorDetail))
                echo 'true';
            else {
                echo 'false';
            }
        } else
            echo 'false';
        exit(0);
    }

    public function addserviceAction() {
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $sarray = array();
            $sarray['service'] = addslashes($data['service']);
            $sarray['building'] = $data['building'];
            try {
                $serviceModel = new Model_Services();
                $existService = $serviceModel->checkServiceByName($data['service'], $data['building']);
                if (!$existService) {
                    $insert_service = $serviceModel->insertServices($sarray);
                    echo 'true';
                } else
                    echo 'false';
            } catch (Exception $e) {
                //echo $e->getMessage();
                echo 'error';
            }
        }
        exit(0);
    }

// close add service 

    public function addcontactAction() {
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $carray = array();
            $carray['contact'] = addslashes($data['contact']);
            $carray['building'] = $data['building'];
            try {
                $contactModel = new Model_ContactType();
                $existConatct = $contactModel->checkContactByName($data['contact'], $data['building']);
                if (!$existConatct) {
                    $insert_contact = $contactModel->insertContactType($carray);
                    echo 'true';
                } else {
                    echo 'false';
                }
            } catch (Exception $e) {
                //echo $e->getMessage();
                echo 'error';
            }
        }
        exit(0);
    }

// close add contact type

    public function showserviceAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $this->view->build_id = $data['building'];
        }
    }

// close show service

    public function showcontactAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $this->view->build_id = $data['building'];
        }
    }

// close show contact

    public function checkemailAction() {
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $email = $data['email'];
            try {
                $userModel = new Model_User();
                $userEmail = $userModel->checkUserEmail($email);
                if (!$userEmail) {
                    /* $vendorEmail = $this->vendorModel->checkVendorByEmail($email);
                      if(!$vendorEmail){
                      $vcModel = new Model_VendorContact();
                      $vcEmail = $vcModel->checkVContactByEmail($email);
                      if(!$vcEmail)
                      echo 'true';
                      else
                      echo 'false';
                      }else */
                    echo 'true';
                } else {
                    echo 'false';
                }
            } catch (Exception $e) {
                //echo $e->getMessage();
                echo 'error';
            }
        }
        exit(0);
    }

// check email

    public function updatevendoremailAction() {
        $name = $this->_getParam('name');
        $value = $this->_getParam('value');
        $email = $value;
        $vid = $this->_getParam('pk');
        $update_flage = false;
        if (!empty($email)) {
            $userModel = new Model_User();
            $userEmail = $userModel->checkUserEmail($email);
            if (!$userEmail) {
                $vendorEmail = $this->vendorModel->checkVendorByEmail($email, $vid);
                if (!$vendorEmail) {
                    $vcModel = new Model_VendorContact();
                    $vcEmail = $vcModel->checkVContactByEmail($email);
                    if (!$vcEmail) {
                        $update_flage = true;
                    }
                }
            }
        } else {
            $update_flage = true;
        }

        if ($update_flage) {
            $data = array(
                $name => $value,
                'updated_at' => date('Y-m-d H:i:s')
            );
            try {
                if ($name != '' && !empty($name)) {
                    $res = $this->vendorModel->updateVendor($data, $vid);
                }
                echo 'false';
            } catch (Exception $e) {
                echo 'error';
            }
        } else {
            echo 'true';
        }

        exit(0);
    }

// close update email

    public function addvendorcontactAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $vcData = array();
            $vcData['vid'] = $data['vid'];
            $vcData['first_name'] = $data['first_name'];
            $vcData['last_name'] = $data['last_name'];
            $vcData['email'] = $data['email'];
            $vcData['phone_number'] = $data['phone_number'];
            $vcData['cell_number'] = $data['cell_number'];
            $vcData['emergency_contact'] = $data['emergency_contact'];
            try {
                $vcModel = new Model_VendorContact();
                $insertContact = $vcModel->insertVendorContact($vcData);
                $this->vm->success = "Alternate Vendor Contact Successfully Created!";
                //$this->_redirect('/vendor/index/bid/'.$data['building'].'/vid/'.$data['vid']);
                $json_data['msg'] = $this->vm->success;
                $json_data['url'] = '/vendor/index/bid/' . $data['building'] . '/vid/' . $data['vid'];
                echo json_encode($json_data);
                exit;
            } catch (Exception $e) {
                $this->vm->error = "Error Occurred During Creation of Alternat Vendor Contact !";
            }
        }
        $vid = $this->_getParam('vid');
        if (!empty($vid)) {
            $vendorData = $this->vendorModel->getVendor($vid);
            $this->view->vendorData = $vendorData;
        }
        $this->view->vid = $vid;
    }

// close add vendor contact

    public function editvendorcontactAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $vcModel = new Model_VendorContact();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $vcData = array();
            //$vcData['vid']= $data['vid'];
            $vcData['first_name'] = $data['first_name'];
            $vcData['last_name'] = $data['last_name'];
            $vcData['email'] = $data['email'];
            $vcData['phone_number'] = $data['phone_number'];
            $vcData['cell_number'] = $data['cell_number'];
            $vcData['emergency_contact'] = $data['emergency_contact'];
            try {

                $insertContact = $vcModel->updateVendorContact($vcData, $data['vcId']);
                $this->vm->success = "Alternate Vendor Contact Updated!";
                //$this->_redirect('/vendor/index/bid/'.$data['building'].'/vid/'.$data['vid']);
                $json_data['msg'] = $this->vm->success;
                $json_data['url'] = '/vendor/index/bid/' . $data['building'] . '/vid/' . $data['vid'];
                echo json_encode($json_data);
                exit;
            } catch (Exception $e) {
                $this->vm->error = "Error Occurred During Updation of Alternat Vendor Contact !";
            }
        }
        $vcId = $this->_getParam('vcId');
        if (!empty($vcId)) {
            $vcData = $vcModel->getVendorContact($vcId);
            if ($vcData) {
                $vendorData = $this->vendorModel->getVendor($vcData[0]['vid']);
                $this->view->vendorData = $vendorData;
            }
            $this->view->vcData = $vcData;
        }
        $this->view->vcId = $vcId;
    }

// close edit vendor contact

    public function removevendorAction() {
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $vid = $data['vid'];
            if ($vid != '') {
                try {
                    $vcModel = new Model_VendorContact();
                    $vcList = $vcModel->getContactByVid($vid);
                    if ($vcList) {
                        foreach ($vcList as $vc) {
                            $vcId = $vc['vcId'];
                            $vcdelete = $vcModel->deleteVendorContact($vcId);
                        }
                    }
                    $vdelete = $this->vendorModel->deleteVendor($vid);
                    echo 'true';
                    $this->vm->success = "Vendor Successfully Deleted!";
                } catch (Exception $e) {
                    echo 'error';
                }
            }
        }
        exit(0);
    }

    public function removevcontactAction() {
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $vcId = $data['vcId'];
            if ($vcId != '') {
                try {
                    $vcModel = new Model_VendorContact();
                    $res = $vcModel->deleteVendorContact($vcId);
                    $this->vm->success = "Alternate Vendor Contact Successfully Deleted!";
                    echo 'true';
                } catch (Exception $e) {
                    echo 'error';
                }
            }
        }
        exit(0);
    }

    public function loadvendormaterialAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getPost();
        if (isset($data['vid']) && $data['vid'] != '') {
            $vdata = $this->vendorModel->getVendor($data['vid']);
            $this->view->vdata = $vdata[0];
            $this->view->mid = $data['mid'];
        } else {
            echo 'Error in the vendor id.';
        }

        $this->view->roleId = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->vendor_location = $this->vendor_location;
    }

    public function vendortemplateAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $submit_flag = true;
            if ($data['company_name'] == '' || $data['first_name'] == '' || $data['services'] == '') {
                $submit_flag = false;
            }

            if ($submit_flag) {
                $vData = array();
                $vData['company_name'] = addslashes($data['company_name']);
                $vData['first_name'] = $data['first_name'];
                $vData['last_name'] = $data['last_name'];
                $vData['services'] = $data['services'];
                $vData['contact_type'] = $data['contact_type'];
                $vData['phone_number'] = $data['phone_number'];
                $vData['cell_number'] = $data['cell_number'];
                $vData['email'] = $data['email'];
                $vData['account_number'] = $data['account_number'];
                $vData['address1'] = $data['address1'];
                $vData['address2'] = $data['address2'];
                $vData['city'] = $data['city'];
                $accountmap = new Model_Account();
                $statename = $accountmap->getStatesByCode($data['state']);
                $vData['state_code'] = $data['state'];
                $vData['state'] = $statename[0]->state;
                $vData['postal_code'] = $data['postal_code'];
                $vData['emergency_contact'] = $data['emergency_contact'];
                $vData['buildingId'] = $data['buildingId'];
                $vData['global_template'] = $data['global_template'];
                try {
                    $insertVendor = $this->vendorModel->insertVendor($vData);
                    $this->vm->success = "Vendor Successfully Created!";
                    //$this->_redirect('/vendor/index/bid/'.$data['buildingId']);
                    $json_data['msg'] = $this->vm->success;
                    $json_data['url'] = '/vendor/index/bid/' . $data['buildingId'];
                    echo json_encode($json_data);
                    exit;
                } catch (Exception $e) {
                    echo $e->getMessage();
                    $this->vm->error = "Error Occurred During Creation of Vendor!";
                }
            } else {
                $build_ID = $data['buildingId'];
                $this->view->build_id = $build_ID;
                $this->vm->error = "Fill all the required field.";
            }
        } else {
            $redirectpage = $this->_getParam('redirect');
            if (isset($redirectpage)) {
                $this->view->redirectpage = $this->_getParam('redirect');
            }
            $build_ID = $this->_getParam('bid', '');
            $this->view->build_id = $build_ID;
            $this->view->cust_id = $this->cust_id;
        }
        $accountMapper = new Model_Account();
        $this->view->states = $accountMapper->getStates();
    }

    public function getvendorAction() {
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $vendorModel = new Model_Vendor();
            $vendorDetails = $vendorModel->getVendor($data['vid']);
            $json_data = $vendorDetails[0];
            $json_data['msg'] = 'success';
            echo json_encode($json_data);
            exit(0);
        } else {
            echo json_encode(array('msg' => 'error'));
        }
    }

    public function createvendortemplateAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $submit_flag = true;
            if ($data['company_name'] == '' || $data['first_name'] == '' || $data['services'] == '') {
                $submit_flag = false;
            }

            if ($submit_flag) {
                $vData = array();
                $vData['company_name'] = addslashes($data['company_name']);
                $vData['first_name'] = $data['first_name'];
                $vData['last_name'] = $data['last_name'];
                $vData['services'] = $data['services'];
                $vData['contact_type'] = $data['contact_type'];
                $vData['phone_number'] = $data['phone_number'];
                $vData['cell_number'] = $data['cell_number'];
                $vData['email'] = $data['email'];
                $vData['account_number'] = $data['account_number'];
                $vData['address1'] = $data['address1'];
                $vData['address2'] = $data['address2'];
                $vData['city'] = $data['city'];
                $accountmap = new Model_Account();
                $statename = $accountmap->getStatesByCode($data['state']);
                $vData['state_code'] = $data['state'];
                $vData['state'] = $statename[0]->state;
                $vData['postal_code'] = $data['postal_code'];
                $vData['emergency_contact'] = $data['emergency_contact'];
                $vData['buildingId'] = $data['buildingId'];
                if ($data['contact_type_text'] != '') {
                    $carray = array();
                    $carray['contact'] = addslashes($data['contact_type_text']);
                    $carray['building'] = $data['buildingId'];
                    try {
                        $contactModel = new Model_ContactType();
                        $existConatct = $contactModel->checkContactByName($data['contact_type_text'], $data['buildingId']);
                        if (!$existConatct) {
                            $insert_contact = $contactModel->insertContactType($carray);
                            $vData['contact_type'] = $insert_contact;
                        }
                    } catch (Exception $e) {
                        //echo $e->getMessage();
                        echo $e->getMessage();
                        $this->vm->error = "Error Occurred During Creation of Vendor!";
                    }
                }
                if ($data['service_type_text'] != '') {
                    $sarray = array();
                    $sarray['service'] = addslashes($data['service_type_text']);
                    $sarray['building'] = $data['buildingId'];
                    try {
                        $serviceModel = new Model_Services();
                        $existService = $serviceModel->checkServiceByName($data['service_type_text'], $data['buildingId']);
                        if (!$existService) {
                            $insert_service = $serviceModel->insertServices($sarray);
                            $vData['services'] = $insert_service;
                        }
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        $this->vm->error = "Error Occurred During Creation of Vendor!";
                    }
                }



                $vData['import_template'] = 1;
                try {
                    $insertVendor = $this->vendorModel->insertVendor($vData);
                    $this->vm->success = "Vendor Successfully Created!";
                    //$this->_redirect('/vendor/index/bid/'.$data['buildingId']);
                    $json_data['msg'] = $this->vm->success;
                    $json_data['url'] = '/vendor/index/bid/' . $data['buildingId'];
                    echo json_encode($json_data);
                    exit;
                } catch (Exception $e) {
                    echo $e->getMessage();
                    $this->vm->error = "Error Occurred During Creation of Vendor!";
                }
            } else {
                $build_ID = $data['buildingId'];
                $this->view->build_id = $build_ID;
                $this->vm->error = "Fill all the required field.";
            }
        } else {
            $redirectpage = $this->_getParam('redirect');
            if (isset($redirectpage)) {
                $this->view->redirectpage = $this->_getParam('redirect');
            }
            $build_ID = $this->_getParam('bid', '');
            $this->view->build_id = $build_ID;
        }
        $accountMapper = new Model_Account();
        $this->view->states = $accountMapper->getStates();
    }

    public function vendorrecoveryAction() {

        $order = $this->_getParam('order', 'company_name');
        $dir = $this->_getParam('dir', 'ASC');        
        $build_ID = $this->_getParam('bid');
        
        $msgId = $this->_getParam('msg', 0);
        $msg = '';
        if ($msgId == 1) {
            $msg = 'vendor user has been activated successfully.';
        }
        $vid = $this->_getParam('vid', '');
        if (!isset($this->vm->msg) && $msgId != 0) {
            $this->vm->msg = $msg;
            $tparam = ($vid != 0) ? '/vid/' . $vid : '';
            $this->_redirect('/vendor/vendorrecovery/bid/' . $build_ID . '' . $tparam);
        }

        $buildingMapper = new Model_Building();
        $search_array = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            if ($data['search_by'] == "services") {
                $search_array['search_by'] = $data['search_by'];
                $search_array['search_value'] = $data['service_value'];
            } else {
                $search_array['search_by'] = $data['search_by'];
                $search_array['search_value'] = $data['search_value'];
            }
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

        $build_ID = $this->_getParam('bid', '');
        foreach ($companyListing as $cl) {
            $buildIds[] = $cl['build_id'];
        }
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");


        $vendorList = [];
        $select_bid = '';
        if ($build_ID != '') {
            $select_bid = $build_ID;
        } else {
            if ($companyListing != '') {
                $select_bid = $companyListing[0]['build_id'];
            }
        }
        
        if (!empty($select_bid)) {
            $search_array = array_map("addslashes", $search_array);
            $search_array = array_map("addslashes", $search_array);
            $search_array = array_map("addslashes", $search_array);
            $vendorList = $this->vendorModel->getVendorByBidRecovery($select_bid, $order, $dir, $search_array);
        } 
        
        $page = $this->_getParam('page', 1);
        $pageObj = new Ve_Paginator();
        $show = $this->_getParam('show', '');
        if($show==""){
           $show=4; 
        }
        if($show=='all'){
            $vendorList_paginator = $pageObj->fetchPageDataResult($vendorList, $page, $show);        
        }else{
            $vendorList_paginator = $pageObj->fetchPageDataResult($vendorList, $page, $show);      
        }
        // print_r($vendorList_paginator);
         //echo "asdf";  die();
        
        $this->view->show=$show;
        $this->view->vendorList = $vendorList_paginator;
        $this->view->select_build_id = $select_bid;
        $this->view->companyListing = $companyListing;
        $this->view->custID = $this->cust_id;
        $this->view->roleId = $this->roleId;
        $this->view->vm = $this->vm;
        $this->view->vId = $vid;
        $this->view->search = $search_array;
        $this->view->order = $order;
        $this->view->dir = $dir;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->vendor_location = $this->vendor_location;
        $this->view->userId = $this->userId;    
        $this->view->vrecovery_location = $this->vrecovery_location;    
    }
    
    public function loadvendorinactiveuserAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getPost();
        $vId = $data['vId']; //50;
        $buildId = $data['bId'];
        $vendorMapper = new Model_Vendor();
        $vendorData = $vendorMapper->getVendorListByBid($buildId, true);

        $modelMapper = new Model_Module();
        $moduleList = $modelMapper->getModule();


        $this->view->roleId = $this->roleId;
        $this->view->userId = $this->userId;
        $this->view->cust_id = $this->cust_id;
        $this->view->vendorId = $vId;
        $this->view->vendorData = $vendorData;
        $this->view->moduleList = $moduleList;
        $this->view->buildId = $buildId;
        $this->view->acesshelper = $this->accessHelper;
        $this->view->vrecovery_location = $this->vrecovery_location;
    }
    
    public function recoveruserAction() {
        $id = $this->_getParam('id');
        $data['remove_status'] = '0';
        $res = $this->vendorModel->updateVendor($data, $id);
        echo true;
        exit(0);
    }
}

// close class
