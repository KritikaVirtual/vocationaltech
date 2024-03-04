<?php
class AccountController extends Ve_Controller_Base
{
	private $userId='';
	private $roleId='';
	public function init() 
    {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout'); 
	   //$this->_helper->layout()->setLayout('homelayout'); 
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
		$page=$this->_getParam('page',1);
		$msgId = $this->_getParam('mId',0);
		$order = $this->_getParam('order','cust_id');
		$dir = $this->_getParam('dir','desc');
		$pageObj=new Ve_Paginator();
		$companyList = $accountMapper->getCompanyList($order,$dir);		
		$paginator=$pageObj->fetchPageDataResult($companyList,$page,10);	    
		//echo '<pre>'; var_dump($paginator);die;
	   // $this->view->total=$pageObj->getTotalCount(); 	 
		 $this->view->companyListing =$paginator;
		 $this->view->page = $page;
		 $this->view->order = $order;
		 $this->view->dir = $dir;		 
		 $msg='';
		 if($msgId==1){
			$msg ='New Company has been created successfully.'; 
		 }
		 
		 if($msgId==2){
			$msg ='Company has been updated successfully.'; 
		 }
		 
		 if($msgId==3){
			$msg ='Company has been deleted successfully.'; 
		 }
		 
		 if($msgId==4){
			 $msg ='Some error occur to creating new company.';
		 }

		 if($msgId==5){
			 $msg ='New company logo is updated.';
		 }

		 if($msgId==6){
			 $msg ='Unable to update the logo due to some technical problem. Please try later';
		 }

		 $am = new Zend_Session_Namespace('account_message');
		 if(!isset($am->msg) && $msgId!=0){
			$am->msg = $msg;
			$this->_redirect('/account/index/page/'.$page);
		  } 
		  
		  
	 // $this->view->companyListing = $accountMapper->getcompany();
		
    }
	//Account Setting
	public function accountsettingAction()
    {
		 $accountMapper=new  Model_Account();
		 //$this->view->companyListing = $accountMapper->getcompany();
		 $passkey = uniqid();
		 $userModel = new Model_User();
         $userModel->setPasskey($passkey, $this->userId);
         $this->view->passkey = $passkey;
    }
	//Search/Filter company
	public function filtercompanyAction()
    {
		$data = array();
		$this->_helper->layout()->disableLayout();
		
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $accountMapper=new  Model_Account();
			 $companyListing = $accountMapper->getcompany();
			 $page=$this->_getParam('page',1);
			 $pageObj=new Ve_Paginator();
			 $paginator=$pageObj->fetchPageDataResult($accountMapper->getcompany(),$page,5);				 	 
			 $companyListing =$paginator;
			 $content='';
			 $content.='<section class="w-48 fr ch-home-form" style="z-index:9999">
<section class="ch-form-header">
 <div class="rowGrid">
               
                <div class="col02 colAnum">Account Number</div>
                <div class="col02 colcname">Company Name</div>
                <div class="col02 coldtactive">Date Activated</div>
                <div class="col02 colstat">Status</div>
                <div class="col02 coladdlastcol lastcol"><a href="javascript:void(0);" id="openaddcompanydiv">Add Company</a></div>
</div>			
             
</section>
<div class="gridContainer">
<div class="tableGrid">';
               if(count($companyListing)>0){$i=1;foreach($companyListing as $companydata){              
              $content.='<div class="rowGrid showCompany" id="compID-'.$companydata['cust_id'].'">
                <div class="col02 colAnum">'.$companydata['corp_account_number'].'</div>
                <div class="col02 colcname">'.$companydata['companyName'].'</div>
                <div class="col02 coldtactive">'.date('M d, Y',strtotime($companydata['activationDate'])).'</div>
                <div class="col02 colstat">';
				if($companydata['status']==1){$content.='Yes';}else{$content.='No';}
				$content.='</div>
                <div class="col02 coladdlastcol lastcol"><a href="javascript:void(0);" onclick="showEditCompany('.$companydata['cust_id'].')" data-id="'.$companydata['cust_id'].'">Edit</a> | <a href="javascript:void(0);" onclick="deleteCompany('.$companydata['cust_id'].')" data-id="'.$companydata['cust_id'].'">Delete</a></div>
              </div>';
			  $content.='<div class="rowGrid editComapny" id="editcompID-'.$companydata['cust_id'].'" style="display:none">';
                $content.='<div class="col02 colAnum"><input type="text" id="editAccountNum-'.$companydata['cust_id'].'" value="'.$companydata['corp_account_number'].'" maxlength="10" onkeypress="return isNumberKey(event)"></div>';
				$content.='<div class="col02 colcname"><input type="text" id="editCName-'.$companydata['cust_id'].'" value="'.$companydata['companyName'].'"></div>
                <div class="col02 coldtactive"><input type="text" id="editActivateDate-'.$companydata['cust_id'].'" value="'.date('M d, Y',strtotime($companydata['activationDate'])).'" class="showcal"></div>
                <div class="col02 colstat"><select id="editActiveInactive-'.$companydata['cust_id'].'"><option value="1"';
				if($companydata['status']==1){$content.="selected=selected";}
				$content.='>Yes</option><option value="0"';
				if($companydata['status']==0){$content.="selected=selected";}
				$content.='>No</option></select></div>
                <div class="col02 coladdlastcol  lastcol"><a href="javascript:void(0);" onclick="editCompanyy('.$companydata['cust_id'].')" class="editcompanyy" data-id="'.$companydata['cust_id'].'"><img src="'.BASEURL.'public/images/edit.png" /></a> <a href="javascript:void(0);" class="hideeditcmpny" onclick="hideEditCompany('.$companydata['cust_id'].')" data-id="'.$companydata['cust_id'].'"><img src="'.BASEURL.'public/images/delete.png" /></a></div>
              </div>';
			  
			  $i++;}}else{
			  $content.='Empty records';
			  }
               
$content.='</div></div></section>';
$content.= Zend_View_Helper_PaginationControl::paginationControl($companyListing, 'Sliding', 'pagination.phtml',array( 'module' => 'default','controller' =>'account','action' =>'index' ));
				 $data['status']  = 'success';
				 $data['message'] = $content;
				 echo json_encode($data);exit; 
			 
		 }
	}
	//Delete Company
	public function deletecompanyAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		 if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $accountMapper=new  Model_Account();
			 $companyID = $this->_request->getPost('companyID');
			 $buildMapper = new Model_Building();
			 $buildData = $buildMapper->getCompanyBuilding($companyID);
			 if($buildData){
				 $data['status']  = 'error';
				 $data['message'] = 'Due to active buildings, System is unable to delete this comapany.';
				 echo json_encode($data);exit;
			 }
			 $resultDel = $accountMapper->deletecompany($companyID);
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
	//Edit Company
	public function editcompanyAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		 if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			$accountMapper = new  Model_Account();
			$cID = $this->_request->getPost('cid');
			$resss = $accountMapper->validatecompanyNameNotID(trim($this->_request->getPost('companyName')),$cID);
			if($resss)
			{
				$data['status']  = 'error';
				$data['message'] = 'Company name already exists';
				echo json_encode($data);exit;
			}
			$resssaccount = $accountMapper->validateaccountNumberNotID(trim($this->_request->getPost('accountNumber')),$cID); 
			if($resssaccount)
			{
				$data['status']  = 'error';
				$data['message'] = 'Account name already exists';
				echo json_encode($data);exit;
			}
			
			$datacontent =array();
			$datacontent['companyName']=trim($this->_request->getPost('companyName'));
			$datacontent['corp_account_number']=trim($this->_request->getPost('accountNumber'));
			$datacontent['activationDate']=date('Y-m-d',strtotime($this->_request->getPost('activationDate')));
			$datacontent['createdDate']=date('Y-m-d');
			$datacontent['status']=$this->_request->getPost('isactive');
			//echo '<pre>';print_r($datacontent);exit;
			$ress =$accountMapper->editcompany($datacontent,$cID);
			$data['status']  = 'success';
            $data['message'] = 'updated successfully';
        }else {
            $data['status']  = 'error';
            $data['message'] = 'you are doing bad request';
        }
		echo json_encode($data);
	}
	//Add Company
	public function addcompanyAction()
    {
		
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		 if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			$accountMapper = new  Model_Account();
			$resss = $accountMapper->validatecompanyName(trim($this->_request->getPost('companyName')));
			if($resss)
			{
				$data['status']  = 'error';
				$data['message'] = 'Company name already exists';
				echo json_encode($data);exit;
			}
			$resssaccount = $accountMapper->validateaccountNumber(trim($this->_request->getPost('accountNumber'))); 
			if($resssaccount)
			{
				$data['status']  = 'error';
				$data['message'] = 'Account number already exists';
				echo json_encode($data);exit;
			}
			
			$datacontent =array();
			$datacontent['companyName']=trim($this->_request->getPost('companyName'));
			$datacontent['corp_account_number']=trim($this->_request->getPost('accountNumber'));
			$datacontent['activationDate']= date('Y-m-d',strtotime($this->_request->getPost('activationDate')));
			$datacontent['createdDate']=date('Y-m-d');
			$datacontent['status']=$this->_request->getPost('isactive');			
			$ress = $accountMapper->addcompany($datacontent);
			if($ress!=''){
				$userMapper=new  Model_User();
				$admin_email = 'admin'.$ress.'@testadmin.com';
				$userDataArray=array();				
				$userDataArray['cust_id']= $ress;
				$userDataArray['firstName']= 'Company';
				$userDataArray['lastName']= 'User';
				$userDataArray['email']= $admin_email;
				$userDataArray['userName']= $admin_email ;
				$userDataArray['password']= md5('admin@123');
				$userDataArray['role_id']= 9;// company admin role id
				$userInsert = $userMapper->insertUser($userDataArray);				
			}
			$data['status']  = 'success';
            $data['message'] = 'saved successfully';
        }else {
            $data['status']  = 'error';
            $data['message'] = 'you are doing bad request';
        }
		echo json_encode($data);
		
	}	 
	public function getcompanynameeAction() 
	{
		$data = array();
		$query = $this->_request->getParams();
		
		$accountMapper = new Model_Account();
		$companyDetail = $accountMapper->getCompanyByName($query['q']);
		//echo '<pre>';print_r($companyDetail);
		
		if(!empty($companyDetail)) {
			foreach($companyDetail as $key => $user) {
				$companyDetail[$key]['label'] = '<span class="cnleft">'.$user['companyName'].'</span>  <span class="anright"> '.$user['corp_account_number'].'</span>';
				$companyDetail[$key]['value'] = $user['companyName'];
			}
		$header = array();	
		$header['label'] = '<span class="cnleft"><strong>Company Name</strong></span> <span class="anright"><strong>Account Number</strong></span>';
		$header['value'] = 'Company Name';		
		array_unshift($companyDetail,$header);
		}
		echo $query["callback"]."(".json_encode($companyDetail).")";
		exit(0);
			
    }
    /***
     * Validate company name
     */ 
    public function getvalidatecompanyAction(){
		
	  $query = $this->_request->getParams();
	  $accountMapper = new Model_Account();  
	  
	  $companyExist = $accountMapper->validatecompanyName($query['cname']);
	  if($companyExist!=false){
		  echo 'true';
	  }else{
		  echo 'false';
	  }
	  exit;
    }
    
    /***
     * Validate Account Number
     */ 
     
    public function getvalidateaccountAction(){
		
	  $query = $this->_request->getParams();
	  $accountMapper = new Model_Account();  
	  
	  $accountExist = $accountMapper->validateaccountNumber($query['acNumber']);
	  if($accountExist!=false){
		  echo 'true';
	  }else{
		  echo 'false';
	  }
	  exit;
    }
    
    public function changepasswordAction(){
		//echo $this->userId;		
		$query = $this->_request->getParams();				
		$userMapper = new Model_User();	  
	    echo $passwordChange = $userMapper->updateresetpassword(md5($query['cpass']),$query['passkey']);
	    exit;
	}
	
	public function changeaccountAction(){
	  $adminNamespace = new Zend_Session_Namespace('Admin_User');	
		if($this->getRequest()->getMethod() == 'POST'){		
			$post = $this->_request->getPost();		
			$user_id = $post['company_account'];
			if($user_id==0 && $adminNamespace->role_id==1){
				$user_id = $adminNamespace->user_id;
			}
			$userModel = new Model_User();
			$userData = $userModel->getUserById($user_id);		
			$userDetails = $userData[0];			
			Zend_Auth::getInstance()->getStorage()->write($userDetails);
			$this->_redirect('dashboard');
		}
		
		$userMod = new Model_User();
		$companyUser = $userMod->getCompanyAdminUser();
		$this->view->user_role = $adminNamespace->role_id;	
		$this->view->user_id = $this->userId;
		$this->view->companyUser = $companyUser;
	}
	
	public function savecompanyAction(){
		
		$post = $this->_request->getPost();		
		$msgId= 0;
		if($this->getRequest()->getMethod() == 'POST') 
		 {
			$accountMapper = new  Model_Account();
			$resss = $accountMapper->validatecompanyName(trim($this->_request->getPost('companynamee')));
			$resssaccount = $accountMapper->validateaccountNumber(trim($this->_request->getPost('accountNumber')));
			if($resss != false || $resssaccount!= false)
			{
				$msgId=4;
			}else{
				try{
					/***** upload company logo image ****/
					$datacontent =array();
					if($_FILES['company_logo']['name']!=''){						
						$uploaddir = BASE_PATH.'public/images/clogo/';
                        $uploadfile_name = 'Clogo-original-'.time().'-'. basename($_FILES['company_logo']['name']);
                        $uploadResize_name = 'Clogo-resize-'.time().'-'. basename($_FILES['company_logo']['name']);
                        $uploadfile = $uploaddir.''.$uploadfile_name;
                        $uploadResize= $uploaddir.''.$uploadResize_name;
						if (!file_exists($uploaddir)) {
							mkdir($uploaddir, 0777, true);
						}
						move_uploaded_file($_FILES["company_logo"]["tmp_name"], $uploadfile);
						$simageObj=new Ve_SimpleImage();
						$simageObj->load($uploadfile); 
						$simageObj->resizeToHeight(105); 
						$simageObj->save($uploadResize);
						$datacontent['company_logo'] = $uploadResize_name;
					}					
					$datacontent['companyName']=trim($this->_request->getPost('companynamee'));
					$datacontent['corp_account_number']=trim($this->_request->getPost('accountNumber'));
					$datacontent['activationDate']= date('Y-m-d',strtotime($this->_request->getPost('activationDate')));
					$datacontent['createdDate']=date('Y-m-d');
					$datacontent['status']=$this->_request->getPost('isactive');			
					$ress = $accountMapper->addcompany($datacontent);
					if($ress!=''){
						$userMapper=new  Model_User();
						$admin_email = 'admin'.$ress.'@testadmin.com';
						$userDataArray=array();				
						$userDataArray['cust_id']= $ress;
						$userDataArray['firstName']= 'Company';
						$userDataArray['lastName']= 'User';
						$userDataArray['email']= $admin_email;
						$userDataArray['userName']= $admin_email ;
						$userDataArray['password']= md5('admin@123');
						$userDataArray['role_id']= 9;// company admin role id
						$userInsert = $userMapper->insertUser($userDataArray);				
					}
					$msgId=1;
				}catch(Exception $e){
					$msgId=4;
					//echo $e->getMessage();
					//exit;
				}				
			}
        }else {
            $msgId=4;
        }		
		$this->_redirect('account/index/mId/'.$msgId);
	}
	

	public function updatelogoAction(){
		$post = $this->_request->getPost();		

		if($this->getRequest()->getMethod() == 'POST') 
		 {  	
		 	$accountMapper = new  Model_Account();
		 	try{
					/***** upload company logo image ****/
					$datacontent =array();

					if($_FILES['company_logo1']['name']!=''){		 				
						$uploaddir = BASE_PATH.'public/images/clogo/';
                        $uploadfile_name = 'Clogo-original-'.time().'-'. basename($_FILES['company_logo1']['name']);
                        $uploadResize_name = 'Clogo-resize-'.time().'-'. basename($_FILES['company_logo1']['name']);
                        $uploadfile = $uploaddir.''.$uploadfile_name;
                        $uploadResize= $uploaddir.''.$uploadResize_name;
						if (!file_exists($uploaddir)) {
							mkdir($uploaddir, 0777, true);
						}
						move_uploaded_file($_FILES["company_logo1"]["tmp_name"], $uploadfile);
						$simageObj=new Ve_SimpleImage();
						$simageObj->load($uploadfile); 
						$simageObj->resizeToHeight(105); 
						$simageObj->save($uploadResize);
						$company_logo = $uploadResize_name;
						$cust_id = $post['cust_id'];
					}			

					$ress = $accountMapper->updateLogo($cust_id,$company_logo);


					if($ress>=1){
						$msgId=5;
					}
					else{
						$msgId=6;
					}

					
				}catch(Exception $e){
					$msgId=6;
					
				}
		 }

		 $this->_redirect('account/index/mId/'.$msgId);
	}
}	     
?>
