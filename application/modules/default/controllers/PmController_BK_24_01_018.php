<?php
/**
 * Description of Access Controller
 *
 * @author sanjay
 */
class PmController extends Ve_Controller_Base {

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
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
        //echo "fsdfsdf";
        //die(12);
        // Send data to view page
        
//        print_r($templatedata);
//        print_r($templateName);
//        print_r($designationName);
//            die;
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
        //$param = $this->getRequest()->getParams();       
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
        //print_r($templatedata);
        $templatedata = $templatedata[0];
        //die;
        // send data on view pages
        $this->view->template = $templatedata;
        
    }
    
    // update template Name 
    public  function updatetemplateAction(){
        $msg = array();
        $typedata = array();
        $template = new Model_PmTemplate();       
        $param = $this->_request->getPost();
        $typedata['VT_Template_Name'] = $param['TemplateName'];
        //$typedata['TypeDestination'] = $param['Template_id'];

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
        //print_r($templatedata);
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
            //die;
            //$data = $this->_request->getPost();
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
            //print_r($alltask);
           // die;
            $list = $subset->get_view_table('task');
            $listview = explode(',',$list[0]->display_table_view);
            $freq = array();
            $CustFreq = array();
            $frequency = $subset->Getallfrequency();
            foreach($frequency as $val){
                    
                        $freq[$val->AU_Frequency_ID] = $val->Name;                  
                        $CustFreq[$val->AU_Frequency_ID] = $val->Interval;
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
            $this->view->CustmeFreq = $CustFreq;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;
        }else{            
            exit();
            //return false;
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
            $getalltask = $subset->GetAlltaskparent($desig_id);
            //print_r($getalltask);
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
            //print_r($alltask);
            //die;
            //$list = $subset->get_view_table('task');
            //$listview = explode(',',$list[0]->display_table_view);
            $listview = explode(',',$param['viewlist']);
            $freq = array();
            $CustFreq = array();
            $frequency = $subset->Getallfrequency();
            foreach($frequency as $val){
                    
                        $freq[$val->AU_Frequency_ID] = $val->Name;                  
                        $CustFreq[$val->AU_Frequency_ID] = $val->Interval;
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
            $this->view->CustmeFreq = $CustFreq;
            $this->view->startdateadjustment = $startdate_ad;
            $this->view->jobtime = $alljobtime;
                
                
                //print_r($alltask);
                //die;
                // send data in view pages                
                 
//                $this->view->listview = $listview;
//                $this->view->desig_id = $desig_id;
//                $this->view->alltask = $alltask;
//                $this->view->subset = $allsubset;
//                $this->view->frequency  = $freq;
//                $this->view->CustmeFreq = $CustFreq;
//                $this->view->startdateadjustment = $startdatead;
//                $this->view->jobtime = $alljobtime;
                
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
              $freq[$val->AU_Frequency_ID] = $val->Name;
              $Intreval[$val->AU_Frequency_ID] = $val->Interval;
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
              $freq[$val->AU_Frequency_ID] = $val->Name;
              $Intreval[$val->AU_Frequency_ID] = $val->Interval;
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
        //print_r($data);
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
            $data['View_order'] = rand(50,100);
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
                $frequency = $subset->Getallfrequency();
                foreach($frequency as $val){
                            $freq[$val->AU_Frequency_ID] = $val->Name;                  
                            $CustFreq[$val->AU_Frequency_ID] = $val->Interval;
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
                $this->view->Interval  = $CustFreq;
                $this->view->startdateadjustment = $startdate_ad;
                $this->view->jobtime = $alljobtime;
                $this->view->desig_id = $desig_id;
                $this->view->subset = $allsubset;
                $this->view->taskdata = $TaskData;
//                $AllSubset = $subset->GetAllSubset($desig_id);
//                $TaskData = $subset->GettaskDataById($task_id);
//                $TaskData = $TaskData[0];
//                
//                //print_r($TaskData);
//                //$CustFreq = array();
//                $custfreq=""; 
//                $editfreq = $subset->GetfrequencybyId($TaskData->frequency);
//                 //print_r($editfreq);
//                if($editfreq[0]->type=='custome'){
//                    $custfreq = $editfreq;
//                }
//                //print_r($custfreq);
//                //die;
//                $frequency = $subset->Getallfrequency();
//                foreach($frequency as $val){
//                        if($val->type == 'default')
//                            $freq[$val->id] = $val->name;
//
//                      //$CustFreq[$val->id] = $val->value.' '.$val->name;
//                }
//
//                $startdatead = array();
//                $startdateadjustment = $subset->Getallstartdateadjustment();
//                foreach($startdateadjustment as $val){
//                      $startdatead[$val->id] = $val->name;  
//                }
//
//                $alljobtime = array();
//                $jobtime = $subset->Getalljobtime();
//                foreach($jobtime as $val){
//                      $alljobtime[$val->id] = $val->name;  
//                }
//                
//                // send data in view pages
//                $this->view->frequency  = $freq;
//                $this->view->startdateadjustment = $startdatead;
//                $this->view->jobtime = $alljobtime;                
//                $this->view->desig_id = $desig_id;
//                $this->view->taskdata = $TaskData;
//                $this->view->subset = $AllSubset;
//                $this->view->custfreq = $custfreq;
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
                    $result = $task->deleteTaskByParentId($param['Task_id']); 
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
            $data = $this->_request->getPost();
            $msg = array();
            $list = $subset->get_view_table('Reading');
            $listview = explode(',',$list[0]->display_table_view);
            $allreading = array();
            $getallreading = $subset->GetAllReadingParent($desig_id);
            foreach($getallreading as $sub){
                $subreading = $subset->GetReadingBysubsetId($sub->AU_Template_Reading_ID);
                if(!empty($subreading)){
                    $allreading[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                    $allreading[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                    $allreading[$sub->AU_Template_Reading_ID]['task'] = $subreading;
                }else{
                    $allreading[][] = $sub;
                }                
            }
            
            $freq = array();
            $frequency = $subset->Getallfrequency();
            foreach($frequency as $val){
                        $freq[$val->AU_Frequency_ID] = $val->Name;                  
                        $CustFreq[$val->AU_Frequency_ID] = $val->Interval;
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
            $data['View_order'] = rand(50,100);
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
                      $freq[$val->AU_Frequency_ID] = $val->Name;
                      $Intreval[$val->AU_Frequency_ID] = $val->Interval;
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
                    $result = $reading->deleteReadingByParentId($param['reading_id']); 
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
                if(!empty($data)){
                    $task = new Model_PmTemplate();
                    $reading_id = $data['reading_id'];
                    unset($data['reading_id']);
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
                      $freq[$val->id] = $val->name;  
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
                $subset = new Model_PmTemplate();
                $data = $this->_request->getPost();                
                if(!empty($desig_id)){
                    $allsubset = $subset->GetAllSubset_reading($desig_id);
                    $allreading = array();
                    $getallreading = $subset->GetAllReadingParent($desig_id);
                    foreach($getallreading as $sub){
                        $subreading = $subset->GetReadingBysubsetId($sub->AU_Template_Reading_ID);
                        if(!empty($subreading)){
                            $allreading[$sub->AU_Template_Reading_ID]['name'] = $sub->Reading_Instruction;
                            $allreading[$sub->AU_Template_Reading_ID]['id'] = $sub->AU_Template_Reading_ID;
                            $allreading[$sub->AU_Template_Reading_ID]['task'] = $subreading;
                        }else{
                            $allreading[][] = $sub;
                        }                
                    }

                    $freq = array();
                    $frequency = $subset->Getallfrequency();
                    foreach($frequency as $val){
                                $freq[$val->AU_Frequency_ID] = $val->Name;                  
                                $CustFreq[$val->AU_Frequency_ID] = $val->Interval;
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
                    $this->view->CustmeFreq = $CustFreq;
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
                $CustFreq = array();
                $frequency = $subset->Getallfrequency();
                foreach($frequency as $val){
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                            $CustFreq[$val->AU_Frequency_ID] = $val->Interval;
                }
                
                $this->view->desig_id = $desig_id;
                $this->view->sec = $section;
                $this->view->CustFreq = $CustFreq;
                $this->view->Freq = $freq;
            }
            
            //// Task Frequency subset
            public function taskfrequencysubsetAction(){
                $this->_helper->layout()->setLayout('popuplayout');
                $param = $this->getRequest()->getParams();
                $subset = new Model_PmTemplate();
                $desig_id = $param['desig_id'];
                $parent_id = $param['parent_id'];
                $CustFreq = array();
                $frequency = $subset->Getallfrequency();
                foreach($frequency as $val){
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                            $CustFreq[$val->AU_Frequency_ID] = $val->Interval;
                }
                $this->view->desig_id = $desig_id;
                $this->view->parent_id = $parent_id;
                $this->view->CustFreq= $CustFreq;
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
                
//                foreach($alltask as $taskdata){
//                    $updata = array();
//                    if($date=='lastday'){
//                         $update_day = date('t',strtotime("May 2017"));                         
//                    }else{                        
//                         $update_day = date('j',strtotime($date." May 2017"));
//                    }
//                    
//                    if($taskdata->startdate_month=='lastday'){
//                        $current_day = date('t',strtotime($taskdata->start_date));                  
//                    }else{                        
//                        $current_day = date('t',strtotime($taskdata->startdate_month.$taskdata->start_date));
//                    }
//                    
//                    if($current_day <=$update_day){
//                        $updata['startdate_month'] = 'lastday';
//                    }else{
//                        $updata['startdate_month'] = $data['startdateofmonth'];
//                    }
//                    $result  = $task->update_grouptask_startdateofmonth($updata,$taskdata->id);
//                }
                
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
                
//                //$result  = $task->update_grouptasksubset($updata,$desig_id,$parent_id);
//                $alltask  = $task->Getalltasksubsetbygroupmodification($desig_id,$parent_id);
//                foreach($alltask as $taskdata){
//                    $updata = array();
//                    if($date=='lastday'){
//                         $update_day = date('t',strtotime("May 2017"));                         
//                    }else{                        
//                         $update_day = date('j',strtotime($date." May 2017"));
//                    }
//                    
//                    if($taskdata->startdate_month=='lastday'){
//                        $current_day = date('t',strtotime($taskdata->start_date));                  
//                    }else{                        
//                        $current_day = date('t',strtotime($taskdata->startdate_month.$taskdata->start_date));
//                    }
//                    
//                    if($current_day <=$update_day){
//                        $updata['startdate_month'] = 'lastday';
//                    }else{
//                        $updata['startdate_month'] = $data['startdateofmonth'];
//                    }
//                    $result  = $task->update_grouptask_startdateofmonth($updata,$taskdata->id);
//                }
                
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
                
                $frequency = $subset->Getallfrequency();
                foreach($frequency as $val){
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                            $CustFreq[$val->AU_Frequency_ID] = $val->Interval;
                }
                
                $this->view->desig_id = $desig_id;
                //$this->view->sec = $section;
                $this->view->CustFreq = $CustFreq;
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
                foreach($frequency as $val){
                            $freq[$val->AU_Frequency_ID] = $val->Name;
                            $CustFreq[$val->AU_Frequency_ID] = $val->Interval;
                }
                
                $this->view->desig_id = $desig_id;
                //$this->view->sec = $section;
                $this->view->Freq = $freq;
                $this->view->CustFreq = $CustFreq;
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
                //print_r($task_subset);
                //die;
                /* task section */
                    foreach($task_subset as $tsubset){
                        $innertask = $template->get_taskbysubsetId('pm_vt_template_task',$import_id,$tsubset->VT_Template_Task_ID);
                        $data = (array) $tsubset;
                        unset($data['VT_Template_Task_ID']);
                        $data['VT_Template_Designation_ID'] = $desig_id;
                        //$$data
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
                    $reading_subset = $template->get_subsetbyid('pm_au_template_reading',$import_id);
                    foreach($reading_subset as $tsubset){
                        $innertask = $template->get_taskbysubsetId('pm_au_template_reading',$import_id,$tsubset->AU_Template_Reading_ID);
                        $data = (array) $tsubset;
                        unset($data['AU_Template_Reading_ID']);
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
                                unset($innerdata['AU_Template_Reading_ID']);
                                $template->InsertReading($innerdata);
                            }
                        }

                    }
                    $task_data = $template->get_tabledata('pm_au_template_reading',$import_id);
                    foreach($task_data as $tdata){
                        $data = (array) $tdata;
                        unset($data['AU_Template_Reading_ID']);
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
            
}   


?>
