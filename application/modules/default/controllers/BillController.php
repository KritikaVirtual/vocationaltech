<?php

/**
 * Description of Bill Controller
 *
 * @author Brijesh Kumar
 */
class BillController extends Ve_Controller_Base {
    
    public function init()  {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');  
       $this->notesModel = new Model_Notes();
       $this->nm = new Zend_Session_Namespace('bill_message');
       $this->accessHelper = $this->_helper->access;
       $this->bill_location = 17;     
    }
    
    // Call befor any action and check is user login or not
    public function preDispatch()
    {    	
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/index');
        }
        
        $level=(Zend_Auth::getInstance()->getStorage()->read())? Zend_Auth::getInstance()->getStorage()->read()->role_id:'';    	     	
    	$this->userId=Zend_Auth::getInstance()->getStorage()->read()->uid;
    	$this->roleId=Zend_Auth::getInstance()->getStorage()->read()->role_id;
    	$this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
    }
    
    
    public function indexAction(){
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
	     
		 foreach($companyListing as $cl){
			 $buildIds[] = $cl['build_id'];
		 }
		 
	    $select_build_id = $this->_getParam('bid','');		
		if(empty($select_build_id) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
		$select_build_id = $_COOKIE['build_cookie'];
		else
		$set_cookie = setcookie('build_cookie',$select_build_id,time() + (86400/24), "/");
		   
		 
		if($companyListing!=''){
				if($select_build_id!='')
				$select_build_id = $select_build_id;
				 else
				$select_build_id = $companyListing[0]['build_id'];
		 }

		 
		// echo 'hello'.$select_build_id;
		$order = $this->_getParam('order','');	
		$dir = $this->_getParam('dir','');
		$grid = $this->_getParam('grid','');		
		 
		 $this->view->order = $order;
		 $this->view->dir = $dir;
		 $this->view->grid= $grid;
		 $blModel = new Model_BillLabor();
		 if($order!='' && $dir!='' && $grid=='labor'){
			 $sorting_order = $order.' '.$dir;
		 }else
		 $sorting_order = array('set_default DESC','assign_to DESC');
		 //$order ='';
		 $blList = $blModel->getBillLaborByBId($select_build_id, $sorting_order);
		 $rate_order ='';
		 if($order!='' && $dir!='' && $grid=='rate'){
			 $rate_order = $order.' '.$dir;
		 }else
		 $rate_order = array('set_default DESC','multiplier ASC');
		 $brModel = new Model_BillRate();
		 $brList = $brModel->getBillRateByBId($select_build_id,$rate_order);
		 
		 $this->view->blList = $blList;
		 $this->view->brList = $brList;
		 
		 $this->view->custID = $this->cust_id; 
		 $this->view->companyListing = $companyListing;
		 $this->view->select_build_id = $select_build_id;
		 
		 $this->view->roleId = $this->roleId;
		 $this->view->acessHelper = $this->accessHelper;        
         $this->view->bill_location = $this->bill_location;
		 $this->view->userId = $this->userId;
	}// close index
	
	public function addlaborAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		$this->view->bId= $data['bId'];
		
	}// close add labor
	
	public function savelaborchargeAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 //print_r($data);
			 if($data['description']=='' || $data['charge_hour']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';				 
			 }else{
				 $laborModel = new Model_BillLabor();
				 $laborDesc = $laborModel->checkLaborDesc($data['description'],$data['building']);			
				 
				 if(!$laborDesc){
					 
					 try{
						 if($data['set_default']=='1'){
							 if($data['assign_to']!='' && !empty($data['assign_to'])){
								 $update_default = $laborModel->updateBillLaborByBId(array('set_default'=>0),$data['building']);
							 }else{
								 $data['set_default']=0;
							 }
						 }
						 $submitLaborCharge = $laborModel->insertBillLabor($data);
						 $message['status'] = 'success';
				         $message['msg']='Labor charge save successfully.';
					 }catch(Exception $e){
					    $message['status'] = 'error';
				        $message['msg']='Error Occurred during the save labor charge';
					 }
				 }else{
				   $message['status'] = 'error';
				   $message['msg']='This description already in use.';
				 }
			 }
			 
			echo json_encode($message);
		 }
		 exit(0);
	}// close save labor charge
	
	public function editlaborAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		$this->view->blid= $data['blid'];
		$this->view->bId= $data['bId'];
		$laborModel = new Model_BillLabor();
		$laborData = $laborModel->getBillLabor($data['blid']);
		$this->view->laborData = $laborData;
		
	}// close edit labor
	
	public function updatelaborchargeAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 //print_r($data);
			 if($data['description']=='' || $data['charge_hour']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';				 
			 }else{
				 $laborModel = new Model_BillLabor();
				 $laborDesc = $laborModel->checkLaborDesc($data['description'],$data['building'],$data['blid']);			
				 
				 if(!$laborDesc){
					 
					 try{
						 if($data['set_default']=='1'){
							 if($data['assign_to']!='' && !empty($data['assign_to'])){
								 $update_default = $laborModel->updateBillLaborByBId(array('set_default'=>0),$data['building']);
							 }else{
								 $data['set_default']=0;
							 }
						 }
						 $submitLaborCharge = $laborModel->updateBillLabor($data,$data['blid']);
						 $message['status'] = 'success';
				         $message['msg']='Labor charge save successfully.';
					 }catch(Exception $e){
					    $message['status'] = 'error';
				        $message['msg']='Error Occurred during the save labor charge';
					 }
				 }else{
				   $message['status'] = 'error';
				   $message['msg']='This description already in use.';
				 }
			 }
			 
			echo json_encode($message);
		 }
		 exit(0);
	}// close save labor charge
	
	public function deletelchargeAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 if($data['blid']!=''){
				 try{
				     $laborModel = new Model_BillLabor();
				     $deleteLabor = $laborModel->deleteBillLabor($data['blid']);
				     echo 'true';				     
				 }catch(Exception $e){
					 echo 'false';
				 }
				 
			 }
		 }
		exit(0); 
	}// close delete labor charge
	
	public function labortemplateAction(){
		$this->_helper->layout()->disableLayout();
		$cust_id = $this->cust_id;
		$data = $this->getRequest()->getPost();
		$this->view->bId= $data['bId'];
		$laborModel = new Model_BillLabor();
		$lcList = $laborModel->getLaborChargeByCompany($this->cust_id,$data['bId']);
		$this->view->lcList = $lcList;
		
	}// close labor template
	
	public function loadlabortemplateAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		$this->view->blid= $data['blid'];
		$laborData = '';
		//$this->view->bId= $data['bId'];
		if($data['blid']!=''){
			$laborModel = new Model_BillLabor();
			$laborData = $laborModel->getBillLabor($data['blid']);
		}else{
			$laborData =false;
		}
		$this->view->laborData = $laborData;
	}// close load labor template
	
	public function addrateAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		$this->view->bId= $data['bId'];
		
	}// close add labor
    
    public function saveratechargeAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 //print_r($data);
			 if($data['rate_name'] == '' || $data['description']=='' || $data['multiplier']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';			 
			 }else{
				 $rateModel = new Model_BillRate();
				 $rateName = $rateModel->checkRateName($data['rate_name'],$data['building']);			
				 
				 if(!$rateName){
					 
					 try{
						 if($data['set_default']=='1'){
							 
							$update_default = $rateModel->updateBillRateByBId(array('set_default'=>0),$data['building']);
							
						 }
						 $submitRateCharge = $rateModel->insertBillRate($data);
						 $message['status'] = 'success';
				         $message['msg']='Rate charge save successfully.';
					 }catch(Exception $e){
					    $message['status'] = 'error';
				        $message['msg']='Error Occurred during the save labor charge';
					 }
				 }else{
				   $message['status'] = 'error';
				   $message['msg']='This Rate Name already in use.';
				 }
			 }
			 
			echo json_encode($message);
		 }
		 exit(0);
	}// close save rate charge
	
	
	public function deleteratechargeAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 if($data['brid']!=''){
				 try{
				     $rateModel = new Model_BillRate();
				     $deleteRate = $rateModel->deleteBillRate($data['brid']);
				     echo 'true';				     
				 }catch(Exception $e){
					 echo 'false';
				 }
				 
			 }
		 }
		exit(0); 
	}// close delete rate charge
	
	public function editrateAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		$this->view->brid= $data['brid'];
		$this->view->bId= $data['bId'];
		$rateModel = new Model_BillRate();
		$rateData = $rateModel->getBillRate($data['brid']);
		$this->view->rateData = $rateData;
		
	}// close edit rate
	
	 public function updateratechargeAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 //print_r($data);
			 if($data['rate_name'] == '' || $data['description']=='' || $data['multiplier']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';			 
			 }else{
				 $rateModel = new Model_BillRate();
				 $rateName = $rateModel->checkRateName($data['rate_name'],$data['building'],$data['brid']);			
				 
				 if(!$rateName){
					 
					 try{
						 if($data['set_default']=='1'){							 
							$update_default = $rateModel->updateBillRateByBId(array('set_default'=>0),$data['building']);							
						 }
						 $updateRateCharge = $rateModel->updateBillRate($data,$data['brid']);
						 $message['status'] = 'success';
				         $message['msg']='Rate charge save successfully.';
					 }catch(Exception $e){
					    $message['status'] = 'error';
				        $message['msg']='Error Occurred during the save labor charge';
					 }
				 }else{
				   $message['status'] = 'error';
				   $message['msg']='This Rate Name already in use.';
				 }
			 }
			 
			echo json_encode($message);
		 }
		 exit(0);
	}// close save rate charge
	
	public function ratetemplateAction(){
		$this->_helper->layout()->disableLayout();
		$cust_id = $this->cust_id;
		$data = $this->getRequest()->getPost();
		$this->view->bId= $data['bId'];
		$rateModel = new Model_BillRate();
		$rcList = $rateModel->getRateChargeByCompany($this->cust_id,$data['bId']);
		$this->view->rcList = $rcList;
		
	}// close rate template
	
	public function loadratetemplateAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		$this->view->brid= $data['brid'];
		$rateData = '';
		//$this->view->bId= $data['bId'];
		if($data['brid']!=''){
			$rateModel = new Model_BillRate();
			$rateData = $rateModel->getBillRate($data['brid']);
		}else{
			$rateData =false;
		}
		$this->view->rateData = $rateData;
	}// close load labor template
}    
