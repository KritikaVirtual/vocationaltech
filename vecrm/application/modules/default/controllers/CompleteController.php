<?php

class CompleteController extends Ve_Controller_Base
{

    public function init() 
    {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');  
    }
	// Call befor any action and check is user login or not
    public function preDispatch()
    {    	
		if (!Zend_Auth::getInstance()->hasIdentity()) $this->_redirect('/index');
		 $level=(Zend_Auth::getInstance()->getStorage()->read())?Zend_Auth::getInstance()->getStorage()->read()->role_id:'';    	     	
    	$this->userId=Zend_Auth::getInstance()->getStorage()->read()->uid;
    	$this->roleId=Zend_Auth::getInstance()->getStorage()->read()->role_id;
    	$this->cust_id=Zend_Auth::getInstance()->getStorage()->read()->cust_id;
    }
    
    public function workorderAction(){
		$companyListing ='';
		$buildingMapper=new  Model_Building();
		if($this->roleId=='9'){
			 $companyListing = $buildingMapper->getCompanyBuilding($this->cust_id);
			}else{
			$user_build_mod = new Model_UserBuildingModule();
			$buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
			if($buildinglists){
				$build_id_array = array();
				foreach($buildinglists as $buildlist)
				  $build_id_array[] = $buildlist['building_id'];
				$companyListing = $buildingMapper->getBuildingList($build_id_array);			
			}
	     }
	     
	    $wnum=$this->_getParam('wnum','');
		
		$build_ID = $this->_getParam('bid','');	
		
		if($companyListing!=''){
				if($build_ID!='')
				$select_build_id = $build_ID;
				 else
				$select_build_id = $companyListing[0]['build_id'];
		   }
		
		$woModel = new Model_WorkOrder();
		$msg = '';
		$this->view->woData ='';		 		 
		if($wnum!='' && $build_ID!=''){
			$wnData = $woModel->getWoIdByNoNBId($wnum,$build_ID);
			if($wnData){
			$woId = $wnData[0]['woId'];	
			$woData = $woModel->getWorkOrderInfo($woId);
			//var_dump($woData);			
			//exit;
			$this->view->woData = ($woData)?$woData[0]:$woData;
		    }else
		    $msg = 'Invalid Work Order Number Or Building Id';
						
		}else
		 $msg = 'Enter Work Order No.'; 
		 
		 $this->view->msg =  $msg;
		 $this->view->custID = $this->cust_id;
		 $this->view->companyListing = $companyListing;
		 $this->view->select_build_id = $select_build_id;
		 $this->view->wnum = $wnum;
		 
	}// end of work report
    
    
    
	public function showeditwoAction(){
		$this->_helper->layout()->disableLayout(); 
		$woId=$this->_getParam('woId','');
		$woModel = new Model_WorkOrder();
		$this->view->woData ='';		 		 
		if($woId!=''){
			$woData = $woModel->getWorkOrderInfo($woId);
			$this->view->woData = $woData[0];			
		}else{
			echo 'Error occurred to open show edit';
			exit(0);
		} 
		
	}// end of show edit wo
	
	
	public function showtuserAction(){
		$this->_helper->layout()->disableLayout(); 
		$tId=$this->_getParam('tId','');
		if($tId!=''){
			$tuserModel = new Model_TenantUser();
			$tuserList = $tuserModel->getTenantUsers($tId);
			$this->view->tuserList = $tuserList;
		}else{
		    echo 'Error Occurred';
		    exit(0);			
		 }
	} /** end of show tenant user **/
	
	public function tuserinfoAction(){
		$tuid=$this->_getParam('tuid','');
		$message='';
		$userData='';
		if($tuid!=''){
			$tuserModel = new Model_TenantUser();
			$tuserinfo = $tuserModel->getTenantUserById($tuid);
			if($tuserinfo){
			   $tuData = $tuserinfo[0];
			   $userData = array(
			              'suite'=>$tuData->suite_location,
			              'email'=>$tuData->email,
			              'phone'=>$tuData->phoneNumber 
			   );
			   $message='success';
			 }else{
				 $message = 'error';
			 }  
			
		}
		echo json_encode(array('message'=>$message,'userData'=>$userData));
		exit(0);
	}
	
	public function updatewoinfoAction(){
		 if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 //print_r($data);
			 if($data['tenant']=='' || $data['date_received']=='' || $data['time_received']=='' || $data['create_user']=='' || $data['wo_request']==""){
				 echo 'error';
				 exit(0);
			 }else{
				 $updateData = array();
				 $updateData['tenant'] = $data['tenant'];
				 $updateData['create_user'] = $data['create_user'];
				 $updateData['date_requested'] = date('Y-m-d',strtotime($data['date_received']));
				 $updateData['time_requested'] = $data['time_received'];
				 $updateData['work_order_request'] = $data['wo_request'];
				 if($data['status']==1){
					   $updateData['category'] = $data['category'];
					   $whlModel = new Model_WoHistoryLog();		   
					   $whData= array();
					   $whData['woId']=$data['woId'];
					   $whData['log_type']='category';
					   $whData['current_value']=$data['curr_cat'];
					   $whData['change_value']=$data['category'];
					   $whData['user_id']=$this->userId;
					   $insertWHL = $whlModel->insertHistoryLog($whData);
				 }
				 try{
					 $woModel = new Model_WorkOrder();
					 $updateWo = $woModel->updateWorkOrder($updateData,$data['woId']);
					 echo 'success';
				 }catch(Exception $e){
					 //echo $e->getMessage();
					 echo 'error';
				 }
			 }
			 
		 }
		 exit(0);
	} /** end of work order info  **/
	
	public function woparameterAction(){
		$this->_helper->layout()->disableLayout();
		$bId=$this->_getParam('bId','');
		$wpModel = new Model_WoParameter();
		$wpData = '';
		if($bId!=''){
			$wpDetails = $wpModel->getWoParameterByBid($bId);
			$wpData = ($wpDetails)?$wpDetails[0]:'';
		}
		$this->view->bId = $bId;
		$this->view->wpData = $wpData;
	} /** end of show wo parameter **/
	
	public function updateparameterAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 //print_r($data);
			 try{
				 $wpModel = new Model_WoParameter();
				 $wpDetails = $wpModel->getWoParameterByBid($data['building']);
				 $wpId = ($wpDetails)?$wpDetails[0]['wpId']:'0';
				 if($wpId!='0'){
					 $updateData = $wpModel->updateWoParameter($data,$wpId);
				 }else{
					 $insertData = $wpModel->insertWoParameter($data);
				 }
				 echo 'success';
			 }catch(Exception $e){
				 echo 'error';
			 }
			 
		 }
		 exit(0);	 
	}/** end of update wo parameter **/
	
	public function showdescAction(){
		$this->_helper->layout()->disableLayout();
		$woId=$this->_getParam('woId','');
		$wdModel = new Model_WorkDescription();
		$wdData = '';
		if($woId!=''){
			$wpDetails = $wdModel->getDescByWoId($woId);
			$wdData = ($wpDetails)?$wpDetails[0]:'';
		}
		$noteModel = new Model_Notes();
		$this->view->noteList = $noteModel->getNotes();
		$this->view->woId = $woId;
		$this->view->wdData = $wdData;
	}// close show description 
	
	public function savedescriptionAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 if($data['description']==''){
				 echo 'error';
				 exit(0);
			 }
			 //print_r($data);
			 try{
				 $wdModel = new Model_WorkDescription();
				 $wpDetails = $wdModel->getDescByWoId($data['woId']);
				 $id = ($wpDetails)?$wpDetails[0]['id']:'0';
				 if($id!='0'){
					 $updateData = $wdModel->updateDescription($data,$id);
				 }else{
					 $insertData = $wdModel->insertDescription($data);
				 }
				 echo 'success';
			 }catch(Exception $e){
				 echo 'error';
			 }
			 
		 }
		 exit(0);
	}// close save description
	
	public function laborformAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();		
		$this->view->bId= $data['bId'];
		$this->view->woId = $data['woId'];		
	}// close add labor form
	
	public function savelaborAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 if($data['emp_id']=='' || $data['charge_hour']=='' || $data['rate_charge']=='' || $data['job_time']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';
			 }else{			 
				 try{
					 $laborModel = new Model_Labor();
					 if(isset($data['lid']) && $data['lid']!=''){
						 $updateLabor = $laborModel->updateLabor($data,$data['lid']);
					 }else{
						 $insertData = $laborModel->insertLabor($data);
					 }					
					 $message['status'] = 'success';
				     $message['msg']='Labor data has been saved successfully.';
				 }catch(Exception $e){
					$message['status'] = 'error';
				    $message['msg']='Error Occurred during the save labor data';
				 }
			 }
			 
		 }else{
			 $message['status'] = 'error';
			 $message['msg']='Error Occurred during the save labor data';
		 }
		 echo json_encode($message);
		 exit(0);
	}// close save labor charge data
	
	public function editlaborAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();		
		$this->view->bId= $data['bId'];
		$this->view->lid = $data['lid'];
		if($data['lid']!=''){
			$laborModel = new Model_Labor();
			$this->view->laborData = $laborModel->getLabor($data['lid']);
		}else{
			echo 'Invalid data';
			exit(0);
		}
	}// close edit labor form
	
	public function deletelaborAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 if($data['lid']!=''){
				 try{
				     $laborModel = new Model_Labor();
				     $deleteLabor = $laborModel->deleteLabor($data['lid']);
				     echo 'true';				     
				 }catch(Exception $e){
					 echo 'false';
				 }
				 
			 }
		 }
		exit(0); 
	}// close delete labor charge
	
	
	public function addbserviceAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();		
		$this->view->bId= $data['bId'];
		$this->view->woId = $data['woId'];		
	}// close add building service form
	
	public function savebuildingserviceAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 if($data['service']=='' || $data['charge']=='' || $data['amount_requested']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';
			 }else{			 
				 try{
					 $bserviceModel = new Model_BuildingService();
					 if(isset($data['bsId']) && $data['bsId']!=''){
						 $updateBuildingService = $bserviceModel->updateBuildingService($data,$data['bsId']);
					 }else{
						 $insertData = $bserviceModel->insertBuildingService($data);
					 }					
					 $message['status'] = 'success';
				     $message['msg']='Building Service Charge has been saved successfully.';
				 }catch(Exception $e){
					$message['status'] = 'error';
				    $message['msg']='Error Occurred during the save building service charge';
				 }
			 }
			 
		 }else{
			 $message['status'] = 'error';
			 $message['msg']='Error Occurred during the save building service charge';
		 }
		 echo json_encode($message);
		 exit(0);
	}// close save labor charge data
	
	public function editbserviceAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();		
		$this->view->bId= $data['bId'];
		$this->view->bsId = $data['bsId'];
		if($data['bsId']!=''){
			$bserviceModel = new Model_BuildingService();
			$this->view->bserviceData = $bserviceModel->getBuildingService($data['bsId']);
		}else{
			echo 'Invalid data';
			exit(0);
		}		
	}// close edit building service form
	
	public function deletebuildingserviceAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 if($data['bsId']!=''){
				 try{
				     $bserviceModel = new Model_BuildingService();
				     $deleteBuildingService = $bserviceModel->deleteBuildingService($data['bsId']);
				     echo 'true';				     
				 }catch(Exception $e){
					 echo 'false';
				 }
				 
			 }
		 }
		exit(0); 
	}// close delete labor charge
	
	public function addmaterialAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();		
		$this->view->bId= $data['bId'];
		$this->view->woId = $data['woId'];		
	}// close add material charge form
    
    public function savematerialAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 if($data['material_id']=='' || $data['cost']=='' || $data['cost']=='0' || $data['markup']=='' || $data['quantity']=='' || $data['quantity']=='0'){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';
			 }else{			 
				 try{
					 $mcModel = new Model_MaterialCharge();
					 if(isset($data['mcId']) && $data['mcId']!=''){
						 $updateMaterial = $mcModel->updateMaterialCharge($data,$data['mcId']);
					 }else{
						 $insertData = $mcModel->insertMaterialCharge($data);
					 }					
					 $message['status'] = 'success';
				     $message['msg']='Material Charge has been saved successfully.';
				 }catch(Exception $e){
					$message['status'] = 'error';
				    $message['msg']='Error Occurred during the save material charge';
				 }
			 }
			 
		 }else{
			 $message['status'] = 'error';
			 $message['msg']='Error Occurred during the save material charge';
		 }
		 echo json_encode($message);
		 exit(0);
	}// close save material charge data
	
	public function editmaterialAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();		
		$this->view->bId= $data['bId'];
		$this->view->mcId = $data['mcId'];
		if($data['mcId']!=''){
			$mcModel = new Model_MaterialCharge();
			$this->view->materialData = $mcModel->getMaterialCharge($data['mcId']);
		}else{
			echo 'Invalid data';
			exit(0);
		}		
	}// close edit material charge form
	
	public function deletematerialAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 if($data['mcId']!=''){
				 try{
				     $mcModel = new Model_MaterialCharge();
				     $deleteMaterial = $mcModel->deleteMaterialCharge($data['mcId']);
				     echo 'true';				     
				 }catch(Exception $e){
					 echo 'false';
				 }
				 
			 }
		 }
		exit(0); 
	}// close delete material
	
	public function addoutsideAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();		
		$this->view->bId= $data['bId'];
		$this->view->woId = $data['woId'];		
	}/*close add material charge form*/
	
	public function saveoutsideAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 if($data['vendor']=='' || $data['job_cost']=='' || $data['job_cost']=='0' || $data['markup']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';
			 }else{			 
				 try{
					 $osModel = new Model_OutsideService();
					 if(isset($data['osId']) && $data['osId']!=''){
						 $updateOutSide = $osModel->updateOutsideService($data,$data['osId']);
					 }else{
						 $insertData = $osModel->insertOutsideService($data);
					 }					
					 $message['status'] = 'success';
				     $message['msg']='Outside service Charge has been saved successfully.';
				 }catch(Exception $e){
					$message['status'] = 'error';
				    $message['msg']='Error Occurred during the save outside service charge';
				 }
			 }
			 
		 }else{
			 $message['status'] = 'error';
			 $message['msg']='Error Occurred during the save outside service charge';
		 }
		 echo json_encode($message);
		 exit(0);
	}/* close save outside service data*/
	
	public function editoutsideAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();		
		$this->view->bId= $data['bId'];
		$this->view->osId = $data['osId'];
		if($data['osId']!=''){
			$osModel = new Model_OutsideService();
			$this->view->outsideData = $osModel->getOutsideService($data['osId']);
		}else{
			echo 'Invalid data';
			exit(0);
		}		
	}/* close edit outside service form */
	
	public function deleteoutsideAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 if($data['osId']!=''){
				 try{
				     $osModel = new Model_OutsideService();
				     $deleteOutside = $osModel->deleteOutsideService($data['osId']);
				     echo 'true';				     
				 }catch(Exception $e){
					 echo 'false';
				 }
				 
			 }
		 }
		exit(0); 
	}/* close delete outside service */
	
	public function addnoteAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();		
		$this->view->woId = $data['woId'];		
	}/*close add note form*/
	
	public function savenoteAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 if($data['note_date']=='' || $data['note']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';
			 }else{			 
				 try{
					 $wnModel = new Model_WoNote();
					 $data['note_date'] = date('Y-m-d',strtotime($data['note_date']));
					 if(isset($data['wnId']) && $data['wnId']!=''){
						 $updateNote = $wnModel->updateWoNote($data,$data['wnId']);
					 }else{
						 $insertData = $wnModel->insertWoNote($data);
					 }					
					 $message['status'] = 'success';
				     $message['msg']='Note has been saved successfully.';
				 }catch(Exception $e){
					$message['status'] = 'error';
				    $message['msg']='Error Occurred during the save note';
				 }
			 }
			 
		 }else{
			 $message['status'] = 'error';
			 $message['msg']='Error Occurred during the save note';
		 }
		 echo json_encode($message);
		 exit(0);
	}/* close save outside service data*/
	
	public function editnoteAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();				
		$this->view->wnId = $data['wnId'];
		if($data['wnId']!=''){
			 $wnModel = new Model_WoNote();
			$this->view->wnData = $wnModel->getWoNote($data['wnId']);
		}else{
			echo 'Invalid data';
			exit(0);
		}		
	}/* close edit note form */
	
	public function deletenoteAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 if($data['wnId']!=''){
				 try{
				     $wnModel = new Model_WoNote();
				     $deleteNote = $wnModel->deleteWoNote($data['wnId']);
				     echo 'true';				     
				 }catch(Exception $e){
					 echo 'false';
				 }
				 
			 }
		 }
		exit(0); 
	}/* close delete note */
	
	public function addfileAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();		
		$this->view->woId = $data['woId'];
		$this->view->bId = $data['bId'];
		$this->view->wnum = $data['wnum'];		
	}/*close add file form*/
	
	public function uploadfileAction(){
		if($this->getRequest()->getMethod() == 'POST'){
		   $data = $this->getRequest()->getPost();
		   $wo_file = $_FILES['wo_file'];
		   if($data['file_title']=='' || $wo_file['name']=='' || !isset($wo_file['name'])){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';
			 }else{
				 if($_FILES['wo_file']['name']!=''){						
						$uploaddir = BASE_PATH.'public/work_order/';
						$uploadfile_name = 'WO-'.time().'-'. basename($_FILES['wo_file']['name']);                        
						$uploadfile = $uploaddir.''.$uploadfile_name;                        
						if (!file_exists($uploaddir)) {
							mkdir($uploaddir, 0777, true);
						}
						move_uploaded_file($_FILES["wo_file"]["tmp_name"], $uploadfile);												
						$file_name = $uploadfile_name;
						$insertData = array();
						$insertData['woId'] = $data['woId'];
						$insertData['file_title'] = $data['file_title'];
						$insertData['file_name'] = $file_name;
						try{
							$fileModel = new Model_WoFiles();
							$insertRecord = $fileModel->insertWoFile($insertData);
							 $message['status'] = 'success';
							$message['msg']='File uploaded successfully.';							
						}catch(Exception $e){
							$message['status'] = 'error';
							$message['msg']='Error occurred during upload file.';
						}
				  }
			 }
			$this->_redirect('/complete/workorder/bid/'.$data['bId'].'/wnum/'.$data['wnum']); 	
		}
		
		$this->_redirect('/complete/workorder');
		exit(0);
	}/**********close of file upload section********/
	
	public function deletefileAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 if($data['wfId']!=''){
				 try{
				     $fileModel = new Model_WoFiles();
				     $fileData = $fileModel->getWoFiles($data['wfId']);
				     if($fileData){
						 $uploaddir = BASE_PATH.'public/work_order/';
						 $file_name = $fileData[0]['file_name'];
						 $uploadfile = $uploaddir.''.$file_name;
						 if(file_exists($uploadfile)){
							 unlink($uploadfile);
						 } 
						 $deletefile = $fileModel->deleteWoFile($data['wfId']);
						 echo 'true';
					 }else
					 echo 'false';				     
				 }catch(Exception $e){
					 echo 'false';
				 }
				 
			 }
		 }
		exit(0); 
	}/* close delete files */
	
	
	public function wocompleteAction(){
		$message = array();
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 
			 if($data['date_cp_in']=='' || $data['date_cp_out']=='' || $data['time_in']=='' || $data['time_out']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';
			 }else{
					 try{
					   $wcModel = new Model_WoComplete();
					   $data['date_cp_in'] = date('Y-m-d',strtotime($data['date_cp_in']));
					   $data['date_cp_out'] = date('Y-m-d',strtotime($data['date_cp_out']));
					   if($data['wcId']!=''){
						   $updateData = $wcModel->updateWoComplete($data,$data['wcId']);
					   }else{
						   unset($data['wcId']);
						   $insertData = $wcModel->insertWoComplete($data);
					   }
					   /********* update work order status *********/
					   $woId = $data['woId'];
					   $status_changed = false;
					   $wpModel = new Model_WorkOrderUpdate();
					   $currWo = $wpModel->getCurrentWoUpdate($woId);
					   if($currWo){
						   $curr_wo_status = $currWo[0]['wo_status'];
						   if($curr_wo_status!= $data['wo_status']){
							   $status_changed = true;
							   // reset work order update
					          $resetCurrent = $wpModel->updateWorkOrderByWoId(array('current_update'=>0),$woId);
					            $wpData = array();
								$wpData['wo_id'] = $woId;
								$wpData['internal_note'] = '';
								$wpData['wo_status'] = $data['wo_status'];
								$wpData['current_update']=1;
								$wpData['user_id']=$this->userId;
								$wpData['created_at'] = date('Y-m-d H:i:s');
								$insertWp = $wpModel->insertWorkOrderUpdate($wpData);
								
								/*********History Log *********/
								$whlModel = new Model_WoHistoryLog();		   
								$whData= array();
								$whData['woId']=$woId;
								$whData['log_type']='status';
								$whData['current_value']=$curr_wo_status;
								$whData['change_value']=$data['wo_status'];
								$whData['user_id']=$this->userId;
								$insertWHL = $whlModel->insertHistoryLog($whData);
								
								// update work order schedule
								$schModel = new Model_Schedule();
								$schData = $schModel->getSchdeuleByCurrWoStatus($woId,$data['wo_status']);
								if($schData){
									   $wstModel = new Model_WoScheduleStatus();				       
									   // fetch current status work order
									   $wsDetail = $wstModel->getCurrentWs($woId);
									   foreach($wsDetail as $wsd){
										   $wsUpdate = $wstModel->updateWoSchedule(array('current_status'=>0),$wsd['wssId']);
									   }
									   $wss_data = array();
									   $wss_data['worder_id']=	$woId;
									   $wss_data['schedule_id']= $schData[0]->id;
									   $wss_data['priority_id']= $schData[0]->priority_id;
									   $wss_data['status']= 1;
									   //$wss_data['ckey']= md5(time());
									   $wss_data['current_status']= 1;
									   $wss_data['created_at'] = date('Y-m-d H:i:s');
									   $ws_insert = $wstModel->insertWoSchedule($wss_data);
								}
						 
							   /********work order status complete *******/
							   if($data['wo_status']=='6'){
								   /***** description in complete *****/
								   $desc = 'Printed out Badge and delivered to Tenant';
								   $desc_arr = array('description'=>$desc,'woId'=>$woId);
								   $wdModel = new Model_WorkDescription();
								   $wpDetails = $wdModel->getDescByWoId($woId);
								   $id = ($wpDetails)?$wpDetails[0]['id']:'0';
								   if($id!='0'){
										 $updateData = $wdModel->updateDescription($desc_arr,$id);
									 }else{
										 $insertData = $wdModel->insertDescription($desc_arr);
									
									}
									
							      $woModel = new Model_WorkOrder();
							      
							      $workOrder = $woModel->getWorkOrder($woId);		
								  $woData = $workOrder[0];
								  $wpModel = new Model_WoParameter();
								  $wpData = '';
									if($woData['building']!=''){
										$wpDetails = $wpModel->getWoParameterByBid($woData['building']);
										$wpData = ($wpDetails)?$wpDetails[0]:'';
									}
									/******* sent email to tenant ********/
									if($wpData['email_tenant']=='1'){
										$sendTenantMail = $woModel->sendClosedNotification($woId,$this->userId);
									}	
							   }
					   
					      }
					   }
					   $message['status'] = 'success';
					   $message['msg']='Save changes successfully.';
					 }catch(Exception $e){
						 $message['status'] = 'error';
						 $message['msg']='Error occurred.';
					 }  
					 
			 }			 
		 }else{
					 $message['status'] = 'error';
					 $message['msg']='Error Occurred during the save note';
				 }	 
		 echo json_encode($message);
		 exit(0);
	} /********* close of wo complete *********/
}


