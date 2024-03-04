<?php
class VE_General_Staticmethods
{

	public static function sendMail($subject,$body,$from,$to,$cc=array(),$bcc=array()){
		$config = array(
			    'port' => SMTP_PORT,
			    'auth' => SMTP_AUTH,
			    'username' => SMTP_USER_NAME,								 
			    'password' => SMTP_PASSWORD
			);		
		$smtpConnection = new Zend_Mail_Transport_Smtp(SMTP_MAIL, $config);
		$mail = new Zend_Mail();
	    $mail->setBodyHtml($body);
	    if(isset($from[0])){
	    	$mail->setFrom($from[0],$from[1]);
	    }else{
	    	$mail->setFrom($from);
	    }
	    $mail->addTo($to);
	    if(count($cc)>0) $mail->addCc($cc);
	    if(count($bcc)>0) $mail->addBcc($cc);
	    $mail->setSubject($subject);
	    return ($mail->send($smtpConnection))?true : false;
		
	}	
	 
 
	
}

?>
