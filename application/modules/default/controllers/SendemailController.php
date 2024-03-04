<?php
require_once 'CompleteController.php';
class SendemailController extends CompleteController {

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
        $this->accessHelper = $this->_helper->access;
        $this->closewo_location = 4;
    }
   
    // Call befor any action and check is user login or not
    public function preDispatch() {
           try {         
    
            // create view object
            $emial_template = new Zend_View();
            $emial_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/email/');
            // assign valeues
			if($_POST['passkey']!='' && $_POST['passkeyStatus']==1 ) {
				$passkey=$_POST['passkey']; 
			} else { 
				$passkey = uniqid(); 
			}
            $emial_template->assign('firstname', $_POST['firstName']);
            $emial_template->assign('lastname', $_POST['lastName']);
            $emial_template->assign('email', $_POST['email']);
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
  		    $mail->addTo($_POST['email'], $_POST['email']);
            $mail->setSubject('Reset password instruction');
            $mail->setBodyHtml($bodyText)->setBodyText($bodyText);
			$mail->addHeader('Content-Type', 'text/html; charset=utf-8');
			$mail->send();

		//  $mail->send();
            
            // set user pass key
            $userModel = new Model_User();
            $userModel->setPasskey($passkey, $_POST['uid']);
        } catch (Exception $e) {
            echo $e->getMessage(); die;
        }
    }
	// Reset Password
}