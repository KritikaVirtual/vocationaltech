<?php

/**
 * Description of Priority
 *
 * @author ivtidai
 */
class Model_Requestform extends Zend_Db_Table_Abstract {

    protected $_name = 'form_request';
    protected $_tab_role = 'form_request';
    public $_errorMessage = '';

    public function insertreData($crdata) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($crdata)) {
            try {
                $insert = $db->insert('form_request', $crdata);
                $id = $db->lastInsertId();
                $emailData = 'Hi,<br><br><table width="100%">'
                        . '<tr><td colspan="2"> '.$crdata['form_name'].' Details</td></tr>'
                        . '<tr><td>Name</td><td>'.$crdata['name'].'</td></tr>'
                        . '<tr><td>Email</td><td>'.$crdata['email'].'</td></tr>'
                        . '<tr><td>Telephone</td><td>'.$crdata['telephone'].'</td></tr>'
                        . '<tr> <td>Company</td><td>'.$crdata['company'].'</td></tr>'
                        . '<tr><td>Comments</td><td>'.$crdata['question'].'</td></tr></table>';
                $setModel = new Model_Setting();
                $setData = $setModel->getSetting(); 
                if($crdata['form_name']=='contactus') 
                    $this->sendNotificationMail($setData[0]['contactus_email'],$setData[0]['contactus_name'],$emailData);
                else
                    $this->sendNotificationMail($setData[0]['support_email'],$setData[0]['support_name'],$emailData);
                return $id;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }
    
    
    public function sendNotificationMail($to, $subject, $ebody) {
        try {
            
            
            
            $mail = new Zend_Mail('utf-8');
            $mail->addHeader('X-greetingsTo', $to, true);
            $mail->addTo($to);
            $mail->setSubject($subject);
            $setModel = new Model_Setting();
            $setData = $setModel->getSetting();
            if ($setData) {
                $setting = $setData[0];
                $mail->setFrom($setting['from_email'], $setting['from_name']);
                $return_path = new Zend_Mail_Transport_Sendmail('-f' . $setting['from_email']);
                if ($setting['bcc_email'])
                    $mail->addBcc($setting['bcc_email'], $setting['bcc_name']);
            }else {
                $mail->setFrom($to, $subject);
                $return_path = new Zend_Mail_Transport_Sendmail($to);
            }
            Zend_Mail::setDefaultTransport($return_path);
            $mail->setBodyHtml($ebody);
            if ($mail->send()) {
                //$this->saveEmailLog($suId, $tuId, $to, $subject, true);
                //return true;
            } else {
                //$this->saveEmailLog($suId, $tuId, $to, $subject, false);
                //return false;
            }
        } catch (Exception $e) {
            //$this->saveEmailLog($suId, $tuId, $to, $subject, false);
        }
    }
    
    
    
}
