<?php

class IndexController extends Ve_Controller_Base {

    private $userId = '';

    // Call befor any action and check is user login or not
    //echo "sadasd";
    //die;
    public function preDispatch() {

        parent::preDispatch();

        if (Zend_Auth::getInstance()->hasIdentity()) {
            if ('logout' != $this->getRequest()->getActionName()) {
                $this->_helper->_redirector->gotoUrl('/dashboard');
            }
        } else {
            if ('logout' == $this->getRequest()->getActionName()) {
                /* $this->_flashMessenger->addMessage(array('success'=>'You have successfully logged out!')); */
                $this->_helper->_redirector->gotoUrl('/index');
            }
        }
    }

    public function changeaccountredirectAction($user_id, $redirecturl) {
        $adminNamespace = new Zend_Session_Namespace('Admin_User');

        //$user_id = $post['company_account'];
        if ($user_id == 0 && $adminNamespace->role_id == 1) {
            $user_id = $adminNamespace->user_id;
        }
        $userModel = new Model_User();
        $userData = $userModel->getUserById($user_id);
        $userDetails = $userData[0];
        Zend_Auth::getInstance()->getStorage()->write($userDetails);
        $this->_redirect($redirecturl);

        $userMod = new Model_User();
        $companyUser = $userMod->getCompanyAdminUser();
        $this->view->user_role = $adminNamespace->role_id;
        $this->view->user_id = $this->userId;
        $this->view->companyUser = $companyUser;
        exit(0);
    }

    public function indexAction() {

        $redirect = new Zend_Session_Namespace('redirect_data');

        $form = new Form_Loginform();
        $request = $this->getRequest();

        // Check if we have a POST request
        if ($this->_request->isPost()) {

            // Get our form and validate it
            $formData = $this->_request->getPost();

            if (!empty($formData['remember'])) {
                setcookie('user_id', $formData['email'], time() + (10 * 365 * 24 * 60 * 60), '/');
                setcookie('password', base64_encode($formData['password']), time() + (10 * 365 * 24 * 60 * 60), '/');
                setcookie('remember', $formData['remember'], time() + (10 * 365 * 24 * 60 * 60), '/');
                // echo $_COOKIE['user_email_id'];
            } else {
                setcookie('user_id', "", time() + (10 * 365 * 24 * 60 * 60), '/');
                setcookie('password', "", time() + (10 * 365 * 24 * 60 * 60), '/');
                setcookie('remember', "", time() + (10 * 365 * 24 * 60 * 60), '/');
            }


            if ($form->isValid($formData)) {

                $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
                $authAdapter->setTableName('users')
                        ->setIdentityColumn('userName')
                        ->setCredentialColumn('password');
                // ->setCredentialColumn('status');
                // pass to the adapter the submitted username and password

                $authAdapter->setIdentity($formData['email'])
                        ->setCredential(md5($formData['password']));
                // ->setCredential('1');				



                $auth = Zend_Auth::getInstance();

                $result = $auth->authenticate($authAdapter);

                $userInfo = $authAdapter->getResultRowObject(null, 'password');

                $accModel = new Model_Account();
                $cplist = $accModel->getcompany();
                $cp_array = array();
                foreach ($cplist as $cp) {
                    if ($cp['status'] == '1') {
                        $cp_array[] = $cp['cust_id'];
                    }
                }

                $company_flag = true;
                if ($userInfo) {
                    if (!empty($userInfo->cust_id) && !in_array($userInfo->cust_id, $cp_array)) {
                        $company_flag = false;
                    }
                    if ($company_flag && ($userInfo->role_id == '5' || $userInfo->role_id == '7')) {
                        $tenantModel = new Model_Tenant();
                        $tdata = $tenantModel->getTenantByUid($userInfo->uid);
                        $tenantData = $tdata[0];
                        if ($tenantData->status == '0')
                            $company_flag = false;
                    }
                }

                if ($result->isValid() && $userInfo->status == '1' && $userInfo->remove_status == '0' && $company_flag) { // is the user a valid one?
                    //$userInfo = $authAdapter->getResultRowObject(null, 'password');			        		            						
                    $authStorage = $auth->getStorage(); // the default storage is a session with namespace Zend_Auth
                    $userModel = new Model_User();

                    $updateUser = $userModel->updateUser(array('login_session' => time(), "ip" => $_SERVER['REMOTE_ADDR']), $userInfo->uid);
                    $userData = $userModel->getUserById($userInfo->uid);
                    $authStorage->write($userData[0]);
                    $adminNamespace = new Zend_Session_Namespace('Admin_User');
                    $adminNamespace->role_id = $userInfo->role_id;
                    $adminNamespace->user_id = $userInfo->uid;

                    if ($redirect->redirecturl == 'redirect' && $adminNamespace->role_id == 1 && $redirect->role_id == 1) {
                        $this->changeaccountredirectAction($redirect->redirectUser, $redirect->http_referer);
                    } else if ($adminNamespace->role_id == $redirect->role_id) {
                        $this->_redirect($redirect->http_referer);
                    }
                    $redirect->role_id = $adminNamespace->role_id;
                    $this->_redirect('dashboard');
                } else if ($result->isValid() && $userInfo->status == '0' && $userInfo->remove_status == '0') {
                    $this->view->error = array('active' => '0');
                    $form->populate($formData);
                    Zend_Auth::getInstance()->clearIdentity();
                } else if ($result->isValid() && $userInfo->remove_status == '1') {
                    $this->view->error = array('remove' => '0');
                    $form->populate($formData);
                    Zend_Auth::getInstance()->clearIdentity();
                } else if ($company_flag == false) {
                    $this->view->error = array('company' => '0');
                    $form->populate($formData);
                    Zend_Auth::getInstance()->clearIdentity();
                } else {

                    $this->view->error = array('save' => '0');
                    $form->populate($formData);
                    Zend_Auth::getInstance()->clearIdentity();
                }
            } else {

                $this->view->error = $form->getMessages();
                $form->populate($formData);
            }
        } else {
            if (isset($_SERVER["HTTP_REFERER"])) {
                $redirect->http_referer = $_SERVER["HTTP_REFERER"];
            }
        }

        // Retreving messages
        $message = $this->_helper->flashMessenger->getMessages();
        $this->view->message = implode('<br>', $message);
        $this->_helper->flashMessenger->clearCurrentMessages();
        $this->view->form = $form;
        // $form->setDefaults(array('email' => 'somevalue','password'=>"sanjay"));
    }

    /**
     * Registration form
     */
    /*
     * website Support
     */
    public function supportAction() {
        
        // And here's our captcha object...  
        $captcha = new Zend_Form_Element_Captcha('captcha', array('label' => 'Write the chars to the field',
            'captcha' => array(// Here comes the magic...  
                // First the type... 

                'captcha' => 'Image',
                'useNumbers' => true,
                'fontSize' => '24',
                'wordLen' => '5',
                'height' => '57',
                'width' => '235',
                // Captcha timeout, 5 mins  
                'timeout' => 300,
                // What font to use...  
                'font' => APPLICATION_PATH . '/../public/fonts/bebasneue.ttf',
                // Where to put the image  
                'imgDir' => APPLICATION_PATH . '/../public/captcha/',
                // URL to the images  
                // This was bogus, here's how it should be... Sorry again :S  
                'imgUrl' =>  BASEURL . 'public/captcha/',
        ),'autocomplete' =>'off'));

        $this->view->captcha = $captcha;
    }
    
    public function supportajaxAction(){
        // Check if we have a POST request
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            //print_r($formData);
            $captchaId = $formData['captcha_id'];
            $captchaInput = $formData['captcha_input'];
            $mysession = new Zend_Session_Namespace('mysession');
            $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $captchaId);
            // To access what's inside the session, we need the Iterator
            // So we get one...
            $captchaIterator = $captchaSession->getIterator();
            if(count($captchaIterator)>0){
                $captchaWord = $mysession->inputword = $captchaIterator['word'];
                              
            }else {
                $captchaWord = $mysession->inputword;  
            } 

            if ($captchaWord == strtolower($captchaInput)) {
                // Get our form and validate it
                unset($formData['captcha_id']);
                unset($formData['captcha_input']);
                unset($mysession->inputword);
                $request = new Model_Requestform();
                $request->insertreData($formData);
                $emailModel = new Model_Email();
                $emailData = $emailModel->loadEmailTemplate(53, "", "", "");
                $subject = $emailData[0]['email_subject'];
                $emailBody = $emailData[0]['email_content'];
                $eBody = $this->getbodyContent($emailBody, $formData, $subject);
                $email = $request->sendNotificationMail($formData['email'], $subject, $eBody);
                echo 'true';
            } else {
                echo 'false';
                
            }
        }
        exit();
    }

    public function contactusAction() {
        
        // And here's our captcha object...  
        $captcha = new Zend_Form_Element_Captcha('captcha', array('label' => 'Write the chars to the field',
            'captcha' => array(// Here comes the magic...  
                // First the type... 

                'captcha' => 'Image',
                'useNumbers' => true,
                'fontSize' => '24',
                'wordLen' => '5',
                'height' => '57',
                'width' => '235',                
                // Captcha timeout, 5 mins  
                'timeout' => 300,
                
                // What font to use...  
                'font' => APPLICATION_PATH . '/../public/fonts/bebasneue.ttf',
                // Where to put the image  
                'imgDir' => APPLICATION_PATH . '/../public/captcha/',
                // URL to the images  
                // This was bogus, here's how it should be... Sorry again :S  
                'imgUrl' =>  BASEURL . 'public/captcha/',
        ),'autocomplete' =>'off'));

        $this->view->captcha = $captcha;
        // Check if we have a POST request        
        
    }
    
    public function contactusajaxAction(){
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            //print_r($formData);
            $captchaId = $formData['captcha_id'];
            $captchaInput = $formData['captcha_input'];
            $mysession = new Zend_Session_Namespace('mysession');
            $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $captchaId);
            
            // To access what's inside the session, we need the Iterator
            // So we get one...
            $captchaIterator = $captchaSession->getIterator();
                        
            if(count($captchaIterator)>0){
                $captchaWord = $mysession->inputword = $captchaIterator['word'];
                              
            }else {
                $captchaWord = $mysession->inputword;  
            }            

            if ($captchaWord == strtolower($captchaInput)) {
                // Get our form and validate it
                unset($formData['captcha_id']);
                unset($formData['captcha_input']);
                unset($mysession->inputword);
                $request = new Model_Requestform();
                $request->insertreData($formData);
                $emailModel = new Model_Email();
                $emailData = $emailModel->loadEmailTemplate(54, "", "", "");
                $subject = $emailData[0]['email_subject'];
                //$ebody = $emailData[0]['email_content'];
                $emailBody = $emailData[0]['email_content'];
                $eBody = $this->getbodyContent($emailBody, $formData, $subject);
                $email = $request->sendNotificationMail($formData['email'], $subject, $eBody);
                //$data = array('status' => 'success', 'content' => "");
                //echo json_encode($data);
                //unset($_SESSION['word']);
                echo 'true';
                
            } else {
                //$data = array('status' => 'failure', 'content' => "");
                //echo json_encode($data);
                echo 'false';
                
            }
        }
        exit();
              
    }  
    

    public function getbodyContent($emailBody, $formData, $subject) {

        $footer_data = $this->getFooterData();
        //// email conatct variables 
        $uri = BASEURL;
        /*         * *****Get voc-tech logo******* */
        $sdModel = new Model_SystemDefault();
        $sdData = $sdModel->getSystemDefault();
        $emailContent = $sdData[0];
        $voc_logo = $emailContent['voc_logo'];

        $voctech_company_logo = '<img src="' . $uri . 'public/images/vt_logo.png" style="width: 240px !important;padding-right: 12px;">';

        if (isset($voc_logo) && !empty($voc_logo)) {
            $voctech_logo_src = '<img src="' . $uri . 'public/images/uploads/' . $voc_logo . '">';
        } else {
            $voctech_logo_src = "";
        }
        ///// header
        $emailBody = str_replace('[[++voctechLogo]]', $voctech_logo_src, $emailBody);
        $emailBody = str_replace('[[++dateTime]]', date("Y-m-d"), $emailBody);
        ///// end header
        //
            ///// Footer
        $emailBody = str_replace('[[++footerInfo]]', $footer_data['footer_info'], $emailBody);
        $emailBody = str_replace('[[++footerSubject]]', $subject, $emailBody);
        $emailBody = str_replace('[[++companyLogo]]', $voctech_company_logo, $emailBody);
        $emailBody = str_replace('[[++custName]]', $formData['name'], $emailBody);
        $emailBody = str_replace('[[++custCompany]]', $formData['company'], $emailBody);
        $emailBody = str_replace('[[++custEmail]]', $formData['email'], $emailBody);
        $emailBody = str_replace('[[++custTelephone]]', $formData['telephone'], $emailBody);
        $emailBody = str_replace('[[++custComments]]', $formData['question'], $emailBody);

        return $emailBody;
    }

    public function getFooterData() {
        $uri = BASEURL;
        /*         * *****Get voc-tech logo******* */
        $sdModel = new Model_SystemDefault();
        $sdData = $sdModel->getSystemDefault();
        $emailContent = $sdData[0];
        $footer_info = $emailContent['footer_info'];
        $emailSubject = $emailContent['subject'];

        $data['footer_info'] = $footer_info;
        //$data['subject']		=	$emailSubject;
        return $data;
    }

    public function logoutAction() {
        $redirect = $this->_getParam('redirect', '');
        $Auth = new Ve_Auth_Auth();
        Zend_Session::namespaceUnset("storage_data");
        Zend_Session::namespaceUnset("page_variables");
        Zend_Session::namespaceUnset("page_data");
        if ($redirect == 'redirect') {
            Zend_Session::namespaceUnset("redirect_data");
        }
        $userModel = new Model_User();
        $uid = $_SESSION['Admin_User']['user_id'];
        $updateUser = $userModel->updateUser(array("ip" => ""), $uid);


        $Auth->doLogout();
        //echo 'dddddd'; die;

        $this->_helper->_redirector->gotoUrl();
    }

    public function forgetpasswordAction() {

        $forgetDetail = array(
            "email" => "",
            "message" => ""
        );
        $message = "";
        if ($this->getRequest()->getMethod() == "POST") {

            $data = $this->getRequest()->getPost();

            $userModel = new Model_User();
            $userDetail = $userModel->isUserExist($data['email']);
            if (!empty($userDetail)) {
                $this->sendResetInstruction($userDetail);
                $forgetDetail = array(
                    "email" => $data['email'],
                    "success" => "Successfully sent mail to your email.",
                );
                if ($forgetDetail['success'] != "") {
                    $this->view->forgetDetail = $forgetDetail;
                }
            } else {
                $forgetDetail = array(
                    "email" => $data['email'],
                    "message" => "Invalid email address or user not exist !!!",
                );
                if ($forgetDetail['message'] != "") {
                    $this->view->forgetDetail = $forgetDetail;
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
            if ($userDetail[0]['passkey'] != '' && $userDetail[0]['passkeyStatus'] == 1) {
                $passkey = $userDetail[0]['passkey'];
            } else {
                $passkey = uniqid();
            }
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
            if ($setData) {
                $setting = $setData[0];
                $mail->setFrom($setting['from_email'], $setting['from_name']);
                if ($setting['bcc_email'])
                    $mail->addBcc($setting['bcc_email'], $setting['bcc_name']);
            }else {
                $mail->setFrom('support@visionworkorders.com', 'Vision Work Order');
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
            echo $e->getMessage();
            die;
        }
    }

    // Reset Password
    public function resetpasswordAction() {
        $show_form_status = true;
        $show_login_url = false;
        $passkey = $this->_request->getParam('id');
        $forgetDetail = array(
            "reset" => "",
            "message" => ""
        );
        $message = "";
        $warn_msg = '';
        if (!empty($passkey)) {
            $userModel = new Model_User();
            $userDetails = $userModel->passkey($passkey);
            if ($userDetails != false) {
                //print_r($userDetails);
                $userData = $userDetails[0];
                $passkeyStatus = $userData['passkeyStatus'];
                $time1 = strtotime($userData['passkeyTime']);
                $time2 = strtotime(date("Y-m-d h:i:s"));
                $timeDiff = abs(($time2 - $time1) / 86400);
                if ($passkeyStatus == 0) {
                    $warn_msg = 'Unauthorize page have open.';
                    $show_form_status = false;
                } else if ($timeDiff > 1) {
                    $warn_msg = 'Your action has been expired for reset password.';
                    $show_form_status = false;
                }
            } else {
                $warn_msg = 'Key has been expired.';
                $show_form_status = false;
            }
        } else {
            $warn_msg = 'Invalid Page URL.';
            $show_form_status = false;
        }
        if ($this->getRequest()->getMethod() == "POST") {
            try {
                $data = $this->getRequest()->getPost();
                $userDetail = $userModel->updateresetpassword(md5($data['repwd']), $passkey);
                $userDetails = $userModel->passkey($passkey);
                $message = "Your password have reset successfully.";
                $show_form_status = false;
                $show_login_url = true;
            } catch (Exception $e) {
                $warn_msg = "There are some problem to reset the password.";
            }
        }

        $this->view->login_url = $show_login_url;
        $this->view->message = $message;
        $this->view->warn_msg = $warn_msg;
        $this->view->form_status = $show_form_status;
    }

    ///

    public function sendUpdateResetInstruction($userDetail, $password) {
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
            if ($setData) {
                $setting = $setData[0];
                $mail->setFrom($setting['from_email'], $setting['from_name']);
            } else {
                $mail->setFrom('support@visionworkorders.com', 'Vision Work Order');
            }

            $mail->setBodyHtml($bodyText);
            $mail->send();
            // set user pass key
            $userModel = new Model_User();
            $userModel->setPasskey($passkey, $userDetail[0]['uid']);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}
