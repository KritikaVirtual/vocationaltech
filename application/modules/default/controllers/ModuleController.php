<?php

/**
 * All Module
 * 
 * 
 * @package    Zend
 * @subpackage Controller
 * @author     Gurubaksh Singh
 */
class ModuleController extends Ve_Controller_Base {

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

    /**
     * @author     Gurubaksh Singh
     *
     * This function shows the listing of module existing in system.
     *
     * @search_array Array is variable for providing search result.
     * @moduleList keeps the listing of all modules.
     *
     * @return void
     */
    public function indexAction() {
        $page = $this->_getParam('page', 1);
        $pageObj = new Ve_Paginator();
        $search_array = array();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            if ($data['search_by'] == 'module_name') {
                $search_array['search_by'] = $data['search_by'];
                $search_array['search_value'] = $data['search_value'];
            }
        }
        $moduleMapper = new Model_Module();
        $moduleList = $moduleMapper->getModuleListing(array(), $search_array);
        $paginator = $pageObj->fetchPageDataResult($moduleList, $page, 15);
        $this->view->moduleList = $paginator;
        $this->view->search = $search_array;
        $this->view->roleId = $this->roleId;
    }

    /**
     * @author     Gurubaksh Singh
     *
     * This function open Pop Up for edit module.
     *
     * @mid number is variable as primary key of module table.
     *
     * @return void
     */
    public function editmoduleAction() {
        $this->_helper->getHelper('layout')->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            if ($data['mid'] != '') {
                try {
                    $moduleMapper = new Model_Module();
                    $moduleDetails = $moduleMapper->getModuleListing(array($data['mid']));
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                $this->view->moduleDetails = $moduleDetails[0];
            }
        }
        $this->view->roleId = $this->roleId;
    }

    /**
     * @author     Gurubaksh Singh
     *
     * This function update module by ajax.
     *
     * @module_id number is variable as primary key of module table.
     *
     * @return void
     */
    public function updatemoduleAction() {
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            if ($data['module_id'] != '') {
                $moduleMapper = new Model_Module();
                try {
                    $moduleMapper->updateModule($data, $data['module_id']);
                    $message['status'] = 'success';
                    $message['msg'] = 'Module updated successfully.';
                } catch (Exception $e) {
                    $message['status'] = 'error';
                    $message['msg'] = 'Error Occurred during the update module';
                }
            }
            echo json_encode($message);
        }

        exit(0);
    }

    /**
     * @author     Gurubaksh Singh
     *
     * This function delete module by ajax.
     *
     * @module_id number is variable as primary key of module table.
     *
     * @return void
     */
    public function deletemoduleAction() {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $data = $this->_request->getPost();
            if ($data['module_id'] != '') {
                try {
                    $moduleMapper = new Model_Module();
                    $deleteRate = $moduleMapper->deletemodule($data['module_id']);
                    echo 'true';
                } catch (Exception $e) {
                    echo 'false';
                }
            }
        }
        exit(0);
    }

    public function loadmodulecompAction() {
        $this->_helper->getHelper('layout')->disableLayout();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $data = $this->_request->getPost();
            try {
                $Model_User_Building_Module = new Model_UserBuildingModule();
                $companyData = $Model_User_Building_Module->getCompanyOfModuleByBuilding($data['module_id']);
            } catch (Exception $e) {
                
            }
        }
        $this->view->companyData = $companyData;
        $this->view->roleId = $this->roleId;
    }

    public function loadmodulebuildingsAction() {
        $this->_helper->getHelper('layout')->disableLayout();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $data = $this->_request->getPost();
            try {
                $Model_User_Building_Module = new Model_UserBuildingModule();
                $buildingData = $Model_User_Building_Module->assignedBuildingToModule($data['module_id'], $data['cust_id']);
            } catch (Exception $e) {
                
            }
        }
        $this->view->buildingData = $buildingData;
        $this->view->roleId = $this->roleId;
    }

    public function deletemodulebuildingAction() {
        $this->_helper->getHelper('layout')->disableLayout();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $data = $this->_request->getPost();
            try {
                $Model_User_Building_Module = new Model_UserBuildingModule();
                echo $buildingData = $Model_User_Building_Module->deleteModuleBuilding($data['module_id'], $data['building_id']);
            } catch (Exception $ex) {
                echo 'false';
            }
        }
        exit(0);
    }

}

?>
