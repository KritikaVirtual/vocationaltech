<?php

class ReportController extends Ve_Controller_Base {

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
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
        $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");

        //$_COOKIE['build_cookie'];

        $woModel = new Model_WorkOrder();
        $search_array = array();
        if (isset($_REQUEST['search_status']) && $_REQUEST['search_status'] != '')
            $search_array['search_status'] = $_REQUEST['search_status'];

        if (isset($_REQUEST['category_name']) && $_REQUEST['category_name'] != '')
            $search_array['category_name'] = $_REQUEST['category_name'];

        if (isset($_REQUEST['tenant_name']) && $_REQUEST['tenant_name'] != '')
            $search_array['tenant_name'] = $_REQUEST['tenant_name'];

        if (isset($_REQUEST['search_wo']) && $_REQUEST['search_wo'] != '')
            $search_array['search_wo'] = $_REQUEST['search_wo'];

        if (isset($_REQUEST['from_date']) && $_REQUEST['from_date'] != '')
            $search_array['from_date'] = date("Y-m-d", strtotime($_REQUEST['from_date']));

        if (isset($_REQUEST['to_date']) && $_REQUEST['to_date'] != '')
            $search_array['to_date'] = date("Y-m-d", strtotime($_REQUEST['to_date']));

        if ($companyListing != '') {
            if ($build_ID == '') {
                $buildIds = array();
                foreach ($companyListing as $cl) {
                    $buildIds[] = $cl['build_id'];
                }

                $wolist = $woModel->getWorkOrderByBuilIds($buildIds, $order, $dir, $search_array, '', '');
            } else {
                $wolist = $woModel->getBuildingWorkOrder($build_ID, $order, $dir, $search_array, '', '');
            }
        }

        $pageObj = new Ve_Paginator();
        $paginator = $pageObj->fetchPageDataResult($wolist, $page, 10);
        $this->view->page = $page;
        $view_type = $this->_getParam('view_type', 'line');
        $this->view->custID = $this->cust_id;
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        $this->view->wolist = $paginator;
        $this->view->order = $order;
        $this->view->dir = $dir;
        $this->view->view_type = $view_type;
        $this->view->userId = $this->userId;
    }

    public function dlworkreportAction() {
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

        $type = $this->_getParam('type', 'csv');
        $order = $this->_getParam('order', 'woId');
        $dir = $this->_getParam('dir', 'DESC');
        $build_ID = $this->_getParam('bid', '');
        $search_array = array();
        $woModel = new Model_WorkOrder();
        if ($companyListing != '') {

            $buildIds = array();
            foreach ($companyListing as $cl) {
                $buildIds[] = $cl['build_id'];
            }

            $wolist = $woModel->getWorkOrderByBuilIds($buildIds, $order, $dir, $search_array);
        }
        $ssModel = new Model_ScheduleStatus();
        $status_list = $ssModel->getScheduleStatus();
        $status_array = array();
        foreach ($status_list as $sl) {
            if ($sl['ssID'] != 8)
                $status_array[$sl['ssID']] = $sl['title'];
        }
        $report_template = new Zend_View();
        $report_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/report/');
        $report_template->assign('wolist', $wolist);
        if ($type == 'excel') {
            require_once (APPLICATION_PATH . '/../library/Ve/PHPExcel/Classes/PHPExcel.php');
            $bodyText = $report_template->render('dlworkorder.phtml');
        }
        if ($type == 'pdf') {
            $bodyText = $report_template->render('dlworkreport.phtml');
            $file_name = 'WOReport-' . date("Y-m-d_H:i:s") . '.pdf';
            require_once(APPLICATION_PATH . '/../library/Ve/tcpdf/tcpdf.php');
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Vocational');
            $pdf->SetTitle('Work Order Report');
            $pdf->SetSubject('PDF Report');
            $logo_image = BASEURL . 'public/images/logo.png';

            // set default header data
            $pdf->SetHeaderData('logo.png', "40", 'Work Order Report', 'PDF Report');

            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            //$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetFont('helvetica', '', 8, '', 'false');
            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->AddPage();
            $pdf->writeHTML($bodyText, true, 0, true, 0);
            $pdf->lastPage();
            $pdf->Output($file_name, 'D');
        }
        exit(0);
    }

// end of download work report

    /**
     *
     * Created By- Gurubaksh Singh

     * @return void
     */
    function listAction() {
        $parent_id = $this->_getParam('parent_id', 1);;
        $page = $this->_getParam('page', 1);
        $pageObj = new Ve_Paginator();
        $rModel = new Model_Report();
        $reportDetails = $rModel->getReport($accounts,$dashboard_menu,$parent_id);
        $reportTabs = $rModel->getReportTabs();
        $this->view->reportDetails = $reportDetails;

        $paginator = $pageObj->fetchPageDataResult($reportDetails, $page, 10);
        $this->view->reportDetails = $paginator;
        $this->view->page = $page;
        $this->view->reportTabs = $reportTabs;
        $this->view->parent_id = $parent_id;
    }

    /**
     *
     * Created By- Gurubaksh Singh
     *
     * This function open pop of create new report page.

     * @return void
     */
    function shownewreportAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        try {
            $userMod = new Model_User();
            $companyUser = $userMod->getCompanyAdminUser();
            $this->view->companyUser = $companyUser;
            $rModel = new Model_Report();
            //$dashboardmenu = $rModel->getDashboardMenu();
            $dashboardmenu = $rModel->getReportTabs();
            $childTabmenu = $rModel->getchildTab();	
            $this->view->reportMrt = glob(REPORTMRT . "*.{mrt}", GLOB_BRACE);
            $this->view->dashboardmenu = $dashboardmenu;
            $this->view->childTabmenu = $childTabmenu;
            
        } catch (Exception $e) {
            $message['status'] = 'error';
            $message['msg'] = 'Error occurred during form submit.';
        }
    }

    /**
     *
     * Created By- Gurubaksh Singh
     *
     * This function insert the new report.
     *
     * @func insertReport insert reports details in database.

     * @return success or failure msg
     */
    function createnewreportAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            $data['report_option'] = implode(",", $data['report_option']);
            if ($data['report_name'] == '' && $data['dashboard_menu'] == '' && $data['select_report'] == '' && $data['report_option'] == '' && $data['report_target'] == '') {
                $message['status'] = 'error';
                $message['msg'] = 'Fill the Form Properly';
            } else {
                try {
                    $rModel = new Model_Report();
                    if (isset($data['rid']) && $data['rid'] != '') {
                        $insertid = $rModel->updateReport($data, $data['rid']);
                        $message['status'] = 'success';
                        $message['msg'] = 'New report has been updated successfully.';
                    } else {
                        $insertid = $rModel->insertReport($data);
                        $message['status'] = 'success';
                        $message['msg'] = 'New report has been saved successfully.';
                    }
                } catch (Exception $e) {
                    $message['status'] = 'error';
                    $message['msg'] = 'Error occurred during form submit.';
                }
            }
        }
        echo json_encode($message);
        exit(0);
    }

    function deletereportAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            if ($data['rid'] == '') {
                $message['status'] = 'error';
            } else {
                try {
                    $rModel = new Model_Report();
                    $deleteId = $rModel->deleteReport($data['rid']);
                    $message['status'] = 'success';
                } catch (Exception $e) {
                    $message['status'] = 'error';
                }
            }
        }
        echo json_encode($message);
        exit(0);
    }

    function editreportAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            if ($data['rid'] == '') {
                $message['status'] = 'error';
            } else {
                try {
                    $rModel = new Model_Report();
                    $reportDetails = $rModel->editReport($data['rid']);
                    $this->view->reportDetails = $reportDetails[0];
                    $userMod = new Model_User();
                    $companyUser = $userMod->getCompanyAdminUser();
                    $this->view->companyUser = $companyUser;
                    $rModel = new Model_Report();
                    //$dashboardmenu = $rModel->getDashboardMenu();
                    $dashboardmenu = $rModel->getReportTabs();
                    $childTabmenu = $rModel->getchildTab();	
                    $this->view->reportMrt = glob(REPORTMRT . "*.{mrt}", GLOB_BRACE);
                    $this->view->dashboardmenu = $dashboardmenu;
                    $this->view->childTabmenu = $childTabmenu;
                    $message['status'] = 'success';
                } catch (Exception $e) {
                    $message['status'] = 'error';
                }
            }
        }
    }

    function showtreportlinkAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {

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
            $this->view->companyListing = $companyListing;

            $data = $this->getRequest()->getPost();
            if ($data['cust_id'] != '' && $data['dash_menu'] != '') {
                $data = $this->getRequest()->getPost();
                $reportModel = new Model_Report();
                $reportDetailLinks = $reportModel->getReport($data['cust_id'], $data['dash_menu']);
                $this->view->reportDetailLinks = $reportDetailLinks;
                $this->view->cust_id = $data['cust_id'];
                $this->view->dash_menu = $data['dash_menu'];
                $this->view->select_build_id = $data['build_id'];
                $this->view->userId = $this->userId;
            } else {
                exit(0);
            }
        } else {
            exit(0);
        }
    }

}
