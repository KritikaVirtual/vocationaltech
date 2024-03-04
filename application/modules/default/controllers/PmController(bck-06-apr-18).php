<?php
/**
 * Description of Access Controller
 *
 * @author sanjay
 */
ob_start();
class PmController extends Ve_Controller_Base {

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
        $this->accessHelper = $this->_helper->access;
    }

    
    public function indexAction() {
        //$this->_redirect('/pm/matrix');   
        $templateName = "";
        $designationName = "";
        $data = $this->_request->getPost();
        $template = new Model_PmTemplate();
        $templatedata = array();
        if($data['search']=='Search'){
            $templateName = $data['templatename'];
            $designationName = $data['designationname'];
            $tempdata = $template->GetAllTemplateName($templateName);
            foreach($tempdata as $temp){
                $find = $template->GetTemplateDetails($temp->VT_Template_Name_ID,$designationName);
                //echo $designationName;
                //print_r($find);
                //die;
                if(!empty($find) && $designationName!=""){
                    $templatedata[$temp->VT_Template_Name_ID][VT_Template_Name] = $temp->VT_Template_Name;
                    $templatedata[$temp->VT_Template_Name_ID][VT_Template_Name_ID] = $temp->VT_Template_Name_ID;
                    $templatedata[$temp->VT_Template_Name_ID][VT_TypeDesignation] = $template->GetTemplateDetails($temp->VT_Template_Name_ID,$designationName);
                }else if($designationName==""){
                    $templatedata[$temp->VT_Template_Name_ID][VT_Template_Name] = $temp->VT_Template_Name;
                    $templatedata[$temp->VT_Template_Name_ID][VT_Template_Name_ID] = $temp->VT_Template_Name_ID;
                    $templatedata[$temp->VT_Template_Name_ID][VT_TypeDesignation] = $template->GetTemplateDetails($temp->VT_Template_Name_ID,$designationName);
                }

            }
        }else{
            $tempdata = $template->GetAllTemplateName();            
            foreach($tempdata as $temp){ 
                    $templatedata[$temp->VT_Template_Name_ID][VT_Template_Name] = $temp->VT_Template_Name;
                    $templatedata[$temp->VT_Template_Name_ID][VT_Template_Name_ID] = $temp->VT_Template_Name_ID;
                    $templatedata[$temp->VT_Template_Name_ID][VT_TypeDesignation] = $template->GetTemplateDetails($temp->VT_Template_Name_ID,$designationName);
            }
        }
        $this->view->templatedetails = $templatedata;
        $this->view->templateName = $templateName;
        $this->view->designationName = $designationName;
    }

    public function createdesignationAction(){
        $this->_helper->layout()->setLayout('popuplayout');
        $template = new Model_PmTemplate();
        $tempdata = $template->GetAllTemplateName();        
        // send data to view pages
        $this->view->templats = $tempdata;
    }
    public function createtemplateAction(){
        //$this->_helper->layout()->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');
        
    }
    
    // validate template before creation
    public function validatetemplateAction()
    {
        //$param = $this->getRequest()->getParams();
        $param = $this->_request->getPost();
        $template_Name = $param['TemplateName'];
        $template_id = $param['Template_id'];
        $template = new Model_PmTemplate();
        $result = $template->GetTemplateByName($template_Name,$template_id);
        if(empty($result)){
            echo  'true';
        }else{
            echo 'false';
        }
        exit();
    }
    
    // Save template Name
    public function savetemplateAction()
    {
        $msg = array();
        $templatedata = array();
        $template = new Model_PmTemplate();
        $param = $this->_request->getPost();
        //$param = $this->getRequest()->getParams();
        $templatedata['VT_Template_Name'] = $param['TemplateName'];
        $result = $template->InsertTemplateName($templatedata);
        //print_r($result);
        if(empty($result)){
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        }else{
            $msg['status'] = 'success';
            $msg['msg'] = 'Temaplate save sucessfully';
        }
        echo json_encode($msg);
        exit();
    }
    
    
    //  Validate Type designation  //
    public function validatetypedesignationAction(){
        $data = $this->_request->getPost();
        //$param = $this->getRequest()->getParams();
        $typedesignation    = $data['typedesignation'];
        $typedesination_id  = $data['typedesination_id'];
        $template = new Model_PmTemplate();
        $result = $template->GetTemplateIdByTypeDesignation($typedesignation,$typedesination_id);
        if(empty($result)){
            echo  'true';
        }else{
            echo 'false';
        }
        exit();
        
    }
    
    /* save Type designation */ 
    public function savetypedesignationAction(){
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
        if(empty($result)){
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        }else{
            $msg['status'] = 'success';
            $msg['msg'] = 'Type Designation save sucessfully';
            $msg['id'] = $result; 
        }
        echo json_encode($msg);
        exit();
    }
    
    // edit template name
    
    public function edittemplateAction(){
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();
        $template_id = $param['template_id'];
        $template = new Model_PmTemplate();
        $templatedata = $template->GetTemplateNameById($template_id);
        $templatedata = $templatedata[0];
        $this->view->template = $templatedata;
        
    }
    
    // update template Name 
    public  function updatetemplateAction(){
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();       
        $param = $this->_request->getPost();
        $typedata['VT_Template_Name'] = $param['TemplateName'];
        $result = $template->updatetemplate($typedata,$param['Template_id']);
        if(empty($result)){
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        }else{
            $msg['status'] = 'success';
            $msg['msg'] = 'Template Update sucessfully';
        }
        echo json_encode($msg);
        exit();
    }
    
    // Delete Template 
    public function deletetemplateAction(){
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();       
        $param = $this->_request->getPost();
        $result = $template->deleteTemplate($param['Template_id']);
        if(empty($result)){
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        }else{
            $template->deleteTypeDesignationByTemplateId($param['Template_id']);
            $msg['status'] = 'success';
            $msg['msg'] = 'Template Deleted sucessfully';
        }
        echo json_encode($msg);
        exit();
    }
    
    /* Edit  designation */
    public function editdesignationAction()
    {
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
    public function updatetypedesignationAction(){
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();       
        $param = $this->_request->getPost();
        $typedata['VT_Template_Name_ID'] = $param['template_id'];
        $typedata['VT_TypeDesignation'] = $param['typedesignation'];
        $typedata['VT_TypeDescritpion'] = $param['typedescription'];
        $result = $template->updatetypedesignation($typedata,$param['typedesination_id']);
        if(empty($result)){
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        }else{
            $msg['status'] = 'success';
            $msg['msg'] = 'Type Designation Update sucessfully';
        }
        echo json_encode($msg);
        exit();
    }
    /* delete type designation */
    public function deletetypedescriptionAction()
    {
        $msg = array();
        $template = new Model_PmTemplate();       
        $data = $this->_request->getPost();
        $result = $template->deleteTypeDesignation($data['type_id']);
        if(empty($result)){
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        }else{
            $msg['status'] = 'success';
            $msg['msg'] = 'Template Deleted sucessfully';
        }
        echo json_encode($msg);
        exit();
    }
    
    // Task Section start
  
    /* Create a task */
    public function createtaskAction()
    {
        $param = $this->getRequest()->getParams();
        $desig_id = $param['desig_id'];
        $subset = new Model_PmTemplate();
        if(!empty($desig_id)){            
            $allsubset = $subset->GetAllSubset($desig_id);
            $msg = array();
            $alltask = array();
            $getalltask = $subset->GetAlltaskparent($desig_id);
            foreach($getalltask as $sub){
                $subtask = $subset->GetTaskBysubsetId($sub->VT_Template_Task_ID);
                if(!empty($subtask)){
                    $alltask[$sub->VT_Template_Task_ID]['name'] = $sub->Task_Instruction;
                    $alltask[$sub->VT_Template_Task_ID]['id'] = $sub->VT_Template_Task_ID;
                    $alltask[$sub->VT_Template_Task_ID]['task'] = $subtask;
                }else{
                    $alltask[][] = $sub;
                }                
            }
            $list = $subset->get_view_table('task');
            $listview = explode(',',$list[0]->display_table_view);
            $freq = array();
            $CustFreq = array();
            $frequency = $subset->Getallfrequency();
            foreach($frequency as $val){
                        if($val->column==1)
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                        if($val->column==2)
                            $Intreval[$val->AU_Frequency_ID] = $val->Name;
            }
            $startdate_ad = array();
            $startdateadjustment = $subset->Getallstartdateadjustment();
            foreach($startdateadjustment as $val){
                  $startdate_ad[$val->AU_sda_ID] = $val->Name;  
            }

            $alljobtime = array();
            $jobtime = $subset->Getalljobtime();
            foreach($jobtime as $val){
                  $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
            }
            
            // get template data 
            $template_data = $subset->GettemplateByTypeDesignationID($desig_id);
            $template_data =  $template_data[0];
            
            
            // send data in view pages
            $this->view->desig_id = $desig_id;
            $this->view->alltask = $alltask;
            $this->view->subset = $allsubset;
            $this->view->listview = $listview;
            $this->view->frequency  = $freq;
            $this->view->CustmeFreq = $Intreval;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;
            $this->view->templateData = $template_data;
        }else{            
            exit();
        }
        
    }  
    
     // edit task section 
        public function viewtaskAction(){
            $this->_helper->getHelper('layout')->disableLayout();                  
            //$param = $this->getRequest()->getParams();
            $param = $this->_request->getPost();
            $desig_id = $param['desig_id'];

            $subset = new Model_PmTemplate();
            if(!empty($desig_id)){            
                $allsubset = $subset->GetAllSubset($desig_id);
                //die;
                //$data = $this->_request->getPost();
                $msg = array();
                $alltask = array();
                $view_empty_subset = array();
                $getalltask = $subset->GetAlltaskparent($desig_id);
                //print_r($getalltask);
                foreach($getalltask as $sub){
                    $subtask = $subset->GetTaskBysubsetId($sub->VT_Template_Task_ID);
                    if(!empty($subtask)){
                        $alltask[$sub->VT_Template_Task_ID]['name'] = $sub->Task_Instruction;
                        $alltask[$sub->VT_Template_Task_ID]['id'] = $sub->VT_Template_Task_ID;
                        $alltask[$sub->VT_Template_Task_ID]['task'] = $subtask;
                    }else if(empty($sub->AU_Frequency_ID)){
                        $view_empty_subset[$sub->VT_Template_Task_ID]['name'] = $sub->Task_Instruction;
                        $view_empty_subset[$sub->VT_Template_Task_ID]['id'] = $sub->VT_Template_Task_ID;
                        $view_empty_subset[$sub->VT_Template_Task_ID]['task'] = "";
                    }else{
                        $alltask[][] = $sub;
                    }                       
                }
                $alltask = array_merge($alltask,$view_empty_subset);
                $listview = explode(',',$param['viewlist']);
                $freq = array();
                $CustFreq = array();
                $frequency = $subset->Getallfrequency();
                foreach($frequency as $val){
                            if($val->column==1)
                                $freq[$val->AU_Frequency_ID] = $val->Name;
                            if($val->column==2)
                                $Intreval[$val->AU_Frequency_ID] = $val->Name;
                }

                $startdate_ad = array();
                $startdateadjustment = $subset->Getallstartdateadjustment();
                foreach($startdateadjustment as $val){
                      $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                }

                $alljobtime = array();
                $jobtime = $subset->Getalljobtime();
                foreach($jobtime as $val){
                      $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
                }

                // send data in view pages
                $this->view->desig_id = $desig_id;
                $this->view->alltask = $alltask;
                $this->view->subset = $allsubset;
                $this->view->listview = $listview;
                $this->view->frequency  = $freq;
                $this->view->CustmeFreq = $Intreval;
                $this->view->startdateadjustment = $startdate_ad;
                $this->view->jobtime = $alljobtime;

                }else{            
                    exit();
                    //return false;
                }
        }
        
    // view add new task 
    public function addtaskAction(){
        //$this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');
        $subset = new Model_PmTemplate();
        $data = $this->_request->getParams();
        //echo $data['desig_id'];
        $desig_id  = $data['desig_id'];
        $allsubset = $subset->GetAllSubset($desig_id);
        //print_r($allsubset);
       // die;
        $freq = array();
        $frequency = $subset->Getallfrequency();
        foreach($frequency as $val){
                        if($val->column==1)
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                        if($val->column==2)
                            $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }
        $startdatead = array();
        $startdateadjustment = $subset->Getallstartdateadjustment();
        foreach($startdateadjustment as $val){
              $startdatead[$val->AU_sda_ID] = $val->Name;  
        }

        $alljobtime = array();
        $jobtime = $subset->Getalljobtime();
        foreach($jobtime as $val){
              $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
        }
        
        /* Reading section */ 
        $ReadingSubset = $subset->GetAllSubset_reading($desig_id);
        
        $this->view->frequency  = $freq;
        $this->view->Interval  = $Intreval;
        $this->view->startdateadjustment = $startdatead;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
        $this->view->ReadingSubset = $ReadingSubset;
    }
    
     // view add new task 
    public function addreadingAction(){
        //$this->_helper->getHelper('layout')->disableLayout();
        $this->_helper->layout()->setLayout('popuplayout');
        $subset = new Model_PmTemplate();
        $data = $this->_request->getParams();
        //echo $data['desig_id'];
        $desig_id  = $data['desig_id'];        
        $allsubset = $subset->GetAllSubset_reading($desig_id);
        //print_r($allsubset);
       // die;
        $freq = array();
        $frequency = $subset->Getallfrequency();
        foreach($frequency as $val){
            if($val->column==1)
              $freq[$val->AU_Frequency_ID] = $val->Name;
            if($val->column==2)
              $Intreval[$val->AU_Frequency_ID] = $val->Name;
        }
        $startdatead = array();
        $startdateadjustment = $subset->Getallstartdateadjustment();
        foreach($startdateadjustment as $val){
              $startdatead[$val->AU_sda_ID] = $val->Name;  
        }

        $alljobtime = array();
        $jobtime = $subset->Getalljobtime();
        foreach($jobtime as $val){
              $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
        }
        
        $allunitofmeasure = array();
        $unitofmeasure = $subset->Getallunitofmeasure();
        foreach($unitofmeasure as $val){
              $allunitofmeasure[$val->AU_uom_ID] = $val->Name;  
        }
        $this->view->frequency  = $freq;
        $this->view->Interval  = $Intreval;
        $this->view->startdateadjustment = $startdatead;
        $this->view->unitofmeasure = $allunitofmeasure;
        $this->view->jobtime = $alljobtime;
        $this->view->desig_id = $desig_id;
        $this->view->subset = $allsubset;
    }
    
    /* Add a new subset */
    public function createsubsetAction()
    {
        $this->_helper->layout()->disableLayout();
        $param = $this->_request->getPost();
        $desig_id = $param['desig_id'];
        
        // send data in view pages
         $this->view->desig_id = $desig_id;
    }
    
    /* Update View Task */
    public function updateviewtaskAction(){
        $param = $this->_request->getPost();
        $task = new Model_PmTemplate();
        $update = array();
        $update['display_table_view'] = $param['viewlist'];
        //$update['pm_type'] = $param['type'];        
        $result = $task->Updateviewlist($update,$param['type']);
        if(!empty($result)){
            echo  'true';
        }else{
            echo 'false';
        }
        exit();
        
    }
    
    /* validate subset */
    public function validatesubsetAction()
    {
        $data = $this->_request->getPost();
        $subsetname    = $data['subsetname'];
        $subsetname_id  = $data['subsetname_id'];
        $desig_id       =$data['desig_id'];
        $subset = new Model_PmTemplate();
        $result = $subset->GetSubseteByName($subsetname,$subsetname_id,$desig_id);
        if(empty($result)){
            echo  'true';
        }else{
            echo 'false';
        }
        exit();
    } 
    
    /* Save a new subset */
    public  function savesubsetAction()
    {
        $msg = array();
        $subsetdata = array();
        $subset = new Model_PmTemplate();        
        $data = $this->_request->getPost();
        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $data['user_id'] = $user_id;
        $result = $subset->insertsubset($data);
        if(empty($result)){
            $msg['status'] = 'error';
            $msg['msg'] = 'Error for save data';
        }else{
            $msg['status'] = 'success';
            $msg['msg'] = 'Subset save sucessfully';
            $msg['InsertId'] = $result;
        }
        echo json_encode($msg);
        exit();
    }
    
    /* Edit subset */
    public function editsubsetAction(){
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
    public function updatesubsetAction(){
        $data = $this->_request->getPost();
        if(!empty($data)){
            $task = new Model_PmTemplate();
            $task_id = $data['subsetname_id'];
            unset($data['subsetname_id']);
            $result = $task->Updatetask($data,$task_id);
            if(empty($result)){
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            }else{
                $msg['status'] = 'success';
                $msg['msg'] = 'Update subset save sucessfully';
            }
            echo json_encode($msg);
            exit();
       }
    }
    
    public function customefrequencyAction(){
        $this->_helper->layout()->setLayout('popuplayout');
        $param = $this->getRequest()->getParams();       
        $freq = $param['frequency'];        
        $GetData = "";
        $task = new Model_PmTemplate();
        if($freq!=""){
            $GetData = $task->get_FrequencydataByID($freq);
            $GetData = $GetData[0];
        }
        $this->view->GetData = $GetData;
    }
    
    public function customefreqAction(){
        $msg = array();
        $data = $this->_request->getPost();
        $_SESSION['custome_freq'] = $data;
        $msg['status'] = 'success';
        $msg['msg'] = 'Frequency save sucessfully';
        echo json_encode($msg);
        exit();
    }
    
    public function savetaskAction(){
       $data = $this->_request->getPost();
        if(!empty($data)){
            $task = new Model_PmTemplate();
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['user_id'] = $user_id;
            // get maximum view oder number paramiter table name
            $view_order = $task->Get_MaxViewOrder("pm_vt_template_task");
            $view_order = $view_order[0]->View_order; 
            $data['View_order'] = $view_order + 1;
            $result = $task->Inserttask($data);
            if(empty($result)){
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            }else{
                $msg['status'] = 'success';
                $msg['msg'] = 'Task save sucessfully';
            }
            echo json_encode($msg);
            exit();
       }       
       //die;
    }
    
    // update task data
    public function updatetaskAction(){
        $data = $this->_request->getPost();
        if(!empty($data)){
            $task = new Model_PmTemplate();
            $task_id = $data['task_id'];
            unset($data['task_id']);
            $result = $task->Updatetask($data,$task_id);
            if(empty($result)){
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            }else{
                $msg['status'] = 'success';
                $msg['msg'] = 'Update Task save sucessfully';
            }
            echo json_encode($msg);
            exit();
       } 
    } 
    public function updatetaskorderAction()
            {
        
                    $data = $this->_request->getPost();
                    $task = json_decode($data[task]);
                    $taskmodule = new Model_PmTemplate();
                    $return = $this->validationdragandrop($task);
                    $order = 1;
                    if($return==1){
                        foreach($task as $val){
                            //print_r($val);
                            if(!empty($val->children) && $val->idSubset){
                                        $getdata = array("view_order"=>$order);
                                        $taskmodule->Updateodrder($getdata,$val->idSubset);
                                        $order++;
                                foreach($val->children as $data){

                                    if(!empty($data->idRoot)){
                                        //echo "idroot children";
                                        $getdata = array("view_order"=>$order,"Parent_ID"=>$val->idSubset);
                                        $taskmodule->Updateodrder($getdata,$data->idRoot);
                                    }
                                    if(!empty($data->id)){
                                        //echo "idroot children";
                                        $getdata = array("view_order"=>$order,"Parent_ID"=>$val->idSubset);
                                        $taskmodule->Updateodrder($getdata,$data->id);
                                    }                               

                                    $order++;
                                }
                            }else{

                                if(!empty($val->idRoot)){
                                    //echo "root";
                                    $getdata = array("view_order"=>$order,"Parent_ID"=>0);
                                    $taskmodule->Updateodrder($getdata,$val->idRoot);
                                }

                                if(!empty($val->id)){
                                    //echo "id";
                                    $getdata = array("view_order"=>$order,"Parent_ID"=>0);
                                    $taskmodule->Updateodrder($getdata,$val->id);
                                }                                
                                $order++;
                            }
                        }
                    }
                    if($return==0){
                        $msg['status'] = 'error';
                        $msg['msg'] = 'Error : This move not posible please try other';
                    }else{
                        $msg['status'] = 'success';
                        $msg['msg'] = 'Order save sucessfully';
                    }
                    echo json_encode($msg);
                    exit();  
            }
                
             
            

            
            // edit task section 
            public function edittaskAction(){
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
                foreach($frequency as $val){
                           if($val->column==1)
                                $freq[$val->AU_Frequency_ID] = $val->Name;
                              if($val->column==2)
                                $Intreval[$val->AU_Frequency_ID] = $val->Name;
                        }

                $startdate_ad = array();
                $startdateadjustment = $subset->Getallstartdateadjustment();
                foreach($startdateadjustment as $val){
                      $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                }

                $alljobtime = array();
                $jobtime = $subset->Getalljobtime();
                foreach($jobtime as $val){
                      $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
                }

                $this->view->frequency  = $freq;                
                $this->view->Interval  = $Intreval;
                $this->view->startdateadjustment = $startdate_ad;
                $this->view->jobtime = $alljobtime;
                $this->view->desig_id = $desig_id;
                $this->view->subset = $allsubset;
                $this->view->taskdata = $TaskData;
            }
            
            /// Delete tasking 
            public function deletetaskAction(){
                $msg = array();
                $typedata = array();
                $task = new Model_PmTemplate();       
                $param = $this->_request->getPost();
                $parent_id = $task->getallparent($param['Task_id']);
                $result = $task->deleteTask($param['Task_id']); 
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $update = array("Parent_ID"=>"");
                    $result = $task->UpdateTaskByparent($update,$param['Task_id']);
                    //$result = $task->deleteTaskByParentId($param['Task_id']); 
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Template Deleted sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            
    /* Reading Section start */ 
    
    public function createreadingAction(){
        $param = $this->getRequest()->getParams();
        $desig_id = $param['desig_id'];
        if(!empty($desig_id)){
            $subset = new Model_PmTemplate();
            $allsubset = $subset->GetAllSubset_reading($desig_id);
            //die(12);
            $data = $this->_request->getPost();
            $msg = array();
            $list = $subset->get_view_table('Reading');
            $listview = explode(',',$list[0]->display_table_view);
            $allreading = array();
            $getallreading = $subset->GetAllReadingParent($desig_id);
            foreach($getallreading as $sub){
                $subreading = $subset->GetReadingBysubsetId($sub->VT_Template_Reading_ID);
                if(!empty($subreading)){
                    $allreading[$sub->VT_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                    $allreading[$sub->VT_Template_Reading_ID]['id'] = $sub->VT_Template_Reading_ID;
                    $allreading[$sub->VT_Template_Reading_ID]['task'] = $subreading;
                }else{
                    $allreading[][] = $sub;
                }                
            }
            
            $freq = array();
            $frequency = $subset->Getallfrequency();
            foreach($frequency as $val){
                        if($val->column==1)
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                        if($val->column==2)
                            $Intreval[$val->AU_Frequency_ID] = $val->Name;
            }

            $startdate_ad = array();
            $startdateadjustment = $subset->Getallstartdateadjustment();
            foreach($startdateadjustment as $val){
                  $startdate_ad[$val->AU_sda_ID] = $val->Name;  
            }

            $alljobtime = array();
            $jobtime = $subset->Getalljobtime();
            foreach($jobtime as $val){
                  $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
            }

            $allunitofmeasure = array();
            $unitofmeasure = $subset->Getallunitofmeasure();
            foreach($unitofmeasure as $val){
                  $allunitofmeasure[$val->AU_uom_ID] = $val->Name;  
            }
            
            // get template data 
            $template_data = $subset->GettemplateByTypeDesignationID($desig_id);
            $template_data =  $template_data[0];

            //die;
            /* send data in view pages */            
            $this->view->frequency  = $freq;
            $this->view->CustmeFreq = $CustFreq;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;
            $this->view->unitofmeasure = $allunitofmeasure;
            $this->view->desig_id = $desig_id;
            $this->view->allreading = $allreading;
            $this->view->subset = $allsubset;
            $this->view->listview = $listview;
            $this->view->templateData = $template_data;
        }else{            
            exit();
            //return false;
        }
    }
    
    public function savereadingAction(){
       $data = $this->_request->getPost();
       //print_r($data);
       
        if(!empty($data)){
            $task = new Model_PmTemplate();
            $user_id = $_SESSION['Zend_Auth']['storage']->uid;
            $data['user_id'] = $user_id;
            // get maximum view oder number paramiter table name
            $view_order = $task->Get_MaxViewOrder("pm_vt_template_reading");
            $view_order = $view_order[0]->View_order; 
            $data['View_order'] = $view_order + 1;
            
            $result = $task->InsertReading($data);
            if(empty($result)){
                $msg['status'] = 'error';
                $msg['msg'] = 'Error for save data';
            }else{
                $msg['status'] = 'success';
                $msg['msg'] = 'Reading save sucessfully';
            }
            echo json_encode($msg);
            exit();
       }       
       //die;
    }
    
    // edit task section 
            public function editreadingAction(){
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
                foreach($frequency as $val){
                        if($val->column==1)
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                        if($val->column==2)
                            $Intreval[$val->AU_Frequency_ID] = $val->Name;
                }
                $startdatead = array();
                $startdateadjustment = $subset->Getallstartdateadjustment();
                foreach($startdateadjustment as $val){
                      $startdatead[$val->AU_sda_ID] = $val->Name;  
                }

                $alljobtime = array();
                $jobtime = $subset->Getalljobtime();
                foreach($jobtime as $val){
                      $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
                }

                $allunitofmeasure = array();
                $unitofmeasure = $subset->Getallunitofmeasure();
                foreach($unitofmeasure as $val){
                      $allunitofmeasure[$val->AU_uom_ID] = $val->Name;  
                }
                $this->view->frequency  = $freq;
                $this->view->Interval  = $Intreval;
                $this->view->startdateadjustment = $startdatead;
                $this->view->unitofmeasure = $allunitofmeasure;
                $this->view->jobtime = $alljobtime;
                $this->view->desig_id = $desig_id;
                $this->view->subset = $allsubset;
                $this->view->readingdata = $ReadingData[0];

            }
            
            /// Delete tasking 
            public function deletereadingAction(){
                $msg = array();
                $typedata = array();
                $reading = new Model_PmTemplate();       
                $param = $this->_request->getPost();
                //$parent_id = $task->getallparent($param['Task_id']);
                $result = $reading->deleteReading($param['reading_id']); 
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $update = array("Parent_ID"=>"");
                    $result = $reading->UpdateReadingByparent($update,$param['reading_id']);
                    //$result = $reading->deleteReadingByParentId($param['reading_id']); 
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Template Deleted sucessfully';
                }
                echo json_encode($msg);
                exit();
                
            }
            // update task data
            public function updatereadingAction(){
                $data = $this->_request->getPost();
                //print_r($data); 
               //die;
                if(!empty($data)){
                    $task = new Model_PmTemplate();
                    $reading_id = $data['reading_id'];
                    $data['Parent_ID']=$data['parent_id'];
                    unset($data['reading_id']);
                    unset($data['parent_id']);
                    //print_r($data);
                    $result = $task->Updatereading($data,$reading_id);
                    if(empty($result)){
                        $msg['status'] = 'error';
                        $msg['msg'] = 'Error for save data';
                    }else{
                        $msg['status'] = 'success';
                        $msg['msg'] = 'Update Reading save sucessfully';
                    }
                    echo json_encode($msg);
                    exit();
               } 
            }
            
           
            
            public function updatereadingorderAction()
            {        
                $data = $this->_request->getPost();
                $task = json_decode($data['reading']);
                $taskmodule = new Model_PmTemplate();
                 $return = $this->validationdragandrop($task);
                 $order = 1;
                 
                if($return==1){
                    foreach($task as $val){
                        //print_r($val);
                        //die;
                        if(!empty($val->children) && $val->idSubset){
                                    $getdata = array("View_order"=>$order);
                                    $taskmodule->Updateodrderreading($getdata,$val->idSubset);
                                    $order++;
                            foreach($val->children as $data){
                               
                                if(!empty($data->idRoot)){
                                    //echo "idroot children";
                                    $getdata = array("View_order"=>$order,"Parent_ID"=>$val->idSubset);
                                    $taskmodule->Updateodrderreading($getdata,$data->idRoot);
                                }
                                if(!empty($data->id)){
                                    //echo "idroot children";
                                    $getdata = array("View_order"=>$order,"Parent_ID"=>$val->idSubset);
                                    $taskmodule->Updateodrderreading($getdata,$data->id);
                                }                                
                                
                                $order++;
                            }
                        }else{
                            
                            if(!empty($val->idRoot)){
                                //echo "root";
                                $getdata = array("View_order"=>$order,"Parent_ID"=>0);
                                $taskmodule->Updateodrderreading($getdata,$val->idRoot);
                            }
                                
                            if(!empty($val->id)){
                                //echo "id";
                                $getdata = array("View_order"=>$order,"Parent_ID"=>0);
                                $taskmodule->Updateodrderreading($getdata,$val->id);
                            }                                
                            $order++;
                        }
                    }
                }
                if($return==0){
                        $msg['status'] = 'error';
                        $msg['msg'] = 'Error : This move not posible please try other';
                    }else{
                        $msg['status'] = 'success';
                        $msg['msg'] = 'Order save sucessfully';
                    }
                    echo json_encode($msg);
                    exit();  
            }
            
            public function editsubsetreadingAction(){
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
                public function validatesubsetreadingAction()
                {
                    $data = $this->_request->getPost();
                    $subsetname    = $data['subsetname'];
                    $subsetname_id  = $data['subsetname_id'];
                    $subset = new Model_PmTemplate();
                    $result = $subset->GetSubsetreadingByName($subsetname,$subsetname_id);
                    if(empty($result)){
                        echo  'true';
                    }else{
                        echo 'false';
                    }
                    exit();
                }
            public  function savereadingsubsetAction()
            {
                $msg = array();
                $subsetdata = array();
                $subset = new Model_PmTemplate();        
                $data = $this->_request->getPost();
                $insertdata = array();
                $insertdata['Reading_Instruction'] = $data['Reading_Instruction'];
                $insertdata['VT_Template_Designation_ID'] = $data['desig_id'];
                $result = $subset->InsertReading($insertdata);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                    
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Subset save sucessfully';
                    $msg["InsertId"] = $result;
                }
                echo json_encode($msg);
                exit();
            }
            // subset data
    
            public function updatereadingsubsetAction(){
                $data = $this->_request->getPost();
                if(!empty($data)){
                    $task = new Model_PmTemplate();
                    $task_id = $data['subsetname_id'];
                    unset($data['subsetname_id']);
                    $result = $task->Updateodrderreading($data,$task_id);
                    if(empty($result)){
                        $msg['status'] = 'error';
                        $msg['msg'] = 'Error for save data';
                    }else{
                        $msg['status'] = 'success';
                        $msg['msg'] = 'Update subset save sucessfully';
                    }
                    echo json_encode($msg);
                    exit();
               }
            }
            
            /// create subset level 
            public function createreadingsubsetAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $desig_id = $param['desig_id'];
                // send data in view pages
                    $this->view->desig_id = $desig_id;
            }
            
            /* view add reading */
            public function viewaddreadingAction(){
                
                $this->_helper->getHelper('layout')->disableLayout(); 
                $param = $this->getRequest()->getParams();
                
                $desig_id = $param['desig_id'];
                $subset = new Model_PmTemplate();                
                $allsubset = $subset->GetAllSubset_reading($desig_id);
                
                $freq = array();
                $frequency = $subset->Getallfrequency();
                foreach($frequency as $val){
                        if($val->column==1)
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                        if($val->column==2)
                            $Intreval[$val->AU_Frequency_ID] = $val->Name;
                        
                }

                $startdateadj = array();
                $startdateadjustment = $subset->Getallstartdateadjustment();
                foreach($startdateadjustment as $val){
                      $startdateadj[$val->id] = $val->name;  
                }

                $alljobtime = array();
                $jobtime = $subset->Getalljobtime();
                foreach($jobtime as $val){
                      $alljobtime[$val->id] = $val->name;  
                }

                $allunitofmeasure = array();
                $unitofmeasure = $subset->Getallunitofmeasure();
                foreach($unitofmeasure as $val){
                      $allunitofmeasure[$val->id] = $val->name;  
                }

                /* send data in view pages */
                $this->view->frequency  = $freq;
                $this->view->startdateadjustment = $startdateadj;
                $this->view->jobtime = $alljobtime;
                $this->view->unitofmeasure = $allunitofmeasure;
                $this->view->subset = $allsubset;
                $this->view->desig_id = $desig_id;
                
                
            }
            /* View All Reading */
            public function viewreadingAction(){
                $this->_helper->getHelper('layout')->disableLayout();
                $param = $this->getRequest()->getParams();
                $desig_id = $param['desig_id'];
                //die(12);
                $subset = new Model_PmTemplate();
               //print_r($view_order);
                $data = $this->_request->getPost();                
                if(!empty($desig_id)){
                    $allsubset = $subset->GetAllSubset_reading($desig_id);
                    $allreading = array();
                    $view_empty_subset = array();
                    $getallreading = $subset->GetAllReadingParent($desig_id);
                    foreach($getallreading as $sub){
                        $subreading = $subset->GetReadingBysubsetId($sub->VT_Template_Reading_ID);
                        if(!empty($subreading)){
                            $allreading[$sub->VT_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                            $allreading[$sub->VT_Template_Reading_ID]['id'] = $sub->VT_Template_Reading_ID;
                            $allreading[$sub->VT_Template_Reading_ID]['task'] = $subreading;
                        }else if(empty($sub->AU_Frequency_ID)){
                                $view_empty_subset[$sub->VT_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                                $view_empty_subset[$sub->VT_Template_Reading_ID]['id'] = $sub->VT_Template_Reading_ID;

                        }else{
                            $allreading[][] = $sub;
                        }   
                                       
                    }
                    $allreading = array_merge($allreading,$view_empty_subset);
                    $freq = array();
                    $frequency = $subset->Getallfrequency();
                    foreach($frequency as $val){
                        if($val->column==1)
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                        if($val->column==2)
                            $Intreval[$val->AU_Frequency_ID] = $val->Name;
                    }

                    $startdate_ad = array();
                    $startdateadjustment = $subset->Getallstartdateadjustment();
                    foreach($startdateadjustment as $val){
                          $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                    }

                    $alljobtime = array();
                    $jobtime = $subset->Getalljobtime();
                    foreach($jobtime as $val){
                          $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
                    }

                    $allunitofmeasure = array();
                    $unitofmeasure = $subset->Getallunitofmeasure();
                    foreach($unitofmeasure as $val){
                          $allunitofmeasure[$val->AU_uom_ID] = $val->Name;  
                    }

                    $listview = explode(',',$param['viewlist']);
                    
                    /* send data in view pages */            
                    $this->view->frequency  = $freq;
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
            
            public function taskfrequencyAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $subset = new Model_PmTemplate();
                $desig_id = $param['desig_id'];
                $section = $param['sec'];
                $Intreval = array();
                $frequency = $subset->Getallfrequency();
                foreach($frequency as $val){
                        if($val->column==1)
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                        if($val->column==2)
                            $Intreval[$val->AU_Frequency_ID] = $val->Name;
                }
                
                $this->view->desig_id = $desig_id;
                $this->view->sec = $section;
                $this->view->CustFreq = $Intreval;
                $this->view->Freq = $freq;
            }
            
            //// Task Frequency subset
            public function taskfrequencysubsetAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $subset = new Model_PmTemplate();
                $desig_id = $param['desig_id'];
                $parent_id = $param['parent_id'];
                $Intreval = array();
                $frequency = $subset->Getallfrequency();
                foreach($frequency as $val){
                        if($val->column==1)
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                        if($val->column==2)
                            $Intreval[$val->AU_Frequency_ID] = $val->Name;
                }
                $this->view->desig_id = $desig_id;
                $this->view->parent_id = $parent_id;
                $this->view->CustFreq= $Intreval;
                $this->view->Freq= $freq;
               
            }
            /// Root frequency 
            public function updatetaskrootfrequeancyAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $Frequency_ID = $data['Frequency_ID'];
                $desig_id = $data['desig_id'];
                $updata['Interval_Value'] = $data['Interval_Value'];
                $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
                $inclidesubset = $data['includesubset'];
                $result  = $task->update_grouptask($updata,$desig_id,$inclidesubset);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Frequency Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* subset frequency */ 
            public function updatetaskfrequeancysubsetAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $parent_id = $data['parent_id'];
                $desig_id = $data['desig_id'];
                $updata['Interval_Value'] = $data['Interval_Value'];
                $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
                $result  = $task->update_grouptasksubset($updata,$desig_id,$parent_id);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Frequecy Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* Start date section start */
            
            public function taskstartdateAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $section = $param['sec'];
                $this->view->desig_id = $templete_id;
                $this->view->sec = $section;
            }
            
            public function taskstartdatesubsetAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $parent_id = $param['parent_id'];
                $this->view->desig_id = $templete_id;
                $this->view->parent_id = $parent_id;
            }
            
            /* Root start date */
            
            public function updatetaskrootstartdateAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $includesubset = $data['includesubset'];
                $desig_id = $data['desig_id'];
                $updata['Start_date'] = $data['startdate'];
                $result  = $task->update_grouptask($updata,$desig_id,$includesubset);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Start Date Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* update data  subset start date */ 
            public function updatetasksubsetstartdateAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $parent_id = $data['parent_id'];
                $desig_id = $data['desig_id'];
                $updata['Start_date'] = $data['startdate'];
                $result  = $task->update_grouptasksubset($updata,$desig_id,$parent_id);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Start Date Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /*  Task start date of month secton start */
            
            /* popup section start */
            public function taskstartdateofmonthAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $section = $param['sec'];
                $this->view->desig_id = $templete_id;
            }
            public function taskstartdateofmonthsubsetAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $parent_id = $param['parent_id'];
                $this->view->desig_id = $templete_id;
                $this->view->parent_id = $parent_id;
            }
            
            /* popup section End */
            
            /* Update tast root start date of month */
            public function updatetaskrootstartdateofmonthAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $updata = array();
                $includesubset = $data['includesubset'];
                $desig_id = $data['desig_id'];
                $date = $data['startdateofmonth'];
                //$alltask  = $task->Getalltaskbygroupmodification($desig_id,$includesubset);
                
                $updata['Startdate_month'] = $data['startdateofmonth'];
                $result  = $task->update_grouptask($updata,$desig_id,$includesubset);
              
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Start Date of Month sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* Update tast subset start date of month */
            public function updatetasksubsetstartdateofmonthAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $updata = array();
                $parent_id = $data['parent_id'];
                $desig_id = $data['desig_id'];
                $date = $data['startdateofmonth'];
                $updata['startdate_month'] = $data['startdateofmonth'];

                $result  = $task->update_grouptasksubset($updata,$desig_id,$parent_id);
                
               
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Start Date of month Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* Start date Adjustment started */
            
            /* popup section start */
            public function taskstartdateadjustmentAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $section = $param['sec'];
                $subset = new Model_PmTemplate();
                $startdate_ad = array();
                $startdateadjustment = $subset->Getallstartdateadjustment();
                foreach($startdateadjustment as $val){
                      $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                }
                $this->view->startdateofad = $startdate_ad;
                $this->view->desig_id = $templete_id;
                
            }
            public function taskstartdateadjustmentsubsetAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $parent_id = $param['parent_id'];
                $subset = new Model_PmTemplate();
                $startdate_ad = array();
                $startdateadjustment = $subset->Getallstartdateadjustment();
                foreach($startdateadjustment as $val){
                      $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                }
                $this->view->startdateofad = $startdate_ad;
                $this->view->desig_id = $templete_id;
                $this->view->parent_id = $parent_id;
            }
            
            /* popup section End */
            
            /* Root start date adjustment section */
            
            public function updatetaskrootstartdateadjustmentAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $updata = array();
                $includesubset = $data['includesubset'];
                $desig_id = $data['desig_id'];
                $updata['AU_sda_ID'] = $data['startdateadjustment'];
                $result  = $task->update_grouptask($updata,$desig_id,$includesubset);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* update data  subset start date */ 
            public function updatetasksubsetstartdateadjustmentAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $updata = array();
                $parent_id = $data['parent_id'];
                $desig_id = $data['desig_id'];
                $updata['AU_sda_ID'] = $data['startdateadjustment'];
                $result  = $task->update_grouptasksubset($updata,$desig_id,$parent_id);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            /* Task  group modification stop */
            
            /* Reading group modification Start */
            
            /* popup section start */
            public function readingfrequencyAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $section = $param['sec'];
                $subset = new Model_PmTemplate();
                $Intreval =array();
                $frequency = $subset->Getallfrequency();
                foreach($frequency as $val){
                        if($val->column==1)
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                        if($val->column==2)
                            $Intreval[$val->AU_Frequency_ID] = $val->Name;
                }
                
                $this->view->desig_id = $desig_id;
                //$this->view->sec = $section;
                $this->view->CustFreq = $Intreval;
                $this->view->Freq = $freq;
                $this->view->desig_id = $templete_id;
                
            }
            public function readingfrequencysubsetAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $subset = new Model_PmTemplate();
                $desig_id = $param['desig_id'];
                $parent_id = $param['parent_id'];
                $frequency = $subset->Getallfrequency();
                $Intreval =array();
                foreach($frequency as $val){
                        if($val->column==1)
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                        if($val->column==2)
                            $Intreval[$val->AU_Frequency_ID] = $val->Name;
                }
                
                $this->view->desig_id = $desig_id;
                //$this->view->sec = $section;
                $this->view->Freq = $freq;
                $this->view->CustFreq = $Intreval;
                $this->view->parent_id = $parent_id;
            }            
            /* popup section End */
            
            /* Update reading frequency group modification*/
            public function updatereadingrootfrequeancyAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $updata = array();
                $inclidesubset = $data['includesubset'];
                $desig_id = $data['desig_id'];
                $updata['Interval_Value'] = $data['Interval_Value'];
                $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
                $result  = $task->update_groupreading($updata,$desig_id,$inclidesubset);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Frequency Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            /* subset frequency */ 
            public function updatereadingfrequeancysubsetAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $parent_id = $data['parent_id'];                
                $desig_id = $data['desig_id'];
                $updata['Interval_Value'] = $data['Interval_Value'];
                $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
                $result  = $task->update_groupreadingsubset($updata,$desig_id,$parent_id);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Frequecy Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            
            
            /* Reading  Start date section start */
            
            /* popup section start */
            public function readingstartdateAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $section = $param['sec'];
                $this->view->desig_id = $templete_id;
                $this->view->sec = $section;
            }
            
            public function readingstartdatesubsetAction(){
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
            
            public function updatereadingrootstartdateAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $includesubset = $data['includesubset'];
                $desig_id = $data['desig_id'];
                $updata['Start_date'] = $data['startdate'];
                $result  = $task->update_groupreading($updata,$desig_id,$includesubset);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Start Date Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* 
             *  Update Reading start date subset Group modication 
            */
            public function updatereadingsubsetstartdateAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $parent_id = $data['parent_id'];
                $desig_id = $data['desig_id'];
                $updata['Start_date'] = $data['startdate'];
                $result  = $task->update_groupreadingsubset($updata,$desig_id,$parent_id);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Start Date Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /*  Reading start date of month secton start */
            
            /* popup section start */
            public function readingstartdateofmonthAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $section = $param['sec'];
                $this->view->desig_id = $templete_id;
            }
            public function readingstartdateofmonthsubsetAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $parent_id = $param['parent_id'];
                $this->view->desig_id = $templete_id;
                $this->view->parent_id = $parent_id;
            }
            
            /* popup section End */
            
            /* Update tast root start date of month */
            public function updatereadingrootstartdateofmonthAction(){
                $data = $this->_request->getPost();
              
                $reading = new Model_PmTemplate();
                $updata = array();
                $includesubset = $data['includesubset'];
                $desig_id = $data['desig_id'];
                $date = $data['startdateofmonth'];
                //$alltask  = $task->Getalltaskbygroupmodification($desig_id,$includesubset);
                
                $updata['Startdate_month'] = $data['startdateofmonth'];
                $result  = $reading->update_groupreading($updata,$desig_id,$includesubset);

                
                
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Start Date of Month sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* Update tast subset start date of month */
            public function updatereadingsubsetstartdateofmonthAction(){
                $data = $this->_request->getPost();
                $reading = new Model_PmTemplate();
                $updata = array();
                $parent_id = $data['parent_id'];
                $desig_id = $data['desig_id'];
                $updata['Startdate_month'] = $data['startdateofmonth'];

                $result  = $reading->update_groupreadingsubset($updata,$desig_id,$parent_id);
                
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Start Date of month Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /*  Reading Start date Adjustment started */
            
            /* popup section start */
            public function readingstartdateadjustmentAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $section = $param['sec'];
                $subset = new Model_PmTemplate();
                $startdate_ad = array();
                $startdateadjustment = $subset->Getallstartdateadjustment();
                foreach($startdateadjustment as $val){
                      $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                }
                $this->view->startdateofad = $startdate_ad;
                $this->view->desig_id = $templete_id;
            }
            public function readingstartdateadjustmentsubsetAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $parent_id = $param['parent_id'];
                $subset = new Model_PmTemplate();
                $startdate_ad = array();
                $startdateadjustment = $subset->Getallstartdateadjustment();
                foreach($startdateadjustment as $val){
                      $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                }
                $this->view->startdateofad = $startdate_ad;
                $this->view->desig_id = $templete_id;
                $this->view->parent_id = $parent_id;
            }
            
            /* popup section End */
            
            /* Root start date adjustment section */
            
            public function updatereadingrootstartdateadjustmentAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $includesubset = $data['includesubset'];
                $desig_id = $data['desig_id'];
                $updata['AU_sda_ID'] = $data['startdateadjustment'];
                $result  = $task->update_groupreading($updata,$desig_id,$includesubset);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* update data  subset start date */ 
            public function updatereadingsubsetstartdateadjustmentAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $parent_id = $data['parent_id'];
                $desig_id = $data['desig_id'];
                $updata['AU_sda_ID'] = $data['startdateadjustment'];
                $result  = $task->update_groupreadingsubset($updata,$desig_id,$parent_id);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            /* End Reading start date Adjustment section */
            
            /* Start Reading value section */
            
            /* popup section start */
            public function readingvalueAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $section = $param['sec'];
                $this->view->desig_id = $templete_id;
            }
            public function readingvaluesubsetAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $templete_id = $param['desig_id'];
                $parent_id = $param['parent_id'];
                $this->view->desig_id = $templete_id;
                $this->view->parent_id = $parent_id;
            }
            
            /* popup section End */
            
            /* Root start date adjustment section */
            
            public function updatereadingrootreadingvalueAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $includesubset = $data['includesubset'];
                $desig_id = $data['desig_id'];
                $updata['Reading_Value'] = $data['reading_value'];
                $result  = $task->update_groupreading($updata,$desig_id,$includesubset);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Reading Value On Root Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* update data  subset start date */ 
            public function updatereadingsubsetreadingvalueAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $parent_id = $data['parent_id'];
                $desig_id = $data['desig_id'];
                $updata['Reading_Value'] = $data['readingvalue'];
                $result  = $task->update_groupreadingsubset($updata,$desig_id,$parent_id);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Reading Value On Subset Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            /* End Reading value section */
            
          /* Start Unit Of Measure section */
            
            /* popup section start */
            public function readingunitofmeasureAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $subset = new Model_PmTemplate();
                $templete_id = $param['desig_id'];
                $section = $param['sec'];
                $allunitofmeasure = array();
                $unitofmeasure = $subset->Getallunitofmeasure();
                foreach($unitofmeasure as $val){
                      $allunitofmeasure[$val->AU_uom_ID] = $val->Name;  
                }
                
                /* Send data to View pages*/
                $this->view->desig_id = $templete_id;
                $this->view->unitofmeasure = $allunitofmeasure;
            }
            public function readingunitofmeasuresubsetAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $subset = new Model_PmTemplate();
                $templete_id = $param['desig_id'];
                $parent_id = $param['parent_id'];
                $allunitofmeasure = array();
                $unitofmeasure = $subset->Getallunitofmeasure();
                foreach($unitofmeasure as $val){
                      $allunitofmeasure[$val->AU_uom_ID] = $val->Name;  
                }
                
                /* Send data to View pages*/
                $this->view->desig_id = $templete_id;
                $this->view->unitofmeasure = $allunitofmeasure;
                $this->view->parent_id = $parent_id;
            }
            
            /* popup section End */
            
            /* Root start date adjustment section */
            
            public function updatereadingrootunitofmeasureAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $includesubset = $data['includesubset'];
                $desig_id = $data['desig_id'];
                $updata['AU_uom_ID'] = $data['unitofmeasure'];
                $result  = $task->update_groupreading($updata,$desig_id,$includesubset);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Reading Value On Root Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* update data  subset start date */ 
            public function updatereadingsubsetunitofmeasureAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $parent_id = $data['parent_id'];
                $desig_id = $data['desig_id'];
                $updata['AU_uom_ID'] = $data['unitofmeasure'];
                $result  = $task->update_groupreadingsubset($updata,$desig_id,$parent_id);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Reading Value On Subset Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
          /* End Unit Of Measure section */ 
            
        /* Start Unit Of Measure section */
            
            /* popup section start */
            public function readingtoleranceAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $subset = new Model_PmTemplate();
                $templete_id = $param['desig_id'];
                $section = $param['sec'];               
                
                /* Send data to View pages*/
                $this->view->desig_id = $templete_id;
            }
            public function readingtolerancesubsetAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $subset = new Model_PmTemplate();
                $templete_id = $param['desig_id'];
                $parent_id = $param['parent_id'];                
                
                /* Send data to View pages*/
                $this->view->desig_id = $templete_id;
                $this->view->parent_id = $parent_id;
            }
            
            /* popup section End */
            
            /* Root start date adjustment section */
            
            public function updatereadingroottoleranceAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $includesubset = $data['includesubset'];
                $desig_id = $data['desig_id'];
                $updata['Tolerance'] = $data['tolerance'];
                $result  = $task->update_groupreading($updata,$desig_id,$includesubset);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Reading Value On Root Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* update data  subset start date */ 
            public function updatereadingsubsettoleranceAction(){
                $data = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $parent_id = $data['parent_id'];
                $desig_id = $data['desig_id'];
                $updata['Tolerance'] = $data['tolerance'];
                $result  = $task->update_groupreadingsubset($updata,$desig_id,$parent_id);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Update Reading Value On Subset Sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            /* End Unit Of Measure section */     
            
            
        /* End Group Modification section */
            
            /* Import section */
            
            public function importAction(){
                $this->_helper->layout()->disableLayout();
                //$this->_helper->layout()->setLayout('popuplayout');
                $param = $this->_request->getPost();
                $desig_id = $param['desig_id'];
                $template = new Model_PmTemplate();
                $resultDesign = $template->get_all_typedesignation($desig_id);
                
                /* send data to view page*/
                $this->view->designation = $resultDesign;
                $this->view->desig_id = $desig_id;
                
            }
            
            public function importreadingAction(){
                $this->_helper->layout()->disableLayout();
                //$this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $desig_id = $param['desig_id'];
                $template = new Model_PmTemplate();
                $resultDesign = $template->get_all_typedesignation($desig_id);
                
                /* send data to view page*/
                $this->view->designation = $resultDesign;
                $this->view->desig_id = $desig_id;
                
            }
            public function importapplyAction(){
                $data = $this->_request->getPost();
                $desig_id = $data['desig_id'];
                $import_id = $data['import_id'];
                $template = new Model_PmTemplate();
                $task_subset = $template->get_subsetbyid('pm_vt_template_task',$import_id);
               // print_r($task_subset);
                //die;
                /* task section */
                    foreach($task_subset as $tsubset){
                        $innertask = $template->get_taskbysubsetId('pm_vt_template_task',$import_id,$tsubset->VT_Template_Task_ID);
                        $data = (array) $tsubset;
                        unset($data['VT_Template_Task_ID']);
                        $data['VT_Template_Designation_ID'] = $desig_id;
                        
                        //print_r($data);
                        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                        $data['User_ID'] = $user_id;
                        $parent_id = $template->insertsubset($data);
                        if(!empty($innertask)){
                            foreach($innertask as $task){
                                $innerdata =(array)$task;
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
                    $task_data = $template->get_tabledata('pm_vt_template_task',$import_id);
                    foreach($task_data as $tdata){
                        $data =(array) $tdata;
                        unset($data['VT_Template_Task_ID']);
                        $data['VT_Template_Designation_ID'] = $desig_id;
                        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                        $data['User_ID'] = $user_id;
                        $template->Inserttask($data);
                    }
                
                 /* Reading section start*/
                    $reading_subset = $template->get_subsetbyid('pm_vt_template_reading',$import_id);
                     //print_r($reading_subset);
                     //die;
                    foreach($reading_subset as $tsubset){
                        $innertask = $template->get_taskbysubsetId('pm_vt_template_reading',$import_id,$tsubset->VT_Template_Reading_ID);
                        $data = (array) $tsubset;
                        unset($data['VT_Template_Reading_ID']);
                        $data['VT_Template_Designation_ID'] = $desig_id;
                        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                        $data['User_ID'] = $user_id;
                        $parent_id = $template->InsertReadingsubset($data);
                        
                        if(!empty($innertask)){
                            
                            foreach($innertask as $task){
                                $innerdata =(array)$task;
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
                    $task_data = $template->get_tabledata('pm_vt_template_reading',$import_id);
                    foreach($task_data as $tdata){
                        $data = (array) $tdata;
                        unset($data['VT_Template_Reading_ID']);
                        $data['VT_Template_Designation_ID'] = $desig_id;
                        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                        $data['User_ID'] = $user_id;
                        $result = $template->InsertReading($data);
                    }
                    if(empty($result)){
                        $msg['status'] = 'error';
                        $msg['msg'] = 'Error for save data';
                    }else{
                        $msg['status'] = 'success';
                        $msg['msg'] = 'Import All The Data Sucessfully';
                    }
                    echo json_encode($msg);
                    exit();
                    
                    
            }
            

            /*  End import section */
            
           function custometimejobAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();       
                $freq = explode("-",$param['timejob']);               
                
                $this->view->customedata = $freq;
                $subset = new Model_PmTemplate();
                $alljobtime = array();
                $jobtime = $subset->Getalljobtime();
                foreach($jobtime as $val){
                      $alljobtime[$val->id] = $val->name;  
                }
                $this->view->timejob = $alljobtime;
           }
           
           function insertfrequencyAction(){
               $data = $this->_request->getPost();
               $InsertData = array(
                                   "Name" =>$data['name'],
                                   "value" =>$data['value'],
                                   "type" =>$data['type'],
                                    );
                $subset = new Model_PmTemplate();
                $last_id = $subset->InsertFrequencyData($InsertData);
                $return = array("id"=>$last_id);
                echo json_encode($return);
                die;
               
           }
           
           public function getsubsetAction(){
               $data = $this->_request->getPost();
               $desig_id  = $data['desig_id'];
               $subset = new Model_PmTemplate();
               $allsubset = $subset->GetAllSubset($desig_id);
               echo json_encode($allsubset);
               die;
           }
           
           public function getsubsetreadingAction(){
               $data = $this->_request->getPost();
               $desig_id  = $data['desig_id'];
               $subset = new Model_PmTemplate();
               $allsubset = $subset->GetAllSubsetReading($desig_id);
               echo json_encode($allsubset);
               die;
           } 
           
/***************************** Equipment template start *********************************/
           
           public function equipmenttemplateAction(){
                //$this->_redirect('/pm/matrix');
                //$_SESSION['Zend_Auth']['storage']
                //print_r($_SESSION);
               
                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                $cust_id = $_SESSION['Zend_Auth']['storage']->cust_id;
                $role_id = $_SESSION['Zend_Auth']['storage']->role_id;
                //print_r($_SESSION['Zend_Auth']['storage']->uid);               
                //die;
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
                    foreach ($companyListing as $cl){
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
                    $user_id=$_SESSION['Zend_Auth']['storage']->uid;
                    $_SESSION['current_building'] = $select_build_id;

                    //echo $select_build_id;
                    //$set_cookie = setcookie('build_cookie1', "sdfsdfsdf", time() + (86400 / 24), "/");
					//print_r($_COOKIE); 
                    //die;
                    $this->view->companyListing = $companyListing;
                    $this->view->select_build_id = $select_build_id;
               
                    $templateName = "";
                    $designationName = "";
                    $data = $this->_request->getPost();
                    $template = new Model_PmTemplate();
                    $templatedata = array();
                    if($data['search']=='Search'){
                        $templateName = $data['templatename'];
                        $designationName = $data['designationname'];
                        $tempdata = $template->GetAllEquipmentTemplateName($templateName,$select_build_id);
                        
                        foreach($tempdata as $temp){
                                $find = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID,$designationName,$select_build_id);
                                if(!empty($find) && $designationName!=""){
                                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name] = $temp->AU_Template_Name;
                                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name_ID] = $temp->AU_Template_Name_ID;
                                    $templatedata[$temp->AU_Template_Name_ID][AU_TypeDesignation] = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID,$designationName,$select_build_id);
                                }else if($designationName==""){
                                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name] = $temp->AU_Template_Name;
                                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name_ID] = $temp->AU_Template_Name_ID;
                                    $templatedata[$temp->AU_Template_Name_ID][AU_TypeDesignation] = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID,$designationName,$select_build_id);
                                }
                        }
                    }else{
                        $tempdata = $template->GetAllEquipmentTemplateName("",$select_build_id);
                        //print_r($tempdata);
                       // die;
                        foreach($tempdata as $temp){ 
                                $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name] = $temp->AU_Template_Name;
                                $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name_ID] = $temp->AU_Template_Name_ID;
                                $templatedata[$temp->AU_Template_Name_ID][AU_TypeDesignation] = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID,$designationName,$select_build_id);
                        }
                    }
                    //die;
                    $user_id=$_SESSION['Zend_Auth']['storage']->uid;
                    //$final=$this->getViewAccess($select_build_id);
                    $this->view->userId=$user_id;
                    $this->view->templatedetails = $templatedata;
                    $this->view->templateName = $templateName;
                    $this->view->designationName = $designationName;
                    $this->view->custID = $this->cust_id;
                    //$this->view->select_build_id = $select_build_id;
                    $this->view->roleId = $role_id;
                    $this->view->acessHelper = $this->accessHelper;
                    $this->view->croom_location = 29;
                    
           }
           
            public function createequipmenttemplatedesignationAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $template = new Model_PmTemplate();
                $user_id= $_SESSION['Zend_Auth']['storage']->uid;
                $build_id =  $_SESSION['current_building'];
                $tempdata = $template->GetAllEquipmentTemplateName("",$build_id,$user_id);        
                // send data to view pages
                $this->view->templats = $tempdata;
            }
            public function createequipmenttemplateAction(){
                //$this->_helper->layout()->disableLayout();
                $this->_helper->layout()->setLayout('popuplayout');

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
            
            

            // validate template before creation
            public function validateequipmenttemplateAction()
            {
                //$param = $this->getRequest()->getParams();
                $param = $this->_request->getPost();

                $template_Name = $param['TemplateName'];
                $template_id = $param['Template_id'];
                $build_id = $_SESSION['current_building'];;
                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                $template = new Model_PmTemplate();
                $result = $template->GetEquipmentTemplateByName($template_Name,$template_id,$build_id,$user_id);
                if(empty($result)){
                    echo  'true';
                }else{
                    echo 'false';
                }
                exit();
            }

            // Save template Name
            public function saveequipmenttemplateAction()
            {
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
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Temaplate save sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            
            /* Edit  designation */
            public function editequipmenttemplatedesignationAction()
            {
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                
                $typedesignation_id = $param['desig_id'];
                $template = new Model_PmTemplate();
                $typedata = $template->GetequipmenttemplatetypedesignationById($typedesignation_id);
                
                //print_r($templatedata);
                $typedata = $typedata[0];
                $build_id =  $_SESSION['current_building'];
                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                $tempdata = $template->GetAllEquipmentTemplateName("",$build_id,$user_id);
                //print_r($tempdata);
                //die;
                // send data on view pages
                $this->view->VT_TypeDesignation = $typedata;
                $this->view->templats = $tempdata;
            }


            //  Validate Type designation  //
            public function validateequipmenttemplatetypedesignationAction(){
                $data = $this->_request->getPost();
                //$param = $this->getRequest()->getParams();
                $typedesignation    = $data['typedesignation'];
                $typedesination_id  = $data['typedesination_id'];
                $build_ID =  $_SESSION['current_building'];
                $template = new Model_PmTemplate();
                $result = $template->GetEquipmentTemplateIdByTypeDesignation($typedesignation,$typedesination_id,$build_ID);

                if(empty($result)){
                    echo  'true';
                }else{
                    echo 'false';
                }
                exit();

            }

            /* save Type designation */ 
            public function saveequipmenttemplatetypedesignationAction(){
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
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Type Designation save sucessfully';
                    $msg['id'] = $result; 
                }
                echo json_encode($msg);
                exit();
            }

            // edit template name

            public function editequipmenttemplateAction(){
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
            public  function updateequipmenttemplateAction(){
                $msg = array();
                $typedata = array();
                $template = new Model_PmTemplate();       
                $param = $this->_request->getPost();
                $typedata['AU_Template_Name'] = $param['TemplateName'];
                //$typedata['TypeDestination'] = $param['Template_id'];

                $result = $template->updateEquipmentTemplate($typedata,$param['Template_id']);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Template Update sucessfully';
                }
                echo json_encode($msg);
                exit();
            }

            // Delete Template 
            public function deleteequipmenttemplateAction(){
                $msg = array();
                $typedata = array();
                $template = new Model_PmTemplate();       
                $param = $this->_request->getPost();

                $result = $template->deleteEquipmentTemplate($param['Template_id']);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{

                    $template->deleteEquipmentTemplateTypeDesignationByTemplateId($param['Template_id']);
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Template Deleted sucessfully';
                }
                echo json_encode($msg);
                exit();
            }

            
            /* Update type designation */
            public function updateequipmenttemplatetypedesignationAction(){
                $msg = array();
                $typedata = array();
                $template = new Model_PmTemplate();       
                $param = $this->_request->getPost();
                $typedata['AU_Template_Name_ID'] = $param['template_id'];
                $typedata['AU_TypeDesignation'] = $param['typedesignation'];
                $typedata['AU_TypeDescritpion'] = $param['typedescription'];
                $result = $template->updateEquipmentTemplatetypedesignation($typedata,$param['typedesination_id']);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Type Designation Update sucessfully';
                }
                echo json_encode($msg);
                exit();
            }
            /* delete type designation */
            public function deleteequipmenttemplatetypedescriptionAction()
            {
                $msg = array();
                $template = new Model_PmTemplate();       
                $data = $this->_request->getPost();

                $result = $template->deleteEquipmentTemplateTypeDesignation($data['type_id']);
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Template Deleted sucessfully';
                }
                echo json_encode($msg);
                exit();
            }

            // Task Section start

            /* Create a task */
            public function createequipmenttemplatetaskAction()
            {
                $param = $this->getRequest()->getParams();
                $desig_id = $param['desig_id'];
                
                $subset = new Model_PmTemplate();
                if(!empty($desig_id)){            
                    $allsubset = $subset->GetAllEquipmentTemplateSubset($desig_id);
                    //die;
                    //$data = $this->_request->getPost();
                    $msg = array();
                    $alltask = array();
                    $getalltask = $subset->GetAllEquipmentTemplatetaskparent($desig_id);
                    foreach($getalltask as $sub){
                        $subtask = $subset->GetEquipmentTemplateTaskBysubsetId($sub->AU_Template_Task_ID);
                        if(!empty($subtask)){
                            $alltask[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                            $alltask[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                            $alltask[$sub->AU_Template_Task_ID]['task'] = $subtask;
                        }else{
                            $alltask[][] = $sub;
                        }                
                    }
                    //print_r($alltask);
                   // die;
                    $list = $subset->get_au_view_table('task');
                    $listview = explode(',',$list[0]->display_table_view);
                    $freq = array();
                    $Intreval = array();
                    $frequency = $subset->GetallEquipmentTemplatefrequency();
                    foreach($frequency as $val){
                                if($val->column==1)
                                    $freq[$val->AU_Frequency_ID] = $val->Name;
                                if($val->column==2)
                                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
                                
                                
                    }

                    $startdate_ad = array();
                    $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                    foreach($startdateadjustment as $val){
                          $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                    }

                    $alljobtime = array();
                    $jobtime = $subset->GetallEquipmentTemplatejobtime();
                    foreach($jobtime as $val){
                          $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
                    }
                    // get template data 
                    $template_data = $subset->GetEquipmentTemplateByTypeDesignationID($param['desig_id']);
                    $template_data =  $template_data[0];
                    
                    // send data in view pages
                    $this->view->desig_id = $desig_id;
                    $this->view->alltask = $alltask;
                    $this->view->subset = $allsubset;
                    $this->view->listview = $listview;
                    $this->view->frequency  = $freq;
                    $this->view->CustmeFreq = $Intreval;
                    $this->view->startdateadjustment = $startdate_ad;
                    $this->view->jobtime = $alljobtime;
                    $this->view->templateData = $template_data;
                }else{            
                    exit();
                    //return false;
                }

            }  

             // edit task section 
                public function viewequipmenttemplatetaskAction(){
                    $this->_helper->getHelper('layout')->disableLayout();                  
                    //$param = $this->getRequest()->getParams();
                    $param = $this->_request->getPost();
                    $desig_id = $param['desig_id'];

                $subset = new Model_PmTemplate();
                if(!empty($desig_id)){            
                    $allsubset = $subset->GetAllEquipmentTemplateSubset($desig_id);
                    //die;
                    //$data = $this->_request->getPost();
                    $msg = array();
                    $alltask = array();
                    $view_empty_subset = array();
                    $getalltask = $subset->GetAllEquipmentTemplatetaskparent($desig_id);
                    //print_r($getalltask);
                    //die;
                    foreach($getalltask as $sub){
                        $subtask = $subset->GetEquipmentTemplateTaskBysubsetId($sub->AU_Template_Task_ID);
                        //print_r($subtask);
                        if(!empty($subtask)){
                            $alltask[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                            $alltask[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                            $alltask[$sub->AU_Template_Task_ID]['task'] = $subtask;
                        }else if(empty($sub->AU_Frequency_ID)){
                            $view_empty_subset[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                            $view_empty_subset[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                            $view_empty_subset[$sub->AU_Template_Task_ID]['task'] = "";
                        }else{
                            $alltask[][] = $sub;
                        }                
                    }
                    $alltask = array_merge($alltask,$view_empty_subset);
                    $listview = explode(',',$param['viewlist']);
                    $freq = array();
                    $Intreval = array();
                    $frequency = $subset->GetallEquipmentTemplatefrequency();
                    foreach($frequency as $val){

                                if($val->column==1)
                                    $freq[$val->AU_Frequency_ID] = $val->Name;
                                if($val->column==2)
                                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
                    }

                    $startdate_ad = array();
                    $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                    foreach($startdateadjustment as $val){
                          $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                    }

                    $alljobtime = array();
                    $jobtime = $subset->GetallEquipmentTemplatejobtime();
                    foreach($jobtime as $val){
                          $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
                    }

                    // send data in view pages
                    $this->view->desig_id = $desig_id;
                    $this->view->alltask = $alltask;
                    $this->view->subset = $allsubset;
                    $this->view->listview = $listview;
                    $this->view->frequency  = $freq;
                    $this->view->CustmeFreq = $Intreval;
                    $this->view->startdateadjustment = $startdate_ad;
                    $this->view->jobtime = $alljobtime;


                    }else{            
                        exit();
                        //return false;
                    }
                }

            // view add new task 
            public function addequipmenttemplatetaskAction(){
                //$this->_helper->getHelper('layout')->disableLayout();
                $this->_helper->layout()->setLayout('popuplayout');
                $subset = new Model_PmTemplate();
                $data = $this->_request->getParams();
                //echo $data['desig_id'];
                $desig_id  = $data['desig_id'];
                $allsubset = $subset->GetAllEquipmentTemplateSubset($desig_id);
                //print_r($allsubset);
                //die;
                $freq = array();
                $frequency = $subset->GetallEquipmentTemplatefrequency();
                foreach($frequency as $val){
                                if($val->column==1)
                                    $freq[$val->AU_Frequency_ID] = $val->Name;
                                if($val->column==2)
                                    $Intreval[$val->AU_Frequency_ID] = $val->Name;                
                                
                }
                //print_r($freq);
                //die;
                $startdatead = array();
                $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                foreach($startdateadjustment as $val){
                      $startdatead[$val->AU_sda_ID] = $val->Name;  
                }

                $alljobtime = array();
                $jobtime = $subset->GetallEquipmentTemplatejobtime();
                foreach($jobtime as $val){
                      $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
                }

                /* Reading section */ 
                $ReadingSubset = $subset->GetAllEquipmentTemplateSubset_reading($desig_id);
                //print_r($jobtime);
                //die;
                $this->view->frequency  = $freq;
                $this->view->Interval  = $Intreval;
                $this->view->startdateadjustment = $startdatead;
                $this->view->jobtime = $alljobtime;
                $this->view->desig_id = $desig_id;
                $this->view->subset = $allsubset;
                $this->view->ReadingSubset = $ReadingSubset;
            }

             // view add new task 
            public function addequipmenttemplatereadingAction(){
                //$this->_helper->getHelper('layout')->disableLayout();
                $this->_helper->layout()->setLayout('popuplayout');
                $subset = new Model_PmTemplate();
                $data = $this->_request->getParams();
                //echo $data['desig_id'];
                $desig_id  = $data['desig_id'];        
                $allsubset = $subset->GetAllEquipmentTemplateSubset_reading($desig_id);
                //print_r($allsubset);
               // die;
                $freq = array();
                $frequency = $subset->GetallEquipmentTemplatefrequency();
                foreach($frequency as $val){
                                if($val->column==1)
                                    $freq[$val->AU_Frequency_ID] = $val->Name;
                                if($val->column==2)
                                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
                }
                $startdatead = array();
                $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                foreach($startdateadjustment as $val){
                      $startdatead[$val->AU_sda_ID] = $val->Name;  
                }

                $alljobtime = array();
                $jobtime = $subset->Getalljobtime();
                foreach($jobtime as $val){
                      $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
                }

                $allunitofmeasure = array();
                $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
                foreach($unitofmeasure as $val){
                      $allunitofmeasure[$val->AU_uom_ID] = $val->Name;  
                }
                $this->view->frequency  = $freq;
                $this->view->Interval  = $Intreval;
                $this->view->startdateadjustment = $startdatead;
                $this->view->unitofmeasure = $allunitofmeasure;
                $this->view->jobtime = $alljobtime;
                $this->view->desig_id = $desig_id;
                $this->view->subset = $allsubset;
            }

            /* Add a new subset */
            public function createequipmenttemplatesubsetAction()
            {
                $this->_helper->layout()->disableLayout();
                $param = $this->_request->getPost();
                $desig_id = $param['desig_id'];

                // send data in view pages
                 $this->view->desig_id = $desig_id;
            }

            /* Update View Task */
            public function updateequipmenttemplateviewtaskAction(){
                $param = $this->_request->getPost();
                $task = new Model_PmTemplate();
                $update = array();
                $update['display_table_view'] = $param['viewlist'];
                //$update['pm_type'] = $param['type'];        
                $result = $task->UpdateEquipmentTemplateviewlist($update,$param['type']);
                if(!empty($result)){
                    echo  'true';
                }else{
                    echo 'false';
                }
                exit();

            }

            /* validate subset */
            public function validateequipmenttemplatesubsetAction()
            {
                $data = $this->_request->getPost();
                $subsetname    = $data['subsetname'];
                $subsetname_id  = $data['subsetname_id'];
                $desig_id       =$data['desig_id'];
                $subset = new Model_PmTemplate();
                
                $result = $subset->GetEquipmentTemplateSubseteByName($subsetname,$subsetname_id,$desig_id);
                if(empty($result)){
                    echo  'true';
                }else{
                    echo 'false';
                }
                exit();
            } 

            /* Save a new subset */
            public  function saveequipmenttemplatesubsetAction()
            {
                $msg = array();
                $subsetdata = array();
                $subset = new Model_PmTemplate();        
                $data = $this->_request->getPost();
                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                $data['user_id'] = $user_id;
                $result = $subset->insertEquipmentTemplatesubset($data);
                //print_r($data);
                //die;
                if(empty($result)){
                    $msg['status'] = 'error';
                    $msg['msg'] = 'Error for save data';
                }else{
                    $msg['status'] = 'success';
                    $msg['msg'] = 'Subset save sucessfully';
                    $msg['InsertId'] = $result;
                }
                echo json_encode($msg);
                exit();
            }

            /* Edit subset */
            public function editequipmenttemplatesubsetAction(){
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
            public function updateequipmenttemplatesubsetAction(){
                $data = $this->_request->getPost();
                //print_r($data);
                if(!empty($data)){
                    $task = new Model_PmTemplate();
                    $task_id = $data['subsetname_id'];
                    unset($data['subsetname_id']);
                    $result = $task->UpdateEquipmentTemplatetask($data,$task_id);
                    if(empty($result)){
                        $msg['status'] = 'error';
                        $msg['msg'] = 'Error for save data';
                    }else{
                        $msg['status'] = 'success';
                        $msg['msg'] = 'Update subset save sucessfully';
                    }
                    echo json_encode($msg);
                    exit();
               }
            }

            public function customeequipmenttemplatefrequencyAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();       
                $freq = $param['frequency'];        
                $GetData = "";
                $task = new Model_PmTemplate();
                if($freq!=""){
                    $GetData = $task->getEquipmentTemplate_FrequencydataByID($freq);
                    $GetData = $GetData[0];
                }
                $this->view->GetData = $GetData;
            }

            public function customeequipmenttemplatefreqAction(){
                $msg = array();
                $data = $this->_request->getPost();
                $_SESSION['custome_freq'] = $data;
                $msg['status'] = 'success';
                $msg['msg'] = 'Frequency save sucessfully';
                echo json_encode($msg);
                exit();
            }

            public function saveequipmenttemplatetaskAction(){
               $data = $this->_request->getPost();

                if(!empty($data)){
                    $task = new Model_PmTemplate();
                    $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                    $data['user_id'] = $user_id;
                    $view_order = $task->Get_MaxViewOrder("pm_au_template_task");
                    $view_order = $view_order[0]->View_order; 
                    $data['View_order'] = $view_order + 1; 
                    //print_r($data);
                    //die;
                    $result = $task->InsertEquipmentTemplatetask($data);
                    if(empty($result)){
                        $msg['status'] = 'error';
                        $msg['msg'] = 'Error for save data';
                    }else{
                        $msg['status'] = 'success';
                        $msg['msg'] = 'Task save sucessfully';
                    }
                    echo json_encode($msg);
                    exit();
               }       
               //die;
            }

            // update task data
            public function updateequipmenttemplatetaskAction(){
                $data = $this->_request->getPost();
                if(!empty($data)){
                    $task = new Model_PmTemplate();
                    $task_id = $data['task_id'];
                    unset($data['task_id']);
                    $result = $task->UpdateEquipmentTemplatetask($data,$task_id);
                    if(empty($result)){
                        $msg['status'] = 'error';
                        $msg['msg'] = 'Error for save data';
                    }else{
                        $msg['status'] = 'success';
                        $msg['msg'] = 'Update Task save sucessfully';
                    }
                    echo json_encode($msg);
                    exit();
               } 
            } 
            public function updateequipmenttemplatetaskorderAction()
                    {

                        $data = $this->_request->getPost();
                        $task = json_decode($data[task]);
                        $taskmodule = new Model_PmTemplate();
                         $return = $this->validationdragandrop($task);
                         $order = 1;
                        if($return==1){
                            foreach($task as $val){
                                //print_r($val);
                                if(!empty($val->children) && $val->idSubset){
                                            $getdata = array("view_order"=>$order);
                                            $taskmodule->UpdateEquipmentTemplateodrder($getdata,$val->idSubset);
                                            $order++;
                                    foreach($val->children as $data){

                                        if(!empty($data->idRoot)){
                                            //echo "idroot children";
                                            $getdata = array("view_order"=>$order,"Parent_ID"=>$val->idSubset);
                                            $taskmodule->UpdateEquipmentTemplateodrder($getdata,$data->idRoot);
                                        }
                                        if(!empty($data->id)){
                                            //echo "idroot children";
                                            $getdata = array("view_order"=>$order,"Parent_ID"=>$val->idSubset);
                                            $taskmodule->UpdateEquipmentTemplateodrder($getdata,$data->id);
                                        }                                

                                        $order++;
                                    }
                                }else{

                                    if(!empty($val->idRoot)){
                                        //echo "root";
                                        $getdata = array("view_order"=>$order,"Parent_ID"=>0);
                                        $taskmodule->UpdateEquipmentTemplateodrder($getdata,$val->idRoot);
                                    }

                                    if(!empty($val->id)){
                                        //echo "id";
                                        $getdata = array("view_order"=>$order,"Parent_ID"=>0);
                                        $taskmodule->UpdateEquipmentTemplateodrder($getdata,$val->id);
                                    }                                
                                    $order++;
                                }
                            }
                        }
                          if($return==0){
                                $msg['status'] = 'error';
                                $msg['msg'] = 'Error : This move not posible please try other';
                            }else{
                                $msg['status'] = 'success';
                                $msg['msg'] = 'Order save sucessfully';
                            }
                            echo json_encode($msg);
                            exit();  
                    }



                    public function validationdragandrop($data){
                        $ret_data = 1;
                        foreach ($data as $val){
                            //print_r($val);
                            if($val->idRoot){
                                if(!empty($val->children))
                                    $ret_data = 0;

                            }
                            if($val->idSubset){
                                foreach ($val->children as $data){
                                    if($data->idSubset){
                                        $ret_data = 0;
                                    }
                                }
                            }
                        }
                        return $ret_data;
                    }

                    // edit task section 
                    public function editequipmenttemplatetaskAction(){
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
                        foreach($frequency as $val){
                                if($val->column==1)
                                    $freq[$val->AU_Frequency_ID] = $val->Name;
                                if($val->column==2)
                                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
                        }

                        $startdate_ad = array();
                        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                        foreach($startdateadjustment as $val){
                              $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                        }

                        $alljobtime = array();
                        $jobtime = $subset->GetallEquipmentTemplatejobtime();
                        foreach($jobtime as $val){
                              $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
                        }

                        $this->view->frequency  = $freq;                
                        $this->view->Interval  = $Intreval;
                        $this->view->startdateadjustment = $startdate_ad;
                        $this->view->jobtime = $alljobtime;
                        $this->view->desig_id = $desig_id;
                        $this->view->subset = $allsubset;
                        $this->view->taskdata = $TaskData;
                        
        
                    }

                    /// Delete tasking 
                    public function deleteequipmenttemplatetaskAction(){
                        $msg = array();
                        $typedata = array();
                        $task = new Model_PmTemplate();       
                        $param = $this->_request->getPost();
                        $parent_id = $task->getallEquipmentTemplateparent($param['Task_id']);
                        $result = $task->deleteEquipmentTemplateTask($param['Task_id']); 
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            //$result = $task->deleteEquipmentTemplateTaskByParentId($param['Task_id']);
                            $update = array("Parent_ID"=>"");
                            $result = $task->UpdateEquipmentTemplateTaskByparent($update,$param['Task_id']);
                            if(!empty($result)){
                                $msg['status'] = 'success';
                                $msg['msg'] = 'Template Deleted sucessfully';
                            }else{
                               $msg['status'] = 'error';
                                $msg['msg'] = 'Error for Delete data'; 
                            }
                            
                        }
                        echo json_encode($msg);
                        exit();
                    }


            /* Reading Section start */ 

            public function createequipmenttemplatereadingAction(){
                $param = $this->getRequest()->getParams();
                $desig_id = $param['desig_id'];
                if(!empty($desig_id)){
                    $subset = new Model_PmTemplate();
                    $allsubset = $subset->GetAllEquipmentTemplateSubset_reading($desig_id);
                    $data = $this->_request->getPost();
                    $msg = array();
                    $list = $subset->get_au_view_table('Reading');
                    $listview = explode(',',$list[0]->display_table_view);
                    $allreading = array();
                    $getallreading = $subset->GetAllEquipmentTemplateReadingParent($desig_id);
                    foreach($getallreading as $sub){
                        //print_r($sub);
                        $subreading = $subset->GetEquipmentTemplateReadingBysubsetId($sub->AU_Template_Reading_ID);
                        if(!empty($subreading)){
                            $allreading[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                            $allreading[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                            $allreading[$sub->AU_Template_Reading_ID]['task'] = $subreading;
                        }else{
                            $allreading[][] = $sub;
                        }                
                    }
                   //print_r($allreading);
                    //die;
                    $freq = array();
                    $frequency = $subset->GetallEquipmentTemplatefrequency();
                    foreach($frequency as $val){
                                if($val->column==1)
                                    $freq[$val->AU_Frequency_ID] = $val->Name;
                                if($val->column==2)
                                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
                    }

                    $startdate_ad = array();
                    $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                    foreach($startdateadjustment as $val){
                          $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                    }

                    $alljobtime = array();
                    $jobtime = $subset->GetallEquipmentTemplatejobtime();
                    foreach($jobtime as $val){
                          $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
                    }

                    $allunitofmeasure = array();
                    $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
                    foreach($unitofmeasure as $val){
                          $allunitofmeasure[$val->AU_uom_ID] = $val->Name;  
                    }
                    
                    // get template data 
                    $template_data = $subset->GetEquipmentTemplateByTypeDesignationID($param['desig_id']);
                    $template_data =  $template_data[0];

                    /* send data in view pages */            
                    $this->view->frequency  = $freq;
                    $this->view->CustmeFreq = $Intreval;
                    $this->view->startdateadjustment = $startdate_ad;
                    $this->view->jobtime = $alljobtime;
                    $this->view->unitofmeasure = $allunitofmeasure;
                    $this->view->desig_id = $desig_id;
                    $this->view->allreading = $allreading;
                    $this->view->subset = $allsubset;
                    $this->view->listview = $listview;
                    $this->view->templateDate = $template_data;
                }else{            
                    exit();
                    //return false;
                }
            }

            public function saveequipmenttemplatereadingAction(){
               $data = $this->_request->getPost();
               //print_r($data);

                if(!empty($data)){
                    $task = new Model_PmTemplate();
                    $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                    $data['user_id'] = $user_id;
                    $view_order = $task->Get_MaxViewOrder("pm_au_template_reading");
                    $view_order = $view_order[0]->View_order; 
                    $data['View_order'] = $view_order + 1;
                    $result = $task->InsertEquipmentTemplateReading($data);
                    if(empty($result)){
                        $msg['status'] = 'error';
                        $msg['msg'] = 'Error for save data';
                    }else{
                        $msg['status'] = 'success';
                        $msg['msg'] = 'Reading save sucessfully';
                    }
                    echo json_encode($msg);
                    exit();
               }       
               //die;
            }

            // edit task section 
                    public function editequipmenttemplatereadingAction(){
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
                        foreach($frequency as $val){
                                if($val->column==1)
                                    $freq[$val->AU_Frequency_ID] = $val->Name;
                                if($val->column==2)
                                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
                        }
                        $startdatead = array();
                        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                        foreach($startdateadjustment as $val){
                              $startdatead[$val->AU_sda_ID] = $val->Name;  
                        }

                        $alljobtime = array();
                        $jobtime = $subset->GetallEquipmentTemplatejobtime();
                        foreach($jobtime as $val){
                              $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
                        }

                        $allunitofmeasure = array();
                        $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
                        foreach($unitofmeasure as $val){
                              $allunitofmeasure[$val->AU_uom_ID] = $val->Name;  
                        }
                        
                        $this->view->frequency  = $freq;
                        $this->view->Interval  = $Intreval;
                        $this->view->startdateadjustment = $startdatead;
                        $this->view->unitofmeasure = $allunitofmeasure;
                        $this->view->jobtime = $alljobtime;
                        $this->view->desig_id = $desig_id;
                        $this->view->subset = $allsubset;
                        $this->view->readingdata = $ReadingData[0];

                    }

                    /// Delete tasking 
                    public function deleteequipmenttemplatereadingAction(){
                        $msg = array();
                        $typedata = array();
                        $reading = new Model_PmTemplate();       
                        $param = $this->_request->getPost();
                        //$parent_id = $task->getallparent($param['Task_id']);
                        $result = $reading->deleteEquipmentTemplateReading($param['reading_id']); 
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $update = array("Parent_ID"=>"");
                            $result = $reading->UpdateEquipmentTemplateReadingByparent($update,$param['reading_id']);
                            if(!empty($result)){
                                $msg['status'] = 'success';
                                $msg['msg'] = 'Template Deleted sucessfully';
                            }else{
                               $msg['status'] = 'error';
                                $msg['msg'] = 'Error for Delete data'; 
                            }
                        }
                        echo json_encode($msg);
                        exit();

                    }
                    // update task data
                    public function updateequipmenttemplatereadingAction(){
                        $data = $this->_request->getPost();
                       //print_r($data);       
                        if(!empty($data)){
                            $task = new Model_PmTemplate();
                            $reading_id = $data['reading_id'];
                            unset($data['reading_id']);
                            $result = $task->UpdateEquipmentTemplatereading($data,$reading_id);
                            if(empty($result)){
                                $msg['status'] = 'error';
                                $msg['msg'] = 'Error for save data';
                            }else{
                                $msg['status'] = 'success';
                                $msg['msg'] = 'Update Reading save sucessfully';
                            }
                            echo json_encode($msg);
                            exit();
                       } 
                    }



                    public function updateequipmenttemplatereadingorderAction()
                    {        
                        $data = $this->_request->getPost();
                        $task = json_decode($data['reading']);
                        $taskmodule = new Model_PmTemplate();
                         $return = $this->validationEquipmentTemplatedragandrop($task);
                         $order = 1;

                        if($return==1){
                            foreach($task as $val){
                                //print_r($val);
                                //die;
                                if(!empty($val->children) && $val->idSubset){
                                            $getdata = array("View_order"=>$order);
                                            $taskmodule->UpdateEquipmentTemplateodrderreading($getdata,$val->idSubset);
                                            $order++;
                                    foreach($val->children as $data){

                                        if(!empty($data->idRoot)){
                                            //echo "idroot children";
                                            $getdata = array("View_order"=>$order,"Parent_ID"=>$val->idSubset);
                                            $taskmodule->UpdateEquipmentTemplateodrderreading($getdata,$data->idRoot);
                                        }
                                        if(!empty($data->id)){
                                            //echo "idroot children";
                                            $getdata = array("View_order"=>$order,"Parent_ID"=>$val->idSubset);
                                            $taskmodule->UpdateEquipmentTemplateodrderreading($getdata,$data->id);
                                        }                                

                                        $order++;
                                    }
                                }else{

                                    if(!empty($val->idRoot)){
                                        //echo "root";
                                        $getdata = array("View_order"=>$order,"Parent_ID"=>0);
                                        $taskmodule->UpdateEquipmentTemplateodrderreading($getdata,$val->idRoot);
                                    }

                                    if(!empty($val->id)){
                                        //echo "id";
                                        $getdata = array("View_order"=>$order,"Parent_ID"=>0);
                                        $taskmodule->UpdateEquipmentTemplateodrderreading($getdata,$val->id);
                                    }                                
                                    $order++;
                                }
                            }
                        }
                        if($return==0){
                                $msg['status'] = 'error';
                                $msg['msg'] = 'Error : This move not posible please try other';
                            }else{
                                $msg['status'] = 'success';
                                $msg['msg'] = 'Order save sucessfully';
                            }
                            echo json_encode($msg);
                            exit();  
                    }

                    public function editequipmenttemplatesubsetreadingAction(){
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
                        public function validateequipmenttemplatesubsetreadingAction()
                        {
                            $data = $this->_request->getPost();
                            $subsetname    = $data['subsetname'];
                            $subsetname_id  = $data['subsetname_id'];
                            $desig_id  = $data['desig_id'];
                            $subset = new Model_PmTemplate();
                            $result = $subset->GetEquipmentTemplateSubsetreadingByName($subsetname,$subsetname_id,$desig_id);
                            if(empty($result)){
                                echo  'true';
                            }else{
                                echo 'false';
                            }
                            exit();
                        }
                    public  function saveequipmenttemplatereadingsubsetAction()
                    {
                        $msg = array();
                        $subsetdata = array();
                        $subset = new Model_PmTemplate();        
                        $data = $this->_request->getPost();
                        $insertdata = array();
                        $insertdata['Reading_Instruction'] = $data['Reading_Instruction'];
                        $insertdata['AU_Template_Designation_ID'] = $data['AU_Template_Designation_ID'];
                        $result = $subset->InsertEquipmentTemplateReading($insertdata);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';

                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Subset save sucessfully';
                            $msg["InsertId"] = $result;
                        }
                        echo json_encode($msg);
                        exit();
                    }
                    // subset data

                    public function updateequipmenttemplatereadingsubsetAction(){
                        $data = $this->_request->getPost();
                        if(!empty($data)){
                            $task = new Model_PmTemplate();
                            $task_id = $data['subsetname_id'];
                            unset($data['subsetname_id']);
                            $result = $task->UpdateEquipmentTemplateodrderreading($data,$task_id);
                            if(empty($result)){
                                $msg['status'] = 'error';
                                $msg['msg'] = 'Error for save data';
                            }else{
                                $msg['status'] = 'success';
                                $msg['msg'] = 'Update subset save sucessfully';
                            }
                            echo json_encode($msg);
                            exit();
                       }
                    }

                    /// create subset level 
                    public function createequipmenttemplatereadingsubsetAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $desig_id = $param['desig_id'];
                        // send data in view pages
                        $this->view->desig_id = $desig_id;
                    }

                    /* view add reading */
                    public function viewequipmenttemplateaddreadingAction(){

                        $this->_helper->getHelper('layout')->disableLayout(); 
                        $param = $this->getRequest()->getParams();

                        $desig_id = $param['desig_id'];
                        $subset = new Model_PmTemplate();                
                        $allsubset = $subset->GetAllEquipmentTemplateSubset_reading($desig_id);

                        $freq = array();
                        $frequency = $subset->GetallEquipmentTemplatefrequency();
                        foreach($frequency as $val){
                              $freq[$val->id] = $val->name;  
                        }

                        $startdateadj = array();
                        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                        foreach($startdateadjustment as $val){
                              $startdateadj[$val->id] = $val->name;  
                        }

                        $alljobtime = array();
                        $jobtime = $subset->GetallEquipmentTemplatejobtime();
                        foreach($jobtime as $val){
                              $alljobtime[$val->id] = $val->name;  
                        }

                        $allunitofmeasure = array();
                        $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
                        foreach($unitofmeasure as $val){
                              $allunitofmeasure[$val->id] = $val->name;  
                        }

                        /* send data in view pages */
                        $this->view->frequency  = $freq;
                        $this->view->startdateadjustment = $startdateadj;
                        $this->view->jobtime = $alljobtime;
                        $this->view->unitofmeasure = $allunitofmeasure;
                        $this->view->subset = $allsubset;
                        $this->view->desig_id = $desig_id;


                    }
                    /* View All Reading */
                    public function viewequipmenttemplatereadingAction(){
                        $this->_helper->getHelper('layout')->disableLayout();
                        $param = $this->getRequest()->getParams();
                        $desig_id = $param['desig_id'];
                        $subset = new Model_PmTemplate();
                        $data = $this->_request->getPost();                
                        if(!empty($desig_id)){
                            $allsubset = $subset->GetAllEquipmentTemplateSubset_reading($desig_id);
                            
                            $allreading = array();
                            $view_empty_subset = array();
                            $getallreading = $subset->GetAllEquipmentTemplateReadingParent($desig_id);
                            //print_r($getallreading);
                            //die;
                            foreach($getallreading as $sub){
                                $subreading = $subset->GetEquipmentTemplateReadingBysubsetId($sub->AU_Template_Reading_ID);
                                if(!empty($subreading)){
                                    $allreading[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                                    $allreading[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                                    $allreading[$sub->AU_Template_Reading_ID]['task'] = $subreading;
                                    }else if(empty($sub->AU_Frequency_ID)){
                                        $view_empty_subset[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                                        $view_empty_subset[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                                        $view_empty_subset[$sub->AU_Template_Reading_ID]['task'] = "";
                         
                                }else{
                                    $allreading[][] = $sub;
                                }                
                            }
                            $allreading = array_merge($allreading,$view_empty_subset);
                            $freq = array();
                            $frequency = $subset->GetallEquipmentTemplatefrequency();
                            foreach($frequency as $val){
                                if($val->column==1)
                                    $freq[$val->AU_Frequency_ID] = $val->Name;
                                if($val->column==2)
                                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
                            }

                            $startdate_ad = array();
                            $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                            foreach($startdateadjustment as $val){
                                  $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                            }

                            $alljobtime = array();
                            $jobtime = $subset->GetallEquipmentTemplatejobtime();
                            foreach($jobtime as $val){
                                  $alljobtime[$val->JobTime_Value] = $val->JobTime_Name;  
                            }

                            $allunitofmeasure = array();
                            $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
                            foreach($unitofmeasure as $val){
                                  $allunitofmeasure[$val->AU_uom_ID] = $val->Name;  
                            }

                            $listview = explode(',',$param['viewlist']);

                            /* send data in view pages */            
                            $this->view->frequency  = $freq;
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

                    public function taskequipmenttemplatefrequencyAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $subset = new Model_PmTemplate();
                        $desig_id = $param['desig_id'];
                        $section = $param['sec'];
                        $Intreval = array();
                        $frequency = $subset->GetallEquipmentTemplatefrequency();
                        foreach($frequency as $val){
                                if($val->column==1)
                                    $freq[$val->AU_Frequency_ID] = $val->Name;
                                if($val->column==2)
                                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
                        }

                        $this->view->desig_id = $desig_id;
                        $this->view->sec = $section;
                        $this->view->CustFreq = $Intreval;
                        $this->view->Freq = $freq;
                    }

                    //// Task Frequency subset
                    public function taskequipmenttemplatefrequencysubsetAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $subset = new Model_PmTemplate();
                        $desig_id = $param['desig_id'];
                        $parent_id = $param['parent_id'];
                        $Intreval = array();
                        $frequency = $subset->GetallEquipmentTemplatefrequency();
                        foreach($frequency as $val){
                                if($val->column==1)
                                    $freq[$val->AU_Frequency_ID] = $val->Name;
                                if($val->column==2)
                                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
                        }
                        $this->view->desig_id = $desig_id;
                        $this->view->parent_id = $parent_id;
                        $this->view->CustFreq= $Intreval;
                        $this->view->Freq= $freq;

                    }
                    /// Root frequency 
                    public function updateequipmenttemplatetaskrootfrequeancyAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $Frequency_ID = $data['Frequency_ID'];
                        $desig_id = $data['desig_id'];
                        $updata['Interval_Value'] = $data['Interval_Value'];
                        $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
                        $inclidesubset = $data['includesubset'];
                        $result  = $task->updateEquipmentTemplate_grouptask($updata,$desig_id,$inclidesubset);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Frequency Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /* subset frequency */ 
                    public function updateequipmenttemplatetaskfrequeancysubsetAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $parent_id = $data['parent_id'];
                        $desig_id = $data['desig_id'];
                        $updata['Interval_Value'] = $data['Interval_Value'];
                        $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
                        $result  = $task->updateEquipmentTemplate_grouptasksubset($updata,$desig_id,$parent_id);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Frequecy Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /* Start date section start */

                    public function taskequipmenttemplatestartdateAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $section = $param['sec'];
                        $this->view->desig_id = $templete_id;
                        $this->view->sec = $section;
                    }

                    public function taskequipmenttemplatestartdatesubsetAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $parent_id = $param['parent_id'];
                        $this->view->desig_id = $templete_id;
                        $this->view->parent_id = $parent_id;
                    }

                    /* Root start date */

                    public function updateequipmenttemplatetaskrootstartdateAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $includesubset = $data['includesubset'];
                        $desig_id = $data['desig_id'];
                        $updata['Start_date'] = $data['startdate'];
                        $result  = $task->updateEquipmentTemplate_grouptask($updata,$desig_id,$includesubset);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Start Date Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /* update data  subset start date */ 
                    public function updateequipmenttemplatetasksubsetstartdateAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $parent_id = $data['parent_id'];
                        $desig_id = $data['desig_id'];
                        $updata['Start_date'] = $data['startdate'];
                        $result  = $task->updateEquipmentTemplate_grouptasksubset($updata,$desig_id,$parent_id);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Start Date Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /*  Task start date of month secton start */

                    /* popup section start */
                    public function taskequipmenttemplatestartdateofmonthAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $section = $param['sec'];
                        $this->view->desig_id = $templete_id;
                    }
                    public function taskequipmenttemplatestartdateofmonthsubsetAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $parent_id = $param['parent_id'];
                        $this->view->desig_id = $templete_id;
                        $this->view->parent_id = $parent_id;
                    }

                    /* popup section End */

                    /* Update tast root start date of month */
                    public function updateequipmenttemplatetaskrootstartdateofmonthAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $updata = array();
                        $includesubset = $data['includesubset'];
                        $desig_id = $data['desig_id'];
                        $date = $data['startdateofmonth'];
                        //$alltask  = $task->Getalltaskbygroupmodification($desig_id,$includesubset);

                        $updata['Startdate_month'] = $data['startdateofmonth'];
                        $result  = $task->updateEquipmentTemplate_grouptask($updata,$desig_id,$includesubset);

                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Start Date of Month sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /* Update tast subset start date of month */
                    public function updateequipmenttemplatetasksubsetstartdateofmonthAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $updata = array();
                        $parent_id = $data['parent_id'];
                        $desig_id = $data['desig_id'];
                        $date = $data['startdateofmonth'];
                        $updata['startdate_month'] = $data['startdateofmonth'];

                        $result  = $task->updateEquipmentTemplate_grouptasksubset($updata,$desig_id,$parent_id);


                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Start Date of month Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /* Start date Adjustment started */

                    /* popup section start */
                    public function taskequipmenttemplatestartdateadjustmentAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $section = $param['sec'];
                        $subset = new Model_PmTemplate();
                        $startdate_ad = array();
                        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                        foreach($startdateadjustment as $val){
                              $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                        }
                        $this->view->startdateofad = $startdate_ad;
                        $this->view->desig_id = $templete_id;

                    }
                    public function taskequipmenttemplatestartdateadjustmentsubsetAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $parent_id = $param['parent_id'];
                        $subset = new Model_PmTemplate();
                        $startdate_ad = array();
                        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                        foreach($startdateadjustment as $val){
                              $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                        }
                        $this->view->startdateofad = $startdate_ad;
                        $this->view->desig_id = $templete_id;
                        $this->view->parent_id = $parent_id;
                    }

                    /* popup section End */

                    /* Root start date adjustment section */

                    public function updateequipmenttemplatetaskrootstartdateadjustmentAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $updata = array();
                        $includesubset = $data['includesubset'];
                        $desig_id = $data['desig_id'];
                        $updata['AU_sda_ID'] = $data['startdateadjustment'];
                        $result  = $task->updateEquipmentTemplate_grouptask($updata,$desig_id,$includesubset);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /* update data  subset start date */ 
                    public function updateequipmenttemplatetasksubsetstartdateadjustmentAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $updata = array();
                        $parent_id = $data['parent_id'];
                        $desig_id = $data['desig_id'];
                        $updata['AU_sda_ID'] = $data['startdateadjustment'];
                        $result  = $task->updateEquipmentTemplate_grouptasksubset($updata,$desig_id,$parent_id);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }
                    /* Task  group modification stop */

                    /* Reading group modification Start */

                    /* popup section start */
                    public function readingequipmenttemplatefrequencyAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $section = $param['sec'];
                        $subset = new Model_PmTemplate();

                        $frequency = $subset->GetallEquipmentTemplatefrequency();
                        foreach($frequency as $val){
                                if($val->column==1)
                                    $freq[$val->AU_Frequency_ID] = $val->Name;
                                if($val->column==2)
                                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
                        }

                        $this->view->desig_id = $desig_id;
                        //$this->view->sec = $section;
                        $this->view->CustFreq = $Intreval;
                        $this->view->Freq = $freq;
                        $this->view->desig_id = $templete_id;

                    }
                    public function readingequipmenttemplatefrequencysubsetAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $subset = new Model_PmTemplate();
                        $desig_id = $param['desig_id'];
                        $parent_id = $param['parent_id'];
                        $frequency = $subset->GetallEquipmentTemplatefrequency();
                        foreach($frequency as $val){
                                if($val->column==1)
                                    $freq[$val->AU_Frequency_ID] = $val->Name;
                                if($val->column==2)
                                    $Intreval[$val->AU_Frequency_ID] = $val->Name;
                        }

                        $this->view->desig_id = $desig_id;
                        //$this->view->sec = $section;
                        $this->view->Freq = $freq;
                        $this->view->CustFreq = $Intreval;
                        $this->view->parent_id = $parent_id;
                    }            
                    /* popup section End */

                    /* Update reading frequency group modification*/
                    public function updateequipmenttemplatereadingrootfrequeancyAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $updata = array();
                        $inclidesubset = $data['includesubset'];
                        $desig_id = $data['desig_id'];
                        $updata['Interval_Value'] = $data['Interval_Value'];
                        $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
                        $result  = $task->updateEquipmentTemplate_groupreading($updata,$desig_id,$inclidesubset);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Frequency Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }
                    /* subset frequency */ 
                    public function updateequipmenttemplatereadingfrequeancysubsetAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $parent_id = $data['parent_id'];                
                        $desig_id = $data['desig_id'];
                        $updata['Interval_Value'] = $data['Interval_Value'];
                        $updata['AU_Frequency_ID'] = $data['Frequency_ID'];
                        $result  = $task->updateEquipmentTemplate_groupreadingsubset($updata,$desig_id,$parent_id);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Frequecy Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }



                    /* Reading  Start date section start */

                    /* popup section start */
                    public function readingequipmenttemplatestartdateAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $section = $param['sec'];
                        $this->view->desig_id = $templete_id;
                        $this->view->sec = $section;
                    }

                    public function readingequipmenttemplatestartdatesubsetAction(){
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

                    public function updateequipmenttemplatereadingrootstartdateAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $includesubset = $data['includesubset'];
                        $desig_id = $data['desig_id'];
                        $updata['Start_date'] = $data['startdate'];
                        $result  = $task->updateEquipmentTemplate_groupreading($updata,$desig_id,$includesubset);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Start Date Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /* 
                     *  Update Reading start date subset Group modication 
                    */
                    public function updateequipmenttemplatereadingsubsetstartdateAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $parent_id = $data['parent_id'];
                        $desig_id = $data['desig_id'];
                        $updata['Start_date'] = $data['startdate'];
                        $result  = $task->updateEquipmentTemplate_groupreadingsubset($updata,$desig_id,$parent_id);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Start Date Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /*  Reading start date of month secton start */

                    /* popup section start */
                    public function readingequipmenttemplatestartdateofmonthAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $section = $param['sec'];
                        $this->view->desig_id = $templete_id;
                    }
                    public function readingequipmenttemplatestartdateofmonthsubsetAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $parent_id = $param['parent_id'];
                        $this->view->desig_id = $templete_id;
                        $this->view->parent_id = $parent_id;
                    }

                    /* popup section End */

                    /* Update tast root start date of month */
                    public function updateequipmenttemplatereadingrootstartdateofmonthAction(){
                        $data = $this->_request->getPost();

                        $reading = new Model_PmTemplate();
                        $updata = array();
                        $includesubset = $data['includesubset'];
                        $desig_id = $data['desig_id'];
                        $date = $data['startdateofmonth'];
                        //$alltask  = $task->Getalltaskbygroupmodification($desig_id,$includesubset);

                        $updata['Startdate_month'] = $data['startdateofmonth'];
                        $result  = $reading->updateEquipmentTemplate_groupreading($updata,$desig_id,$includesubset);



                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Start Date of Month sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /* Update tast subset start date of month */
                    public function updateequipmenttemplatereadingsubsetstartdateofmonthAction(){
                        $data = $this->_request->getPost();
                        $reading = new Model_PmTemplate();
                        $updata = array();
                        $parent_id = $data['parent_id'];
                        $desig_id = $data['desig_id'];
                        $updata['Startdate_month'] = $data['startdateofmonth'];

                        $result  = $reading->updateEquipmentTemplate_groupreadingsubset($updata,$desig_id,$parent_id);

                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Start Date of month Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /*  Reading Start date Adjustment started */

                    /* popup section start */
                    public function readingequipmenttemplatestartdateadjustmentAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $section = $param['sec'];
                        $subset = new Model_PmTemplate();
                        $startdate_ad = array();
                        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                        foreach($startdateadjustment as $val){
                              $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                        }
                        $this->view->startdateofad = $startdate_ad;
                        $this->view->desig_id = $templete_id;
                    }
                    public function readingequipmenttemplatestartdateadjustmentsubsetAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $parent_id = $param['parent_id'];
                        $subset = new Model_PmTemplate();
                        $startdate_ad = array();
                        $startdateadjustment = $subset->GetallEquipmentTemplatestartdateadjustment();
                        foreach($startdateadjustment as $val){
                              $startdate_ad[$val->AU_sda_ID] = $val->Name;  
                        }
                        $this->view->startdateofad = $startdate_ad;
                        $this->view->desig_id = $templete_id;
                        $this->view->parent_id = $parent_id;
                    }

                    /* popup section End */

                    /* Root start date adjustment section */

                    public function updateequipmenttemplatereadingrootstartdateadjustmentAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $includesubset = $data['includesubset'];
                        $desig_id = $data['desig_id'];
                        $updata['AU_sda_ID'] = $data['startdateadjustment'];
                        $result  = $task->updateEquipmentTemplate_groupreading($updata,$desig_id,$includesubset);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /* update data  subset start date */ 
                    public function updateequipmenttemplatereadingsubsetstartdateadjustmentAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $parent_id = $data['parent_id'];
                        $desig_id = $data['desig_id'];
                        $updata['AU_sda_ID'] = $data['startdateadjustment'];
                        $result  = $task->updateEquipmentTemplate_groupreadingsubset($updata,$desig_id,$parent_id);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Start Date Adjustment Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }
                    /* End Reading start date Adjustment section */

                    /* Start Reading value section */

                    /* popup section start */
                    public function readingequipmenttemplatevalueAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $section = $param['sec'];
                        $this->view->desig_id = $templete_id;
                    }
                    public function readingequipmenttemplatevaluesubsetAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $templete_id = $param['desig_id'];
                        $parent_id = $param['parent_id'];
                        $this->view->desig_id = $templete_id;
                        $this->view->parent_id = $parent_id;
                    }

                    /* popup section End */

                    /* Root start date adjustment section */

                    public function updateequipmenttemplatereadingrootreadingvalueAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $includesubset = $data['includesubset'];
                        $desig_id = $data['desig_id'];
                        $updata['Reading_Value'] = $data['reading_value'];
                        $result  = $task->updateEquipmentTemplate_groupreading($updata,$desig_id,$includesubset);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Reading Value On Root Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /* update data  subset start date */ 
                    public function updateequipmenttemplatereadingsubsetreadingvalueAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $parent_id = $data['parent_id'];
                        $desig_id = $data['desig_id'];
                        $updata['Reading_Value'] = $data['readingvalue'];
                        $result  = $task->updateEquipmentTemplate_groupreadingsubset($updata,$desig_id,$parent_id);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Reading Value On Subset Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }
                    /* End Reading value section */

                  /* Start Unit Of Measure section */

                    /* popup section start */
                    public function readingequipmenttemplateunitofmeasureAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $subset = new Model_PmTemplate();
                        $templete_id = $param['desig_id'];
                        $section = $param['sec'];
                        $allunitofmeasure = array();
                        $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
                        foreach($unitofmeasure as $val){
                              $allunitofmeasure[$val->AU_uom_ID] = $val->Name;  
                        }

                        /* Send data to View pages*/
                        $this->view->desig_id = $templete_id;
                        $this->view->unitofmeasure = $allunitofmeasure;
                    }
                    public function readingequipmenttemplateunitofmeasuresubsetAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $subset = new Model_PmTemplate();
                        $templete_id = $param['desig_id'];
                        $parent_id = $param['parent_id'];
                        $allunitofmeasure = array();
                        $unitofmeasure = $subset->GetallEquipmentTemplateunitofmeasure();
                        foreach($unitofmeasure as $val){
                              $allunitofmeasure[$val->AU_uom_ID] = $val->Name;  
                        }

                        /* Send data to View pages*/
                        $this->view->desig_id = $templete_id;
                        $this->view->unitofmeasure = $allunitofmeasure;
                        $this->view->parent_id = $parent_id;
                    }

                    /* popup section End */

                    /* Root start date adjustment section */

                    public function updateequipmenttemplatereadingrootunitofmeasureAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $includesubset = $data['includesubset'];
                        $desig_id = $data['desig_id'];
                        $updata['AU_uom_ID'] = $data['unitofmeasure'];
                        $result  = $task->updateEquipmentTemplate_groupreading($updata,$desig_id,$includesubset);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Reading Value On Root Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /* update data  subset start date */ 
                    public function updateequipmenttemplatereadingsubsetunitofmeasureAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $parent_id = $data['parent_id'];
                        $desig_id = $data['desig_id'];
                        $updata['AU_uom_ID'] = $data['unitofmeasure'];
                        $result  = $task->updateEquipmentTemplate_groupreadingsubset($updata,$desig_id,$parent_id);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Reading Value On Subset Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }
                  /* End Unit Of Measure section */ 

                /* Start Unit Of Measure section */

                    /* popup section start */
                    public function readingequipmenttemplatetoleranceAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $subset = new Model_PmTemplate();
                        $templete_id = $param['desig_id'];
                        $section = $param['sec'];               

                        /* Send data to View pages*/
                        $this->view->desig_id = $templete_id;
                    }
                    public function readingequipmenttemplatetolerancesubsetAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $subset = new Model_PmTemplate();
                        $templete_id = $param['desig_id'];
                        $parent_id = $param['parent_id'];                

                        /* Send data to View pages*/
                        $this->view->desig_id = $templete_id;
                        $this->view->parent_id = $parent_id;
                    }

                    /* popup section End */

                    /* Root start date adjustment section */

                    public function updateequipmenttemplatereadingroottoleranceAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $includesubset = $data['includesubset'];
                        $desig_id = $data['desig_id'];
                        $updata['Tolerance'] = $data['tolerance'];
                        $result  = $task->updateEquipmentTemplate_groupreading($updata,$desig_id,$includesubset);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Reading Value On Root Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }

                    /* update data  subset start date */ 
                    public function updateequipmenttemplatereadingsubsettoleranceAction(){
                        $data = $this->_request->getPost();
                        $task = new Model_PmTemplate();
                        $parent_id = $data['parent_id'];
                        $desig_id = $data['desig_id'];
                        $updata['Tolerance'] = $data['tolerance'];
                        $result  = $task->updateEquipmentTemplate_groupreadingsubset($updata,$desig_id,$parent_id);
                        if(empty($result)){
                            $msg['status'] = 'error';
                            $msg['msg'] = 'Error for save data';
                        }else{
                            $msg['status'] = 'success';
                            $msg['msg'] = 'Update Reading Value On Subset Sucessfully';
                        }
                        echo json_encode($msg);
                        exit();
                    }
                    /* End Unit Of Measure section */     


                /* End Group Modification section */

                    /* Import section */

                    public function importequipmenttemplateAction(){
                        $this->_helper->layout()->disableLayout();
                        //$this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->_request->getPost();
                        $desig_id = $param['desig_id'];
                        $template = new Model_PmTemplate();
                        $resultDesign = $template->get_allEquipmentTemplate_typedesignation($desig_id);

                        /* send data to view page*/
                        $this->view->designation = $resultDesign;
                        $this->view->desig_id = $desig_id;

                    }

                    public function importequipmenttemplatereadingAction(){
                        $this->_helper->layout()->disableLayout();
                        //$this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();
                        $desig_id = $param['desig_id'];
                        $template = new Model_PmTemplate();
                        $resultDesign = $template->get_allEquipmentTemplate_typedesignation($desig_id);

                        /* send data to view page*/
                        $this->view->designation = $resultDesign;
                        $this->view->desig_id = $desig_id;

                    }
                    public function importequipmenttemplateapplyAction(){
                        $data = $this->_request->getPost();
                        $desig_id = $data['desig_id'];
                        $import_id = $data['import_id'];
                        $template = new Model_PmTemplate();
                        $task_subset = $template->getEquipmentTemplate_subsetbyid('pm_au_template_task',$import_id);
                        //print_r($task_subset);
                        //die;
                        /* task section */
                            foreach($task_subset as $tsubset){
                                $innertask = $template->getEquipmentTemplate_taskbysubsetId('pm_au_template_task',$import_id,$tsubset->AU_Template_Task_ID);
                                $data = (array) $tsubset;
                                unset($data['AU_Template_Task_ID']);
                                $data['AU_Template_Designation_ID'] = $desig_id;
                                //$$data
                                 //print_r($data);
                                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                                $data['User_id'] = $user_id;
                                $parent_id = $template->insertEquipmentTemplatesubset($data);

                                if(!empty($innertask)){
                                    foreach($innertask as $task){
                                        $innerdata =(array)$task;
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
                            
                            $task_data = $template->get_tabledata_Au('pm_au_template_task',$import_id);
                            foreach($task_data as $tdata){
                                $data =(array) $tdata;
                                unset($data['AU_Template_Task_ID']);
                                $data['AU_Template_Designation_ID'] = $desig_id;
                                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                                $data['User_id'] = $user_id;
                                $template->InsertEquipmentTemplatetask($data);
                            }
                            
                         /* Reading section start*/
                            $reading_subset = $template->getEquipmentTemplate_subsetbyid('pm_au_template_reading',$import_id);
                            foreach($reading_subset as $tsubset){
                                $innertask = $template->getEquipmentTemplate_taskbysubsetId('pm_au_template_reading',$import_id,$tsubset->AU_Template_Reading_ID);
                                $data = (array) $tsubset;
                                unset($data['AU_Template_Reading_ID']);
                                $data['AU_Template_Designation_ID'] = $desig_id;
                                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                                $data['User_id'] = $user_id;
                                $parent_id = $template->InsertEquipmentTemplateReadingsubset($data);

                                if(!empty($innertask)){

                                    foreach($innertask as $task){
                                        $innerdata =(array)$task;
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
                            $task_data = $template->getEquipmentTemplate_tabledata('pm_au_template_reading',$import_id);
                            foreach($task_data as $tdata){
                                $data = (array) $tdata;
                                unset($data['AU_Template_Reading_ID']);
                                $data['AU_Template_Designation_ID'] = $desig_id;
                                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                                $data['User_id'] = $user_id;
                                $result = $template->InsertEquipmentTemplateReading($data);
                            }
                            if(empty($result)){
                                $msg['status'] = 'error';
                                $msg['msg'] = 'Error for save data';
                            }else{
                                $msg['status'] = 'success';
                                $msg['msg'] = 'Import All The Data Sucessfully';
                            }
                            echo json_encode($msg);
                            exit();


                    }



                    /*  End import section */

                   function equipmenttemplatecustometimejobAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $param = $this->getRequest()->getParams();       
                        $freq = explode("-",$param['timejob']);               

                        $this->view->customedata = $freq;
                        $subset = new Model_PmTemplate();
                        $alljobtime = array();
                        $jobtime = $subset->GetallEquipmentTemplatejobtime();
                        foreach($jobtime as $val){
                              $alljobtime[$val->id] = $val->name;  
                        }
                        $this->view->timejob = $alljobtime;
                   }

                   function insertequipmenttemplatefrequencyAction(){
                       $data = $this->_request->getPost();
                       $InsertData = array(
                                           "Name" =>$data['name'],
                                           "value" =>$data['value'],
                                           "type" =>$data['type'],
                                            );
                        $subset = new Model_PmTemplate();
                        $last_id = $subset->InsertEquipmentTemplateFrequencyData($InsertData);
                        $return = array("id"=>$last_id);
                        echo json_encode($return);
                        die;

                   }

                   public function getequipmenttemplatesubsetAction(){
                       $data = $this->_request->getPost();
                       $desig_id  = $data['desig_id'];
                       $subset = new Model_PmTemplate();
                       $allsubset = $subset->GetAllEquipmentTemplateSubset($desig_id);
                       echo json_encode($allsubset);
                       die;
                   }

                   public function getequipmenttemplatesubsetreadingAction(){
                       $data = $this->_request->getPost();
                       $desig_id  = $data['desig_id'];
                       $subset = new Model_PmTemplate();
                       $allsubset = $subset->GetAllEquipmentTemplateSubsetReading($desig_id);
                       echo json_encode($allsubset);
                       die;
                   }
                   
                   public function importtemplateAction(){
                       
                        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                        
                        $this->_helper->layout()->setLayout('popuplayout');
                        $build_ID =  $_SESSION['current_building'];
                        //die;
                        $template = new Model_PmTemplate();
                        $templatedata = array();
                        $design = $template->GetallAUTemplateDetails($build_ID);
                        $usertemplate = $template->GetallAUTemplateDetailsByUserId($build_ID,$user_id);
                         

                        $design1 = $template->GetallVTTemplateDetails();
                        $allTemplate = array_merge($design,$design1);
                      
                        $AllTemplateList = array();
                        $i = 1;
                        /* User section */
                        foreach($design as $data){
                              $AllTemplateList[$i]['TemplateName'] = $data->AU_Template_Name;
                              $AllTemplateList[$i]['desig_id'] = $data->AU_Template_Designation_ID;
                              $AllTemplateList[$i]['TypeDesignation'] = $data->AU_TypeDesignation;
                              $AllTemplateList[$i]['TypeDescritpion'] = $data->AU_TypeDescritpion;
                              $AllTemplateList[$i]['admin_template'] = "";
                              $i++;
                        }
                        /* VT Admin section */
                        foreach($design1 as $data){
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
                   
                   
                    public function importdesiganitiontemplateAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $data = $this->_request->getPost();
                        $desig_id  = $data['design_id'];
                        $type  = $data['type'];
                        $subset = new Model_PmTemplate();
                        
                        // task Section
                        if($type=="AU"){
                            $alltask = array();
                            $view_empty_subset = array();
                            $getalltask = $subset->GetAllEquipmentTemplatetaskparent($desig_id);
                            //print_r($getalltask);
                            foreach($getalltask as $sub){
                                $subtask = $subset->GetallTaskBysubsetId_Import($sub->AU_Template_Task_ID);
                                if(!empty($subtask)){
                                    $alltask[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                                    $alltask[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                                    $alltask[$sub->AU_Template_Task_ID]['startdate'] = $sub->AU_Template_Task_ID;
                                    $alltask[$sub->AU_Template_Task_ID]['assignto'] = $sub->AU_Template_Task_ID;
                                    $alltask[$sub->AU_Template_Task_ID]['task'] =  $subtask;
                                    //array("Task_Instruction"=>$subtask->Task_Instruction,"View_order"=>$subtask->View_order);
                                    
                                 }else if(empty($sub->AU_Frequency_ID)){
                                    $view_empty_subset[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                                    $view_empty_subset[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                                    $view_empty_subset[$sub->AU_Template_Task_ID]['task'] = "";
                                }else{
                                    $alltask[][] = $sub;
                                    //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
                                }                
                             }
                           $alltask = array_merge($alltask,$view_empty_subset); 
                        }else{

                            $alltask = array();
                            $view_empty_subset = array();
                            $getalltask = $subset->GetAlltaskparentImport($desig_id);
                            //print_r($getalltask);
                            foreach($getalltask as $sub){
                                $subtask = $subset->GetallTaskBysubsetIdImport($sub->VT_Template_Task_ID);
                                //print_r($sub);
                                if(!empty($subtask)){
                                     $alltask[$sub->VT_Template_Task_ID]['name'] = $sub->Task_Instruction;
                                     $alltask[$sub->VT_Template_Task_ID]['id'] = $sub->VT_Template_Task_ID;
                                     $alltask[$sub->VT_Template_Task_ID]['task'] = $subtask;
                                 }else if(empty($sub->AU_Frequency_ID)){
                                    $view_empty_subset[$sub->VT_Template_Task_ID]['name'] = $sub->Task_Instruction;
                                    $view_empty_subset[$sub->VT_Template_Task_ID]['id'] = $sub->VT_Template_Task_ID;
                                    $view_empty_subset[$sub->VT_Template_Task_ID]['task'] = "";
                                }else{
                                     $alltask[][] = $sub;
                                        //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
                                }                
                            }
                            $alltask = array_merge($alltask,$view_empty_subset); 
                            //print_r($alltask);
                            //die;
                        }

                        // Reading Section Start
                            if($type=="AU"){
                                 $allreading = array();
                                 $view_empty_subset = array();
                                 $getalltask = $subset->GetAllEquipmentTemplateReadingParent($desig_id);
                                 //print_r($getalltask);
                                 foreach($getalltask as $sub){
                                     //echo $sub->AU_Template_Reading_ID;
                                     $subtask = $subset->GetEquipmentTemplateReadingBysubsetId_import($sub->AU_Template_Reading_ID);
                                     if(!empty($subtask)){
                                         $allreading[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                                         $allreading[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                                         //$allreading[$sub->AU_Template_Reading_ID]['startdate'] = $sub->AU_Template_Reading_ID;
                                         //$allreading[$sub->AU_Template_Reading_ID]['assignto'] = $sub->AU_Template_Reading_ID;
                                         $allreading[$sub->AU_Template_Reading_ID]['task'] =  $subtask;//array("Reading_Instruction"=>$subtask->Reading_Instruction,"View_order"=>$subtask->View_order);
                                        }else if(empty($sub->AU_Frequency_ID)){
                                            $view_empty_subset[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                                            $view_empty_subset[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                                            $view_empty_subset[$sub->AU_Template_Reading_ID]['task'] = "";
                                        }else{
                                             $allreading[][] = $sub;
                                                //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
                                        }                
                                 }
                                 $allreading = array_merge($allreading,$view_empty_subset);
                                //print_r($allreading);
                                //die;
                            }else{

                                 $allreading = array();
                                 $view_empty_subset = array();
                                 $getalltask = $subset->GetAllreadingparentImport($desig_id);
                                 //print_r($getalltask);
                                 
                                 foreach($getalltask as $sub){
                                     $subtask = $subset->GetallReadingBysubsetIdImport($sub->VT_Template_Reading_ID);
                                      
                                     if(!empty($subtask)){
                                         $allreading[$sub->VT_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                                         $allreading[$sub->VT_Template_Reading_ID]['id'] = $sub->VT_Template_Reading_ID;
                                         $allreading[$sub->VT_Template_Reading_ID]['task'] = $subtask;
                                     }else if(empty($sub->AU_Frequency_ID)){
                                            $view_empty_subset[$sub->VT_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                                            $view_empty_subset[$sub->VT_Template_Reading_ID]['id'] = $sub->VT_Template_Reading_ID;
                                            $view_empty_subset[$sub->VT_Template_Reading_ID]['task'] = "";
                                        }else{
                                             $allreading[][] = $sub;
                                                //array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
                                        }                  
                                 }
                                 $allreading = array_merge($allreading,$view_empty_subset); 
                            }
                        
                       
                        /* send data to view page*/
                        $this->view->taskdata = $alltask;
                        $this->view->readingdata =$allreading;

                   }
                   
                   public function importdesiganitiontemplatechangeAction(){
                       $this->_helper->layout()->setLayout('popuplayout');
                       $data = $this->_request->getPost();
                       $desig_id  = $data['design_id'];
                       $type  = $data['type'];

                       $subset = new Model_PmTemplate();
                       
                       // task Section
                             $alltask = array();
                             $getalltask = $subset->GetAllEquipmentTemplatetaskparent($desig_id);
                             foreach($getalltask as $sub){
                                 $subtask = $subset->GetallTaskBysubsetId_Import($sub->AU_Template_Task_ID);
                                 if(!empty($subtask)){
                                     $alltask[$sub->AU_Template_Task_ID]['name'] = $sub->Task_Instruction;
                                     $alltask[$sub->AU_Template_Task_ID]['id'] = $sub->AU_Template_Task_ID;
                                     $alltask[$sub->AU_Template_Task_ID]['Start_date'] = $sub->AU_Template_Task_ID;
                                     $alltask[$sub->AU_Template_Task_ID]['Assigned_to'] = $sub->AU_Template_Task_ID;
                                     $alltask[$sub->AU_Template_Task_ID]['task'] =  $subtask;//array("Task_Instruction"=>$subtask->Task_Instruction,"View_order"=>$subtask->View_order);
                                 }else{
                                     $alltask[][] = $sub;//array("Task_Instruction"=>$sub->Task_Instruction,"View_order"=>$sub->View_order);
                                 }                
                             }
                        //}
                        
                        ///die;
                        // Reading Section Start
                         //   if($type=="AU"){
                                $allreading = array();
                                $getalltask = $subset->GetAllEquipmentTemplateReadingParent($desig_id);
                                //print_r($getalltask);
                                foreach($getalltask as $sub){
                                    //echo $sub->AU_Template_Reading_ID;
                                    $subtask = $subset->GetEquipmentTemplateReadingBysubsetId_import($sub->AU_Template_Reading_ID);
                                     
                                    if(!empty($subtask)){
                                        $allreading[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                                        $allreading[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                                        $allreading[$sub->AU_Template_Reading_ID]['task'] =  $subtask;//array("Reading_Instruction"=>$subtask->Reading_Instruction,"View_order"=>$subtask->View_order);
                                    }else{
                                        $allreading[][] = $sub;//array("Reading_Instruction"=>$sub->Reading_Instruction,"View_order"=>$sub->View_order);
                                    }                
                                }
                                // print_r($allreading);
                                //die;
                            //}else{

//                                 $allreading = array();
//                                 $getalltask = $subset->GetAllreadingparentImport($desig_id);
//                                 //print_r($getalltask);
//                                 
//                                 foreach($getalltask as $sub){
//                                     $subtask = $subset->GetallReadingBysubsetIdImport($sub->VT_Template_Reading_ID);
//                                    // print_r($subtask);
//                                     if(!empty($subtask)){
//                                         $allreading[$sub->VT_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
//                                         $allreading[$sub->VT_Template_Reading_ID]['id'] = $sub->VT_Template_Reading_ID;
//                                         $allreading[$sub->VT_Template_Reading_ID]['task'] = $subtask;
//                                     }else{
//                                         $allreading[][] = $sub;
//                                     }                
//                                 }
//                            }
                        
                       
                        /* send data to view page*/
                        $this->view->taskdata = $alltask;
                        $this->view->readingdata = $allreading;

                   }
                
                public function importtemplatebytypeAction(){
                        $this->_helper->layout()->setLayout('popuplayout');
                        $build_ID =  $_SESSION['current_building'];
                        $data = $this->_request->getPost();
                        $type = $data['type'];
                        
                        $template = new Model_PmTemplate();
                        $templatedata = array();
                        $build_ID =  $_SESSION['current_building'];
                        $design = $template->GetallAUTemplateDetails($build_ID);
                        $design1 = $template->GetallVTTemplateDetails();
                        $AllTemplateList = array();
                        $i = 1;
                        if($type=='all'){
                            


                            /* VT Admin section */
                            foreach($design1 as $data){
                                  $AllTemplateList[$i]['TemplateName'] = $data->VT_Template_Name;
                                  $AllTemplateList[$i]['desig_id'] = $data->VT_Template_Designation_ID;
                                  $AllTemplateList[$i]['TypeDesignation'] = $data->VT_TypeDesignation;
                                  $AllTemplateList[$i]['TypeDescritpion'] = $data->VT_TypeDescritpion;
                                  $AllTemplateList[$i]['admin_template'] = $data->VT_Admin_Template;
                                  $i++;
                            }
                            
                        }else if($type=='vtonly'){
                            
                            /* VT Admin section */
                            foreach($design1 as $data){
                                    if($data->VT_Admin_Template=='Yes'){
                                        $AllTemplateList[$i]['TemplateName'] = $data->VT_Template_Name;
                                        $AllTemplateList[$i]['desig_id'] = $data->VT_Template_Designation_ID;
                                        $AllTemplateList[$i]['TypeDesignation'] = $data->VT_TypeDesignation;
                                        $AllTemplateList[$i]['TypeDescritpion'] = $data->VT_TypeDescritpion;
                                        $AllTemplateList[$i]['admin_template'] = $data->VT_Admin_Template;
                                        $i++;
                                    }
                                  
                            }
                            
                        }else{


                            /* User section */
                            foreach($design1 as $data){
                                if($data->VT_Admin_Template=='No'){
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
                public function importtemplatedatainuserAction(){
                        $data = $this->_request->getPost();
                        $action = $data['action'];
                        $design_id = $data['design_id'];
                        $type = $data['type'];
                        $tempoption = $data['tempoption'];
                        $template_name = $data['template_name'];
                        if(!empty($data['imp_design_id'])){
                            $imp_design_id = $data['imp_design_id'];
                        }
                        $templatedata = array();
                        $typedata = array();
                        //echo $type;
                        //echo $tempoption;
                        //die;
                        //$import_id = $data['import_id'];
                        $template = new Model_PmTemplate();
                        if(trim($type)=='VT'){
                            $task = $template->GetTemplateandDesighationDetails_import($design_id);
                            $task = $task[0];
                            if($tempoption==1){
                                $templatedata['AU_Template_Name'] = $task->VT_Template_Name;                            
                                $templatedata['BuildingID'] =  $_SESSION['current_building'];
                                $templatedata['user_id'] = $_SESSION['Zend_Auth']['storage']->uid;
                                $template_id = $template->InsertEquipmentTemplateName($templatedata);
                                $typedata['AU_Template_Name_ID'] = $template_id;
                                $typedata['AU_TypeDesignation'] = $task->VT_TypeDesignation;
                                if($action==0){
                                    $typedata['AU_TypeDesignation'] = $task->VT_TypeDesignation."-".rand(10,99999);
                                }

                                $typedata['AU_TypeDescritpion'] = $task->VT_TypeDescritpion;
                                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                                $typedata['user_id'] = $user_id;
                                $newdesign_id = $template->InsertEquipmentTemplateTypeDesignation($typedata);
                            }else if($tempoption==2){
                                $typedata['AU_Template_Name_ID'] = $imp_design_id;
                                $typedata['AU_TypeDesignation'] = $task->VT_TypeDesignation;
                                $typedata['AU_TypeDescritpion'] = $task->VT_TypeDescritpion;
                                if($action==0){
                                    $typedata['AU_TypeDesignation'] = $task->VT_TypeDesignation."-".rand(10,99999);
                                }
                                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                                $typedata['user_id'] = $user_id;
                                $newdesign_id = $template->InsertEquipmentTemplateTypeDesignation($typedata);
                                //$newdesign_id = $imp_design_id;
                                
                            }else{
                                $templatedata['AU_Template_Name'] = $template_name;                            
                                $templatedata['BuildingID'] =  $_SESSION['current_building'];
                                $templatedata['user_id'] = $_SESSION['Zend_Auth']['storage']->uid;
                                $template_id = $template->InsertEquipmentTemplateName($templatedata);
                                $typedata['AU_Template_Name_ID'] = $template_id;
                                $typedata['AU_TypeDesignation'] = $task->VT_TypeDesignation;
                                if($action==0){
                                    $typedata['AU_TypeDesignation'] = $task->VT_TypeDesignation."-".rand(10,99999);
                                }
                                $typedata['AU_TypeDescritpion'] = $task->VT_TypeDescritpion;
                                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                                $typedata['user_id'] = $user_id;
                                $newdesign_id = $template->InsertEquipmentTemplateTypeDesignation($typedata);
                                

                            }
                            
                            //die;
                            /* Task Section  */
                            $task_subset = $template->getEquipmentTemplate_subsetbyid_import('pm_vt_template_task',$design_id);
                            foreach($task_subset as $tsubset){
                                $innertask = $template->getEquipmentTemplate_taskbysubsetId_import('pm_vt_template_task',$design_id,$tsubset->VT_Template_Task_ID);
                                $data = (array) $tsubset;
                                unset($data['VT_Template_Task_ID']);
                                $data['AU_Template_Designation_ID'] = $newdesign_id;
                                unset($data['VT_Template_Designation_ID']);
                                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                                $data['User_ID'] = $user_id;
                                $parent_id = $template->insertEquipmentTemplatesubset($data);
                                if(!empty($innertask)){
                                    foreach($innertask as $task){
                                        $innerdata =(array)$task;
                                        unset($innerdata['VT_Template_Task_ID']);
                                        $taskdate = strtotime($innerdata['Startdate_month']." ".$innerdata['Start_date']);
                                        $currentdate = strtotime("now");
                                        if(!empty($taskdate)){
                                            while($taskdate <= $currentdate){
                                                $taskdate = $this->update_task_by_frequency($innerdata);
                                                $innerdata['Start_date'] = date("F Y",$taskdate);
                                                $innerdata['Startdate_month'] = date("j",$taskdate);
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
                            $task_data = $template->get_tabledata('pm_vt_template_task',$design_id);
                            foreach($task_data as $tdata){
                                $data =(array) $tdata;
                                unset($data['VT_Template_Task_ID']);                                
                                $data['AU_Template_Designation_ID'] = $newdesign_id;
                                unset($data['VT_Template_Designation_ID']);
                                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                                $taskdate = strtotime($data['Startdate_month']." ".$data['Start_date']);                                        
                                $currentdate = strtotime("now");
                               if(!empty($taskdate))
                                while($taskdate <= $currentdate){
                                    $taskdate = $this->update_task_by_frequency($data);
                                    //echo date("d F Y",$taskdate);
                                    $data['Start_date'] = date("F Y",$taskdate);
                                    $data['Startdate_month'] = date("j",$taskdate);
                                }
                                $data['User_ID'] = $user_id;
                                $template->InsertEquipmentTemplatetask($data);
                            }
                            //die('sanjay');
                            
                         /* Reading section start*/
                            //$task_subset = $template->getEquipmentTemplate_subsetbyid_import('pm_vt_template_task',$design_id);
                            //echo $design_id;
                            $reading_subset = $template->getEquipmentTemplate_subsetbyid_import('pm_vt_template_reading',$design_id);
                            //print_r($reading_subset);
                            //die;
                            foreach($reading_subset as $tsubset){
                                $innertask = $template->getEquipmentTemplate_taskbysubsetId_import('pm_vt_template_reading',$design_id,$tsubset->VT_Template_Reading_ID);
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

                                if(!empty($innertask)){

                                    foreach($innertask as $task){
                                        $innerdata =(array)$task;
                                        unset($innerdata['VT_Template_Reading_ID']);
                                        unset($innerdata['VT_Template_Designation_ID']);
                                        $taskdate = strtotime($innerdata['Startdate_month']." ".$innerdata['Start_date']);

                                         $currentdate = strtotime("now");
                                       
                                        if(!empty($taskdate))
                                        while($taskdate <= $currentdate){
                                            //echo "inloop";
                                            $taskdate = $this->update_task_by_frequency($innerdata);
                                            //echo date("d F Y",$taskdate);
                                            $innerdata['Start_date'] = date("F Y",$taskdate);
                                            $innerdata['Startdate_month'] = date("j",$taskdate);
                                            
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
                            $task_data = $template->get_tabledata('pm_vt_template_reading',$design_id);
                           // print_r($task_data);
                            foreach($task_data as $tdata){
                                $data = (array) $tdata;

                                unset($data['VT_Template_Reading_ID']);
                                unset($data['VT_Template_Designation_ID']);
                                $taskdate = strtotime($data['Startdate_month']." ".$data['Start_date']);
                                $currentdate = strtotime("now");

                               
                                if(!empty($taskdate)){
                                    while($taskdate <= $currentdate){
                                        $taskdate = $this->update_task_by_frequency($data);
                                        //echodate("d F Y",$taskdate);
                                        $data['Start_date'] = date("F Y",$taskdate);
                                        $data['Startdate_month'] = date("j",$taskdate);
                                    }
                                }
                                $data['AU_Template_Designation_ID'] = $newdesign_id;
                                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                                $data['User_ID'] = $user_id;
                                $result = $template->InsertEquipmentTemplateReading($data);
                            } 
                       
                        }
                            if(empty($result)){
                                $msg['status'] = 'error';
                                $msg['msg'] = 'Error for save data';
                            }else{
                                $msg['status'] = 'success';
                                $msg['msg'] = 'Import All The Data Sucessfully';
                            }
                            echo json_encode($msg);
                            exit();
                }
                
                public function update_task_by_frequency($data){
                            
                            $template = new Model_PmTemplate();
                            //print_r($data);
                           
                            //echo $data['AU_Frequency_ID'];
                            $fdata = $template->GetfrequencybyId($data['AU_Frequency_ID']);
                            $fdata = $fdata[0];
                            if($data['Interval_Value']==0 || $data['Interval_Value']==1){
                                $frequency = $fdata->Interval;
                                //if()
                                if($data['AU_Frequency_ID']==4){
                                    $nextdate = strtotime("+3 month",strtotime($data['Startdate_month']." ".$data['Start_date']));
                                }else{
                                    $nextdate = strtotime("+1 ".$frequency."",strtotime($data['Startdate_month']." ".$data['Start_date']));
                                }
                                
                            }else{
                                
                                if($data['AU_Frequency_ID']==4){                                    
                                    $intyear = 3 * $data['Interval_Value'];                                    
                                    $nextdate = strtotime("+".$intyear." month",strtotime($data['Startdate_month']." ".$data['Start_date']));
                                }else{
                                    
                                        $frequency = $data['Interval_Value'].' '.$fdata->Interval;
                                        $nextdate = strtotime("+".$frequency."",strtotime($data['Startdate_month']." ".$data['Start_date']));
                                }
                                //echo date("d F Y",$nextdate);
                                //die;
                                
                            }

                            return $nextdate;
                    
                }
                
                public function validationEquipmentTemplatedragandrop($data){
                        $ret_data = 1;
                        foreach ($data as $val){
                            //print_r($val);
                            if($val->idRoot){
                                if(!empty($val->children))
                                    $ret_data = 0;

                            }
                            if($val->idSubset){
                                foreach ($val->children as $data){
                                    if($data->idSubset){
                                        $ret_data = 0;
                                    }
                                }
                            }
                        }
                        return $ret_data;
                    }
                    
    /************************************************** Equipment Section start ********************/
                    
              public function equipmentAction(){
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
                    foreach ($companyListing as $cl){
                        $buildIds[] = $cl['build_id'];
                    }

                    $build_ID = $this->_getParam('bid', '');
                    if (empty($build_ID) && (isset( $_COOKIE['build_cookie']) && in_array( $_COOKIE['build_cookie'], $buildIds)))
                        $build_ID = $_COOKIE['build_cookie'];
                    else
                        $set_cookie = setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
                    
                    if ($companyListing != '') {
                        if ($build_ID != '')
                            $select_build_id = $build_ID;
                        else
                            $select_build_id = $companyListing[0]['build_id'];
                    }
                    $user_id=$_SESSION['Zend_Auth']['storage']->uid;
                    if(empty($_SESSION['current_building'])){
                        $_SESSION['current_building'] = $select_build_id;
                    }
                    setcookie('build_cookie', $select_build_id, time() + (86400 / 24), "/");
                    $this->view->companyListing = $companyListing;
                    $this->view->select_build_id = $select_build_id;
               
                    $templateName = "";
                    $designationName = "";
                    $data = $this->_request->getPost();
                    $template = new Model_PmTemplate();
                    $templatedata = array();
                    if($data['search']=='Search'){
                        $templateName = $data['templatename'];
                        $designationName = $data['designationname'];
                        $tempdata = $template->GetAllEquipmentTemplateName($templateName,$select_build_id);
                        
                        foreach($tempdata as $temp){
                                $find = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID,$designationName,$select_build_id);
                                if(!empty($find) && $designationName!=""){
                                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name] = $temp->AU_Template_Name;
                                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name_ID] = $temp->AU_Template_Name_ID;
                                    $templatedata[$temp->AU_Template_Name_ID][AU_TypeDesignation] = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID,$designationName,$select_build_id);
                                }else if($designationName==""){
                                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name] = $temp->AU_Template_Name;
                                    $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name_ID] = $temp->AU_Template_Name_ID;
                                    $templatedata[$temp->AU_Template_Name_ID][AU_TypeDesignation] = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID,$designationName,$select_build_id);
                                }
                        }
                    }else{
                        $tempdata = $template->GetAllEquipmentTemplateName("",$select_build_id);
                        foreach($tempdata as $temp){ 
                                $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name] = $temp->AU_Template_Name;
                                $templatedata[$temp->AU_Template_Name_ID][AU_Template_Name_ID] = $temp->AU_Template_Name_ID;
                                $templatedata[$temp->AU_Template_Name_ID][AU_TypeDesignation] = $template->GetEquipmentTemplateDetails($temp->AU_Template_Name_ID,$designationName,$select_build_id);
                        }
                    }
                    
                    $this->view->templatedetails = "";//$templatedata;
                    $this->view->templateName = "";//$templateName;
                    $this->view->designationName = "";//$designationName;
                }
                 public function addequipmentAction(){
                    $this->_helper->layout()->setLayout('popuplayout');
                    $subset = new Model_PmTemplate();
                    $data = $this->_request->getParams();
                    $desig_id  = $data['desig_id'];                    
                    $user_id = $_SESSION['Zend_Auth']['storage']->uid;
                    $this->_helper->layout()->setLayout('popuplayout');
                    $build_ID =  $_SESSION['current_building'];
                    $template = new Model_PmTemplate();
                    $templatedata = array();
                    $design = $template->GetallAUTemplateDetails($build_ID);
                    $usertemplate = $template->GetallAUTemplateDetailsByUserId($build_ID,$user_id);


                    $design1 = $template->GetallVTTemplateDetails();
                    $allTemplate = array_merge($design,$design1);

                    $AllTemplateList = array();
                    $i = 1;
                    /* User section */
                    foreach($design as $data){
                          $AllTemplateList[$i]['TemplateName'] = $data->AU_Template_Name;
                          $AllTemplateList[$i]['desig_id'] = $data->AU_Template_Designation_ID;
                          $AllTemplateList[$i]['TypeDesignation'] = $data->AU_TypeDesignation;
                          $AllTemplateList[$i]['TypeDescritpion'] = $data->AU_TypeDescritpion;
                          $AllTemplateList[$i]['admin_template'] = "";
                          $i++;
                    }
                    /* VT Admin section */
//                    foreach($design1 as $data){
//                          $AllTemplateList[$i]['TemplateName'] = $data->VT_Template_Name;
//                          $AllTemplateList[$i]['desig_id'] = $data->VT_Template_Designation_ID;
//                          $AllTemplateList[$i]['TypeDesignation'] = $data->VT_TypeDesignation;
//                          $AllTemplateList[$i]['TypeDescritpion'] = $data->VT_TypeDescritpion;
//                          $AllTemplateList[$i]['admin_template'] = $data->Vt_Admin_Template;
//                          $i++;
//                    }
                    /* get distribution groups */
                    $email_group_model = new Model_EmailGroup();
                    $emailGroup = $email_group_model->get_email_group_by_building_id($_SESSION['current_building']);
                    
                    /* get all Venders */
                    $order = $this->_getParam('order', 'company_name');
                    $dir = $this->_getParam('dir', 'ASC');
                    $vender = new Model_Vendor();
                    $vendorList = $vender->getVendorByBid($_SESSION['current_building'],$order, $dir);

                    
                    $this->view->alltemplates = $AllTemplateList;                       
                    $this->view->usertemplate = $usertemplate;
                    /*  Reading section */ 
                    $ReadingSubset = $subset->GetAllSubset_reading($desig_id);
                    $this->view->frequency  = $freq;
                    $this->view->Interval  = $Intreval;
                    $this->view->startdateadjustment = $startdatead;
                    $this->view->jobtime = $alljobtime;
                    $this->view->desig_id = $desig_id;
                    $this->view->subset = $allsubset;
                    $this->view->ReadingSubset = $ReadingSubset;
                    $this->view->EmailGroup = $emailGroup;
                    $this->view->VendorList = $vendorList;
                 }
                 
                 public function uploadimageAction(){
                     //$data = $this->_request->getPost();
                     //print_r($_SERVER);
                     $uploaddir = BASE_PATH . 'vecrm/public/pm/';
                     $filename = basename($_FILES['file']['name']);
                     //print_r($_FILES['file']);
                     $data = move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir.''.$filename);
                     if($data){
                         echo "true";
                     }else{
                         echo "false";
                     }
                     //var_dump($data);
                     //print_r($data);
                     die;
                     
                 }
                 
                 function saveequipmentAction(){
                    $data = $this->_request->getPost();
                    $equipment = new Model_PmTemplate();
                    print_r($data);
                    //die;
                    $insert_equipment = array('AU_Equipment_Name'=>$data['AU_Equipment_Name']);
                    $equipment_id = $equipment->InsertEquipment($insert_equipment);
                    //get au template name by designation id 
                    $autemplatedetails = $equipment->GetEquipmentTemplateByTypeDesignationID($data['designation_id']);
                    $autemplatedetails = $autemplatedetails[0];
                    print_r($autemplatedetails);
                    unset($data['AU_Equipment_Name']);
                    unset($data['designation_id']);
                    $data['AU_Equipment_Name_ID'] = $equipment_id;
                    $data['AU_Template_Name_ID'] = $autemplatedetails->AU_Template_Name_ID;
                    $data['BuildingID'] = $_SESSION['current_building'];
                    
                    $uploaddir = BASE_PATH . 'vecrm/public/pm/';
                    $filename = basename($_FILES['file']['name']);
                     //print_r($_FILES['file']);
                     $data = move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir.''.$filename);
                     $data['Equipment_Manual'] = $filename;
                    $autemplatedetails = $equipment->InsertEquipmentDetails($data);
                    
                    // import data from au To Equipment
//                    $task_subset = $template->getEquipmentTemplate_subsetbyid('pm_au_template_task',$data['designation_id']);
//                    foreach($task_subset as $tsubset){
//                        $innertask = $template->getEquipmentTemplate_taskbysubsetId('pm_au_template_task',$design_id,$tsubset->AU_Template_Task_ID);
//                        $data = (array) $tsubset;
//                        unset($data['AU_Template_Task_ID']);
//                        $data['AU_Template_Designation_ID'] = $newdesign_id;
//                        //$$data
//                        //print_r($data);
//                        //$user_id = $_SESSION['Zend_Auth']['storage']->uid;
//                        $data['User_id'] = $user_id;
//
//                        $parent_id = $template->insertEquipmentTemplatesubset($data);
//
//                        if(!empty($innertask)){
//                            foreach($innertask as $task){
//                                $innerdata =(array)$task;
//                                unset($innerdata['AU_Template_Task_ID']);
//                                $innerdata['Parent_ID'] = $parent_id;
//                                $innerdata['AU_Template_Designation_ID'] = $newdesign_id;
//                                $user_id = $_SESSION['Zend_Auth']['storage']->uid;
//                                //$innerdata['User_ID'] = $user_id;
//                                //print_r($innerdata);
//                                //die;
//                                $template->InsertEquipmentTemplatetask($innerdata);                                       
//
//                            }
//                        }
//
//                    }
//                    $task_data = $template->get_tabledata_Au('pm_au_template_task',$design_id);
//                    foreach($task_data as $tdata){
//                        $data =(array) $tdata;
//                        unset($data['AU_Template_Task_ID']);
//                        $data['AU_Template_Designation_ID'] = $newdesign_id;
//                        $user_id = $_SESSION['Zend_Auth']['storage']->uid;
//                        $data['User_id'] = $user_id;
//                        $template->InsertEquipmentTemplatetask($data);
//                    }
                    
                    // End 
                    
                    echo $autemplatedetails;
                    
                    print_r($_FILES);
                    print_r($data);
                    die;
                 }
                 
                function getvenderdetailsAction(){
                    $data = $this->_request->getPost();
                    $vid = $data['vid'];
                    $vender = new Model_Vendor(); 
                    $getdata = $vender->getVendor($vid);                  
                    //return $this->_helper->json->sendJson($getdata);
                    echo json_encode($getdata);
                    exit();
                }
}   

ob_end_flush();
?>  
