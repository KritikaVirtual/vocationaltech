<?php

class DBFunctions {
    private $db;

    function __Construct() {
        require_once 'DBConnect.php';
        $this->db = new DBConnect();
        $this->db->connect();
    }

    function __Destruct() {

    }
    public function requestLog($str, $path = '') {
        $query = "Insert into request_log set str='$str',path='$path'";
        $result = mysql_query($query) or die(mysql_error());
    }
    public function getLogin($username, $password) {
        $query = "SELECT * FROM users WHERE userName = '" . $username . "' AND password = '" . md5($password) . "' AND status = '1' AND remove_status = '0'";
        //$query = "SELECT * FROM users WHERE userName = '".$username."' AND password = '".md5($password)."' AND status = '1' AND remove_status = '0' AND role_id != '1'";
        //echo $query; exit;
        $result = mysql_query($query) or die(mysql_error());
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows == 1) {
            $result = mysql_fetch_array($result);
            return $result;
        }
    }

    public function getStatus($selected) {
        $query = "SELECT * FROM schedule_status where status = '1'";
        $status = mysql_query($query) or die(mysql_error());
        $no_of_rows = mysql_num_rows($status);
        if ($no_of_rows > 0) {
            $status_list = "";
            if ($selected == "") {
                $status_list .= "<option value=''>Select All Status</option>";
            }

            while ($status_row = mysql_fetch_array($status)) {
                $status_list .= "<option value='" . $status_row['ssID'] . "' ";
                if ($selected == $status_row['ssID']) {
                    $status_list .= "selected = 'selected'";
                }
                $status_list .= ">" . $status_row['title'] . "</option>";
            }
            return $status_list;
        } else {
            return false;
        }
    }

    public function getPredefinedNotes() {
        $query = "SELECT * FROM notes_predefined where status = '1'";
        $status = mysql_query($query) or die(mysql_error());
        $no_of_rows = mysql_num_rows($status);
        if ($no_of_rows > 0) {
            $status_list = "";
            $status_list .= "<option value=''>Select Notes</option>";
            while ($status_row = mysql_fetch_array($status)) {
                $status_list .= "<option value='" . $status_row['notes'] . "'>" . $status_row['notes'] . "</option>";
            }
            return $status_list;
        } else {
            return false;
        }
    }

    public function getWorkOrder($role_id, $uid, $cust_id, $filter, $woid_array) {

        $condition = "";
        if ($filter != '') {
            $condition .= " AND wop.wo_status = '" . $filter . "'";
        }

        //// check for search from wo detail page
        if (strlen($woid_array) > 0) {
            $condition .= " AND wo.woId IN (" . $woid_array . ")";
        }
        //// end check for search from wo detail page

        $work_rows = "";
        if ($role_id == '7') {

            // dashboard for Tenant User

            $query_tenant = "SELECT `t`.*, `tuser`.`id` AS `tuId`, `tuser`.`id` AS `tuserId`, `tuser`.`tenantId`, `tuser`.`userId` AS `tenantuserId`, `tuser`.`suite_location`, `tuser`.`cc_enable`, `tuser`.`send_as`, `tuser`.`complete_notification`, `u`.`uid`, `u`.`firstName`, `u`.`lastName`, `u`.`phoneNumber` AS `userpNumber`, `u`.`email`, `u`.`role_id`, `u`.`userName`, `st`.`state` AS `statename` FROM `tenant` AS `t` INNER JOIN `tenantUsers` AS `tuser` ON tuser.tenantId = t.id LEFT JOIN `users` AS `u` ON u.uid = tuser.userId LEFT JOIN `states` AS `st` ON st.state_code = t.state_code WHERE (tuser.userId='" . $uid . "')";
            $tenant_sql = mysql_query($query_tenant);
            $tenant_data = mysql_fetch_array($tenant_sql);

            $query = "SELECT `wo`.*, `t`.`tenantName`, `t`.`tenantContact`, `bu`.`buildingName`, `cat`.`categoryName`, `wop`.`wo_status`, `wop`.`internal_note`, `u`.`firstName`, `u`.`lastName`, `u`.`email`
				FROM `work_order` AS `wo`
				INNER JOIN `tenant` AS `t` ON t.id = wo.tenant
				LEFT JOIN `buildings` AS `bu` ON bu.build_id = wo.building
				LEFT JOIN `category` AS `cat` ON cat.cat_id = wo.category
				LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update=1
				LEFT JOIN `users` AS `u` ON wo.create_user = u.uid
				WHERE (wo.tenant='" . $tenant_data['id'] . "') AND (wo.master_internal_work_order!=1) AND (wo.create_user='" . $uid . "') " . $condition . "
				ORDER BY `woId` DESC";

            //echo $query;

            $wo_query = mysql_query($query);
            $work_rows = "";
            $wocnt = 0;
            while ($wo_row = mysql_fetch_array($wo_query)) {
                $tenantName = $wo_row['tenantName'];
                $buildingName = $wo_row['buildingName'];
                $description = $wo_row['work_order_request'];
                $wo_status = $wo_row['wo_status'];
                $wo_id = $wo_row['woId'];
                $wo_number = $wo_row['wo_number'];
                $wocnt++;

                if ($wocnt % 2 == 0) {
                    $work_rows .= "<tr onclick='getDetail(" . $wo_id . ");' class='vt_set_yellow'><td>" . $wocnt . "</td><td>" . $buildingName . "</td><td>" . $tenantName . "</td><td>" . $description . "</td><td>";

                    $schedule_query = "SELECT `priority_schedule`.* FROM `priority_schedule` WHERE (priority_id = '" . $wo_row['prioritySchedule'] . "') AND (start_status = '" . $wo_status . "')";
                    $schedule_sql = mysql_query($schedule_query);
                    $schedule = mysql_fetch_array($schedule_sql);

                    if ($wo_status == '1') {
                        $lastTime = $wo_row['date_requested'] . ' ' . $wo_row['time_requested'];
                    } else if ($wo_row['updated_at'] != '0000-00-00 00:00:00') {
                        $lastTime = $wo_row['updated_at'];
                    } else {
                        $lastTime = $wo_row['created_at'];
                    }
                    $currTime = date("Y-m-d h:i:s");
                    $lastTime = date("Y-m-d h:i:s", strtotime($lastTime));
                    $timeDiff = strtotime($currTime) - strtotime($lastTime);

                    $time = $schedule['Time'];
                    $length = $schedule['length'];
                    $diff = $timeDiff;

                    $reminderStatus = "";
                    $time_factor = array('1' => 60, '2' => 3600, '3' => 86400, '4' => 604800, '5' => 2592000, '6' => 31536000);
                    if (isset($time_factor[$length])) {
                        $cal_time = intval($diff / $time_factor[$length]);
                        if ($cal_time >= $time) {
                            $reminderStatus = true;
                        } else {
                            $reminderStatus = false;
                        }
                    }

                    if ($reminderStatus && ($wo_status != '6' && $wo_status != '7')) {
                        $work_rows .= $reminderStatus . " <img src='img/alert.png' alt=''/>";
                    } else {
                        if ($wo_status == '1') {
                            $work_rows .= " <img src='img/bell-icon.png' alt=''/>";
                        }
                    }

                    $work_rows .= "</td></tr>";
                } else {
                    $work_rows .= "<tr onclick='getDetail(" . $wo_id . ");' class='vt_set_purple'><td>" . $wo_number . "</td><td>" . $buildingName . "</td><td>" . $tenantName . "</td><td>" . $description . "</td><td>";

                    $schedule_query = "SELECT `priority_schedule`.* FROM `priority_schedule` WHERE (priority_id = '" . $wo_row['prioritySchedule'] . "') AND (start_status = '" . $wo_status . "')";
                    $schedule_sql = mysql_query($schedule_query);
                    $schedule = mysql_fetch_array($schedule_sql);

                    if ($wo_status == '1') {
                        $lastTime = $wo_row['date_requested'] . ' ' . $wo_row['time_requested'];
                    } else if ($wo_row['updated_at'] != '0000-00-00 00:00:00') {
                        $lastTime = $wo_row['updated_at'];
                    } else {
                        $lastTime = $wo_row['created_at'];
                    }
                    $currTime = date("Y-m-d h:i:s");
                    $lastTime = date("Y-m-d h:i:s", strtotime($lastTime));
                    $timeDiff = strtotime($currTime) - strtotime($lastTime);

                    $time = $schedule['Time'];
                    $length = $schedule['length'];
                    $diff = $timeDiff;

                    $reminderStatus = "";
                    $time_factor = array('1' => 60, '2' => 3600, '3' => 86400, '4' => 604800, '5' => 2592000, '6' => 31536000);
                    if (isset($time_factor[$length])) {
                        $cal_time = intval($diff / $time_factor[$length]);
                        if ($cal_time >= $time) {
                            $reminderStatus = true;
                        } else {
                            $reminderStatus = false;
                        }
                    }

                    if ($reminderStatus && ($wo_status != '6' && $wo_status != '7')) {
                        $work_rows .= $reminderStatus . " <img src='img/alert.png' alt=''/>";
                    } else {
                        if ($wo_status == '1') {
                            $work_rows .= " <img src='img/bell-icon.png' alt=''/>";
                        }
                    }

                    $work_rows .= "</td></tr>";
                }
            }
        } else if ($role_id == '5') {

            // dashboard for Tenant Admin

            $query_tenant = "SELECT `t`.*, `tuser`.`id` AS `tuId`, `tuser`.`id` AS `tuserId`, `tuser`.`tenantId`, `tuser`.`userId` AS `tenantuserId`, `tuser`.`suite_location`, `tuser`.`cc_enable`, `tuser`.`send_as`, `tuser`.`complete_notification`, `u`.`uid`, `u`.`firstName`, `u`.`lastName`, `u`.`phoneNumber` AS `userpNumber`, `u`.`email`, `u`.`role_id`, `u`.`userName`, `st`.`state` AS `statename` FROM `tenant` AS `t` INNER JOIN `tenantUsers` AS `tuser` ON tuser.tenantId = t.id LEFT JOIN `users` AS `u` ON u.uid = tuser.userId LEFT JOIN `states` AS `st` ON st.state_code = t.state_code WHERE (tuser.userId='" . $uid . "')";
            $tenant_sql = mysql_query($query_tenant);
            $tenant_data = mysql_fetch_array($tenant_sql);

            $query = "SELECT `wo`.*, `t`.`tenantName`, `t`.`tenantContact`, `bu`.`buildingName`, `cat`.`categoryName`, `wop`.`wo_status`, `wop`.`internal_note`, `u`.`firstName`, `u`.`lastName`, `u`.`email`
				FROM `work_order` AS `wo`
				INNER JOIN `tenant` AS `t` ON t.id = wo.tenant
				LEFT JOIN `buildings` AS `bu` ON bu.build_id = wo.building
				LEFT JOIN `category` AS `cat` ON cat.cat_id = wo.category
				LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update=1
				LEFT JOIN `users` AS `u` ON wo.create_user = u.uid
				WHERE (wo.tenant='" . $tenant_data['id'] . "') AND (wo.master_internal_work_order!=1) " . $condition . "
				ORDER BY `woId` DESC";

            //echo $query;

            $wo_query = mysql_query($query);
            $work_rows = "";
            $wocnt = 0;
            while ($wo_row = mysql_fetch_array($wo_query)) {
                $tenantName = $wo_row['tenantName'];
                $buildingName = $wo_row['buildingName'];
                $description = $wo_row['work_order_request'];
                $wo_status = $wo_row['wo_status'];
                $wo_id = $wo_row['woId'];
                $wo_number = $wo_row['wo_number'];
                $wocnt++;

                if ($wocnt % 2 == 0) {
                    $work_rows .= "<tr onclick='getDetail(" . $wo_id . ");' class='vt_set_yellow'><td>" . $wo_number . "</td><td>" . $buildingName . "</td><td>" . $tenantName . "</td><td>" . $description . "</td><td>";
                    $schedule_query = "SELECT `priority_schedule`.* FROM `priority_schedule` WHERE (priority_id = '" . $wo_row['prioritySchedule'] . "') AND (start_status = '" . $wo_status . "')";
                    $schedule_sql = mysql_query($schedule_query);
                    $schedule = mysql_fetch_array($schedule_sql);

                    if ($wo_status == '1') {
                        $lastTime = $wo_row['date_requested'] . ' ' . $wo_row['time_requested'];
                    } else if ($wo_row['updated_at'] != '0000-00-00 00:00:00') {
                        $lastTime = $wo_row['updated_at'];
                    } else {
                        $lastTime = $wo_row['created_at'];
                    }
                    $currTime = date("Y-m-d h:i:s");
                    $lastTime = date("Y-m-d h:i:s", strtotime($lastTime));
                    $timeDiff = strtotime($currTime) - strtotime($lastTime);

                    $time = $schedule['Time'];
                    $length = $schedule['length'];
                    $diff = $timeDiff;

                    $reminderStatus = "";
                    $time_factor = array('1' => 60, '2' => 3600, '3' => 86400, '4' => 604800, '5' => 2592000, '6' => 31536000);
                    if (isset($time_factor[$length])) {
                        $cal_time = intval($diff / $time_factor[$length]);
                        if ($cal_time >= $time) {
                            $reminderStatus = true;
                        } else {
                            $reminderStatus = false;
                        }
                    }

                    if ($reminderStatus && ($wo_status != '6' && $wo_status != '7')) {
                        $work_rows .= $reminderStatus . " <img src='img/alert.png' alt=''/>";
                    } else {
                        if ($wo_status == '1') {
                            $work_rows .= " <img src='img/bell-icon.png' alt=''/>";
                        }
                    }

                    $work_rows .= "</td></tr>";
                } else {
                    $work_rows .= "<tr onclick='getDetail(" . $wo_id . ");' class='vt_set_purple'><td>" . $wo_number . "</td><td>" . $buildingName . "</td><td>" . $tenantName . "</td><td>" . $description . "</td><td>";
                    $schedule_query = "SELECT `priority_schedule`.* FROM `priority_schedule` WHERE (priority_id = '" . $wo_row['prioritySchedule'] . "') AND (start_status = '" . $wo_status . "')";
                    $schedule_sql = mysql_query($schedule_query);
                    $schedule = mysql_fetch_array($schedule_sql);

                    if ($wo_status == '1') {
                        $lastTime = $wo_row['date_requested'] . ' ' . $wo_row['time_requested'];
                    } else if ($wo_row['updated_at'] != '0000-00-00 00:00:00') {
                        $lastTime = $wo_row['updated_at'];
                    } else {
                        $lastTime = $wo_row['created_at'];
                    }
                    $currTime = date("Y-m-d h:i:s");
                    $lastTime = date("Y-m-d h:i:s", strtotime($lastTime));
                    $timeDiff = strtotime($currTime) - strtotime($lastTime);

                    $time = $schedule['Time'];
                    $length = $schedule['length'];
                    $diff = $timeDiff;

                    $reminderStatus = "";
                    $time_factor = array('1' => 60, '2' => 3600, '3' => 86400, '4' => 604800, '5' => 2592000, '6' => 31536000);
                    if (isset($time_factor[$length])) {
                        $cal_time = intval($diff / $time_factor[$length]);
                        if ($cal_time >= $time) {
                            $reminderStatus = true;
                        } else {
                            $reminderStatus = false;
                        }
                    }

                    if ($reminderStatus && ($wo_status != '6' && $wo_status != '7')) {
                        $work_rows .= $reminderStatus . " <img src='img/alert.png' alt=''/>";
                    } else {
                        if ($wo_status == '1') {
                            $work_rows .= " <img src='img/bell-icon.png' alt=''/>";
                        }
                    }

                    $work_rows .= "</td></tr>";
                }
            }

        } else {
            if ($role_id == '9') {
                $query = "SELECT `bu`.*, `st`.`state` AS `statename`, `bst`.`state` AS `billstatename` FROM `buildings` AS `bu` LEFT JOIN `states` AS `st` ON st.state_code = bu.state_code LEFT JOIN `states` AS `bst` ON bst.state_code = bu.billState_code WHERE (bu.cust_id='" . $cust_id . "') AND (bu.status='1')";
                $a1_res = mysql_query($query);
                //$companyListing = mysql_fetch_array($res);
            } else {
                if ($role_id == '1') {
                    $sql = "SELECT ubma.*, (select count(*) from user_building_module_access where building_id=ubma.building_id) as total FROM `user_building_module_access` as ubma";
                } else {
                    $sql = "SELECT ubma.*, (select count(*) from user_building_module_access where building_id=ubma.building_id) as total FROM `user_building_module_access` as ubma WHERE ubma.user_id=" . $uid;
                }

                $res = mysql_query($sql);

                //if($buildinglists)
                //{
                $build_id_array = array();
                while ($row = mysql_fetch_array($res)) {
                    $build_id_array[] = $row['building_id'];
                }
                //foreach($buildinglists as $buildlist)
                //$build_id_array[] = $buildlist['building_id'];

                $a1 = "SELECT `buildings`.`build_id`, `buildings`.`cust_id`, `buildings`.`buildingName`, `buildings`.`user_id` FROM `buildings` WHERE (status='1') AND build_id in (" . implode(',', $build_id_array) . ")";

                //print_r($build_id_array); exit;
                $a1_res = mysql_query($a1);
                //$companyListing = mysql_fetch_array($a1_res);
                //}
            }
            $build_ID = '';
            //if($companyListing!='')
            //{
            if ($build_ID == '') {
                $buildIds = array();
                while ($row = mysql_fetch_array($a1_res)) {
                    $buildIds[] = $row['build_id'];
                }

                $query = "SELECT `wo`.*, `t`.`tenantName`, `t`.`tenantContact`, `bu`.`buildingName`, `cat`.`categoryName`, `cat`.`prioritySchedule`, `wop`.`wo_status`, `wop`.`internal_note`, `wop`.`wo_request`, `wop`.`created_at` AS `created_date`, `wop`.`updated_at` AS `updated_date`, `u`.`firstName`, `u`.`lastName`, `u`.`email`
							 FROM `work_order` AS `wo`
							 INNER JOIN `tenant` AS `t` ON t.id = wo.tenant
							 LEFT JOIN `buildings` AS `bu` ON bu.build_id = wo.building
							 LEFT JOIN `category` AS `cat` ON cat.cat_id = wo.category
							 LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1
							 LEFT JOIN `users` AS `u` ON wo.create_user = u.uid
							 WHERE (wo.building in (" . implode(',', $buildIds) . ")) " . $condition . "
							 ORDER BY `woId` DESC";
                //print_r($buildIds);
                $wo_query = mysql_query($query);
                $work_rows = "";
                $wocnt = 0;
                while ($wo_row = mysql_fetch_array($wo_query)) {

                    /******************* set timezone through building id ****************************/
                    $ob = new Helper();
                    $ob->setTimezone($wo_row['building']);
                    /******************* end - set timezone through building id ****************************/

                    $tenantName = $wo_row['tenantName'];
                    $buildingName = $wo_row['buildingName'];
                    $description = $wo_row['work_order_request'];
                    $wo_status = $wo_row['wo_status'];
                    $wo_id = $wo_row['woId'];
                    $wo_number = $wo_row['wo_number'];
                    $wocnt++;

                    if ($wocnt % 2 == 0) {
                        $work_rows .= "<tr onclick='getDetail(" . $wo_id . ");' class='vt_set_yellow'><td>" . $wo_number . "</td><td>" . $buildingName . "</td><td>" . $tenantName . "</td><td>" . $description . "</td><td>";
                        $schedule_query = "SELECT `priority_schedule`.* FROM `priority_schedule` WHERE (priority_id = '" . $wo_row['prioritySchedule'] . "') AND (start_status = '" . $wo_status . "')";
                        $schedule_sql = mysql_query($schedule_query);
                        $schedule = mysql_fetch_array($schedule_sql);

                        if ($wo_status == '1') {
                            $lastTime = $wo_row['date_requested'] . ' ' . $wo_row['time_requested'];
                        } else if ($wo_row['updated_at'] != '0000-00-00 00:00:00') {
                            $lastTime = $wo_row['updated_at'];
                        } else {
                            $lastTime = $wo_row['created_at'];
                        }
                        $currTime = date("Y-m-d h:i:s");
                        $lastTime = date("Y-m-d h:i:s", strtotime($lastTime));
                        $timeDiff = strtotime($currTime) - strtotime($lastTime);

                        $time = $schedule['Time'];
                        $length = $schedule['length'];
                        $diff = $timeDiff;

                        $reminderStatus = "";
                        $time_factor = array('1' => 60, '2' => 3600, '3' => 86400, '4' => 604800, '5' => 2592000, '6' => 31536000);
                        if (isset($time_factor[$length])) {
                            $cal_time = intval($diff / $time_factor[$length]);
                            if ($cal_time >= $time) {
                                $reminderStatus = true;
                            } else {
                                $reminderStatus = false;
                            }
                        }

                        if ($reminderStatus && ($wo_status != '6' && $wo_status != '7')) {
                            $work_rows .= " <img src='img/alert.png' alt=''/>";
                        } else {
                            if ($wo_status == '1') {
                                $work_rows .= " <img src='img/bell-icon.png' alt=''/>";
                            }
                        }

                        $work_rows .= "</td></tr>";
                    } else {
                        $work_rows .= "<tr onclick='getDetail(" . $wo_id . ");' class='vt_set_purple'><td>" . $wo_number . "</td><td>" . $buildingName . "</td><td>" . $tenantName . "</td><td>" . $description . "</td><td>";
                        $schedule_query = "SELECT `priority_schedule`.* FROM `priority_schedule` WHERE (priority_id = '" . $wo_row['prioritySchedule'] . "') AND (start_status = '" . $wo_status . "')";
                        //echo $schedule_query;
                        $schedule_sql = mysql_query($schedule_query);
                        $schedule = mysql_fetch_array($schedule_sql);

                        if ($wo_status == '1') {
                            $lastTime = $wo_row['date_requested'] . ' ' . $wo_row['time_requested'];
                        } else if ($wo_row['updated_at'] != '0000-00-00 00:00:00') {
                            $lastTime = $wo_row['updated_at'];
                        } else {
                            $lastTime = $wo_row['created_at'];
                        }
                        $currTime = date("Y-m-d h:i:s");
                        $lastTime = date("Y-m-d h:i:s", strtotime($lastTime));
                        $timeDiff = strtotime($currTime) - strtotime($lastTime);

                        $time = $schedule['Time'];
                        $length = $schedule['length'];
                        $diff = $timeDiff;

                        $reminderStatus = "";
                        $time_factor = array('1' => 60, '2' => 3600, '3' => 86400, '4' => 604800, '5' => 2592000, '6' => 31536000);
                        if (isset($time_factor[$length])) {
                            $cal_time = intval($diff / $time_factor[$length]);
                            if ($cal_time >= $time) {
                                $reminderStatus = true;
                            } else {
                                $reminderStatus = false;
                            }
                        }

                        if ($reminderStatus && ($wo_status != '6' && $wo_status != '7')) {
                            $work_rows .= " <img src='img/alert.png' alt=''/>";
                        } else {
                            if ($wo_status == '1') {
                                $work_rows .= " <img src='img/bell-icon.png' alt=''/>";
                            }
                        }

                        $work_rows .= "</td></tr>";
                    }
                }

            } else {

                $query = "SELECT `wo`.*, `t`.`tenantName`, `t`.`tenantContact`, `bu`.`buildingName`, `cat`.`categoryName`, `cat`.`prioritySchedule`, `wop`.`wo_status`, `wop`.`internal_note`, `wop`.`wo_request`, `wop`.`created_at` AS `created_date`, `wop`.`updated_at` AS `updated_date`, `u`.`firstName`, `u`.`lastName`, `u`.`email`
							FROM `work_order` AS `wo`
							INNER JOIN `tenant` AS `t` ON t.id = wo.tenant
							LEFT JOIN `buildings` AS `bu` ON bu.build_id = wo.building
							LEFT JOIN `category` AS `cat` ON cat.cat_id = wo.category
							LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update=1
							LEFT JOIN `users` AS `u` ON wo.create_user = u.uid
							WHERE (wo.building='" . $build_ID . "') " . $condition . "
							ORDER BY `woId` DESC";

                $wo_query = mysql_query($query);
                $work_rows = "";
                $wocnt = 0;
                while ($wo_row = mysql_fetch_array($wo_query)) {
                    $tenantName = $wo_row['tenantName'];
                    $buildingName = $wo_row['buildingName'];
                    $description = $wo_row['work_order_request'];
                    $wo_status = $wo_row['wo_status'];
                    $wo_id = $wo_row['woId'];
                    $wo_number = $wo_row['wo_number'];
                    $wocnt++;

                    if ($wocnt % 2 == 0) {
                        $work_rows .= "<tr onclick='getDetail(" . $wo_id . ");' class='vt_set_yellow'><td>" . $wo_number . "</td><td>" . $buildingName . "</td><td>" . $tenantName . "</td><td>" . $description . "</td><td>";
                        $schedule_query = "SELECT `priority_schedule`.* FROM `priority_schedule` WHERE (priority_id = '" . $wo_row['prioritySchedule'] . "') AND (start_status = '" . $wo_status . "')";
                        $schedule_sql = mysql_query($schedule_query);
                        $schedule = mysql_fetch_array($schedule_sql);

                        if ($wo_status == '1') {
                            $lastTime = $wo_row['date_requested'] . ' ' . $wo_row['time_requested'];
                        } else if ($wo_row['updated_at'] != '0000-00-00 00:00:00') {
                            $lastTime = $wo_row['updated_at'];
                        } else {
                            $lastTime = $wo_row['created_at'];
                        }
                        $currTime = date("Y-m-d h:i:s");
                        $lastTime = date("Y-m-d h:i:s", strtotime($lastTime));
                        $timeDiff = strtotime($currTime) - strtotime($lastTime);

                        $time = $schedule['Time'];
                        $length = $schedule['length'];
                        $diff = $timeDiff;

                        $reminderStatus = "";
                        $time_factor = array('1' => 60, '2' => 3600, '3' => 86400, '4' => 604800, '5' => 2592000, '6' => 31536000);
                        if (isset($time_factor[$length])) {
                            $cal_time = intval($diff / $time_factor[$length]);
                            if ($cal_time >= $time) {
                                $reminderStatus = true;
                            } else {
                                $reminderStatus = false;
                            }
                        }

                        if ($reminderStatus && ($wo_status != '6' && $wo_status != '7')) {
                            $work_rows .= $reminderStatus . " <img src='img/alert.png' alt=''/>";
                        } else {
                            if ($wo_status == '1') {
                                $work_rows .= " <img src='img/bell-icon.png' alt=''/>";
                            }
                        }

                        $work_rows .= "</td></tr>";
                    } else {
                        $work_rows .= "<tr onclick='getDetail(" . $wo_id . ");' class='vt_set_purple'><td>" . $wo_number . "</td><td>" . $buildingName . "</td><td>" . $tenantName . "</td><td>" . $description . "</td><td>";
                        $schedule_query = "SELECT `priority_schedule`.* FROM `priority_schedule` WHERE (priority_id = '" . $wo_row['prioritySchedule'] . "') AND (start_status = '" . $wo_status . "')";
                        $schedule_sql = mysql_query($schedule_query);
                        $schedule = mysql_fetch_array($schedule_sql);

                        if ($wo_status == '1') {
                            $lastTime = $wo_row['date_requested'] . ' ' . $wo_row['time_requested'];
                        } else if ($wo_row['updated_at'] != '0000-00-00 00:00:00') {
                            $lastTime = $wo_row['updated_at'];
                        } else {
                            $lastTime = $wo_row['created_at'];
                        }
                        $currTime = date("Y-m-d h:i:s");
                        $lastTime = date("Y-m-d h:i:s", strtotime($lastTime));
                        $timeDiff = strtotime($currTime) - strtotime($lastTime);

                        $time = $schedule['Time'];
                        $length = $schedule['length'];
                        $diff = $timeDiff;

                        $reminderStatus = "";
                        $time_factor = array('1' => 60, '2' => 3600, '3' => 86400, '4' => 604800, '5' => 2592000, '6' => 31536000);
                        if (isset($time_factor[$length])) {
                            $cal_time = intval($diff / $time_factor[$length]);
                            if ($cal_time >= $time) {
                                $reminderStatus = true;
                            } else {
                                $reminderStatus = false;
                            }
                        }

                        if ($reminderStatus && ($wo_status != '6' && $wo_status != '7')) {
                            $work_rows .= " <img src='img/alert.png' alt=''/>";
                        } else {
                            if ($wo_status == '1') {
                                $work_rows .= " <img src='img/bell-icon.png' alt=''/>";
                            }
                        }

                        $work_rows .= "</td></tr>";
                    }
                }
            }
            //}

            //$work_rows = "<tr class='vt_set_yellow'><td colspan='5'>Data not found for this User !</td></tr>";
        }

        return $work_rows;
    }

    function getWorkOrderDetail($role_id, $uid, $cust_id, $woid) {
        $query = "SELECT `wo`.*, `wop`.*, `t`.`tenantName`, `t`.`tenantContact`, `bu`.`buildingName`, `cat`.`categoryName`, `cat`.`send_email`, `pt`.`priorityName`, `pt`.`pid`, `u`.`firstName`, `u`.`lastName`, `u`.`email`, `u`.`phoneNumber`, `tu`.`suite_location` AS `tenant_suite`,`wf`.`file_name` as attachment
			FROM `work_order` AS `wo`
			INNER JOIN `tenant` AS `t` ON t.id = wo.tenant
			LEFT JOIN `buildings` AS `bu` ON bu.build_id = wo.building
			LEFT JOIN `category` AS `cat` ON cat.cat_id = wo.category
			LEFT JOIN `priority` AS `pt` ON pt.pid = cat.prioritySchedule
			LEFT JOIN `users` AS `u` ON wo.create_user = u.uid
			LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update=1
			LEFT JOIN `tenantUsers` AS `tu` ON wo.create_user = tu.userId
			LEFT JOIN `wo_files` AS `wf` ON wf.woId = wo.woId
			WHERE (wo.woId='" . $woid . "')";
        $sql = mysql_query($query);

        $data = mysql_fetch_array($sql);

        return $data;

    }

    function checkWorkOrderExist($role_id, $uid, $cust_id, $wo_number, $building_id) {

        $query = "SELECT `wo`.*, `t`.`tenantName`, `t`.`tenantContact`, `bu`.`buildingName`, `cat`.`categoryName`, `cat`.`prioritySchedule`, `wop`.`wo_status`, `wop`.`internal_note`, `wop`.`wo_request`, `wop`.`created_at` AS `created_date`, `wop`.`updated_at` AS `updated_date`, `u`.`firstName`, `u`.`lastName`, `u`.`email`
					FROM `work_order` AS `wo`
					INNER JOIN `tenant` AS `t` ON t.id = wo.tenant
					LEFT JOIN `buildings` AS `bu` ON bu.build_id = wo.building
					LEFT JOIN `category` AS `cat` ON cat.cat_id = wo.category
					LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update=1
					LEFT JOIN `users` AS `u` ON wo.create_user = u.uid
					WHERE (wo.building='" . $building_id . "')  AND  ( wo.wo_number = '" . $wo_number . "' OR  t.tenantName LIKE '" . $wo_number . "%' OR bu.buildingName LIKE '" . $wo_number . "%')
					ORDER BY `woId` DESC";

        //echo $query;
        //$query = "SELECT woId from work_order where building = '".$building_id."' AND (wo_number = '".$wo_number."' OR wo_number = '".$wo_number."')";
        $sql = mysql_query($query);
        $cnt = mysql_num_rows($sql);
        if ($cnt == 1) {
            $data = array();
            $data_arr = mysql_fetch_array($sql);
            $data[] = $data_arr['woId'];
            return $data;
        } else if ($cnt > 1) {
            $data = array();
            while ($row = mysql_fetch_array($sql)) {
                $data[] = $row['woId'];
            }
            return $data;
        } else {
            return "FALSE";
        }

    }

    function getDescriptionOfWork($woid) {

        $query = "select description from work_description where woId = '" . $woid . "' ORDER BY id DESC";

        //echo $query;

        $sql = mysql_query($query);
        $cnt = mysql_num_rows($sql);
        $evenodd = 0;
        $html = "";
        if ($cnt > 0) {
            while ($row = mysql_fetch_array($sql)) {
                if (($evenodd % 2) == 0) {
                    $evenodd++;
                    $html .= "<tr style='background:#f9f9f9 ;'><td>" . $evenodd . "</td><td>" . $row['description'] . "</td></tr>";
                } else {
                    $evenodd++;
                    $html .= "<tr style='background:#f1f1f1 ;'><td>" . $evenodd . "</td><td>" . $row['description'] . "</td></tr>";
                }
            }
        } else {
            $html .= "<tr style='background:#f1f1f1;'><td style='text-align:center;' colspan='2'>No description found !</td></tr>";
        }

        return $html;

    }

    function getLabor($woid) {

        $query = "SELECT br.brid,`l`.`lid`, `l`.`woId`, `l`.`emp_id`, `l`.`charge_hour`, `l`.`rate_charge`, `l`.`job_time`, `u`.`uid`, `u`.`firstName`, `u`.`lastName`, `br`.`description`, `br`.`multiplier`
			FROM `labor` AS `l`
			LEFT JOIN `users` AS `u` ON u.uid = l.emp_id
			LEFT JOIN `bill_rate` AS `br` ON l.rate_charge = br.brid
			WHERE (l.woId = '" . $woid . "')";

        //echo $query;
        $rate_charge = 5;
        $sql = mysql_query($query);
        $cnt = mysql_num_rows($sql);
        $evenodd = 0;
        $html = "";
        $total_labor_charge = 0.00;
        if ($cnt > 0) {
            while ($row = mysql_fetch_array($sql)) {
                /****start calculation of total labor charge ***/
                $lab_charge = 0;
                $time = explode(":", $row['job_time']);
                $jb_min = (isset($time[1])) ? $time[1] : '00';
                $jb_time = $time[0] * 60 + $jb_min;
                $lab_charge = number_format(($row['charge_hour'] / 60) * $row['multiplier'] * $jb_time, '2', '.', '');
                $total_labor_charge = $total_labor_charge + $lab_charge;
                /******end calculation of total labor charge *******/

                $totallabourcost = $totallabourcost + $row['charge_hour'];
                if (($evenodd % 2)) {
                    $evenodd++;
                    $html .= "<tr style='background:#f9f9f9 ;'><td>" . $evenodd . "</td><td>" . $row['firstName'] . " " . $row['lastName'] . "</td><td>$" . $row['charge_hour'] . "</td><td>" . $row['description'] . "</td><td>" . $row['job_time'] . "</td><td width='50'><a onclick=\"updateLabourDataTemp('" . $row['emp_id'] . "', '" . $row['charge_hour'] . "', '" . $rate_charge . "', '" . $row['brid'] . "','" . $row['job_time'] . "', '" . $row['woId'] . "', '" . $row['lid'] . "')\"><img src='img/edit-icon.png'/></a> | <a onclick=\"deleteLabourTemp('" . $row['woId'] . "','" . $row['lid'] . "')\"><img src='img/delete-icon.png'/></a></td></tr>";
                } else {
                    $evenodd++;
                    $html .= "<tr style='background:#f1f1f1 ;'><td>" . $evenodd . "</td><td>" . $row['firstName'] . " " . $row['lastName'] . "</td><td>$" . $row['charge_hour'] . "</td><td>" . $row['description'] . "</td><td>" . $row['job_time'] . "</td><td width='50'><a onclick=\"updateLabourDataTemp('" . $row['emp_id'] . "', '" . $row['charge_hour'] . "', '" . $rate_charge . "', '" . $row['brid'] . "','" . $row['job_time'] . "', '" . $row['woId'] . "', '" . $row['lid'] . "')\"><img src='img/edit-icon.png'/></a> | <a onclick=\"deleteLabourTemp('" . $row['woId'] . "','" . $row['lid'] . "')\"><img src='img/delete-icon.png'/></a></td></tr>";
                }
            }
        } else {
            $html .= "<tr style='background:#f1f1f1;'><td style='text-align:center;' colspan='6'>No labor data found !</td></tr>";
        }

        return compact("html", "total_labor_charge");

    }

    function getBuildingServices($woid) {

        $query = "SELECT `bs`.`bsId`, `bs`.`woId`, `bs`.`service`, `bs`.`charge`, `bs`.`amount_requested`, `bs`.`comment`, `bserv`.`service_name`, `bserv`.`unit_measure`, `bserv`.`minimum` , `bserv`.`building`
			FROM `building_service` AS `bs`
			LEFT JOIN `build_service` AS `bserv` ON bserv.bsid = bs.service
			WHERE (bs.woId = '" . $woid . "')";

        //echo $query; exit;

        $sql = mysql_query($query);
        $cnt = mysql_num_rows($sql);
        $evenodd = 0;
        $html = "";
        $total_bs_charge = 0;
        if ($cnt > 0) {
            while ($row = mysql_fetch_array($sql)) {
                $bs_charge = 0;
                $bs_charge = number_format(($row['charge'] * $row['amount_requested']), '2', '.', '');
                $total_bs_charge = $total_bs_charge + $bs_charge;

                if (($evenodd % 2)) {
                    $evenodd++;
                    $html .= "<tr style='background:#f9f9f9 ;'><td>" . $evenodd . "</td><td>" . $row['service_name'] . "</td><td>$" . $row['charge'] . "</td><td>" . $row['unit_measure'] . "</td><td>" . $row['amount_requested'] . "</td><td>" . $row['comment'] . "</td><td width='50'><a onclick=\"updateBServiceDataTemp('" . $row['bsId'] . "', '" . $row['service'] . "', '" . $row['charge'] . "', '" . $row['amount_requested'] . "', '" . $row['comment'] . "', '" . $row['woId'] . "', '" . str_replace("'", ";-", $row['service_name']) . "', '" . $row['building'] . "')\"><img src='img/edit-icon.png'/></a> | <a onclick=\"deleteBserviceTemp('" . $row['bsId'] . "')\"><img src='img/delete-icon.png'/></a></td></tr>";
                } else {
                    $evenodd++;
                    $html .= "<tr style='background:#f1f1f1 ;'><td>" . $evenodd . "</td><td>" . $row['service_name'] . "</td><td>$" . $row['charge'] . "</td><td>" . $row['unit_measure'] . "</td><td>" . $row['amount_requested'] . "</td><td>" . $row['comment'] . "</td><td width='50'><a onclick=\"updateBServiceDataTemp('" . $row['bsId'] . "', '" . $row['service'] . "', '" . $row['charge'] . "', '" . $row['amount_requested'] . "', '" . $row['comment'] . "', '" . $row['woId'] . "', '" . str_replace("'", ";-", $row['service_name']) . "', '" . $row['building'] . "')\"><img src='img/edit-icon.png'/></a> | <a onclick=\"deleteBserviceTemp('" . $row['bsId'] . "')\"><img src='img/delete-icon.png'/></a></td></tr>";
                }
            }
        } else {
            $html .= "<tr style='background:#f1f1f1;'><td style='text-align:center;' colspan='7'>No building services found !</td></tr>";
        }

        return compact("html", "total_bs_charge");

    }

    function getMaterials($woid) {

        $query = "SELECT `mc`.`mcId`,`m`.`mid`, `mc`.`woId`, `mc`.`material_id`, `mc`.`cost`, `mc`.`quantity`, `mc`.`markup`, `mc`.`tax`, `m`.`mid`, `m`.`description`
			FROM `material_charge` AS `mc`
			LEFT JOIN `material` AS `m` ON m.mid = mc.material_id
			WHERE (mc.woId = '" . $woid . "')";

        //echo $query;

        $sql = mysql_query($query);
        $cnt = mysql_num_rows($sql);
        $evenodd = 0;
        $html = "";
        $total_material_charge = 0.00;
        $tot_material_tax = 0;
        $tot_mat_mkp = 0;
        if ($cnt > 0) {
            while ($row = mysql_fetch_array($sql)) {
                /****start calculation of total labor charge ***/
                $mcharge = 0;
                $mat_mkp = 0;
                $mat_tax = 0;
                $mcharge = ($row['cost'] * $row['quantity']);
                $mat_mkp = number_format(($mcharge * $row['markup']) / 100, 2, '.', '');
                $total_material_charge = $total_material_charge + $mcharge;
                $tot_mat_mkp = $tot_mat_mkp + $mat_mkp;
                if ($row['tax'] == '1') {
                    //$mat_tax = number_format((($mcharge + $mat_mkp) * $wpData['sale_tax']) / 100, 2, '.', '');
                }

                $tot_material_tax = $tot_material_tax + $mat_tax;

                /******end calculation of total labor charge *******/

                $val_tax = $row['tax'];
                if ($val_tax == 1) {
                    $tax = "Yes";
                } else {
                    $tax = "No";
                }

                if (($evenodd % 2)) {
                    $evenodd++;
                    $html .= "<tr style='background:#f9f9f9 ;'><td>" . $evenodd . "</td><td>" . $row['description'] . "</td><td>$" . $row['cost'] . "</td><td>" . $row['quantity'] . "</td><td>" . $row['markup'] . "</td><td>" . $tax . "</td></td><td width='50'><a onclick=\"updateMaterialsDataTemp('" . $row['cost'] . "', '" . $row['quantity'] . "', '" . $val_tax . "', '" . $row['markup'] . "','" . $row['mid'] . "', '" . $row['woId'] . "', '" . $row['mcId'] . "')\"><img src='img/edit-icon.png'/></a> | <a onclick=\"deleteMaterialTemp('" . $row['woId'] . "','" . $row['mcId'] . "')\"><img src='img/delete-icon.png'/></a></td></tr>";
                } else {
                    $evenodd++;
                    $html .= "<tr style='background:#f1f1f1 ;'><td>" . $evenodd . "</td><td>" . $row['description'] . "</td><td>$" . $row['cost'] . "</td><td>" . $row['quantity'] . "</td><td>" . $row['markup'] . "</td><td>" . $tax . "</td><td width='50'><a onclick=\"updateMaterialsDataTemp('" . $row['cost'] . "', '" . $row['quantity'] . "', '" . $val_tax . "', '" . $row['markup'] . "','" . $row['mid'] . "', '" . $row['woId'] . "', '" . $row['mcId'] . "')\"><img src='img/edit-icon.png'/></a> | <a onclick=\"deleteMaterialTemp('" . $row['woId'] . "','" . $row['mcId'] . "')\"><img src='img/delete-icon.png'/></a></td></tr>";
                }
            }
        } else {
            $html .= "<tr style='background:#f1f1f1;'><td style='text-align:center;' colspan='7'>No materials data found !</td></tr>";
        }

        return compact("html", "total_material_charge");

    }

    function getOutsideServices($woid) {

        $query = "SELECT `os`.`osId`,v.vid, `os`.`woId`, `os`.`vendor`, `os`.`job_cost`, `os`.`job_description`, `os`.`markup`, `os`.`tax`, `v`.`vid`, `v`.`company_name`
			FROM `outside_service` AS `os`
			LEFT JOIN `vendor` AS `v` ON v.vid = os.vendor
			WHERE (os.woId = '" . $woid . "')";

        //echo $query;

        $sql = mysql_query($query);
        $cnt = mysql_num_rows($sql);
        $evenodd = 0;
        $total_outside_charge = 0.00;
        $tot_outside_tax = 0;
        $tot_outside_mkp = 0;
        $html = "";
        if ($cnt > 0) {
            while ($row = mysql_fetch_array($sql)) {
                /****start calculation of total outside service charge ***/
                $mcharge = 0;
                $os_mkp = 0;
                $osl_tax = 0;
                $osl_cost = $row['job_cost'];
                $os_mkp = number_format(($osl_cost * $row['markup']) / 100, 2, '.', '');
                $total_outside_charge = $total_outside_charge + $osl_cost;
                $tot_outside_mkp = $tot_outside_mkp + $os_mkp;
                if ($row['tax'] == '1') {
                    //$osl_tax = number_format((($osl_cost + $os_mkp)*$wpData['sale_tax'])/100,2,'.','');
                }

                $tot_outside_tax = $tot_outside_tax + $osl_tax;

                /******end calculation of total labor charge *******/

                if ($row['tax'] == 1) {
                    $tax = "Yes";
                } else {
                    $tax = "No";
                }

                if (($evenodd % 2)) {
                    $evenodd++;
                    $html .= "<tr style='background:#f9f9f9 ;'><td>" . $evenodd . "</td><td>" . $row['company_name'] . "</td><td>" . $row['job_description'] . "</td><td>$" . $row['job_cost'] . "</td><td>" . $row['markup'] . "</td><td>" . $tax . "</td><td width='50'><a onclick=\"updateOutsideServiceDataTemp('" . $row['vid'] . "', '" . $row['job_description'] . "', '" . $row['job_cost'] . "', '" . $row['markup'] . "','" . $tax . "', '" . $row['woId'] . "', '" . $row['osId'] . "')\"><img src='img/edit-icon.png'/></a> | <a onclick=\"deleteOutsideTemp('" . $row['woId'] . "','" . $row['osId'] . "')\"><img src='img/delete-icon.png'/></a></td></tr>";
                } else {
                    $evenodd++;
                    $html .= "<tr style='background:#f1f1f1 ;'><td>" . $evenodd . "</td><td>" . $row['company_name'] . "</td><td>" . $row['job_description'] . "</td><td>$" . $row['job_cost'] . "</td><td>" . $row['markup'] . "</td><td>" . $tax . "</td><td width='50'><a onclick=\"updateOutsideServiceDataTemp('" . $row['vid'] . "', '" . $row['job_description'] . "', '" . $row['job_cost'] . "', '" . $row['markup'] . "','" . $tax . "', '" . $row['woId'] . "', '" . $row['osId'] . "')\"><img src='img/edit-icon.png'/></a> | <a onclick=\"deleteOutsideTemp('" . $row['woId'] . "','" . $row['osId'] . "')\"><img src='img/delete-icon.png'/></a></td></tr>";
                }
            }
        } else {
            $html .= "<tr style='background:#f1f1f1;'><td style='text-align:center;' colspan='7'>No outside service data found !</td></tr>";
        }

        return compact("html", "total_outside_charge");

    }

    function getNotes($woid) {

        $query = "SELECT `wo_note`.* FROM `wo_note` WHERE (woId = '" . $woid . "' )";

        //echo $query;

        $sql = mysql_query($query);
        $cnt = mysql_num_rows($sql);
        $evenodd = 0;
        $html = "";
        if ($cnt > 0) {
            while ($row = mysql_fetch_array($sql)) {

                $date = date('m/d/Y', strtotime($row['note_date']));

                if ($row['internal'] == 1) {
                    $internal = "Yes";
                } else {
                    $internal = "No";
                }

                if (($evenodd % 2)) {
                    $evenodd++;
                    $html .= "<tr style='background:#f9f9f9 ;'><td>" . $evenodd . "</td><td>" . $date . "</td><td>" . $row['note'] . "</td><td>" . $internal . "</td><td width='50'><a onclick=\"updateNotesDataTemp('" . $date . "', '" . $row['note'] . "', '" . $internal . "', '" . $row['woId'] . "', '" . $row['wnId'] . "')\"><img src='img/edit-icon.png'/></a> | <a onclick=\"deleteNoteTemp('" . $row['wnId'] . "')\"><img src='img/delete-icon.png'/></a></td></tr>";
                } else {
                    $evenodd++;
                    $html .= "<tr style='background:#f1f1f1 ;'><td>" . $evenodd . "</td><td>" . $date . "</td><td>" . $row['note'] . "</td><td>" . $internal . "</td><td width='50'><a onclick=\"updateNotesDataTemp('" . $date . "', '" . $row['note'] . "', '" . $internal . "', '" . $row['woId'] . "', '" . $row['wnId'] . "')\"><img src='img/edit-icon.png'/></a> | <a onclick=\"deleteNoteTemp('" . $row['wnId'] . "')\"><img src='img/delete-icon.png'/></a></td></tr>";
                }
            }
        } else {
            $html .= "<tr style='background:#f1f1f1;'><td style='text-align:center;' colspan='6'>No Note found !</td></tr>";
        }

        return $html;

    }

    function updateWorkOrderStatus($woid, $role_id, $uid, $cust_id, $wo_status, $new_wo_status, $building_id) {

        /******************* set timezone through building id ****************************/
        $ob = new Helper();
        $ob->setTimezone($building_id);
        /******************* end - set timezone through building id ****************************/
        $created_at = date('Y-m-d H:i:s');

        $query = "insert into work_order_update (wo_id, wo_status, billable_opt, created_at, current_update, user_id) values('" . $woid . "', '" . $new_wo_status . "', '0', '" . $created_at . "', '1', '" . $uid . "')";
        $sql = mysql_query($query);

        if ($sql) {
            $q = "select upId from work_order_update where user_id = '" . $uid . "' ORDER BY upId DESC LIMIT 1 ";
            $q_sql = mysql_query($q);
            $q_row = mysql_fetch_array($q_sql);

            $query = "update work_order_update set current_update = '0' where upId != '" . $q_row['upId'] . "' AND wo_id = '" . $woid . "'";

            $update_sql = mysql_query($query);

            if ($update_sql) {
                $query_history = "insert into wo_history_log (woId, log_type, current_value, change_value, user_id, created_at) values('" . $woid . "', 'status', '" . $wo_status . "', '" . $new_wo_status . "', '" . $uid . "', '" . $created_at . "')";

                $update_sql_history = mysql_query($query_history);

                if ($update_sql_history) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;

    }

    function getBuildingsByUser($role_id, $uid, $cust_id) {

        $query = "SELECT `bu`.*, `st`.`state` AS `statename`, `bst`.`state` AS `billstatename`
		FROM `buildings` AS `bu`
		LEFT JOIN `states` AS `st` ON st.state_code = bu.state_code
		LEFT JOIN `states` AS `bst` ON bst.state_code = bu.billState_code
		WHERE (bu.cust_id='" . $cust_id . "')
		AND (bu.status='1')
		AND bu.build_id IN (select DISTINCT building_id from user_building_module_access where user_id = '" . $uid . "')";
        //echo $query;
        $sql = mysql_query($query);
        $option_building = "";
        //$option_building .= "<option value=''>Select Building</option>";
        $cnt = 0;
        $first_building = "";
        while ($row = mysql_fetch_array($sql)) {
            $cnt++;
            if ($cnt == 1) {
                $first_building = $row['build_id'];
            }
            $option_building .= "<option value='" . $row['build_id'] . "'>" . $row['buildingName'] . "</option>";
        }

        return $option_building . ":" . $first_building;
    }

    function getCompanyByUser($role_id, $uid, $cust_id, $build_id) {

        $query = "SELECT `t`.*, `u`.`uid`, `u`.`firstName` AS `firstname`, `u`.`lastName` AS `lastname`, `u`.`phoneNumber` AS `phonenumber`, `u`.`email`, `u`.`role_id`
		FROM `tenant` AS `t`
		LEFT JOIN `users` AS `u` ON u.uid = t.userId
		WHERE (t.buildingId='" . $build_id . "') AND (t.remove_status=0)";
        //$query = "select * from company where cust_id = '".$cust_id."'";
        //echo $query;
        $sql = mysql_query($query);
        $option_company = "";
        //$option_building .= "<option value=''>Select Building</option>";
        $cnt = 0;
        $first_companyuser = "";
        $option_company = '<option value="">--select--</option>';
        while ($row = mysql_fetch_array($sql)) {
            $cnt++;
            if ($cnt == 1) {
                $first_companyuser = $row['id'];
            }
            $option_company .= "<option value='" . $row['id'] . "'>" . $row['tenantName'] . "</option>";
        }

        return $option_company . ":" . $first_companyuser;
    }

    function getTenants($role_id, $uid, $cust_id, $tnadmin_id) {
        $query = "SELECT `u`.`uid`, `u`.`userName`, `u`.`firstName`, `u`.`lastName`, `u`.`email`, `u`.`role_id`, `u`.`phoneNumber`, `u`.`phoneExt`, `u`.`Title`, `u`.`status`, `tu`.`tenantId`, `tu`.`cc_enable`, `tu`.`send_as`, `tu`.`complete_notification`
		FROM `users` AS `u`
		INNER JOIN `tenantUsers` AS `tu` ON u.uid = tu.userId
		WHERE (u.remove_status = '0') AND (tu.tenantId = '" . $tnadmin_id . "')";
        $sql = mysql_query($query);
        $option_company = "";
        while ($row = mysql_fetch_array($sql)) {
            $option_company .= "<option value='" . $row['uid'] . "'>" . $row['firstName'] . ", " . $row['lastName'] . "</option>";
        }

        return $option_company;
    }

    function getCompanyCategory($role_id, $uid, $cust_id, $build_id) {
        $query = "SELECT `category`.*
		FROM `category`
		WHERE (building_id = '" . $build_id . "') AND (remove_status = 0) ORDER BY `categoryName` ASC";
        $sql = mysql_query($query);
        $company_category = "";
        while ($row = mysql_fetch_array($sql)) {
            $company_category .= "<option value='" . $row['cat_id'] . "'>" . $row['categoryName'] . "</option>";
        }

        return $company_category;
    }

    function getBuildingServicesPopUp($build_id) {
        $query = "SELECT `build_service`.* FROM `build_service` WHERE (building = '" . $build_id . "' ) AND (status = '1' )";

        $sql = mysql_query($query);
        $building_service = "";
        $building_service .= "<option value=''>-Select-</option>";
        while ($row = mysql_fetch_array($sql)) {
            $building_service .= "<option value='" . $row['bsid'] . "' flag_id='" . $row['unit_measure'] . "'>" . $row['service_name'] . "</option>";
        }
        return $building_service;
    }
    function getUpdateServicesCost($bsid) {
        $query = "SELECT `build_service`.* FROM `build_service` WHERE (bsid = '" . $bsid . "' ) AND (status = '1' )";
        $sql = mysql_query($query);
        $row = mysql_fetch_array($sql);
        $cost = $row['cost'];
        $minimum = $row['minimum'];
        return $cost . ";" . $minimum;
    }

    function getBuildingDateAndTime($role_id, $uid, $cust_id, $build_id) {
        /******************* set timezone through building id ****************************/
        $ob = new Helper();
        $timezone = date_default_timezone_get();
        $ob->setTimezone($build_id);
        /******************* end - set timezone through building id ****************************/

        $current_date = date('m-d-Y');
        $current_time = date("h:i:s A");
        return $current_date . ";" . $current_time . ";" . $timezone;
    }

    function createSaveWorkOrder($role_id, $uid, $cust_id, $buildings_list, $company_list, $tenantusers_list, $category_list, $current_date_field, $current_time_field, $internal_wo, $wo_request, $wo_notes, $file_name) {
        /******************* set timezone through building id ****************************/
        $ob = new Helper();
        $ob->setTimezone($buildings_list);
        /******************* end - set timezone through building id ****************************/
        $timezone = date_default_timezone_get();
        $current_date = date('Y-m-d');
        $current_time = date("h:i:s A");

        //$variable = $role_id.", ".$uid.", ".$cust_id.", ".$buildings_list.", ".$company_list.", ".$tenantusers_list.", ".$category_list.", ".$current_date_field.", ".$current_time_field.", ".$internal_wo.", ".$wo_request.", ".$wo_notes;
        //return $variable;

        $created_at = date('Y-m-d H:i:s');
        $current_date_field = $current_date;

        /////getting suit location
        $suite_query = "select suite_location from tenantUsers where tenantId = '" . $company_list . "'";
        //echo $suite_query; exit;
        $suite_sql = mysql_query($suite_query);
        $suite_data = mysql_fetch_array($suite_sql);
        $suite_location = $suite_data['suite_location'];
        /////end of getting suit location

        /////getting last wo Number
        $wo_number_query = "select wo_number from work_order where building = '" . $buildings_list . "' ORDER BY wo_number DESC LIMIT 1";
        $wo_number_sql = mysql_query($wo_number_query);
        $wo_number_data = mysql_fetch_array($wo_number_sql);
        $wo_number = $wo_number_data['wo_number'];
        $wo_number = $wo_number + 1;
        /////end of getting last wo Number

        if ($internal_wo == 0) {
            $master_internal_wo = "0";
        } else {
            $master_internal_wo = "1";
        }

        /////////////////////////////////////////////////////////////////////////////////////////////////

        $query = "insert into work_order (
		tenant,
		building,
		suite_location,
		suite_location2,
		create_user,
		date_requested,
		time_requested,
		category,
		internal_work_order,
		master_internal_work_order,
		work_order_request,
		created_at,
		wo_number,
		user_id)
		values(
		'" . $company_list . "',
		'" . $buildings_list . "',
		'" . $suite_location . "',
		'" . $suite_location . "',
		'" . $tenantusers_list . "',
		'" . $current_date_field . "',
		'" . $current_time_field . "',
		'" . $category_list . "',
		'1',
		'" . $master_internal_wo . "',
		'" . $wo_request . "',
		'" . $created_at . "',
		'" . $wo_number . "',
		'" . $uid . "')";
        $res = mysql_query($query);
        $last_id = mysql_insert_id();
        ////////////////////////////////////////////////////////////////////////////////////////////////

        $query_update = "insert into work_order_update (
		wo_id,
		wo_request,
		internal_note,
		wo_status,
		current_update,
		created_at,
		user_id)
		values(
		'" . $last_id . "',
		'',
		'" . $wo_notes . "',
		'1',
		'1',
		'" . date('Y-m-d H:i:s') . "',
		'" . $uid . "')";
        $res_update = mysql_query($query_update);
        $last_id_update = mysql_insert_id();

        //////insert work order schedule

        /////insert for workorder file upload
        $query_fileUpload = "insert into wo_files (woId,file_title,file_name,created_at)
        values(
        '" . $last_id . "',
        '" . $file_name . "',
       '" . $file_name . "',
        '" . date('Y-m-d H:i:s') . "'
        )";
        $query_fileUploadres = mysql_query($query_fileUpload);
        ///end insert for workorder file upload

        //// getting priority
        $priority_query = "SELECT `pr`.*, `cat`.`cat_id`
		FROM `priority` AS `pr`
		INNER JOIN `category` AS `cat` ON cat.prioritySchedule = pr.pid
		WHERE (cat.cat_id = '" . $category_list . "')";
        $priority_sql = mysql_query($priority_query);
        $priority_res = mysql_fetch_array($priority_sql);

        if ($priority_res) {
            $priorityName = $priority_res['priorityName'];
            $pid = $priority_res['pid'];
        } else {
            $priorityName = 'Not Assigned';
            $pid = 0;
        }
        //// getting priority

        //// getting schedule
        $schedule_query = "SELECT `priority_schedule`.*
		FROM `priority_schedule`
		WHERE (priority_id = '" . $pid . "') AND (start_status = 1)";
        $schedule_sql = mysql_query($schedule_query);
        $schedule_res = mysql_fetch_array($schedule_sql);
        $schedule_id = $schedule_res['id'];
        //// getting schedule

        $worder_id = $last_id;
        $schedule_id = $schedule_id; //
        $priority_id = $pid; //
        $status = 1;
        $ckey = md5(time());
        $current_status = 1;
        $created_at = date('Y-m-d H:i:s');

        $wo_schedule_status_query = "insert into wo_schedule_status(
		worder_id,
		schedule_id,
		priority_id,
		status,
		current_status,
		ckey,
		created_at) values
		('" . $worder_id . "',
		'" . $schedule_id . "',
		'" . $priority_id . "',
		'" . $status . "',
		'" . $current_status . "',
		'" . $ckey . "',
		'" . $created_at . "')";

        $wo_shedule_status_sql = mysql_query($wo_schedule_status_query);

        //////end of insert work order schedule

        $get_tenant_query = "SELECT `t`.*, `u`.`uid`, `u`.`firstName` AS `firstname`, `u`.`lastName` AS `lastname`, `u`.`phoneNumber` AS `phonenumber`, `u`.`email`, `u`.`role_id`, `st`.`state` AS `statename`
	FROM `tenant` AS `t`
	LEFT JOIN `users` AS `u` ON u.uid = t.userId
	LEFT JOIN `states` AS `st` ON st.state_code = t.state_code
	WHERE (t.id='" . $company_list . "')";
        $get_tenant_sql = mysql_query($get_tenant_query);
        $get_tenant_res = mysql_fetch_array($get_tenant_sql);
        $get_tenant_res['tenantId'] = $get_tenant_res['id'];
        $get_tenant_res['woId'] = $worder_id;

        return $get_tenant_res;
    }

    function getWoDescription($woid) {
        $query = "SELECT description from work_description where woId = '" . $woid . "' ORDER BY id DESC LIMIT 1";
        $sql = mysql_query($query);
        $cnt = mysql_num_rows($sql);
        if ($cnt > 0) {
            $row = mysql_fetch_array($sql);
            return $row['description'];
        } else {
            return false;
        }

    }

}

class Helper {

    function __Construct() {}

    function setTimezone($building_id) {
        $ob = new Helper();
        $tz_build_data = $ob->getBuildingById($building_id);
        if (isset($tz_build_data['timezone']) && $tz_build_data['timezone'] != 0) {
            $timezone_data = $ob->getTimeZone($tz_build_data['timezone']);
            if ($timezone_data) {
                $timezone = $timezone_data['time_value'];
                date_default_timezone_set($timezone);
            }
        } else if ($tz_build_data['timezone'] == 0) {
            date_default_timezone_set("America/Chicago");
        }

    }

    function getBuildingById($building_id) {
        $building_query = "SELECT `bu`.*, `st`.`state` AS `statename`, `bst`.`state` AS `billstatename`
			FROM `buildings` AS `bu`
			LEFT JOIN `states` AS `st` ON st.state_code = bu.state_code
			LEFT JOIN `states` AS `bst` ON bst.state_code = bu.billState_code
			WHERE (build_id='" . $building_id . "')";
        $building_sql = mysql_query($building_query);
        $building = mysql_fetch_array($building_sql);
        return $building;
    }

    function getTimeZone($time_zone_id) {
        $time_zone_query = "select * from time_zone where status = '1' AND id = '" . $time_zone_id . "' ";
        $time_zone_sql = mysql_query($time_zone_query);
        $time_zone = mysql_fetch_array($time_zone_sql);
        return $time_zone;
    }
}

?>