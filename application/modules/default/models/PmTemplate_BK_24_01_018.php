<?php
/**
 * Description of Work Order
 *
 * @author brijesh
 */
class Model_PmTemplate extends Zend_Db_Table_Abstract {

   protected $_name = 'pm_vt_template_typedesignation';   
   protected $_tab_role = 'pm_vt_template_typedesignation';  
   public $_errorMessage='';
   
    
    public function GetAllTemplateName($search = ""){
        $db = Zend_Db_Table::getDefaultAdapter();
       
        $select = $db->select()
                ->from(array('pm_vt_template_name'));
        if($search!=""){
            $select = $select->where("VT_Template_Name like '%".$search."%'");
        }
        $select = $select->order('VT_Template_Name ASC');
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
        
    }
    
    
    public function GetAllDesignName($search = ""){
        $db = Zend_Db_Table::getDefaultAdapter();
       
        $select = $db->select()
                ->from(array('pm_vt_template_typedesignation'));
        if($search!=""){
            $select = $select->where("VT_TypeDesignation like '%".$search."%'");
        }
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
        
    }
    
    public function GetTemplateNameById($TemplateId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        if(!empty($TemplateId)){
            $select = $db->select()
                ->from(array('pm_vt_template_name'))               
                ->where("VT_Template_Name_ID=?",$TemplateId);
        }
        
        $res = $db->fetchAll($select);

        return ($res && sizeof($res) > 0) ? $res : false;
        
    }
    
    /* get all frequency data */
    
    public function Getallfrequency()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        //if(!empty($TemplateId)){
            $select = $db->select()
                ->from(array('pm_au_frequency'),Array('AU_Frequency_ID','Name','Interval_Value','Interval'));
               /// ->where("type=?","default");
        //}
        
        $res = $db->fetchAll($select);

        return ($res && sizeof($res) > 0) ? $res : false;
        
    }
    
    
    /* get all frequency data */
    
//    public function GetfrequencybyId($id)
//    {
//        $db = Zend_Db_Table::getDefaultAdapter();
//        //if(!empty($TemplateId)){
//            $select = $db->select()
//                ->from(array('pm_au_frequency'),Array('id','name','type','value'))
//                ->where("id=?",$id);
//        //}
//        
//        $res = $db->fetchAll($select);
//
//        return ($res && sizeof($res) > 0) ? $res : false;
//        
//    }
    
    /* get all frequency data */
    
    public function Getallstartdateadjustment()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                ->from(array('pm_au_startdateadjustment'),Array('AU_sda_ID','Name'));  
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
    /* get all task job time data */
    
    public function Getalljobtime()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                ->from(array('pm_au_jobtime'),Array('AU_JobTime_ID','JobTime_Name','JobTime_Value'));  
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
     /* get all Unit of Measure data */
    
    public function Getallunitofmeasure()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                ->from(array('pm_au_unitofmeasure'),Array('AU_uom_ID','Name'));  
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
    public function GetTemplateDetails($TemplateId,$search="")
    {

        $db = Zend_Db_Table::getDefaultAdapter();
        if(!empty($TemplateId)){
            $select = $db->select()
                ->from(array('pm_vt_template_typedesignation'))               
                ->where("VT_Template_Name_ID=?",$TemplateId);
            if($search!=""){
                $select = $select->where("VT_TypeDesignation like '%".$search."%'");
            } 
            $select = $select->order('VT_TypeDesignation ASC');
            $res = $db->fetchAll($select);
            
        }

        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
    public function GetTemplateByName($templateName="",$template_id="")
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        if(!empty($templateName)){
            $select = $db->select()
                ->from(array('pm_vt_template_name'))               
                ->where("VT_Template_Name=?",$templateName);
            if(!empty($template_id)){
                $select = $select->where('VT_Template_Name_ID!=?', $template_id);
            }
           // $select = $select->order('Template_Name ASC');
        }
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
    public function InsertTemplateName($templatedata)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($templatedata)) {
            try {
                $insert = $db->insert('pm_vt_template_name', $templatedata);
                $id = $db->lastInsertId();
                return $id;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }
    
    public function GetTemplateIdByTypeDesignation($typeDesignation="",$typeDesignation_id="")
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        if(!empty($typeDesignation)){
            $select = $db->select()
                ->from(array('pm_vt_template_typedesignation'))               
                ->where("VT_TypeDesignation=?",$typeDesignation);
            if(!empty($typeDesignation_id)){
                $select = $select->where('VT_Template_Designation_ID!=?', $typeDesignation_id);
            }
        }
        //echo $select;
        //die;
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
    public function InsertTypeDesignation($typedata)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($typedata)) {
            try {
                $insert = $db->insert('pm_vt_template_typedesignation', $typedata);
                $id = $db->lastInsertId();
                return $id;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }
    public function updatetemplate($tempdata,$temp_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($tempdata)) {
            try {
                $condition = array('VT_Template_Name_ID = ' . $temp_id);
                $db->update('pm_vt_template_name', $tempdata, $condition);
                return true;
                
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }
    
    public function Updateviewlist($update,$type){
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($update)) {
            try {
                $condition = array("pm_type = '" . $type."'");
                $db->update('pm_view_tables', $update, $condition);
                return true;
                
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }
    
    public function deleteTemplate($template_id) 
    {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $condition = array('VT_Template_Name_ID = ' . $template_id);
            $db->delete('pm_vt_template_name', $condition);
            //$db->update('croom_rate_schedule', $data, $condition);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function GettypedesignationById($desig_id)
    {

        $db = Zend_Db_Table::getDefaultAdapter();
        if(!empty($desig_id)){
            $select = $db->select()
                ->from(array('pm_vt_template_typedesignation'))               
                ->where("VT_Template_Designation_ID=?",$desig_id);
        }

        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
    public function updatetypedesignation($typedata,$type_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($typedata)) {
            try {
                $condition = array('VT_Template_Designation_ID = ' . $type_id);
                $db->update('pm_vt_template_typedesignation', $typedata, $condition);
                return true;
                
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }
    
     public function deleteTypeDesignation($type_id) 
    {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $condition = array('VT_Template_Designation_ID = ' . $type_id);
            $db->delete('pm_vt_template_typedesignation', $condition);
            //$db->update('croom_rate_schedule', $data, $condition);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function deleteTypeDesignationByTemplateId($template_id) 
    {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $condition = array('VT_Template_Name_ID = ' . $template_id);
            $db->delete('pm_vt_template_typedesignation', $condition);
            //$db->update('croom_rate_schedule', $data, $condition);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /* Tesk module start  */    
    public function GetSubseteByName($SubsetName="",$VT_Template_Task_ID="",$desig_id=""){
        $db = Zend_Db_Table::getDefaultAdapter();
        if(!empty($SubsetName)){
            $select = $db->select()
                ->from(array('pm_vt_template_task'))               
                ->where("Task_Instruction=?",$SubsetName)
                ->where("Parent_ID=?",0)
                ->where("VT_Template_Designation_ID=?",$desig_id);
            if(!empty($VT_Template_Task_ID)){
                $select = $select->where('VT_Template_Task_ID!=?', $VT_Template_Task_ID);
            }
        }
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
    public function insertsubset($SubsetData)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($SubsetData)) {
            try {
                $insert = $db->insert('pm_vt_template_task', $SubsetData);
                $id = $db->lastInsertId();
                return $id;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }
    
    public function insertReadingsubset($SubsetData)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($SubsetData)) {
            try {
                $insert = $db->insert('pm_au_template_reading', $SubsetData);
                $id = $db->lastInsertId();
                return $id;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }
    
    public function GetAllSubset($desig_id="")
    {
        $db = Zend_Db_Table::getDefaultAdapter();
       
        $select = $db->select()
                ->from(array('pm_vt_template_task'))
                ->where('AU_Frequency_ID IS NULL or AU_Frequency_ID = ""');
        if(!empty($desig_id)){
                $select = $select->where('VT_Template_Designation_ID=?', $desig_id);
            }
        //echo $select;
        $res = $db->fetchAll($select);
        //print_r($res);
        //die;
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
    public function GetAllSubsetReading($desig_id="")
    {
        $db = Zend_Db_Table::getDefaultAdapter();
       
        $select = $db->select()
                ->from(array('pm_au_template_reading'))
                ->where('AU_Frequency_ID IS NULL or AU_Frequency_ID = ""');
        if(!empty($desig_id)){
                $select = $select->where('VT_Template_Designation_ID=?', $desig_id);
            }
        $res = $db->fetchAll($select);
        //print_r($res);
        //die;
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
     public function GetSubsetById($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
       
        $select = $db->select()
                ->from(array('subset_level'))              
                ->where("id=?",$id);
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
    
    public function GetAlltaskparent($desig_id="")
    {
        $db = Zend_Db_Table::getDefaultAdapter();      
        
        $select = $db->select()
                ->from(array('pm_vt_template_task'))
                ->where("Parent_ID=?",0);
        if(!empty($desig_id)){
                $select = $select->where('VT_Template_Designation_ID=?', $desig_id);
            }
        $select = $select->order('view_order ASC');
        //echo $select;
        //die;
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
    public function GetAlltask()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
       
        
//        $select1 = $db->select()
//                ->from(array('pm_vt_template_task'))
//                ->where("Parent_ID=?",1);
//        $select2 = $db->select()
//                ->from(array('pm_vt_template_task'))       
//                ->where("Parent_ID=?",0);
        $select1  = 'Select  n.* from  pm_vt_template_task  t ,pm_vt_template_task  n  where t.id=n.Parent_ID  and n.Parent_ID <> 0';
        $select2 = 'Select  * from  pm_vt_template_task  p where p.Parent_ID = 0  order by view_order ASC';

        $select = $db->select()
                ->union(array($select1, $select2));
        //echo $select;
        //die;
        //$select = $db->select()
                //->from(array('pm_vt_template_task'));               
            //$select = $select->order('view_order ASC');        
            //->from(array('t' => 'pm_vt_template_task'));
           // ->joinInner(array('sl' => 'subset_level'), 'sl.id = t.subset_id');
        //$select = $select->order('t.view_order ASC');
                
               // ->from(array('t' => 'pm_vt_template_task')); 
                
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    
    public function Inserttask($taskdata)
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                if (!empty($taskdata)) {
                    try {
                        $insert = $db->insert('pm_vt_template_task', $taskdata);
                        $id = $db->lastInsertId();
                        return $id;
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        return false;
                    }
                }
            }
    /// update task data        
    public function Updatetask($taskdata,$task_id)
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                if (!empty($taskdata)) {
                    try {
                        $condition = array('VT_Template_Task_ID = ' . $task_id);
                        $db->update('pm_vt_template_task', $taskdata, $condition);
                        return true;

                    } catch (Exception $e) {
                        echo $e->getMessage();
                        return false;
                    }
                }
            }
    public function GetTaskBysubsetId($suset_id)
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                if(!empty($suset_id)){
                    $select = $db->select()
                        ->from(array('pm_vt_template_task'))               
                        ->where("Parent_ID=?",$suset_id);                    
                }
                        $select = $select->order('view_order ASC');

                $res = $db->fetchAll($select);
                return ($res && sizeof($res) > 0) ? $res : false;
            }
    public function Updateodrder($data,$task_id)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            if (!empty($data)) {
                try {
                    
                    $condition = array('VT_Template_Task_ID = ' . $task_id);
                   // print_r($data);
                   // print_r($condition);
                    $db->update('pm_vt_template_task', $data, $condition);
                    return true;

                } catch (Exception $e) {
                    echo $e->getMessage();
                    return false;
                }
            }
        }
        public function GettaskDataById($task_id)
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                if(!empty($task_id)){
                    $select = $db->select()
                        ->from(array('pm_vt_template_task'))               
                        ->where("VT_Template_Task_ID=?",$task_id);                    
                }
                //$select = $select->order('view_order ASC');
                //echo $select;
                //die;
                $res = $db->fetchAll($select);
                return ($res && sizeof($res) > 0) ? $res : false;
            }
            
        public function deleteReading($task_id) 
        {
            try {
                $db = Zend_Db_Table::getDefaultAdapter();
                $condition = array('AU_Template_Reading_ID = ' . $task_id);             
                $db->delete('pm_au_template_reading', $condition);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        
        // start new section
        // Reading section start
        
        public function InsertReading($data)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            if (!empty($data)) {
                try {
                    $insert = $db->insert('pm_au_template_reading', $data);
                    $id = $db->lastInsertId();
                    return $id;
                } catch (Exception $e){
                    echo $e->getMessage();
                    return false;
                }
            }
        }
        public function GetAllSubset_reading($desig_id="")
            {
                $db = Zend_Db_Table::getDefaultAdapter();

                $select = $db->select()
                        ->from(array('pm_au_template_reading'))
                        ->where('AU_Frequency_ID IS NULL');
                if(!empty($desig_id)){
                        $select = $select->where('VT_Template_Designation_ID=?', $desig_id);
                    }
                $res = $db->fetchAll($select);
                return ($res && sizeof($res) > 0) ? $res : false;
            } 
        public function GetAllReadingParent($desig_id="")
        {
            $db = Zend_Db_Table::getDefaultAdapter();      

            $select = $db->select()
                    ->from(array('pm_au_template_reading'))
                    ->where("Parent_ID=?",0);
            if(!empty($desig_id)){
                    $select = $select->where('VT_Template_Designation_ID=?', $desig_id);
                }
            $select = $select->order('view_order ASC');
             $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        
        public function GetReadingBysubsetId($suset_id)
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                if(!empty($suset_id)){
                    $select = $db->select()
                        ->from(array('pm_au_template_reading'))               
                        ->where("Parent_ID=?",$suset_id);                    
                }
                        $select = $select->order('view_order ASC');

                $res = $db->fetchAll($select);
                return ($res && sizeof($res) > 0) ? $res : false;
            }
         public function GetreadingDataById($id)
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                if(!empty($id)){
                    $select = $db->select()
                        ->from(array('pm_au_template_reading'))               
                        ->where("AU_Template_Reading_ID=?",$id);                    
                }
                //$select = $select->order('view_order ASC');
                //echo $select;
                //die;
                $res = $db->fetchAll($select);
                return ($res && sizeof($res) > 0) ? $res : false;
            }
        /// update task data        
        public function Updatereading($data,$id)
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                //print_r($taskdata);
                if (!empty($data)) {
                    try {
                        $condition = array('AU_Template_Reading_ID = ' . $id);
                        $db->update('pm_au_template_reading', $data, $condition);
                        return true;

                    } catch (Exception $e) {
                        echo $e->getMessage();
                        return false;
                    }
                }
            }
            
        public function deleteTask($id) 
        {
            try {
                $db = Zend_Db_Table::getDefaultAdapter();
                $condition = array('VT_Template_Task_ID = ' . $id);             
                $db->delete('pm_vt_template_task', $condition);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        
         public function deleteTaskByParentId($parent_id) 
        {
            try {
                $db = Zend_Db_Table::getDefaultAdapter();
                $condition = array('Parent_ID = ' . $parent_id);             
                $db->delete('pm_vt_template_task', $condition);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        
        public function deleteReadingByParentId($parent_id) 
        {
            try {
                $db = Zend_Db_Table::getDefaultAdapter();
                $condition = array('Parent_ID = ' . $parent_id);             
                $db->delete('pm_au_template_reading', $condition);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        
        //task 
        public function getallparent($Parent_ID){
            $db = Zend_Db_Table::getDefaultAdapter();
            if(!empty($Parent_ID)){
                $select = $db->select()
                    ->from(array('pm_vt_template_task'))
                    ->where("Parent_ID = ".$Parent_ID);
                }
            //echo $select;
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        //reading 
        public function getallparent_reading($Parent_ID){
            $db = Zend_Db_Table::getDefaultAdapter();
            if(!empty($Parent_ID)){
                $select = $db->select()
                    ->from(array('pm_vt_template_task'))
                    ->where("Parent_ID = ".$Parent_ID);
                }
            //echo $select;
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        
        public function Updateodrderreading($data,$reading_id)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            if (!empty($data)) {
                try {
                    
                    $condition = array('AU_Template_Reading_ID = ' . $reading_id);
                    $db->update('pm_au_template_reading', $data, $condition);
                    return true;

                } catch (Exception $e) {
                    echo $e->getMessage();
                    return false;
                }
            }
        }
         /* Reading module start  */    
        public function GetSubsetreadingByName($SubsetName,$id){
            $db = Zend_Db_Table::getDefaultAdapter();
            if(!empty($SubsetName)){
                $select = $db->select()
                    ->from(array('pm_au_template_reading'))               
                    ->where("Reading_Instruction=?",$SubsetName)
                    ->where("Parent_ID=?",0);
                if(!empty($id)){
                    $select = $select->where('AU_Template_Reading_ID!=?', $id);
                }
            }
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
    ///// group update section start 
        
        public function update_grouptask($data,$desig_id="",$includesubset="")
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    if (!empty($data)) {
                        try {
                             $con = 'VT_Template_Designation_ID='.$desig_id;
                            if($includesubset==""){
                               $con .= ' and Parent_ID = 0 ';
                            }
                            $con .= ' and AU_Frequency_ID is not null';
                            $condition = array($con);
                            $db->update('pm_vt_template_task', $data, $condition);
                            return true;

                        } catch (Exception $e) {
                            echo $e->getMessage();
                            return false;
                        }
                    }
                }
                
        public function update_grouptasksubset($data,$desig_id="",$Parent_ID="")
                {   
                    $db = Zend_Db_Table::getDefaultAdapter();
                    if (!empty($data)) {
                        try {
                            $con = 'VT_Template_Designation_ID='.$desig_id;
                            $con .= ' and Parent_ID = '.$Parent_ID;
                            $con .= ' and AU_Frequency_ID is not null ';
                            $condition = array($con);
                            $db->update('pm_vt_template_task', $data, $condition);
                            return true;

                        } catch (Exception $e) {
                            echo $e->getMessage();
                            return false;
                        }
                    }                      
                }
            /*  Insert the PM Frequency data  */
                public function InsertFrequencyData($data)
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    if (!empty($data)) {
                        try {
                            $insert = $db->insert('pm_frequency', $data);
                            $id = $db->lastInsertId();
                            return $id;
                        } catch (Exception $e) {
                            echo $e->getMessage();
                            return false;
                        }
                    }
                }
           /*  Group modification start date of month update function */
            public function update_grouptask_startdateofmonth($data,$id)
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    if (!empty($data)) {
                        try {
                             $con = 'id='.$id;
                            $condition = array($con);
                            $db->update('pm_vt_template_task', $data, $condition);
                            return true;

                        } catch (Exception $e) {
                            echo $e->getMessage();
                            return false;
                        }
                    }
                }
                
             /* get all task before update chaeck the date*/
                
                public function Getalltaskbygroupmodification($id,$includesubset=""){
                    $db = Zend_Db_Table::getDefaultAdapter();
                    if(!empty($id)){
                        $select = $db->select()
                            ->from(array('pm_vt_template_task'));               
                          
                        if(!empty($includesubset)){
                            $select = $select->where("pm_vt_template_name_id = ".$id."  and frequency is not null");
                        }else{
                            $select = $select->where("pm_vt_template_name_id = ".$id." and Parent_ID = 0 and frequency is not null");
                        }
                    }
                    //echo $select;
                    $res = $db->fetchAll($select);
                    return ($res && sizeof($res) > 0) ? $res : false;
                }
                
                
                public function Getalltasksubsetbygroupmodification($id,$Parent_ID=""){
                    $db = Zend_Db_Table::getDefaultAdapter();
                    if(!empty($id)){
                        $select = $db->select()
                            ->from(array('pm_vt_template_task'))
                            ->where("pm_vt_template_name_id = ".$id."  and Parent_ID = '".$Parent_ID."'");
                        }
                    //echo $select;
                    $res = $db->fetchAll($select);
                    return ($res && sizeof($res) > 0) ? $res : false;
                }
         
        /* Reading Group modification start */
        public function update_groupreading($data,$desig_id="",$includesubset="")
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    if (!empty($data)) {
                        try {
                             $con = 'VT_Template_Designation_ID='.$desig_id;
                            if($includesubset==""){
                               $con .= ' and Parent_ID = 0 ';
                            }
                            $con .= ' and AU_Frequency_ID is not null';
                            $condition = array($con);
                            $db->update('pm_au_template_reading', $data, $condition);
                            return true;

                        } catch (Exception $e) {
                            echo $e->getMessage();
                            return false;
                        }
                    }
                }
                
        public function update_groupreadingsubset($data,$desig_id="",$Parent_ID="")
                {   
                    $db = Zend_Db_Table::getDefaultAdapter();
                    if (!empty($data)) {
                        try {
                            $con = 'VT_Template_Designation_ID='.$desig_id;
                            $con .= ' and Parent_ID = '.$Parent_ID;
                            $con .= ' and AU_Frequency_ID is not null ';
                            $condition = array($con);
                            $db->update('pm_au_template_reading', $data, $condition);
                            return true;

                        } catch (Exception $e) {
                            echo $e->getMessage();
                            return false;
                        }
                    }                      
                }
        /* get all Reading before update chaeck the date*/
                
                public function Getallreadingbygroupmodification($id,$includesubset=""){
                    $db = Zend_Db_Table::getDefaultAdapter();
                    if(!empty($id)){
                        $select = $db->select()
                            ->from(array('pm_au_template_reading'));               
                          
                        if(!empty($includesubset)){
                            $select = $select->where("pm_vt_template_name_id = ".$id."  and frequency is not null");
                        }else{
                            $select = $select->where("pm_vt_template_name_id = ".$id." and Parent_ID = 0 and frequency is not null");
                        }
                    }
                    //echo $select;
                    $res = $db->fetchAll($select);
                    return ($res && sizeof($res) > 0) ? $res : false;
                }
        /*  Group modification start date of month update function */
            public function update_groupreading_startdateofmonth($data,$id)
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    if (!empty($data)) {
                        try {
                             $con = 'id='.$id;
                            $condition = array($con);
                            $db->update('pm_au_template_reading', $data, $condition);
                            return true;

                        } catch (Exception $e) {
                            echo $e->getMessage();
                            return false;
                        }
                    }
                }
            public function Getallreadingsubsetbygroupmodification($id,$Parent_ID=""){
                    $db = Zend_Db_Table::getDefaultAdapter();
                    if(!empty($id)){
                        $select = $db->select()
                            ->from(array('pm_au_template_reading'))
                            ->where("pm_vt_template_name_id = ".$id."  and Parent_ID = '".$Parent_ID."'");
                        }
                    //echo $select;
                    $res = $db->fetchAll($select);
                    return ($res && sizeof($res) > 0) ? $res : false;
                }  
                
        /* View table in list */
              public function get_view_table($type){
                  $db = Zend_Db_Table::getDefaultAdapter();
                    if(!empty($type)){
                        $select = $db->select()
                            ->from(array('pm_view_tables'))
                            ->where("pm_type = '".$type."'");
                        }
                    //echo $select;
                    $res = $db->fetchAll($select);
                    return ($res && sizeof($res) > 0) ? $res : false;
              }
              
        /* Import Template section*/
              
              public function get_all_typedesignation($id){
                  $db = Zend_Db_Table::getDefaultAdapter();
                    if(!empty($id)){
                        $select = $db->select()
                                //->setIntegrityCheck(false) 
                                //->distinct() 
                                ->from(array('pty' => 'pm_vt_template_typedesignation'),array('DISTINCT(pty.VT_TypeDesignation)'))
                                //->from($this->_dbTable, new Zend_Db_Expr('DISTINCT(field_1) as field_1'))
                                //$this->_dbTable, new Zend_Db_Expr('DISTINCT(field_1) as field_1')
                                ->joinInner(array('pt' => 'pm_vt_template_task'),'pty.VT_Template_Designation_ID = pt.VT_Template_Designation_ID', array('pty.VT_Template_Designation_ID'))  
                                ->where("pty.VT_Template_Designation_ID != ".$id);
                        }
                    $res = $db->fetchAll($select);
                    return ($res && sizeof($res) > 0) ? $res : false;
              }
              public function get_subsetbyid($table_name,$desig_id){
                  $db = Zend_Db_Table::getDefaultAdapter();
                    if(!empty($desig_id)){
                        $select = $db->select()
                            ->from(array($table_name))
                            ->where("VT_Template_Designation_ID = ".$desig_id." and AU_Frequency_ID is null and Parent_ID = 0");
                        }
                    //echo $select;
                    $res = $db->fetchAll($select);
                    return ($res && sizeof($res) > 0) ? $res : false;
              }
                public function get_taskbysubsetId($table_name,$desig_id,$Parent_ID){
                  $db = Zend_Db_Table::getDefaultAdapter();
                    if(!empty($desig_id)){
                        $select = $db->select()
                            ->from(array($table_name))
                            ->where("VT_Template_Designation_ID = ".$desig_id."  and Parent_ID = ".$Parent_ID);
                        }
                    //echo $select;
                    $res = $db->fetchAll($select);
                    return ($res && sizeof($res) > 0) ? $res : false;
                }
              public function get_tabledata($table_name,$desig_id){
                  $db = Zend_Db_Table::getDefaultAdapter();
                    if(!empty($desig_id)){
                        $select = $db->select()
                            ->from(array($table_name))
                            ->where("VT_Template_Designation_ID = ".$desig_id." and AU_Frequency_ID is not null  and Parent_ID = 0");
                        }
                    //echo $select;
                    $res = $db->fetchAll($select);
                    return ($res && sizeof($res) > 0) ? $res : false;
                }
                public function get_FrequencydataByID($FreqId){
                  $db = Zend_Db_Table::getDefaultAdapter();
                    if(!empty($FreqId)){
                        $select = $db->select()
                            ->from(array('pm_frequency'))
                            ->where("id = ".$FreqId);
                        }
                    //echo $select;
                    $res = $db->fetchAll($select);
                    return ($res && sizeof($res) > 0) ? $res : false;
                }
                
                /* cronjob module function start */
                
                public function gettaskdata($temp_id = ""){
                    
                    $db = Zend_Db_Table::getDefaultAdapter();
                    if(empty($temp_id)){
                        $select = $db->select()      
                        ->from(array('pt' => 'pm_vt_template_task'),array('pt.Interval_Value as Interval_Data','pt.*'))
                        ->joinInner(array('pf' => 'pm_au_frequency'), 'pt.AU_Frequency_ID = pf.AU_Frequency_ID')
                        ->joinInner(array('ps' => 'pm_au_startdateadjustment'), 'pt.AU_sda_ID = ps.AU_sda_ID')
                        ->where("pt.VT_Template_Designation_ID > 1");
                        }
                    //echo $select;
                    //die;
                    $res = $db->fetchAll($select);
                    return ($res && sizeof($res) > 0) ? $res : false;
                    
                    
                }
                
                
                
}