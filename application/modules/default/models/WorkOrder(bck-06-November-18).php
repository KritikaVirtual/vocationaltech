<?php

/**
 * Description of Work Order
 *
 * @author brijesh
 */
class Model_WorkOrder extends Zend_Db_Table_Abstract {

    protected $_name = 'work_order';
    protected $_tab_role = 'work_order';
    public $_errorMessage = '';

    /* Get all work order list */

    public function getWorkOrder($woId = "") {
        $select = $this->select()->where('status=?', '1');

        if (!empty($woId)) {
            $select = $select->where('woId = ? ', $woId);
        }

        $res = $this->fetchAll($select);

        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }

    public function getWorkOrderInfo($woId) {

        if (!empty($woId)) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('wo' => 'work_order'))
                    ->joinInner(array('t' => 'tenant'), 't.id = wo.tenant', array('tenantName', 'tenantContact'))
                    ->joinLeft(array('bu' => 'buildings'), 'bu.build_id = wo.building', array('buildingName'))
                    ->joinLeft(array('cat' => 'category'), 'cat.cat_id = wo.category', array('categoryName', 'send_email'))
                    ->joinLeft(array('pt' => 'priority'), 'pt.pid = cat.prioritySchedule', array('priorityName', 'pid'))
                    ->joinLeft(array('u' => 'users'), 'wo.create_user = u.uid', array('firstName', 'lastName', 'email', 'phoneNumber'))
                    ->joinLeft(array('tu' => 'tenantusers'), 'wo.create_user = tu.userId', array('tenant_suite' => 'tu.suite_location'))
                    ->where('wo.woId=?', $woId);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } else
            return false;
    }

    public function insertWorkOrder($data) {
        try {
            $this->_errorMessage = "";
            return $this->insert($data);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function updateWorkOrder($data, $woId) {
        $this->_errorMessage = "";
        try {
            if (isset($woId) && !empty($woId)) {
                $where = $this->getAdapter()->quoteInto('woId = ?', $woId);
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

    public function getTenantWorkOrder($tenantId, $order, $dir, $userId = '') {
        if ($tenantId) {
            //$select = $this->select()->where('status=?','1') ;
            $orderBy = $order . ' ' . $dir;
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('wo' => 'work_order'))
                    ->joinInner(array('t' => 'tenant'), 't.id = wo.tenant', array('tenantName', 'tenantContact'))
                    ->joinLeft(array('bu' => 'buildings'), 'bu.build_id = wo.building', array('buildingName'))
                    ->joinLeft(array('cat' => 'category'), 'cat.cat_id = wo.category', array('categoryName'))
                    ->joinLeft(array('wop' => 'work_order_update'), 'wop.wo_id = wo.woId AND wop.current_update=1', array('wop.wo_status', 'wop.internal_note'))
                    ->joinLeft(array('u' => 'users'), 'wo.create_user = u.uid', array('firstName', 'lastName', 'email'))
                    ->where('wo.tenant=?', $tenantId)
                    ->where('wo.master_internal_work_order!=?', 1);
            if ($userId != '') {
                $select = $select->where('wo.create_user=?', $userId);
            }
            $select = $select->order(array($orderBy));
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } else
            return false;
    }

    public function getBuildingWorkOrder($buildID, $order, $dir, $search_array = array(), $page, $show) {
        $offset = ($page - 1) * $show;
        $show;
        if ($buildID) {
            //$select = $this->select()->where('status=?','1') ;
            $orderBy = $order . ' ' . $dir;
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('wo' => 'work_order'))
                    ->joinInner(array('t' => 'tenant'), 't.id = wo.tenant', array('tenantName', 'tenantContact'))
                    ->joinLeft(array('bu' => 'buildings'), 'bu.build_id = wo.building', array('buildingName', 'uniqueCostCenter'))
                    ->joinLeft(array('cat' => 'category'), 'cat.cat_id = wo.category', array('categoryName', 'prioritySchedule'))
                    ->joinLeft(array('wop' => 'work_order_update'), 'wop.wo_id = wo.woId AND wop.current_update=1', array('wop.wo_status', 'wop.internal_note', 'wop.wo_request', 'wop.billable_opt', 'created_date' => 'wop.created_at', 'updated_date' => 'wop.updated_at'))
                    ->joinLeft(array('u' => 'users'), 'wo.create_user = u.uid', array('firstName', 'lastName', 'email'))
                    ->where('wo.building=?', $buildID);

            if (isset($search_array['search_status']) && $search_array['search_status'] != '') {
                $select = $select->where('wop.wo_status in (' . implode(",", $search_array['search_status']) . ')');
            }

            if (isset($search_array['category_name']) && $search_array['category_name'] != '') {
                $select = $select->where("cat.categoryName LIKE '" . $search_array['category_name'] . "%'");
            }

            if (isset($search_array['tenant_name']) && $search_array['tenant_name'] != '') {
                $select = $select->where("t.tenantName LIKE '" . $search_array['tenant_name'] . "%'");
            }

            if (isset($search_array['search_wo']) && $search_array['search_wo'] != '') {
                $select = $select->where('wo.wo_number=?', $search_array['search_wo']);
            }

            if (isset($search_array['from_date']) && $search_array['to_date'] != '') {
                $select = $select->where("DATE(wo.created_at) BETWEEN '" . $search_array['from_date'] . "' AND '" . $search_array['to_date'] . "'");
            }
            $select = $select->order(array($orderBy));
            //if(empty($search_array))
            $select = $select->limit($show, $offset);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } else
            return false;
    }

    /**
     * This is also for count only
     */
    public function getBuildingWorkOrderNew($buildID, $order, $dir, $search_array = array()) {
        if ($buildID) {
            //$select = $this->select()->where('status=?','1') ;
            $orderBy = $order . ' ' . $dir;
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('wo' => 'work_order'), array('count(*) as c'))
                    ->joinInner(array('t' => 'tenant'), 't.id = wo.tenant', array('tenantName', 'tenantContact'))
                    ->joinLeft(array('bu' => 'buildings'), 'bu.build_id = wo.building', array('buildingName', 'uniqueCostCenter'))
                    ->joinLeft(array('cat' => 'category'), 'cat.cat_id = wo.category', array('categoryName', 'prioritySchedule'))
                    ->joinLeft(array('wop' => 'work_order_update'), 'wop.wo_id = wo.woId AND wop.current_update=1', array('wop.wo_status', 'wop.internal_note', 'wop.wo_request', 'wop.billable_opt', 'created_date' => 'wop.created_at', 'updated_date' => 'wop.updated_at'))
                    ->joinLeft(array('u' => 'users'), 'wo.create_user = u.uid', array('firstName', 'lastName', 'email'))
                    ->where('wo.building=?', $buildID);

            if (isset($search_array['search_status']) && $search_array['search_status'] != '') {
                $select = $select->where('wop.wo_status in (' . implode(",", $search_array['search_status']) . ')');
            }

            if (isset($search_array['category_name']) && $search_array['category_name'] != '') {
                $select = $select->where("cat.categoryName LIKE '" . $search_array['category_name'] . "%'");
            }

            if (isset($search_array['tenant_name']) && $search_array['tenant_name'] != '') {
                $select = $select->where("t.tenantName LIKE '" . $search_array['tenant_name'] . "%'");
            }

            if (isset($search_array['search_wo']) && $search_array['search_wo'] != '') {
                $select = $select->where('wo.wo_number=?', $search_array['search_wo']);
            }

            if (isset($search_array['from_date']) && $search_array['to_date'] != '') {
                $select = $select->where("DATE(wo.created_at) BETWEEN '" . $search_array['from_date'] . "' AND '" . $search_array['to_date'] . "'");
            }
            //$select = $select->order(array($orderBy));                      
            $res = $db->fetchAll($select);
            $count = $res[0]->c;
            return ($count > 0) ? $count : false;
        } else
            return false;
    }

    public function getWorkOrderByBuilIds($buildingIds, $order, $dir, $search_array = array(), $page, $show) {
        $offset = ($page - 1) * $show;
        $show;
        if (!empty($buildingIds)) {
            //$select = $this->select()->where('status=?','1') ;
            //print_r($search_array);
            //exit;
            $orderBy = $order . ' ' . $dir;
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('wo' => 'work_order'))
                    ->joinInner(array('t' => 'tenant'), 't.id = wo.tenant', array('tenantName', 'tenantContact'))
                    ->joinLeft(array('bu' => 'buildings'), 'bu.build_id = wo.building', array('buildingName', 'uniqueCostCenter'))
                    ->joinLeft(array('cat' => 'category'), 'cat.cat_id = wo.category', array('categoryName', 'prioritySchedule'))
                    ->joinLeft(array('wop' => 'work_order_update'), 'wop.wo_id = wo.woId AND wop.current_update = 1', array('wop.wo_status', 'wop.internal_note', 'wop.wo_request', 'wop.billable_opt', 'created_date' => 'wop.created_at', 'updated_date' => 'wop.updated_at'))
                    ->joinLeft(array('u' => 'users'), 'wo.create_user = u.uid', array('firstName', 'lastName', 'email'))
                    ->where('wo.building in (' . implode(",", $buildingIds) . ')');
            if (isset($search_array['search_status']) && $search_array['search_status'] != '') {
                $select = $select->where('wop.wo_status in (' . implode(",", $search_array['search_status']) . ')');
            }
            if (isset($search_array['category_name']) && $search_array['category_name'] != '') {
                $select = $select->where("cat.categoryName LIKE '" . $search_array['category_name'] . "%'");
            }

            if (isset($search_array['tenant_name']) && $search_array['tenant_name'] != '') {
                $select = $select->where("t.tenantName LIKE '" . $search_array['tenant_name'] . "%'");
            }

            if (isset($search_array['search_wo']) && $search_array['search_wo'] != '') {
                $select = $select->where('wo.wo_number=?', $search_array['search_wo']);
            }
            if (isset($search_array['from_date']) && $search_array['to_date'] != '') {
                $select = $select->where("DATE(wo.created_at) BETWEEN '" . $search_array['from_date'] . "' AND '" . $search_array['to_date'] . "'");
            }
            $select = $select->order(array($orderBy));
            //if(empty($search_array))
            $select = $select->limit($show, $offset);

            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } else
            return false;
    }

    /**
     * This is for count only
     */
    public function getWorkOrderByBuilIdsNew($buildingIds, $order, $dir, $search_array = array()) {
        if (!empty($buildingIds)) {
            //$select = $this->select()->where('status=?','1') ;
            //print_r($search_array);
            //exit;
            $orderBy = $order . ' ' . $dir;
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('wo' => 'work_order'), array('count(*) as c'))
                    ->joinInner(array('t' => 'tenant'), 't.id = wo.tenant', array('tenantName', 'tenantContact'))
                    ->joinLeft(array('bu' => 'buildings'), 'bu.build_id = wo.building', array('buildingName', 'uniqueCostCenter'))
                    ->joinLeft(array('cat' => 'category'), 'cat.cat_id = wo.category', array('categoryName', 'prioritySchedule'))
                    ->joinLeft(array('wop' => 'work_order_update'), 'wop.wo_id = wo.woId AND wop.current_update = 1', array('wop.wo_status', 'wop.internal_note', 'wop.wo_request', 'wop.billable_opt', 'created_date' => 'wop.created_at', 'updated_date' => 'wop.updated_at'))
                    ->joinLeft(array('u' => 'users'), 'wo.create_user = u.uid', array('firstName', 'lastName', 'email'))
                    ->where('wo.building in (' . implode(",", $buildingIds) . ')');
            if (isset($search_array['search_status']) && $search_array['search_status'] != '') {
                $select = $select->where('wop.wo_status in (' . implode(",", $search_array['search_status']) . ')');
            }
            if (isset($search_array['category_name']) && $search_array['category_name'] != '') {
                $select = $select->where("cat.categoryName LIKE '" . $search_array['category_name'] . "%'");
            }

            if (isset($search_array['tenant_name']) && $search_array['tenant_name'] != '') {
                $select = $select->where("t.tenantName LIKE '" . $search_array['tenant_name'] . "%'");
            }

            if (isset($search_array['search_wo']) && $search_array['search_wo'] != '') {
                $select = $select->where('wo.wo_number=?', $search_array['search_wo']);
            }
            if (isset($search_array['from_date']) && $search_array['to_date'] != '') {
                $select = $select->where("DATE(wo.created_at) BETWEEN '" . $search_array['from_date'] . "' AND '" . $search_array['to_date'] . "'");
            }
            //$select = $select->order(array($orderBy));

            $res = $db->fetchAll($select);
            $count = $res[0]->c;
            return ($count > 0) ? $count : false;
        } else
            return false;
    }

    /*     * *******work order by category ids ********* */

    public function getWorkOrderByCatIds($catIds) {
        if (!empty($catIds)) {
            //$select = $this->select()->where('status=?','1') ;
            //$orderBy = $order.' '.$dir;
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('wo' => 'work_order'))
                    ->joinInner(array('t' => 'tenant'), 't.id = wo.tenant', array('tenantName', 'tenantContact'))
                    ->joinLeft(array('bu' => 'buildings'), 'bu.build_id = wo.building', array('buildingName'))
                    ->joinLeft(array('cat' => 'category'), 'cat.cat_id = wo.category', array('categoryName'))
                    ->joinLeft(array('wop' => 'work_order_update'), 'wop.wo_id = wo.woId AND wop.current_update=1', array('wop.wo_status', 'wop.wo_request'))
                    ->joinLeft(array('u' => 'users'), 'wo.create_user = u.uid', array('firstName', 'lastName', 'email'))
                    ->where('wo.category in (' . implode(",", $catIds) . ')');
            //$select = $select->order(array($orderBy));
            //echo $select->__toString();
            //exit;                      
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } else
            return false;
    }

    /*     * *******get work order id by wo number and building id ******** */

    public function getWoIdByNoNBId($wo_number, $bId) {
        if (!empty($wo_number) && !empty($bId)) {
            $select = $this->select()->from(array('work_order'), array('woId'));
            $select = $select->where('wo_number = ? ', $wo_number);
            $select = $select->where('building = ? ', $bId);
            $res = $this->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res->toArray() : false;
        } else
            return false;
    }

    /* delete work order */

    public function deleteWorkOrder($woId) {
        try {
            $this->delete('woId = ' . $woId);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * send email function
     */
    public function sendWorkOrderEmail($woId, $tenantData) {
        /*         * ******* get tenant users ****** */
        $tenantId = $tenantData['tenantId'];
        $buildId = $tenantData['buildingId'];
        /*         * ****** get comapny info ****** */
        $accountModel = new Model_Account();
        $accoundDetail = $accountModel->getCompanyByBuilding($buildId);
        $accounData = (array) $accoundDetail[0];
        /*         * ***** get work order info ********* */
        $companyName = $accounData['companyName'];
        $woDetail = $this->getWorkOrderInfo($woId);
        $woData = (array) $woDetail[0];
        $tuserMapper = new Model_TenantUser();
        $tuserList = $tuserMapper->getTenantUsers($tenantId);

        $wssModel = new Model_WoScheduleStatus();
        $wssDetail = $wssModel->getCurrentWs($woId);
        $wssData = $wssDetail[0];
        $sendEmail = array();
        //var_dump($tenantData);		 
        foreach ($tuserList as $tuser) {
            if ($tuser->role_id == 5) {
                //echo $tuser->email;
                $sendEmail[] = $tuser->email;
                $send_as = $tuser->send_as;
                $htmlContent = $this->getHtmlContent($send_as, $woData, $tenantData, $accounData);
                //print_r($htmlContent);
                $acknowledge = '';
                $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content']);
                $this->sendNotificationMail($woData['create_user'], $tuser->uid, $tuser->email, $htmlContent['subject'], $htmlContent['content']);
            } else {
                if ($tuser->cc_enable == '1' || $tuser->uid == $woData['create_user']) {
                    //echo $tuser->email;
                    $sendEmail[] = $tuser->email;
                    $send_as = $tuser->send_as;
                    $htmlContent = $this->getHtmlContent($send_as, $woData, $tenantData, $accounData);
                    //print_r($htmlContent);
                    $acknowledge = '';
                    $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content']);

                    $this->sendNotificationMail($woData['create_user'], $tuser->uid, $tuser->email, $htmlContent['subject'], $htmlContent['content']);
                }
            }
        }

        /*         * ********get user from category code ******** */
        $categoryId = $woData['category'];
        $catModel = new Model_Category();
        $catDetail = $catModel->getAllCategory($categoryId);

        $catData = $catDetail[0];
        $accountUser = $catData['account_user'];
        $distGroup = $catData['send_email'];

        /*         * ********get default group id from email group (16-07-2015) ******** */
        $def_email_Model = new Model_EmailGroup();
        $default_id = $def_email_Model->get_default_email_building_id($buildId);

        if (isset($default_id[0]['id']) && $distGroup != $default_id[0]['id']) {

            $distGroup = (($distGroup == "") ? $default_id[0]['id'] : $distGroup . "," . $default_id[0]['id']);
        }

        if ($accountUser != '') {
            $userModel = new Model_User();
            $acuserList = $userModel->getUserBySetIds($accountUser);
            //var_dump($acuserList);
            foreach ($acuserList as $acuser) {
                if (!in_array($acuser['email'], $sendEmail)) {
                    //echo $acuser['email'];
                    $sendEmail[] = $acuser['email'];
                    $htmlContent = $this->getHtmlContent(1, $woData, $tenantData, $accounData);
                    //print_r($htmlContent);
                    $activateURL = BASEURL . '/workstatus/change/woId/' . $woData['woId'] . '/sId/' . $wssData['schedule_id'] . '/ckey/' . $wssData['ckey'];
                    $acknowledge = '<a href="' . $activateURL . '" target="_blank">Click here to acknowledge this work order</a>';
                    $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content']);
                    //print_r($htmlContent);
                    $this->sendNotificationMail($woData['create_user'], $acuser['uid'], $acuser['email'], $htmlContent['subject'], $htmlContent['content']);
                }
            }
        }
        if ($distGroup != '') {
            $disGpArray = explode(",", $distGroup);
            foreach ($disGpArray as $distGP) {
                $eguModel = new Model_EmailGroupUsers();
                $guserList = $eguModel->getGroupUsers($distGP);
                foreach ($guserList as $gpuser) {
                    if (!in_array($gpuser->email, $sendEmail) && $this->getDayAvailable($gpuser->days_of_week)) {
                        //echo $gpuser->email;
                        $sendEmail[] = $gpuser->email;
                        $htmlContent = $this->getHtmlContent($gpuser->sid, $woData, $tenantData, $accounData);
                        //print_r($htmlContent);
                        $activateURL = BASEURL . '/workstatus/change/woId/' . $woData['woId'] . '/sId/' . $wssData['schedule_id'] . '/ckey/' . $wssData['ckey'];
                        $acknowledge = '<a href="' . $activateURL . '" target="_blank">Click here to acknowledge this work order</a>';
                        $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content']);

                        $this->sendNotificationMail($woData['create_user'], $gpuser->uid, $gpuser->email, $htmlContent['subject'], $htmlContent['content']);
                    }
                }
            }
        }
    }

    /*     * **** resend work order email ***** */

    public function resendWorkOrderEmail($woId, $tenantData) {
        /*         * ******* get tenant users ****** */
        $tenantId = $tenantData['tenantId'];
        $buildId = $tenantData['buildingId'];
        /*         * ****** get comapny info ****** */
        $accountModel = new Model_Account();
        $accoundDetail = $accountModel->getCompanyByBuilding($buildId);
        $accounData = (array) $accoundDetail[0];
        /*         * ***** get work order info ********* */
        $companyName = $accounData['companyName'];
        $woDetail = $this->getWorkOrderInfo($woId);
        $woData = (array) $woDetail[0];
        $tuserMapper = new Model_TenantUser();
        $tuserList = $tuserMapper->getTenantUsers($tenantId);

        $wssModel = new Model_WoScheduleStatus();
        $wssDetail = $wssModel->getCurrentWs($woId);
        $wssData = $wssDetail[0];
        $sendEmail = array();

        /*         * ********get user from category code ******** */
        $categoryId = $woData['category'];
        $catModel = new Model_Category();
        $catDetail = $catModel->getAllCategory($categoryId);
        //var_dump($catDetail);
        $catData = $catDetail[0];
        $accountUser = $catData['account_user'];
        $distGroup = $catData['send_email'];
        if ($accountUser != '') {
            $userModel = new Model_User();
            $acuserList = $userModel->getUserBySetIds($accountUser);
            //var_dump($acuserList);
            foreach ($acuserList as $acuser) {
                if ($acuser['alert_notification'] == 1) {
                    if (!in_array($acuser['email'], $sendEmail)) {
                        //echo $acuser['email'];
                        $sendEmail[] = $acuser['email'];
                        $htmlContent = $this->getHtmlContent(1, $woData, $tenantData, $accounData);
                        //print_r($htmlContent);
                        $activateURL = BASEURL . '/workstatus/change/woId/' . $woData['woId'] . '/sId/' . $wssData['schedule_id'] . '/ckey/' . $wssData['ckey'];
                        $acknowledge = '<a href="' . $activateURL . '" target="_blank">Click here to acknowledge this work order</a>';
                        $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content']);
                        //print_r($htmlContent);
                        $this->sendNotificationMail($woData['create_user'], $acuser['uid'], $acuser['email'], $htmlContent['subject'], $htmlContent['content']);
                    }
                }
            }
        }
        if ($distGroup != '') {
            $disGpArray = explode(",", $distGroup);
            foreach ($disGpArray as $distGP) {
                $eguModel = new Model_EmailGroupUsers();
                $guserList = $eguModel->getGroupUsers($distGP);
                //var_dump($guserList);
                foreach ($guserList as $gpuser) {
                    if (!in_array($gpuser->email, $sendEmail) && $this->getDayAvailable($gpuser->days_of_week)) {
                        //echo $gpuser->email;
                        $sendEmail[] = $gpuser->email;
                        $htmlContent = $this->getHtmlContent(1, $woData, $tenantData, $accounData);
                        //print_r($htmlContent);
                        $activateURL = BASEURL . '/workstatus/change/woId/' . $woData['woId'] . '/sId/' . $wssData['schedule_id'] . '/ckey/' . $wssData['ckey'];
                        $acknowledge = '<a href="' . $activateURL . '" target="_blank">Click here to acknowledge this work order</a>';
                        $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content']);

                        $this->sendNotificationMail($woData['create_user'], $gpuser->uid, $gpuser->email, $htmlContent['subject'], $htmlContent['content']);
                    }
                }
            }
        }
    }

    public function getHeaderData($woData) {
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
        $bm_data = $buildingModel->getbuildingbyid($woData['building']);

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
        if (isset($email_template_data['woData']))
            $woData = $email_template_data['woData'];
        if (isset($email_template_data['tenantData']))
            $tenantData = $email_template_data['tenantData'];
        if (isset($email_template_data['accounData']))
            $accounData = $email_template_data['accounData'];

        if (isset($email_template_data['html_type']))
            $html_type = $email_template_data['html_type'];
        if (isset($email_template_data['order_template_details']))
            $order_template_details = $email_template_data['order_template_details'];

        if (isset($email_template_data['status_array']))
            $status_array = $email_template_data['status_array'];
        if (isset($email_template_data['schData']))
            $schData = $email_template_data['schData'];

        /*         * ****Get Building data***** */
        $buildingModel = new Model_Building();
        $buildData = $buildingModel->getbuildingbyid($woData['building']);
        $bData = $buildData[0];

        $wou_status = new Model_WorkOrderUpdate();
        $status_data = $wou_status->getWoStatus($woData['woId']);
        $status_name = $status_data['status'];

        $emailSubject = $emailContent['email_subject'];
        $emailSubject = str_replace('[[++workOrderId]]', $woData['wo_number'], $emailSubject);
        $emailSubject = str_replace('[[++wo_number]]', $woData['wo_number'], $emailSubject);
        $emailSubject = str_replace('[[++workOrderStatus]]', $status_name, $emailSubject);

        ///// Billing Details
        $emailSubject = str_replace('[[++billState]]', $bData['billState'], $emailSubject);
        $emailSubject = str_replace('[[++billPostalCode]]', $bData['billPostalCode'], $emailSubject);
///// End Billing Details
        // Start email subject

        $emailSubject = str_replace('[[++requestTime]]', $woData['time_requested'], $emailSubject);
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

        $emailSubject = str_replace('[[++tenantName]]', $tenantData['tenantName'], $emailSubject);
        $emailSubject = str_replace('[[++requestedBy]]', $woData['firstName'] . ' ' . $woData['lastName'], $emailSubject);
        $emailSubject = str_replace('[[++phoneNumber]]', $tenantData['phoneNumber'], $emailSubject);
        $emailSubject = str_replace('[[++suite]]', $woData['tenant_suite'], $emailSubject);
        $emailSubject = str_replace('[[++email]]', $woData['email'], $emailSubject);
        $emailSubject = str_replace('[[++category]]', $woData['categoryName'], $emailSubject);
        $emailSubject = str_replace('[[++woDescription]]', $woData['work_order_request'], $emailSubject);
        $emailSubject = str_replace('[[++costNumber]]', $header_data['corp_account_number'], $emailSubject);



        // End Email subject









        $emailBody = $emailContent['email_content'];
        $timeZone = $this->getBuildingTimeZone($woData['building']);
        $requestDate = date('m/d/Y', strtotime($woData['date_requested'])) . ' ' . $woData['time_requested'] . ' ' . $timeZone;
///// header 
        $emailBody = str_replace('[[++companyLogo]]', $header_data['building_logo_src'], $emailBody);
        $emailBody = str_replace('[[++voctechLogo]]', $header_data['voctech_logo_src'], $emailBody);
        $emailBody = str_replace('[[++dateTime]]', $header_data['date'], $emailBody);
        $emailBody = str_replace('[[++costNumber]]', $header_data['corp_account_number'], $emailBody);
///// end header
        $emailBody = str_replace('[[++wo_number]]', $woData['wo_number'], $emailBody);
///// Footer 
        $emailBody = str_replace('[[++footerInfo]]', $footer_data['footer_info'], $emailBody);
        $emailBody = str_replace('[[++footerSubject]]', $emailSubject, $emailBody);
///// End Footer
/// acknowledge
        $wssModel = new Model_WoScheduleStatus();
        $wssDetail = $wssModel->getCurrentWs($woData['woId']);
        $wssData = $wssDetail[0];
        $activateURL = BASEURL . '/workstatus/change/woId/' . $woData['woId'] . '/sId/' . $wssData['schedule_id'] . '/ckey/' . $wssData['ckey'];
        $acknowledge = '<a href="' . $activateURL . '" target="_blank">Click here to acknowledge this work order</a>';
        //$emailBody = str_replace('[[++acknowledge]]', $acknowledge, $emailBody);
/// end acknowledge
///// Billing Details
        $emailBody = str_replace('[[++billState]]', $bData['billState'], $emailBody);
        $emailBody = str_replace('[[++billPostalCode]]', $bData['billPostalCode'], $emailBody);
///// End Billing Details

        $emailBody = str_replace('[[++requestDate]]', $requestDate, $emailBody);
        $emailBody = str_replace('[[++requestTime]]', $woData['time_requested'], $emailBody);
        $emailBody = str_replace('[[++companyName]]', $accounData['companyName'], $emailBody);
        $emailBody = str_replace('[[++buildingName]]', $bData['buildingName'], $emailBody);
        $emailBody = str_replace('[[++address1]]', $accounData['address'], $emailBody);
        $emailBody = str_replace('[[++address2]]', $accounData['address2'], $emailBody);
        $emailBody = str_replace('[[++city]]', $accounData['city'], $emailBody);
        $emailBody = str_replace('[[++state]]', $accounData['state'], $emailBody);
        $emailBody = str_replace('[[++postalCode]]', $accounData['postalCode'], $emailBody);
        $emailBody = str_replace('[[++phone]]', $accounData['phoneNumber'], $emailBody);
        if (isset($accounData['phoneExt']) && $accounData['phoneExt'] != '')
            $emailBody = str_replace('[[++phoneExt]]', '( ' . $accounData['phoneExt'] . ' )', $emailBody);
        else
            $emailBody = str_replace('[[++phoneExt]]', '', $emailBody);

        $emailBody = str_replace('[[++tenantName]]', $tenantData['tenantName'], $emailBody);
        $emailBody = str_replace('[[++requestedBy]]', $woData['firstName'] . ' ' . $woData['lastName'], $emailBody);
        $emailBody = str_replace('[[++phoneNumber]]', $tenantData['phoneNumber'], $emailBody);
        $emailBody = str_replace('[[++suite]]', $woData['tenant_suite'], $emailBody);
        $emailBody = str_replace('[[++email]]', $woData['email'], $emailBody);
        $emailBody = str_replace('[[++category]]', $woData['categoryName'], $emailBody);
        $emailBody = str_replace('[[++woDescription]]', $woData['work_order_request'], $emailBody);

        // html type 7 (close template)
        if ($html_type == 7) {
            $buildingModel = new Model_Building();
            $builddata_closedesc = $buildingModel->getWoClosedDesc($woData['woId']);
            $work_order_closed = "";
            if (isset($builddata_closedesc[0]->description)) {
                $work_order_closed = $builddata_closedesc[0]->description;
            }
            $emailBody = str_replace('[[++workOrderCloseDesc]]', $work_order_closed, $emailBody);

            $emailSubject = str_replace('[[++workOrderCloseDesc]]', $work_order_closed, $emailSubject);
        }

        $emailSubject = str_replace('[[++tenantName]]', $tenantData['tenantName'], $emailSubject);
        $emailSubject = str_replace('[[++category]]', $woData['categoryName'], $emailSubject);
        $descText = strip_tags($woData['work_order_request']);
        $descText = str_replace("&nbsp;", ' ', $descText);
        $shortDescription = substr($descText, 0, 40) . '...';
        $emailBody = str_replace('[[++shortDescription]]', $shortDescription, $emailBody);
        $emailSubject = str_replace('[[++shortDescription]]', $shortDescription, $emailSubject);
///////////////////////////// html type 6
        if ($html_type == 6) {
            // replace subject text
            $emailSubject = str_replace('[[++workOrderId]]', $order_template_details['wo_id'], $emailSubject);
            $emailSubject = str_replace('[[++workOrderStatus]]', $order_template_details['status'], $emailSubject);

            /*             * *******change content from template ******* */
            $emailBody = str_replace('[[++startStatus]]', $status_array[$schData['start_status']], $emailBody);
            $emailBody = str_replace('[[++endStatus]]', $status_array[$schData['end_status']], $emailBody);
            $emailBody = str_replace('[[++wo_num]]', $order_template_details['wo_id'], $emailBody);
            $emailBody = str_replace('[[++wo_status]]', $order_template_details['status'], $emailBody);
            $emailBody = str_replace('[[++wo_tenant]]', $order_template_details['tenant_name'], $emailBody);
            $emailBody = str_replace('[[++wo_building]]', $order_template_details['building_name'], $emailBody);
            $emailBody = str_replace('[[++wo_category]]', $order_template_details['category_name'], $emailBody);


            $emailSubject = str_replace('[[++startStatus]]', $status_array[$schData['start_status']], $emailSubject);
            $emailSubject = str_replace('[[++endStatus]]', $status_array[$schData['end_status']], $emailSubject);
            $emailSubject = str_replace('[[++wo_num]]', $order_template_details['wo_id'], $emailSubject);
            $emailSubject = str_replace('[[++wo_status]]', $order_template_details['status'], $emailSubject);
            $emailSubject = str_replace('[[++wo_tenant]]', $order_template_details['tenant_name'], $emailSubject);
            $emailSubject = str_replace('[[++wo_building]]', $order_template_details['building_name'], $emailSubject);
            $emailSubject = str_replace('[[++wo_category]]', $order_template_details['category_name'], $emailSubject);
        }
        // Note Notification  

        if ($html_type == 18) {
            if (isset($tenantData['note_insert_id']) && $tenantData['note_insert_id'] != 0) {
                $notesModel = new Model_WoNote();
                $notesDetails = $notesModel->getWoNote($tenantData['note_insert_id']);
                $emailBody = str_replace('[[++noteDate]]', $notesDetails[0]['note_date'], $emailBody);
                $emailBody = str_replace('[[++noteNote]]', $notesDetails[0]['note'], $emailBody);
                $userModeln = new Model_User();
                $authordetails = $userModeln->getUserById($notesDetails[0]['user_id']);
                $emailBody = str_replace('[[++noteAuther]]', $authordetails[0]['firstName'] . ' ' . $authordetails[0]['lastName'], $emailBody);
            }
        }
        $htmlContent = array('subject' => $emailSubject, 'content' => $emailBody);
        return $htmlContent;
    }

    /*     * ****get html content email** */

    public function getHtmlDoc($woData, $tenantData, $accounData) {
        $header_data = $this->getHeaderData($woData);
        $footer_data = $this->getFooterData();

        $emailMapper = new Model_Email();
        $htmlDocId = 6; // email template id
        $loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
        if ($loadTemplate) {
            $emailContent = $loadTemplate[0];
            $email_template_data['header_data'] = $header_data;
            $email_template_data['emailContent'] = $emailContent;
            $email_template_data['footer_data'] = $footer_data;
            $email_template_data['woData'] = $woData;
            $email_template_data['tenantData'] = $tenantData;
            $email_template_data['accounData'] = $accounData;
            $email_template_data['html_type'] = 1;
            $htmlContent = $this->getBodyData($email_template_data);
            return $htmlContent;
        }
    }

    /*     * ****get html basic e-mail content email** */

    public function getHtmBasiclDoc($woData, $tenantData, $accounData) {
        $header_data = $this->getHeaderData($woData);
        $footer_data = $this->getFooterData();

        $emailMapper = new Model_Email();
        $htmlDocId = 8; // email template id
        $loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
        if ($loadTemplate) {
            $emailContent = $loadTemplate[0];

            $email_template_data['header_data'] = $header_data;
            $email_template_data['emailContent'] = $emailContent;
            $email_template_data['footer_data'] = $footer_data;
            $email_template_data['woData'] = $woData;
            $email_template_data['tenantData'] = $tenantData;
            $email_template_data['accounData'] = $accounData;
            $email_template_data['html_type'] = 2;
            $htmlContent = $this->getBodyData($email_template_data);
            return $htmlContent;
        }
    }

    /*     * ****get html text e-mail content email** */

    public function getHtmlTextDoc($woData, $tenantData, $accounData) {
        $header_data = $this->getHeaderData($woData);
        $footer_data = $this->getFooterData();

        $emailMapper = new Model_Email();
        $htmlDocId = 9; // email template id
        $loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
        if ($loadTemplate) {
            $emailContent = $loadTemplate[0];
            $email_template_data['header_data'] = $header_data;
            $email_template_data['emailContent'] = $emailContent;
            $email_template_data['footer_data'] = $footer_data;
            $email_template_data['woData'] = $woData;
            $email_template_data['tenantData'] = $tenantData;
            $email_template_data['accounData'] = $accounData;
            $email_template_data['html_type'] = 3;
            $htmlContent = $this->getBodyData($email_template_data);
            return $htmlContent;
        }
    }

    /*     * ****get Text e-mail content email** */

    public function getTextEmailDoc($woData, $tenantData, $accounData) {
        $header_data = $this->getHeaderData($woData);
        $footer_data = $this->getFooterData();
        $emailMapper = new Model_Email();
        $htmlDocId = 10; // email template id
        $loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
        if ($loadTemplate) {
            $emailContent = $loadTemplate[0];
            $email_template_data['header_data'] = $header_data;
            $email_template_data['emailContent'] = $emailContent;
            $email_template_data['footer_data'] = $footer_data;
            $email_template_data['woData'] = $woData;
            $email_template_data['tenantData'] = $tenantData;
            $email_template_data['accounData'] = $accounData;
            $email_template_data['html_type'] = 4;
            $htmlContent = $this->getBodyData($email_template_data);
            return $htmlContent;
        }
    }

    public function getHtmlContent($send_as, $woData, $tenantData, $accounData) {
        //echo "send as->".$send_as;
        //echo "<br><pre>";
        //print_r($woData);
        //print_r($tenantData);
        //print_r($accounData); die;
        $htmlContent = '';
        if ($send_as == '1') {
            $htmlContent = $this->getHtmlDoc($woData, $tenantData, $accounData);
        } else if ($send_as == '2') {
            $htmlContent = $this->getHtmBasiclDoc($woData, $tenantData, $accounData);
        } else if ($send_as == '3') {
            $htmlContent = $this->getHtmlTextDoc($woData, $tenantData, $accounData);
        } else {
            $htmlContent = $this->getTextEmailDoc($woData, $tenantData, $accounData);
        }
        return $htmlContent;
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

    public function sendReminderNotificationMail($suId, $tuId, $to, $subject, $ebody) {
        try {
            $mail = new Zend_Mail('utf-8');
            $mail->addTo($to);
            $mail->setSubject($subject);
            $setModel = new Model_Setting();
            $setData = $setModel->getSetting();
            if ($setData) {
                $setting = $setData[0];
                $mail->setFrom($setting['from_email'], $setting['from_name']);
                $return_path = new Zend_Mail_Transport_Sendmail('-f' . $setting['from_email']);
            } else {
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

    public function sendReminderNotification($woId, $scheduleId) {
        $woDetail = $this->getWorkOrderInfo($woId);
        $woData = (array) $woDetail[0];

        /*         * ********* get tenant data ********** */
        $tenantMapper = new Model_Tenant();
        $tenantDetail = $tenantMapper->getTenantByWoId($woId);
        $tenantInfo = $tenantDetail[0];
        $tenantData = (array) $tenantInfo;

        $order_template_details = array();


        /*         * ********* get status data ********** */
        $wo_data = $this->getWorkOrderInfo($woId);

        /*         * ********* get work id ********** */
        $order_template_details['wo_id'] = $wo_data[0]->wo_number;

        $wou_status = new Model_WorkOrderUpdate();
        $status_data = $wou_status->getWoStatus($woId);
        $order_template_details['status'] = $status_name = $status_data['status'];

        /*         * ********* get building name ********** */
        $order_template_details['building_name'] = $building_name = $woData['buildingName'];

        /*         * ********* get category name ********** */
        $order_template_details['category_name'] = $category_name = $woData['categoryName'];

        /*         * ********* get tenant name ********** */
        $order_template_details['tenant_name'] = $tenant_name = $woData['tenantName'];

        /*         * ********* get account details ********** */
        $accountModel = new Model_Account();
        $accoundDetail = $accountModel->getCompanyByBuilding($woData['building']);
        $accounData = (array) $accoundDetail[0];

        /*         * ********* get schedule data ********** */
        $schData = '';
        $schModel = new Model_Schedule();
        $schDetail = $schModel->getScheduleById($scheduleId);
        if ($schDetail)
            $schData = $schDetail[0];
        /*         * ********get user from category code ******** */
        $categoryId = $woData['category'];
        $catModel = new Model_Category();
        $catDetail = $catModel->getAllCategory($categoryId);
        $sendEmail = array();
        //var_dump($catDetail);
        $catData = $catDetail[0];
        $accountUser = $catData['account_user'];
        $distGroup = $catData['send_email'];
        if ($accountUser != '') {
            $userModel = new Model_User();
            $acuserList = $userModel->getUserBySetIds($accountUser);
            //var_dump($acuserList);
            foreach ($acuserList as $acuser) {
                if ($acuser['alert_notification'] == 1) {
                    if (!in_array($acuser['email'], $sendEmail)) {
                        //echo $acuser['email'];
                        $sendEmail[] = $acuser['email'];
                        $htmlContent = $this->getReminderContent($schData, $order_template_details, $woData, $accounData, $tenantData);
                        //print_r($htmlContent);
                        //$acknowledge = '<a href="">Click here to acknowledge this work order</a>';
                        //$htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content'] );
                        $this->sendReminderNotificationMail($woData['create_user'], $acuser['uid'], $acuser['email'], $htmlContent['subject'], $htmlContent['content']);
                    }
                }
            }
        }
        if ($distGroup != '') {
            $disGpArray = explode(",", $distGroup);
            foreach ($disGpArray as $distGP) {
                $eguModel = new Model_EmailGroupUsers();
                $guserList = $eguModel->getGroupUsers($distGP);
                //print_r($guserList);
                //die;
                foreach ($guserList as $gpuser) {
                    //print_r()
                    if ($gpuser->alert_notification == 1) {
                        if (!in_array($gpuser->email, $sendEmail) && $this->getDayAvailable($gpuser->days_of_week)) {
                            //echo $gpuser->email;
                            $sendEmail[] = $gpuser->email;
                            $htmlContent = $this->getReminderContent($schData, $order_template_details, $woData, $accounData, $tenantData);
                            //print_r($htmlContent);
                            //$acknowledge = '<a href="">Click here to acknowledge this work order</a>';
                            //$htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content'] );
                            $this->sendReminderNotificationMail($woData['create_user'], $gpuser->uid, $gpuser->email, $htmlContent['subject'], $htmlContent['content']);
                        }
                    }
                }
            }
        }
    }

    public function getReminderContent($schData, $order_template_details, $woData, $accounData, $tenantData) {

        $header_data = $this->getHeaderData($woData);
        $footer_data = $this->getFooterData();

        $emailMapper = new Model_Email();
        $htmlDocId = 12; // email template id
        $ssModel = new Model_ScheduleStatus();
        $status_list = $ssModel->getScheduleStatus();
        $status_array = array();
        foreach ($status_list as $sl) {
            $status_array[$sl['ssID']] = $sl['title'];
        }
        $loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
        if ($loadTemplate) {
            $emailContent = $loadTemplate[0];

            $email_template_data['header_data'] = $header_data;
            $email_template_data['emailContent'] = $emailContent;
            $email_template_data['footer_data'] = $footer_data;
            $email_template_data['woData'] = $woData;
            $email_template_data['tenantData'] = $tenantData;
            $email_template_data['accounData'] = $accounData;
            $email_template_data['order_template_details'] = $order_template_details;
            $email_template_data['html_type'] = 6;
            $email_template_data['status_array'] = $status_array;
            $email_template_data['schData'] = $schData;
            $htmlContent = $this->getBodyData($email_template_data);

            //$htmlContent = array('subject'=>$emailSubject,'content'=>$emailBody);
            return $htmlContent;
        }
    }

    /*     * ******Get Building Time Zone ******* */

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

    public function getDayAvailable($wd) {
        $day = date('l');
        if ($wd == 1) {
            return true;
        } else if ($wd == 2 && $day != 'Sunday' && $day != 'Saturday') {
            return true;
        } else if ($wd == 3 && ($day == 'Sunday' || $day == 'Saturday')) {
            return true;
        } else if ($wd == 4 && ($day == 'Monday' || $day == 'Wednesday' || $day == 'Friday')) {
            return true;
        } else if ($wd == 5 && ($day == 'Tuesday' || $day == 'Thursday')) {
            return true;
        } else if ($wd == 6 && $day == 'Monday') {
            return true;
        } else if ($wd == 7 && $day == 'Tuesday') {
            return true;
        } else if ($wd == 8 && $day == 'Wednesday') {
            return true;
        } else if ($wd == 9 && $day == 'Thursday') {
            return true;
        } else if ($wd == 10 && $day == 'Friday') {
            return true;
        } else if ($wd == 11 && $day == 'Saturday') {
            return true;
        } else if ($wd == 12 && $day == 'Sunday') {
            return true;
        } else
            return false;
    }

    public function getMaxWoNumber($bid) {
        if (!empty($bid)) {
            $select = $this->select()
                    ->from(array('t' => 'work_order'), array(new Zend_Db_Expr('MAX(wo_number) as maxwnum')));
            $select = $select->where('building = ? ', $bid);
            $row = $this->fetchRow($select);
            if (!$row) {
                return false;
            } else {
                $tData = $row->toArray();
                return $tData ['maxwnum'];
            }
        } else
            return false;
    }

    public function sendClosedNotification($woId, $cur_user, $accountuseremail = null, $master_internal_work_order = 0) {

        $woInfo = $this->getWorkOrderInfo($woId);
        $to = '';
        $toEmail = array();
        if ($woInfo) {
            $woData = $woInfo[0];
            $to = $woData->email;
            if ($accountuseremail == 'users') {
                $grpModel = new Model_EmailGroup();
                $groupDetails = $grpModel->getCompeleteNotficationUsers($woData->building);
                if (isset($groupDetails) && $groupDetails != '') {
                    foreach ($groupDetails as $value) {
                        if (isset($value) && $value != '') {
                            foreach ($value as $valueinn) {
                                $toEmail[] = $valueinn->email;
                            }
                        }
                    }
                }
                if ($master_internal_work_order != 1) {
                    $toEmail[] = $to;
                }
                $toEmail = array_unique($toEmail);
                $to = $toEmail;
            }

            $htmlContent = $this->getCloseContent($woData);
            $this->sendNotificationMail($cur_user, $woData->create_user, $to, $htmlContent['subject'], $htmlContent['content']);
        }
    }

    public function getCloseContent($woData) {
        $woId = $woData->woId;
        /*         * ********* get tenant data ********** */
        $tenantMapper = new Model_Tenant();
        $tenantDetail = $tenantMapper->getTenantByWoId($woId);
        $tenantInfo = $tenantDetail[0];
        $tenantData = (array) $tenantInfo;

        /*         * ********* get account details ********** */
        $accountModel = new Model_Account();
        $accoundDetail = $accountModel->getCompanyByBuilding($woData->building);
        $accounData = (array) $accoundDetail[0];

        $emailMapper = new Model_Email();
        $htmlDocId = 14; // email template id
        $loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
        if ($loadTemplate) {
            $emailContent = $loadTemplate[0];
            $emailSubject = $emailContent['email_subject'];
            $emailBody = $emailContent['email_content'];
            $woData = (array) $woData;
            $header_data = $this->getHeaderData($woData);
            $footer_data = $this->getFooterData();
            $email_template_data['header_data'] = $header_data;
            $email_template_data['emailContent'] = $emailContent;
            $email_template_data['footer_data'] = $footer_data;
            $email_template_data['woData'] = $woData;
            $email_template_data['tenantData'] = $tenantData;
            $email_template_data['accounData'] = $accounData;
            $email_template_data['html_type'] = 7;
            $htmlContent = $this->getBodyData($email_template_data);
            //$htmlContent = array('subject'=>$emailSubject,'content'=>$emailBody);
            return $htmlContent;
        }
    }

    /* Get active work order of building */

    public function getActiveWorkOrderByBId($bid) {
        if (!empty($bid)) {
            $select = $this->select()
                    ->from(array('t' => 'work_order'), array('woId', 'building'));
            $select = $select->where('building = ? ', $bid);
            $select = $select->where('status = ? ', '1');
            $res = $this->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res->toArray() : false;
        } else
            return false;
    }

    public function getWorkOrderByBuilIdsFilter($buildingIds, $tid, $order, $dir, $search_array = array(), $tid, $showbatch, $lastbatch, $searchtodate, $searchfromdate) {
        if (!empty($buildingIds)) {
            //$select = $this->select()->where('status=?','1') ;
            //print_r($search_array);
            //exit;
            $orderBy = $order . ' ' . $dir;
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('wo' => 'work_order'))
                    ->joinInner(array('t' => 'tenant'), 't.id = wo.tenant', array('tenantName', 'tenantContact'))
                    ->joinLeft(array('bu' => 'buildings'), 'bu.build_id = wo.building', array('buildingName', 'uniqueCostCenter'))
                    ->joinLeft(array('cat' => 'category'), 'cat.cat_id = wo.category', array('categoryName', 'prioritySchedule'))
                    ->joinLeft(array('wop' => 'work_order_update'), 'wop.wo_id = wo.woId AND wop.current_update = 1', array('wop.wo_status', 'wop.billable_opt', 'wop.internal_note', 'wop.wo_request', 'created_date' => 'wop.created_at', 'updated_date' => 'wop.updated_at'))
                    ->joinLeft(array('u' => 'users'), 'wo.create_user = u.uid', array('firstName', 'lastName', 'email'))
                    ->where('wo.building in (' . implode(",", $buildingIds) . ')')
                    ->where('wop.billable_opt=?', 1);

            if ($tid != '') {
                $select = $select->where('t.id=?', $tid);
            }
            if ($showbatch != '') {
                if ($lastbatch != '') {
                    $select = $select->where('wo.wo_batch in (' . implode(",", $lastbatch) . ')');
                }
            } else {
                $select = $select->where('wo.wo_batch=?', 0);
            }
            if ($searchtodate != '') {
                $select = $select->where("DATE(wop.created_at) BETWEEN '" . $searchfromdate . "' AND '" . $searchtodate . "'");
            }
            if (isset($search_array['search_status']) && $search_array['search_status'] != '') {
                $select = $select->where('wop.wo_status in (' . implode(",", $search_array['search_status']) . ')');
            }
            if (isset($search_array['category_name']) && $search_array['category_name'] != '') {
                $select = $select->where("cat.categoryName LIKE '" . $search_array['category_name'] . "%'");
            }

            if (isset($search_array['tenant_name']) && $search_array['tenant_name'] != '') {
                $select = $select->where("t.tenantName LIKE '" . $search_array['tenant_name'] . "%'");
            }

            if (isset($search_array['search_wo']) && $search_array['search_wo'] != '') {
                $select = $select->where('wo.wo_number=?', $search_array['search_wo']);
            }
            if (isset($search_array['from_date']) && $search_array['to_date'] != '') {
                $select = $select->where("DATE(wo.created_at) BETWEEN '" . $search_array['from_date'] . "' AND '" . $search_array['to_date'] . "'");
            }
            $select = $select->order(array($orderBy));

            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } else
            return false;
    }

    public function getBuildingWorkOrderFilter($buildID, $tid, $order, $dir, $search_array = array(), $tid, $showbatch, $lastbatch, $searchtodate, $searchfromdate) {
        if ($buildID) {
            //$select = $this->select()->where('status=?','1') ;
            $orderBy = $order . ' ' . $dir;
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('wo' => 'work_order'))
                    ->joinInner(array('t' => 'tenant'), 't.id = wo.tenant', array('tenantName', 'tenantContact'))
                    ->joinLeft(array('bu' => 'buildings'), 'bu.build_id = wo.building', array('buildingName', 'uniqueCostCenter'))
                    ->joinLeft(array('cat' => 'category'), 'cat.cat_id = wo.category', array('categoryName', 'prioritySchedule'))
                    ->joinLeft(array('wop' => 'work_order_update'), 'wop.wo_id = wo.woId AND wop.current_update=1', array('wop.wo_status', 'wop.internal_note', 'wop.wo_request', 'wop.billable_opt', 'created_date' => 'wop.created_at', 'updated_date' => 'wop.updated_at'))
                    ->joinLeft(array('u' => 'users'), 'wo.create_user = u.uid', array('firstName', 'lastName', 'email'))
                    ->where('wo.building=?', $buildID)
                    ->where('wop.billable_opt=?', 1);
            if ($tid != '') {
                $select = $select->where('t.id=?', $tid);
            }
            if ($showbatch != '') {
                if ($lastbatch != '') {
                    $select = $select->where('wo.woId in (' . implode(",", $lastbatch) . ')');
                }
            } else {
                $select = $select->where('wo.wo_batch=?', 0);
            }
            if ($searchtodate != '') {
                //$select = $select->where('wop.created_at BETWEEN ' .$searchtodate.' and '. $searchfromdate);
                $select = $select->where("DATE(wop.created_at) BETWEEN '" . $searchfromdate . "' AND '" . $searchtodate . "'");
            }
            if (isset($search_array['search_status']) && $search_array['search_status'] != '') {
                $select = $select->where('wop.wo_status in (' . implode(",", $search_array['search_status']) . ')');
            }

            if (isset($search_array['category_name']) && $search_array['category_name'] != '') {
                $select = $select->where("cat.categoryName LIKE '" . $search_array['category_name'] . "%'");
            }

            if (isset($search_array['tenant_name']) && $search_array['tenant_name'] != '') {
                $select = $select->where("t.tenantName LIKE '" . $search_array['tenant_name'] . "%'");
            }

            if (isset($search_array['search_wo']) && $search_array['search_wo'] != '') {
                $select = $select->where('wo.wo_number=?', $search_array['search_wo']);
            }

            if (isset($search_array['from_date']) && $search_array['to_date'] != '') {
                $select = $select->where("DATE(wo.created_at) BETWEEN '" . $search_array['from_date'] . "' AND '" . $search_array['to_date'] . "'");
            }
            $select = $select->order(array($orderBy));
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } else
            return false;
    }

    public function updateWorkOrderBatchid($data, $batchid) {
        $this->_errorMessage = "";
        try {
            if (isset($data) && !empty($data)) {
                foreach ($data as $value) {
                    $where = $this->getAdapter()->quoteInto('woId = ?', $value);
                    $this->update($batchid, $where);
                }
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function deleteBatch($data, $woid, $wo_batch, $bid) {
        $this->_errorMessage = "";
        try {
            if (isset($data) && !empty($data)) {
                $where = $this->getAdapter()->quoteInto('woId = ?', $woid);
                $this->update($data, $where);
                $select = $this->select()
                        ->from(array('wo' => 'work_order'), array(new Zend_Db_Expr('COUNT(wo_batch) as count')))
                        ->where('wo_batch = ? ', $wo_batch);
                $row = $this->fetchRow($select);
                if ($row) {
                    foreach ($row as $value) {
                        if ($value == 0) {
                            $woBobj = new Model_WoBatch();
                            $woBobj->delete(array('batch_number =?' => $wo_batch, 'building_id = ?' => $bid));
                        }
                    }
                }
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function sendEmailToTenant($woId, $tenantData) {
        /*         * ******* get tenant users ****** */
        $tenantId = $tenantData['tenantId'];
        $buildId = $tenantData['buildingId'];
        /*         * ****** get comapny info ****** */
        $accountModel = new Model_Account();
        $accoundDetail = $accountModel->getCompanyByBuilding($buildId);
        $accounData = (array) $accoundDetail[0];
        /*         * ***** get work order info ********* */
        $companyName = $accounData['companyName'];
        $woDetail = $this->getWorkOrderInfo($woId);
        $woData = (array) $woDetail[0];
        $tuserMapper = new Model_TenantUser();
        $tuserList = $tuserMapper->getTenantUsers($tenantId);
        //print_r($tenantData['userId']); echo 'aaaaaaaaaaaaaaaaa';
        $wssModel = new Model_WoScheduleStatus();
        $wssDetail = $wssModel->getCurrentWs($woId);
        $wssData = $wssDetail[0];
        $sendEmail = array();
        //var_dump($tenantData);		 
        foreach ($tuserList as $tuser) {
            if ($tuser->note_notification == 1) {
                if ($tenantData['note_created_user'] == $tuser->uid) {
                    //echo $tuser->email;
                    $sendEmail[] = $tuser->email;
                    $htmlContent = $this->getNotesContent($woData, $tenantData, $accounData, $buildId);
                    //print_r($htmlContent);
                    // print_r($htmlContent);
                    //die;
                    $acknowledge = '';
                    $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content']);
                    $this->sendNotificationMail($woData['create_user'], $tuser->uid, $tuser->email, $htmlContent['subject'], $htmlContent['content']);
                } else {
                    if ($tuser->cc_enable == '1' || $tuser->uid == $woData['create_user']) {
                        //echo $tuser->email;
                        $sendEmail[] = $tuser->email;
                        $htmlContent = $this->getNotesContent($woData, $tenantData, $accounData, $buildId);
                        $acknowledge = '';
                        $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content']);
                        $this->sendNotificationMail($woData['create_user'], $tuser->uid, $tuser->email, $htmlContent['subject'], $htmlContent['content']);
                    }
                }
            }
        }
    }

    public function sendEmailToAccountUsers($woId, $tenantData) {
        /*         * ******* get tenant users ****** */
        $tenantId = $tenantData['tenantId'];
        $buildId = $tenantData['buildingId'];
        /*         * ****** get comapny info ****** */
        $accountModel = new Model_Account();
        $accoundDetail = $accountModel->getCompanyByBuilding($buildId);
        $accounData = (array) $accoundDetail[0];
        /*         * ***** get work order info ********* */
        $companyName = $accounData['companyName'];
        $woDetail = $this->getWorkOrderInfo($woId);
        $woData = (array) $woDetail[0];
        $tuserMapper = new Model_TenantUser();
        $tuserList = $tuserMapper->getTenantUsers($tenantId);

        $wssModel = new Model_WoScheduleStatus();
        $wssDetail = $wssModel->getCurrentWs($woId);
        $wssData = $wssDetail[0];
        $sendEmail = array();

        /*         * ********get user from category code ******** */
        $categoryId = $woData['category'];
        $catModel = new Model_Category();
        $catDetail = $catModel->getAllCategory($categoryId);

        $catData = $catDetail[0];
        $accountUser = $catData['account_user'];
        $distGroup = $catData['send_email'];

        /*         * ********get default group id from email group (16-07-2015) ******** */
        $def_email_Model = new Model_EmailGroup();
        $default_id = $def_email_Model->get_default_email_building_id($buildId);

        if (isset($default_id[0]['id']) && $distGroup != $default_id[0]['id']) {

            $distGroup = (($distGroup == "") ? $default_id[0]['id'] : $distGroup . "," . $default_id[0]['id']);
        }

        if ($accountUser != '') {
            $userModel = new Model_User();
            $acuserList = $userModel->getUserBySetIds($accountUser);
            //var_dump($acuserList);
            foreach ($acuserList as $acuser) {
                if ($acuser['note_notification'] == 1) {
                    if (!in_array($acuser['email'], $sendEmail)) {
                        //echo $acuser['email'];
                        $sendEmail[] = $acuser['email'];
                        $htmlContent = $this->getNotesContent($woData, $tenantData, $accounData, $buildId);
                        //print_r($htmlContent);
                        //print_r($htmlContent);
                        //die;
                        $this->sendNotificationMail($woData['create_user'], $acuser['uid'], $acuser['email'], $htmlContent['subject'], $htmlContent['content']);
                    }
                }
            }
        }

        if ($distGroup != '') {
            $disGpArray = explode(",", $distGroup);
            foreach ($disGpArray as $distGP) {
                $eguModel = new Model_EmailGroupUsers();
                $guserList = $eguModel->getGroupUsers($distGP);
                foreach ($guserList as $gpuser) {
                    if ($gpuser->note_notification == 1) {
                        if (!in_array($gpuser->email, $sendEmail) && $this->getDayAvailable($gpuser->days_of_week)) {
                            //echo $gpuser->email;
                            $sendEmail[] = $gpuser->email;
                            $htmlContent = $this->getNotesContent($woData, $tenantData, $accounData, $buildId);
                            //print_r($htmlContent);
                            //echo "asdas";
                            //print_r($htmlContent);

                            $this->sendNotificationMail($woData['create_user'], $gpuser->uid, $gpuser->email, $htmlContent['subject'], $htmlContent['content']);
                        }
                    }
                }
            }
        }
    }

    public function getNotesContent($woData, $tenantData, $accounData, $buildId = "") {
        $header_data = $this->getHeaderData($woData);
        $footer_data = $this->getFooterData();
        $htmlDocId = '';
        $emailMapper = new Model_Email();
        //if($tenantData['login_roleId'] == 4) {
        $loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId, "", "8", $buildId);
        if ($loadTemplate) {
            $htmlDocId = $loadTemplate[0]['id'];
        } else {
            $htmlDocId = 18;
        }

        $loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
        if ($loadTemplate) {
            $emailContent = $loadTemplate[0];
            $email_template_data['header_data'] = $header_data;
            $email_template_data['emailContent'] = $emailContent;
            $email_template_data['footer_data'] = $footer_data;
            $email_template_data['woData'] = $woData;
            $email_template_data['tenantData'] = $tenantData;
            $email_template_data['accounData'] = $accounData;
            $email_template_data['html_type'] = 18;
            $htmlContent = $this->getBodyData($email_template_data);
            return $htmlContent;
        }
    }

    public function getWorkorderbystatus($user_id, $status) {
        if ($user_id != '') {
            $buildingIds = array();
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('bma' => 'user_building_module_access'), array('building_id'))
                    ->where('user_id=?', $user_id);
            $building = $db->fetchAll($select);
            foreach ($building as $val) {
                $buildingIds[] = $val->building_id;
            }
            $select = $db->select()
                    ->from(array('wo' => 'work_order'), array('count_workorder' => 'COUNT(*)'))
                    ->joinLeft(array('wop' => 'work_order_update'), 'wop.wo_id = wo.woId AND wop.current_update=1', array())
                    ->where('wo.building in (' . implode(",", $buildingIds) . ')')
                    ->where('wop.wo_status in (' . implode(",", $status) . ')');
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
    }

    public function getBuildingbyworkorder($woId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('wo' => 'work_order'), array('building'))
                ->where('woId=?', $woId);
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }

    /* public function get_last_activity(){
      $db = Zend_Db_Table::getDefaultAdapter();
      $select = $db->select()
      ->from('work_order_update');
      $res = $db->fetchAll($select);
      return ($res && sizeof($res)>0)? $res : false ;
      }
      public function get_last_activity1(){
      $db = Zend_Db_Table::getDefaultAdapter();
      $select = $db->select()
      ->from('wo_history_log');
      $res = $db->fetchAll($select);
      return ($res && sizeof($res)>0)? $res : false ;
      }
      public function get_last_activity2(){
      $db = Zend_Db_Table::getDefaultAdapter();
      $select = $db->select()
      ->from('work_order');
      $res = $db->fetchAll($select);
      return ($res && sizeof($res)>0)? $res : false ;
      } */

    public function get_last_activity() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from('work_order_update', array('count(*) as c'));
        $res = $db->fetchAll($select);
        $count = $res[0]->c;
        return ($count && $count > 0) ? $count : false;
    }

    public function get_last_activity1() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from('wo_history_log', array('count(*) as c'));
        $res = $db->fetchAll($select);
        $count = $res[0]->c;
        return ($count && $count > 0) ? $count : false;
    }

    public function get_last_activity2() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from('work_order', array('count(*) as c'));
        $res = $db->fetchAll($select);
        $count = $res[0]->c;
        return ($count && $count > 0) ? $count : false;
    }

}
