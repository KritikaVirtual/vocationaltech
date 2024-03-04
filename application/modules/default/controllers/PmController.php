<?php

ob_start();
 
class PmController extends Ve_Controller_Base {

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
        $this->accessHelper = $this->_helper->access;
    }

    public function indexAction() {
       
        //$this->_redirect('/pm/matrix');
        $page = $this->_getParam('page', 1);
        $pageObj = new Ve_Paginator();
        $templateName = "";
        $designationName = "";
        $data = $this->_request->getPost();
        $template = new Model_PmTemplate();
        $templatedata = array();
        if ($data['search'] == 'Search') {
            $templateName = $data['templatename'];
            $designationName = $data['designationname'];
            $tempdata = $template->GetAllTemplateName($templateName);
            foreach ($tempdata as $temp) {
                $find = $template->GetTemplateDetails($temp->VT_Template_Name_ID, $designationName);
                //echo $designationName;
                //print_r($find);
                //die;
                if (!empty($find) && $designationName != "") {
                    $templatedata[$temp->VT_Template_Name_ID][VT_Template_Name] = $temp->VT_Template_Name;
                    $templatedata[$temp->VT_Template_Name_ID][VT_Template_Name_ID] = $temp->VT_Template_Name_ID;
                    $templatedata[$temp->VT_Template_Name_ID][VT_TypeDesignation] = $template->GetTemplateDetails($temp->VT_Template_Name_ID, $designationName);
                } else if ($designationName == "") {
                    $templatedata[$temp->VT_Template_Name_ID][VT_Template_Name] = $temp->VT_Template_Name;
                    $templatedata[$temp->VT_Template_Name_ID][VT_Template_Name_ID] = $temp->VT_Template_Name_ID;
                    $templatedata[$temp->VT_Template_Name_ID][VT_TypeDesignation] = $template->GetTemplateDetails($temp->VT_Template_Name_ID, $designationName);
                }
            }
        } else {
            $tempdata = $template->GetAllTemplateName();
            foreach ($tempdata as $temp) {
                $templatedata[$temp->VT_Template_Name_ID][VT_Template_Name] = $temp->VT_Template_Name;
                $templatedata[$temp->VT_Template_Name_ID][VT_Template_Name_ID] = $temp->VT_Template_Name_ID;
                $templatedata[$temp->VT_Template_Name_ID][VT_TypeDesignation] = $template->GetTemplateDetails($temp->VT_Template_Name_ID, $designationName);
            }
        }
        $show = $this->_getParam('show', '');
        if($show != ""){
            setcookie('show_limit', $show, 2147483647, '/');
        }else{
           $show =  $_COOKIE['show_limit'];
        }
        if($show==""){
            $show = 5;
        }
        $this->view->templatedetails = $templatedata;
        
        $paginator = $pageObj->fetchPageDataResult($templatedata, $page, $show);
        $this->view->templatedetails = $paginator;
        $this->view->page = $page;
        $this->view->show = $show;
        $this->view->templateName = $templateName;
        $this->view->designationName = $designationName;
    }

    public function createdesignationAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $template = new Model_PmTemplate();
        $tempdata = $template->GetAllTemplateName();
        // send data to view pages
        $this->view->templats = $tempdata;
    }

    public function createtemplateAction() {
        //$this->_helper->layout()->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');
    }

    // validate template before creation
    public function validatetemplateAction() {
        //$param = $this->getRequest()->getParams();
        $param = $this->_request->getPost();
        $template_Name = $param['TemplateName'];
        $template_id = $param['Template_id'];
        $template = new Model_PmTemplate();
        $result = $template->GetTemplateByName($template_Name, $template_id);
        if (empty($result)) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit();
    }

    // Save template Name
    public function savetemplateAction() {
        $msg = array();
        $templatedata = array();
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();
        //$param = $this->getRequest()->getParams();
        $templatedata['VT_Template_Name'] = $param['TemplateName'];
        $result = $template->InsertTemplateName($templatedata);
        //print_r($result);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Temaplate save sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    //  Validate Type designation  //
    public function validatetypedesignationAction() {
        $data = $this->_request->getPost();
        //$param = $this->getRequest()->getParams();
        $typedesignation = $data['typedesignation'];
        $typedesination_id = $data['typedesination_id'];
        $template = new Model_PmTemplate();
        $result = $template->GetTemplateIdByTypeDesignation($typedesignation, $typedesination_id);
        if (empty($result)) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit();
    }

    /* save Type designation */

    public function savetypedesignationAction() {
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();
        $typedata['VT_Template_Name_ID'] = $param['VT_Template_Name_ID'];
        $typedata['VT_TypeDesignation'] = $param['VT_TypeDesignation'];
        $typedata['VT_TypeDescritpion'] = $param['VT_TypeDescritpion'];
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $typedata['user_id'] = $user_id;
        $result = $template->InsertTypeDesignation($typedata);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Type Designation save sucessfully';
            $msg['id'] = $result;
        }
        echo json_encode($msg);
        exit();
    }

    // edit template name

    public function edittemplateAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $template_id = $param['template_id'];
        $template = new Model_PmTemplate();
        $templatedata = $template->GetTemplateNameById($template_id);
        $templatedata = $templatedata[0];
        $this->view->template = $templatedata;
    }

    // update template Name 
    public function updatetemplateAction() {
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();
        $typedata['VT_Template_Name'] = $param['TemplateName'];
        $result = $template->updatetemplate($typedata, $param['Template_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Template Update sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    // Delete Template 
    public function deletetemplateAction() {
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();
        $result = $template->deleteTemplate($param['Template_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $template->deleteTypeDesignationByTemplateId($param['Template_id']);
            $msg['status'] = 'success';
            $msg['msg'] = 'Template Deleted sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Edit  designation */

    public function editdesignationAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $typedesignation_id = $param['desig_id'];
        $template = new Model_PmTemplate();
        $typedata = $template->GettypedesignationById($typedesignation_id);
        $typedata = $typedata[0];
        $tempdata = $template->GetAllTemplateName();
        // send data on view pages
        $this->view->VT_TypeDesignation = $typedata;
        $this->view->templats = $tempdata;
    }

    /* Update type designation */

    public function updatetypedesignationAction() {
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();
        $typedata['VT_Template_Name_ID'] = $param['template_id'];
        $typedata['VT_TypeDesignation'] = $param['typedesignation'];
        $typedata['VT_TypeDescritpion'] = $param['typedescription'];
        $result = $template->updatetypedesignation($typedata, $param['typedesination_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Type Designation Update sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* delete type designation */

    public function deletetypedescriptionAction() {
        $msg = array();
        $template = new Model_PmTemplate();
        $data = $this->_request->getPost();
        $result = $template->deleteTypeDesignation($data['type_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Template Deleted sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    // Task Section start

    /* Create a task */
    public function createtaskAction() {
        $param = $this->getRequest()->getParams();
        $desig_id = $param['desig_id'];
        $subset = new Model_PmTemplate();
        if (!empty($desig_id)) {
            $allsubset = $subset->GetAllSubset($desig_id);
            $msg = array();
            $alltask = array();
            $getalltask = $subset->GetAlltaskparent($desig_id);
            foreach ($getalltask as $sub) {
                $subtask = $subset->GetTaskBysubsetId($sub->VT_Template_Task_ID);
                if (!empty($subtask)) {
                    $alltask[$sub->VT_Template_Task_ID]['name'] = $sub->Task_Instruction;
                    $alltask[$sub->VT_Template_Task_ID]['id'] = $sub->VT_Template_Task_ID;
                    $alltask[$sub->VT_Template_Task_ID]['task'] = $subtask;
                } else {
                    $alltask[][] = $sub;
                }
            }
            $list = $subset->get_view_table('task');
            $listview = explode(',', $list[0]->display_table_view);
            $freq = array();
            $CustFreq = array();
            $frequency = $subset->Getallfrequency();
            foreach ($frequency as $val) {
                if ($val->column == 1)
                    $freq[$val->AU_Frequency_ID] = $val->Name;
                if ($val->column == 2)
                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
            }
            $startdate_ad = array();
            $startdateadjustment = $subset->Getallstartdateadjustment();
            foreach ($startdateadjustment as $val) {
                $startdate_ad[$val->AU_sda_ID] = $val->Name;
            }

            $alljobtime = array();
            $jobtime = $subset->Getalljobtime();
            foreach ($jobtime as $val) {
                $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
            }

            // get template data 
            $template_data = $subset->GettemplateByTypeDesignationID($desig_id);
            $template_data = $template_data[0];


            // send data in view pages
            $this->view->desig_id = $desig_id;
            $this->view->alltask = $alltask;
            $this->view->subset = $allsubset;
            $this->view->listview = $listview;
            $this->view->frequency = $freq;
            $this->view->CustmeFreq = $Intreval;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;
            $this->view->templateData = $template_data;
        } else {
            exit();
        }
    }

    // edit task section 
    public function viewtaskAction() {
        $this->_helper->getHelper('layout')->disableLayout();
        //$param = $this->getRequest()->getParams();
        $param = $this->_request->getPost();
        $desig_id = $param['desig_id'];

        $subset = new Model_PmTemplate();
        if (!empty($desig_id)) {
            $allsubset = $subset->GetAllSubset($desig_id);
            //die;
            //$data = $this->_request->getPost();
            $msg = array();
            $alltask = array();
            $view_empty_subset = array();
            $getalltask = $subset->GetAlltaskparent($desig_id);
            //print_r($getalltask);
            foreach ($getalltask as $sub) {
                $subtask = $subset->GetTaskBysubsetId($sub->VT_Template_Task_ID);
                if (!empty($subtask)) {
                    $alltask[$sub->VT_Template_Task_ID]['name'] = $sub->Task_Instruction;
                    $alltask[$sub->VT_Template_Task_ID]['id'] = $sub->VT_Template_Task_ID;
                    $alltask[$sub->VT_Template_Task_ID]['task'] = $subtask;
                } else if (empty($sub->AU_Frequency_ID)) {
                    $view_empty_subset[$sub->VT_Template_Task_ID]['name'] = $sub->Task_Instruction;
                    $view_empty_subset[$sub->VT_Template_Task_ID]['id'] = $sub->VT_Template_Task_ID;
                    $view_empty_subset[$sub->VT_Template_Task_ID]['task'] = "";
                } else {
                    $alltask[][] = $sub;
                }
            }
            $alltask = array_merge($alltask, $view_empty_subset);
            $listview = explode(',', $param['viewlist']);
            $freq = array();
            $CustFreq = array();
            $frequency = $subset->Getallfrequency();
            foreach ($frequency as $val) {
                if ($val->column == 1)
                    $freq[$val->AU_Frequency_ID] = $val->Name;
                if ($val->column == 2)
                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
            }

            $startdate_ad = array();
            $startdateadjustment = $subset->Getallstartdateadjustment();
            foreach ($startdateadjustment as $val) {
                $startdate_ad[$val->AU_sda_ID] = $val->Name;
            }

            $alljobtime = array();
            $jobtime = $subset->Getalljobtime();
            foreach ($jobtime as $val) {
                $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
            }

            // send data in view pages
            $this->view->desig_id = $desig_id;
            $this->view->alltask = $alltask;
            $this->view->subset = $allsubset;
            $this->view->listview = $listview;
            $this->view->frequency = $freq;
            $this->view->CustmeFreq = $Intreval;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;
        } else {
            exit();
            //return false;
        }
    }

    // view add new task 
    public function addtaskAction() {
        //$this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');
        $subset = new Model_PmTemplate();
        $data = $this->_request->getParams();
        //echo $data['desig_id'];
        $desig_id = $data['desig_id'];
        $allsubset = $subset->GetAllSubset($desig_id);
        //print_r($allsubset);
        // die;
        $freq = array();
        $frequency = $subset->Getallfrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }
        $startdatead = array();
        $startdateadjustment = $subset->Getallstartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdatead[$val->AU_sda_ID] = $val->Name;
        }

        $alljobtime = array();
        $jobtime = $subset->Getalljobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
        }

        /* Reading section */
        $ReadingSubset = $subset->GetAllSubset_reading($desig_id);

        $this->view->frequency = $freq;
        $this->view->Interval = $Intreval;
        $this->view->startdateadjustment = $startdatead;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
        $this->view->ReadingSubset = $ReadingSubset;
    }

    // view add new task 
    public function addreadingAction() {
        //$this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');
        $subset = new Model_PmTemplate();
        $data = $this->_request->getParams();
        //echo $data['desig_id'];
        $desig_id = $data['desig_id'];
        $allsubset = $subset->GetAllSubset_reading($desig_id);
        //print_r($allsubset);
        // die;
        $freq = array();
        $frequency = $subset->Getallfrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }
        $startdatead = array();
        $startdateadjustment = $subset->Getallstartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdatead[$val->AU_sda_ID] = $val->Name;
        }

        $alljobtime = array();
        $jobtime = $subset->Getalljobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
        }

        $allunitofmeasure = array();
        $unitofmeasure = $subset->Getallunitofmeasure();
        foreach ($unitofmeasure as $val) {
            $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
        }
        $this->view->frequency = $freq;
        $this->view->Interval = $Intreval;
        $this->view->startdateadjustment = $startdatead;
        $this->view->unitofmeasure = $allunitofmeasure;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
    }

    /* Add a new subset */

    public function createsubsetAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_request->getPost();
        $desig_id = $param['desig_id'];

        // send data in view pages
        $this->view->desig_id = $desig_id;
    }

    /* Update View Task */

    public function updateviewtaskAction() {
        $param = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $update = array();
        $update['display_table_view'] = $param['viewlist'];
        //$update['pm_type'] = $param['type'];        
        $result = $task->Updateviewlist($update, $param['type']);
        if (!empty($result)) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit();
    }

    /* validate subset */

    public function validatesubsetAction() {
        $data = $this->_request->getPost();
        $subsetname = $data['subsetname'];
        $subsetname_id = $data['subsetname_id'];
        $desig_id = $data['desig_id'];
        $subset = new Model_PmTemplate();
        $result = $subset->GetSubseteByName($subsetname, $subsetname_id, $desig_id);
        if (empty($result)) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit();
    }

    /* Save a new subset */

    public function savesubsetAction() {
        $msg = array();
        $subsetdata = array();
        $subset = new Model_PmTemplate();
        $data = $this->_request->getPost();
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $data['user_id'] = $user_id;
        $result = $subset->insertsubset($data);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Subset save sucessfully';
            $msg['InsertId'] = $result;
        }
        echo json_encode($msg);
        exit();
    }

    /* Edit subset */

    public function editsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset_id = $param['subset_id'];
        $desig_id = $param['desig_id'];
        $subset = new Model_PmTemplate();
        $subdata = $subset->GettaskDataById($subset_id);
        $subdata = $subdata[0];

        // Send data to view pages
        $this->view->subsetdata = $subdata;
        $this->view->desig_id = $desig_id;
    }

    /* update subset data */

    public function updatesubsetAction() {
        $data = $this->_request->getPost();
        if (!empty($data)) {
            $task = new Model_PmTemplate();
            $task_id = $data['subsetname_id'];
            unset($data['subsetname_id']);
            $result = $task->Updatetask($data, $task_id);
            if (empty($result)) {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            } else {
                $msg['status'] = 'success';
                $msg['msg'] = 'Update subset save sucessfully';
            }
            echo json_encode($msg);
            exit();
        }
    }

    public function customefrequencyAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $freq = $param['frequency'];
        $GetData = "";
        $task = new Model_PmTemplate();
        if ($freq != "") {
            $GetData = $task->get_FrequencydataByID($freq);
            $GetData = $GetData[0];
        }
        $this->view->GetData = $GetData;
    }

    public function customefreqAction() {
        $msg = array();
        $data = $this->_request->getPost();
        $_SESSION['custome_freq'] = $data;
        $msg['status'] = 'success';
        $msg['msg'] = 'Frequency save sucessfully';
        echo json_encode($msg);
        exit();
    }

    public function savetaskAction() {
        $data = $this->_request->getPost();
        if (!empty($data)) {
            $task = new Model_PmTemplate();
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['user_id'] = $user_id;
            // get maximum view oder number paramiter table name
            $view_order = $task->Get_MaxViewOrder("pm_vt_template_task");
            $view_order = $view_order[0]->View_order;
            $data['View_order'] = $view_order + 1;
            $result = $task->Inserttask($data);
            if (empty($result)) {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            } else {
                $msg['status'] = 'success';
                $msg['msg'] = 'Task save sucessfully';
            }
            echo json_encode($msg);
            exit();
        }
        //die;
    }

    // update task data
    public function updatetaskAction() {
        $data = $this->_request->getPost();
        if (!empty($data)) {
            $task = new Model_PmTemplate();
            $task_id = $data['task_id'];
            unset($data['task_id']);
            $result = $task->Updatetask($data, $task_id);
            if (empty($result)) {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            } else {
                $msg['status'] = 'success';
                $msg['msg'] = 'Update Task save sucessfully';
            }
            echo json_encode($msg);
            exit();
        }
    }

    public function updatetaskorderAction() {

        $data = $this->_request->getPost();
        $task = json_decode($data[task]);
        $taskmodule = new Model_PmTemplate();
        $return = $this->validationdragandrop($task);
        $order = 1;
        if ($return == 1) {
            foreach ($task as $val) {
                //print_r($val);
                if (!empty($val->children) && $val->idSubset) {
                    $getdata = array("view_order" => $order);
                    $taskmodule->Updateodrder($getdata, $val->idSubset);
                    $order++;
                    foreach ($val->children as $data) {

                        if (!empty($data->idRoot)) {
                            //echo "idroot children";
                            $getdata = array("view_order" => $order, "Parent_ID" => $val->idSubset);
                            $taskmodule->Updateodrder($getdata, $data->idRoot);
                        }
                        if (!empty($data->id)) {
                            //echo "idroot children";
                            $getdata = array("view_order" => $order, "Parent_ID" => $val->idSubset);
                            $taskmodule->Updateodrder($getdata, $data->id);
                        }

                        $order++;
                    }
                } else {

                    if (!empty($val->idRoot)) {
                        //echo "root";
                        $getdata = array("view_order" => $order, "Parent_ID" => 0);
                        $taskmodule->Updateodrder($getdata, $val->idRoot);
                    }

                    if (!empty($val->id)) {
                        //echo "id";
                        $getdata = array("view_order" => $order, "Parent_ID" => 0);
                        $taskmodule->Updateodrder($getdata, $val->id);
                    }
                    $order++;
                }
            }
        }
        if ($return == 0) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error : This move not posible please try other';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Order save sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    // edit task section 
    public function edittaskAction() {
        //$this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');

        $subset = new Model_PmTemplate();
        $data = $this->_request->getParams();
        $task_id = $data['task_id'];
        $desig_id = $data['desig_id'];

        $TaskData = $subset->GettaskDataById($task_id);
        $TaskData = $TaskData[0];
        $allsubset = $subset->GetAllSubset($desig_id);
        $freq = array();
        $Intreval = array();
        $frequency = $subset->Getallfrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }

        $startdate_ad = array();
        $startdateadjustment = $subset->Getallstartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdate_ad[$val->AU_sda_ID] = $val->Name;
        }

        $alljobtime = array();
        $jobtime = $subset->Getalljobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
        }

        $this->view->frequency = $freq;
        $this->view->Interval = $Intreval;
        $this->view->startdateadjustment = $startdate_ad;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
        $this->view->taskdata = $TaskData;
    }

    /// Delete tasking 
    public function deletetaskAction() {
        $msg = array();
        $typedata = array();
        $task = new Model_PmTemplate();
        $param = $this->_request->getPost();
        $parent_id = $task->getallparent($param['Task_id']);
        $result = $task->deleteTask($param['Task_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $update = array("Parent_ID" => "");
            $result = $task->UpdateTaskByparent($update, $param['Task_id']);
            //$result = $task->deleteTaskByParentId($param['Task_id']); 
            $msg['status'] = 'success';
            $msg['msg'] = 'Template Deleted sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Reading Section start */

    public function createreadingAction() {
        $param = $this->getRequest()->getParams();
        $desig_id = $param['desig_id'];
        if (!empty($desig_id)) {
            $subset = new Model_PmTemplate();
            $allsubset = $subset->GetAllSubset_reading($desig_id);
            //die(12);
            $data = $this->_request->getPost();
            $msg = array();
            $list = $subset->get_view_table('Reading');
            $listview = explode(',', $list[0]->display_table_view);
            $allreading = array();
            $getallreading = $subset->GetAllReadingParent($desig_id);
            foreach ($getallreading as $sub) {
                $subreading = $subset->GetReadingBysubsetId($sub->VT_Template_Reading_ID);
                if (!empty($subreading)) {
                    $allreading[$sub->VT_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                    $allreading[$sub->VT_Template_Reading_ID]['id'] = $sub->VT_Template_Reading_ID;
                    $allreading[$sub->VT_Template_Reading_ID]['task'] = $subreading;
                } else {
                    $allreading[][] = $sub;
                }
            }

            $freq = array();
            $frequency = $subset->Getallfrequency();
            foreach ($frequency as $val) {
                if ($val->column == 1)
                    $freq[$val->AU_Frequency_ID] = $val->Name;
                if ($val->column == 2)
                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
            }

            $startdate_ad = array();
            $startdateadjustment = $subset->Getallstartdateadjustment();
            foreach ($startdateadjustment as $val) {
                $startdate_ad[$val->AU_sda_ID] = $val->Name;
            }

            $alljobtime = array();
            $jobtime = $subset->Getalljobtime();
            foreach ($jobtime as $val) {
                $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
            }

            $allunitofmeasure = array();
            $unitofmeasure = $subset->Getallunitofmeasure();
            foreach ($unitofmeasure as $val) {
                $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
            }

            // get template data 
            $template_data = $subset->GettemplateByTypeDesignationID($desig_id);
            $template_data = $template_data[0];

            //die;
            /* send data in view pages */
            $this->view->frequency = $freq;
            $this->view->CustmeFreq = $CustFreq;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;
            $this->view->unitofmeasure = $allunitofmeasure;
            $this->view->desig_id = $desig_id;
            $this->view->allreading = $allreading;
            $this->view->subset = $allsubset;
            $this->view->listview = $listview;
            $this->view->templateData = $template_data;
        } else {
            exit();
            //return false;
        }
    }

    public function savereadingAction() {
        $data = $this->_request->getPost();
        //print_r($data);

        if (!empty($data)) {
            $task = new Model_PmTemplate();
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['user_id'] = $user_id;
            // get maximum view oder number paramiter table name
            $view_order = $task->Get_MaxViewOrder("pm_vt_template_reading");
            $view_order = $view_order[0]->View_order;
            $data['View_order'] = $view_order + 1;

            $result = $task->InsertReading($data);
            if (empty($result)) {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            } else {
                $msg['status'] = 'success';
                $msg['msg'] = 'Reading save sucessfully';
            }
            echo json_encode($msg);
            exit();
        }
        //die;
    }

    // edit task section 
    public function editreadingAction() {
        //$this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');
        //$data = $this->_request->getPost();
        $data = $this->getRequest()->getParams();
        $desig_id = $data['desig_id'];

        $subset = new Model_PmTemplate();
        $reading_id = $data['reading_id'];
        $ReadingData = $subset->GetreadingDataById($reading_id);

        $allsubset = $subset->GetAllSubset_reading($desig_id);
        //print_r($allsubset);
        // die;
        $freq = array();
        $frequency = $subset->Getallfrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }
        $startdatead = array();
        $startdateadjustment = $subset->Getallstartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdatead[$val->AU_sda_ID] = $val->Name;
        }

        $alljobtime = array();
        $jobtime = $subset->Getalljobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
        }

        $allunitofmeasure = array();
        $unitofmeasure = $subset->Getallunitofmeasure();
        foreach ($unitofmeasure as $val) {
            $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
        }
        $this->view->frequency = $freq;
        $this->view->Interval = $Intreval;
        $this->view->startdateadjustment = $startdatead;
        $this->view->unitofmeasure = $allunitofmeasure;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
        $this->view->readingdata = $ReadingData[0];
    }

    /// Delete tasking 
    public function deletereadingAction() {
        $msg = array();
        $typedata = array();
        $reading = new Model_PmTemplate();
        $param = $this->_request->getPost();
        //$parent_id = $task->getallparent($param['Task_id']);
        $result = $reading->deleteReading($param['reading_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $update = array("Parent_ID" => "");
            $result = $reading->UpdateReadingByparent($update, $param['reading_id']);
            //$result = $reading->deleteReadingByParentId($param['reading_id']); 
            $msg['status'] = 'success';
            $msg['msg'] = 'Template Deleted sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    // update task data
    public function updatereadingAction() {
        $data = $this->_request->getPost();
        //print_r($data); 
        //die;
        if (!empty($data)) {
            $task = new Model_PmTemplate();
            $reading_id = $data['reading_id'];
            $data['Parent_ID'] = $data['parent_id'];
            unset($data['reading_id']);
            unset($data['parent_id']);
            //print_r($data);
            $result = $task->Updatereading($data, $reading_id);
            if (empty($result)) {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            } else {
                $msg['status'] = 'success';
                $msg['msg'] = 'Update Reading save sucessfully';
            }
            echo json_encode($msg);
            exit();
        }
    }

    public function updatereadingorderAction() {
        $data = $this->_request->getPost();
        $task = json_decode($data['reading']);
        $taskmodule = new Model_PmTemplate();
        $return = $this->validationdragandrop($task);
        $order = 1;

        if ($return == 1) {
            foreach ($task as $val) {
                //print_r($val);
                //die;
                if (!empty($val->children) && $val->idSubset) {
                    $getdata = array("View_order" => $order);
                    $taskmodule->Updateodrderreading($getdata, $val->idSubset);
                    $order++;
                    foreach ($val->children as $data) {

                        if (!empty($data->idRoot)) {
                            //echo "idroot children";
                            $getdata = array("View_order" => $order, "Parent_ID" => $val->idSubset);
                            $taskmodule->Updateodrderreading($getdata, $data->idRoot);
                        }
                        if (!empty($data->id)) {
                            //echo "idroot children";
                            $getdata = array("View_order" => $order, "Parent_ID" => $val->idSubset);
                            $taskmodule->Updateodrderreading($getdata, $data->id);
                        }

                        $order++;
                    }
                } else {

                    if (!empty($val->idRoot)) {
                        //echo "root";
                        $getdata = array("View_order" => $order, "Parent_ID" => 0);
                        $taskmodule->Updateodrderreading($getdata, $val->idRoot);
                    }

                    if (!empty($val->id)) {
                        //echo "id";
                        $getdata = array("View_order" => $order, "Parent_ID" => 0);
                        $taskmodule->Updateodrderreading($getdata, $val->id);
                    }
                    $order++;
                }
            }
        }
        if ($return == 0) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error : This move not posible please try other';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Order save sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function editsubsetreadingAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $reading_id = $param['subset_id'];
        $desig_id = $param['desig_id'];
        $subset = new Model_PmTemplate();
        $subdata = $subset->GetreadingDataById($reading_id);
        $subdata = $subdata[0];

        // Send data to view pages
        $this->view->subsetdata = $subdata;
        $this->view->desig_id = $desig_id;
    }

    public function validatesubsetreadingAction() {
        $data = $this->_request->getPost();
        $subsetname = $data['subsetname'];
        $subsetname_id = $data['subsetname_id'];
        $subset = new Model_PmTemplate();
        $result = $subset->GetSubsetreadingByName($subsetname, $subsetname_id);
        if (empty($result)) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit();
    }

    public function savereadingsubsetAction() {
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $msg = array();
        $subsetdata = array();
        $subset = new Model_PmTemplate();
        $data = $this->_request->getPost();
        $insertdata = array();
        $insertdata['Reading_Instruction'] = $data['Reading_Instruction'];
        if($user_id==1){
            $insertdata['VT_Template_Designation_ID'] = $data['desig_id'];
            $table = 'pm_vt_template_reading';
        } else {
            $insertdata['AU_Template_Designation_ID'] = $data['desig_id'];
            $table = 'pm_au_template_reading';
        }
        
        $result = $subset->InsertReading($table,$insertdata);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Subset save sucessfully';
            $msg["InsertId"] = $result;
        }
        echo json_encode($msg);
        exit();
    }

    // subset data

    public function updatereadingsubsetAction() {
        $data = $this->_request->getPost();
        if (!empty($data)) {
            $task = new Model_PmTemplate();
            $task_id = $data['subsetname_id'];
            unset($data['subsetname_id']);
            $result = $task->Updateodrderreading($data, $task_id);
            if (empty($result)) {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            } else {
                $msg['status'] = 'success';
                $msg['msg'] = 'Update subset save sucessfully';
            }
            echo json_encode($msg);
            exit();
        }
    }

    /// create subset level 
    public function createreadingsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $desig_id = $param['desig_id'];
        // send data in view pages
        $this->view->desig_id = $desig_id;
    }

    /* view add reading */

    public function viewaddreadingAction() {

        $this->_helper->getHelper('layout')->disableLayout();
        $param = $this->getRequest()->getParams();

        $desig_id = $param['desig_id'];
        $subset = new Model_PmTemplate();
        $allsubset = $subset->GetAllSubset_reading($desig_id);

        $freq = array();
        $frequency = $subset->Getallfrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }

        $startdateadj = array();
        $startdateadjustment = $subset->Getallstartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdateadj[$val->id] = $val->name;
        }

        $alljobtime = array();
        $jobtime = $subset->Getalljobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->id] = $val->name;
        }

        $allunitofmeasure = array();
        $unitofmeasure = $subset->Getallunitofmeasure();
        foreach ($unitofmeasure as $val) {
            $allunitofmeasure[$val->id] = $val->name;
        }

        /* send data in view pages */
        $this->view->frequency = $freq;
        $this->view->startdateadjustment = $startdateadj;
        $this->view->jobtime = $alljobtime;
        $this->view->unitofmeasure = $allunitofmeasure;
        $this->view->subset = $allsubset;
        $this->view->desig_id = $desig_id;
    }

    /* View All Reading */

    public function viewreadingAction() {
        $this->_helper->getHelper('layout')->disableLayout();
        $param = $this->getRequest()->getParams();
        $desig_id = $param['desig_id'];
        //die(12);
        $subset = new Model_PmTemplate();
        //print_r($view_order);
        $data = $this->_request->getPost();
        if (!empty($desig_id)) {
            $allsubset = $subset->GetAllSubset_reading($desig_id);
            $allreading = array();
            $view_empty_subset = array();
            $getallreading = $subset->GetAllReadingParent($desig_id);
            foreach ($getallreading as $sub) {
                $subreading = $subset->GetReadingBysubsetId($sub->VT_Template_Reading_ID);
                if (!empty($subreading)) {
                    $allreading[$sub->VT_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                    $allreading[$sub->VT_Template_Reading_ID]['id'] = $sub->VT_Template_Reading_ID;
                    $allreading[$sub->VT_Template_Reading_ID]['task'] = $subreading;
                } else if (empty($sub->AU_Frequency_ID)) {
                    $view_empty_subset[$sub->VT_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                    $view_empty_subset[$sub->VT_Template_Reading_ID]['id'] = $sub->VT_Template_Reading_ID;
                } else {
                    $allreading[][] = $sub;
                }
            }
            $allreading = array_merge($allreading, $view_empty_subset);
            $freq = array();
            $frequency = $subset->Getallfrequency();
            foreach ($frequency as $val) {
                if ($val->column == 1)
                    $freq[$val->AU_Frequency_ID] = $val->Name;
                if ($val->column == 2)
                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
            }

            $startdate_ad = array();
            $startdateadjustment = $subset->Getallstartdateadjustment();
            foreach ($startdateadjustment as $val) {
                $startdate_ad[$val->AU_sda_ID] = $val->Name;
            }

            $alljobtime = array();
            $jobtime = $subset->Getalljobtime();
            foreach ($jobtime as $val) {
                $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
            }

            $allunitofmeasure = array();
            $unitofmeasure = $subset->Getallunitofmeasure();
            foreach ($unitofmeasure as $val) {
                $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
            }

            $listview = explode(',', $param['viewlist']);

            /* send data in view pages */
            $this->view->frequency = $freq;
            $this->view->CustmeFreq = $Intreval;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;
            $this->view->unitofmeasure = $allunitofmeasure;
            $this->view->desig_id = $desig_id;
            $this->view->allreading = $allreading;
            $this->view->subset = $allsubset;
            $this->view->listview = $listview;
        }
    }

    // Group modification section start

    /* Task frequency popup section */

    public function taskfrequencyAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $desig_id = $param['desig_id'];
        $section = $param['sec'];
        $Intreval = array();
        $frequency = $subset->Getallfrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }

        $this->view->desig_id = $desig_id;
        $this->view->sec = $section;
        $this->view->CustFreq = $Intreval;
        $this->view->Freq = $freq;
    }

    //// Task Frequency subset
    public function taskfrequencysubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $desig_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $Intreval = array();
        $frequency = $subset->Getallfrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }
        $this->view->desig_id = $desig_id;
        $this->view->parent_id = $parent_id;
        $this->view->CustFreq = $Intreval;
        $this->view->Freq = $freq;
    }

    /// Root frequency 
    public function updatetaskrootfrequeancyAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $Frequency_ID = $data['Frequency_ID'];
        $desig_id = $data['desig_id'];
        $updata['Interval_Value'] = $data['Interval_Value'];
        $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
        $inclidesubset = $data['includesubset'];
        $result = $task->update_grouptask($updata, $desig_id, $inclidesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Frequency Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* subset frequency */

    public function updatetaskfrequeancysubsetAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Interval_Value'] = $data['Interval_Value'];
        $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
        $result = $task->update_grouptasksubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Frequecy Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Start date section start */

    public function taskstartdateAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $this->view->desig_id = $templete_id;
        $this->view->sec = $section;
    }

    public function taskstartdatesubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* Root start date */

    public function updatetaskrootstartdateAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['Start_date'] = date('M Y', strtotime($data['startdate']));
        $result = $task->startDateUpdateForTask($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* update data  subset start date */

    public function updatetasksubsetstartdateAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        //$updata['Start_date'] = $data['startdate'];
        $updata['Start_date'] = date('M Y', strtotime($data['startdate']));
        $result = $task->startDateUpdateForTaskSubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*  Task start date of month secton start */

    /* popup section start */

    public function taskstartdateofmonthAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $this->view->desig_id = $templete_id;
    }

    public function taskstartdateofmonthsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Update tast root start date of month */

    public function updatetaskrootstartdateofmonthAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata = array();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $date = $data['startdateofmonth'];
        //$alltask  = $task->Getalltaskbygroupmodification($desig_id,$includesubset);

        $updata['Startdate_month'] = $data['startdateofmonth'];
        $result = $task->update_grouptask($updata, $desig_id, $includesubset);

        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date of Month sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Update tast subset start date of month */

    public function updatetasksubsetstartdateofmonthAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata = array();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $date = $data['startdateofmonth'];
        $updata['startdate_month'] = $data['startdateofmonth'];

        $result = $task->update_grouptasksubset($updata, $desig_id, $parent_id);


        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date of month Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Start date Adjustment started */

    /* popup section start */

    public function taskstartdateadjustmentAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $subset = new Model_PmTemplate();
        $startdate_ad = array();
        $startdateadjustment = $subset->Getallstartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdate_ad[$val->AU_sda_ID] = $val->Name;
        }
        $this->view->startdateofad = $startdate_ad;
        $this->view->desig_id = $templete_id;
    }

    public function taskstartdateadjustmentsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $subset = new Model_PmTemplate();
        $startdate_ad = array();
        $startdateadjustment = $subset->Getallstartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdate_ad[$val->AU_sda_ID] = $val->Name;
        }
        $this->view->startdateofad = $startdate_ad;
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Root start date adjustment section */

    public function updatetaskrootstartdateadjustmentAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata = array();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['AU_sda_ID'] = $data['startdateadjustment'];
        $result = $task->update_grouptask($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* update data  subset start date */

    public function updatetasksubsetstartdateadjustmentAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata = array();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['AU_sda_ID'] = $data['startdateadjustment'];
        $result = $task->update_grouptasksubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Task  group modification stop */

    /* Reading group modification Start */

    /* popup section start */

    public function readingfrequencyAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $subset = new Model_PmTemplate();
        $Intreval = array();
        $frequency = $subset->Getallfrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }

        $this->view->desig_id = $desig_id;
        //$this->view->sec = $section;
        $this->view->CustFreq = $Intreval;
        $this->view->Freq = $freq;
        $this->view->desig_id = $templete_id;
    }

    public function readingfrequencysubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $desig_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $frequency = $subset->Getallfrequency();
        $Intreval = array();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }

        $this->view->desig_id = $desig_id;
        //$this->view->sec = $section;
        $this->view->Freq = $freq;
        $this->view->CustFreq = $Intreval;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Update reading frequency group modification */

    public function updatereadingrootfrequeancyAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata = array();
        $inclidesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['Interval_Value'] = $data['Interval_Value'];
        $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
        $result = $task->update_groupreading($updata, $desig_id, $inclidesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Frequency Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* subset frequency */

    public function updatereadingfrequeancysubsetAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Interval_Value'] = $data['Interval_Value'];
        $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
        $result = $task->update_groupreadingsubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Frequecy Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Reading  Start date section start */

    /* popup section start */

    public function readingstartdateAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $this->view->desig_id = $templete_id;
        $this->view->sec = $section;
    }

    public function readingstartdatesubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section END */

    /*
     *  Update Reading start date Group modication 
     */

    public function updatereadingrootstartdateAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        //$updata['Start_date'] = $data['startdate'];
        $updata['Start_date'] = date('M Y', strtotime($data['startdate']));
        $result = $task->startDateUpdateForReadingRoot($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*
     *  Update Reading start date subset Group modication 
     */

    public function updatereadingsubsetstartdateAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Start_date'] = date('M Y', strtotime($data['startdate']));
        $result = $task->startDateUpdateForReadingSubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*  Reading start date of month secton start */

    /* popup section start */

    public function readingstartdateofmonthAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $this->view->desig_id = $templete_id;
    }

    public function readingstartdateofmonthsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Update tast root start date of month */

    public function updatereadingrootstartdateofmonthAction() {
        $data = $this->_request->getPost();

        $reading = new Model_PmTemplate();
        $updata = array();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $date = $data['startdateofmonth'];
        //$alltask  = $task->Getalltaskbygroupmodification($desig_id,$includesubset);

        $updata['Startdate_month'] = $data['startdateofmonth'];
        $result = $reading->update_groupreading($updata, $desig_id, $includesubset);



        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date of Month sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Update tast subset start date of month */

    public function updatereadingsubsetstartdateofmonthAction() {
        $data = $this->_request->getPost();
        $reading = new Model_PmTemplate();
        $updata = array();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Startdate_month'] = $data['startdateofmonth'];

        $result = $reading->update_groupreadingsubset($updata, $desig_id, $parent_id);

        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date of month Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*  Reading Start date Adjustment started */

    /* popup section start */

    public function readingstartdateadjustmentAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $subset = new Model_PmTemplate();
        $startdate_ad = array();
        $startdateadjustment = $subset->Getallstartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdate_ad[$val->AU_sda_ID] = $val->Name;
        }
        $this->view->startdateofad = $startdate_ad;
        $this->view->desig_id = $templete_id;
    }

    public function readingstartdateadjustmentsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $subset = new Model_PmTemplate();
        $startdate_ad = array();
        $startdateadjustment = $subset->Getallstartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdate_ad[$val->AU_sda_ID] = $val->Name;
        }
        $this->view->startdateofad = $startdate_ad;
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Root start date adjustment section */

    public function updatereadingrootstartdateadjustmentAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['AU_sda_ID'] = $data['startdateadjustment'];
        $result = $task->update_groupreading($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* update data  subset start date */

    public function updatereadingsubsetstartdateadjustmentAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['AU_sda_ID'] = $data['startdateadjustment'];
        $result = $task->update_groupreadingsubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* End Reading start date Adjustment section */

    /* Start Reading value section */

    /* popup section start */

    public function readingvalueAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $this->view->desig_id = $templete_id;
    }

    public function readingvaluesubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Root start date adjustment section */

    public function updatereadingrootreadingvalueAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['Reading_Value'] = $data['reading_value'];
        $result = $task->update_groupreading($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Reading Value On Root Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* update data  subset start date */

    public function updatereadingsubsetreadingvalueAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Reading_Value'] = $data['readingvalue'];
        $result = $task->update_groupreadingsubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Reading Value On Subset Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* End Reading value section */

    /* Start Unit Of Measure section */

    /* popup section start */

    public function readingunitofmeasureAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $allunitofmeasure = array();
        $unitofmeasure = $subset->Getallunitofmeasure();
        foreach ($unitofmeasure as $val) {
            $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
        }

        /* Send data to View pages */
        $this->view->desig_id = $templete_id;
        $this->view->unitofmeasure = $allunitofmeasure;
    }

    public function readingunitofmeasuresubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $allunitofmeasure = array();
        $unitofmeasure = $subset->Getallunitofmeasure();
        foreach ($unitofmeasure as $val) {
            $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
        }

        /* Send data to View pages */
        $this->view->desig_id = $templete_id;
        $this->view->unitofmeasure = $allunitofmeasure;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Root start date adjustment section */

    public function updatereadingrootunitofmeasureAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['AU_uom_ID'] = $data['unitofmeasure'];
        $result = $task->update_groupreading($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Reading Value On Root Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* update data  subset start date */

    public function updatereadingsubsetunitofmeasureAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['AU_uom_ID'] = $data['unitofmeasure'];
        $result = $task->update_groupreadingsubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Reading Value On Subset Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* End Unit Of Measure section */

    /* Start Unit Of Measure section */

    /* popup section start */

    public function readingtoleranceAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];

        /* Send data to View pages */
        $this->view->desig_id = $templete_id;
    }

    public function readingtolerancesubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];

        /* Send data to View pages */
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Root start date adjustment section */

    public function updatereadingroottoleranceAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['Tolerance'] = $data['tolerance'];
        $result = $task->update_groupreading($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Reading Value On Root Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* update data  subset start date */

    public function updatereadingsubsettoleranceAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Tolerance'] = $data['tolerance'];
        $result = $task->update_groupreadingsubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Reading Value On Subset Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* End Unit Of Measure section */


    /* End Group Modification section */

    /* Import section */

    public function importAction() {
        $this->_helper->layout()->disableLayout();
        //$this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getPost();
        $desig_id = $param['desig_id'];
        $template = new Model_PmTemplate();
        $resultDesign = $template->get_all_typedesignation($desig_id);

        /* send data to view page */
        $this->view->designation = $resultDesign;
        $this->view->desig_id = $desig_id;
    }

    public function importreadingAction() {
        $this->_helper->layout()->disableLayout();
        //$this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $desig_id = $param['desig_id'];
        $template = new Model_PmTemplate();
        $resultDesign = $template->get_all_typedesignation($desig_id);

        /* send data to view page */
        $this->view->designation = $resultDesign;
        $this->view->desig_id = $desig_id;
    }

    public function importapplyAction() {
        $data = $this->_request->getPost();
        $desig_id = $data['desig_id'];
        $import_id = $data['import_id'];
        $template = new Model_PmTemplate();
        $task_subset = $template->get_subsetbyid('pm_vt_template_task', $import_id);
        // print_r($task_subset);
        //die;
        /* task section */
        foreach ($task_subset as $tsubset) {
            $innertask = $template->get_taskbysubsetId('pm_vt_template_task', $import_id, $tsubset->VT_Template_Task_ID);
            $data = (array) $tsubset;
            unset($data['VT_Template_Task_ID']);
            $data['VT_Template_Designation_ID'] = $desig_id;

            //print_r($data);
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['User_ID'] = $user_id;
            $parent_id = $template->insertsubset($data);
            if (!empty($innertask)) {
                foreach ($innertask as $task) {
                    $innerdata = (array) $task;
                    unset($innerdata['VT_Template_Task_ID']);
                    $innerdata['Parent_ID'] = $parent_id;
                    $innerdata['VT_Template_Designation_ID'] = $desig_id;
                    $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                    $innerdata['User_ID'] = $user_id;
                    $template->Inserttask($innerdata);
                    //print_r($innerdata);
                }
            }
        }
        $task_data = $template->get_tabledata('pm_vt_template_task', $import_id);
        foreach ($task_data as $tdata) {
            $data = (array) $tdata;
            unset($data['VT_Template_Task_ID']);
            $data['VT_Template_Designation_ID'] = $desig_id;
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['User_ID'] = $user_id;
            $template->Inserttask($data);
        }

        /* Reading section start */
        $reading_subset = $template->get_subsetbyid('pm_vt_template_reading', $import_id);
        //print_r($reading_subset);
        //die;
        foreach ($reading_subset as $tsubset) {
            $innertask = $template->get_taskbysubsetId('pm_vt_template_reading', $import_id, $tsubset->VT_Template_Reading_ID);
            $data = (array) $tsubset;
            unset($data['VT_Template_Reading_ID']);
            $data['VT_Template_Designation_ID'] = $desig_id;
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['User_ID'] = $user_id;
            $parent_id = $template->InsertReadingsubset($data);

            if (!empty($innertask)) {

                foreach ($innertask as $task) {
                    $innerdata = (array) $task;
                    unset($innerdata[id]);
                    $innerdata['Parent_ID'] = $parent_id;
                    $innerdata['VT_Template_Designation_ID'] = $desig_id;
                    $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                    $innerdata['User_ID'] = $user_id;
                    unset($innerdata['VT_Template_Reading_ID']);
                    $template->InsertReading($innerdata);
                }
            }
        }
        $task_data = $template->get_tabledata('pm_vt_template_reading', $import_id);
        foreach ($task_data as $tdata) {
            $data = (array) $tdata;
            unset($data['VT_Template_Reading_ID']);
            $data['VT_Template_Designation_ID'] = $desig_id;
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['User_ID'] = $user_id;
            $result = $template->InsertReading($data);
        }
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Import All The Data Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*  End import section */

    function custometimejobAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $freq = explode("-", $param['timejob']);

        $this->view->customedata = $freq;
        $subset = new Model_PmTemplate();
        $alljobtime = array();
        $jobtime = $subset->Getalljobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->id] = $val->name;
        }
        $this->view->timejob = $alljobtime;
    }

    function insertfrequencyAction() {
        $data = $this->_request->getPost();
        $InsertData = array(
            "Name" => $data['name'],
            "value" => $data['value'],
            "type" => $data['type'],
        );
        $subset = new Model_PmTemplate();
        $last_id = $subset->InsertFrequencyData($InsertData);
        $return = array("id" => $last_id);
        echo json_encode($return);
        die;
    }

    public function getsubsetAction() {
        $data = $this->_request->getPost();
        $desig_id = $data['desig_id'];
        $subset = new Model_PmTemplate();
        $allsubset = $subset->GetAllSubset($desig_id);
        echo json_encode($allsubset);
        die;
    }

    public function getsubsetreadingAction() {
        $data = $this->_request->getPost();
        $desig_id = $data['desig_id'];
        $subset = new Model_PmTemplate();
        $allsubset = $subset->GetAllSubsetReading($desig_id);
        echo json_encode($allsubset);
        die;
    }

    /*     * *************************** Equipment template start ******************************** */

    public function equipmenttemplateAction() {
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $cust_id = $_SESSION['Zend_Auth']['storage']->cust_id;
        $role_id = $_SESSION['Zend_Auth']['storage']->role_id;
        $companyListing = '';
        $buildingMapper = new Model_Building();
        $rModel = new Model_Report();
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

        $_SESSION['current_building'] = $select_build_id;
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;

        $templateName = "";
        $designationName = "";
        $data = $this->_request->getPost();
        $template = new Model_PmTemplate();
        $listAllTemplateName = $template->GetAllEquipmentTemplateName("", $select_build_id);
        $templatedata = array();
        if ($data['search'] == 'Search') {
            $templateName = $data['templatename'];
            $designationName = $data['designationname'];
            $tempdata = $template->GetAllEquipmentTemplateName($templateName, $select_build_id);

            foreach ($tempdata as $temp) {
                $find = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID, $designationName, $select_build_id);
                if (!empty($find) && $designationName != "") {
                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name] = $temp->AU_Template_Name;
                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name_ID] = $temp->AU_Template_Name_ID;
                    $templatedata[$temp->AU_Template_Name_ID][AU_TypeDesignation] = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID, $designationName, $select_build_id);
                } else if ($designationName == "") {
                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name] = $temp->AU_Template_Name;
                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name_ID] = $temp->AU_Template_Name_ID;
                    $templatedata[$temp->AU_Template_Name_ID][AU_TypeDesignation] = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID, $designationName, $select_build_id);
                }
            }
        } else {
            $tempdata = $template->GetAllEquipmentTemplateName("", $select_build_id);
            //print_r($tempdata);
            // die;
            foreach ($tempdata as $temp) {
                $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name] = $temp->AU_Template_Name;
                $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name_ID] = $temp->AU_Template_Name_ID;
                $templatedata[$temp->AU_Template_Name_ID][AU_TypeDesignation] = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID, $designationName, $select_build_id);
            }
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

        $this->view->userId = $user_id;
        $this->view->templatedetails = $templatedata;
        $pageObj = new Ve_Paginator();
        $paginator = $pageObj->fetchPageDataResult($templatedata, $page, $show);
        $this->view->templatedetails = $paginator;
        $this->view->listAllTemplateName = $listAllTemplateName;
        $this->view->templateName = $templateName;
        $this->view->designationName = $designationName;
        $this->view->custID = $cust_id;
        $this->view->select_build_id = $select_build_id;
        $this->view->roleId = $role_id;
        $this->view->acessHelper = $this->accessHelper;
        $this->view->croom_location = 29;
        $taskData = $template->getUpdatedEquipmentTaskList();
        $readingData = $template->getUpdatedEquipmentReadingList();
        /*$dashboard_menu = 42;
        $reportDetail = $rModel->getReportDedail($dashboard_menu); */               
        $this->view->nooftask = sizeof($taskData);
        $this->view->noofreading = sizeof($readingData);
        $this->view->reportDetail = $reportDetail[0];
        $this->view->page = $page;
        $this->view->show = $show;
    }

    public function createequipmenttemplatedesignationAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $template = new Model_PmTemplate();
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $build_id = $_SESSION['current_building'];
        $tempdata = $template->GetAllEquipmentTemplateName("", $build_id, null);
        // send data to view pages
        $this->view->templats = $tempdata;
    }

    public function createequipmenttemplateAction() {
        //$this->_helper->layout()->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');
    }

    public function getViewAccess($bid) {

        $checkscheduler = new Model_ConferenceSchedule();
        $getscheduler = $checkscheduler->getcrDetailsByBid($bid);

        foreach ($getscheduler as $da) {
            $getcs = $checkscheduler->getCrdata($da->schedule_id);
            $data[] = $this->getshowday($getcs[0]->week_days_id);
        }
        foreach ($data as $get) {
            foreach ($get as $get) {
                $final[$get] = $get;
            }
        }
        return $final;
    }

    // validate template before creation
    public function validateequipmenttemplateAction() {
        //$param = $this->getRequest()->getParams();
        $param = $this->_request->getPost();

        $template_Name = $param['TemplateName'];
        $template_id = $param['Template_id'];
        $build_id = $_SESSION['current_building'];
        ;
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $template = new Model_PmTemplate();
        $result = $template->GetEquipmentTemplateByName($template_Name, $template_id, $build_id, $user_id);
        if (empty($result)) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit();
    }

    // Save template Name
    public function saveequipmenttemplateAction() {
        $msg = array();
        $templatedata = array();
        $template = new Model_PmTemplate();

        $param = $this->_request->getPost();
        //print_r($param);
        //die;
        //$param = $this->getRequest()->getParams();
        $templatedata['AU_Template_Name'] = $param['TemplateName'];
        $templatedata['BuildingID'] = $_SESSION['current_building'];
        $templatedata['user_id'] = $_SESSION['Zend_Auth']['storage']->uid;
        //print_r($templatedata);
        //die;
        $result = $template->InsertEquipmentTemplateName($templatedata);
        //print_r($result);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Temaplate save sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Edit  designation */

    public function editequipmenttemplatedesignationAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();

        $typedesignation_id = $param['desig_id'];
        $template = new Model_PmTemplate();
        $typedata = $template->GetequipmenttemplatetypedesignationById($typedesignation_id);

        //print_r($templatedata);
        $typedata = $typedata[0];
        $build_id = $_SESSION['current_building'];
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        //$tempdata = $template->GetAllEquipmentTemplateName("", $build_id, $user_id);
        $tempdata = $template->GetAllEquipmentTemplateName("", $build_id);
        //print_r($tempdata);
        //die;
        // send data on view pages
        $this->view->VT_TypeDesignation = $typedata;
        $this->view->templats = $tempdata;
    }

    //  Validate Type designation  //
    public function validateequipmenttemplatetypedesignationAction() {
        $data = $this->_request->getPost();
        //$param = $this->getRequest()->getParams();
        $typedesignation = $data['typedesignation'];
        $typedesination_id = $data['typedesination_id'];
        $build_ID = $_SESSION['current_building'];
        $template = new Model_PmTemplate();
        $result = $template->GetEquipmentTemplateIdByTypeDesignation($typedesignation, $typedesination_id, $build_ID);

        if (empty($result)) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit();
    }

    /* save Type designation */

    public function saveequipmenttemplatetypedesignationAction() {
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();
        //$param = $this->getRequest()->getParams();       
        $param = $this->_request->getPost();
        //print_r($param);

        $typedata['AU_Template_Name_ID'] = $param['AU_Template_Name_ID'];
        $typedata['AU_TypeDesignation'] = $param['AU_TypeDesignation'];
        $typedata['AU_TypeDescritpion'] = $param['AU_TypeDescritpion'];
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $typedata['user_id'] = $user_id;
        //print_r($typedata);
        $result = $template->InsertEquipmentTemplateTypeDesignation($typedata);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Type Designation save sucessfully';
            $msg['id'] = $result;
        }
        echo json_encode($msg);
        exit();
    }

    // edit template name

    public function editequipmenttemplateAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $template_id = $param['template_id'];
        $template = new Model_PmTemplate();
        $templatedata = $template->GetEquipmentTemplateNameById($template_id);
        //print_r($templatedata);
        $templatedata = $templatedata[0];
        //die;
        // send data on view pages
        $this->view->template = $templatedata;
    }

    // update template Name 
    public function updateequipmenttemplateAction() {
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();
        $typedata['AU_Template_Name'] = $param['TemplateName'];
        //$typedata['TypeDestination'] = $param['Template_id'];

        $result = $template->updateEquipmentTemplate($typedata, $param['Template_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Template Update sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    // Delete Template 
    public function deleteequipmenttemplateAction() {
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();

        $result = $template->deleteEquipmentTemplate($param['Template_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {

            $template->deleteEquipmentTemplateTypeDesignationByTemplateId($param['Template_id']);
            $msg['status'] = 'success';
            $msg['msg'] = 'Template Deleted sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Update type designation */

    public function updateequipmenttemplatetypedesignationAction() {
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();
        $typedata['AU_Template_Name_ID'] = $param['template_id'];
        $typedata['AU_TypeDesignation'] = $param['typedesignation'];
        $typedata['AU_TypeDescritpion'] = $param['typedescription'];
        $result = $template->updateEquipmentTemplatetypedesignation($typedata, $param['typedesination_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Type Designation Update sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* delete type designation */

    public function deleteequipmenttemplatetypedescriptionAction() {
        $msg = array();
        $template = new Model_PmTemplate();
        $data = $this->_request->getPost();

        $result = $template->deleteEquipmentTemplateTypeDesignation($data['type_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Template Deleted sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    // Task Section start

    /* Create a task */
    public function createequipmenttemplatetaskAction() {
        $param = $this->getRequest()->getParams();
        $desig_id = $param['desig_id'];

        $subset = new Model_PmTemplate();
        if (!empty($desig_id)) {
            $allsubset = $subset->GetAllEquipmentTemplateSubset($desig_id);
            //die;
            //$data = $this->_request->getPost();
            $msg = array();
            $alltask = array();
            $getalltask = $subset->GetAllEquipmentTemplatetaskparent($desig_id);
            foreach ($getalltask as $sub) {
                $subtask = $subset->GetEquipmentTemplateTaskBysubsetId($sub->AU_Template_Task_ID);
                if (!empty($subtask)) {
                    $alltask[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                    $alltask[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                    $alltask[$sub->AU_Template_Task_ID]['task'] = $subtask;
                } else {
                    $alltask[][] = $sub;
                }
            }
            //print_r($alltask);
            // die;
            $list = $subset->get_au_view_table('task');
            $listview = explode(',', $list[0]->display_table_view);
            $freq = array();
            $Intreval = array();
            $frequency = $subset->GetallEquipmentTemplatefrequency();
            foreach ($frequency as $val) {
                if ($val->column == 1)
                    $freq[$val->AU_Frequency_ID] = $val->Name;
                if ($val->column == 2)
                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
            }

            $startdate_ad = array();
            $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
            foreach ($startdateadjustment as $val) {
                $startdate_ad[$val->AU_sda_ID] = $val->Name;
            }

            $alljobtime = array();
            $jobtime = $subset->GetallEquipmentTemplatejobtime();
            foreach ($jobtime as $val) {
                $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
            }
            // get template data 
            $template_data = $subset->GetEquipmentTemplateByTypeDesignationID($param['desig_id']);
            $template_data = $template_data[0];

            // send data in view pages
            $this->view->desig_id = $desig_id;
            $this->view->alltask = $alltask;
            $this->view->subset = $allsubset;
            $this->view->listview = $listview;
            $this->view->frequency = $freq;
            $this->view->CustmeFreq = $Intreval;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;
            $this->view->templateData = $template_data;
        } else {
            exit();
            //return false;
        }
    }

    // edit task section 
    public function viewequipmenttemplatetaskAction() {
        $this->_helper->getHelper('layout')->disableLayout();
        //$param = $this->getRequest()->getParams();
        $param = $this->_request->getPost();
        $desig_id = $param['desig_id'];

        $subset = new Model_PmTemplate();
        if (!empty($desig_id)) {
            $allsubset = $subset->GetAllEquipmentTemplateSubset($desig_id);
            //die;
            //$data = $this->_request->getPost();
            $msg = array();
            $alltask = array();
            $view_empty_subset = array();
            $getalltask = $subset->GetAllEquipmentTemplatetaskparent($desig_id);
            //print_r($getalltask);
            //die;
            foreach ($getalltask as $sub) {
                $subtask = $subset->GetEquipmentTemplateTaskBysubsetId($sub->AU_Template_Task_ID);
                //print_r($subtask);
                if (!empty($subtask)) {
                    $alltask[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                    $alltask[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                    $alltask[$sub->AU_Template_Task_ID]['task'] = $subtask;
                } else if (empty($sub->AU_Frequency_ID)) {
                    $view_empty_subset[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                    $view_empty_subset[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                    $view_empty_subset[$sub->AU_Template_Task_ID]['task'] = "";
                } else {
                    $alltask[][] = $sub;
                }
            }
            $alltask = array_merge($alltask, $view_empty_subset);
            $listview = explode(',', $param['viewlist']);
            $freq = array();
            $Intreval = array();
            $frequency = $subset->GetallEquipmentTemplatefrequency();
            foreach ($frequency as $val) {

                if ($val->column == 1)
                    $freq[$val->AU_Frequency_ID] = $val->Name;
                if ($val->column == 2)
                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
            }

            $startdate_ad = array();
            $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
            foreach ($startdateadjustment as $val) {
                $startdate_ad[$val->AU_sda_ID] = $val->Name;
            }

            $alljobtime = array();
            $jobtime = $subset->GetallEquipmentTemplatejobtime();
            foreach ($jobtime as $val) {
                $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
            }

            // send data in view pages
            $this->view->desig_id = $desig_id;
            $this->view->alltask = $alltask;
            $this->view->subset = $allsubset;
            $this->view->listview = $listview;
            $this->view->frequency = $freq;
            $this->view->CustmeFreq = $Intreval;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;
        } else {
            exit();
            //return false;
        }
    }

    // view add new task 
    public function addequipmenttemplatetaskAction() {
        //$this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');
        $subset = new Model_PmTemplate();
        $data = $this->_request->getParams();
        //echo $data['desig_id'];
        $desig_id = $data['desig_id'];
        $allsubset = $subset->GetAllEquipmentTemplateSubset($desig_id);
        //print_r($allsubset);
        //die;
        $freq = array();
        $frequency = $subset->GetallEquipmentTemplatefrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }
        //print_r($freq);
        //die;
        $startdatead = array();
        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdatead[$val->AU_sda_ID] = $val->Name;
        }

        $alljobtime = array();
        $jobtime = $subset->GetallEquipmentTemplatejobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
        }

        /* Reading section */
        $ReadingSubset = $subset->GetAllEquipmentTemplateSubset_reading($desig_id);
        //print_r($jobtime);
        //die;
        $this->view->frequency = $freq;
        $this->view->Interval = $Intreval;
        $this->view->startdateadjustment = $startdatead;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
        $this->view->ReadingSubset = $ReadingSubset;
    }

    // view add new task 
    public function addequipmenttemplatereadingAction() {
        //$this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');
        $subset = new Model_PmTemplate();
        $data = $this->_request->getParams();
        //echo $data['desig_id'];
        $desig_id = $data['desig_id'];
        $allsubset = $subset->GetAllEquipmentTemplateSubset_reading($desig_id);
        //print_r($allsubset);
        // die;
        $freq = array();
        $frequency = $subset->GetallEquipmentTemplatefrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }
        $startdatead = array();
        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdatead[$val->AU_sda_ID] = $val->Name;
        }

        $alljobtime = array();
        $jobtime = $subset->Getalljobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
        }

        $allunitofmeasure = array();
        $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
        foreach ($unitofmeasure as $val) {
            $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
        }
        $this->view->frequency = $freq;
        $this->view->Interval = $Intreval;
        $this->view->startdateadjustment = $startdatead;
        $this->view->unitofmeasure = $allunitofmeasure;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
    }

    /* Add a new subset */

    public function createequipmenttemplatesubsetAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_request->getPost();
        $desig_id = $param['desig_id'];

        // send data in view pages
        $this->view->desig_id = $desig_id;
    }

    /* Update View Task */

    public function updateequipmenttemplateviewtaskAction() {
        $param = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $update = array();
        $update['display_table_view'] = $param['viewlist'];
        //$update['pm_type'] = $param['type'];        
        $result = $task->UpdateEquipmentTemplateviewlist($update, $param['type']);
        if (!empty($result)) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit();
    }

    /* validate subset */

    public function validateequipmenttemplatesubsetAction() {
        $data = $this->_request->getPost();
        $subsetname = $data['subsetname'];
        $subsetname_id = $data['subsetname_id'];
        $desig_id = $data['desig_id'];
        $subset = new Model_PmTemplate();

        $result = $subset->GetEquipmentTemplateSubseteByName($subsetname, $subsetname_id, $desig_id);
        if (empty($result)) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit();
    }

    /* Save a new subset */

    public function saveequipmenttemplatesubsetAction() {
        $build_ID = $_SESSION['current_building'];
        $msg = array();
        $subsetdata = array();
        $subset = new Model_PmTemplate();
        $data = $this->_request->getPost();
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $data['user_id'] = $user_id;
        $templateTaskId = $subset->insertEquipmentTemplatesubset($data);
        //print_r($data);
        //die;
        if (empty($templateTaskId)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            
            //Insert into equipment detail task
            $result = $subset->getEquipmentDetailID($build_ID,$data['AU_Template_Designation_ID']);
                foreach($result as $val){
                    
                    $datas = array('AU_Equipment_Detail_ID' => $val->AU_Equipment_Detail_ID,
                        'AU_Template_Task_ID' => $templateTaskId);
                    $subset->InsertEquipmentTemplateTaskInEquipmentDetail($datas);                    
                    
                } 
            
            $msg['status'] = 'success';
            $msg['msg'] = 'Subset save sucessfully';
            $msg['InsertId'] = $templateTaskId;
        }
        echo json_encode($msg);
        exit();
    }

    /* Edit subset */

    public function editequipmenttemplatesubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset_id = $param['subset_id'];
        $desig_id = $param['desig_id'];
        $subset = new Model_PmTemplate();
        $subdata = $subset->GetEquipmentTemplatetaskDataById($subset_id);
        $subdata = $subdata[0];

        // Send data to view pages
        $this->view->subsetdata = $subdata;
        $this->view->desig_id = $desig_id;
    }

    /* update subset data */

    public function updateequipmenttemplatesubsetAction() {
        $data = $this->_request->getPost();
        //print_r($data);
        if (!empty($data)) {
            $task = new Model_PmTemplate();
            $task_id = $data['subsetname_id'];
            unset($data['subsetname_id']);
            $result = $task->UpdateEquipmentTemplatetask($data, $task_id);
            if (empty($result)) {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            } else {
                $msg['status'] = 'success';
                $msg['msg'] = 'Update subset save sucessfully';
            }
            echo json_encode($msg);
            exit();
        }
    }

    public function customeequipmenttemplatefrequencyAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $freq = $param['frequency'];
        $GetData = "";
        $task = new Model_PmTemplate();
        if ($freq != "") {
            $GetData = $task->getEquipmentTemplate_FrequencydataByID($freq);
            $GetData = $GetData[0];
        }
        $this->view->GetData = $GetData;
    }

    public function customeequipmenttemplatefreqAction() {
        $msg = array();
        $data = $this->_request->getPost();
        $_SESSION['custome_freq'] = $data;
        $msg['status'] = 'success';
        $msg['msg'] = 'Frequency save sucessfully';
        echo json_encode($msg);
        exit();
    }

    public function saveequipmenttemplatetaskAction() {
        $build_ID = $_SESSION['current_building'];
        $data = $this->_request->getPost();
        if (!empty($data)) {
            $task = new Model_PmTemplate();
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['user_id'] = $user_id;
            $view_order = $task->Get_MaxViewOrder("pm_au_template_task");
            $view_order = $view_order[0]->View_order;
            $data['View_order'] = $view_order + 1;
            //print_r($data);
            //die;
            $templateTaskId = $task->InsertEquipmentTemplatetask($data);
            if (empty($templateTaskId)) {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            } else {
                // Need to update equipment details
               /* $result1 = $task->getupdatedfromtemplate($data['AU_Template_Designation_ID']);
                $result2 = $task->getEquipmentToUpdate();
                foreach ($result2 as $val) {
                    $datas = array('AU_Equipment_Detail_ID' => $val->AU_Equipment_Detail_ID,
                        'AU_Template_Task_ID' => $templateTaskId,
                        'Start_Date' => $data['Start_date'],
                        'Email_group_ID' => $data['Assigned_to']);
                    if ($val->AU_Template_Designation_ID == $result1[0]->AU_Template_Designation_ID && $val->total < $result1[0]->total) {
                        $task->InsertEquipmentTemplateTaskInEquipmentDetail($datas);
                    }
                }*/
                $result = $task->getEquipmentDetailID($build_ID,$data['AU_Template_Designation_ID']);
                foreach($result as $val){
                    
                    $datas = array('AU_Equipment_Detail_ID' => $val->AU_Equipment_Detail_ID,
                        'AU_Template_Task_ID' => $templateTaskId,
                        'Start_Date' => $data['Start_date'],
                        'Email_group_ID' => $data['Assigned_to']);
                    $task->InsertEquipmentTemplateTaskInEquipmentDetail($datas);                    
                    
                }
                $msg['status'] = 'success';
                $msg['msg'] = 'Task save sucessfully';
            }
            echo json_encode($msg);
            exit();
        }
        //die;
    }

    // update task data
    public function updateequipmenttemplatetaskAction() {
        $data = $this->_request->getPost();
        if (!empty($data)) {
            $task = new Model_PmTemplate();
            $task_id = $data['task_id'];
            unset($data['task_id']);
            $result = $task->UpdateEquipmentTemplatetask($data, $task_id);
            if (empty($result)) {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            } else {
                $msg['status'] = 'success';
                $msg['msg'] = 'Update Task save sucessfully';
            }
            echo json_encode($msg);
            exit();
        }
    }

    public function updateequipmenttemplatetaskorderAction() {

        $data = $this->_request->getPost();
        $task = json_decode($data[task]);
        $taskmodule = new Model_PmTemplate();
        $return = $this->validationdragandrop($task);
        $order = 1;
        if ($return == 1) {
            foreach ($task as $val) {
                //print_r($val);
                if (!empty($val->children) && $val->idSubset) {
                    $getdata = array("view_order" => $order);
                    $taskmodule->UpdateEquipmentTemplateodrder($getdata, $val->idSubset);
                    $order++;
                    foreach ($val->children as $data) {

                        if (!empty($data->idRoot)) {
                            //echo "idroot children";
                            $getdata = array("view_order" => $order, "Parent_ID" => $val->idSubset);
                            $taskmodule->UpdateEquipmentTemplateodrder($getdata, $data->idRoot);
                        }
                        if (!empty($data->id)) {
                            //echo "idroot children";
                            $getdata = array("view_order" => $order, "Parent_ID" => $val->idSubset);
                            $taskmodule->UpdateEquipmentTemplateodrder($getdata, $data->id);
                        }

                        $order++;
                    }
                } else {

                    if (!empty($val->idRoot)) {
                        //echo "root";
                        $getdata = array("view_order" => $order, "Parent_ID" => 0);
                        $taskmodule->UpdateEquipmentTemplateodrder($getdata, $val->idRoot);
                    }

                    if (!empty($val->id)) {
                        //echo "id";
                        $getdata = array("view_order" => $order, "Parent_ID" => 0);
                        $taskmodule->UpdateEquipmentTemplateodrder($getdata, $val->id);
                    }
                    $order++;
                }
            }
        }
        if ($return == 0) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error : This move not posible please try other';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Order save sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function validationdragandrop($data) {
        $ret_data = 1;
        foreach ($data as $val) {
            //print_r($val);
            if ($val->idRoot) {
                if (!empty($val->children))
                    $ret_data = 0;
            }
            if ($val->idSubset) {
                foreach ($val->children as $data) {
                    if ($data->idSubset) {
                        $ret_data = 0;
                    }
                }
            }
        }
        return $ret_data;
    }

    // edit task section 
    public function editequipmenttemplatetaskAction() {
        //$this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');

        $subset = new Model_PmTemplate();
        $data = $this->_request->getParams();
        $task_id = $data['task_id'];
        $desig_id = $data['desig_id'];

        $TaskData = $subset->GetEquipmentTemplatetaskDataById($task_id);
        $TaskData = $TaskData[0];
        $allsubset = $subset->GetAllEquipmentTemplateSubset($desig_id);
        $freq = array();
        $frequency = $subset->GetallEquipmentTemplatefrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }

        $startdate_ad = array();
        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdate_ad[$val->AU_sda_ID] = $val->Name;
        }

        $alljobtime = array();
        $jobtime = $subset->GetallEquipmentTemplatejobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
        }

        $this->view->frequency = $freq;
        $this->view->Interval = $Intreval;
        $this->view->startdateadjustment = $startdate_ad;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
        $this->view->taskdata = $TaskData;
    }

    /// Delete tasking 
    public function deleteequipmenttemplatetaskAction() {
        $msg = array();
        $typedata = array();
        $task = new Model_PmTemplate();
        $param = $this->_request->getPost();
        $parent_id = $task->getallEquipmentTemplateparent($param['Task_id']);
        $result = $task->deleteEquipmentTemplateTask($param['Task_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            //$result = $task->deleteEquipmentTemplateTaskByParentId($param['Task_id']);
            $update = array("Parent_ID" => "");
            $result = $task->UpdateEquipmentTemplateTaskByparent($update, $param['Task_id']);
            if (!empty($result)) {
                $msg['status'] = 'success';
                $msg['msg'] = 'Template Deleted sucessfully';
            } else {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for Delete data';
            }
        }
        echo json_encode($msg);
        exit();
    }

    /* Reading Section start */

    public function createequipmenttemplatereadingAction() {
        $param = $this->getRequest()->getParams();
        $desig_id = $param['desig_id'];
        if (!empty($desig_id)) {
            $subset = new Model_PmTemplate();
            $allsubset = $subset->GetAllEquipmentTemplateSubset_reading($desig_id);
            $data = $this->_request->getPost();
            $msg = array();
            $list = $subset->get_au_view_table('Reading');
            $listview = explode(',', $list[0]->display_table_view);
            $allreading = array();
            $getallreading = $subset->GetAllEquipmentTemplateReadingParent($desig_id);
            foreach ($getallreading as $sub) {
                //print_r($sub);
                $subreading = $subset->GetEquipmentTemplateReadingBysubsetId($sub->AU_Template_Reading_ID);
                if (!empty($subreading)) {
                    $allreading[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                    $allreading[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                    $allreading[$sub->AU_Template_Reading_ID]['task'] = $subreading;
                } else {
                    $allreading[][] = $sub;
                }
            }
            //print_r($allreading);
            //die;
            $freq = array();
            $frequency = $subset->GetallEquipmentTemplatefrequency();
            foreach ($frequency as $val) {
                if ($val->column == 1)
                    $freq[$val->AU_Frequency_ID] = $val->Name;
                if ($val->column == 2)
                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
            }

            $startdate_ad = array();
            $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
            foreach ($startdateadjustment as $val) {
                $startdate_ad[$val->AU_sda_ID] = $val->Name;
            }

            $alljobtime = array();
            $jobtime = $subset->GetallEquipmentTemplatejobtime();
            foreach ($jobtime as $val) {
                $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
            }

            $allunitofmeasure = array();
            $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
            foreach ($unitofmeasure as $val) {
                $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
            }

            // get template data 
            $template_data = $subset->GetEquipmentTemplateByTypeDesignationID($param['desig_id']);
            $template_data = $template_data[0];

            /* send data in view pages */
            $this->view->frequency = $freq;
            $this->view->CustmeFreq = $Intreval;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;
            $this->view->unitofmeasure = $allunitofmeasure;
            $this->view->desig_id = $desig_id;
            $this->view->allreading = $allreading;
            $this->view->subset = $allsubset;
            $this->view->listview = $listview;
            $this->view->templateDate = $template_data;
        } else {
            exit();
            //return false;
        }
    }

    public function saveequipmenttemplatereadingAction() {
        $build_ID = $_SESSION['current_building'];
        $data = $this->_request->getPost();
        //print_r($data);

        if (!empty($data)) {
            $task = new Model_PmTemplate();
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['user_id'] = $user_id;
            $view_order = $task->Get_MaxViewOrder("pm_au_template_reading");
            $view_order = $view_order[0]->View_order;
            $data['View_order'] = $view_order + 1;
            $templateReadingId = $task->InsertEquipmentTemplateReading($data);
            if (empty($templateReadingId)) {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            } else {
                // Need to update equipment details
                /*$result1 = $task->getupdatedfromtemplateReading($data['AU_Template_Designation_ID']);
                $result2 = $task->getEquipmentToUpdateReading();
                $flag = 0;
                foreach ($result2 as $val) {
                    if(!empty($val->AU_Equipment_Detail_ID)){
                        $datas = array('AU_Equipment_Detail_ID' => $val->AU_Equipment_Detail_ID,
                        'AU_Template_Reading_ID' => $templateReadingId,
                        'Start_Date' => $data['Start_date'],
                        'Email_group_ID' => $data['Assigned_to']);
                        if ($val->AU_Template_Designation_ID == $result1[0]->AU_Template_Designation_ID && $val->total < $result1[0]->total) {
                            $task->InsertEquipmentTemplateReadingInEquipmentDetail($datas);
                            $flag = 1;
                            break;
                        }
                        if($flag==0){
                             $equDetailId = $task->getEquipmentDetailID();
                             $datas = array('AU_Equipment_Detail_ID' => $equDetailId[0]->AU_Equipment_Detail_ID,
                                 'AU_Template_Reading_ID' => $templateReadingId,
                                 'Start_Date' => $data['Start_date'],
                                 'Email_group_ID' => $data['Assigned_to']);
                             $task->InsertEquipmentTemplateReadingInEquipmentDetail($datas);
                             break;
                        }
                    }
                }  */ 
                $result = $task->getEquipmentDetailID($build_ID,$data['AU_Template_Designation_ID']);
                foreach($result as $val){
                    
                    $datas = array('AU_Equipment_Detail_ID' => $val->AU_Equipment_Detail_ID,
                        'AU_Template_Reading_ID' => $templateReadingId,
                        'Start_Date' => $data['Start_date'],
                        'Email_group_ID' => $data['Assigned_to']);
                    $task->InsertEquipmentTemplateReadingInEquipmentDetail($datas);                    
                    
                }               
                
                $msg['status'] = 'success';
                $msg['msg'] = 'Reading save sucessfully';
            }
            echo json_encode($msg);
            exit();
        }
        //die;
    }

    // edit task section 
    public function editequipmenttemplatereadingAction() {
        //$this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');
        //$data = $this->_request->getPost();
        $data = $this->getRequest()->getParams();
        $desig_id = $data['desig_id'];

        $subset = new Model_PmTemplate();
        $reading_id = $data['reading_id'];
        $ReadingData = $subset->GetEquipmentTemplatereadingDataById($reading_id);

        $allsubset = $subset->GetAllEquipmentTemplateSubset_reading($desig_id);
        $freq = array();
        $frequency = $subset->GetallEquipmentTemplatefrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }
        $startdatead = array();
        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdatead[$val->AU_sda_ID] = $val->Name;
        }

        $alljobtime = array();
        $jobtime = $subset->GetallEquipmentTemplatejobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
        }

        $allunitofmeasure = array();
        $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
        foreach ($unitofmeasure as $val) {
            $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
        }

        $this->view->frequency = $freq;
        $this->view->Interval = $Intreval;
        $this->view->startdateadjustment = $startdatead;
        $this->view->unitofmeasure = $allunitofmeasure;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
        $this->view->readingdata = $ReadingData[0];
    }

    /// Delete tasking 
    public function deleteequipmenttemplatereadingAction() {
        $msg = array();
        $typedata = array();
        $reading = new Model_PmTemplate();
        $param = $this->_request->getPost();
        //$parent_id = $task->getallparent($param['Task_id']);
        $result = $reading->deleteEquipmentTemplateReading($param['reading_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $update = array("Parent_ID" => "");
            $result = $reading->UpdateEquipmentTemplateReadingByparent($update, $param['reading_id']);
            if (!empty($result)) {
                $msg['status'] = 'success';
                $msg['msg'] = 'Template Deleted sucessfully';
            } else {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for Delete data';
            }
        }
        echo json_encode($msg);
        exit();
    }

    // update task data
    public function updateequipmenttemplatereadingAction() {
        $data = $this->_request->getPost();
        //print_r($data);       
        if (!empty($data)) {
            $task = new Model_PmTemplate();
            $reading_id = $data['reading_id'];
            unset($data['reading_id']);
            $result = $task->UpdateEquipmentTemplatereading($data, $reading_id);
            if (empty($result)) {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            } else {
                $msg['status'] = 'success';
                $msg['msg'] = 'Update Reading save sucessfully';
            }
            echo json_encode($msg);
            exit();
        }
    }

    public function updateequipmenttemplatereadingorderAction() {
        $data = $this->_request->getPost();
        $task = json_decode($data['reading']);
        $taskmodule = new Model_PmTemplate();
        $return = $this->validationEquipmentTemplatedragandrop($task);
        $order = 1;

        if ($return == 1) {
            foreach ($task as $val) {
                //print_r($val);
                //die;
                if (!empty($val->children) && $val->idSubset) {
                    $getdata = array("View_order" => $order);
                    $taskmodule->UpdateEquipmentTemplateodrderreading($getdata, $val->idSubset);
                    $order++;
                    foreach ($val->children as $data) {

                        if (!empty($data->idRoot)) {
                            //echo "idroot children";
                            $getdata = array("View_order" => $order, "Parent_ID" => $val->idSubset);
                            $taskmodule->UpdateEquipmentTemplateodrderreading($getdata, $data->idRoot);
                        }
                        if (!empty($data->id)) {
                            //echo "idroot children";
                            $getdata = array("View_order" => $order, "Parent_ID" => $val->idSubset);
                            $taskmodule->UpdateEquipmentTemplateodrderreading($getdata, $data->id);
                        }

                        $order++;
                    }
                } else {

                    if (!empty($val->idRoot)) {
                        //echo "root";
                        $getdata = array("View_order" => $order, "Parent_ID" => 0);
                        $taskmodule->UpdateEquipmentTemplateodrderreading($getdata, $val->idRoot);
                    }

                    if (!empty($val->id)) {
                        //echo "id";
                        $getdata = array("View_order" => $order, "Parent_ID" => 0);
                        $taskmodule->UpdateEquipmentTemplateodrderreading($getdata, $val->id);
                    }
                    $order++;
                }
            }
        }
        if ($return == 0) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error : This move not posible please try other';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Order save sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function editequipmenttemplatesubsetreadingAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $reading_id = $param['subset_id'];
        $desig_id = $param['desig_id'];
        $subset = new Model_PmTemplate();
        $subdata = $subset->GetEquipmentTemplatereadingDataById($reading_id);
        $subdata = $subdata[0];

        // Send data to view pages
        $this->view->subsetdata = $subdata;
        $this->view->desig_id = $desig_id;
    }

    public function validateequipmenttemplatesubsetreadingAction() {
        $data = $this->_request->getPost();
        $subsetname = $data['subsetname'];
        $subsetname_id = $data['subsetname_id'];
        $desig_id = $data['desig_id'];
        $subset = new Model_PmTemplate();
        $result = $subset->GetEquipmentTemplateSubsetreadingByName($subsetname, $subsetname_id, $desig_id);
        if (empty($result)) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit();
    }

    public function saveequipmenttemplatereadingsubsetAction() {
        $build_ID = $_SESSION['current_building'];
        $msg = array();
        $subsetdata = array();
        $subset = new Model_PmTemplate();
        $data = $this->_request->getPost();
        $insertdata = array();
        $insertdata['Reading_Instruction'] = $data['Reading_Instruction'];
        $insertdata['AU_Template_Designation_ID'] = $data['AU_Template_Designation_ID'];
        $templateReadingId = $subset->InsertEquipmentTemplateReading($insertdata);
        if (empty($templateReadingId)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            //Inser into equipment detail reading
            $result = $subset->getEquipmentDetailID($build_ID,$data['AU_Template_Designation_ID']);
                foreach($result as $val){
                    
                    $datas = array('AU_Equipment_Detail_ID' => $val->AU_Equipment_Detail_ID,
                        'AU_Template_Reading_ID' => $templateReadingId);
                    $subset->InsertEquipmentTemplateReadingInEquipmentDetail($datas);                    
                    
                }  
            $msg['status'] = 'success';
            $msg['msg'] = 'Subset save sucessfully';
            $msg["InsertId"] = $templateReadingId;
        }
        echo json_encode($msg);
        exit();
    }

    // subset data

    public function updateequipmenttemplatereadingsubsetAction() {
        $data = $this->_request->getPost();
        if (!empty($data)) {
            $task = new Model_PmTemplate();
            $task_id = $data['subsetname_id'];
            unset($data['subsetname_id']);
            $result = $task->UpdateEquipmentTemplateodrderreading($data, $task_id);
            if (empty($result)) {
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            } else {
                $msg['status'] = 'success';
                $msg['msg'] = 'Update subset save sucessfully';
            }
            echo json_encode($msg);
            exit();
        }
    }

    /// create subset level 
    public function createequipmenttemplatereadingsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $desig_id = $param['desig_id'];
        // send data in view pages
        $this->view->desig_id = $desig_id;
    }

    /* view add reading */

    public function viewequipmenttemplateaddreadingAction() {

        $this->_helper->getHelper('layout')->disableLayout();
        $param = $this->getRequest()->getParams();

        $desig_id = $param['desig_id'];
        $subset = new Model_PmTemplate();
        $allsubset = $subset->GetAllEquipmentTemplateSubset_reading($desig_id);

        $freq = array();
        $frequency = $subset->GetallEquipmentTemplatefrequency();
        foreach ($frequency as $val) {
            $freq[$val->id] = $val->name;
        }

        $startdateadj = array();
        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdateadj[$val->id] = $val->name;
        }

        $alljobtime = array();
        $jobtime = $subset->GetallEquipmentTemplatejobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->id] = $val->name;
        }

        $allunitofmeasure = array();
        $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
        foreach ($unitofmeasure as $val) {
            $allunitofmeasure[$val->id] = $val->name;
        }

        /* send data in view pages */
        $this->view->frequency = $freq;
        $this->view->startdateadjustment = $startdateadj;
        $this->view->jobtime = $alljobtime;
        $this->view->unitofmeasure = $allunitofmeasure;
        $this->view->subset = $allsubset;
        $this->view->desig_id = $desig_id;
    }

    /* View All Reading */

    public function viewequipmenttemplatereadingAction() {
        $this->_helper->getHelper('layout')->disableLayout();
        $param = $this->getRequest()->getParams();
        $desig_id = $param['desig_id'];
        $subset = new Model_PmTemplate();
        $data = $this->_request->getPost();
        if (!empty($desig_id)) {
            $allsubset = $subset->GetAllEquipmentTemplateSubset_reading($desig_id);

            $allreading = array();
            $view_empty_subset = array();
            $getallreading = $subset->GetAllEquipmentTemplateReadingParent($desig_id);
            //print_r($getallreading);
            //die;
            foreach ($getallreading as $sub) {
                $subreading = $subset->GetEquipmentTemplateReadingBysubsetId($sub->AU_Template_Reading_ID);
                if (!empty($subreading)) {
                    $allreading[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                    $allreading[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                    $allreading[$sub->AU_Template_Reading_ID]['task'] = $subreading;
                } else if (empty($sub->AU_Frequency_ID)) {
                    $view_empty_subset[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                    $view_empty_subset[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                    $view_empty_subset[$sub->AU_Template_Reading_ID]['task'] = "";
                } else {
                    $allreading[][] = $sub;
                }
            }
            $allreading = array_merge($allreading, $view_empty_subset);
            $freq = array();
            $frequency = $subset->GetallEquipmentTemplatefrequency();
            foreach ($frequency as $val) {
                if ($val->column == 1)
                    $freq[$val->AU_Frequency_ID] = $val->Name;
                if ($val->column == 2)
                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
            }

            $startdate_ad = array();
            $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
            foreach ($startdateadjustment as $val) {
                $startdate_ad[$val->AU_sda_ID] = $val->Name;
            }

            $alljobtime = array();
            $jobtime = $subset->GetallEquipmentTemplatejobtime();
            foreach ($jobtime as $val) {
                $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
            }

            $allunitofmeasure = array();
            $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
            foreach ($unitofmeasure as $val) {
                $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
            }

            $listview = explode(',', $param['viewlist']);

            /* send data in view pages */
            $this->view->frequency = $freq;
            $this->view->CustmeFreq = $Intreval;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;
            $this->view->unitofmeasure = $allunitofmeasure;
            $this->view->desig_id = $desig_id;
            $this->view->allreading = $allreading;
            $this->view->subset = $allsubset;
            $this->view->listview = $listview;
        }
    }

    // Group modification section start



    /* Task frequency popup section */

    public function taskequipmenttemplatefrequencyAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $desig_id = $param['desig_id'];
        $section = $param['sec'];
        $Intreval = array();
        $frequency = $subset->GetallEquipmentTemplatefrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }

        $this->view->desig_id = $desig_id;
        $this->view->sec = $section;
        $this->view->CustFreq = $Intreval;
        $this->view->Freq = $freq;
    }

    //// Task Frequency subset
    public function taskequipmenttemplatefrequencysubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $desig_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $Intreval = array();
        $frequency = $subset->GetallEquipmentTemplatefrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }
        $this->view->desig_id = $desig_id;
        $this->view->parent_id = $parent_id;
        $this->view->CustFreq = $Intreval;
        $this->view->Freq = $freq;
    }

    /// Root frequency 
    public function updateequipmenttemplatetaskrootfrequeancyAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $Frequency_ID = $data['Frequency_ID'];
        $desig_id = $data['desig_id'];
        $updata['Interval_Value'] = $data['Interval_Value'];
        $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
        $inclidesubset = $data['includesubset'];
        $result = $task->updateEquipmentTemplate_grouptask($updata, $desig_id, $inclidesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Frequency Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* subset frequency */

    public function updateequipmenttemplatetaskfrequeancysubsetAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Interval_Value'] = $data['Interval_Value'];
        $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
        $result = $task->updateEquipmentTemplate_grouptasksubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Frequecy Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Start date section start */

    public function taskequipmenttemplatestartdateAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $this->view->desig_id = $templete_id;
        $this->view->sec = $section;
    }

    public function taskequipmenttemplatestartdatesubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* Root start date */

    public function updateequipmenttemplatetaskrootstartdateAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['Start_date'] = $data['startdate'];
        $result = $task->updateEquipmentTemplate_grouptask($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* update data  subset start date */

    public function updateequipmenttemplatetasksubsetstartdateAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Start_date'] = $data['startdate'];
        $result = $task->updateEquipmentTemplate_grouptasksubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*  Task start date of month secton start */

    /* popup section start */

    public function taskequipmenttemplatestartdateofmonthAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $this->view->desig_id = $templete_id;
    }

    public function taskequipmenttemplatestartdateofmonthsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Update tast root start date of month */

    public function updateequipmenttemplatetaskrootstartdateofmonthAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata = array();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $date = $data['startdateofmonth'];
        //$alltask  = $task->Getalltaskbygroupmodification($desig_id,$includesubset);

        $updata['Startdate_month'] = $data['startdateofmonth'];
        $result = $task->updateEquipmentTemplate_grouptask($updata, $desig_id, $includesubset);

        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date of Month sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Update tast subset start date of month */

    public function updateequipmenttemplatetasksubsetstartdateofmonthAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata = array();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $date = $data['startdateofmonth'];
        $updata['startdate_month'] = $data['startdateofmonth'];

        $result = $task->updateEquipmentTemplate_grouptasksubset($updata, $desig_id, $parent_id);


        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date of month Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Start date Adjustment started */

    /* popup section start */

    public function taskequipmenttemplatestartdateadjustmentAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $subset = new Model_PmTemplate();
        $startdate_ad = array();
        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdate_ad[$val->AU_sda_ID] = $val->Name;
        }
        $this->view->startdateofad = $startdate_ad;
        $this->view->desig_id = $templete_id;
    }

    public function taskequipmenttemplatestartdateadjustmentsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $subset = new Model_PmTemplate();
        $startdate_ad = array();
        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdate_ad[$val->AU_sda_ID] = $val->Name;
        }
        $this->view->startdateofad = $startdate_ad;
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Root start date adjustment section */

    public function updateequipmenttemplatetaskrootstartdateadjustmentAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata = array();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['AU_sda_ID'] = $data['startdateadjustment'];
        $result = $task->updateEquipmentTemplate_grouptask($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* update data  subset start date */

    public function updateequipmenttemplatetasksubsetstartdateadjustmentAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata = array();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['AU_sda_ID'] = $data['startdateadjustment'];
        $result = $task->updateEquipmentTemplate_grouptasksubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Task  group modification stop */

    /* Reading group modification Start */

    /* popup section start */

    public function readingequipmenttemplatefrequencyAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $subset = new Model_PmTemplate();

        $frequency = $subset->GetallEquipmentTemplatefrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }

        $this->view->desig_id = $desig_id;
        //$this->view->sec = $section;
        $this->view->CustFreq = $Intreval;
        $this->view->Freq = $freq;
        $this->view->desig_id = $templete_id;
    }

    public function readingequipmenttemplatefrequencysubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $desig_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $frequency = $subset->GetallEquipmentTemplatefrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }

        $this->view->desig_id = $desig_id;
        //$this->view->sec = $section;
        $this->view->Freq = $freq;
        $this->view->CustFreq = $Intreval;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Update reading frequency group modification */

    public function updateequipmenttemplatereadingrootfrequeancyAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata = array();
        $inclidesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['Interval_Value'] = $data['Interval_Value'];
        $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
        $result = $task->updateEquipmentTemplate_groupreading($updata, $desig_id, $inclidesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Frequency Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* subset frequency */

    public function updateequipmenttemplatereadingfrequeancysubsetAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Interval_Value'] = $data['Interval_Value'];
        $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
        $result = $task->updateEquipmentTemplate_groupreadingsubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Frequecy Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Reading  Start date section start */

    /* popup section start */

    public function readingequipmenttemplatestartdateAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $this->view->desig_id = $templete_id;
        $this->view->sec = $section;
    }

    public function readingequipmenttemplatestartdatesubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section END */

    /*
     *  Update Reading start date Group modication 
     */

    public function updateequipmenttemplatereadingrootstartdateAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['Start_date'] = $data['startdate'];
        $result = $task->updateEquipmentTemplate_groupreading($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*
     *  Update Reading start date subset Group modication 
     */

    public function updateequipmenttemplatereadingsubsetstartdateAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Start_date'] = $data['startdate'];
        $result = $task->updateEquipmentTemplate_groupreadingsubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*  Reading start date of month secton start */

    /* popup section start */

    public function readingequipmenttemplatestartdateofmonthAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $this->view->desig_id = $templete_id;
    }

    public function readingequipmenttemplatestartdateofmonthsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Update tast root start date of month */

    public function updateequipmenttemplatereadingrootstartdateofmonthAction() {
        $data = $this->_request->getPost();

        $reading = new Model_PmTemplate();
        $updata = array();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $date = $data['startdateofmonth'];
        //$alltask  = $task->Getalltaskbygroupmodification($desig_id,$includesubset);

        $updata['Startdate_month'] = $data['startdateofmonth'];
        $result = $reading->updateEquipmentTemplate_groupreading($updata, $desig_id, $includesubset);



        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date of Month sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* Update tast subset start date of month */

    public function updateequipmenttemplatereadingsubsetstartdateofmonthAction() {
        $data = $this->_request->getPost();
        $reading = new Model_PmTemplate();
        $updata = array();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Startdate_month'] = $data['startdateofmonth'];

        $result = $reading->updateEquipmentTemplate_groupreadingsubset($updata, $desig_id, $parent_id);

        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date of month Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*  Reading Start date Adjustment started */

    /* popup section start */

    public function readingequipmenttemplatestartdateadjustmentAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $subset = new Model_PmTemplate();
        $startdate_ad = array();
        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdate_ad[$val->AU_sda_ID] = $val->Name;
        }
        $this->view->startdateofad = $startdate_ad;
        $this->view->desig_id = $templete_id;
    }

    public function readingequipmenttemplatestartdateadjustmentsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $subset = new Model_PmTemplate();
        $startdate_ad = array();
        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdate_ad[$val->AU_sda_ID] = $val->Name;
        }
        $this->view->startdateofad = $startdate_ad;
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Root start date adjustment section */

    public function updateequipmenttemplatereadingrootstartdateadjustmentAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['AU_sda_ID'] = $data['startdateadjustment'];
        $result = $task->updateEquipmentTemplate_groupreading($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* update data  subset start date */

    public function updateequipmenttemplatereadingsubsetstartdateadjustmentAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['AU_sda_ID'] = $data['startdateadjustment'];
        $result = $task->updateEquipmentTemplate_groupreadingsubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* End Reading start date Adjustment section */

    /* Start Reading value section */

    /* popup section start */

    public function readingequipmenttemplatevalueAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $this->view->desig_id = $templete_id;
    }

    public function readingequipmenttemplatevaluesubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Root start date adjustment section */

    public function updateequipmenttemplatereadingrootreadingvalueAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['Reading_Value'] = $data['reading_value'];
        $result = $task->updateEquipmentTemplate_groupreading($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Reading Value On Root Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* update data  subset start date */

    public function updateequipmenttemplatereadingsubsetreadingvalueAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Reading_Value'] = $data['readingvalue'];
        $result = $task->updateEquipmentTemplate_groupreadingsubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Reading Value On Subset Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* End Reading value section */

    /* Start Unit Of Measure section */

    /* popup section start */

    public function readingequipmenttemplateunitofmeasureAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];
        $allunitofmeasure = array();
        $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
        foreach ($unitofmeasure as $val) {
            $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
        }

        /* Send data to View pages */
        $this->view->desig_id = $templete_id;
        $this->view->unitofmeasure = $allunitofmeasure;
    }

    public function readingequipmenttemplateunitofmeasuresubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];
        $allunitofmeasure = array();
        $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
        foreach ($unitofmeasure as $val) {
            $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
        }

        /* Send data to View pages */
        $this->view->desig_id = $templete_id;
        $this->view->unitofmeasure = $allunitofmeasure;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Root start date adjustment section */

    public function updateequipmenttemplatereadingrootunitofmeasureAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['AU_uom_ID'] = $data['unitofmeasure'];
        $result = $task->updateEquipmentTemplate_groupreading($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Reading Value On Root Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* update data  subset start date */

    public function updateequipmenttemplatereadingsubsetunitofmeasureAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['AU_uom_ID'] = $data['unitofmeasure'];
        $result = $task->updateEquipmentTemplate_groupreadingsubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Reading Value On Subset Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* End Unit Of Measure section */

    /* Start Unit Of Measure section */

    /* popup section start */

    public function readingequipmenttemplatetoleranceAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $templete_id = $param['desig_id'];
        $section = $param['sec'];

        /* Send data to View pages */
        $this->view->desig_id = $templete_id;
    }

    public function readingequipmenttemplatetolerancesubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $templete_id = $param['desig_id'];
        $parent_id = $param['parent_id'];

        /* Send data to View pages */
        $this->view->desig_id = $templete_id;
        $this->view->parent_id = $parent_id;
    }

    /* popup section End */

    /* Root start date adjustment section */

    public function updateequipmenttemplatereadingroottoleranceAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['Tolerance'] = $data['tolerance'];
        $result = $task->updateEquipmentTemplate_groupreading($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Reading Value On Root Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* update data  subset start date */

    public function updateequipmenttemplatereadingsubsettoleranceAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Tolerance'] = $data['tolerance'];
        $result = $task->updateEquipmentTemplate_groupreadingsubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Reading Value On Subset Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /* End Unit Of Measure section */


    /* End Group Modification section */

    /* Import section */

    public function importequipmenttemplateAction() {
        $this->_helper->layout()->disableLayout();
        //$this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getPost();
        $desig_id = $param['desig_id'];
        $template = new Model_PmTemplate();
        $resultDesign = $template->get_allEquipmentTemplate_typedesignation($desig_id);

        /* send data to view page */
        $this->view->designation = $resultDesign;
        $this->view->desig_id = $desig_id;
    }

    public function importequipmenttemplatereadingAction() {
        $this->_helper->layout()->disableLayout();
        //$this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $desig_id = $param['desig_id'];
        $template = new Model_PmTemplate();
        $resultDesign = $template->get_allEquipmentTemplate_typedesignation($desig_id);

        /* send data to view page */
        $this->view->designation = $resultDesign;
        $this->view->desig_id = $desig_id;
    }

    public function importequipmenttemplateapplyAction() {
        $data = $this->_request->getPost();
        $desig_id = $data['desig_id'];
        $import_id = $data['import_id'];
        $template = new Model_PmTemplate();
        $task_subset = $template->getEquipmentTemplate_subsetbyid('pm_au_template_task', $import_id);
        //print_r($task_subset);
        //die;
        /* task section */
        foreach ($task_subset as $tsubset) {
            $innertask = $template->getEquipmentTemplate_taskbysubsetId('pm_au_template_task', $import_id, $tsubset->AU_Template_Task_ID);
            $data = (array) $tsubset;
            unset($data['AU_Template_Task_ID']);
            $data['AU_Template_Designation_ID'] = $desig_id;
            //$$data
            //print_r($data);
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['User_id'] = $user_id;
            $parent_id = $template->insertEquipmentTemplatesubset($data);

            if (!empty($innertask)) {
                foreach ($innertask as $task) {
                    $innerdata = (array) $task;
                    unset($innerdata['AU_Template_Task_ID']);
                    $innerdata['Parent_ID'] = $parent_id;
                    $innerdata['AU_Template_Designation_ID'] = $desig_id;
                    $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                    $innerdata['User_id'] = $user_id;
                    $template->InsertEquipmentTemplatetask($innerdata);
                    //print_r($innerdata);
                }
            }
        }

        $task_data = $template->get_tabledata_Au('pm_au_template_task', $import_id);
        foreach ($task_data as $tdata) {
            $data = (array) $tdata;
            unset($data['AU_Template_Task_ID']);
            $data['AU_Template_Designation_ID'] = $desig_id;
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['User_id'] = $user_id;
            $template->InsertEquipmentTemplatetask($data);
        }

        /* Reading section start */
        $reading_subset = $template->getEquipmentTemplate_subsetbyid('pm_au_template_reading', $import_id);
        foreach ($reading_subset as $tsubset) {
            $innertask = $template->getEquipmentTemplate_taskbysubsetId('pm_au_template_reading', $import_id, $tsubset->AU_Template_Reading_ID);
            $data = (array) $tsubset;
            unset($data['AU_Template_Reading_ID']);
            $data['AU_Template_Designation_ID'] = $desig_id;
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['User_id'] = $user_id;
            $parent_id = $template->InsertEquipmentTemplateReadingsubset($data);

            if (!empty($innertask)) {

                foreach ($innertask as $task) {
                    $innerdata = (array) $task;
                    unset($innerdata[id]);
                    $innerdata['Parent_ID'] = $parent_id;
                    $innerdata['AU_Template_Designation_ID'] = $desig_id;
                    $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                    $innerdata['User_id'] = $user_id;
                    unset($innerdata['AU_Template_Reading_ID']);
                    $template->InsertEquipmentTemplateReading($innerdata);
                }
            }
        }
        $task_data = $template->getEquipmentTemplate_tabledata('pm_au_template_reading', $import_id);
        foreach ($task_data as $tdata) {
            $data = (array) $tdata;
            unset($data['AU_Template_Reading_ID']);
            $data['AU_Template_Designation_ID'] = $desig_id;
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['User_id'] = $user_id;
            $result = $template->InsertEquipmentTemplateReading($data);
        }
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Import All The Data Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*  End import section */

    function equipmenttemplatecustometimejobAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $freq = explode("-", $param['timejob']);

        $this->view->customedata = $freq;
        $subset = new Model_PmTemplate();
        $alljobtime = array();
        $jobtime = $subset->GetallEquipmentTemplatejobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->id] = $val->name;
        }
        $this->view->timejob = $alljobtime;
    }

    function insertequipmenttemplatefrequencyAction() {
        $data = $this->_request->getPost();
        $InsertData = array(
            "Name" => $data['name'],
            "value" => $data['value'],
            "type" => $data['type'],
        );
        $subset = new Model_PmTemplate();
        $last_id = $subset->InsertEquipmentTemplateFrequencyData($InsertData);
        $return = array("id" => $last_id);
        echo json_encode($return);
        die;
    }

    public function getequipmenttemplatesubsetAction() {
        $data = $this->_request->getPost();
        $desig_id = $data['desig_id'];
        $subset = new Model_PmTemplate();
        $allsubset = $subset->GetAllEquipmentTemplateSubset($desig_id);
        echo json_encode($allsubset);
        die;
    }

    public function getequipmenttemplatesubsetreadingAction() {
        $data = $this->_request->getPost();
        $desig_id = $data['desig_id'];
        $subset = new Model_PmTemplate();
        $allsubset = $subset->GetAllEquipmentTemplateSubsetReading($desig_id);
        echo json_encode($allsubset);
        die;
    }

    public function importtemplateAction() {

        $user_id = $_SESSION['Zend_Auth']['storage']->uid;

        $this->_helper->layout()->setLayout('popuplayout');
        $build_ID = $_SESSION['current_building'];
        //die;
        $template = new Model_PmTemplate();
        $templatedata = array();
        $design = $template->GetallAUTemplateDetails($build_ID);
        $usertemplate = $template->GetallAUTemplateDetailsByUserId($build_ID, $user_id);


        $design1 = $template->GetallVTTemplateDetails();
        $allTemplate = array_merge($design, $design1);

        $AllTemplateList = array();
        $i = 1;
        /* User section */
        foreach ($design as $data) {
            $AllTemplateList[$i]['TemplateName'] = $data->AU_Template_Name;
            $AllTemplateList[$i]['desig_id'] = $data->AU_Template_Designation_ID;
            $AllTemplateList[$i]['TypeDesignation'] = $data->AU_TypeDesignation;
            $AllTemplateList[$i]['TypeDescritpion'] = $data->AU_TypeDescritpion;
            $AllTemplateList[$i]['admin_template'] = "";
            $i++;
        }
        /* VT Admin section */
        foreach ($design1 as $data) {
            $AllTemplateList[$i]['TemplateName'] = $data->VT_Template_Name;
            $AllTemplateList[$i]['desig_id'] = $data->VT_Template_Designation_ID;
            $AllTemplateList[$i]['TypeDesignation'] = $data->VT_TypeDesignation;
            $AllTemplateList[$i]['TypeDescritpion'] = $data->VT_TypeDescritpion;
            $AllTemplateList[$i]['admin_template'] = $data->Vt_Admin_Template;
            $i++;
        }
        $this->view->alltemplates = $AllTemplateList;
        $this->view->usertemplate = $usertemplate;
    }

    public function importdesiganitiontemplateAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $data = $this->_request->getPost();
        $desig_id = $data['design_id'];
        $type = $data['type'];
        $subset = new Model_PmTemplate();

        // task Section
        if ($type == "AU") {
            $alltask = array();
            $view_empty_subset = array();
            $getalltask = $subset->GetAllEquipmentTemplatetaskparent($desig_id);
            foreach ($getalltask as $sub) {
                $subtask = $subset->GetallTaskBysubsetId_Import($sub->AU_Template_Task_ID);
                //print_r($sub);
                if (!empty($subtask)) {
                    $alltask[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                    $alltask[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                    $alltask[$sub->AU_Template_Task_ID]['startdate'] = $sub->AU_Template_Task_ID;
                    $alltask[$sub->AU_Template_Task_ID]['assignto'] = $sub->AU_Template_Task_ID;
                    $alltask[$sub->AU_Template_Task_ID]['task'] = $subtask;
                    //array("Task_Instruction"=>$subtask->Task_Instruction,"View_order"=>$subtask->View_order);
                } else if (empty($sub->AU_Frequency_ID)) {
                    $view_empty_subset[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                    $view_empty_subset[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                    $view_empty_subset[$sub->AU_Template_Task_ID]['task'] = "";
                } else {
                    $alltask[][] = $sub;
                    //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
                }
            }
           /* $getalltask = $subset->GetAllEquipmenttaskparentsubset($desig_id); 
        foreach ($getalltask as $sub) {
        if (!empty($sub->Parent_ID)) {
            $array = array('AU_Template_Task_ID'=>$sub->AU_Template_Task_ID,'AU_Template_Designation_ID'=>$sub->AU_Template_Designation_ID,'Task_Instruction'=>$sub->Task_Instruction,'AU_Frequency_ID'=>$sub->AU_Frequency_ID,'Start_date'=>$sub->Start_date,'Assigned_to'=>$sub->Assigned_to,'Parent_ID'=>$sub->Parent_ID,'group_name'=>$sub->group_name);
            $taskvalue = (object)$array;            
                $alltask[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                $alltask[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                $alltask[$sub->AU_Template_Task_ID]['startdate'] = $sub->AU_Template_Task_ID;
                $alltask[$sub->AU_Template_Task_ID]['assignto'] = $sub->AU_Template_Task_ID;
                $alltask[$sub->AU_Template_Task_ID]['task'] = array($taskvalue);
            } else if (empty($sub->AU_Frequency_ID)) {
                $view_empty_subset[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                $view_empty_subset[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                $view_empty_subset[$sub->AU_Template_Task_ID]['task'] = "";
            } else {
                $alltask[][] = $sub;
                //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
            }
        }*/
            $alltask = array_merge($alltask, $view_empty_subset);
            
        } else {

            $alltask = array();
            $view_empty_subset = array();
            $getalltask = $subset->GetAlltaskparentImport($desig_id);
            //print_r($getalltask);
            foreach ($getalltask as $sub) {
                $subtask = $subset->GetallTaskBysubsetIdImport($sub->VT_Template_Task_ID);
                //print_r($sub);
                if (!empty($subtask)) {
                    $alltask[$sub->VT_Template_Task_ID]['name'] = $sub->Task_Instruction;
                    $alltask[$sub->VT_Template_Task_ID]['id'] = $sub->VT_Template_Task_ID;
                    $alltask[$sub->VT_Template_Task_ID]['task'] = $subtask;
                } else if (empty($sub->AU_Frequency_ID)) {
                    $view_empty_subset[$sub->VT_Template_Task_ID]['name'] = $sub->Task_Instruction;
                    $view_empty_subset[$sub->VT_Template_Task_ID]['id'] = $sub->VT_Template_Task_ID;
                    $view_empty_subset[$sub->VT_Template_Task_ID]['task'] = "";
                } else {
                    $alltask[][] = $sub;
                    //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
                }
            }
            $alltask = array_merge($alltask, $view_empty_subset);
            
        }

        // Reading Section Start
        if ($type == "AU") {
            $allreading = array();
            $view_empty_subset = array();
            $getalltask = $subset->GetAllEquipmentTemplateReadingParent($desig_id);
            //print_r($getalltask);
            foreach ($getalltask as $sub) {
                //echo $sub->AU_Template_Reading_ID;
                $subtask = $subset->GetEquipmentTemplateReadingBysubsetId_import($sub->AU_Template_Reading_ID);
                if (!empty($subtask)) {
                    $allreading[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                    $allreading[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                    //$allreading[$sub->AU_Template_Reading_ID]['startdate'] = $sub->AU_Template_Reading_ID;
                    //$allreading[$sub->AU_Template_Reading_ID]['assignto'] = $sub->AU_Template_Reading_ID;
                    $allreading[$sub->AU_Template_Reading_ID]['task'] = $subtask; //array("Reading_Instruction"=>$subtask->Reading_Instruction,"View_order"=>$subtask->View_order);
                } else if (empty($sub->AU_Frequency_ID)) {
                    $view_empty_subset[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                    $view_empty_subset[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                    $view_empty_subset[$sub->AU_Template_Reading_ID]['task'] = "";
                } else {
                    $allreading[][] = $sub;
                    //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
                }
            }
            /*$getalltask = $subset->GetAllEquipmentTemplateReadingParentsubset($desig_id);
            foreach ($getalltask as $sub) {
        if (!empty($sub->Parent_ID)) {
            $array = array('AU_Template_Reading_ID'=>$sub->AU_Template_Reading_ID,'AU_Template_Designation_ID'=>$sub->AU_Template_Designation_ID,'Reading_Instruction'=>$sub->Reading_Instruction,'AU_Frequency_ID'=>$sub->AU_Frequency_ID,'Start_date'=>$sub->Start_date,'Assigned_to'=>$sub->Assigned_to,'Parent_ID'=>$sub->Parent_ID,'group_name'=>$sub->group_name);
            $taskvalue = (object)$array;            
                $allreading[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                $allreading[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                $allreading[$sub->AU_Template_Reading_ID]['startdate'] = $sub->AU_Template_Reading_ID;
                $allreading[$sub->AU_Template_Reading_ID]['assignto'] = $sub->AU_Template_Reading_ID;
                $allreading[$sub->AU_Template_Reading_ID]['task'] = array($taskvalue);
            } else if (empty($sub->AU_Frequency_ID)) {
                $view_empty_subset[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                $view_empty_subset[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                $view_empty_subset[$sub->AU_Template_Reading_ID]['task'] = "";
            } else {
                $allreading[][] = $sub;
                //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
            }
        } */
            $allreading = array_merge($allreading, $view_empty_subset);
            
        } else {

            $allreading = array();
            $view_empty_subset = array();
            $getalltask = $subset->GetAllreadingparentImport($desig_id);
            //print_r($getalltask);

            foreach ($getalltask as $sub) {
                $subtask = $subset->GetallReadingBysubsetIdImport($sub->VT_Template_Reading_ID);

                if (!empty($subtask)) {
                    $allreading[$sub->VT_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                    $allreading[$sub->VT_Template_Reading_ID]['id'] = $sub->VT_Template_Reading_ID;
                    $allreading[$sub->VT_Template_Reading_ID]['task'] = $subtask;
                } else if (empty($sub->AU_Frequency_ID)) {
                    $view_empty_subset[$sub->VT_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                    $view_empty_subset[$sub->VT_Template_Reading_ID]['id'] = $sub->VT_Template_Reading_ID;
                    $view_empty_subset[$sub->VT_Template_Reading_ID]['task'] = "";
                } else {
                    $allreading[][] = $sub;
                    //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
                }
            }
            $allreading = array_merge($allreading, $view_empty_subset);
        }
            
        /* send data to view page */
        $this->view->taskdata = $alltask;
        $this->view->readingdata = $allreading;
        
    }

    public function importdesiganitiontemplatechangeAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $data = $this->_request->getPost();
        $desig_id = $data['design_id'];
        $type = $data['type'];
        $subset = new Model_PmTemplate();
        $alltask = array();
        $view_empty_subset = array();
        //$getalltask = $subset->GetAllEquipmentTemplatetaskparent($desig_id);
        $getalltask = $subset->GetAllEquipmenttaskparent($desig_id);

        foreach ($getalltask as $sub) {
            //$subtask = $subset->GetallTaskBysubsetId_Import($sub->AU_Template_Task_ID);
            $subtask = $subset->GetallTaskBysubsetId_Import_change($sub->AU_Template_Task_ID);

            if (!empty($subtask)) {
                $alltask[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                $alltask[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                $alltask[$sub->AU_Template_Task_ID]['Start_date'] = $sub->AU_Template_Task_ID;
                $alltask[$sub->AU_Template_Task_ID]['Assigned_to'] = $sub->AU_Template_Task_ID;
                $alltask[$sub->AU_Template_Task_ID]['task'] = $subtask; //array("Task_Instruction"=>$subtask->Task_Instruction,"View_order"=>$subtask->View_order);
            } else if (empty($sub->AU_Frequency_ID)) {
                $view_empty_subset[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                $view_empty_subset[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                $view_empty_subset[$sub->AU_Template_Task_ID]['task'] = "";
            } else {
                $alltask[][] = $sub;
                //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
            }
        }
        /*$getalltask = $subset->GetAllEquipmenttaskparentsubset($desig_id); 
        foreach ($getalltask as $sub) {
        if (!empty($sub->Parent_ID)) {
            $array = array('AU_Template_Task_ID'=>$sub->AU_Template_Task_ID,'AU_Template_Designation_ID'=>$sub->AU_Template_Designation_ID,'Task_Instruction'=>$sub->Task_Instruction,'AU_Frequency_ID'=>$sub->AU_Frequency_ID,'Start_date'=>$sub->Start_date,'Assigned_to'=>$sub->Assigned_to,'Parent_ID'=>$sub->Parent_ID,'group_name'=>$sub->group_name);
            $taskvalue = (object)$array;            
                $alltask[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                $alltask[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                $alltask[$sub->AU_Template_Task_ID]['Start_date'] = $sub->AU_Template_Task_ID;
                $alltask[$sub->AU_Template_Task_ID]['Assigned_to'] = $sub->AU_Template_Task_ID;
                $alltask[$sub->AU_Template_Task_ID]['task'] = array($taskvalue);
            } else if (empty($sub->AU_Frequency_ID)) {
                $view_empty_subset[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                $view_empty_subset[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                $view_empty_subset[$sub->AU_Template_Task_ID]['task'] = "";
            } else {
                $alltask[][] = $sub;
                //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
            }
        } */       
        
        $alltask = array_merge($alltask, $view_empty_subset);
                        
        $allreading = array();
        $view_empty_subset = array();
        //$getalltask = $subset->GetAllEquipmentTemplateReadingParent($desig_id);
        $getalltask = $subset->GetAllEquipmentReadingParent($desig_id);
        foreach ($getalltask as $sub) {
            //$subtask = $subset->GetEquipmentTemplateReadingBysubsetId_import($sub->AU_Template_Reading_ID);
            $subtask = $subset->GetEquipmentTemplateReadingBysubsetId_import_change($sub->AU_Template_Reading_ID);
            if (!empty($subtask)) {
                $allreading[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                $allreading[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                $allreading[$sub->AU_Template_Reading_ID]['task'] = $subtask; //array("Reading_Instruction"=>$subtask->Reading_Instruction,"View_order"=>$subtask->View_order);
            } else if (empty($sub->AU_Frequency_ID)) {
                $view_empty_subset[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                $view_empty_subset[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                $view_empty_subset[$sub->AU_Template_Reading_ID]['task'] = "";
            } else {
                $allreading[][] = $sub;
            }
        }
        /*$getalltask = $subset->GetAllEquipmentReadingParentsubset($desig_id);
            foreach ($getalltask as $sub) {
        if (!empty($sub->Parent_ID)) {
            $array = array('AU_Template_Reading_ID'=>$sub->AU_Template_Reading_ID,'AU_Template_Designation_ID'=>$sub->AU_Template_Designation_ID,'Reading_Instruction'=>$sub->Reading_Instruction,'AU_Frequency_ID'=>$sub->AU_Frequency_ID,'Start_date'=>$sub->Start_date,'Assigned_to'=>$sub->Assigned_to,'Parent_ID'=>$sub->Parent_ID,'group_name'=>$sub->group_name);
            $taskvalue = (object)$array;            
                $allreading[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                $allreading[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                $allreading[$sub->AU_Template_Reading_ID]['task'] = array($taskvalue);
            } else if (empty($sub->AU_Frequency_ID)) {
                $view_empty_subset[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                $view_empty_subset[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                $view_empty_subset[$sub->AU_Template_Reading_ID]['task'] = "";
            } else {
                $allreading[][] = $sub;
                //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
            }
        }*/
        $allreading = array_merge($allreading, $view_empty_subset);
        /* send data to view page */
        $this->view->taskdata = $alltask;
        $this->view->readingdata = $allreading;
        $this->view->desig_id = $desig_id;
    }

    public function importtemplatebytypeAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $build_ID = $_SESSION['current_building'];
        $data = $this->_request->getPost();
        $type = $data['type'];

        $template = new Model_PmTemplate();
        $templatedata = array();
        $build_ID = $_SESSION['current_building'];
        $design = $template->GetallAUTemplateDetails($build_ID);
        $design1 = $template->GetallVTTemplateDetails();
        $AllTemplateList = array();
        $i = 1;
        if ($type == 'all') {
            /* VT Admin section */
            foreach ($design1 as $data) {
                $AllTemplateList[$i]['TemplateName'] = $data->VT_Template_Name;
                $AllTemplateList[$i]['desig_id'] = $data->VT_Template_Designation_ID;
                $AllTemplateList[$i]['TypeDesignation'] = $data->VT_TypeDesignation;
                $AllTemplateList[$i]['TypeDescritpion'] = $data->VT_TypeDescritpion;
                $AllTemplateList[$i]['admin_template'] = $data->VT_Admin_Template;
                $i++;
            }
        } else if ($type == 'vtonly') {

            /* VT Admin section */
            foreach ($design1 as $data) {
                if ($data->VT_Admin_Template == 'Yes') {
                    $AllTemplateList[$i]['TemplateName'] = $data->VT_Template_Name;
                    $AllTemplateList[$i]['desig_id'] = $data->VT_Template_Designation_ID;
                    $AllTemplateList[$i]['TypeDesignation'] = $data->VT_TypeDesignation;
                    $AllTemplateList[$i]['TypeDescritpion'] = $data->VT_TypeDescritpion;
                    $AllTemplateList[$i]['admin_template'] = $data->VT_Admin_Template;
                    $i++;
                }
            }
        } else {


            /* User section */
            foreach ($design1 as $data) {
                if ($data->VT_Admin_Template == 'No') {
                    $AllTemplateList[$i]['TemplateName'] = $data->VT_Template_Name;
                    $AllTemplateList[$i]['desig_id'] = $data->VT_Template_Designation_ID;
                    $AllTemplateList[$i]['TypeDesignation'] = $data->VT_TypeDesignation;
                    $AllTemplateList[$i]['TypeDescritpion'] = $data->VT_TypeDescritpion;
                    $AllTemplateList[$i]['admin_template'] = 'No';
                    $i++;
                }
            }
        }

        echo json_encode($AllTemplateList);
        die;
    }

    public function importtemplatedatainuserAction() {
        $data = $this->_request->getPost();
        $action = $data['action'];
        $design_id = $data['design_id'];
        $type = $data['type'];
        $tempoption = $data['tempoption'];
        $template_name = $data['template_name'];
        if (!empty($data['imp_design_id'])) {
            $imp_design_id = $data['imp_design_id'];
        }
        $templatedata = array();
        $typedata = array();
        $template = new Model_PmTemplate();
        if (trim($type) == 'VT') {
            $task = $template->GetTemplateandDesighationDetails_import($design_id);
            $task = $task[0];
            if ($tempoption == 1) {
                $templatedata['AU_Template_Name'] = $task->VT_Template_Name;
                $templatedata['BuildingID'] = $_SESSION['current_building'];
                $templatedata['user_id'] = $_SESSION['Zend_Auth']['storage']->uid;
                $template_id = $template->InsertEquipmentTemplateName($templatedata);
                $typedata['AU_Template_Name_ID'] = $template_id;
                $typedata['AU_TypeDesignation'] = $task->VT_TypeDesignation;
                if ($action == 0) {
                    $typedata['AU_TypeDesignation'] = $task->VT_TypeDesignation . "-" . rand(10, 99999);
                }

                $typedata['AU_TypeDescritpion'] = $task->VT_TypeDescritpion;
                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                $typedata['user_id'] = $user_id;
                $newdesign_id = $template->InsertEquipmentTemplateTypeDesignation($typedata);
            } else if ($tempoption == 2) {
                $typedata['AU_Template_Name_ID'] = $imp_design_id;
                $typedata['AU_TypeDesignation'] = $task->VT_TypeDesignation;
                $typedata['AU_TypeDescritpion'] = $task->VT_TypeDescritpion;
                if ($action == 0) {
                    $typedata['AU_TypeDesignation'] = $task->VT_TypeDesignation . "-" . rand(10, 99999);
                }
                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                $typedata['user_id'] = $user_id;
                $newdesign_id = $template->InsertEquipmentTemplateTypeDesignation($typedata);
                //$newdesign_id = $imp_design_id;
            } else {
                $templatedata['AU_Template_Name'] = $template_name;
                $templatedata['BuildingID'] = $_SESSION['current_building'];
                $templatedata['user_id'] = $_SESSION['Zend_Auth']['storage']->uid;
                $template_id = $template->InsertEquipmentTemplateName($templatedata);
                $typedata['AU_Template_Name_ID'] = $template_id;
                $typedata['AU_TypeDesignation'] = $task->VT_TypeDesignation;
                if ($action == 0) {
                    $typedata['AU_TypeDesignation'] = $task->VT_TypeDesignation . "-" . rand(10, 99999);
                }
                $typedata['AU_TypeDescritpion'] = $task->VT_TypeDescritpion;
                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                $typedata['user_id'] = $user_id;
                $newdesign_id = $template->InsertEquipmentTemplateTypeDesignation($typedata);
            }

            /* Task Section  */
            $task_subset = $template->getEquipmentTemplate_subsetbyid_import('pm_vt_template_task', $design_id);
            foreach ($task_subset as $tsubset) {
                $innertask = $template->getEquipmentTemplate_taskbysubsetId_import('pm_vt_template_task', $design_id, $tsubset->VT_Template_Task_ID);
                $data = (array) $tsubset;
                unset($data['VT_Template_Task_ID']);
                $data['AU_Template_Designation_ID'] = $newdesign_id;
                unset($data['VT_Template_Designation_ID']);
                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                $data['User_ID'] = $user_id;
                $parent_id = $template->insertEquipmentTemplatesubset($data);
                if (!empty($innertask)) {
                    foreach ($innertask as $task) {
                        $innerdata = (array) $task;
                        unset($innerdata['VT_Template_Task_ID']);
                        $taskdate = strtotime($innerdata['Startdate_month'] . " " . $innerdata['Start_date']);
                        $currentdate = strtotime("now");
                        if (!empty($taskdate)) {
                            while ($taskdate <= $currentdate) {
                                $taskdate = $this->update_task_by_frequency($innerdata);
                                $innerdata['Start_date'] = date("F Y", $taskdate);
                                $innerdata['Startdate_month'] = date("j", $taskdate);
                            }
                        }
                        $innerdata['Parent_ID'] = $parent_id;
                        $innerdata['AU_Template_Designation_ID'] = $newdesign_id;
                        unset($innerdata['VT_Template_Designation_ID']);
                        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                        $innerdata['User_ID'] = $user_id;
                        $template->InsertEquipmentTemplatetask($innerdata);
                        //die;
                    }
                }
            }
            //die;
            $task_data = $template->get_tabledata('pm_vt_template_task', $design_id);
            foreach ($task_data as $tdata) {
                $data = (array) $tdata;
                unset($data['VT_Template_Task_ID']);
                $data['AU_Template_Designation_ID'] = $newdesign_id;
                unset($data['VT_Template_Designation_ID']);
                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                $taskdate = strtotime($data['Startdate_month'] . " " . $data['Start_date']);
                $currentdate = strtotime("now");
                if (!empty($taskdate))
                    while ($taskdate <= $currentdate) {
                        $taskdate = $this->update_task_by_frequency($data);
                        //echo date("d F Y",$taskdate);
                        $data['Start_date'] = date("F Y", $taskdate);
                        $data['Startdate_month'] = date("j", $taskdate);
                    }
                $data['User_ID'] = $user_id;
                $template->InsertEquipmentTemplatetask($data);
            }
            //die('sanjay');

            /* Reading section start */
            //$task_subset = $template->getEquipmentTemplate_subsetbyid_import('pm_vt_template_task',$design_id);
            //echo $design_id;
            $reading_subset = $template->getEquipmentTemplate_subsetbyid_import('pm_vt_template_reading', $design_id);
            //print_r($reading_subset);
            //die;
            foreach ($reading_subset as $tsubset) {
                $innertask = $template->getEquipmentTemplate_taskbysubsetId_import('pm_vt_template_reading', $design_id, $tsubset->VT_Template_Reading_ID);
                //print_r($innertask);
                $data = (array) $tsubset;
                unset($data['VT_Template_Reading_ID']);
                unset($data['VT_Template_Designation_ID']);
                $data['AU_Template_Designation_ID'] = $newdesign_id;
                //$data['Reading_Value'] = 1;                                
                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                $data['User_ID'] = $user_id;
                //print_r($data);
                $parent_id = $template->InsertEquipmentTemplateReadingsubset($data);

                if (!empty($innertask)) {

                    foreach ($innertask as $task) {
                        $innerdata = (array) $task;
                        unset($innerdata['VT_Template_Reading_ID']);
                        unset($innerdata['VT_Template_Designation_ID']);
                        $taskdate = strtotime($innerdata['Startdate_month'] . " " . $innerdata['Start_date']);

                        $currentdate = strtotime("now");

                        if (!empty($taskdate))
                            while ($taskdate <= $currentdate) {
                                //echo "inloop";
                                $taskdate = $this->update_task_by_frequency($innerdata);
                                //echo date("d F Y",$taskdate);
                                $innerdata['Start_date'] = date("F Y", $taskdate);
                                $innerdata['Startdate_month'] = date("j", $taskdate);
                            }
                        $innerdata['Parent_ID'] = $parent_id;
                        $innerdata['AU_Template_Designation_ID'] = $newdesign_id;
                        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                        $innerdata['User_ID'] = $user_id;
                        $template->InsertEquipmentTemplateReading($innerdata);
                    }
                }
            }
            //die;
            $task_data = $template->get_tabledata('pm_vt_template_reading', $design_id);
            // print_r($task_data);
            foreach ($task_data as $tdata) {
                $data = (array) $tdata;

                unset($data['VT_Template_Reading_ID']);
                unset($data['VT_Template_Designation_ID']);
                $taskdate = strtotime($data['Startdate_month'] . " " . $data['Start_date']);
                $currentdate = strtotime("now");


                if (!empty($taskdate)) {
                    while ($taskdate <= $currentdate) {
                        $taskdate = $this->update_task_by_frequency($data);
                        //echodate("d F Y",$taskdate);
                        $data['Start_date'] = date("F Y", $taskdate);
                        $data['Startdate_month'] = date("j", $taskdate);
                    }
                }
                $data['AU_Template_Designation_ID'] = $newdesign_id;
                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                $data['User_ID'] = $user_id;
                $result = $template->InsertEquipmentTemplateReading($data);
            }
        }
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Import All The Data Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function update_task_by_frequency($data) {

        $template = new Model_PmTemplate();
        //print_r($data);
        //echo $data['AU_Frequency_ID'];
        $fdata = $template->GetfrequencybyId($data['AU_Frequency_ID']);
        $fdata = $fdata[0];
        if ($data['Interval_Value'] == 0 || $data['Interval_Value'] == 1) {
            $frequency = $fdata->Interval;
            if ($data['AU_Frequency_ID'] == 4) {
                $nextdate = strtotime("+3 month", strtotime($data['Startdate_month'] . " " . $data['Start_date']));
            } else {
                $nextdate = strtotime("+1 " . $frequency . "", strtotime($data['Startdate_month'] . " " . $data['Start_date']));
            }
        } else {

            if ($data['AU_Frequency_ID'] == 4) {
                $intyear = 3 * $data['Interval_Value'];
                $nextdate = strtotime("+" . $intyear . " month", strtotime($data['Startdate_month'] . " " . $data['Start_date']));
            } else {

                $frequency = $data['Interval_Value'] . ' ' . $fdata->Interval;
                $nextdate = strtotime("+" . $frequency . "", strtotime($data['Startdate_month'] . " " . $data['Start_date']));
            }
        }

        return $nextdate;
    }

    public function validationEquipmentTemplatedragandrop($data) {
        $ret_data = 1;
        foreach ($data as $val) {
            //print_r($val);
            if ($val->idRoot) {
                if (!empty($val->children))
                    $ret_data = 0;
            }
            if ($val->idSubset) {
                foreach ($val->children as $data) {
                    if ($data->idSubset) {
                        $ret_data = 0;
                    }
                }
            }
        }
        return $ret_data;
    }

    /*     * *********************** Equipment Section start ******************* */

    public function equipmentAction() {
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
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");

        /* if (empty($build_ID) && (isset($_SESSION['current_building']) && in_array($_SESSION['current_building'], $buildIds))) {
          $build_ID = $_SESSION['current_building'];
          } */

        if ($companyListing != '') {
            if ($build_ID != '')
                $select_build_id = $build_ID;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }
        
        $_SESSION['current_building'] = $select_build_id;    

        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;

        $templateName = "";
        $designationName = "";
        $data = $this->_request->getPost();
        
        // print_r($data);
        // die;
        $equipment = new Model_PmTemplate();
        $templatedata = array();
        $AllEquipment = $equipment->getallEquipmentNameByBuildId($select_build_id);
        $totalEquipment = array();
        foreach ($AllEquipment as $Equip) {
            $elm_details = $equipment->getallEquipmentDetail($select_build_id, $Equip->AU_Equipment_Name_ID);
            if ($Equip->AU_Equipment_Name_ID != "" && !empty($elm_details)) {
                $totalEquipment[$Equip->AU_Equipment_Name_ID]['AU_Equipment_Name'] = $Equip->AU_Equipment_Name;
                $totalEquipment[$Equip->AU_Equipment_Name_ID]['AU_Equipment_Name_ID'] = $Equip->AU_Equipment_Name_ID;
                $totalEquipment[$Equip->AU_Equipment_Name_ID]['Equipment_detail'] = $elm_details;
            }
        }

//                    if($data['search']=='Search'){
//                        $templateName = $data['templatename'];
//                        $designationName = $data['designationname'];
//                        $tempdata = $template->GetAllEquipmentTemplateName($templateName,$select_build_id);
//                        
//                        foreach($tempdata as $temp){
//                                $find = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID,$designationName,$select_build_id);
//                                if(!empty($find) && $designationName!=""){
//                                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name] = $temp->AU_Template_Name;
//                                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name_ID] = $temp->AU_Template_Name_ID;
//                                    $templatedata[$temp->AU_Template_Name_ID][AU_TypeDesignation] = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID,$designationName,$select_build_id);
//                                }else if($designationName==""){
//                                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name] = $temp->AU_Template_Name;
//                                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name_ID] = $temp->AU_Template_Name_ID;
//                                    $templatedata[$temp->AU_Template_Name_ID][AU_TypeDesignation] = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID,$designationName,$select_build_id);
//                                }
//                        }
//                    }else{
//                        $tempdata = $template->GetAllEquipmentTemplateName("",$select_build_id);
//                        foreach($tempdata as $temp){ 
//                                $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name] = $temp->AU_Template_Name;
//                                $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name_ID] = $temp->AU_Template_Name_ID;
//                                $templatedata[$temp->AU_Template_Name_ID][AU_TypeDesignation] = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID,$designationName,$select_build_id);
//                        }
//                    }
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
        $getEquipmentNameList = $equipment->getEquipmentNameList($select_build_id);

        if(isset($_COOKIE['eqname']) && !empty($_COOKIE['eqname'])){
            $data['eqname'] = $_COOKIE['eqname'];
            if(isset($_COOKIE['eqparts'])&& !empty($_COOKIE['eqparts'])){
                $data['eqparts'] = $_COOKIE['eqparts'];
            }
            $equipmentList = $equipment->searchEquipment($select_build_id, $data);
        }else{
            $equipmentList = $equipment->getEquipmentList($select_build_id, $data = 0);
        }
        // if(isset($_COOKIE['eqparts']) && !empty($_COOKIE['eqparts'])){
        //     $data['eqparts'] = $_COOKIE['eqparts'];
        // }
        $this->view->equipmentList = $equipmentList;
        $this->view->getEquipmentNameList = $getEquipmentNameList;
        $this->view->custID = $cust_id;
        $pageObj = new Ve_Paginator();
        $paginator = $pageObj->fetchPageDataResult($equipmentList, $page, $show);
        $this->view->equipmentList = $paginator;
        $this->view->page = $page;
        $this->view->show = $show;
        $this->view->totalEquipment = $totalEquipment; //$templatedata;
        $this->view->templateName = ""; //$templateName;
        $this->view->designationName = ""; //$designationName;
        $this->view->AllEquipment = $AllEquipment;
        //This is for modified equipment list
        $taskData = $equipment->getUpdatedEquipmentTaskList();
        $readingData = $equipment->getUpdatedEquipmentReadingList();
        $this->view->nooftask = sizeof($taskData);
        $this->view->noofreading = sizeof($readingData);
        $this->view->userId = $user_id;
    }

    /**
     * rest filter By Dadhi
     * 
     */

    public function resetfilterAction() {
        if (isset($_COOKIE['eqname'])) {
            unset($_COOKIE['eqname']); 
            setcookie('eqname', '', -1, '/');           
        }         
        if (isset($_COOKIE['eqparts'])) {
            unset($_COOKIE['eqparts']); 
            setcookie('eqparts', '', -1, '/');           
        }        
        $this->_redirect('/pm/equipment');
    }
    /**
     * This is the sorting purpose
     */
    public function sortequipmentnameAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->_request->getParams();
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

        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;

        $templateName = "";
        $designationName = "";
        $data = $this->_request->getPost();
        $equipment = new Model_PmTemplate();
        $templatedata = array();
        $AllEquipment = $equipment->getallEquipmentNameByBuildId($select_build_id);
        $totalEquipment = array();
        foreach ($AllEquipment as $Equip) {
            $elm_details = $equipment->getallEquipmentDetail($select_build_id, $Equip->AU_Equipment_Name_ID);
            if ($Equip->AU_Equipment_Name_ID != "" && !empty($elm_details)) {
                $totalEquipment[$Equip->AU_Equipment_Name_ID]['AU_Equipment_Name'] = $Equip->AU_Equipment_Name;
                $totalEquipment[$Equip->AU_Equipment_Name_ID]['AU_Equipment_Name_ID'] = $Equip->AU_Equipment_Name_ID;
                $totalEquipment[$Equip->AU_Equipment_Name_ID]['Equipment_detail'] = $elm_details;
            }
        }

        $equipmentList = $equipment->getEquipmentList($select_build_id, $data);

        $this->view->totalEquipment = $totalEquipment; //$templatedata;
        $this->view->templateName = ""; //$templateName;
        $this->view->designationName = ""; //$designationName;
        $this->_helper->viewRenderer('sortequipmentname');
        $this->view->equipmentList = $equipmentList;
        $this->view->userId = $user_id;
    }

    /**
     * This is the sorting purpose
     */
    public function sortequipmentdetailAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->_request->getParams();
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
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;

        $templateName = "";
        $designationName = "";
        $data = $this->_request->getPost();
        $equipment = new Model_PmTemplate();
        $templatedata = array();
        $AllEquipment = $equipment->getallEquipmentNameByBuildId($select_build_id);
        $totalEquipment = array();
        foreach ($AllEquipment as $Equip) {
            $elm_details = $equipment->getallEquipmentDetail($select_build_id, $Equip->AU_Equipment_Name_ID);
            if ($Equip->AU_Equipment_Name_ID != "" && !empty($elm_details)) {
                $totalEquipment[$Equip->AU_Equipment_Name_ID]['AU_Equipment_Name'] = $Equip->AU_Equipment_Name;
                $totalEquipment[$Equip->AU_Equipment_Name_ID]['AU_Equipment_Name_ID'] = $Equip->AU_Equipment_Name_ID;
                $totalEquipment[$Equip->AU_Equipment_Name_ID]['Equipment_detail'] = $elm_details;
            }
        }

        //$equipmentList = $equipment->getEquipmentListBy($select_build_id, $data);        
        $this->_helper->viewRenderer('sortequipmentdetail');
        $equipmentList = $equipment->getEquipmentListBy($select_build_id, $data);
        $this->view->equipmentList = $equipmentList;
        $this->view->userId = $user_id;
    }

    public function searchequipmentAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->_request->getParams();
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

        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;

        $templateName = "";
        $designationName = "";
        $data = $this->_request->getPost();
        if(isset($data['eqname'])){
            setcookie('eqname', $data['eqname'], time() + (86400 / 24), "/");
        }
        if(isset($data['eqparts'])){
             setcookie('eqparts', $data['eqparts'], time() + (86400 / 24), "/");
        }

        $equipment = new Model_PmTemplate();
        $templatedata = array();
        $AllEquipment = $equipment->getallEquipmentNameByBuildId($select_build_id);
        $totalEquipment = array();
        foreach ($AllEquipment as $Equip) {
            $elm_details = $equipment->getallEquipmentDetail($select_build_id, $Equip->AU_Equipment_Name_ID);
            if ($Equip->AU_Equipment_Name_ID != "" && !empty($elm_details)) {
                $totalEquipment[$Equip->AU_Equipment_Name_ID]['AU_Equipment_Name'] = $Equip->AU_Equipment_Name;
                $totalEquipment[$Equip->AU_Equipment_Name_ID]['AU_Equipment_Name_ID'] = $Equip->AU_Equipment_Name_ID;
                $totalEquipment[$Equip->AU_Equipment_Name_ID]['Equipment_detail'] = $elm_details;
            }
        }

        $equipmentList = $equipment->searchEquipment($select_build_id, $data);

        $this->view->totalEquipment = $totalEquipment; //$templatedata;
        $this->view->templateName = ""; //$templateName;
        $this->view->designationName = ""; //$designationName;
        $this->_helper->viewRenderer('sortequipmentname');
        $this->view->equipmentList = $equipmentList;
        $this->view->userId = $user_id;
    }

    /*
     * Here for onchange of search for
     */

    public function searchforAction() {
        $data = $this->_request->getParams();
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
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;

        $equipment = new Model_PmTemplate();
        $AllEquipment = $equipment->searchEquipment($select_build_id, $data);
        echo json_encode($AllEquipment);
        die;
    }

    public function addequipmentAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $subset = new Model_PmTemplate();
        // This is for delete data from temporary tables
        $subset->deleteDataFromTemporaryTable();
        $data = $this->_request->getParams();
        $desig_id = $data['desig_id'];
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $cust_id = $_SESSION['Zend_Auth']['storage']->cust_id;
        $role_id = $_SESSION['Zend_Auth']['storage']->role_id;
        
        $build_ID = $_SESSION['current_building'];
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
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");


        if ($companyListing != '') {
            if ($build_ID != '')
                $build_ID = $build_ID;
            else
                $build_ID = $companyListing[0]['build_id'];
        }

        $template = new Model_PmTemplate();
        $templatedata = array();
        $design = $template->GetallAUTemplateDetails($build_ID);
        $usertemplate = $template->GetallAUTemplateDetailsByUserId($build_ID);
        $allEquipmentName = $template->getallEquipmentNameByBuildId($build_ID);
        $design1 = $template->GetallVTTemplateDetails();
        $allTemplate = array_merge($design, $design1);

        $AllTemplateList = array();
        $i = 1;
        /* User section */
        foreach ($design as $data) {
            $AllTemplateList[$i]['TemplateName'] = $data->AU_Template_Name;
            $AllTemplateList[$i]['desig_id'] = $data->AU_Template_Designation_ID;
            $AllTemplateList[$i]['TypeDesignation'] = $data->AU_TypeDesignation;
            $AllTemplateList[$i]['TypeDescritpion'] = $data->AU_TypeDescritpion;
            $AllTemplateList[$i]['admin_template'] = "";
            $i++;
        }

        /* get distribution groups */
        $email_group_model = new Model_EmailGroup();
        $emailGroup = $email_group_model->get_email_group_by_building_id($build_ID);

        /* get all Venders */
        $order = $this->_getParam('order', 'company_name');
        $dir = $this->_getParam('dir', 'ASC');
        $vender = new Model_Vendor();
        $vendorList = $vender->getVendorByBid($build_ID, $order, $dir);

        /* Send data to view page */
        $this->view->alltemplates = $AllTemplateList;
        $this->view->usertemplate = $usertemplate;

        /*  Reading section */
        $ReadingSubset = $subset->GetAllSubset_reading($desig_id);
        $this->view->frequency = $freq;
        $this->view->Interval = $Intreval;
        $this->view->startdateadjustment = $startdatead;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
        $this->view->ReadingSubset = $ReadingSubset;
        $this->view->EmailGroup = $emailGroup;
        $this->view->VendorList = $vendorList;
        $this->view->build_id = $build_ID;
        $this->view->EquipmentName = $allEquipmentName;
    }

    public function uploadimageAction() {
        //$uploaddir = BASE_PATH . 'vecrm/public/pm/';
        $uploaddir = IMAGE_UPLOAD_DIR . '/public/pm/';
        $filename = basename($_FILES['file']['name']);
        $data = move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . '' . $filename);
        if ($data) {
            echo "true";
        } else {
            echo "false";
        }
        die;
    }

    function saveequipmentAction() {
        $data = $this->_request->getPost();
        $taskdata = $data;
        $build_ID = $data['build_id'];
        $equipment = new Model_PmTemplate();
        /* Equipment Section */
        if(empty($data['build_id'])){
            $this->_redirect('/pm/equipment');            
        }
        $insert_equipment = array('AU_Equipment_Name' => $data['AU_Equipment_Name'], 'BuildingID' => $data['build_id']);
        $equipment_id = $equipment->checkEquipment($insert_equipment);
        //print_r($equipment_id);
        //$equipment_id = $equipment_id[0]->AU_Equipment_Name_ID;
        if (empty($equipment_id)) {
            $equipment_id = $equipment->InsertEquipment($insert_equipment);
        } else {
            $equipment_id = $equipment_id[0]->AU_Equipment_Name_ID;
        }

        //$equipment_id = $equipment->Equipment($insert_equipment);                    
        //get au template name by designation id 
        $autemplatedetails = $equipment->GetEquipmentTemplateByTypeDesignationID($data['designation_id']);
        $autemplatedetails = $autemplatedetails[0];
        //print_r($autemplatedetails);
        unset($data['AU_Equipment_Name']);
        unset($data['designation_id']);
        unset($data['build_id']);
        unset($data['file']);
        unset($data['startdate']);
        unset($data['assignto']);
        unset($data['venderid']);
        unset($data['groupid']);
        unset($data['outsidevendor']);
        $data['AU_Equipment_Name_ID'] = $equipment_id;
        $data['AU_Template_Name_ID'] = $autemplatedetails->AU_Template_Name_ID;

       /* $build_ID = $this->_getParam('bid', '');
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']))) {
            $build_ID = $_COOKIE['build_cookie'];
        } else {
            setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        }

        //$data['BuildingID'] = $_SESSION['current_building'];*/
        $data['BuildingID'] = $build_ID;

        //$uploaddir = BASE_PATH . 'vecrm/public/pm/';

        $uploaddir = IMAGE_UPLOAD_DIR . '/public/pm/';
        $filename = basename($_FILES['file']['name']['equipmentmenual']);
        $da = move_uploaded_file($_FILES['file']['tmp_name']['equipmentmenual'], $uploaddir . '' . $filename);
        $data['Equipment_Manual'] = $filename;

        $filename = basename($_FILES['file']['name']['Contract']);
        $da = move_uploaded_file($_FILES['file']['tmp_name']['Contract'], $uploaddir . '' . $filename);
        $data['Outside_Contract'] = $filename;

        $data['Equipment_Inservice_Date'] = date("Y-m-d", strtotime($data['Equipment_Inservice_Date']));
        $data['AU_Template_Designation_ID'] = $taskdata['designation_id'];

        $autemplatedetails = $equipment->InsertEquipmentDetails($data);
        $tableTask = $equipment->getTableNameOfTask();

        $templateTaskIds = $equipment->getTemplateTaskIdByDesignationId($tableTask, $taskdata['designation_id']);
        //print_r($templateTaskIds);
        //echo $templateTaskIds[0]->AU_Template_Task_ID;
        $currentDate = date('M Y');
        $currentYear = date('Y');
        $months = array();
        $num = date("n", strtotime(date("M")));
        array_push($months, date("M"));
        for ($i = ($num + 1); $i <= 12; $i++) {
            $dateObj = DateTime::createFromFormat('!m', $i);
            array_push($months, $dateObj->format('M'));
        }

        foreach ($templateTaskIds as $val) {
            if (!empty($val->Start_date)) {
                $startDate = date('M Y', strtotime($val->Start_date));
                $matchMonth = explode('', $val->Start_date);
                if ($matchMonth['1'] > $currentYear) {
                    $startDate = date('M Y', strtotime($val->Start_date));
                } else if ($matchMonth['1'] == $currentYear) {
                    if (!in_array($matchMonth['0'], $months)) {
                        $startDate = $currentDate;
                    }
                }
            } else {
                $startDate = '';
            }
            
            if(empty($val->Startdate_month)){
                $startdate_month = '';                
            }else {
                $startdate_month = $val->Startdate_month;
            }

            $equipment_task = array(
                "AU_Equipment_Detail_ID" => $autemplatedetails,
                "AU_Template_Task_ID" => $val->AU_Template_Task_ID,
                "Start_Date" => (!empty($taskdata['startdate'])) ? date('M Y', strtotime($taskdata['startdate'])) : $val->Start_date,
                //"Start_Date" => $startDate,
                "End_Date" => $val->End_date,
                "Startdate_month" => (!empty($taskdata['startdate'])) ? date('d', strtotime($taskdata['startdate'])) : $startdate_month,
                "Email_group_ID" => (!empty($val->Assigned_to) and $tableTask == 'pm_au_temporary_template_task') ? $val->Assigned_to : $taskdata['assignto'], //$val->Assigned_to,
                "AU_Assign_Vendor" => $taskdata['outsidevendor'], //This will insert Yes/No
                "Vendor_ID" => $taskdata['venderid']
            );
            $equipment->insertEquipmentTask($equipment_task);
        }

        $tableReading = $equipment->getTableNameOfReading();

        $templateReadingIds = $equipment->getTemplateReadingIdByDesignationId($tableReading, $taskdata['designation_id']);

        foreach ($templateReadingIds as $val) {

            if (!empty($val->Start_date)) {
                $startDate = date('M Y', strtotime($val->Start_date));
                $matchMonth = explode('', $val->Start_date);
                if ($matchMonth['1'] > $currentYear) {
                    $startDate = date('M Y', strtotime($val->Start_date));
                } else if ($matchMonth['1'] == $currentYear) {
                    if (!in_array($matchMonth['0'], $months)) {
                        $startDate = $currentDate;
                    }
                }
            } else {
                $startDate = '';
            }
            
            if(empty($val->Startdate_month)){
                $startdate_month = '';                
            }else {
                $startdate_month = $val->Startdate_month;
            }

            $equipment_readings = array(
                "AU_Equipment_Detail_ID" => $autemplatedetails,
                "AU_Template_Reading_ID" => $val->AU_Template_Reading_ID,
                //"Start_Date" => (!empty($taskdata['startdate'])) ? date('M Y', strtotime($taskdata['startdate'])) : $val->Start_date,
                "Start_Date" => $startDate,
                "End_Date" => $val->End_date,
                "Startdate_month" => (!empty($taskdata['startdate'])) ? date('d', strtotime($taskdata['startdate'])) : $startdate_month,
                "Email_group_ID" => (!empty($val->Assigned_to) and $tableReading == 'pm_au_temporary_template_reading') ? $val->Assigned_to : $taskdata['assignto'],
                "AU_Assign_Vendor" => $taskdata['outsidevendor'], //This will insert Yes/No
                "Vendor_ID" => $taskdata['venderid']
            );
            $res = $equipment->insertEquipmentReadings($equipment_readings);
        }

        if ($res) {
            $equipment->deleteDataFromTemporaryTable();
            $msg['status'] = 'success';
            $msg['msg'] = 'Data save sucessfully';
        } else {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        }
        echo json_encode($msg);
        exit();
    }

    function savemultipleequipmentAction() {

        $data = $this->_request->getPost();
        $designationId = $data['designation_id'];
        $taskdata = $data;
        $build_ID = $data['build_id'];
        if(empty($data['build_id'])){
            $this->_redirect('/pm/equipment');            
        }
        $equipment = new Model_PmTemplate();
        $autemplatedetails = $equipment->GetEquipmentTemplateByTypeDesignationID($designationId);
        $autemplatedetails = $autemplatedetails[0];
        $au_template_name_id = $autemplatedetails->AU_Template_Name_ID;

        $insert_equipment = array('AU_Equipment_Name' => $data['AU_Equipment_Name'], 'BuildingID' => $data['build_id']);
        $equipment_id = $equipment->checkEquipment($insert_equipment);
        if (empty($equipment_id)) {
            $equipment_id = $equipment->InsertEquipment($insert_equipment);
        } else {
            $equipment_id = $equipment_id[0]->AU_Equipment_Name_ID;
        }

        $uploaddir = IMAGE_UPLOAD_DIR . '/public/pm/';
        $filename1 = basename($_FILES['file']['name']['equipmentmenual']);
        $da = move_uploaded_file($_FILES['file']['tmp_name']['equipmentmenual'], $uploaddir . '' . $filename1);

        $filename2 = basename($_FILES['file']['name']['Contract']);
        $da = move_uploaded_file($_FILES['file']['tmp_name']['Contract'], $uploaddir . '' . $filename2);

        $currentDate = date('M Y');
        $currentYear = date('Y');
        $months = array();
        $num = date("n", strtotime(date("M")));
        array_push($months, date("M"));
        for ($i = ($num + 1); $i <= 12; $i++) {
            $dateObj = DateTime::createFromFormat('!m', $i);
            array_push($months, $dateObj->format('M'));
        }

        $totalequipment = $data['Equipment_Unit'] * $data['Equipment_Floor'];
        $floor = $data['Equipment_Floor'];
        $unit = $data['Equipment_Unit'];

        $tableTask = $equipment->getTableNameOfTask();

        $tableReading = $equipment->getTableNameOfReading();

        for ($k = 1; $k <= $floor; $k++) {
            for ($j = 1; $j <= $unit; $j++) {
                $data['Equipment_Unit'] = $j;
                $data['Equipment_Floor'] = $k;

                //print_r($autemplatedetails);
                unset($data['AU_Equipment_Name']);
                unset($data['designation_id']);
                unset($data['build_id']);
                unset($data['file']);
                unset($data['startdate']);
                unset($data['assignto']);
                unset($data['venderid']);
                unset($data['groupid']);
                unset($data['outsidevendor']);
                $data['AU_Equipment_Name_ID'] = $equipment_id;
                $data['AU_Template_Name_ID'] = $au_template_name_id;
                $data['BuildingID'] = $build_ID;

                //$uploaddir = BASE_PATH . 'vecrm/public/pm/';                
                $data['Equipment_Manual'] = $filename1;
                $data['Outside_Contract'] = $filename2;

                $data['Equipment_Inservice_Date'] = date("Y-m-d", strtotime($data['Equipment_Inservice_Date']));
                $data['AU_Template_Designation_ID'] = $taskdata['designation_id'];

                $au_equipment_detail_id = $equipment->InsertEquipmentDetails($data);

                $templateTaskIds = $equipment->getTemplateTaskIdByDesignationId($tableTask, $taskdata['designation_id']);

                foreach ($templateTaskIds as $val) {
                    if (!empty($val->Start_date)) {
                        $startDate = date('M Y', strtotime($val->Start_date));
                        $matchMonth = explode('', $val->Start_date);
                        if ($matchMonth['1'] > $currentYear) {
                            $startDate = date('M Y', strtotime($val->Start_date));
                        } else if ($matchMonth['1'] == $currentYear) {
                            if (!in_array($matchMonth['0'], $months)) {
                                $startDate = $currentDate;
                            }
                        }
                    } else {
                        $startDate = '';
                    }

                    $equipment_task = array(
                        "AU_Equipment_Detail_ID" => $au_equipment_detail_id,
                        "AU_Template_Task_ID" => $val->AU_Template_Task_ID,
                        //"Start_Date" => (!empty($taskdata['startdate'])) ? date('M Y', strtotime($taskdata['startdate'])) : $val->Start_date,
                        "Start_Date" => $startDate,
                        "End_Date" => $val->End_date,
                        "Startdate_month" => (!empty($taskdata['startdate'])) ? date('d', strtotime($taskdata['startdate'])) : $val->Startdate_month,
                        "Email_group_ID" => (!empty($val->Assigned_to) and $tableTask == 'pm_au_temporary_template_task') ? $val->Assigned_to : $taskdata['assignto'], //$val->Assigned_to,
                        "AU_Assign_Vendor" => $taskdata['outsidevendor'], //This will insert Yes/No
                        "Vendor_ID" => $taskdata['venderid']
                    );
                    $equipment->insertEquipmentTask($equipment_task);
                }

                $templateReadingIds = $equipment->getTemplateReadingIdByDesignationId($tableReading, $taskdata['designation_id']);

                foreach ($templateReadingIds as $val) {
                    if (!empty($val->Start_date)) {
                        $startDate = date('M Y', strtotime($val->Start_date));
                        $matchMonth = explode('', $val->Start_date);
                        if ($matchMonth['1'] > $currentYear) {
                            $startDate = date('M Y', strtotime($val->Start_date));
                        } else if ($matchMonth['1'] == $currentYear) {
                            if (!in_array($matchMonth['0'], $months)) {
                                $startDate = $currentDate;
                            }
                        }
                    } else {
                        $startDate = '';
                    }

                    $equipment_readings = array(
                        "AU_Equipment_Detail_ID" => $au_equipment_detail_id,
                        "AU_Template_Reading_ID" => $val->AU_Template_Reading_ID,
                        //"Start_Date" => (!empty($taskdata['startdate'])) ? date('M Y', strtotime($taskdata['startdate'])) : $val->Start_date,
                        "Start_Date" => $startDate,
                        "End_Date" => $val->End_date,
                        "Startdate_month" => (!empty($taskdata['startdate'])) ? date('d', strtotime($taskdata['startdate'])) : $val->Startdate_month,
                        "Email_group_ID" => (!empty($val->Assigned_to) and $tableReading == 'pm_au_temporary_template_reading') ? $val->Assigned_to : $taskdata['assignto'],
                        "AU_Assign_Vendor" => $taskdata['outsidevendor'], //This will insert Yes/No
                        "Vendor_ID" => $taskdata['venderid']
                    );
                    $res = $equipment->insertEquipmentReadings($equipment_readings);
                }
            }
        }
        if ($res) {
            $msg['status'] = 'success';
            $msg['msg'] = 'Data save sucessfully';
        } else {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        }
        echo json_encode($msg);
        exit();
    }

    function getvenderdetailsAction() {
        $data = $this->_request->getPost();
        $vid = $data['vid'];
        $vender = new Model_Vendor();
        $getdata = $vender->getVendor($vid);
        //return $this->_helper->json->sendJson($getdata);
        echo json_encode($getdata);
        exit();
    }

    public function validatsfloorandunitAction() {
        $param = $this->_request->getPost();
        $data = $param;
        $build_id = $_COOKIE['build_cookie'];
        //$user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $template = new Model_PmTemplate();
        if (($param['unit'] == $param['old_unit']) && ($param['floor'] == $param['old_floor'])) {
            $result = 0;
        } else {
            $result = $template->checkUnitWithFloor($data, $build_id);
        }
        if ($result) {
            echo 'false';
        } else {
            echo 'true';
        }
        exit();
    }

    /* Group modification section */

    public function equipmenttaskstartdaterootAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $template_id = $param['desig_id'];
        $this->view->desig_id = $template_id;
    }

    public function equipmenttaskstartdatesubsetAction() {

        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $desig_id = $param['desig_id'];
        $parent_id = $param['subset_id'];
        $this->view->desig_id = $desig_id;
        $this->view->parent_id = $parent_id;
    }

    public function equipmenttaskassigntorootAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $template_id = $param['desig_id'];
        $email_group_model = new Model_EmailGroup();
        $emailGroup = $email_group_model->get_email_group_by_building_id($_SESSION['current_building']);
        $this->view->desig_id = $template_id;
        $this->view->EmailGroup = $emailGroup;
    }

    public function equipmenttaskassigntosubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $desig_id = $param['desig_id'];
        $parent_id = $param['subset_id'];
        $email_group_model = new Model_EmailGroup();
        $emailGroup = $email_group_model->get_email_group_by_building_id($_SESSION['current_building']);
        $this->view->desig_id = $desig_id;
        $this->view->parent_id = $parent_id;
        $this->view->EmailGroup = $emailGroup;
    }

    /**
     *  Equipment name update functionality
     */
    public function updateequipmentnameAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $postData = $this->_request->getPost();
        $equipment_id = $param['equipment_id'];
        $equipment = new Model_PmTemplate();
        $updateData = $equipment->getEquipmentNameByEquipmentId($equipment_id);
        $this->view->EquipmentName = $updateData[0]->AU_Equipment_Name;
        $this->view->EquipmentId = $updateData[0]->AU_Equipment_Name_ID;
    }

    public function editequipmentnameAction() {
        $data = $this->_request->getPost();
        $equipment_id = $data['Equipment_id'];
        $postData = array('AU_Equipment_Name' => $data['EquipmentName']);
        $equipment = new Model_PmTemplate();
        $result = $equipment->updateequipmentname($postData, $equipment_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Equipment name Updated sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function validateequipmentnameAction() {
        //$param = $this->getRequest()->getParams();
        $param = $this->_request->getPost();
        $equipment_Name = $param['EquipmentName'];
        //$equipment_id = $param['Equipment_id'];
        $build_id = $_COOKIE['build_cookie'];
        //$user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $template = new Model_PmTemplate();
        $result = $template->getEquipmentNameByName($equipment_Name, $build_id);
        if (empty($result)) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit();
    }

    /*
     * This is for Equipement reading update start date for root 
     */

    public function equipmentreadingstartdaterootAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $template_id = $param['desig_id'];
        $this->view->desig_id = $template_id;
    }

    /*
     * This is for Equipment reading update start date for subset
     */

    public function equipmentreadingstartdatesubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $desig_id = $param['desig_id'];
        $parent_id = $param['subset_id'];
        $this->view->desig_id = $desig_id;
        $this->view->parent_id = $parent_id;
    }

    public function updatetaskrootassignAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['Assigned_to'] = $data['assignto'];
        //$updata = $data['assignto'];
        $result = $task->assignUpdateForTask($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Assignto Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function updatetasksubsetassignAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $desig_id = $data['desig_id'];
        $updata['Assigned_to'] = $data['assignto'];
        $result = $task->assignUpdateForTaskSubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Assignto Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function equipmentreadingassigntorootAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $desig_id = $param['desig_id'];
        $email_group_model = new Model_EmailGroup();
        $emailGroup = $email_group_model->get_email_group_by_building_id($_COOKIE['build_cookie']);
        $this->view->desig_id = $desig_id;
        $this->view->EmailGroup = $emailGroup;
    }

    /*
     * update reading assignto to root 
     */

    public function updatereadingrootassigntoAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $desig_id = $data['desig_id'];
        $updata['Assigned_to'] = $data['assignto'];
        //$updata = $data['assignto'];
        $result = $task->assignUpdateForReadingRoot($updata, $desig_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update assignto Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function equipmentreadingassigntosubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $desig_id = $param['desig_id'];
        $parent_id = $param['subset_id'];
        $email_group_model = new Model_EmailGroup();
        $emailGroup = $email_group_model->get_email_group_by_building_id($_COOKIE['build_cookie']);
        $this->view->desig_id = $desig_id;
        $this->view->EmailGroup = $emailGroup;
        $this->view->parent_id = $parent_id;
    }

    /*
     * update reading assignto to root 
     */

    public function updatereadingsubsetassigntoAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $desig_id = $data['desig_id'];
        $parent_id = $data['parent_id'];
        $updata['Assigned_to'] = $data['assignto'];
        //$updata = $data['assignto'];
        $result = $task->assignUpdateForReadingSubset($updata, $desig_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Assignto Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    // Delete equipment name 
    public function deleteequipmentnameAction() {
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();

        $result = $template->deleteEquipmentName($param['au_equipment_name_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {

            $template->deleteEquipmentdetailByAuEqupmentNameId($param['au_equipment_name_id']);
            $msg['status'] = 'success';
            $msg['msg'] = 'Template Deleted sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function equipmentdateupdateAction() {
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();
        $result = $template->getDateUpdate($param);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for update date';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Date Update sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function equipmentupdateassignAction() {
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();
        $result = $template->getUpdateAssignToAll($param);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for update date';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Date Update sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /**
     * This is for Add multiple equipment of same type
     */
    public function addmultipleequipmentAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $subset = new Model_PmTemplate();
        // This is for delete data from temporary tables
        $subset->deleteDataFromTemporaryTable();
        $data = $this->_request->getParams();
        $desig_id = $data['desig_id'];
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $cust_id = $_SESSION['Zend_Auth']['storage']->cust_id;
        $role_id = $_SESSION['Zend_Auth']['storage']->role_id;
        $this->_helper->layout()->setLayout('popuplayout');
        //$build_ID = $_SESSION['current_building'];
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
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");


        if ($companyListing != '') {
            if ($build_ID != '')
                $build_ID = $build_ID;
            else
                $build_ID = $companyListing[0]['build_id'];
        }
        $template = new Model_PmTemplate();
        $templatedata = array();
        $design = $template->GetallAUTemplateDetails($build_ID);
        $usertemplate = $template->GetallAUTemplateDetailsByUserId($build_ID);
        $allEquipmentName = $template->getallEquipmentNameByBuildId($build_ID);
        $design1 = $template->GetallVTTemplateDetails();
        $allTemplate = array_merge($design, $design1);

        $AllTemplateList = array();
        $i = 1;
        /* User section */
        foreach ($design as $data) {
            $AllTemplateList[$i]['TemplateName'] = $data->AU_Template_Name;
            $AllTemplateList[$i]['desig_id'] = $data->AU_Template_Designation_ID;
            $AllTemplateList[$i]['TypeDesignation'] = $data->AU_TypeDesignation;
            $AllTemplateList[$i]['TypeDescritpion'] = $data->AU_TypeDescritpion;
            $AllTemplateList[$i]['admin_template'] = "";
            $i++;
        }

        /* get distribution groups */
        $email_group_model = new Model_EmailGroup();
        $emailGroup = $email_group_model->get_email_group_by_building_id($build_ID);

        /* get all Venders */
        $order = $this->_getParam('order', 'company_name');
        $dir = $this->_getParam('dir', 'ASC');
        $vender = new Model_Vendor();
        $vendorList = $vender->getVendorByBid($build_ID, $order, $dir);

        /* Send data to view page */
        $this->view->alltemplates = $AllTemplateList;
        $this->view->usertemplate = $usertemplate;

        /*  Reading section */
        $ReadingSubset = $subset->GetAllSubset_reading($desig_id);
        $this->view->frequency = $freq;
        $this->view->Interval = $Intreval;
        $this->view->startdateadjustment = $startdatead;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
        $this->view->ReadingSubset = $ReadingSubset;
        $this->view->EmailGroup = $emailGroup;
        $this->view->VendorList = $vendorList;
        $this->view->build_id = $build_ID;
        $this->view->EquipmentName = $allEquipmentName;
    }

    /*
     * This is for update Email group id
     */

    public function emailgroupidupdateAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $desig_id = $data['desig_id'];
        $updata['Assigned_to'] = $data['emailGroupId'];
        $result = $task->updateEmailGroupId($updata, $desig_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Assinged to Successfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*
     * This is for Equipment task detail
     */

    public function createequipmentdetailtaskAction() {
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
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $companyListing[0]['build_id'], time() + (86400 / 24), "/");

        /* if (empty($build_ID) && (isset($_SESSION['current_building']) && in_array($_SESSION['current_building'], $buildIds))) {
          $build_ID = $_SESSION['current_building'];
          } */

        if ($companyListing != '') {
            if ($build_ID != '')
                $select_build_id = $build_ID;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }

        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        $equipment = new Model_PmTemplate();
        $params = $this->_request->getParams();
        if (!empty($params['modified']) && $params['modified'] == 1) {
            $update['AU_Assign_Vendor'] = 'No';
            $equipment->UpdateTaskandReading($update, $params['eqp_detail_id']);
        }
        $this->view->eqp_detail_id = $params['eqp_detail_id'];
        $this->view->eqp_name = $params['eqp_name'];
        $this->view->temp = $params['temp'];
        $results = $equipment->getTemplateTask($params['eqp_detail_id']);
        $list = $equipment->get_au_view_table('task');
        $listview = explode(',', $list[0]->display_table_view);
        $this->view->results = $results;
        $this->view->listview = $listview;
        //This is for modified equipment list
        $taskData = $equipment->getUpdatedEquipmentTaskList();
        $readingData = $equipment->getUpdatedEquipmentReadingList();
        $this->view->nooftask = sizeof($taskData);
        $this->view->noofreading = sizeof($readingData);
        $this->view->custID = $cust_id;
        $this->view->userId = $user_id;
        $this->view->eqp_detail_id = $params['eqp_detail_id'];
        
        $freq = array();
            $CustFreq = array();
            $frequency = $equipment->Getallfrequency();
            foreach ($frequency as $val) {
                if ($val->column == 1)
                    $freq[$val->AU_Frequency_ID] = $val->Name;
                if ($val->column == 2)
                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
            }
        
        $this->view->frequency = $freq;
            $this->view->CustmeFreq = $Intreval;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;    
    }

    /*
     * This is for Equipment reading detail
     */

    public function createequipmentdetailreadingAction() {
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
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");

        /* if (empty($build_ID) && (isset($_SESSION['current_building']) && in_array($_SESSION['current_building'], $buildIds))) {
          $build_ID = $_SESSION['current_building'];
          } */

        if ($companyListing != '') {
            if ($build_ID != '')
                $select_build_id = $build_ID;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }

        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        $equipment = new Model_PmTemplate();
        $params = $this->_request->getParams();
        if (!empty($params['modified']) && $params['modified'] == 1) {
            $update['AU_Assign_Vendor'] = 'No';
            $equipment->UpdateTaskandReading($update, $params['eqp_detail_id']);
        }
        $this->view->eqp_detail_id = $params['eqp_detail_id'];
        $this->view->eqp_name = $params['eqp_name'];
        $this->view->temp = $params['temp'];
        
        $results = $equipment->getTemplateReading($params['eqp_detail_id']);
        $list = $equipment->get_au_view_table('Reading');
        $listview = explode(',', $list[0]->display_table_view);
        $this->view->results = $results;
        $this->view->listview = $listview;
        //This is for modified equipment list
        $taskData = $equipment->getUpdatedEquipmentTaskList();
        $readingData = $equipment->getUpdatedEquipmentReadingList();
        $this->view->nooftask = sizeof($taskData);
        $this->view->noofreading = sizeof($readingData);
        $this->view->userId = $user_id;
    }

    public function equipmentdetailtaskstartdaterootAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $eqp_detail_id = $param['eqp_detail_id'];
        $this->view->eqp_detail_id = $eqp_detail_id;
    }

    public function updateonlystartdateofequipmentdetailtaskrootAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $eqp_detail_id = $data['eqp_detail_id'];
        $updata['Start_date'] = date('M Y', strtotime($data['startdate']));
        $updata = date('M Y', strtotime($data['startdate']));
        $result = $task->updateonlystartdateofequipmentdetailtaskroot($updata, $eqp_detail_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function equipmentdetailtaskstartdatesubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $eqp_detail_id = $param['eqp_detail_id'];
        $parent_id = $param['subset_id'];
        $this->view->eqp_detail_id = $eqp_detail_id;
        $this->view->parent_id = $parent_id;
    }

    public function updateonlystartdateofequipmentdetailtasksubsetAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $eqp_detail_id = $data['eqp_detail_id'];
        //$updata['Start_date'] = $data['startdate'];
        $updata = date('M Y', strtotime($data['startdate']));
        $result = $task->updateonlystartdateofequipmentdetailtasksubset($updata, $eqp_detail_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function editequipmentdetailtaskAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $subset = new Model_PmTemplate();
        $param = $this->_request->getParams();
        $data = $subset->getTemplateTask($param['eqp_detail_id'], $param['subset_id']);
        $data = $data[0];
        $desig_id = $data['AU_Template_Designation_ID'];
        $task_id = $param['subset_id'];
        $allsubset = $subset->GetAllEquipmentTemplateSubset($desig_id);
        $TaskData = $subset->GetEquipmentTemplatetaskDataById($task_id);
        $TaskData = $TaskData[0];
        $freq = array();
        $frequency = $subset->GetallEquipmentTemplatefrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }

        $alljobtime = array();
        $jobtime = $subset->GetallEquipmentTemplatejobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
        }

        $startdate_ad = array();
        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdate_ad[$val->AU_sda_ID] = $val->Name;
        }

        /* get distribution groups */
        $email_group_model = new Model_EmailGroup();
        $emailGroup = $email_group_model->get_email_group_by_building_id($_COOKIE['build_cookie']);

        $this->view->subset = $allsubset;
        $this->view->taskdata = $TaskData;
        $this->view->frequency = $freq;
        $this->view->Interval = $Intreval;
        $this->view->eqpDetailData = $data;
        $this->view->jobtime = $alljobtime;
        $this->view->startdateadjustment = $startdate_ad;
        $this->view->emailGroup = $emailGroup;
    }

    public function updateonlystartdateofequipmentdetailtaskAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata['Startdate_month'] = $data['day_of_month'];
        $updata['Email_group_ID'] = $data['assingedTo'];
        $eqp_task_id = $data['eqp_task_id'];
        $updata['Start_date'] = date('M Y', strtotime($data['startdate']));
        //$updata = date('M Y', strtotime($data['startdate']));    
        $result = $task->updateonlystartdateofequipmentdetailtask($updata, $eqp_task_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function equipmentdetailreadingstartdaterootAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $eqp_detail_id = $param['eqp_detail_id'];
        $this->view->eqp_detail_id = $eqp_detail_id;
    }

    /*
     * Equipment detail reading update start date only on root 
     */

    public function updateonlystartdateofequipmentdetailreadingrootAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $eqp_detail_id = $data['eqp_detail_id'];
        $updata = date('M Y', strtotime($data['startdate']));
        $result = $task->updateonlystartdateofequipmentdetailreadingroot($updata, $eqp_detail_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*
     * Equipment detail reading update start date on subset poppup
     */

    public function equipmentdetailreadingstartdatesubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $eqp_detail_id = $param['eqp_detail_id'];
        $parent_id = $param['subset_id'];
        $this->view->eqp_detail_id = $eqp_detail_id;
        $this->view->parent_id = $parent_id;
    }

    /*
     * Equipment detail reading update start date only on subset
     */

    public function updateonlystartdateofequipmentreadingsubsetAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $parent_id = $data['parent_id'];
        $eqp_detail_id = $data['eqp_detail_id'];
        $updata = date('M Y', strtotime($data['startdate']));
        $result = $task->updateonlystartdateofequipmentreadingsubset($updata, $eqp_detail_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*
     * equipment detail Edit start date only
     */

    public function editequipmentdetailreadingAction() {

        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $subset = new Model_PmTemplate();
        $data = $subset->getTemplateReading($param['eqp_detail_id'], $param['subset_id']);
        $data = $data[0];
        $desig_id = $data['AU_Template_Designation_ID'];
        $reading_id = $data['AU_Template_Reading_ID'];
        $ReadingData = $subset->GetEquipmentTemplatereadingDataById($reading_id);
        $allsubset = $subset->GetAllEquipmentTemplateSubset_reading($desig_id);
        $freq = array();
        $frequency = $subset->GetallEquipmentTemplatefrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
            if ($val->column == 2)
                $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }
        $startdatead = array();
        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
        foreach ($startdateadjustment as $val) {
            $startdatead[$val->AU_sda_ID] = $val->Name;
        }

        $alljobtime = array();
        $jobtime = $subset->GetallEquipmentTemplatejobtime();
        foreach ($jobtime as $val) {
            $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;
        }

        $allunitofmeasure = array();
        $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
        foreach ($unitofmeasure as $val) {
            $allunitofmeasure[$val->AU_uom_ID] = $val->Name;
        }
        /* get distribution groups */
        $email_group_model = new Model_EmailGroup();
        $emailGroup = $email_group_model->get_email_group_by_building_id($_COOKIE['build_cookie']);

        $this->view->frequency = $freq;
        $this->view->Interval = $Intreval;
        $this->view->startdateadjustment = $startdatead;
        $this->view->unitofmeasure = $allunitofmeasure;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
        $this->view->readingdata = $ReadingData[0];
        $this->view->eqpDetailData = $data;
        $this->view->emailGroup = $emailGroup;
    }

    /**
     * Equipment detail reading start date update on 
     */
    public function updateonlystartdateofequipmentdetailreadingAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $eqp_task_id = $data['eqp_task_id'];
        $updata['Startdate_month'] = $data['startdate_month'];
        $updata['Email_group_ID'] = $data['assignedTo'];

        $updata['Start_date'] = date('M Y', strtotime($data['startdate']));
        //$updata = date('M Y', strtotime($data['startdate']));    
        $result = $task->updateonlystartdateofequipmentdetailreading($updata, $eqp_task_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    public function updateequipmentdetailviewtaskAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $this->_helper->viewRenderer('updateequipmentdetailviewtask');
        $listview = explode(',', $param['viewlist']);
        $results = $task->getTemplateTask($param['eqp_detail_id']);
        $this->view->listview = $listview;
        $this->view->results = $results;
        $this->view->eqp_detail_id = $param['eqp_detail_id'];
        
        $freq = array();
        $CustFreq = array();
        $frequency = $task->Getallfrequency();
        foreach ($frequency as $val) {
            if ($val->column == 1)
                $freq[$val->AU_Frequency_ID] = $val->Name;
                if ($val->column == 2)
                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }
        $this->view->frequency = $freq;
        $this->view->CustmeFreq = $Intreval;
        $this->view->startdateadjustment = $startdate_ad;
        $this->view->jobtime = $alljobtime; 
        
    }

    public function updateequipmentdetailviewreadingAction() {
        $this->_helper->layout()->disableLayout();
        $param = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $this->_helper->viewRenderer('updateequipmentdetailviewreading');
        $listview = explode(',', $param['viewlist']);
        $results = $task->getTemplateReading($param['eqp_detail_id']);
        $this->view->listview = $listview;
        $this->view->results = $results;
        $this->view->eqp_detail_id = $param['eqp_detail_id'];
    }

    /**
     * Equipment updated records will show after updated the template 
     */
    public function modifiedequipmentlistAction() {
        $pmtemplate = new Model_PmTemplate();
        $param = $this->getRequest()->getParams();
        if (!empty($param['eqpdetailid'])) {
            $update['AU_Assign_Vendor'] = 'No';
            $pmtemplate->UpdateTaskandReading($update, $param['eqpdetailid']);
        }
        $taskData = $pmtemplate->getUpdatedEquipmentTaskList();
        $readingData = $pmtemplate->getUpdatedEquipmentReadingList();
        $this->view->taskData = $taskData;
        $this->view->readingData = $readingData;
        $this->view->nooftask = sizeof($taskData);
        $this->view->noofreading = sizeof($readingData);
    }

    /**
     * Edit the Equipment detail
     */
    public function editequipmentAction() {
        
        $this->_helper->layout()->setLayout('popuplayout');        
        $data = $this->_request->getParams();
        $desig_id = $data['desig_id'];
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $cust_id = $_SESSION['Zend_Auth']['storage']->cust_id;
        $role_id = $_SESSION['Zend_Auth']['storage']->role_id;
        
        $build_ID = $_SESSION['current_building'];
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
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");


        if ($companyListing != '') {
            if ($build_ID != '')
                $build_ID = $build_ID;
            else
                $build_ID = $companyListing[0]['build_id'];
        }
        $pmtemplate = new Model_PmTemplate();
       // $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $eqpDetail = $pmtemplate->getEquipmentDetailById($param['eqp_detail_id']);
        $this->view->eqpDetail = $eqpDetail[0];
        $eqpManual = $pmtemplate->getEquipmentManualById($param['eqp_detail_id']);
        $this->view->eqpManual = $eqpManual;
        $email_group_model = new Model_EmailGroup();
        $emailGroup = $email_group_model->get_email_group_by_building_id($build_ID);
		$this->view->EmailGroup = $emailGroup;
    }

    /**
     * This for saving the equipment detail updated data
     */
    function saveeditequipmentAction() {
        $data = $this->_request->getPost();
        $equipment = new Model_PmTemplate();
        //$data['BuildingID'] = $_SESSION['current_building'];        
        $uploaddir = IMAGE_UPLOAD_DIR . '/public/pm/';
        $filename = basename($_FILES['file']['name']['equipmentmenual']);
        $da = move_uploaded_file($_FILES['file']['tmp_name']['equipmentmenual'], $uploaddir . '' . $filename);
        //$data['Equipment_Manual'] = $filename;               
        $data['Equipment_Inservice_Date'] = date("Y-m-d", strtotime($data['Equipment_Inservice_Date']));
        $result = $equipment->editequipmentdetail($data, $filename);
        if ($result) {
            $msg['status'] = 'success';
            $msg['msg'] = 'Data save sucessfully';
        } else {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        }
        echo json_encode($msg);
        exit();
    }

    /**
     * This is for delete the equipment manual PDF during the edit equipment detail 
     */
    // Delete equipment name 
    public function deleteequipmentmanualpdfAction() {
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();
        $result = $template->deleteEquipmentManualPDF($param);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Manual PDF Deleted sucessfully';
        }
        echo json_encode($msg);
        exit();
    }

    /*
     * This is for showing the manual PDF list
     */

    public function manualpdflistAction() {
        $template = new Model_PmTemplate();
        $this->_helper->layout()->disableLayout();
        $data = $this->_request->getParams();
        $this->_helper->viewRenderer('manualpdflist');
        $eqpDetail = $template->getEquipmentDetailById($data['au_equipment_detail_id']);
        $this->view->eqpDetail = $eqpDetail[0];
        $eqpManual = $template->getEquipmentManualById($data['au_equipment_detail_id']);
        $this->view->eqpManual = $eqpManual;
    }

    /**
     * Pm Work order section start
     */
    public function pmworkorderAction() {
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

    public function pmworkorderbynumberAction() {
        $pmTemplate = new Model_PmTemplate();
        $actionnm = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        if ($actionnm == 'pmworkorderbynumber') {
            setcookie('by_wonumber', 'YES', 2147483647, '/');
        }

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
        $pmCompleteJobTime = $pmTemplate->getPmCompleteJobTime($select_build_id);
        $this->view->workorderList = $workorderList;
        $pageObj = new Ve_Paginator();
        $paginator = $pageObj->fetchPageDataResult($workorderList, $page, $show);  
        $this->view->workorderList = $paginator;
        $this->view->userId = $user_id;
        $this->view->page = $page;
        $this->view->show = $show;
        $this->view->pmCompleteJobTime = $pmCompleteJobTime[0];
    }

    public function sortworkorderequipmentAction() {
        $this->_helper->layout()->disableLayout();
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
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        $data = $this->_request->getPost();
        $this->_helper->viewRenderer('sortworkorderequipment');
        $workorderList = $pmTemplate->getWorkorderListBy($select_build_id, $data);
        $this->view->workorderList = $workorderList;
        $this->view->id = $data['id'];
    }

    /*
     * Here for onchange of work order search for
     */

    public function wosearchforAction() {
        $data = $this->_request->getParams();
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

        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;

        $equipment = new Model_PmTemplate();
        $AllEquipment = $equipment->wosearchEquipment($select_build_id, $data);
        echo json_encode($AllEquipment);
        die;
    }

    /**
     * This is the sorting for work order equipment name
     */
    public function wosortequipmentnameAction() {
        $pmTemplate = new Model_PmTemplate();
        $this->_helper->layout()->disableLayout();
        $data = $this->_request->getParams();
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
        $data = $this->_request->getPost();
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        $this->_helper->viewRenderer('wosortequipmentname');
        $workorderList = $pmTemplate->getWorkorderListByEquipment($select_build_id, $data);
        $this->view->workorderList = $workorderList;
    }

    /**
     * This is the sorting for work order equipment name
     */
    public function wosearchequipmentAction() {
        $pmTemplate = new Model_PmTemplate();
        $this->_helper->layout()->disableLayout();
        $data = $this->_request->getParams();
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

        $data = $this->_request->getPost();
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        $this->_helper->viewRenderer('wosortequipmentname');
        $workorderList = $pmTemplate->getWorkorderListByEquipment($select_build_id, $data);
        $this->view->workorderList = $workorderList;
    }

    /**
     * This is for sorting of work order by work order
     */
    public function sortworkorderequipmentbywoAction() {
        $this->_helper->layout()->disableLayout();
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

        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        $data = $this->_request->getPost();
        $this->_helper->viewRenderer('sortworkorderequipmentbywo');
        $workorderList = $pmTemplate->getWorkorderListByEquipment($select_build_id, $data);
        $this->view->workorderList = $workorderList;
        $this->view->id = $data['id'];
    }

    /**
     * This is for PM Set up and Options
     */
    public function pmsetupoptionsAction() {
        $building_id = $this->_getParam('buiild_id', '');
        $this->_helper->layout()->setLayout('popuplayout');
        $pmTemplate = new Model_PmTemplate();
        $pmSetupOptionsData = $pmTemplate->getWorkOrderOptionByBuilding($building_id);
        $this->view->pmSetupOptionsData = $pmSetupOptionsData;
    }

    /**
     * This is for save the Pm setup and options data
     */
    public function savepmsetupoptionsAction() {
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

        $data = $this->_request->getPost();
        $result = $pmTemplate->insertpmsetupoptions($data, $select_build_id);
        if ($result == 1) {
            echo 'updated';
        } else if ($result == 2) {
            echo 'added';
        } else {
            echo 'false';
        }
        exit();
    }

    /**
     * This is for PM - Complete WO's
     */
    public function pmcompletewoAction() {
        $param = $this->_request->getParams();        
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
        
        //$pmWoNumbr = $pmTemplate->callProcedure($select_build_id);
        $pmWoNumbr = $pmTemplate->getWoNumberByBuildingID($select_build_id);
        //$pmWoNumbr = $pmTemplate->getWorkorderListByEquipment($select_build_id, $data = 0);
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        $this->view->pmWoNumbr = $pmWoNumbr;
        $this->view->completewo = $param['wo_number'];
        $this->view->completeRadingTask = $param['reading_task'];
        $this->view->userId = $user_id;
        
    }

    /**
     * Search data for pmcomplete work order
     */
    public function searchofpmcompletewoAction() {
        
        $this->_helper->layout()->disableLayout();
        $param = $this->_request->getParams();   
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
      
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds))){
            $build_ID = $_COOKIE['build_cookie'];
        } else {
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        }
          
        if ($companyListing != '') {
            if ($build_ID != '')
                $select_build_id = $build_ID;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }
        
        $data = $this->_request->getPost();        
        $pmwonumber = $data['pmwonumber'];
        $pmTemplate = new Model_PmTemplate();
        $openPmWoRT = $pmTemplate->getReadingTaskByBuildingID($pmwonumber, $select_build_id);
        if(count($openPmWoRT)>1 && !empty($openPmWoRT)){
            $task = $openPmWoRT[0]->Reading_Task;
            $reading = $openPmWoRT[1]->Reading_Task;            
            $readingTask = $openPmWoRT[0]->Reading_Task;
        } else {
           $readingTask = $openPmWoRT[0]->Reading_Task;
        }
        
        $openPmWo = $pmTemplate->getOpenPmWorkorder($pmwonumber,$readingTask, $select_build_id);
        $pmWoTaskDetail = $pmTemplate->getPmWorkorderTaskDetail($pmwonumber, $select_build_id, $openPmWo[0]->Reading_Task);
        $pmWoReadingDetail = $pmTemplate->getPmWorkorderReadingDetail($pmwonumber, $select_build_id, $openPmWo[0]->Reading_Task);
        $pmCompleteJobTime = $pmTemplate->getPmCompleteJobTime($select_build_id);
        $pmcompletedby = $pmTemplate->getPmCompletedBy($select_build_id);        
        $pmWoNumbr = $pmTemplate->getWorkorderListByEquipment($select_build_id, $data = 0);
        $this->view->pmWoNumbr = $pmWoNumbr;
        $this->view->pmcompletedby = $pmcompletedby;
        $this->view->openPmWo = $openPmWo;
        $this->view->pmWoTaskDetail = $pmWoTaskDetail;
        $this->view->pmWoReadingDetail = $pmWoReadingDetail;
        $this->view->pmCompleteJobTime = $pmCompleteJobTime[0]->PM_Complete_Job_Time;
        $this->view->select_build_id = $select_build_id;
        $this->view->readingTask = $openPmWoRT;
        $this->view->completewo = $param['wo_number'];
        $this->view->completeRadingTask = $param['reading_task'];
        $this->_helper->viewRenderer('searchofpmcompletewo');
        
    }
    
    /**
     * This is reading  for pmcomplete work order
     */
    public function readingofpmcompletewoAction() {        
        $this->_helper->layout()->disableLayout();
        $param = $this->_request->getParams();   
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
      
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds))){
            $build_ID = $_COOKIE['build_cookie'];
        } else {
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        }
          
        if ($companyListing != '') {
            if ($build_ID != '')
                $select_build_id = $build_ID;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }
        
        $data = $this->_request->getPost();
        
        $pmwonumber = $data[pmwonumber];
        $readingTask = $data[reading];
        $pmTemplate = new Model_PmTemplate();        
        $openPmWo = $pmTemplate->getOpenPmWorkorder($pmwonumber,$readingTask, $select_build_id);
        //$pmWoTaskDetail = $pmTemplate->getPmWorkorderTaskDetail($pmwonumber, $select_build_id, $openPmWo[0]->Reading_Task);
        $pmWoReadingDetail = $pmTemplate->getPmWorkorderReadingDetail($pmwonumber, $select_build_id, $readingTask);
        $pmCompleteJobTime = $pmTemplate->getPmCompleteJobTime($select_build_id);
        $pmcompletedby = $pmTemplate->getPmCompletedBy($select_build_id);        
        $pmWoNumbr = $pmTemplate->getWorkorderListByEquipment($select_build_id, $data = 0);        
        $this->view->pmWoNumbr = $pmWoNumbr;
        $this->view->pmcompletedby = $pmcompletedby;
        $this->view->openPmWo = $openPmWo;
        //$this->view->pmWoTaskDetail = $pmWoTaskDetail;
        $this->view->pmWoReadingDetail = $pmWoReadingDetail;
        $this->view->pmCompleteJobTime = $pmCompleteJobTime[0]->PM_Complete_Job_Time;
        $this->view->select_build_id = $select_build_id;                
        $this->_helper->viewRenderer('readingofpmcompletewo');        
    }
    
    /**
     * This is task  for pmcomplete work order
     */
    public function taskofpmcompletewoAction() {        
        $this->_helper->layout()->disableLayout();
        $param = $this->_request->getParams();   
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
      
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds))){
            $build_ID = $_COOKIE['build_cookie'];
        } else {
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        }
          
        if ($companyListing != '') {
            if ($build_ID != '')
                $select_build_id = $build_ID;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }
        
        $data = $this->_request->getPost();
        
        $pmwonumber = $data[pmwonumber];
        $readingTask = $data[task];
        $pmTemplate = new Model_PmTemplate();        
        $openPmWo = $pmTemplate->getOpenPmWorkorder($pmwonumber,$readingTask, $select_build_id);
        $pmWoTaskDetail = $pmTemplate->getPmWorkorderTaskDetail($pmwonumber, $select_build_id, $openPmWo[0]->Reading_Task);
        //$pmWoReadingDetail = $pmTemplate->getPmWorkorderReadingDetail($pmwonumber, $select_build_id, $readingTask);
        $pmCompleteJobTime = $pmTemplate->getPmCompleteJobTime($select_build_id);
        $pmcompletedby = $pmTemplate->getPmCompletedBy($select_build_id);        
        $pmWoNumbr = $pmTemplate->getWorkorderListByEquipment($select_build_id, $data = 0);        
        $this->view->pmWoNumbr = $pmWoNumbr;
        $this->view->pmcompletedby = $pmcompletedby;
        $this->view->openPmWo = $openPmWo;
        $this->view->pmWoTaskDetail = $pmWoTaskDetail;
        //$this->view->pmWoReadingDetail = $pmWoReadingDetail;
        $this->view->pmCompleteJobTime = $pmCompleteJobTime[0]->PM_Complete_Job_Time;
        $this->view->select_build_id = $select_build_id;                    
        $this->_helper->viewRenderer('taskofpmcompletewo');
        
    }

    /**
     * PM complete workorder data save in pm_au_history functionality
     */
    public function savepmcompletehistoryAction() {
        $pmTemplate = new Model_PmTemplate();
        $data = $this->_request->getPost();
        $pwoids = array();
        foreach ($data['list'] as $userData) {
            $pmWoData = $pmTemplate->getPmCompleteWoData($userData[0]);
            $completeData = array('AU_Equipment_Task_Reading_ID' => $pmWoData['0']->AU_Equipment_Task_Reading_ID,
                'AU_Equipment_Detail_ID' => $pmWoData['0']->AU_Equipment_Detail_ID,
                'AU_Template_Designation_ID' => $pmWoData['0']->AU_Template_Designation_ID,
                'Parent_ID' => $pmWoData['0']->Parent_ID,
                'PM_WO_StartDate' => $pmWoData['0']->PM_WO_StartDate,
                'BuildingID' => $pmWoData['0']->BuildingID,
                'PM_WO_Number' => $pmWoData['0']->PM_WO_Number,
                'Reading_Task' => $pmWoData['0']->Reading_Task,
                'PM_Actual_JobTime' => $userData[1],
                'PM_WO_Complete_Date' => date("Y-m-d", strtotime($userData[2])),
                'PM_Note_Comments' => $userData[3],
                'PM_CompletedBy_UID' => $userData[4],
                'Reading_Value' => $userData[5] ? $userData[5] : '0.00'
            );
            $success = $pmTemplate->savePmCompleteData($completeData);
            if ($success) {
                //$pwoids[] = $userData[0];
                $pmTemplate->deletePmcompletewo($userData[0]);
            }
        }
        echo 'PM Task/Reading Instructions completed!';
        exit();
    }

    public function showequipmentoftemplateAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $build_id = $_SESSION['current_building'];
        $temp_id = $param['temp_id'];
        $template = new Model_PmTemplate();
        if (!empty($temp_id)) {
            $equipmentDetail = $template->getEquipmentDetailbyTempId($temp_id, $build_id);
        }

        $this->view->equipmentDetail = $equipmentDetail;
    }

    //This is for populate the  Template desingation by Template name

    public function searchfortemplateAction() {
        $data = $this->_request->getParams();
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
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        //$data = $this->_request->getPost();        
        $template = new Model_PmTemplate();
        $templatedata = array();
        $template_name_id = $data['template_name_id'];
        $tempdata = $template->getTypeDesignationbyTempId($template_name_id, $select_build_id);
        echo json_encode($tempdata);
        die;
    }

    /*     * *
     * This is for PM complete Work order date completed
     */

    public function pmcompletewodatecompletedAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $reading_task = $param['reading_task'];
        $this->view->reading_task = $reading_task;
    }

    /**
     * This is for Completed by
     */
    public function pmcompletewocompletedbyAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
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
        $pmTemplate = new Model_PmTemplate();
        $reading_task = $param['reading_task'];
        $pmcompletedby = $pmTemplate->getPmCompletedBy($select_build_id);
        $this->view->pmcompletedby = $pmcompletedby;
        $this->view->reading_task = $reading_task;
    }

    /**
     * This is User created template
     */
    public function usercreatedtemplateAction() {
        $param = $this->_request->getParams();
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $this->_helper->layout()->setLayout('popuplayout');
        $build_ID = $_SESSION['current_building'];
        //die;
        $template = new Model_PmTemplate();
        $templateAllVTList = $template->getAllVTTemplate();
        $templatDesination = $template->getTemplateTypedesignationByTypeDesig($param['desig_id'], $build_ID);

        $this->view->templateAllVTList = $templateAllVTList;
        $this->view->templatDesination = $templatDesination[0];
        $this->view->desig_id = $param['desig_id']; 
    }

    /**
     * This is for check the VT user created template name
     */
    public function validatevttemplatenameAction() {
        $data = $this->_request->getPost();
        $template = new Model_PmTemplate();
        if ($data['temptypedata'] == 3) {
            $checktempname = $template->checkvttemplatename($data);
            if ($checktempname) {
                echo $checktypedesig = $template->checkvttypedesig($data);
            } else {
                echo $checktempname;
            }
            exit();
        } else {
            $checktempname = $template->checktypedesig($data);
            if (is_array($checktempname)) {
                if (count($checktempname) > 0) {
                    echo 'false';
                } else {
                    echo $checktempname;
                }
            } else {
                echo $checktempname;
            }
            exit();
        }
    }

    /**
     * This is for Save Vt Type Designation Name
     */
    public function savevttypedesignationAction() {
        $data = $this->_request->getPost();
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;        
        $template = new Model_PmTemplate();
        $vt_desing_id = $template->saveVttypedesign($data, $user_id);        
        $result = $template->saveIntoVtTableTaskAndReading($data['desig_id'], $vt_desing_id, $user_id);   
        if ($result) {
            echo 'true';
        }
        exit();
    }

    /**
     * This is for export to another building
     */
    public function exporttoanotherbuildingAction() {
        $this->_helper->layout()->setLayout('popuplayout');
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

        $_SESSION['current_building'] = $select_build_id;
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
    }

    public function importtoanotherbuildingAction() {
        $this->_helper->layout()->setLayout('popuplayout');
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

        $_SESSION['current_building'] = $select_build_id;

        function cmp($a, $b) {
            return strcmp($a["buildingName"], $b["buildingName"]);
        }

        usort($companyListing, "cmp");
        
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        echo json_encode($companyListing);
        die;
    }

    public function importtemplatedesignationAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $data = $this->_request->getPost();
        $template = new Model_PmTemplate();
        $res = $template->getTemplateByBuildingId($data, $user_id);
        if ($res) {
            echo 'true';
        }
        exit();
    }
    
    public function equipmentdetailtaskstartdateofmonthrootAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $eqp_detail_id = $param['eqp_detail_id'];
        $this->view->eqp_detail_id = $eqp_detail_id;
    }
    
    public function updateequipmentdetailtaskrootstartdateofmonthAction(){
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $eqp_detail_id = $data['eqp_detail_id'];
        $updata['Startdate_month'] = $data['startdateofmonth'];
        $result = $task->updateonlystartdateofmonthequipmentdetailtaskroot($updata, $eqp_detail_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();
        
        
    }
    
    public function equipmentdetailtaskstartdateofmonthsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $eqp_detail_id = $param['eqp_detail_id'];
        $parent_id = $param['subset_id'];
        $this->view->eqp_detail_id = $eqp_detail_id;
        $this->view->parent_id = $parent_id;
    }
    
    public function updateequipmentdetailtasksubsetstartdateofmonthAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata = array();
        $parent_id = $data['parent_id'];
        $eqp_detail_id = $data['eqp_detail_id'];
        $updata['startdate_month'] = $data['startdateofmonth'];
        $result = $task->updateonlystartdateofmonthequipmentdetailtasksubset($updata, $eqp_detail_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date of month Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }
    
    public function equipmentdetailreadingstartdateofmonthrootAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->_request->getParams();
        $eqp_detail_id = $param['eqp_detail_id'];
        $this->view->eqp_detail_id = $eqp_detail_id;
    }
    
    public function updateequipmentdetailreadingrootstartdateofmonthAction(){
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $includesubset = $data['includesubset'];
        $eqp_detail_id = $data['eqp_detail_id'];
        $updata['Startdate_month'] = $data['startdateofmonth'];
        $result = $task->updateonlystartdateofmonthequipmentdetailreadingroot($updata, $eqp_detail_id, $includesubset);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date Sucessfully';
        }
        echo json_encode($msg);
        exit();      
        
    } 
    
    public function equipmentdetailreadingstartdateofmonthsubsetAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $eqp_detail_id = $param['eqp_detail_id'];
        $parent_id = $param['subset_id'];
        $this->view->eqp_detail_id = $eqp_detail_id;
        $this->view->parent_id = $parent_id;
    }
    
    public function updateequipmentdetailreadingsubsetstartdateofmonthAction() {
        $data = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $updata = array();
        $parent_id = $data['parent_id'];
        $eqp_detail_id = $data['eqp_detail_id'];
        $updata['startdate_month'] = $data['startdateofmonth'];
        $result = $task->updateonlystartdateofmonthequipmentdetailreadingsubset($updata, $eqp_detail_id, $parent_id);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Update Start Date of month Sucessfully';
        }
        echo json_encode($msg);
        exit();
    }
    
    /**
     * This is for Mannually create wo's
     */
    public function pmmanuallycreatewoAction() {
        $building_id = $this->_getParam('buiild_id', '');
        $this->_helper->layout()->setLayout('popuplayout');
        $pmTemplate = new Model_PmTemplate();
        $manuallyGenerateWO = $pmTemplate->getManuallyGenerateWO($building_id);
        $this->view->manuallyGenerateWO = $manuallyGenerateWO;
        
    }
    
    /**
     * This is for save the Pm Generate WO's data
     */
    public function savepmgeneratewoAction() {
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
        
        foreach ($companyListing as $cb) {
            if ($cb['build_id'] == $select_build_id) {
                    $uniqueCostCenter = $cb['uniqueCostCenter'];
                }
        }

        $data = $this->_request->getPost();
        
        $insertData = array('Building_ID'=>$select_build_id,
                'uniqueCostCenter'=> $uniqueCostCenter,
            'user_id'=> $user_id,
            'PM_MAN_Date'=> date("Y-m-d", strtotime($data['wogenerateddate']))
            
        );
        $result = $pmTemplate->insertpmgeneratewo($insertData);
        //$result = true;
        if ($result == true) {
           $data = array('message'=>true,'PM_MAN_DATE'=> date("m/d/Y", strtotime($data['wogenerateddate'])), 'Key_Building_Number'=> $select_build_id, 'User_Id'=> $user_id, 'Cost_Center_Number'=> $uniqueCostCenter);
           $_SESSION['Key_Building_Number'] = $select_build_id;
           $_SESSION['User_ID'] = $user_id;
           $_SESSION['Cost_Center_Number'] = $uniqueCostCenter;
           $_SESSION['PM_Man_Date'] = date("m/d/Y", strtotime($data['wogenerateddate']));
           //$data = array('message'=>'success');
           echo $character = json_encode($data);
            //echo 'added';
        } else {
            $data = array('message'=>'fail');
           echo $character = json_encode($data);
            //echo 'false';
        }
        exit();
    }
    
    /**
     * This is for Notes / Comments of PM-Complete wo's
     */
    public function notesforpmcompletewoAction() {
        $pmTemplate = new Model_PmTemplate();
        $params = $this->getRequest()->getParams();        
        $this->_helper->layout()->setLayout('popuplayout');
        $res = $pmTemplate->getNotesofpmcompletewo($params['build_id'], $params['pm_wo_no']);
        if(count($res)>0){
            $this->view->notes = $res[0]->PM_WO_Notes;
            $this->view->PM_WO_Notes_ID = $res[0]->PM_WO_Notes_ID;           
        }
        $this->view->params = $params;    
        
    }
    
    /**
     * This is for save the Notes / Comments WO's data
     */
    public function savenotespmcompletewoAction() {
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
        $data = $this->_request->getPost();
        $updateData = array('PM_WO_Notes'=> $data['notes']);
        if(!empty($data['pm_wo_notes_id'])){
            $result = $pmTemplate->updatenotespmcompletewo($updateData,$data['pm_wo_notes_id']);
        
        } else {
           $pmauwodata = $pmTemplate->getWorkOrderData($data['pm_wo_no'],$select_build_id);
            $insertData = array('Building_ID'=> $select_build_id,
                'PM_WO_Notes'=> $data['notes'],
            'PM_WO_Number'=> $data['pm_wo_no'],
                'AU_Equipment_Detail_ID'=> $pmauwodata[0]->AU_Equipment_Detail_ID,
                'AU_Template_Designation_ID'=> $pmauwodata[0]->AU_Template_Designation_ID
        );
        $result = $pmTemplate->insertnotespmcompletewo($insertData);
            
        }        
        //$result = true;
        if ($result == true) {
           $data = array('message'=>'success');
           echo $character = json_encode($data);            
        } else {
            $data = array('message'=>'fail');
           echo $character = json_encode($data);            
        }
        exit();
    }
    
    /**
     * This is for Photo's / Documents of PM-Complete wo's
     */
    public function documentsforpmcompletewoAction() {
        $pmTemplate = new Model_PmTemplate();
        $params = $this->getRequest()->getParams();        
        $this->_helper->layout()->setLayout('popuplayout');
        //$res = $pmTemplate->getNotesofpmcompletewo($params['build_id'], $params['pm_wo_no']);
        /* if(count($res)>0){
            $this->view->notes = $res[0]->PM_WO_Notes;
            $this->view->PM_WO_Notes_ID = $res[0]->PM_WO_Notes_ID;           
        }*/
        
        $photosData = $pmTemplate->getHistoryPhotosWOData($params['pm_wo_no'],$params['build_id']);
        $this->view->photosData = $photosData;
        $this->view->params = $params;   
        
    }
    
    /**
     * This is for save the Photos / Documents WO's data
     */
    public function savedocumentspmcompletewoAction() {
        $pmTemplate = new Model_PmTemplate();
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $cust_id = $_SESSION['Zend_Auth']['storage']->cust_id;
        $role_id = $_SESSION['Zend_Auth']['storage']->role_id;
        $this->_helper->layout()->setLayout('popuplayout');
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
        
        $uploaddir = IMAGE_UPLOAD_DIR . '/public/pm/historyPhotos/';
        $filename = basename($_FILES['file']['name']['photos']);
        $dd = move_uploaded_file($_FILES['file']['tmp_name']['photos'], $uploaddir . '' . $filename);
        
        $data = $this->_request->getPost();
        
        $pmauwodata = $pmTemplate->getWorkOrderData($data['pm_wo_no'],$select_build_id);
        
        $insertData = array('Building_ID'=> $select_build_id,
            'AU_Equipment_Detail_ID'=> $pmauwodata[0]->AU_Equipment_Detail_ID,
            'AU_Template_Designation_ID'=> $pmauwodata[0]->AU_Template_Designation_ID,
            'PM_WO_Photo'=> $filename,
            'PM_WO_Number'=> $data['pm_wo_no']           
        );                
        $result = $pmTemplate->insertphotospmcompletewo($insertData);
        $this->_helper->viewRenderer('documentsforpmcompletewo');        
    }
    
    /**
     * Delete photos / Documents
     */
    
    public function deleteedocumenthistorywoAction() {
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();
        $result = $template->deletedocumenthistorywo($param['pm_wo_photo_id']);
        if (empty($result)) {
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for Delted photos / documents';
        } else {
            $msg['status'] = 'success';
            $msg['msg'] = 'Photos / Documents Deleted sucessfully';
        }
        echo json_encode($msg);
        exit();
    }
    public function searchtemplateAction() {
        $data = $this->_request->getParams();
		$user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $cust_id = $_SESSION['Zend_Auth']['storage']->cust_id;
        $role_id = $_SESSION['Zend_Auth']['storage']->role_id;
        
        $build_ID = $_SESSION['current_building'];
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
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $build_ID = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");


        if ($companyListing != '') {
            if ($build_ID != '')
                $build_ID = $build_ID;
            else
                $build_ID = $companyListing[0]['build_id'];
        }

        $pmtemplate = new Model_PmTemplate();
       
        $AllEquipment = $pmtemplate->getTemplateDetailForHistory($data);
        $design = $pmtemplate->GetallAUTemplateDetails($build_ID);
        foreach($design as $designs){	
        if(!empty($AllEquipment))		
		//$designs->AU_Template_Name_IDD = $AllEquipment[0]->AU_Template_Name_IDD;
		//Lari code
	   	$designs->AU_Template_Name_IDD = $AllEquipment[0]->AU_Template_desi_IDD;
        else	
		$designs->AU_Template_Name_IDD = '';
		 }
        echo json_encode($design);
        die;
    }
}
?>  
