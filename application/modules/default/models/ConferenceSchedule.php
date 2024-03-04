<?php
/**
 * Description of Priority
 *
 * @author ivtidai
 */
class Model_ConferenceSchedule extends Zend_Db_Table_Abstract {

    protected $_name = 'conference_schedule';
    protected $_tab_role = 'conference_schedule';
    public $_errorMessage = '';

    public function insertCSchedule($data) {
        try {
            return $this->insert($data);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
            return false;
        }
    }

    public function getCScheduleByBid($building_id = '', $search_array = array(),$status='') {
        try {

            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('cs' => 'conference_schedule'))
                    ->joinLeft(array('wd' => 'week_days'), 'cs.week_days_id = wd.wdID', array('wdID' => 'wd.wdID', 'schedule_title' => 'wd.title'));
            if ($building_id != '') {
                $select = $select->where('cs.building_id=?', $building_id);
            }
            if (isset($search_array['schedule_name']) && $search_array['schedule_name'] != '') {
                $select = $select->where("cs.schedule_name LIKE '" . addslashes($search_array['schedule_name']) . "%'");
            }
            if ($status != '') {
                $select = $select->where('cs.status=?', 1);
            }
           $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function getCScheduleBySid($sid = '') {
        try {
            $select = $this->select();
            if ($sid != '') {
                $select = $select->where('id=?', $sid);
            }

            $res = $this->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res->toArray() : false;
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function updateCSchedule($data, $sid = '') {
        $this->_errorMessage = "";
        try {
            if (isset($sid) && !empty($sid)) {
                $where = $this->getAdapter()->quoteInto('id = ?', $sid);
                $this->update($data, $where);
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function deleteCSchedule($sid) {

        if (!empty($sid) && !empty($sid)) {
            $condition = array(
                'id = ' . $sid
            );
            try {
                $this->delete($condition);
                return true;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
        return false;
    }

    public function getCPlan() {
        $db = Zend_Db_Table::getDefaultAdapter();
        try {
            $select = $db->select()
                    ->from(array('cp' => 'croom_plan'));
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function insertcrData($crdata) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($crdata)) {
            try {
                $insert = $db->insert('conference_room', $crdata);
                $id = $db->lastInsertId();
                return $id;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }

    public function insertcrAccess($craccess) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($craccess)) {
            try {
                $insert = $db->insert('confroom_building_access', $craccess);
                $id = $db->lastInsertId();
                return $id;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }
    
    function get_recurrence_access($rid){
        $db = Zend_Db_Table::getDefaultAdapter();
        try {
            $select = $db->select()
                    ->from(array('cp' => 'confroom_building_access'));
            $select = $select->where('room_id=?', $rid);
            $res = $db->fetchAll($select);
            
            return ($res && sizeof($res) > 0) ? $res : false;
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function insertcrRateSch($crrateData) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($crrateData)) {
            try {
                $insert = $db->insert('croom_rate_schedule', $crrateData);
                $id = $db->lastInsertId();
                return $id;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }

    public function insertcrLayout($crlayoutData) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($crlayoutData)) {
            try {
                $insert = $db->insert('croom_design', $crlayoutData);
                $id = $db->lastInsertId();
                return $id;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }

    public function getcrDetailsByBid($building_id, $search_array = array(), $status = '') {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($building_id != '') {
            $select = $db->select()
                    ->from(array('cr' => 'conference_room'))
                    ->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    ->where('building_id=?', $building_id);
            if ($status != '') {
                $select = $select->where('cr.status=?', 1);
            }
            if (isset($search_array['room_name']) && $search_array['room_name'] != '') {
                $select = $select->where("cr.room_name LIKE '" . addslashes($search_array['room_name']) . "%'");
            }
            if (isset($search_array['location']) && $search_array['location'] != '') {
                $select = $select->where("cba.location LIKE '" . $search_array['location'] . "%'");
            }
            $select = $select->order(array('cr.room_name ASC'));

            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }

    public function getcrDetailsByBidTuser($building_id, $search_array = array(), $status = '') {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($building_id != '') {
            $select = $db->select()
                    ->from(array('cr' => 'conference_room'))
                    ->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    ->where('building_id=?', $building_id)
                    ->where('cba.tenant_user=?', 1);
            if ($status != '') {
                $select = $select->where('cr.status=?', 1);
            }
            if (isset($search_array['room_name']) && $search_array['room_name'] != '') {
                $select = $select->where("cr.room_name LIKE '" . addslashes($search_array['room_name']) . "%'");
            }
            if (isset($search_array['location']) && $search_array['location'] != '') {
                $select = $select->where("cba.location LIKE '" . $search_array['location'] . "%'");
            }
            $select = $select->order(array('cr.room_name ASC'));
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }

    public function checkTenantConfRoomByBuidingId($building_id,$role_id){
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($building_id != '') {
            $select = $db->select()
                    ->from(array('cr' => 'conference_room'))
                    ->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    ->where('building_id=?', $building_id);
                if($role_id==7)
                $select->where('cba.tenant_user=?', 1);   
                if($role_id==5)
                $select->where('cba.tenant_admin=?', 1);         
                $sql = $select->__toString();
                //echo "$sql\n";

                
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? sizeof($res) : 0;
        }
        return false;
    }

   

    public function getcrvaliddays($building_id, $date) {
        $days=date("w",strtotime($date));

        $ids="";
        switch($days){
            case 0:
               $ids='1,3,12';
               break;
            case 1:
               $ids='1,2,4,6';
               break;
           case 2:
               $ids='1,2,5,7';
               break;
           case 3:
               $ids='1,2,4,8';
               break;
           case 4:
               $ids='1,2,5,9';
               break;
           case 5:
               $ids='1,2,4,10';
               break;
           case 6:
               $ids='1,3,11';
               break;

        }
        $role=$_SESSION['Admin_User']['role_id'];
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($building_id != '') {
            if($role==7){
               $select = $db->select()
                    ->from(array('cr' => 'conference_room'))
                    ->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    ->joinInner(array('cs' => 'conference_schedule'), 'cs.id = cba.schedule_id')
                    ->where('cba.building_id='.$building_id.' AND cba.tenant_user=1 AND cr.status=1 AND  cs.week_days_id IN('.$ids.') ');
            }else if($role==5){
               $select = $db->select()
                    ->from(array('cr' => 'conference_room'))
                    ->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    ->joinInner(array('cs' => 'conference_schedule'), 'cs.id = cba.schedule_id')
                    ->where('cba.building_id='.$building_id.' AND cba.tenant_admin=1 AND cr.status=1 AND cs.week_days_id IN('.$ids.') ');
            }else{
            $select = $db->select()
                    ->from(array('cr' => 'conference_room'))
                    ->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    ->joinInner(array('cs' => 'conference_schedule'), 'cs.id = cba.schedule_id')
                    ->where('cba.building_id='.$building_id.' AND cr.status=1 AND cs.week_days_id IN('.$ids.') ');
            }
//if ($status != '') {
                //$select = $select->where('cr.status=?', 1);
            //}
           // echo $select;
            //die;
            $select = $select->order(array('cr.room_name ASC'));
            $res = $db->fetchAll($select);

            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }
// get room avaliablity day in a week
    public function getroomavaliabledays($rid){
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($rid != '') {

            $select = $db->select()
                    ->from(array('cba' => 'confroom_building_access'))
                    ->joinInner(array('cs' => 'conference_schedule'), 'cba.schedule_id = cs.id')
                    ->joinLeft(array('wd' => 'week_days'), 'cs.week_days_id = wd.wdID')
                    ->where('cba.room_id=?', $rid);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }
    
    public function getcrDetails($cid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($cid != '') {

            $select = $db->select()
                    ->from(array('cr' => 'conference_room'))
                    ->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    // ->joinLeft(array('cd' => 'croom_design'), 'cd.room_id = cr.cid')
                    // ->joinLeft(array('crs' => 'croom_rate_schedule'), 'crs.room_id = cr.cid')
                    ->where('cr.cid=?', $cid);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }
    
    public function getcrDetailsData($cid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($cid != '') {

            $select = $db->select()
                    ->from(array('cr' => 'conference_room'))
                    ->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    // ->joinLeft(array('cd' => 'croom_design'), 'cd.room_id = cr.cid')
                    // ->joinLeft(array('crs' => 'croom_rate_schedule'), 'crs.room_id = cr.cid')
                    ->where('cr.cid IN("'.$cid.'")');
            //echo $select;
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }
    
     public function getcrDetailsuseraccess($rid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($rid != '') {

            $select = $db->select()
                    ->from(array('cr' => 'confroom_building_access'))
                    //->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    // ->joinLeft(array('cd' => 'croom_design'), 'cd.room_id = cr.cid')
                    // ->joinLeft(array('crs' => 'croom_rate_schedule'), 'crs.room_id = cr.cid')
                    ->where('cr.room_id=?', $rid);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }

    public function getCrdata($csid){
        $db = Zend_Db_Table::getDefaultAdapter();

        if (!empty($csid)) {
            $select = $db->select()
                    ->from(array('cs' => 'conference_schedule'))
                    ->where('id=?',$csid);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }
    public function checkavaliablecr($chd) {
        $db = Zend_Db_Table::getDefaultAdapter();

        if (!empty($chd)) {
            $select = $db->select()
                    ->from(array('cr' => 'croom_request'))
                    ->joinInner(array('cba' => 'confroom_building_access'), 'cr.croom_id = cba.room_id')
                    //->where('wop.wo_status in ('.implode(",",$search_array['search_status']).')');
                    ->where('cr.croom_id in(' . $chd['croom_id'] . ') or FIND_IN_SET("' . $chd['croom_id'] . '",cba.multi_id)')
                    ->where('cr.requested_date=?', $chd['requested_date']);
                    //->where("((end_date >= '".$chd['requested_date']."' && requested_date <= '".$chd['requested_date']."') or (end_date >= '".$chd['requested_date']."' && requested_date <= '".$chd['requested_date']."') or (end_date <= '".$chd['requested_date']."' && requested_date >= '".$chd['requested_date']."'))");
            //echo $select;
            //die;
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }
     public function updatecheckavaliablecr($chd) {
        $db = Zend_Db_Table::getDefaultAdapter();

        if (!empty($chd)) {
            $select = $db->select()
                    ->from(array('cr' => 'croom_request'))
                    //->where('wop.wo_status in ('.implode(",",$search_array['search_status']).')');
                    ->where('cr.croom_id in(' . $chd['croom_id'] . ')')
                    ->where('cr.crid NOT in(' . $chd['crid'] . ')')
                    ->where('cr.requested_date=?', $chd['requested_date']);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }

    public function getcrRateSch($room_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($room_id != '') {
            $select = $db->select()
                    ->from(array('crs' => 'croom_rate_schedule'))
                    ->where('crs.room_id=?', $room_id);
                    //->where('plan in(1,2,3)');
            //$select = $select->order(array('id DESC'));
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }

     public function getcrRateSchdata($room_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($room_id != '') {
            $select = $db->select()
                    ->from(array('crs' => 'croom_rate_schedule'))
                    ->where('crs.room_id=?', $room_id)
                    ->where('plan in(1,2,3)');
            $select = $select->order(array('id DESC'));
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }

    public function getcrRateSchdatavalid($room_id,$plan) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($room_id != '') {
            $select = $db->select()
                    ->from(array('crs' => 'croom_rate_schedule'))
                    ->where('crs.room_id=?', $room_id)
                    ->where('plan in('.$plan.')');
            $select = $select->order(array('id DESC'));
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }
    public function getcrRateSchget($room_id,$plan) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($room_id != '') {
            $select = $db->select()
                    ->from(array('crs' => 'croom_rate_schedule'))
                    ->where('crs.room_id=?', $room_id)
                    ->where('plan=?',$plan);
            $select = $select->order(array('id DESC'));
            $select = $select->limit(1);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }



    public function getcrDesignLayout($room_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($room_id != '') {
            $select = $db->select()
                    ->from(array('cd' => 'croom_design'))
                    ->where('cd.room_id=?', $room_id);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }

    function getscheduletime($cid)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('cs' => 'conference_schedule'))
                    ->joinInner(array('cba' => 'confroom_building_access'), 'cs.id = cba.schedule_id')
                    // ->joinLeft(array('cd' => 'croom_design'), 'cd.room_id = cr.cid')
                    // ->joinLeft(array('crs' => 'croom_rate_schedule'), 'crs.room_id = cr.cid')
                    ->where('cba.room_id=?', $cid);
            //echo $select;
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
    }

    public function updatecrData($data, $cid) {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $condition = array('cid = ' . $cid);
            $db->update('conference_room', $data, $condition);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updatecrAccess($data, $room_id) {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $condition = array('room_id = ' . $room_id);
            $db->update('confroom_building_access', $data, $condition);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updatecrRateSch($data, $room_id) {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $condition = array('room_id = ' . $room_id);
            $db->delete('croom_rate_schedule', $condition);
            $db->insert('croom_rate_schedule', $data);
            //$db->update('croom_rate_schedule', $data, $condition);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deletecrRateSch($room_id) {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $condition = array('room_id = ' . $room_id);
            $db->delete('croom_rate_schedule', $condition);
            //$db->insert('croom_rate_schedule', $data);
            //$db->update('croom_rate_schedule', $data, $condition);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteAttachment($ids) {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $condition = array('d_id in (' . $ids . ')');

            $db->delete('croom_design', $condition);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteCRoom($cid) {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $condition1 = array('cid = ' . $cid);
            $db->delete('conference_room', $condition1);
            $condition2 = array('room_id =' . $cid);
            $db->delete('confroom_building_access', $condition2);
            $condition3 = array('room_id =' . $cid);
            $db->delete('croom_rate_schedule', $condition3);
            $condition4 = array('room_id = ' . $cid);
            $db->delete('croom_design', $condition4);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteCrRoom($crid) {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $condition1 = array('crid = ' . $crid);
            $db->delete('croom_request', $condition1);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function  sendConferenceEmail($accountUser, $conferenceData,$act="") {


        $sendEmail = array();
        $accountModel = new Model_Account();
        $accoundDetail = $accountModel->getCompanyByBuilding($conferenceData['building_id']);
        $accounData = (array) $accoundDetail[0];
        if ($accountUser != '') {
            $userModel = new Model_User();
            $acuserList = $userModel->getUserBySetIds($accountUser);
            foreach ($acuserList as $acuser) {
                $sendEmail[] = $acuser['email'];
                $userData['building_id'] = $conferenceData['building_id'];
                $userData['uid'] = $acuser['uid'];
                $htmlContent = $this->getHtmlContent(1, $userData, $accounData, $conferenceData,$userData['building_id'],$act);
               // print_r($htmlContent);
                //die;
                $this->sendNotificationMail($conferenceData['userId'], $acuser['uid'], $acuser['email'], $htmlContent['subject'], $htmlContent['content']);
            }
        }
    }

    public function getHtmlContent($send_as, $userData, $accounData, $conferenceData,$build_id="",$act="") {
        $htmlContent = '';
        if ($send_as == '1'){
            $htmlContent = $this->getHtmlDoc($userData, $accounData, $conferenceData,$build_id,$act);
        }
        return $htmlContent;
    }

    public function getHtmlDoc($userData, $accounData, $conferenceData,$build_id="",$act="") {
        $header_data = $this->getHeaderData($userData);
        $footer_data = $this->getFooterData();
        $emailMapper = new Model_Email();
        $role_id=$_SESSION['Admin_User']['role_id'];
        $emailt = $emailMapper->getCustomtemplate("", 9, $build_id);
 
        if(!empty($act) && $act=='delete'){
            $emailt = $emailMapper->getCustomtemplate("", 9, $build_id,1);
            if(!empty($emailt)){
                $htmlDocId = $emailt[0]['id'];
            }else{
                $htmlDocId = 42;
            }
        }else{
            $htmlDocId = 24;
                if($emailt) {
                    $htmlDocId = $emailt[0]['id'];
                }// email template id
        }
        $loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
        if ($loadTemplate) {
            $emailContent = $loadTemplate[0];
            $email_template_data['header_data'] = $header_data;
            $email_template_data['emailContent'] = $emailContent;
            $email_template_data['footer_data'] = $footer_data;
            $email_template_data['userData'] = $userData;
            $email_template_data['accounData'] = $accounData;
            $email_template_data['conferenceData'] = $conferenceData;
            $email_template_data['html_type'] = 1;
            $htmlContent = $this->getBodyData($email_template_data);
            return $htmlContent;
        }
    }



    public function getHeaderData($userData) {
        $uri = BASEURL;
        /*         * *****Get voc-tech logo******* */
        $sdModel = new Model_SystemDefault();
        $sdData = $sdModel->getSystemDefault();
        $emailContent = $sdData[0];
        $voc_logo = $emailContent['voc_logo'];

        if (isset($voc_logo) && !empty($voc_logo)) {
            $voctech_logo_src = '<img src="' . $uri . 'public/images/uploads/' . $voc_logo . '">';
        } else {
            $voctech_logo_src = "";
        }


        /*         * *****Get Company Data******* */
        $buildingModel = new Model_Building();
        $bm_data = $buildingModel->getbuildingbyid($userData['building_id']);

        $accModel = new Model_Account();
        $accData = $accModel->getcompany($bm_data[0]['cust_id']);
        $aData = $accData[0];

        $building_logo_src = "";

        // Company logo
        if (isset($aData['company_logo']) && !empty($aData['company_logo'])) {
            $building_logo_src = '<img src="' . $uri . 'public/images/clogo/' . $aData['company_logo'] . '">';
        } else {
            //$building_logo_src	=	'<img src="'.$uri.'/public/images/logo.png">';
            $building_logo_src = '';
        }

        $data['building_logo_src'] = $building_logo_src;
        $data['voctech_logo_src'] = $voctech_logo_src;
        $data['corp_account_number'] = $aData['corp_account_number'];
        $data['date'] = $this->getDateFormat();
        return $data;
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

    public function getDateFormat($data = null) {
        if ($data == null)
            $data = date("Y-m-d h:i:s");

        return date("Y-m-d h:i:s", strtotime($data));
    }

    public function getBodyData($email_template_data) {

        if (isset($email_template_data['header_data']))
            $header_data = $email_template_data['header_data'];
        if (isset($email_template_data['emailContent']))
            $emailContent = $email_template_data['emailContent'];
        if (isset($email_template_data['footer_data']))
            $footer_data = $email_template_data['footer_data'];
        if (isset($email_template_data['accounData']))
            $accounData = $email_template_data['accounData'];

        if (isset($email_template_data['html_type']))
            $html_type = $email_template_data['html_type'];

        if (isset($email_template_data['userData']))
            $userData = $email_template_data['userData'];

        if (isset($email_template_data['conferenceData']))
            $conferenceData = $email_template_data['conferenceData'];

        /*         * ****Get Building data***** */
        $buildingModel = new Model_Building();
        $buildData = $buildingModel->getbuildingbyid($userData['building_id']);
        $bData = $buildData[0];
        $emailSubject = $emailContent['email_subject'];
        ///// Billing Details
        $emailSubject = str_replace('[[++billState]]', $bData['billState'], $emailSubject);
        $emailSubject = str_replace('[[++billPostalCode]]', $bData['billPostalCode'], $emailSubject);
        ///// End Billing Details
        ///// Start email subject

        $emailSubject = str_replace('[[++companyName]]', $accounData['companyName'], $emailSubject);
        $emailSubject = str_replace('[[++buildingName]]', $bData['buildingName'], $emailSubject);
        $emailSubject = str_replace('[[++address1]]', $accounData['address'], $emailSubject);
        $emailSubject = str_replace('[[++address2]]', $accounData['address2'], $emailSubject);
        $emailSubject = str_replace('[[++city]]', $accounData['city'], $emailSubject);
        $emailSubject = str_replace('[[++state]]', $accounData['state'], $emailSubject);
        $emailSubject = str_replace('[[++postalCode]]', $accounData['postalCode'], $emailSubject);
        $emailSubject = str_replace('[[++phone]]', $accounData['phoneNumber'], $emailSubject);
        if (isset($accounData['phoneExt']) && $accounData['phoneExt'] != '')
            $emailSubject = str_replace('[[++phoneExt]]', '( ' . $accounData['phoneExt'] . ' )', $emailSubject);
        else
            $emailSubject = str_replace('[[++phoneExt]]', '', $emailSubject);
        $emailSubject = str_replace('[[++costNumber]]', $header_data['corp_account_number'], $emailSubject);

        $emailSubject = str_replace('[[++conferenceRoomName]]', html_entity_decode($conferenceData['room_name']), $emailSubject);
        $emailSubject = str_replace('[[++roomTitle]]', $conferenceData['room_name'], $emailSubject);
        $timeZone = $this->getBuildingTimeZone($userData['building_id']);
        date_default_timezone_set($timeZone);
        $requestDate = date('m/d/Y');
        $emailSubject = str_replace('[[++roomDateAndTime]]', $requestDate, $emailSubject);
        $emailSubject = str_replace('[[++conferenceRoomTenantname]]', $conferenceData['tenant'], $emailSubject);
        $emailSubject = str_replace('[[++conferenceRoomRequestBy]]', $conferenceData['created_user'], $emailSubject);
        $emailSubject = str_replace('[[++conferenceRoomMeetingTitle]]', $conferenceData['meeting_title'], $emailSubject);
        $emailSubject = str_replace('[[++conferenceRoomPhoneNumber]]', $conferenceData['phone'], $emailSubject);
        $emailSubject = str_replace('[[++conferenceRoomEmail]]', $conferenceData['email'], $emailSubject);
        $emailSubject = str_replace('[[++conferenceRoomSelectedRoom]]', $conferenceData['room_name'], $emailSubject);
        $emailSubject = str_replace('[[++conferenceRoomPDFName]]', $conferenceData['design'], $emailSubject);
        $emailSubject = str_replace('[[++conferenceRoomTimeStart]]', $conferenceData['start_time'], $emailSubject);
        $emailSubject = str_replace('[[++conferenceRoomTimeEnd]]', $conferenceData['end_time'], $emailSubject);
        $emailSubject = str_replace('[[++conferenceBookingDate]]', $conferenceData['booking_date'], $emailSubject);


        // End Email subject


        $emailBody = $emailContent['email_content'];


        ///// header
        $emailBody = str_replace('[[++companyLogo]]', $header_data['building_logo_src'], $emailBody);
        $emailBody = str_replace('[[++voctechLogo]]', $header_data['voctech_logo_src'], $emailBody);
        $emailBody = str_replace('[[++dateTime]]', $header_data['date'], $emailBody);
        $emailBody = str_replace('[[++costNumber]]', $header_data['corp_account_number'], $emailBody);
        ///// end header
        //
        ///// Footer
        $emailBody = str_replace('[[++footerInfo]]', $footer_data['footer_info'], $emailBody);
        $emailBody = str_replace('[[++footerSubject]]', $emailSubject, $emailBody);

        $emailBody = str_replace('[[++billState]]', $bData['billState'], $emailBody);
        $emailBody = str_replace('[[++billPostalCode]]', $bData['billPostalCode'], $emailBody);

        $emailBody = str_replace('[[++conferenceRoomName]]', $conferenceData['room_name'], $emailBody);
        $emailBody = str_replace('[[++roomTitle]]', $conferenceData['room_name'], $emailBody);
        $emailBody = str_replace('[[++roomDateAndTime]]', $requestDate, $emailBody);

        $emailBody = str_replace('[[++companyName]]', $accounData['companyName'], $emailBody);
        $emailBody = str_replace('[[++buildingName]]', $bData['buildingName'], $emailBody);
        $emailBody = str_replace('[[++address1]]', $accounData['address'], $emailBody);
        $emailBody = str_replace('[[++address2]]', $accounData['address2'], $emailBody);
        $emailBody = str_replace('[[++city]]', $accounData['city'], $emailBody);
        $emailBody = str_replace('[[++state]]', $accounData['state'], $emailBody);
        $emailBody = str_replace('[[++postalCode]]', $accounData['postalCode'], $emailBody);
        $emailBody = str_replace('[[++phone]]', $accounData['phoneNumber'], $emailBody);
        /// new create scetion
        $emailBody = str_replace('[[++conferenceRoomTenantname]]', $conferenceData['tenant'], $emailBody);
        $emailBody = str_replace('[[++conferenceRoomRequestBy]]', $conferenceData['created_user'], $emailBody);
        $emailBody = str_replace('[[++conferenceRoomMeetingTitle]]', $conferenceData['meeting_title'], $emailBody);
        $emailBody = str_replace('[[++conferenceRoomPhoneNumber]]', $conferenceData['phone'], $emailBody);
        $emailBody = str_replace('[[++conferenceRoomEmail]]', $conferenceData['email'], $emailBody);
        $emailBody = str_replace('[[++conferenceRoomSelectedRoom]]', $conferenceData['room_name'], $emailBody);
        $emailBody = str_replace('[[++conferenceRoomPDFName]]', $conferenceData['design'], $emailBody);
        $emailBody = str_replace('[[++conferenceRoomTimeStart]]', $conferenceData['start_time'], $emailBody);
        $emailBody = str_replace('[[++conferenceRoomTimeEnd]]', $conferenceData['end_time'], $emailBody);
        $emailBody = str_replace('[[++conferenceBookingDate]]', $conferenceData['booking_date'], $emailBody);


        if (isset($accounData['phoneExt']) && $accounData['phoneExt'] != '')
            $emailBody = str_replace('[[++phoneExt]]', '( ' . $accounData['phoneExt'] . ' )', $emailBody);
        $htmlContent = array('subject' => $emailSubject, 'content' => $emailBody);
        // $htmlContent;
        //die;
        return $htmlContent;
    }

    public function getBuildingTimeZone($bid) {
        $buildModel = new Model_Building();
        $build_data = $buildModel->getbuildingbyid($bid);
        if ($build_data) {
            $btimezone = $build_data[0]['timezone'];
            if ($btimezone != 0) {

                $tModel = new Model_TimeZone();
                $tzonelist = $tModel->getTimeZoneById($btimezone);
                $time_zone = $tzonelist[0]['time_value'];
                return $time_zone;
            } else {
                $timeZone = date_default_timezone_get();
                return $timeZone;
            }
        } else {
            $timeZone = date_default_timezone_get();
            return $timeZone;
        }
    }

    public function sendNotificationMail($suId, $tuId, $to, $subject, $ebody) {
        try {
            $mail = new Zend_Mail('utf-8');
            $mail->addHeader('X-greetingsTo', 'support@visionworkorders.com', true);
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
                $mail->setFrom('support@visionworkorders.com', 'Vision Work Orders');
                $return_path = new Zend_Mail_Transport_Sendmail('-fsupport@visionworkorders.com');
            }
            Zend_Mail::setDefaultTransport($return_path);
            $mail->setBodyHtml($ebody);
            if ($mail->send()) {
                $this->saveEmailLog($suId, $tuId, $to, $subject, true);
                return true;
            } else {
                $this->saveEmailLog($suId, $tuId, $to, $subject, false);
                return false;
            }
        } catch (Exception $e) {
            $this->saveEmailLog($suId, $tuId, $to, $subject, false);
        }
    }

    public function saveEmailLog($suId, $tuId, $email, $message, $mail_status) {
        try {
            $email_log = new Model_Log();
            $logData = array();
            $logData['email_sent_by'] = $suId;
            $logData['userId'] = $tuId;
            $logData['log_type'] = 'email';
            if (is_array($email)) {
                $logData['email'] = implode(',', $email);
            } else {
                $logData['email'] = $email;
            }
            $logData['log_message'] = $message;

            if ($mail_status) {
                $logData['email_status'] = 1;
                $email_log->insertLog($logData);
            } else {
                $logData['email_status'] = 0;
                $email_log->insertLog($logData);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function insertcrRequestData($crrequestdata) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($crrequestdata)) {
            try {
                $insert = $db->insert('croom_request', $crrequestdata);
                $id = $db->lastInsertId();
                return $id;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }
    public function updatecrRequestData($getdata,$w_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($getdata)) {
            try {
                $condition = array('crid = ' . $w_id['crid']);
                $db->update('croom_request', $getdata, $condition);
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }

    public function getBookingEvent($from_date, $to_date, $bid = '') {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('cbr' => 'croom_request'))
                ->joinInner(array('cr' => 'conference_room'), 'cbr.croom_id = cr.cid', array('cr.room_name'))
                ->where("DATE(cbr.requested_date) BETWEEN '" . $from_date . "' AND '" . $to_date . "'");
        if ($bid != '') {
            $select = $select->where('cbr.building_id=?', $bid);
        } 
        
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }

    public function getBookingEventTenant($from_date, $to_date, $bid = '',$tid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('cbr' => 'croom_request'))
                ->joinInner(array('cr' => 'conference_room'), 'cbr.croom_id = cr.cid', array('cr.room_name'))
                ->where("DATE(cbr.requested_date) BETWEEN '" . $from_date . "' AND '" . $to_date . "'");
        if ($bid != '') {
            $select = $select->where('cbr.building_id='.$bid.' AND tenant='.$tid);
        }
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }

    public function getcdDetails($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($id != '') {
            $select = $db->select()
                    ->from(array('cr' => 'conference_room'))
                    //->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    // ->joinLeft(array('cd' => 'croom_design'), 'cd.room_id = cr.cid')
                    // ->joinLeft(array('crs' => 'croom_rate_schedule'), 'crs.room_id = cr.cid')
                    ->where('cr.cid=?', $id);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }

    public function getcrbookDetails($crid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($crid != '') {
            $select = $db->select()
                    ->from(array('cr' => 'croom_request'))
                    //->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    // ->joinLeft(array('cd' => 'croom_design'), 'cd.room_id = cr.cid')
                    // ->joinLeft(array('crs' => 'croom_rate_schedule'), 'crs.room_id = cr.cid')
                    ->where('cr.crid=?', $crid);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }

    public function updatedsgAccess($data, $d_id) {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $condition = array('d_id = ' . $d_id);
            $db->update('croom_design', $data, $condition);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    //check the id in design

    public function check_id($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($id)) {
            $select = $db->select()
                    ->from(array('cr' => 'croom_design'))
                    ->where('cr.d_id=?', $id);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }

    public function isScheduleExist($schedule_name,$bid = "" ) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('sh' => 'conference_schedule'), array('sh.schedule_name'))
                ->where('sh.schedule_name = ?', $schedule_name)
                ->where('sh.building_id = ?', $bid);
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }

    public function isCRoomExist($room_name,$bid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('cr' => 'conference_room'), array('cr.room_name'))
                ->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                ->where('cr.room_name = "'.htmlentities($room_name).'" and building_id='.$bid);
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }

    public function isEditCRoomExist($room_name, $id,$bid = "") {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                    //                ->from(array('cr' => 'conference_room'), array('cr.room_name'))
                    //                ->where('cr.room_name = ?', $room_name)
                    //                ->where('cr.cid <> ?', $id);
                
                ->from(array('cr' => 'conference_room'), array('cr.room_name'))
                ->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                ->where('cr.room_name = "'.htmlentities($room_name).'" and building_id='.$bid.' AND cba.room_id!='.$id );
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }

    public function getuserformail($gid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('egu' => 'email_group_users'), array('egu.user_id'))
                ->where('egu.group_id = ?', $gid);
           $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }


    public function getCroomById($sid){
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($sid != '') {
            $select = $db->select()
                    ->from(array('cr' => 'conference_room'))
                    ->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    ->where('cba.schedule_id=?', $sid);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }
    
    public function getCroomRequestById($cid){
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($cid != '') {
            $select = $db->select()
                    ->from(array('cr' => 'conference_room'))
                    //->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    ->where('cr.cid=?', $cid);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }
    
    public function getCroomRequestBytime($starttime,$endtime,$rid,$startdate,$enddate,$crid= ""){
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($starttime != '') {
            $select = $db->select()
                    ->from(array('cr' => 'croom_request'))
                    //->joinInner(array('cba' => 'confroom_building_access'), 'cr.cid = cba.room_id')
                    //->where('cr.cid=?', $cid);
                    ->where("((cr.start_time < '".$starttime."' and cr.end_time > '".$starttime."') or (cr.end_time > '".$starttime."' and cr.end_time < '".$endtime."')) and croom_id IN(".$rid.") and ((end_date >= '".$startdate."' && requested_date <= '".$startdate."') or (end_date >= '".$enddate."' && requested_date <= '".$enddate."')or (end_date <= '".$enddate."' && requested_date >= '".$startdate."'))");
                    //echo $select;
            if(!empty($crid)){
                $select = $select->where('cr.crid !='.$crid);
            }
            //echo $select;
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }  
    
    public function getallchieldbooking($crid){
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($crid != '') {
            $select = $db->select()
                    ->from(array('cr' => 'croom_request'))
                    ->where("parent_id = ".$crid);
                    //echo $select;
            //echo $select;
            $res = $db->fetchAll($select);
            //print_r($res);
            foreach($res as $val){
                $this->deleteCrRoom($val->crid);
            }
            $this->deleteCrRoom($crid);
            //die;
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }
    
    public function GetAllupcommingbooking($crid,$enddate){
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($crid != '') {
            $select = $db->select()
                    ->from(array('cr' => 'croom_request'))
                    ->where("parent_id = ".$crid." and end_date >='".$enddate."'");
                    //echo $select;
            $res = $db->fetchAll($select);
             foreach($res as $val){
                $this->deleteCrRoom($val->crid);
            }
            
            //die;
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }

}
