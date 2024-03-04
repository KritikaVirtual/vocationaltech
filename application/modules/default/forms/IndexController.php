<?php
class IndexController extends Ve_Controller_Base
{

	private $userId='';
	
	// Call befor any action and check is user login or not
  
    
    public function preDispatch()
	{		
		parent::preDispatch();
		
		if (Zend_Auth::getInstance()->hasIdentity())
		{
			if ('logout' != $this->getRequest()->getActionName())
			{
				$this->_helper->_redirector->gotoUrl('/dashboard');
			}
		}else{
			if ('logout' == $this->getRequest()->getActionName())
			{
				/*$this->_flashMessenger->addMessage(array('success'=>'You have successfully logged out!'));*/
				$this->_helper->_redirector->gotoUrl('/index');
			}
		}
	}
	public function indexAction()
    {
    	$form=new Form_Loginform();
		$request = $this->getRequest();
		
		
    	// Check if we have a POST request
    	if ($this->_request->isPost()) {
    		
    		// Get our form and validate it
    		$formData=$this->_request->getPost();
			
			
			
    		if ($form->isValid($formData)) {
    			
				$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());  	
				$authAdapter->setTableName('users')  
		                ->setIdentityColumn('userName')  
		                ->setCredentialColumn('password');		                
		               // ->setCredentialColumn('status');
		        // pass to the adapter the submitted username and password
				$authAdapter->setIdentity($formData['email'])
		                    ->setCredential(md5($formData['password']))	;	                    
		                   // ->setCredential('1');				
				
					
		                   		
		        $auth = Zend_Auth::getInstance();
		        $result = $auth->authenticate($authAdapter);
		        
		        $userInfo = $authAdapter->getResultRowObject(null, 'password');
		        
		        $accModel = new Model_Account();
		        $cplist = $accModel->getcompany();
		        $cp_array = array();
		        foreach($cplist as $cp){
					if($cp['status']=='1'){
						$cp_array[] = $cp['cust_id'];
					}
				}
				
				$company_flag = true;
				if($userInfo){
					if(!empty($userInfo->cust_id) && !in_array($userInfo->cust_id,$cp_array)){
						$company_flag = false;
					}
					if($company_flag && ($userInfo->role_id == '5' || $userInfo->role_id == '7') ){
						$tenantModel = new Model_Tenant();
						$tdata = $tenantModel->getTenantByUid($userInfo->uid);
						$tenantData = $tdata[0];
						if($tenantData->status == '0')
						 $company_flag = false;
					}
					
				}
		        
		        if($result->isValid() && $userInfo->status=='1' && $userInfo->remove_status=='0' && $company_flag){ // is the user a valid one?
		        	//$userInfo = $authAdapter->getResultRowObject(null, 'password');			        		            						
			        $authStorage = $auth->getStorage();// the default storage is a session with namespace Zend_Auth
			        $userModel = new Model_User();
			        $updateUser = $userModel->updateUser(array('login_session'=>time()),$userInfo->uid);
			        $userData = $userModel->getUserById($userInfo->uid);
			        $authStorage->write($userData[0]);
					$adminNamespace = new Zend_Session_Namespace('Admin_User');
                    $adminNamespace->role_id = $userInfo->role_id;
                    $adminNamespace->user_id = $userInfo->uid;                    
					$this->_redirect('dashboard');	
					
			        
		        }else if($result->isValid() && $userInfo->status=='0' && $userInfo->remove_status=='0'){
					$this->view->error=array('active'=>'0');            	
	                $form->populate($formData);	
	                Zend_Auth::getInstance()->clearIdentity();
		        }
		        else if($result->isValid() && $userInfo->remove_status=='1'){
					$this->view->error=array('remove'=>'0');            	
	                $form->populate($formData);	
	                Zend_Auth::getInstance()->clearIdentity();
		        }else if($company_flag == false){
					$this->view->error=array('company'=>'0');            	
	                $form->populate($formData);	
	                Zend_Auth::getInstance()->clearIdentity();
		        }
		        else{
		        	
	            	$this->view->error=array('save'=>'0');            	
	                $form->populate($formData);	
	                Zend_Auth::getInstance()->clearIdentity();	        	
		        }  
		          			
    		}else{
    			
            	$this->view->error=$form->getMessages();            	
                $form->populate($formData);
    		}                
        }
        
        // Retreving messages
    	$message = $this->_helper->flashMessenger->getMessages();
    	$this->view->message=implode('<br>',$message);
    	$this->_helper->flashMessenger->clearCurrentMessages();
    	$this->view->form = $form;
    }

     /**     
     * Registration form
     */   
    public function logoutAction()
    {
     
		$Auth = new Ve_Auth_Auth();		
			Zend_Session::namespaceUnset("storage_data");
			Zend_Session::namespaceUnset("page_variables");
			Zend_Session::namespaceUnset("page_data");
			
			
			$Auth->doLogout();		
			//echo 'dddddd'; die;
		
		$this->_helper->_redirector->gotoUrl();
		
    }
    
    
    public function forgetpasswordAction() {
        
        $forgetDetail = array(
            "email"     => "",
            "message"   => ""
        );
        $message = "";
        if($this->getRequest()->getMethod() == "POST"){
			
            $data = $this->getRequest()->getPost();
            $userModel = new Model_User();
            $userDetail = $userModel->isUserExist($data['email']);
            if(!empty($userDetail)) { 
                $this->sendResetInstruction($userDetail);
				 $forgetDetail = array(
                    "email"     => $data['email'],
                    "success"   => "Successfully sent mail to your email.",
                );
				if($forgetDetail['success'] != "") 
				{
					$this->view->forgetDetail =  $forgetDetail;
				} 
            } else {
                 $forgetDetail = array(
                    "email"     => $data['email'],
                    "message"   => "Invalid email address or user not exist !!!",
                );
				if($forgetDetail['message'] != "") 
				{
					$this->view->forgetDetail =  $forgetDetail;
				} 
            }   
			//echo "<pre>";
			//print_r($forgetDetail);
			//exit;
				
        }
    }
    public function sendResetInstruction($userDetail) {
        try {         
    
            // create view object
            $emial_template = new Zend_View();
            $emial_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/email/');
            
            // assign valeues
            $passkey = uniqid();
            $emial_template->assign('firstname', $userDetail[0]['firstName']);
            $emial_template->assign('lastname', $userDetail[0]['lastName']);
            $emial_template->assign('email', $userDetail[0]['email']);
            $emial_template->assign('passkey', $passkey);   
             $bodyText = $emial_template->render('resetPassword.phtml');   
            // create mail object
            $mail = new Zend_Mail();
			
            // render view
            
			//echo $bodyText;exit;
						
            // configure base stuff
            $setModel = new Model_Setting();
		    $setData = $setModel->getSetting();
		    if($setData){
					$setting = $setData[0];
					$mail->setFrom($setting['from_email'],$setting['from_name']);
					if($setting['bcc_email'])
					$mail->addBcc($setting['bcc_email'], $setting['bcc_name']);					
			}else{
				$mail->setFrom('support@visionworkorders.com','Vision Work Order');
			}
  		    $mail->addTo($userDetail[0]['email'], $userDetail[0]['email']);
            $mail->setSubject('Reset password instruction');
            $mail->setBodyHtml($bodyText)->setBodyText($bodyText);
			$mail->addHeader('Content-Type', 'text/html; charset=utf-8');
			$mail->send();

		//  $mail->send();
            
            // set user pass key
            $userModel = new Model_User();
            $userModel->setPasskey($passkey, $userDetail[0]['uid']);
        } catch (Exception $e) {
            echo $e->getMessage(); die;
        }
    }
	// Reset Password
	 public function resetpasswordAction() {        
		$show_form_status = true;
		$show_login_url = false;
		$passkey = $this->_request->getParam('id');		
        $forgetDetail = array(
            "reset"     => "",
            "message"   => ""
        );
        $message = "";
        $warn_msg = '';
        if(!empty($passkey)){
			$userModel = new Model_User();
			$userDetails = $userModel->passkey($passkey);
			if($userDetails != false){
				//print_r($userDetails);
				$userData = $userDetails[0];
				$passkeyStatus = $userData['passkeyStatus'];			
				 $time1 = strtotime($userData['passkeyTime']);
				 $time2 = strtotime(date("Y-m-d h:i:s"));
				 $timeDiff = abs(($time2-$time1)/86400);
				if($passkeyStatus==0)
				  {
					  $warn_msg = 'Unauthorize page have open.';
					  $show_form_status = false;
				  }
				  else if($timeDiff>1){
					  $warn_msg = 'Your action has been expired for reset password.';
					  $show_form_status = false;
				  }
				
			}else{
				$warn_msg = 'Key has been expired.';
				$show_form_status = false;
			}
			}else{
				$warn_msg = 'Invalid Page URL.';
				$show_form_status = false;
			}				
		if($this->getRequest()->getMethod() == "POST")
		{	
			try{					
				$data = $this->getRequest()->getPost();					
				$userDetail = $userModel->updateresetpassword(md5($data['repwd']),$passkey);				
				$userDetails = $userModel->passkey($passkey);
				$message = "Your password have reset successfully.";
				$show_form_status = false;
				$show_login_url = true;
			}catch(Exception $e){
				$warn_msg = "There are some problem to reset the password.";
			}
			
			
	    }
	    
	    $this->view->login_url = $show_login_url;
	    $this->view->message = $message;
	    $this->view->warn_msg = $warn_msg;
	    $this->view->form_status = $show_form_status;
				
    }
	///
	
	 public function sendUpdateResetInstruction($userDetail,$password) 
	 {
        try 
		{         			
            // create view object
            $emial_template = new Zend_View();
            $emial_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/email/');            
            // assign valeues
            $passkey = uniqid();
            $emial_template->assign('firstname', $userDetail[0]['firstName']);
            $emial_template->assign('lastname', $userDetail[0]['lastName']);
            $emial_template->assign('email', $userDetail[0]['email']);
            $emial_template->assign('passkey', $passkey);  
			$emial_template->assign('password', $password);                  
            // create mail object
            $mail = new Zend_Mail('utf-8');
            // render view
            $bodyText = $emial_template->render('updatePassword.phtml');					
            // configure base stuff
            $mail->addTo($userDetail[0]['email'], $userDetail[0]['email']);
            
            $mail->setSubject('Update Password Instruction');
            
            $setModel = new Model_Setting();
		    $setData = $setModel->getSetting();
		    if($setData){
					$setting = $setData[0];
					$mail->setFrom($setting['from_email'],$setting['from_name']);					
			}else{
				$mail->setFrom('support@visionworkorders.com','Vision Work Order');
			}
            
            $mail->setBodyHtml($bodyText);
            $mail->send();            
            // set user pass key
            $userModel = new Model_User();
            $userModel->setPasskey($passkey, $userDetail[0]['uid']);
            return true;
        } 
		catch (Exception $e) 
		{
            return $e->getMessage();
        }
    }  
}

