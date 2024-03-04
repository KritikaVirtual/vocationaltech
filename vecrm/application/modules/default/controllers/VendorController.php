<?php

/**
 * Description of Vendor
 *
 * @author Brijesh
 */
class VendorController extends Ve_Controller_Base {
   
   
    public function init()  {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');   
       $this->vendorModel = new Model_Vendor();
       $this->vm = new Zend_Session_Namespace('vendor_message');
       $this->accessHelper = $this->_helper->access;
       $this->vendor_location = 15;         
    }// close of init function
    
    
    public function preDispatch()
    {    	
      if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/index');
        }
        
        $level=(Zend_Auth::getInstance()->getStorage()->read())? Zend_Auth::getInstance()->getStorage()->read()->role_id:'';                
        $this->userId=Zend_Auth::getInstance()->getStorage()->read()->uid;
        $this->roleId=Zend_Auth::getInstance()->getStorage()->read()->role_id;
        $this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
        $this->email = Zend_Auth::getInstance()->getStorage()->read()->email;
    }
    
    public function indexAction(){
		$build_ID = $this->_getParam('bid','');
		if(empty($build_ID) && isset($_COOKIE['build_cookie']))
		$build_ID = $_COOKIE['build_cookie'];
		else
		$set_cookie = setcookie('build_cookie',$build_ID,time() + (86400/24), "/");
		
		$order=$this->_getParam('order','company_name');
		$dir=$this->_getParam('dir','ASC'); 
		
		$buildingMapper=new  Model_Building();
		$search_array= array();
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			if($data['search_by']=="services"){
				$search_array['search_by']= $data['search_by'];
				$search_array['search_value']= $data['service_value'];
			}else{
				$search_array['search_by']= $data['search_by'];
				$search_array['search_value']= $data['search_value'];
			}	
			
		}
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
          $vendorList = false;
          $select_bid = '';
           if($build_ID!=''){
			   $select_bid = $build_ID;			   
			}
			 else{ 
				 if($companyListing!=''){
					 $select_bid = $companyListing[0]['build_id'];
					 
				 }
			 }
		 if(!empty($select_bid)){
			 $vendorList = $this->vendorModel->getVendorByBid($select_bid,$order,$dir,$search_array);
		 }	 
		 $vid = $this->_getParam('vid','');	 
		 $this->view->vendorList = $vendorList;	 
         $this->view->select_build_id = $select_bid;
         $this->view->companyListing = $companyListing;
         $this->view->custID = $this->cust_id;
         $this->view->roleId     = $this->roleId;
         $this->view->vm = $this->vm;
         $this->view->vId = $vid;
         $this->view->search = $search_array;
         $this->view->order = $order;
		 $this->view->dir = $dir;
		 $this->view->acessHelper = $this->accessHelper;        
         $this->view->vendor_location = $this->vendor_location;
         
	}
	
	public function createvendorAction(){
		
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$submit_flag = true;
			if($data['company_name']=='' || $data['first_name']=='' || $data['services']==''){
				$submit_flag = false;
			}
			
			if($submit_flag){			
				$vData = array();
				$vData['company_name']=$data['company_name'];
				$vData['first_name']=$data['first_name'];
				$vData['last_name']=$data['last_name'];
				$vData['services']=$data['services'];
				$vData['contact_type']=$data['contact_type'];
				$vData['phone_number']=$data['phone_number'];
				$vData['cell_number']=$data['cell_number'];
				$vData['email']=$data['email'];
				$vData['account_number']=$data['account_number'];
				$vData['address1']=$data['address1'];
				$vData['address2']=$data['address2'];
				$vData['city']=$data['city'];
				$vData['state']=$data['state'];
				$vData['postal_code']=$data['postal_code'];
				$vData['emergency_contact']=$data['emergency_contact'];
				$vData['buildingId']=$data['buildingId'];
				try{
					$insertVendor = $this->vendorModel->insertVendor($vData);
					$this->vm->success = "Vendor Successfully Created!";
					$this->_redirect('/vendor/index/bid/'.$data['buildingId']);
				}catch(Exception $e){
					echo $e->getMessage();
					$this->vm->error = "Error Occurred During Creation of Vendor!";
				}
			}else{
				$build_ID = $data['buildingId'];
                $this->view->build_id = $build_ID;
                $this->vm->error = "Fill all the required field.";
			}
			
		}else{
			$build_ID = $this->_getParam('bid','');
            $this->view->build_id = $build_ID;
		}
	}// close create vendor method
	
	public function updatevendorAction(){
		

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $id = $this->getRequest()->getPost('pk');
            $key = $this->getRequest()->getPost('name');
            $value = $this->getRequest()->getPost('value');
            $data= array(
                    $key => $value,
                    'updated_at'=> date('Y-m-d H:i:s')
                );
           if($key!='' && !empty($key)){        
               $res = $this->vendorModel->updateVendor($data,$id);
            }
        }
        exit;
		
	} // close update vendor method
	
	public function updatevendornameAction(){
		
		
		$name = $this->_getParam('name');
		$value = $this->_getParam('value');
		$building = $this->_getParam('building');
		
		$vid = $this->_getParam('pk');		
		$vendorDetail = $this->vendorModel->checkVendorByName($value,$building,$vid);
       //var_dump($tenantDetail);
        if(!empty($vendorDetail)) 
            echo 'true';
        else{ 
              $data= array(
                    $name => $value,
                    'updated_at'=> date('Y-m-d H:i:s')
                );
			   if($name!='' && !empty($name)){        
				   $res = $this->vendorModel->updateVendor($data,$vid);
				}
			 echo 'false';	
            }
		
		exit(0);
	} // close update vendor name field
	
	public function loadvendorAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		if(isset($data['vid']) && $data['vid']!=''){
			$vdata = $this->vendorModel->getVendor($data['vid']);
			$this->view->vdata = $vdata[0];
		}else{
			echo 'Error in the vendor id.';
		}
		
		 $this->view->roleId     = $this->roleId;
		 $this->view->acessHelper = $this->accessHelper;        
         $this->view->vendor_location = $this->vendor_location;
	}// close show vendor method
	
	public function checkvendorAction(){
		$cname = $this->_getParam('cname');
		$bid = $this->_getParam('bid');
		if($cname !='' && $bid!= ''){
			$vendorDetail = $this->vendorModel->checkVendorByName($cname,$bid);       
			if(!empty($vendorDetail)) 
				echo 'true';
			else{
				echo 'false';
			} 
		}else
		 echo 'false';
		 exit(0);
	}
	
	public function addserviceAction(){
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$sarray = array();
			$sarray['service'] = $data['service'];
			try{
				$serviceModel = new Model_Services();
				$existService = $serviceModel->checkServiceByName($data['service']);
				if(!$existService){
				    $insert_service = $serviceModel->insertServices($sarray);
				    echo 'true';
			     }else
			     echo 'false';
			}catch(Exception $e){
				//echo $e->getMessage();
				echo 'error';
			}
		  }
		  exit(0);	
		
	}// close add service 
	
	public function addcontactAction(){
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$carray = array();
			$carray['contact'] = $data['contact'];
			try{
				$contactModel = new Model_ContactType();
				$existConatct = $contactModel->checkContactByName($data['contact']);
				if(!$existConatct){
				   $insert_contact = $contactModel->insertContactType($carray);
				   echo 'true';
			     }else{
					 echo 'false';
				 }
				
			}catch(Exception $e){
				//echo $e->getMessage();
				echo 'error';
			}
		  }
		  exit(0);	
	}// close add contact type
	
	public function showserviceAction(){
		$this->_helper->layout()->disableLayout();        
	}// close show service
	
	public function showcontactAction(){
		$this->_helper->layout()->disableLayout();        
	}// close show contact
	
	public function checkemailAction(){
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$email = $data['email'];
			try{
				$userModel = new Model_User();
				$userEmail = $userModel->checkUserEmail($email);
				if(!$userEmail){
					/*$vendorEmail = $this->vendorModel->checkVendorByEmail($email);
					if(!$vendorEmail){
						$vcModel = new Model_VendorContact();
						$vcEmail = $vcModel->checkVContactByEmail($email);
						if(!$vcEmail)
						   echo 'true';
						else
						   echo 'false';  
					}else*/
					 echo 'true'; 
				}else{
					echo 'false';
				}
				
			}catch(Exception $e){
				//echo $e->getMessage();
				echo 'error';
			}
		  }
		  exit(0);
	}// check email
	
	public function updatevendoremailAction(){
		$name = $this->_getParam('name');
		$value = $this->_getParam('value');		
		$email = $value;
		$vid = $this->_getParam('pk');
		$update_flage= false;
		if(!empty($email))		
		{
			$userModel = new Model_User();
			$userEmail = $userModel->checkUserEmail($email);
			if(!$userEmail){
				$vendorEmail = $this->vendorModel->checkVendorByEmail($email,$vid);
				if(!$vendorEmail){
					$vcModel = new Model_VendorContact();
					$vcEmail = $vcModel->checkVContactByEmail($email);
					if(!$vcEmail)
					  {
						  $update_flage= true;
					  }					
				}
			}
		}
        else{ 
			  $update_flage = true;			 
            }
            
       if($update_flage){
		   $data= array(
                    $name => $value,
                    'updated_at'=> date('Y-m-d H:i:s')
                );
              try{  
			   if($name!='' && !empty($name)){        
				   $res = $this->vendorModel->updateVendor($data,$vid);				   
				}
				echo 'false';
			}catch(Exception $e){
				echo 'error';
			}		 		 		
	   }else{
		   echo 'true';
	   }     
		
		exit(0);
	} // close update email
	
	
	public function addvendorcontactAction(){
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$vcData = array();
			$vcData['vid']= $data['vid'];
			$vcData['first_name']= $data['first_name'];
			$vcData['last_name']= $data['last_name'];
			$vcData['email']= $data['email'];
			$vcData['phone_number']= $data['phone_number'];
			$vcData['cell_number']= $data['cell_number'];
			$vcData['emergency_contact']= $data['emergency_contact'];
			try{
				$vcModel = new Model_VendorContact();
				$insertContact = $vcModel->insertVendorContact($vcData);
				$this->vm->success = "Alternate Vendor Contact Successfully Created!";
				$this->_redirect('/vendor/index/bid/'.$data['building'].'/vid/'.$data['vid']);
			}catch(Exception $e){
				$this->vm->error = "Error Occurred During Creation of Alternat Vendor Contact !";
			}
		}
		$vid = $this->_getParam('vid');
		if(!empty($vid)){
		   $vendorData = $this->vendorModel->getVendor($vid);
		   $this->view->vendorData = $vendorData;
	    }
	    $this->view->vid = $vid;
	}// close add vendor contact
	
	public function editvendorcontactAction(){
		$vcModel = new Model_VendorContact();
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$vcData = array();
			//$vcData['vid']= $data['vid'];
			$vcData['first_name']= $data['first_name'];
			$vcData['last_name']= $data['last_name'];
			$vcData['email']= $data['email'];
			$vcData['phone_number']= $data['phone_number'];
			$vcData['cell_number']= $data['cell_number'];
			$vcData['emergency_contact']= $data['emergency_contact'];
			try{
				
				$insertContact = $vcModel->updateVendorContact($vcData,$data['vcId']);
				$this->vm->success = "Alternate Vendor Contact Updated!";
				$this->_redirect('/vendor/index/bid/'.$data['building'].'/vid/'.$data['vid']);
			}catch(Exception $e){
				$this->vm->error = "Error Occurred During Updation of Alternat Vendor Contact !";
			}
		}
		$vcId = $this->_getParam('vcId');
		if(!empty($vcId)){
		   $vcData = $vcModel->getVendorContact($vcId);
		   if($vcData){
			   $vendorData = $this->vendorModel->getVendor($vcData[0]['vid']);
		       $this->view->vendorData = $vendorData;
		   }
		   $this->view->vcData = $vcData;
	    }
	    $this->view->vcId = $vcId;
	}// close edit vendor contact
	
	
	public function removevendorAction(){
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$vid = $data['vid'];
			if($vid!=''){				
				try{
					$vcModel = new Model_VendorContact();
					$vcList = $vcModel->getContactByVid($vid);
					if($vcList){
						foreach($vcList as $vc){
							$vcId = $vc['vcId'];
					       $vcdelete = $vcModel->deleteVendorContact($vcId);
					    }
				     }
					$vdelete = $this->vendorModel->deleteVendor($vid);
					echo 'true';
					$this->vm->success = "Vendor Successfully Deleted!";
				}catch(Exception $e){
					echo 'error';
				}
			}
		}
		exit(0);
	}
	
	public function removevcontactAction(){
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$vcId = $data['vcId'];
			if($vcId!=''){				
				try{
					$vcModel = new Model_VendorContact();
					$res = $vcModel->deleteVendorContact($vcId);
					$this->vm->success = "Alternate Vendor Contact Successfully Deleted!";
					echo 'true';
				}catch(Exception $e){
					echo 'error';
				}
			}
		}
		exit(0);
	}
	
}// close class
