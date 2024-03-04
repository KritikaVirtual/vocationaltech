<?php

   $to = "haseebalam@virtualemployee.com";
   $subject = "This is subject";
   
//$message = '<html><body>';   
$message = '<p><strong>This is strong text</strong> while this is not.</p>';
//$message = '</body></html>';

$headers = "From: test@visionworkorders.com\r\n";
$headers .= "Reply-To: test@visionworkorders.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

//   $header .= "BCC: prabhat@virtualemployee.com";
   
/*$currentFilePath = dirname(realpath(__FILE__));
set_include_path($currentFilePath . '/library/'  . PATH_SEPARATOR . get_include_path());
require_once 'Zend/Mail.php';

$mail = new Zend_Mail(); 
$mail->addTo($to, 'haseeb'); 
$mail->setFrom('test@visionworkorders.com', 'Test'); 
$mail->setSubject('Hey you!'); 
$mail->setBodyText('<p>Hello!</p>'); */
//$mail->send(); 
   try {
    $retval = mail($to,$subject,$message,$headers);
    echo "Message sent!<br />\n";
} catch (Exception $ex) {
    echo "Failed to send mail! " . $ex->getMessage() . "<br />\n";
}
?>
