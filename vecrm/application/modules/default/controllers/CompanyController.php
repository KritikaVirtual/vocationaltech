<?php
class CompanyController extends Ve_Controller_Base
{
	private $userId='';
	private $roleId='';
	private $accountMapper='';
	private $buildingMapper='';
	private $cust_id ='';
	private $userMapper='';
	public function init() 
    {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');
	   $this->accountMapper=new  Model_Account();
	   $this->buildingMapper=new  Model_Building();
	   $this->userMapper = new Model_User();
	   $this->accessHelper = $this->_helper->access; 
    }
	// Call befor any action and check is user login or not
    public function preDispatch()
    {    	
		if (!Zend_Auth::getInstance()->hasIdentity()) $this->_redirect('/index');
		 $level=(Zend_Auth::getInstance()->getStorage()->read())?Zend_Auth::getInstance()->getStorage()->read()->role_id:'';    	     	
    	$this->userId=Zend_Auth::getInstance()->getStorage()->read()->uid;
    	$this->roleId=Zend_Auth::getInstance()->getStorage()->read()->role_id;
		$this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
    }	
	public function indexAction()
    {	  
		$companyListing ='';
		if($this->roleId=='9'){
			 $companyListing = $this->buildingMapper->getCompanyBuilding($this->cust_id);
			}else{
				$user_build_mod = new Model_UserBuildingModule();
				$buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
				if($buildinglists){
					$build_id_array = array();
					foreach($buildinglists as $buildlist)
						  $build_id_array[] = $buildlist['building_id'];
						$companyListing = $this->buildingMapper->getBuildingList($build_id_array);
						//print_r($build_data);
					}
		   }
		 $this->view->companyListing = $companyListing;
		 $this->view->custID = $this->cust_id;
		 $this->view->roleId     = $this->roleId;

		 $this->view->acessHelper = $this->accessHelper;

		 //to set the access of Building Information
		 $this->view->building_location_id = 6;
		 
    }
	public function updateinfoAction(){
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{	// Address Field update
			if($this->getRequest()->getPost('address')){
				$build_id = $this->getRequest()->getPost('buildingID');
				$address = $this->getRequest()->getPost('address');
				$data= array(
					'address' => $address,
				);
			}
			// Address2 Field update
			elseif($this->getRequest()->getPost('address2')){
				$build_id = $this->getRequest()->getPost('buildingID');
				$address2 = $this->getRequest()->getPost('address2');
				$data= array(
					'address2' => $address2,
				);
			}
			// Suite Field update
			elseif($this->getRequest()->getPost('suite')){
				$build_id = $this->getRequest()->getPost('buildingID');
				$suite = $this->getRequest()->getPost('suite');
				$data= array(
					'suite' => $suite,
				);
			}
			// City Field update
			elseif($this->getRequest()->getPost('city')){
				$build_id = $this->getRequest()->getPost('buildingID');
				$city = $this->getRequest()->getPost('city');
				$data= array(
					'city' => $city,
				);
			}
			// State Field Updated
			elseif($this->getRequest()->getPost('state')){
				$build_id = $this->getRequest()->getPost('buildingID');
				$state = $this->getRequest()->getPost('state');
				$data= array(
					'state' => $state,
				);
			}
			// Postal Code field update
			elseif($this->getRequest()->getPost('postalCode')){
				$build_id = $this->getRequest()->getPost('buildingID');
				$postalCode = $this->getRequest()->getPost('postalCode');
				$data= array(
					'postalCode' => $postalCode,
				);
			}
			// Phone Number field Updated.
			elseif($this->getRequest()->getPost('phoneNumber')){
				$build_id = $this->getRequest()->getPost('buildingID');
				$phoneNumber = $this->getRequest()->getPost('phoneNumber');
				$data= array(
					'phoneNumber' => $phoneNumber,
				);
			}
			// Fax Number Field Updated
			elseif($this->getRequest()->getPost('faxNumber')){
				$build_id = $this->getRequest()->getPost('buildingID');
				$faxNumber = $this->getRequest()->getPost('faxNumber');
				$data= array(
					'faxNumber' => $faxNumber,
				);
			}
			// Attention Field Updated
			elseif($this->getRequest()->getPost('attention')){
				$build_id = $this->getRequest()->getPost('buildingID');
				$attention = $this->getRequest()->getPost('attention');
				$data= array(
					'attention' => $attention,
				);
			}
			
			$res = $this->buildingMapper->updateBuilding($data,$build_id);
			if($res)
			{
				$data['status']  = 'success';
				$data['message'] = 'Updated successfully';
				echo json_encode($data);exit;
			}else{
				$data['status']  = 'error';
				$data['message'] = 'exception occured';
				echo json_encode($data);exit;
			}
		}
	}
	
	public function updatebuildingAction(){
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$build_id = $this->getRequest()->getPost('pk');
			$key = $this->getRequest()->getPost('name');
			$value = $this->getRequest()->getPost('value');
			$data= array(
					$key => $value,
				);
		   if($key!='' && !empty($key)){		
			   $res = $this->buildingMapper->updateBuilding($data,$build_id);
		    }
		}
		exit;
	}
	public function usersAction(){		
		$companyListing ='';
		if($this->roleId=='9'){
			 $companyListing = $this->buildingMapper->getCompanyBuilding($this->cust_id);
			}else{
			$user_build_mod = new Model_UserBuildingModule();
			$buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
			if($buildinglists){
				$build_id_array = array();
				foreach($buildinglists as $buildlist)
				  $build_id_array[] = $buildlist['building_id'];
				$companyListing = $this->buildingMapper->getBuildingList($build_id_array);			
			}
	     }
		$build_ID = $this->_getParam('bid','');
		
		if(empty($build_ID))
		$build_ID = $_COOKIE['build_cookie'];
		else
		$set_cookie = setcookie('build_cookie',$build_ID,time() + (86400/24), "/");
		
		if($companyListing!=''){
				if($build_ID!='')
				$select_build_id = $build_ID;
				 else
				$select_build_id = $companyListing[0]['build_id'];
		 }
		 $this->view->companyListing = $companyListing;
		 $this->view->custID = $this->cust_id;
		 $this->view->roleId     = $this->roleId;
         $this->view->select_build_id = $select_build_id;
		 $this->view->acessHelper = $this->accessHelper;
		 $this->view->userId = $this->userId;
		 //to set the access of Building Information
		 $this->view->user_info_id = 7;
		
	}
	
	public function findcompanynameAction(){
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$companyName = $this->getRequest()->getPost('companyName');

			$company_result = $this->accountMapper->findCompanyName($companyName);
			if($company_result && count($company_result) > 0){
				echo "found";
			}
			
		}
	}
	public function findaccountnumberAction(){
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$accountNumber = $this->getRequest()->getPost('accountNumber');

			$company_result = $this->accountMapper->findAccountNumber($accountNumber);
			if($company_result && count($company_result) > 0){
				echo "found";
			}
			
		}
	}
	
	public function updateuserAction(){
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$user_id = $this->getRequest()->getPost('pk');
			$key = $this->getRequest()->getPost('name');
			$value = $this->getRequest()->getPost('value');
			$data= array(
					$key => $value,
				);
		   if($key!='' && !empty($key)){		
			   $res = $this->userMapper->updateUser($data,$user_id);
		    }
		}
		exit;
	}
	
	public function deletebuildinguserAction(){
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$data = $this->getRequest()->getPost();			
			$userId= $data['userId'];
			$buildId= $data['buildId'];
			$groupIds = $data['group_Ids'];
			if(!empty($userId) && !empty($buildId)){
				$userBuilding = new Model_UserBuildingModule();
				$userGroupModel = new Model_EmailGroupUsers();
				$categoryModel = new Model_Category();
				try{
					$deleteBuildingUser = $userBuilding->deleteBuildingUser($userId,$buildId);
					$groupid_array = explode(',',$groupIds);
					if(count($groupid_array)>0){
						foreach($groupid_array as $gpId){							
							$deletGroupUse = $userGroupModel->deleteEmailUser($userId,$gpId);
						}
					}
					
					/************delete user from category*************/
					$categoryList = $categoryModel->getCategoryByUser($userId,$buildId);
					//var_dump($categoryList);
					if($categoryList){
						foreach($categoryList as $cat){
							$updateCat = array();
							$acuserlist = explode(",",$cat['account_user']);
							$ackey = array_search($userId,$acuserlist);
							unset($acuserlist[$ackey]);
							$acuser = implode(",",$acuserlist);
							$updateCat['account_user']=$acuser;
							
							$updateCat = $categoryModel->updateCategory($updateCat,$cat['cat_id']);
							
						}
					}
					$msg='true';
				}catch(Exception $e){
					$msg='error';
				}
			}else
			$msg='error';
			echo json_encode(array('msg'=>$msg));
		}
		exit;
	}
}
