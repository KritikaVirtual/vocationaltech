<?php
class BuildingController extends Zend_Controller_Action
{
	private $userId='';
	private $roleId='';
	public function init() 
    {
       parent::init();
       $this->_helper->layout()->setLayout('homelayout');  
    }
	// Call befor any action and check is user login or not
    public function preDispatch()
    {    	
		if (!Zend_Auth::getInstance()->hasIdentity()) $this->_redirect('/index');
		 $level=(Zend_Auth::getInstance()->getStorage()->read())?Zend_Auth::getInstance()->getStorage()->read()->role_id:'';    	 
    	/**if($level==md5('D')){
    		Zend_Auth::getInstance()->clearIdentity();
			$this->_redirect('sales');
    	}else if($level==md5('A')) {
			$this->_redirect('site');
		}*/
    	$this->userId=Zend_Auth::getInstance()->getStorage()->read()->uid;
    	$this->roleId=Zend_Auth::getInstance()->getStorage()->read()->role_id;
    }	
	public function indexAction()
    {
		 $accountMapper=new  Model_Account();
		 $this->view->companyListing = $accountMapper->getcompany();
    }
	public function showbuildinglistAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		
		
		 if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
		 $cust_id = (int)$this->getRequest()->getPost('companyID');
			$buildingMapper=new  Model_Building();
			//  $this->_helper->viewRenderer('showbuildinglist');
			//$this->view->buildingListing = $buildingMapper->getBuilding();
			$buildList = $buildingMapper->getbuilding($cust_id);
			$dataString = '';
			$dataString .= '<div class="building-list">
							<section class="w-48 fr ch-home-form" id="buildList">
							<section class="ch-form-header" >
							 <div class="rowGrid">
											<div class="col01">Building Name</div>
											<div class="col02">Address</div>
											<div class="col02">Phone</div>
											<div class="col02">City</div>
											<div class="col02">State</div>
											<div class="col02">Active</div>
											<div class="col02"></div>
											
							</div>			
										 
							</section>
							<div id="showForm-'.$cust_id.'" class="showForm"></div>
							<div class="gridContainer" id="buildListData">
							<div class="tableGrid">';
			if(count($buildList)>0){
				$i=1;
				foreach($buildList as $bl){
					$st = ($bl["status"] == '1') ?  "Yes" : "No";
					$dataString .= '
						 <div class="rowGrid" id="build-'.$bl['build_id'].'">
								<div class="col01">'.$bl["buildingName"].'</div>
								<div class="col02">'.$bl["address"].'</div>
								<div class="col02">'.$bl["phoneNumber"].'</div>
								<div class="col02">'.$bl["city"].'</div>
								<div class="col02">'.$bl["state"].'</div>
								<div class="col02">'.$st.'</div>
								<div class="col02"><a href="javascript:void(0);" onClick="editBuilding('.$bl['build_id'].');">Edit</a>  <a href="javascript:void(0);" onClick="deleteBuilding('.$bl['build_id'].');">Delete</a></div>
						</div>
						
				'; 
			
				$i++;}
			}else{
				$dataString .= '<div class="rowGrid">
								No Record Found!
						</div>';
			}
			$dataString .= '<div class="newBuild"> <a href="javascript:void(0);" onClick="return showBuildingForm(this);" class="" id="'.$cust_id.'" >Add New Record</a></div>';
			print($dataString);	exit();
		}
	}	
	public function showbuildingformAction(){
		$this->_helper->layout()->disableLayout();
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$cust_id = (int)$this->getRequest()->getPost('companyID');
			 $accountMap=new  Model_Account();
			 $acc = $accountMap->getcompany($cust_id);
	
			 $corp_account_number = $acc[0]['corp_account_number'];
			$formString = '<div class="addbuild"><div class="message"></div><div class=""><h3>Add new Building Account</h3></div>'; 
			$formString .= '<form method="post" id="addNewBuilding" ><input type="hidden" id="companyID" name="companyID" value="'.$cust_id.'" >
				<label>(Auto Fill) Account Number :</label> <input type="text" name="accountNumber" class="" id="accountNumber" value="'.$corp_account_number.'" disabled="disabled" >
				<label>Cost Center :</label> 	<input type="text" name="costCenter" class="" id="costCenter" required ><span class="costCenterErr"></span>
				<label>Building Name :</label> 	<input type="text" name="buildingName" class="" id="buildingName" required ><span class="buildingNameErr"></span>
				<label>Address :</label>		<input type="text" name="address" class="" id="address"  required><span class="addressErr"></span>
				<label>Address2 :</label> 		<input type="text" name="address2" class="" id="address2" ><span class="address2Err"></span>
				<label>City :</label>			<input type="text" name="city" class="" id="city" required ><span class="cityErr"></span>
				<label>State :</label> 			<input type="text" name="state" class="" id="state" required ><span class="stateErr"></span>
				<label>Postal Code :</label> 	<input type="text" name="postalCode" class="" id="postalCode" required ><span class="postalCodeErr"></span>
				<label>Phone Number :</label> 	<input type="text" name="phoneNumber" class="" id="phoneNumber" required ><span class="phoneNumberErr"></span>
				<label>Fax Number :</label> 	<input type="text" name="faxNumber" class="" id="faxNumber" required ><span class="faxNumberErr"></span>
				<label>Active? :</label> <select name="status" class="status" id="status" required>
								<option value="1">Yes</option>
								<option value="0">No</option>
								</select>
				<div class="confirmBtn"><input type="submit" onClick="return addNewBuilding();" class="confirm" name="confirm" value="Confirm">
					<input type="reset" class="cancel" name="cancel" value="Cancel"></div></form>';
			$formString .= '</div>';		
			echo $formString;
			exit();
		}
	}
	public function addbuildingAction(){
		$this->_helper->layout()->disableLayout();
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$today = date("Y-m-d H:i:s");
			$accountNumber = $this->getRequest()->getPost('accountNumber');
			$datacontent =array();
			$datacontent['cust_id'] 		= $this->getRequest()->getPost('companyID');
			$datacontent['user_id'] 		= $this->userId;
			$datacontent['buildingName'] 	= $this->getRequest()->getPost('buildingName');
			$datacontent['address'] 		= $this->getRequest()->getPost('address');
			$datacontent['address2']		= $this->getRequest()->getPost('address2');
			$datacontent['city'] 			= $this->getRequest()->getPost('city');
			$datacontent['state'] 			= $this->getRequest()->getPost('state');
			$datacontent['postalCode'] 		= $this->getRequest()->getPost('postalCode');
			$datacontent['phoneNumber']	 	= $this->getRequest()->getPost('phoneNumber');
			$datacontent['faxNumber'] 		= $this->getRequest()->getPost('faxNumber');
			$datacontent['uniqueCostCenter']= $this->getRequest()->getPost('costCenter');
			$datacontent['dateCreated'] 	= $today;
			$datacontent['status'] 			= $this->getRequest()->getPost('status');
			
			$buildingMapper=new  Model_Building();
			$ress =$buildingMapper->addBuilding($datacontent);
		//	$data['status']  = 'success';
            $data['message'] = 'saved successfully';
        }else {
        //    $data['status']  = 'error';
            $data['message'] = 'you are doing bad request';
        }
		echo $data['message'];
			exit();
		
	}
	public function deletebuildingAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		 if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $buildingMapper=new  Model_Building();
			 $buildID = $this->_request->getPost('buildID');
			 $resultDel = $buildingMapper->deleteBuilding($buildID);
			 if($resultDel)
			 {
				 $data['status']  = 'success';
				 $data['message'] = 'deleted successfully';
				 echo json_encode($data);exit;
			 }else{
				 $data['status']  = 'error';
				 $data['message'] = 'exception occured';
				 echo json_encode($data);exit;
			 }
		 }
	}
	///////////////////////by sohan
	public function registrationAction(){
		$moduleMapper=new  Model_Module();
		$roleMapper=new  Model_Role();
		$accountMapper=new  Model_Account();
		$this->view->companyList = $accountMapper->getcompany();
		$this->view->moduleList = $moduleMapper->getModule();
		$this->view->roleList = $roleMapper->getRole();
		
	}
	public function registernewbuildingAction(){
	
		$moduleMapper=new  Model_Module();
		$roleMapper=new  Model_Role();
		$this->view->moduleList = $moduleMapper->getModule();
		$this->view->roleList = $roleMapper->getRole();
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			  $buildingMapper=new  Model_Building();
			  $companyID = $this->_request->getPost('cust_id');
			  $buildingPerinfo = $this->_request->getPost('buildingPerinfo');
			  parse_str($buildingPerinfo, $buildingPerData);
			   $billinginfo = $this->_request->getPost('billinginfo');
			  parse_str($billinginfo, $billingdata);
			  $adminuserinfo = $this->_request->getPost('adminuserinfo');
			  parse_str($adminuserinfo, $adminuserdata);
			  
			  //building info
			  $data['cust_id']= $companyID;
			  $data['user_id']= $this->userId;
			  $data['buildingName']= $buildingPerData['buildingName'];
			  $data['address']= $buildingPerData['address'];
			  $data['address2']= $buildingPerData['address2'];
			  $data['city']= $buildingPerData['city'];
			  $data['state']= $buildingPerData['state'];
			  $data['postalCode']= $buildingPerData['postalCode'];
			  $data['phoneNumber']= $buildingPerData['phoneNumber'];
			  $data['faxNumber']= $buildingPerData['faxNumber'];
			  $data['status']= $buildingPerData['status'];
			  //billing fields
			 
			  $data['billCompanyName']= $billingdata['billCName'];
			  $data['billAddress']= $billingdata['billaddress'];
			  $data['billAddress2']= $billingdata['billaddress2'];
			  $data['billSuite']= $billingdata['billsuite'];
			  $data['billCity']= $billingdata['billcity'];
			  $data['billState']= $billingdata['billstate'];
			  $data['billpostalCode']= $billingdata['billpostalCode'];
			  $data['billphoneNumber']= $billingdata['billphoneNumber'];
			  $data['billfaxNumber']= $billingdata['billfaxNumber'];
			  $data['attention']= $billingdata['attention'];
			  $buildingMapper->addBuilding($data);
			  //Create Admin user
			  $data['billCompanyName']= $billingdata['adminEmailAddress'];
			  $data['billAddress']= $billingdata['fname'];
			  $data['billAddress2']= $billingdata['lname'];
			  $data['billSuite']= $billingdata['username'];
			  $data['billCity']= $billingdata['city'];
			  $data['billState']= $billingdata['state'];
			  $data['billpostalCode']= $billingdata['postalCode'];
			  $data['billphoneNumber']= $billingdata['phoneNumber'];
			  $data['billfaxNumber']= $billingdata['accesslevel'];
			  $data['attention']= $billingdata['modulee'];
			  $buildingMapper->addBuilding($data);
			  
				//create admin user
				  $userDataArray=array();
				  $userDataArray['cust_id']= $adminuserdata['adminEmailAddress'];
				  $userDataArray['firstName']= $adminuserdata['fname'];
				  $userDataArray['lastName']= $adminuserdata['lname'];
				  $userDataArray['billSuite']= $adminuserdata['username'];
				  $userDataArray['userName']= $adminuserdata['uPass'];
				  $userDataArray['billState']= $adminuserdata['uConfPass'];
				  $userDataArray['billpostalCode']= $adminuserdata['phone'];
				  $userDataArray['role_id']= $adminuserdata['accesslevel'];
				  $userDataArray['billfaxNumber']= $adminuserdata['modulee'];
				  $buildingMapper->addBuilding($data);
				  
				//
		}
	}
	
	
	
}
?>