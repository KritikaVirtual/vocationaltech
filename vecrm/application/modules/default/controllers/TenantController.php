<?php
/**
 * Description of TenantController
 *
 * @author Anuj
 */
class TenantController extends Ve_Controller_Base {
    
    public function init()  {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout'); 
       $this->buildingMapper=new  Model_Building();
       $this->accessHelper = $this->_helper->access;
       $this->tenant_location = 12; 
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
		$roleMapper = new Model_Role();
        $roleDetail = $roleMapper->getRole();

        $modules = new Model_Module();
        $modulesDetail = $modules->getModule();
        $build_ID = $this->_getParam('bid','');
        //echo $this->cust_id;
        if(empty($build_ID) && isset($_COOKIE['build_cookie']))
		$build_ID = $_COOKIE['build_cookie'];
		else
		$set_cookie = setcookie('build_cookie',$build_ID,time() + (86400/24), "/");
        if($this->roleId!=1){
        $building = new Model_Building();
        $buildingDetail = $building->getbuildingbyid($build_ID);
        }else
        $buildingDetail =array();
        $this->view->role = $this->roleId;
        $this->view->cust_id = $this->cust_id;
        $this->view->companyListing = array(
            "roles"     => $roleDetail,
            "modules"   => $modulesDetail,
            "buildings" => $buildingDetail,
        );

        $this->view->acesshelper = $this->accessHelper;        
        $this->view->tenant_location = $this->tenant_location;
        $this->view->select_build_id = $build_ID;
	}

    public function checktenantnameAction(){
		$tenantName = $this->_getParam('tenantName');
		$building = $this->_getParam('building');
		$tenantModel = new Model_Tenant();
		$tenantDetail = $tenantModel->checkTenantByName($tenantName,$building);

        if(!empty($tenantDetail)) 
            echo true;
        else 
            echo false;
		
		exit(0);
	}
	public function checkexisttenantAction(){
		$tenantName = $this->_getParam('tenantName');
		$building = $this->_getParam('building');
		$tId = $this->_getParam('tId');
		$tenantModel = new Model_Tenant();
		$tenantDetail = $tenantModel->checkTenantByName($tenantName,$building,$tId);
       //var_dump($tenantDetail);
        if(!empty($tenantDetail)) 
            echo 'true';
        else 
            echo 'false';
		
		exit(0);
	}
	
	public function updatetenantnameAction(){
		$name = $this->_getParam('name');
		$value = $this->_getParam('value');
		$building = $this->_getParam('building');
		$tId = $this->_getParam('pk');		
		$tenantModel = new Model_Tenant();
		$tenantDetail = $tenantModel->checkTenantByName($value,$building,$tId);
       //var_dump($tenantDetail);
        if(!empty($tenantDetail)) 
            echo 'true';
        else{ 
              $data= array(
                    $name => $value,
                    'updateddate'=> date('Y-m-d H:i:s')
                );
			   if($name!='' && !empty($name)){        
				   $res = $tenantModel->updateTenant($data,$tId);
				}
			 echo 'false';	
            }
		
		exit(0);
	}
    public function checktenantAction(){

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $email = $this->_getParam('email');

        $userModel = new Model_User();
        $userDetail = $userModel->isUserExist($email);

        if(!empty($userDetail)) 
            echo true;
        else 
            echo false;

        exit();

    }

    public function createtenantAction(){
        $data = $this->getRequest()->getPost();
        $tenantModel = new Model_Tenant();
        $userModel = new Model_User();
        $tenantUserModel = new Model_TenantUser();
        //$isExist = $userModel->tenantUserExist($data['email'],5);

        $isExist = $userModel->isUserExist($data['email']);
        
        if(!$isExist){
			$buildingData = array();
			//$module = explode(',',$data['modules']);
			if(isset($data['building']) && $data['building']!=''){             
				$building = new Model_Building();
				$buildingDetail = $building->getbuildingbyid($data['building']);
				$buildingData = $buildingDetail[0];
			}
			/*if(isset($module) && count($module)>0){
			   $modules = new Model_Module();
			   $modulesDetail = $modules->getModuleListing($module);
			}*/

			$tenantData['tenantName'] = $data['tenantName'];
			$tenantData['tenantContact'] = $data['tenantContact'];
			$tenantData['address1'] = $data['address1'];
			$tenantData['address2'] = $data['address2'];
			$tenantData['suite'] = $data['suite'];
			$tenantData['city'] = $data['city'];
			$tenantData['state'] = $data['state'];
			$tenantData['postalCode'] = $data['postalCode'];
			$tenantData['phoneNumber'] = $data['phoneNumber'];
			$tenantData['phoneExt'] = $data['phoneExt'];
			$tenantData['billtoAddress'] = $data['billtoAddress'];
			$tenantData['status'] = $data['status'];
			/*$tenantData['faxNumber'] = $data['faxNumber'];
			$tenantData['attention'] = $data['attention']; */
			
			$tenantData['updateddate'] = date('Y-m-d H:i:s');

			$tenantData['buildingId']= $data['building'];//implode(',', $data['building']);
			$tenantData['tenant_number']=time();			
			//$modules = implode(',', $module);
			

			$detail = array(
					"tenantName"    => $data['tenantName'],
					"tenantContact"    => $data['tenantContact'],
					"phoneNumber"   => $buildingData['phoneNumber'],
					"phoneExt"   => $buildingData['phoneExt'],
					"email"         => $data['email'],		
					"username"         => $data['email'],				
					"access"        => 'Tenant Manager',					
					"address1"      => $data['address1'],
					"address2"      => $data['address2'],
					"suite"         => $data['suite'],
					"city"          => $data['city'],
					"state"         => $data['state'],
					"postalCode"    => $data['postalCode'],                    
				);


				$gpass= $this->generateRandomString();
				$detail['gpass']    = $gpass;
				$userData['email'] = $data['email']; 
				$userData['firstName'] = $data['firstName']; 
				$userData['lastName'] = $data['lastName'];
				$userData['Title'] = $data['title']; 
				$userData['phoneNumber'] = $data['phoneNumber'];
				$userData['userName'] = $data['email']; 
				$userData['password'] = md5($gpass);
				$userData['role_id'] = $data['access']; //tenant manager
				$userData['cust_id']= $this->cust_id;
				$userData['regDate'] = date('Y-m-d H:i:s');

				$userData['uid'] = $userModel->insertUser($userData);
				$tenantData['userId'] = $userData['uid'];
				$tenant = $tenantModel->insertTenant($tenantData); 
                
                /******Insert data in tenant user table *******/
                $tenantUserData['userId'] = $userData['uid'];
				$tenantUserData['tenantId'] = $tenant;
				$tenantUserData['suite_location'] = $data['suite_location'];
				$tenantUserData['cc_enable'] = ($data['access']==5)?1:0; // 1 for tenant admin & 0 for tenant user
				$tenantUserData['send_as'] = 1; // HTML by default
				$tenantUserData['complete_notification'] = 0; // no by default
				$tenantUserModel->insertTenantUser($tenantUserData);
				
				$userBuildingAccess = array();
				$detail['building'] = array(); 
				$userBuildingAccess = array();
					if(isset($data['building']) && $data['building']!=''){
						$userBuildingAccess[] = array(
							"user_id"           => $userData['uid'],
							"building_id"       => $data['building'],
							"modules_id"        => '0',
							"assigned_date"     => date('Y-m-d H:i:s'),
							"last_update_date"  => date('Y-m-d H:i:s'),
						);
					}
				  /*if(isset($data['modules']) && $data['modules']!='' && isset($data['building'])){
						foreach($modulesDetail as $key => $module) {
							$detail['building'][] = array(
									"building"  =>   $this->getBuildingName($buildingDetail, $data['building']),
									"module"    =>   $module['module_name']
								);
						}
					}*/

				if(!empty($userBuildingAccess)) {
						$Model_User_Building_Module = new Model_UserBuildingModule();
						$Model_User_Building_Module->updateBuildingModule($userBuildingAccess);
					}  

				

				if($tenant > 0){
				    $tuser = new Zend_Session_Namespace('tenant_user');
				    $tuser->detail = $detail;
					$res = $this->getWelcomeLetter($detail);
				}else{
					$res = false;
				}

				       
			print_r($res);
        }
        exit();    
               

    }


    public function usersAction(){      
        $companyListing ='';
        
        $msgId = $this->_getParam('msg',0);
        $tId = $this->_getParam('tId',0);
        $build_ID = $this->_getParam('bid','');
        if(empty($build_ID) && isset($_COOKIE['build_cookie']))
		$build_ID = $_COOKIE['build_cookie'];
		else
		$set_cookie = setcookie('build_cookie',$build_ID,time() + (86400/24), "/");
        $msg='';
		 if($msgId==1){
			$msg ='Tenant user has been created successfully.'; 
		 }
		 
		 if($msgId==2){
			$msg ='Tenant user has been updated successfully.'; 
		 }
		 if($msgId==3){
			$msg ='Tenant has been deleted successfully.'; 
		 }
		 $tm = new Zend_Session_Namespace('tenant_message');
		 if(!isset($tm->msg) && $msgId!=0){
			$tm->msg = $msg;
			$tparam = ($tId!=0)?'/tId/'.$tId:'';
			$this->_redirect('/tenant/users/bid/'.$build_ID.''.$tparam);
		  }
        
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
        

         $this->view->companyListing = $companyListing;
         $this->view->custID = $this->cust_id;
         $this->view->roleId     = $this->roleId;

         $tenantList='';
        $tenant = new Model_Tenant();
        
        
         if($this->roleId=='5'){
			 $tenantList = $tenant->getTenantById($this->userId);
			 $this->view->select_build_id = $build_ID;
		 }else {
			if($build_ID!=''){
				$tenantList = $tenant->getTenantByBuildingId($build_ID);
				$this->view->select_build_id = $build_ID;
			}
			 else{ 
				 if($companyListing!=''){
					$tenantList = $tenant->getTenantByBuildingId($companyListing[0]['build_id']);
					$this->view->select_build_id = $companyListing[0]['build_id'];
				}
			}
		}
		
        $this->view->acesshelper = $this->accessHelper;        
        $this->view->tenant_location = $this->tenant_location;
        $this->view->tenantList = $tenantList;
        $this->view->tId = $tId;

        
    }


    public function createuserAction(){
        $roleMapper = new Model_Role();
        $roleDetail = $roleMapper->getRole();

        $modules = new Model_Module();
        $modulesDetail = $modules->getModule();
        $buildingMapper=new  Model_Building();
        $companyListing = array();       
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
        $build_ID = $this->_getParam('bid','');
        $this->view->select_build_id = $build_ID;
        $this->view->role = $this->roleId;
        $this->view->cust_id = $this->cust_id;
        $this->view->companyListing = array(
            "roles"     => $roleDetail,
            "modules"   => $modulesDetail,
            "buildings" => $companyListing,
        );

     
    }

    public function createtenantsusersAction(){
        $data = $this->getRequest()->getPost();
        //print_r($data);
        //exit(0);
        if( $this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')  {
			$tenantModel = new Model_Tenant();
			$userModel = new Model_User();
			$tenantUserModel = new Model_TenantUser();

			//$isExist = $userModel->tenantUserExist($data['email'],5);
            $isExist = $userModel->isUserExist($data['email']);
            if(!$isExist){   
					$module = explode(',',$data['modules']);
					if(isset($data['building']) && $data['building']!=''){             
						$building = new Model_Building();
						$buildingDetail = $building->getbuildingbyid($data['building']);
					}
					if(isset($module) && count($module)>0){
					   $modules = new Model_Module();
					   $modulesDetail = $modules->getModuleListing($module);
					}        

					$tenantData['buildingId']= $data['building'];
					$modules = implode(',', $module);
					$roleMapper = new Model_Role();
					$roleDetail = $roleMapper->getRole($data['access']);

					$detail = array(
							"uname"         => $data['firstName']." ".$data['lastName'],
							"office_phone"  => $data['phoneNumber'],
							"email"         => $data['email'],
							"username"      => $data['email'],
							"userPassowd"   => "**************",
							"access"        => $roleDetail[0]['title'],         

						);


					$gpass= $this->generateRandomString();
					$detail['gpass']    = $gpass;
					$userData['email'] = $data['email']; 
					$userData['firstName'] = $data['firstName']; 
					$userData['lastName'] = $data['lastName']; 
					$userData['phoneNumber'] = $data['phoneNumber'];
					$userData['userName'] = $data['email']; 
					$userData['password'] = md5($gpass);
					$userData['role_id'] = $data['access']; //tenant manager
					$userData['cust_id']= $this->cust_id;
					$userData['regDate'] = date('Y-m-d H:i:s');
					$userDetail = $userModel->isUserExist($data['email']);
					if(!$userDetail){
						$userData['uid'] = $userModel->insertUser($userData);

						$tenantUserDate['userId'] = $userData['uid'];
						$tenantUserDate['tenantId'] = $this->userId;
						$tenantUserModel->insertTenantUser($tenantUserDate);
						
						$tenantData['userId'] = $userData['uid'];
						if($userData['uid'] > 0){
							$mail_flag =1;
						}
					}

					$userBuildingAccess = array();
					$detail['building'] = array(); 
					$userBuildingAccess = array();
					if(isset($data['building']) && $data['building']!=''){
						$userBuildingAccess[] = array(
							"user_id"           => $userData['uid'],
							"building_id"       => $data['building'],
							"modules_id"        => $data['modules'],
							"assigned_date"     => date('Y-m-d H:i:s'),
							"last_update_date"  => date('Y-m-d H:i:s'),
						);
					}
					  if(isset($data['modules']) && $data['modules']!='' && isset($data['building'])){
						foreach($modulesDetail as $key => $module) {
							$detail['building'][] = array(
									"building"  =>   $this->getBuildingName($buildingDetail, $data['building']),
									"module"    =>   $module['module_name']
								);
						}
					}

					if(!empty($userBuildingAccess)) {
							$Model_User_Building_Module = new Model_UserBuildingModule();
							$Model_User_Building_Module->updateBuildingModule($userBuildingAccess);
						}  
				
				if($mail_flag ==1){
							try{
							 $this->sendMail($detail);
						   }catch(Exception $e){
						   }
				 }        
				echo json_encode($detail);   
				
			}
		 }	
        exit(0);    
               

    }


    public function userinfoAction(){
        $companyListing ='';
        $user_build_mod = new Model_UserBuildingModule();
		$buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
		
		 $build_ID = $this->_getParam('bid','');
		/* if($companyListing!=''){
			if($build_ID!='')
			$select_build_id = $build_ID;
			 else
			$select_build_id = $companyListing[0]['build_id'];
	     }*/
	     $select_build_id = $buildinglists[0]['building_id'];
	     $tenantUserModel = new Model_TenantUser();
	     $tuserlist = $tenantUserModel->getTenantUsers($this->userId,$select_build_id);    
		 //$this->view->companyListing = $companyListing;
		 $this->view->custID = $this->cust_id;
		 $this->view->roleId     = $this->roleId;
		 $this->view->tuserlist     = $tuserlist;
         $this->view->select_build_id = $select_build_id;
		 $this->view->acessHelper = $this->accessHelper;		 
		 $this->view->user_info_id = 7;
    }


    public function updatetenantAction(){

        $tenantMapper = new Model_Tenant();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $id = $this->getRequest()->getPost('pk');
            $key = $this->getRequest()->getPost('name');
            $value = $this->getRequest()->getPost('value');
            $data= array(
                    $key => $value,
                    'updateddate'=> date('Y-m-d H:i:s')
                );
           if($key!='' && !empty($key)){        
               $res = $tenantMapper->updateTenant($data,$id);
            }
        }
        exit;
    }
    
    public function updatetenantuserAction(){

        $tenantMapper = new Model_TenantUser();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $id = $this->getRequest()->getPost('pk');
            $key = $this->getRequest()->getPost('name');
            $value = $this->getRequest()->getPost('value');
            $data= array(
                    $key => $value,
                   'updateddate'=> date('Y-m-d H:i:s')
                );               
           if($key!='' && !empty($key)){			         
               $res = $tenantMapper->updateTenantUser($data,$id);
            }
        }
        exit;
    }
    
    
    /**
     *Generate Random Password 
     */
    function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    
    /*
     * Get Building Name
     */ 
    public function getBuildingName($buildingDetail, $building) {
        foreach ( $buildingDetail as $key => $data ) {
            if($data['build_id'] == $building) {
                return $data['buildingName'];
            }
        }
    }
    public function sendWelcomeLetterNow($detail){
		try{		
			$content = $this->getWelcomeLetter($detail);
			$mail = new Zend_Mail('utf-8');	
			$mail->addTo($detail['email']);		   
			//$mail->addTo('brijeshkumar@virtualemployee.com');
			$mail->setSubject('Building Service Request');
			$mail->setFrom('info@virtualemployee.com','Vecrm');
			$mail->setBodyHtml($content);
			$res = $mail->send();
			return $res;
		}catch(Exception $e){
			return false;
		}
	}
    public function sendwelcomeletterAction(){
         $tuser = new Zend_Session_Namespace('tenant_user');		
         $build_ID = $this->_getParam('bid');
         $send_letter = $this->_getParam('send_letter');

		 $detail = $tuser->detail;
			 if($detail!=''){
				/*$detail = array(
							"tenantName"    => 'Tenant Name',
							"tenantContact"    => 'Tenant Name',
							"phoneNumber"   => 'Tenant Name',
							"phoneExt"   => 'Tenant Name',
							"email"         => 'Tenant Name',					
							"access"        => 'Tenant Manager',					
							"address1"      => 'Tenant Name',
							"address2"      => 'Tenant Name',
							"suite"         => 'Tenant Name',
							"city"          => 'Tenant Name',
							"state"         => 'Tenant Name',
							"postalCode"    => 'Tenant Name',					

						);*/
			  $data = $this->getRequest()->getPost();
			  /*****Send Welcome mail *****/
			  if(isset($send_letter) && $send_letter=='1'){		
			    $content = $this->getWelcomeLetter($detail);
			    try{
					$mail = new Zend_Mail('utf-8');	
				   
					// configure base stuff
					$mail->addTo($detail['email']);
					//$mail->addTo('brijeshkumar@virtualemployee.com');
					$mail->setSubject('Building Service Request');
					$mail->setFrom('info@virtualemployee.com','Vecrm');
					$mail->setBodyHtml($content);
					$res = $mail->send();
				}catch(Exception $e){
					echo $e->getMessage();
				}
				$userModel = new Model_User();
				$userData = $userModel->checkUserEmail($detail['email']);
				
				$email_log = new Model_Log();
				$logData = array();
				$logData['email_sent_by'] =  $this->userId;
			    $logData['userId'] =  $userData[0]['uid'];
				$logData['log_type'] =  'email';
				$logData['email'] =  $detail['email'];
				$logData['log_message'] =  'Sent welcome letter to tenant';

				if($res){
				  	$logData['email_status'] =  1;
				  	$email_log->insertLog($logData);
				}
				else{
				  	$logData['email_status'] =  0;
				  	$email_log->insertLog($logData);
				}
		     }
		 }		
		$this->_redirect('/tenant/users/bid/'.$build_ID);
	}
   /**
    *Get Welcome letter
    */
    public function getWelcomeLetter($detail){
		$emailMapper = new Model_Email();
		$loadTemplate = $emailMapper->loadEmailTemplate(5);
		if($loadTemplate){
			$emailTemplate = $loadTemplate[0];
			$content = $emailTemplate['email_content'];
			/******get Company Name ******/
			$accoutMapper = new Model_Account();
			$company = $accoutMapper->getcompany($this->cust_id);
			$companyName = $company[0]['companyName'];
			
			/******** get User Data ************/
			$userModel = new Model_User();
			$userDetail = $userModel->getUserInfo($this->userId);
			$userData = $userDetail[0];
			$full_Name = $userData->firstName.' '.$userData->lastName;
			$roleManager = $userData->role_title;
			/*****change the key with value in the content ****/
			$currDate = date('F d, Y');
			$content = str_replace('[[++currDate]]', $currDate, $content );
			$content = str_replace('[[++tenantContact]]', $detail['tenantContact'], $content);
			$content = str_replace('[[++tenantName]]', $detail['tenantName'], $content);
			$content = str_replace('[[++address1]]', $detail['address1'], $content);
			$content = str_replace('[[++address2]]', $detail['address2'], $content);
			$content = str_replace('[[++city]]', $detail['city'], $content);
			$content = str_replace('[[++state]]', $detail['state'], $content);
			$content = str_replace('[[++postalCode]]', $detail['postalCode'], $content);
			$content = str_replace('[[++siteURL]]', BASEURL, $content);
			$content = str_replace('[[++email]]', $detail['email'], $content);
			$content = str_replace('[[++username]]', $detail['username'], $content);
			$content = str_replace('[[++password]]', $detail['gpass'], $content);
			$content = str_replace('[[++phoneNumber]]', $detail['phoneNumber'], $content);
			
			if($detail['phoneExt']!='')
			 $content = str_replace('[[++phoneExt]]', '( '.$detail['phoneExt'].' )', $content);
			 else
			 $content = str_replace('[[++phoneExt]]', '', $content);
			$content = str_replace('[[++acCompanyName]]', $companyName, $content);
			
			$content = str_replace('[[++userFullName]]', $full_Name, $content);
			$content = str_replace('[[++userRole]]', $roleManager, $content);
			
			return $content;
		}else
		return false;
	}
   public function sendMail($detail){      
        $emial_template = new Zend_View();
        $emial_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/email/');
        $emial_template->assign('uname', $detail['uname']);
        $emial_template->assign('access', $detail['access']);
        $emial_template->assign('email', $detail['email']);
        $emial_template->assign('password', $detail['gpass']);
        $emial_template->assign('building',$detail['building']);

        
                        
        $mail = new Zend_Mail('utf-8');
        // render view
        
        $bodyText = $emial_template->render('newUserRegistation.phtml');
       
        // configure base stuff
        $mail->addTo($detail['email'], $detail['email']);
        $mail->setSubject('New User Registration Conformation');
        $mail->setFrom('info@virtualemployee.com','Vecrm');
        $mail->setBodyHtml($bodyText);
        $mail->send();              
            
   }
   
	
	 
	 /***
	  * Show tenant user's detail
	  */ 
    public function tenantuserAction(){
		$msgId = $this->_getParam('msg',0);        
        $msg='';
		 if($msgId==1){
			$msg ='Tenant user has been created successfully.'; 
		 }
		 
		 if($msgId==2){
			$msg ='Tenant user has been updated successfully.'; 
		 }
		 if($msgId==3){
			$msg ='Tenant has been deleted successfully.'; 
		 }
		 $tm = new Zend_Session_Namespace('tenant_message');
		 if(!isset($tm->msg) && $msgId!=0){
			$tm->msg = $msg;			
			$this->_redirect('/tenant/tenantuser');
		  }
		$tenant = new Model_Tenant();		
		$tenantuser = $tenant->getTenantByUser($this->userId);	
		//var_dump($tenantuser);
		
		$this->view->roleId = $this->roleId;
		$this->view->tenantuser = $tenantuser[0];
	}	
	 
	 /**
	  * Load tenant user list
	  */ 
	 public function loadtenantuserAction(){
		$this->_helper->layout()->disableLayout();        
		$tenant = new Model_TenantUser();		
		$data = $this->getRequest()->getPost();
		$userId = $data['tId']; //50;
		$tenantMapper = new Model_Tenant();
		
		$tenantData = $tenantMapper->getTenantById($userId);
		$tenantuser = $tenant->getTenantUsers($userId);		
		
		
		$modelMapper = new Model_Module();
		$moduleList = $modelMapper->getModule();
		
		$this->view->moduleList = $moduleList;
		$this->view->roleId = $this->roleId;
		$this->view->cust_id = $this->cust_id;
		$this->view->tenantuser = $tenantuser;
		$this->view->tenantData = $tenantData;
		
		$this->view->acesshelper = $this->accessHelper;        
        $this->view->tenant_location = $this->tenant_location;	
	 }
	 /**
	  * Add new user under tenant
	  */
	 public function  addtenanttuserAction(){
		   $data = $this->getRequest()->getPost();
		   //print_r($data);
		   $message = array();
		    $gpass= $this->generateRandomString();
		    $userData = array();
			//$detail['gpass']    = $gpass;
			$userData['email'] = $data['email']; 
			$userData['firstName'] = $data['firstName']; 
			$userData['lastName'] = $data['lastName'];
			/*$userData['Title'] = $data['title'];*/ 
			$userData['phoneNumber'] = $data['phone'];
			$userData['userName'] = $data['email']; 
			$userData['password'] = md5($gpass);
			$userData['role_id'] = $data['access']; //tenant manager
			$userData['cust_id']= $this->cust_id;
			$userData['regDate'] = date('Y-m-d H:i:s');
			$userModel     = new Model_User();
			$tenantUserModel = new Model_TenantUser();
            $userDetail = $userModel->isUserExist($data['email']);
            //var_dump($userDetail);
            $tenantUserData = array();
            if(isset($data['buildId']) && $data['buildId']!=''){             
				$building = new Model_Building();
				$buildingDetail = $building->getbuildingbyid($data['buildId']);
			}
			$modList = explode(',',$data['modules']);
			if(isset($data['modules']) && count($modList)>0){
			   $modules = new Model_Module();
			   $modulesDetail = $modules->getModuleListing($modList);
			}
            if(!$userDetail){
				try{
				    $userData['uid'] = $userModel->insertUser($userData);
					$tenantUserData['userId'] = $userData['uid'];
					$tenantUserData['tenantId'] = $data['tenantId'];
					$tenantUserModel->insertTenantUser($tenantUserData);
					
					$tenantData['userId'] = $userData['uid'];
					$userBuildingAccess = array();
					if(isset($data['buildId']) && $data['buildId']!=''){
						$userBuildingAccess[] = array(
							"user_id"           => $userData['uid'],
							"building_id"       => $data['buildId'],
							"modules_id"        => $data['modules'],
							"assigned_date"     => date('Y-m-d H:i:s'),
							"last_update_date"  => date('Y-m-d H:i:s'),
						);
					}
					if(!empty($userBuildingAccess)) {
						$Model_User_Building_Module = new Model_UserBuildingModule();
						$Model_User_Building_Module->updateBuildingModule($userBuildingAccess);
					}
					
					$roleMapper = new Model_Role();
					$roleDetail = $roleMapper->getRole($data['access']);
                    
                   
					$detail = array(
							"uname"         => $data['firstName']." ".$data['lastName'],
							"office_phone"  => $data['phone'],
							"email"         => $data['email'],
							"username"      => $data['email'],
							"gpass"         =>   $gpass,
							"access"        => $roleDetail[0]['title'],         

						);
					 if(isset($data['modules']) && $data['modules']!='' && isset($data['buildId'])){
						foreach($modulesDetail as $key => $module) {
							$detail['building'][] = array(
									"building"  =>   $this->getBuildingName($buildingDetail, $data['buildId']),
									"module"    =>   $module['module_name']
								);
						}
					}	 
					if($userData['uid'] > 0){
						$this->sendMail($detail);
					}
					$message['status'] = 'success';
				     $message['msg']='New User has been created.';
				 }catch(Exception $e){
					 $message['status'] = 'error';
				     $message['msg']='Some error occurred during create new user.';
				 }   
			}else{
				$message['status'] = 'email_error';
				$message['msg']='This email id is exists.';
			}
			echo json_encode($message);
		 exit(0);
	 }
	 
	 public function loadedittuserAction(){
		 $data = $this->getRequest()->getPost();
		 $tuserId = $data['tuserId'];
		 $tenantId = $data['tId'];
		 $userModel     = new Model_User();
		 $userDetail    = $userModel->getUserById($tuserId);
		 $tuser_template = new Zend_View();
		 $tuser_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/tenant/');
		 $tuser_template->assign('roleId', $this->roleId);
		 $tuser_template->assign('cust_id', $this->cust_id);
		 $tuser_template->assign('userDetail', $userDetail[0]);
		 $tuser_template->assign('tenantId', $tenantId);	 			
		 $bodyText = $tuser_template->render('edittuser.phtml');
		 echo $bodyText;
		 exit(0);
	 }
	 
	 public function edittuserAction(){
		 $tuId = $this->_getParam('tuId');
		 $message = array();
		 $tenantUserModel = new Model_TenantUser();
		 $tenant = new Model_Tenant();		 
		 $tenantData = $tenantUserModel->getTenantUserById($tuId);
		 if($this->getRequest()->getMethod() == 'POST'){
				$data = $this->getRequest()->getPost();				
				 
				$uid = $data['uid'];
				$userData = array();
				$userData['userName'] = $data['userName']; 
				$userData['email'] = $data['email']; 
				$userData['firstName'] = $data['firstname']; 
				$userData['lastName'] = $data['lastname'];			
				$userData['phoneNumber'] = $data['phone'];
				$userData['role_id'] = $data['access']; 			
				$userData['regDate'] = date('Y-m-d H:i:s');
				$userData['status'] = $data['status'];
                $send_email = 0; 
				if(isset($data['auto']) && $data['auto'] == 1)
				{	
					$gpass= $this->generateRandomString();
					$userData['password']= md5($gpass);
					$data['password']=$gpass;
					$send_email=1;
				}
				else if(!empty($data['password'])){
					$userData['password']= md5($data['password']);
					$send_email=1;
				}
				else{
					$data['password']='';
				}
				
				if($tenantData[0]->userName != $data['userName'])
					$send_email=1;

				if($tenantData[0]->email != $data['email'])
					$send_email = 1;


				$userModel     = new Model_User();			
                $userNameDetail = $userModel->checkUserName($data['userName'],$uid);
                $userEmailDetail = $userModel->checkUserEmail($data['email'],$uid);
                if(!$userNameDetail && !$userEmailDetail){
					try{
					    $userData['uid'] = $userModel->updateUser($userData,$uid);
					    $id = $data['id'];
					    $tenantUserData['suite_location'] = $data['suite_location'];
					    $tenantUserData['cc_enable'] = $data['cc_enable']; 
						$tenantUserData['send_as'] = $data['send_as'];
						$tenantUserData['complete_notification'] = $data['complete_notification'];
						if($data['welcome_letter'] == 1)
						{
							$this->sendemailAction(true,$data['uid'],$data['tenantId'],$data['password']);

						}
						elseif($send_email == 1){
							$this->sendemailAction(true,$data['uid'],$data['tenantId'],$data['password']);
						}

						$tenantUserModel->updateTenantUser($tenantUserData,$id);
						$build_ID = $data['building'];
						$tId = $data['tenantId'];
						if($this->roleId==5){
							$this->_redirect('/tenant/tenantuser/msg/2');
						}else
						$this->_redirect('/tenant/users/bid/'.$build_ID.'/tId/'.$tId.'/msg/2');
				    }catch(Exception $e){
						$message['msg']='Error occured';
					}
				}else
                $message['msg']='Error occured';
		  }
		 if($tenantData){
			 $tuserDetail = $tenantData[0];			 
			 $tenantData = $tenant->getTenantById($tuserDetail->tenantId);
			 $this->view->roleId = $this->roleId;
			 $this->view->tenantId = $tuserDetail->tenantId;
			 $this->view->tenantData = $tenantData[0];
			 $this->view->tuserDetial = $tuserDetail;
		 }else
		   $message['msg']='Invalid Data';
		 $this->view->message = $message;		
	 }
	 
	 public function addtuserAction(){
		 $message = array();		 
		 $tId = $this->_getParam('tId');
		 $tenant = new Model_Tenant();
		 $tenantData = $tenant->getTenantById($tId);
		 if($this->getRequest()->getMethod() == 'POST') 
		 {
		   $data = $this->getRequest()->getPost();
		   //print_r($data);
		   $message = array();
		    $gpass= $this->generateRandomString();
		    $userData = array();
			//$detail['gpass']    = $gpass;
			$userData['email'] = $data['email']; 
			$userData['firstName'] = $data['firstname']; 
			$userData['lastName'] = $data['lastname'];
			/*$userData['Title'] = $data['title'];*/ 
			$userData['phoneNumber'] = $data['phone'];
			$userData['userName'] = $data['email']; 
			$userData['password'] = md5($gpass);
			$userData['role_id'] = $data['access']; //tenant manager
			$userData['cust_id']= $this->cust_id;
			$userData['regDate'] = date('Y-m-d H:i:s');
			$userData['status'] = $data['status'];
			$userModel     = new Model_User();
			$tenantUserModel = new Model_TenantUser();
            $userDetail = $userModel->isUserExist($data['email']);
             if(!$userDetail){
					try{
						$userData['uid'] = $userModel->insertUser($userData);
						$tenantUserData['userId'] = $userData['uid'];
						$tenantUserData['tenantId'] = $data['tenantId'];
						$tenantUserData['suite_location'] = $data['suite_location'];
						$tenantUserData['cc_enable'] = $data['cc_enable']; 
						$tenantUserData['send_as'] = $data['send_as'];
						$tenantUserData['complete_notification'] = $data['complete_notification'];
						$tenantUserModel->insertTenantUser($tenantUserData);
						
						$tenantData['userId'] = $userData['uid'];
						
						$userBuildingAccess = array();
						$userBuildingAccess[] = array(
							"user_id"           => $userData['uid'],
							"building_id"       => $data['building'],
							"modules_id"        => '0',
							"assigned_date"     => date('Y-m-d H:i:s'),
							"last_update_date"  => date('Y-m-d H:i:s'),
						);
						
						if(!empty($userBuildingAccess)) {
							$Model_User_Building_Module = new Model_UserBuildingModule();
							$Model_User_Building_Module->updateBuildingModule($userBuildingAccess);
						}
						
						$buildingData = array();
						//$module = explode(',',$data['modules']);
						if(isset($data['building']) && $data['building']!=''){             
							$building = new Model_Building();
							$buildingDetail = $building->getbuildingbyid($data['building']);
							$buildingData = $buildingDetail[0];
						}
						$roleMapper = new Model_Role();
						$roleDetail = $roleMapper->getRole($data['access']);
						
					   $tdata = (array)$tenantData[0];
						$detail = array(
							"tenantName"    => $tdata['tenantName'],
							"tenantContact"    => $tdata['tenantContact'],
							"phoneNumber"   => $buildingData['phoneNumber'],
							"phoneExt"   => $buildingData['phoneExt'],
							"email"         => $data['email'],	
							"username"         => $data['email'],																
							"address1"      => $tdata['address1'],
							"address2"      => $tdata['address2'],
							"suite"         => $tdata['suite'],
							"city"          => $tdata['city'],
							"state"         => $tdata['state'],
							"postalCode"    => $tdata['postalCode'],                    
						);
						$detail['gpass']    = $gpass;
						$msg = 1;
						try{
						  $res = $this->sendWelcomeLetterNow($detail);

							$userLData = $userModel->checkUserEmail($data['email']);
							
							$email_log = new Model_Log();
							$logData = array();
							$logData['email_sent_by'] =  $this->userId;
						    $logData['userId'] =  $userLData[0]['uid'];
						    $logData['email'] =  $data['email'];
							$logData['log_type'] =  'email';
							$logData['log_message'] =  'Sent welcome letter to tenant user in create new tenant user';

							if($res){
							  	$logData['email_status'] =  1;
							  	$email_log->insertLog($logData);
							}
							else{
							  	$logData['email_status'] =  0;
							  	$email_log->insertLog($logData);
							}

					    }catch(Exception $e){
							echo 'Mail not sending';
						} 
						$message['status'] = 'success';
						$message['msg']='New User has been created.';
						$build_ID = $tdata['buildingId'];
						if($this->roleId==5){
							$this->_redirect('/tenant/tenantuser/msg/1');
						}else
						$this->_redirect('/tenant/users/bid/'.$build_ID.'/tId/'.$tId.'/msg/1');
					 }catch(Exception $e){
						 echo $e->getMessage();
						 $message['status'] = 'error';
						 $message['msg']='Some error occurred during create new user.';
					 }   
				}else{
					$message['status'] = 'email_error';
					$message['msg']='This email id is exists.';
				}
		   
		 }
		 
		 $this->view->roleId = $this->roleId;
		 $this->view->tenantId = $tId;
		 $this->view->tenantData = $tenantData[0];
		 $this->view->message = $message;
		  
	 }


	public function sendTenantMail($detail){	   
	 
		// $config = array('ssl' => 'tls', 'port' => 587, 'auth' => 'login', 'username' => 'anujatazularc@gmail.com', 'password' => '2j86mbday');

		// $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
		try{				
			$content = $this->getWelcomeLetter($detail);
			$mail = new Zend_Mail('utf-8');	
			$mail->addTo($detail['email']);		   
			//$mail->addTo('brijeshkumar@virtualemployee.com');
			$mail->setSubject('Building Service Request');
			$mail->setFrom('info@virtualemployee.com','Vecrm');
			$mail->setBodyHtml($content);
			if($mail->send())
				return true;
			else
				return false;
		 }catch(Exception $e){
			 return false;
		 }						
			
   } 	 


   public function sendtenantemailAction(){
   		$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->getRequest()->getPost();
        $tenantId = $data['tid'];

        

        $userModel = new Model_User();
        $tenantUserModel = new Model_TenantUser();
		$tenant = new Model_Tenant();
		$gpass= $this->generateRandomString();		 
		$tenantData = $tenant->getTenantById($tenantId);
		$userData = $tenantUserModel->getTenantUserById($tenantId);

        
		$tenantData = $tenant->getTenantById($tenantId);
		$tenantuser = $tenantUserModel->getTenantUsers($tenantId);
		$buildingData = array();
		//$module = explode(',',$data['modules']);
		if(isset($tenantData['buildingId']) && $tenantData['buildingId']!=''){             
			$building = new Model_Building();
			$buildingDetail = $building->getbuildingbyid($tenantData['buildingId']);
			$buildingData = $buildingDetail[0];
		}	

		$i=0;
		$res= array();

		foreach ($tenantuser as $key => $value) {
			$detail = array(
					"tenantName"    => $tenantData[0]->tenantName,
					"tenantContact"    => $tenantData[0]->tenantContact,
					"phoneNumber"   => $buildingData['phoneNumber'],
					"phoneExt"   => $buildingData['phoneExt'],
					"email"         => $value->email,		
					"username"         => $value->userName,			
					//"access"        => 'Tenant Manager',					
					"address1"      => $tenantData[0]->address1,
					"address2"      => $tenantData[0]->address2,
					"suite"         => $tenantData[0]->suite,
					"city"          => $tenantData[0]->city,
					"state"         => $tenantData[0]->state,
					"postalCode"    => $tenantData[0]->postalCode,                    
				);
                $gpass= $this->generateRandomString();
				$detail['gpass'] = $gpass;

				$resp = $res[$i++] = $this->sendTenantMail($detail);

				$userModel = new Model_User();
				$userData = $userModel->checkUserEmail($value->email);
				
				$email_log = new Model_Log();
				$logData = array();
				$logData['email_sent_by'] =  $this->userId;
			    $logData['userId'] =  $userData[0]['uid'];
			    $logData['email'] =  $value->email;
				$logData['log_type'] =  'email';
				$logData['log_message'] =  'Sent welcome letter to tenant user';

				if($resp){
				  	$logData['email_status'] =  1;
				  	$email_log->insertLog($logData);
				}
				else{
				  	$logData['email_status'] =  0;
				  	$email_log->insertLog($logData);
				}

				if(count($res) >=1){
					$userModel->changePassword($gpass,$value->uid);
				}		
		}

		if($i == count($res))
			echo true;
		else
			false;

		exit();
   }   
   
	public function sendemailAction($welcome_letter=false,$userId='',$tenantId='',$password=''){

	 	$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->getRequest()->getPost();

        if(!$welcome_letter){
        	$userId = $data['uid'];
        	$tenantId = $data['tid'];
        }
        

        $userModel = new Model_User();
        $tenantUserModel = new Model_TenantUser();
		$tenant = new Model_Tenant();
		$gpass= $this->generateRandomString();		 
		$tenantData = $tenant->getTenantById($tenantId);
		$userData = $tenantUserModel->getTenantUserById($userId);
        $buildingData = array();
		//$module = explode(',',$data['modules']);
		if(isset($tenantData['buildingId']) && $tenantData['buildingId']!=''){             
			$building = new Model_Building();
			$buildingDetail = $building->getbuildingbyid($tenantData['buildingId']);
			$buildingData = $buildingDetail[0];
		}

		$detail = array(
					"tenantName"    => $tenantData[0]->tenantName,
					"tenantContact"    => $tenantData[0]->tenantContact,
					"phoneNumber"   => $buildingData['phoneNumber'],
					"phoneExt"   => $buildingData['phoneExt'],
					"email"         => $userData[0]->email,
					"username"         => $userData[0]->userName,					
					//"access"        => 'Tenant Manager',					
					"address1"      => $tenantData[0]->address1,
					"address2"      => $tenantData[0]->address2,
					"suite"         => $tenantData[0]->suite,
					"city"          => $tenantData[0]->city,
					"state"         => $tenantData[0]->state,
					"postalCode"    => $tenantData[0]->postalCode,                    
				);

		if(!empty($password)){
			$detail['gpass'] = $password;
		}
		else{
			$detail['gpass'] = $gpass;
		}
			

		$res = $this->sendTenantMail($detail);

		$userModel = new Model_User();
		$userData = $userModel->checkUserEmail($detail['email']);
		
		$email_log = new Model_Log();
		$logData = array();
		$logData['email_sent_by'] =  $this->userId;
	    $logData['userId'] =  $userData[0]['uid'];
	    $logData['email'] =  $detail['email'];
		$logData['log_type'] =  'email';
		$logData['log_message'] =  'Sent welcome letter to tenant user';

		if($res){
		  	$logData['email_status'] =  1;
		  	$email_log->insertLog($logData);
		}
		else{
		  	$logData['email_status'] =  0;
		  	$email_log->insertLog($logData);
		}

		if($res && empty($password)){
			if($userModel->changePassword($gpass,$userId)){
				if(!$welcome_letter)
					echo true;
				else
					return true;
			}
			else
				echo false;
		}
		else if($welcome_letter)
			return true;
		else
			echo false;

		exit();

	 }

	 
	 public function deletetuserAction(){		
		 if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
		   $data = $this->getRequest()->getPost();
		   $uId = $data['uId'];
		    $message= array();
		   if(isset($uId) && $uId!=''){
			  try{
				  $userMapper = new Model_User();
				  $deleteUser = $userMapper->deleteUser($uId);	

				  if($deleteUser){
				  	$updateData['userStatus'] = '1';
				  	$tenantMapper = new Model_Tenant();
				  	$tenantMapper->updateTenant($updateData,$data['tId']);
				  }			  
				  $tm = new Zend_Session_Namespace('tenant_message');
				  $tm->msg = 'Tenant user deleted successfully.';
		          $message['msg']='true';
				  
			  }catch(Exception $e){
				  $message['msg']='false';
			  } 
		   }else{		    
		    $message['msg']='false';
		    }
	     }
	     
	     echo json_encode($message);
	     exit(0);
	 }
	 
	 public function deletetenantAction(){
		 if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
		   $data = $this->getRequest()->getPost();
		   $tId = $data['tId'];
		    $message= array();
		   if(isset($tId) && $tId!=''){
			  try{
				  $tenantMapper = new Model_Tenant();
				  $deletetenant = $tenantMapper->deleteTenant($tId);			  
				  
		          $message['msg']='true';
				  
			  }catch(Exception $e){
				  $message['msg']='false';
			  } 
		   }else{		    
		    $message['msg']='false';
		    }
	     }
	     
	     echo json_encode($message);
	     exit(0);
	 }

	 public function tenantrecoveryAction(){

	 	$tenantMapper = new Model_Tenant();

	 	$companyListing ='';
        
        $msgId = $this->_getParam('msg',0);
        $tId = $this->_getParam('tId',0);
        $build_ID = $this->_getParam('bid');
        if(empty($build_ID))
		$build_ID = $_COOKIE['build_cookie'];
		else
		$set_cookie = setcookie('build_cookie',$build_ID,time() + (86400/24), "/");
        $msg='';

		 if($msgId==1){
			$msg ='Tenant user has been activated successfully.'; 
		 }
		 
		 
		 $tm = new Zend_Session_Namespace('tenant_message');
		 if(!isset($tm->msg) && $msgId!=0){
			$tm->msg = $msg;
			$tparam = ($tId!=0)?'/tId/'.$tId:'';
			$this->_redirect('/tenant/tenantrecovery/bid/'.$build_ID.''.$tparam);
		  }
        
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
        

         $this->view->companyListing = $companyListing;
         $this->view->custID = $this->cust_id;
         $this->view->roleId     = $this->roleId;

         $tenantList='';
        $tenant = new Model_Tenant();
        
 
         if($this->roleId=='5'){
			 $tenantList = $tenant->getTenantById($this->userId,true);
			 $this->view->select_build_id = $build_ID;
		 }else {
			if($build_ID!=''){ 
				$tenantList = $tenant->getTenantByBuildingId($build_ID,true);
				$this->view->select_build_id = $build_ID;

			}
			 else{ 
				 if($companyListing!=''){
					$tenantList = $tenant->getTenantByBuildingId($companyListing[0]['build_id'],true);
					$this->view->select_build_id = $companyListing[0]['build_id'];
				}
			}
		}
		

        $this->view->tenantList = $tenantList;
        $this->view->tId = $tId;

	 }


	 public function loadtenantinactiveuserAction(){
	 	$this->_helper->layout()->disableLayout();        
		$tenant = new Model_TenantUser();
		//$tenantuser = $tenant->getTenantUsers($this->userId);
		$data = $this->getRequest()->getPost();
		$userId = $data['tId']; //50;
		$buildId = $data['bId'];
		$tenantMapper = new Model_Tenant();
		$tenantData = $tenantMapper->getTenantById($userId,true);
		$tenantuser = $tenant->getTenantUsers($userId,'',true);		
		
		$modelMapper = new Model_Module();
		$moduleList = $modelMapper->getModule();
		
		
		$this->view->roleId = $this->roleId;
		$this->view->cust_id = $this->cust_id;
		$this->view->tenantuser = $tenantuser;
		$this->view->tenantId = $userId;
		$this->view->tenantData = $tenantData;
		$this->view->moduleList = $moduleList;
		$this->view->buildId = $buildId;		
	 }


	 public function activetenantuserAction(){
	 	$data = $this->getRequest()->getPost();
	 	$user = new Model_User();
	 	$tenant = new Model_Tenant();
	 	$usersId = $data['tenantUser'];
	 	$tenantId = $data['main_tenant'];
	 	$build_ID = $data['buildID'];
	 	$userData['remove_status'] = 0;
	 	
	 	$i=0;
	 	$result = false;
	 	if(!empty($usersId)){
	 		foreach ($usersId as $key => $id) {
	 			$result[$i++] = $user->updateUser($userData,$id);
	 		}
	 	}
	 	
	 	

	 	if($i == count($result)){
	 		if($data['totalUser'] == count($data['tenantUser'])){
	 			$tenantData['userStatus'] = 0;
	 			$tenant->updateTenant($tenantData,$tenantId);
	 		}
	 		$this->_redirect('/tenant/tenantrecovery/bid/'.$build_ID.'/tId/'.$tenantId.'/msg/1');
	 	}
	 	
	 }


	 public function recoveruserAction(){
	 	$id = $this->_getParam('id');

	 	$tenant = new Model_Tenant();
	 	$user = new Model_User();
	 	$tenantUserModel = new Model_TenantUser();

	 	$tenantuser = $tenantUserModel->getTenantUsers($id,'',true);	
	 	$data['remove_status'] = '0';
	 	$res = $tenant->updateTenant($data,$id);
	 	$res = 1;
	 	$i=0;
	 	if($res){
	 		$result = false;
	 		if(!empty($tenantuser)){
	 			foreach ($tenantuser as $key => $value) {
	 				$result[$i++] = $user->updateUser($data,$value->uid);
	 				//echo "<pre>"; print_r($value->uid); exit();
	 			}
	 		}
	 		else{

	 			$updateData['userStatus'] = '0';
				$tenant->updateTenant($updateData,$id); 
	 			echo true;
	 			exit(0);
	 		}
	 		
	 	}
	 	else{
	 		echo false;
	 		exit(0);
	 	}

	 	$updateData['userStatus'] = '0';
		$tenant->updateTenant($updateData,$id); 

	 	if($i == count($result)){
	 		echo true;
	 		exit(0);
	 	}
	 	else{
	 		echo false;
	 		exit(0);
	 	}


	 }
	 
	 public function checkusernameAction(){
		$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $userName = $this->_getParam('userName');
        $uid = $this->_getParam('uid');

        $userModel = new Model_User();
        $userDetail = $userModel->checkUserName($userName,$uid);

        if(!empty($userDetail)) 
            echo true;
        else 
            echo false;

        exit();
	 }
	 
	 public function checkuseremailAction(){
		$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $email = $this->_getParam('email');
        $uid = $this->_getParam('uid');

        $userModel = new Model_User();
        $userDetail = $userModel->checkUserEmail($email,$uid);

        if(!empty($userDetail)) 
            echo true;
        else 
            echo false;

        exit();
	 }
	 
	 public function createtuserAction(){
		$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->getRequest()->getPost();
        //print_r($data);
        $userModel     = new Model_User();
		$tenantUserModel = new Model_TenantUser();        
        $isExist = $userModel->isUserExist($data['email']);
		if(!$isExist){
			   try{
					$userData = array();
					$gpass= $this->generateRandomString();
					$detail['gpass']    = $gpass;
					$userData['email'] = $data['email']; 
					$userData['firstName'] = $data['firstName']; 
					$userData['lastName'] = $data['lastName']; 
					$userData['phoneNumber'] = $data['phoneNumber'];
					$userData['userName'] = $data['email']; 
					$userData['password'] = md5($gpass);
					$userData['role_id'] = 7; //tenant user
					$userData['cust_id']= $this->cust_id;
					$userData['regDate'] = date('Y-m-d H:i:s');				
					$userData['uid'] = $userModel->insertUser($userData);
					
					$tenantUserData = array();
					$tenantUserData['userId'] = $userData['uid'];
					$tenantUserData['tenantId'] = $data['tenantId'];
					$tenantUserData['suite_location'] = $data['suite_location'];
					$tenantUserData['cc_enable'] = 0;
					$tenantUserData['send_as'] = 1; // default HTML
					$tenantUserData['complete_notification'] = 0;
					$tenantUserModel->insertTenantUser($tenantUserData);
					$status = 'success';
					$msg="Tenant User created successfully.";
				}catch(Exception $e){
					$status = "error";
					$msg="Error Ocurred During the creation of new user";
				}
				
				
			}else{
				$status = 'error';
				$msg="Email Already exists!!";
			}
			
			echo json_encode(array('status'=>$status,'msg'=>$msg));
	     exit(0);
	 }

}

?>
