<?php

class DBFunctions {
    private $db;

    function __Construct() {
        require_once 'DBConnect.php';
        $this->db = new DBConnect();
        $this->db->connect();
        //global $con;
    }

    function __Destruct() {

    }
    public function requestLog($str, $path = '') {
        $query = "Insert into request_log set str='$str',path='$path'";
        $result = mysqli_query($this->db->connect(), $query) or die(mysql_error());
    }
    public function getLogin($username, $password) {
        // on 5/4/16 $query = "SELECT * FROM users WHERE userName = '" . $username . "' AND password = '" . md5($password) . "' AND status = '1' AND remove_status = '0'";
		$query = "SELECT * FROM users WHERE userName = '" . $username . "' AND password = '" . md5($password) . "' AND status = '1' AND remove_status = '0' AND role_id NOT IN(1,5,7,8,9);";
		
        //$query = "SELECT * FROM users WHERE userName = '".$username."' AND password = '".md5($password)."' AND status = '1' AND remove_status = '0' AND role_id != '1'";
        //echo $query; exit;
        $result = mysqli_query($this->db->connect(), $query);
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows == 1) {
            $result = mysqli_fetch_array($result); 
			return $result; 
        }
    }

	function getAccessMatrix($role_id, $uid='') {
			$result = array();
			if($uid != '' && $role_id != '') {
				$access_query_loc1 = "SELECT * FROM user_access WHERE user_id= '" . $uid. "' AND location_id =". 1; 
				$access_query_loc1 = mysqli_query($this->db->connect(), $access_query_loc1);
				$access_result_loc1 = mysqli_fetch_array($access_query_loc1);
				if($access_result_loc1) {
					$result['dline_access']['is_access'] =$access_result_loc1['is_access']; 
					$result['dline_access']['is_read'] =$access_result_loc1['is_read']; 
					$result['dline_access']['is_write'] =$access_result_loc1['is_write'];
				} else {
					$access_query_loc1 = "SELECT * FROM location_access WHERE role= '" . $role_id. "' AND location_id =". 1; 
					$access_query_loc1 = mysqli_query($this->db->connect(), $access_query_loc1);
					$access_result_loc1 = mysqli_fetch_array($access_query_loc1);
					$result['dline_access']['is_access'] =$access_result_loc1['is_access']; 
					$result['dline_access']['is_read'] =$access_result_loc1['is_read']; 
					$result['dline_access']['is_write'] =$access_result_loc1['is_write'];
				
				}
				$access_query_loc3 = $access_query = "SELECT * FROM user_access WHERE user_id= '" . $uid. "' AND location_id =". 3;  
				$access_query_loc3 = mysqli_query($this->db->connect(), $access_query_loc3);
				$access_result_loc3 = mysqli_fetch_array($access_query_loc3);
				
				if($access_result_loc3) {
					$result['create_access']['is_access'] =$access_result_loc3['is_access']; 
					$result['create_access']['is_read'] =$access_result_loc3['is_read']; 
					$result['create_access']['is_write'] =$access_result_loc3['is_write'];
				} else {
					$access_query_loc3 = "SELECT * FROM location_access WHERE role= '" . $role_id. "' AND location_id =". 3; 
					$access_query_loc3 = mysqli_query($this->db->connect(), $access_query_loc3);
					$access_result_loc3 = mysqli_fetch_array($access_query_loc3);
					$result['create_access']['is_access'] =$access_result_loc3['is_access']; 
					$result['create_access']['is_read'] =$access_result_loc3['is_read']; 
					$result['create_access']['is_write'] =$access_result_loc3['is_write'];
				}
				
				$access_query_loc4 = "SELECT * FROM user_access WHERE user_id= '" . $uid. "' AND location_id =". 4; 
				$access_query_loc4 = mysqli_query($this->db->connect(), $access_query_loc4);
				$access_result_loc4 = mysqli_fetch_array($access_query_loc4);
				
				if($access_result_loc4) {
					$result['closeWO_access']['is_access'] =$access_result_loc4['is_access']; 
					$result['closeWO_access']['is_read'] =$access_result_loc4['is_read']; 
					$result['closeWO_access']['is_write'] =$access_result_loc4['is_write'];
				} else {
					$access_query_loc4 = "SELECT * FROM location_access WHERE role= '" . $role_id. "' AND location_id =". 4; 
					$access_query_loc4 = mysqli_query($this->db->connect(), $access_query_loc4);
					$access_result_loc4 = mysqli_fetch_array($access_query_loc4);
					$result['closeWO_access']['is_access'] =$access_result_loc4['is_access']; 
					$result['closeWO_access']['is_read'] =$access_result_loc4['is_read']; 
					$result['closeWO_access']['is_write'] =$access_result_loc4['is_write'];
				}
				
				
			} 
				
			
            return $result; 
	}

    public function getStatus($selected) {
		$all = array();
        $query = "SELECT * FROM schedule_status where status = '1'";
        $status = mysqli_query($this->db->connect(), $query);
        $no_of_rows = mysqli_num_rows($status);
        if ($no_of_rows > 0) {
			while ($status_row = mysqli_fetch_array($status)) {
				$all[] = $status_row['ssID'];

			}
			$all = implode(",",$all);
            $status_list = "<option value='0'  class='tabhide' selected></option>";
            if ($selected == "") {
                //$status_list .= "<option value='".$all. "'  >All Status</option>";
            }
		

		mysqli_data_seek($status,0);
		
		while ($status_row = mysqli_fetch_array($status)) {
                $status_list .= "<option value='" . $status_row['ssID'] . "' ";
                if ($selected == $status_row['ssID']) {
                    $status_list .= "selected = 'selected'";
                }  else if($selected !='' & $selected !='null') { 
				 
				if(is_string($selected)) {
					$selected =  explode(",",$selected);
					if (in_array($status_row['ssID'], $selected)){ 
						$status_list .= "selected = 'selected'";
					}
				} else {
					if (in_array($status_row['ssID'], $selected)){ 
						$status_list .= "selected = 'selected'";
					}
				} 
					
					
				}else if($status_row['ssID'] == 1 || $status_row['ssID'] == 2) {
					$status_list .= "selected = 'selected'";
				}
                $status_list .= ">" . $status_row['title'] . "</option>";
            }
			
            return $status_list;
        } else {
            return false;
        }
    }
	public function checkAutoRefershExist($uid) {
		$building_array =array();
		$query1 = "select DISTINCT building_id from user_building_module_access where user_id = '" . $uid . "' ";
        //echo $query;
        $sql1 = mysqli_query($this->db->connect(), $query1);
		while($row1 = mysqli_fetch_assoc($sql1)) {
			$building_array[] =$row1['building_id'];
		}
		 $building_string = implode(",",$building_array);
		 
		 $query = "SELECT auto_refersh FROM auto_refersh where building_id in($building_string) AND auto_refersh = 1";
		 $val = mysqli_query($this->db->connect(), $query) ;
		 $val_row = mysqli_num_rows($val);
		 if($val_row == 0) { 
			return true;
		 } else {
			return false;
		 }
	}
    public function getPredefinedNotes($cust_id) {
        $query = "SELECT * FROM notes_predefined where status = '1' AND cust_id ='" . $cust_id . "'";
        $status = mysqli_query($this->db->connect(), $query) ;
        $no_of_rows = mysqli_num_rows($status);
        if ($no_of_rows > 0) {
            $status_list = "";
            $status_list .= "<option value=''>Select From Predefined Notes</option>";
            while ($status_row = mysqli_fetch_array($status)) {
                $status_list .= "<option value='" . $status_row['notes'] . "'>" . $status_row['notes'] . "</option>";
            }
            return $status_list;
        } else {
            return false;
        }
    }

    public function getWorkOrder($role_id, $uid, $cust_id, $filter, $woid_array, $workorderid, $woLimit='', $exsting_header='', $return_max_wo_val='',$wo_offset='') {
		$preDate = '00-00-00';
		$alert_rows='';
		$ackbutton = '';
		$filter2 = '';
		$oldName = '';
		$max_wo_val= '';
		$requesttimeordate ='';
		$newcount=0;
		$headerexist = false;
		$workbkground='';
		$exsting_header_val = array();
		if($exsting_header !='')  {
			$exsting_header = rtrim($exsting_header,',');
			$exsting_header_val = explode(",",$exsting_header);
		}
		$color_code = array('00FFFF','FF0000','FF00FF','800000','008000','800080','808000','0000FF','00FF00','00008075','FFFF00','808000','008080','FFA500');$building_color = array();
		
		$allstatus = array();
		$query = "SELECT * FROM schedule_status where status = '1'";
        $status = mysqli_query($this->db->connect(), $query) ;
        $no_of_rows = mysqli_num_rows($status);
        if ($no_of_rows > 0) {
			while ($status_row = mysqli_fetch_array($status)) {
				$allstatus[$status_row['ssID']] = $status_row['title'];
				$filter2 = $status_row['ssID'].','.$filter2;
			}
		}
		$filter = trim($filter,',');
		$condition = "";
        if ($filter != '') {
            $condition .= " AND wop.wo_status in (" . $filter . ")";
        } else {
			$filter2 = trim($filter2,','); 
			$condition .= " AND wop.wo_status in (" . $filter2 . ")";
		}
		if($return_max_wo_val!='') {
			 $condition .= " AND wo.woId > $return_max_wo_val";	
		}
		if($workorderid !='') {
			$condition .= " AND wo.wo_number LIKE '". $workorderid ."%'";
		}
		
		$woLimitCon = '';
		if($woLimit !='') {
			$woLimitCon = " LIMIT ".$wo_offset." OFFSET ".$woLimit;
		}

        //// check for search from wo detail page
        if (strlen($woid_array) > 0) {
            $condition .= " AND wo.woId IN (" . $woid_array . ")";
        }
        //// end check for search from wo detail page

        $work_rows = "";
		
               $sql = "SELECT ubma.*, (select count(*) from user_building_module_access where building_id=ubma.building_id) as total FROM `user_building_module_access` as ubma WHERE ubma.user_id=" . $uid;
                $res = mysqli_query($this->db->connect(), $sql);
				$build_id_array = array();
                while ($row = mysqli_fetch_array($res)) {
                    $build_id_array[] = $row['building_id'];
                }
                $a1 = "SELECT `buildings`.`build_id`, `buildings`.`cust_id`, `buildings`.`buildingName`, `buildings`.`user_id` FROM `buildings` WHERE (status='1') AND build_id in (" . implode(',', $build_id_array) . ")";
				
                $a1_res = mysqli_query($this->db->connect(), $a1);
            
            $build_ID = '';
            
            if ($build_ID == '') {
                $buildIds = array();
				$j=1;
				$buildingcount =array();
                while ($row = mysqli_fetch_array($a1_res)) {
                    $buildIds[] = $row['build_id'];
					$building_color[$row['build_id']] = $color_code[$j];
					$j++;
                }
				$buildingcount = count($buildIds);
				$leftbarstyle ='';
				$max_wo = "SELECT wo.woId FROM `work_order` AS `wo` INNER JOIN `tenant` AS `t` ON t.id = wo.tenant LEFT JOIN `buildings` AS `bu` ON bu.build_id = wo.building LEFT JOIN `category` AS `cat` ON cat.cat_id = wo.category LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1 LEFT JOIN `users` AS `u` ON wo.create_user = u.uid WHERE (wo.building in (" . implode(',', $buildIds) . ")) " . $condition . " ORDER BY  `woId` DESC LIMIT 1";
				
				$max_wo_query = mysqli_query($this->db->connect(), $max_wo);
				$max_wo_val = mysqli_fetch_assoc($max_wo_query);
				
				if($max_wo_val !='') {
					$max_wo_val = $max_wo_val['woId']; 
					$max_wo_val = $max_wo_val;
				} else {
					$max_wo_val = $return_max_wo_val;
				}
                $query = "SELECT `wo`.*, `t`.`tenantName`, `t`.`tenantContact`, `bu`.`buildingName`, `cat`.`categoryName`, `cat`.`prioritySchedule`, `wop`.`wo_status`, `wop`.`internal_note`, `wop`.`wo_request`, `wop`.`created_at` AS `created_date`, `wop`.`updated_at` AS `updated_date`, `u`.`firstName`, `u`.`lastName`, `u`.`email`
							 FROM `work_order` AS `wo`
							 INNER JOIN `tenant` AS `t` ON t.id = wo.tenant
							 LEFT JOIN `buildings` AS `bu` ON bu.build_id = wo.building
							 LEFT JOIN `category` AS `cat` ON cat.cat_id = wo.category
							 LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1
							 LEFT JOIN `users` AS `u` ON wo.create_user = u.uid
							 WHERE (wo.building in (" . implode(',', $buildIds) . ")) " . $condition . "
							 ORDER BY `date_requested` DESC, `woId` DESC" . $woLimitCon;
                
                $wo_query = mysqli_query($this->db->connect(), $query);
                $work_rows = "";
                $wocnt = 0;
				$maxDate ='';
                while ($wo_row = mysqli_fetch_array($wo_query)) {
				
                    /******************* set timezone through building id ****************************/
					date_default_timezone_set('America/Los_Angeles');
					
					$currentqueryDate = date("Y-m-d");
					$todayMax = $currentqueryDate;
					$currentqueryDate = strtotime($currentqueryDate);
					$maxDateyesterday = strtotime('-1 day', $currentqueryDate);
					$maxDateyesterday = date('Y-m-d', $maxDateyesterday);
					$minDateWeek = strtotime('-7 day', $currentqueryDate);
					$minDateWeek = date('Y-m-d', $minDateWeek);
					$maxDateWeek = strtotime('-2 day', $currentqueryDate);
					$maxDateWeek = date('Y-m-d', $maxDateWeek);
					$minDateMonth = strtotime('-30 day', $currentqueryDate);
					$minDateMonth = date('Y-m-d', $minDateMonth);
					$maxDateMonth = strtotime('-7 day', $currentqueryDate);
					$maxDateMonth = date('Y-m-d', $maxDateMonth);
					$maxDateOldmon = strtotime('-30 day', $currentqueryDate);
					$maxDateOldmon = date('Y-m-d', $maxDateOldmon);
					
                    $ob = new Helper();
                    $ob->setTimezone($wo_row['building']);
					$currentDate = new DateTime($todayMax);
                    /******************* end - set timezone through building id ****************************/
					$requestedDate = new DateTime($wo_row['date_requested']);
					$days_old = $currentDate->diff($requestedDate);
					$wo_row['days_old'] = $days_old->days; 
					$checkfield =0;
					if($wo_row['days_old'] == 0 ) {  
						 $newwoquery = "SELECT COUNT(*) as newwocount FROM `work_order` AS `wo`INNER JOIN `tenant` AS `t` ON t.id = wo.tenant LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1 WHERE (wo.building in (" . implode(',', $buildIds) . "))  AND wop.wo_status in (1) AND wo.date_requested ='". $todayMax . "'". $condition ." ORDER BY `woId` DESC"; 
						$checkfield = 1;
						$oldName = 'Today';  
						$newwoqueryex = mysqli_query($this->db->connect(), $newwoquery);
						$newwoqueryresult = mysqli_fetch_array($newwoqueryex);
						$oldName .= '(<span id="count_header1">'.$newwoqueryresult['newwocount'].'</span>)';
						$requesttimeordate = date("h:i A", strtotime($wo_row['time_requested']));
						$oldName .= '<input type="hidden" class="wo_header" value="1">';
						$newcount = $newwoqueryresult['newwocount'];
					
					} else if($wo_row['days_old'] == 1) {    
						$checkfield = 2;
						$oldName = 'Yesterday'; 
						$newwoquery = "SELECT COUNT(*) as newwocount FROM `work_order` AS `wo`INNER JOIN `tenant` AS `t` ON t.id = wo.tenant LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1 WHERE (wo.building in (" . implode(',', $buildIds) . ")) AND wop.wo_status in (1) AND wo.date_requested ='". $maxDateyesterday . "'". $condition ." ORDER BY `woId` DESC"; 
						
						$newwoqueryex = mysqli_query($this->db->connect(), $newwoquery);
						$newwoqueryresult = mysqli_fetch_array($newwoqueryex);
						$oldName .= '(<span id="count_header2">'. $newwoqueryresult['newwocount'] .'</span>)';
						$requesttimeordate = date("h:i A", strtotime($wo_row['time_requested']));
						$oldName .= '<input type="hidden" class="wo_header" value="2">';
					
					} else if($wo_row['days_old'] < 7 && $wo_row['days_old'] >=2){
						$checkfield = 3;
						$oldName = 'Last 7 Days';
						$newwoquery = "SELECT COUNT(*) as newwocount FROM `work_order` AS `wo`INNER JOIN `tenant` AS `t` ON t.id = wo.tenant LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1 WHERE (wo.building in (" . implode(',', $buildIds) . ")) AND wop.wo_status in (1) AND wo.date_requested <='". $maxDateWeek . "' AND wo.date_requested >'". $minDateWeek . "'". $condition ." ORDER BY `woId` DESC"; 
						$newwoqueryex = mysqli_query($this->db->connect(), $newwoquery); 
						$newwoqueryresult = mysqli_fetch_array($newwoqueryex);
						$oldName .= '(<span id="count_header3">'. $newwoqueryresult['newwocount'].'</span>)';
						$requesttimeordate = date("m/y", strtotime($wo_row['date_requested']));
						$oldName .= '<input type="hidden" class="wo_header" value="3">';
						} else if($wo_row['days_old'] < 30 && $wo_row['days_old'] >= 7) { 
							$checkfield = 4;
							$oldName = 'Last 30 Days';
							$newwoquery = "SELECT COUNT(*) as newwocount FROM `work_order` AS `wo`INNER JOIN `tenant` AS `t` ON t.id = wo.tenant LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1 WHERE (wo.building in (" . implode(',', $buildIds) . ")) AND wop.wo_status in (1) AND wo.date_requested <='". $maxDateMonth . "' AND wo.date_requested >'". $minDateMonth . "'". $condition ." ORDER BY `woId` DESC"; 
							$newwoqueryex = mysqli_query($this->db->connect(), $newwoquery);
							$newwoqueryresult = mysqli_fetch_array($newwoqueryex);
							$oldName .= '(<span id="count_header4">'.$newwoqueryresult['newwocount'].'</span>)';
							$requesttimeordate = date("m/y", strtotime($wo_row['date_requested']));
							$oldName .= '<input type="hidden" class="wo_header" value="4">';
						} else { 
							$checkfield = 5;
							$oldName = 'Older than 30 Days'; 
							$newwoquery = "SELECT COUNT(*) as newwocount FROM `work_order` AS `wo`INNER JOIN `tenant` AS `t` ON t.id = wo.tenant LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1 WHERE (wo.building in (" . implode(',', $buildIds) . ")) AND wop.wo_status in (1) AND wo.date_requested <='". $maxDateOldmon . "'". $condition ." ORDER BY `woId` DESC";  
							$newwoqueryex = mysqli_query($this->db->connect(), $newwoquery);
							$newwoqueryresult = mysqli_fetch_array($newwoqueryex);
							$oldName .= '(<span id="count_header5">'.$newwoqueryresult['newwocount'].'</span>)';
							$requesttimeordate = date("m/y", strtotime($wo_row['date_requested']));
							$oldName .= '<input type="hidden" class="wo_header" value="5">';
						}
						
						
					if($oldName != $preDate && (!in_array($checkfield, $exsting_header_val))) {
						$headerexist = true;
						if($buildingcount>1) {
							$work_rows .= "<tr class='vt_set_gray vt_set_gray_header'><td colspan='2'>". $oldName. "</td>";
						} else {
							$work_rows .= "<tr class='vt_set_gray vt_set_gray_header'><td colspan='1'>". $oldName. "</td>";
						}
						if($wo_row['days_old'] == 0 || $wo_row['days_old'] == 1) {
							$work_rows .= "<td style='text-align:center'>". date("m/d/y", strtotime($wo_row['date_requested'])). "</td> ";
						} else {
							$work_rows .= "<td></td> ";
						}
						$work_rows .= "</tr>";
					}
					$preDate = $oldName;
					$tenantName = $wo_row['tenantName'];
                    $buildingName = $wo_row['buildingName'];
                    $description = $wo_row['work_order_request'];
                    $wo_status = $wo_row['wo_status'];
                    $wo_id = $wo_row['woId'];
                    $wo_number = $wo_row['wo_number'];
                    $wocnt++;

                    if ($wocnt % 2 == 0) {
					
						if($wo_status == 1) {
							$statusQuery = "SELECT `ws`.*, `ps`.`Time`, `ps`.`length` FROM `wo_schedule_status` AS `ws` INNER JOIN `priority_schedule` AS `ps` ON ps.id = ws.schedule_id WHERE (ws.current_status = '1') AND (ws.worder_id = $wo_id)";
							$statusResult = mysqli_query($this->db->connect(), $statusQuery);
							$statusResult = mysqli_fetch_assoc($statusResult);
							if($statusResult['ckey'] !='') {
								$ackUrl = '"'.BASE_URL.'/appcomplete/preDispatch/woId/'.$statusResult['worder_id'].'/sId/'.$statusResult['schedule_id'].'/ckey/'.$statusResult['ckey'].'/statusChange/tag/acknowledge/userId/'.$uid.'"'; 
								$ackbutton ="<button class='btn btn-danger btn-xs' onclick='acknowledgeWorkorder(event, ".$ackUrl.")'  type='button'>Ack</button>";
							}
						} else {
							$ackbutton ='&nbsp;';
						}
						
				
                        $schedule_query = "SELECT `priority_schedule`.* FROM `priority_schedule` WHERE (priority_id = '" . $wo_row['prioritySchedule'] . "') AND (start_status = '" . $wo_status . "')";
                        $schedule_sql = mysqli_query($this->db->connect(), $schedule_query);
                        $schedule = mysqli_fetch_array($schedule_sql);

                        if ($wo_status == '1') {
                            $lastTime = $wo_row['date_requested'] . ' ' . $wo_row['time_requested'];
                        } else if ($wo_row['updated_at'] != '0000-00-00 00:00:00') {
                            $lastTime = $wo_row['updated_at'];
                        } else {
                            $lastTime = $wo_row['created_date'];
                        }
                        $currTime = date("Y-m-d h:i:s");
                        $lastTime = date("Y-m-d h:i:s", strtotime($lastTime));
                        $timeDiff = strtotime($currTime) - strtotime($lastTime);
						$wo_row['currTime'] = date("Y-m-d h:i:s");
			
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
                            $alert_rows = " <img src='img/alert.png' alt=''/>";
                        } else {
                            if ($wo_status == '1') {
								
								$alert_rows = " <img src='img/bell-icon.png' alt=''/>";
                            } 
                        }	
						if($wo_status == 1) { $workbkground = 'newworkorder'; $tdback = "background:#fff";  } else { $workbkground = 'openworkorder'; $tdback = "background:#ddd";  }
						if($buildingcount > 1) {
							$leftbarstyle="background:#".$building_color[$wo_row['building']]."!important;";
						} else {
							$leftbarstyle="background:#".$building_color[$wo_row['building']]."!important; display:none";
						}
						
						$work_rows .= "<tr class='".$workbkground." workorderbackground'><td rowspan='3' class='left_bar'  style='".$leftbarstyle."'></td><td class='work_order_Text firstorder' style='".$tdback."' rowspan='3' onclick='getDetail(" . $wo_id . ");' ><div class='maindata'><h4>".$wo_number."-".$wo_row['categoryName']."</h4></div><div class='alarmright'>".$alert_rows."</div><div class='clearfix'>  </div><h5>".$wo_row['tenantName']." - ".$buildingName."</h5><p>".$description."</p></td><td class='lasttd'>".$requesttimeordate."</td></tr><tr  class='".$workbkground."' ><td class='lasttd'>".$allstatus[$wo_status]."</td></tr><tr class='".$workbkground."'><td class='lasttd ackbutton'>".$ackbutton."</td></tr>";
					} else {
						if($wo_status == 1) {
							$statusQuery = "SELECT `ws`.*, `ps`.`Time`, `ps`.`length` FROM `wo_schedule_status` AS `ws` INNER JOIN `priority_schedule` AS `ps` ON ps.id = ws.schedule_id WHERE (ws.current_status = '1') AND (ws.worder_id = $wo_id)";
							$statusResult = mysqli_query($this->db->connect(), $statusQuery);
							$statusResult = mysqli_fetch_assoc($statusResult);
							//print_r($statusResult);
							if($statusResult['ckey'] !='') {
								$ackUrl = '"'.BASE_URL.'/appcomplete/preDispatch/woId/'.$statusResult['worder_id'].'/sId/'.$statusResult['schedule_id'].'/ckey/'.$statusResult['ckey'].'/statusChange/tag/acknowledge/userId/'.$uid.'"'; 
								$ackbutton ="<button class='btn btn-danger btn-xs' onclick='acknowledgeWorkorder(event, ".$ackUrl.")' type='button'>Ack</button>";
							}
						} else {
							$ackbutton ='&nbsp;';
						}
						
						 $schedule_query = "SELECT `priority_schedule`.* FROM `priority_schedule` WHERE (priority_id = '" . $wo_row['prioritySchedule'] . "') AND (start_status = '" . $wo_status . "')";
                        //echo $schedule_query;
                        $schedule_sql = mysqli_query($this->db->connect(), $schedule_query);
                        $schedule = mysqli_fetch_array($schedule_sql);

                        if ($wo_status == '1') {
                            $lastTime = $wo_row['date_requested'] . ' ' . $wo_row['time_requested'];
                        } else if ($wo_row['updated_at'] != '0000-00-00 00:00:00') {
                            $lastTime = $wo_row['updated_at'];
                        } else {
                            $lastTime = $wo_row['created_date'];
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
                            $alert_rows = " <img src='img/alert.png' alt=''/>";
                        } else {
                            if ($wo_status == '1') {
                                $alert_rows = " <img src='img/bell-icon.png' alt=''/>";
								
                            } 
                        }
						if($wo_status == 1) { $workbkground = 'newworkorder'; $tdback = "background:#fff";  } else { $workbkground = 'openworkorder';  $tdback = "background:#ddd"; }
						if($buildingcount > 1) {
							$leftbarstyle="background:#".$building_color[$wo_row['building']]."!important;";
							
						} else {
							$leftbarstyle="background:#".$building_color[$wo_row['building']]."!important; display:none";
							
						}
						
						$work_rows .= "<tr class='".$workbkground." workorderbackground'><td rowspan='3' class='left_bar' style='".$leftbarstyle."'></td><td class='work_order_Text firstorder'  rowspan='3' style='".$tdback."' onclick='getDetail(" . $wo_id . ");' ><div class='maindata'><h4>".$wo_number."-".$wo_row['categoryName']."</h4></div><div class='alarmright'> ".$alert_rows."</div><div class='clearfix'>  </div><h5>".stripslashes($wo_row['tenantName'])." - ".stripslashes($buildingName)."</h5><p >".stripslashes($description)."</p></td><td class='lasttd'>".$requesttimeordate."</td></tr><tr class='".$workbkground."'><td class='lasttd' >".$allstatus[$wo_status]."</td></tr><tr class='".$workbkground."'><td class='lasttd ackbutton'>".$ackbutton."</td></tr>";
                       
                    }
                }

            } 
			
            //}

            //$work_rows = "<tr class='vt_set_yellow'><td colspan='5'>Data not found for this User !</td></tr>";
       
		$wo_return['work_rows'] = $work_rows;
		$wo_return['max_wo_val'] = $max_wo_val;
		$wo_return['headerexist'] = $headerexist;
		$wo_return['newcount'] = $newcount;
        return $wo_return;
    }
	public function getWorkOrderNew($role_id, $uid, $cust_id, $filter, $woid_array, $workorderid, $woLimit='', $exsting_header='', $return_max_wo_val='',$wo_offset='') {
		$preDate = '00-00-00';
		$alert_rows='';
		$ackbutton = '';
		$filter2 = '';
		$oldName = '';
		$max_wo_val= '';
		$requesttimeordate ='';
		$newcount=0;
		$headerexist = false;
		$workbkground='';
		$exsting_header_val = array();
		if($exsting_header !='')  {
			$exsting_header = rtrim($exsting_header,',');
			$exsting_header_val = explode(",",$exsting_header);
		}
		$color_code = array('00FFFF','FF0000','FF00FF','800000','008000','800080','808000','0000FF','00FF00','000080','FFFF00','808000','008080','FFA500');
		$building_color = array();
		
		$allstatus = array();
		$query = "SELECT * FROM schedule_status where status = '1'";
        $status = mysqli_query($this->db->connect(), $query) ;
        $no_of_rows = mysqli_num_rows($status);
        if ($no_of_rows > 0) {
			while ($status_row = mysqli_fetch_array($status)) {
				$allstatus[$status_row['ssID']] = $status_row['title'];
				$filter2 = $status_row['ssID'].','.$filter2;
			}
		}
		$filter = trim($filter,',');
		$condition = "";
        if ($filter != '') {
            $condition .= " AND wop.wo_status in (" . $filter . ")";
        } else {
			$filter2 = trim($filter2,','); 
			$condition .= " AND wop.wo_status in (" . $filter2 . ")";
		}
		if($return_max_wo_val!='') {
			 $condition .= " AND wo.woId > $return_max_wo_val";	
		}
		if($workorderid !='') {
			$condition .= " AND wo.wo_number LIKE '". $workorderid ."%'";
		}
		
		$woLimitCon = '';
		if($woLimit !='') {
			$woLimitCon = " LIMIT ".$wo_offset." OFFSET ".$woLimit;
		}

        //// check for search from wo detail page
        if (strlen($woid_array) > 0) {
            $condition .= " AND wo.woId IN (" . $woid_array . ")";
        }
        //// end check for search from wo detail page

        $work_rows = "";
        $work_rows_array = [];
		
               $sql = "SELECT ubma.*, (select count(*) from user_building_module_access where building_id=ubma.building_id) as total FROM `user_building_module_access` as ubma WHERE ubma.user_id=" . $uid;
                $res = mysqli_query($this->db->connect(), $sql);
				$build_id_array = array();
                while ($row = mysqli_fetch_array($res)) {
                    $build_id_array[] = $row['building_id'];
                }
                $a1 = "SELECT `buildings`.`build_id`, `buildings`.`cust_id`, `buildings`.`buildingName`, `buildings`.`user_id` FROM `buildings` WHERE (status='1') AND build_id in (" . implode(',', $build_id_array) . ")";
				
                $a1_res = mysqli_query($this->db->connect(), $a1);
            
            $build_ID = '';
            
            if ($build_ID == '') {
                $buildIds = array();
				$j=1;
				$buildingcount =array();
                while ($row = mysqli_fetch_array($a1_res)) {
                    $buildIds[] = $row['build_id'];
					$building_color[$row['build_id']] = $color_code[$j];
					$j++;
                }
				$buildingcount = count($buildIds);
				$leftbarstyle ='';
				$max_wo = "SELECT wo.woId FROM `work_order` AS `wo` INNER JOIN `tenant` AS `t` ON t.id = wo.tenant LEFT JOIN `buildings` AS `bu` ON bu.build_id = wo.building LEFT JOIN `category` AS `cat` ON cat.cat_id = wo.category LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1 LEFT JOIN `users` AS `u` ON wo.create_user = u.uid WHERE (wo.building in (" . implode(',', $buildIds) . ")) " . $condition . " ORDER BY  `woId` DESC LIMIT 1";
				
				$max_wo_query = mysqli_query($this->db->connect(), $max_wo);
				$max_wo_val = mysqli_fetch_assoc($max_wo_query);
				
				if($max_wo_val !='') {
					$max_wo_val = $max_wo_val['woId']; 
					$max_wo_val = $max_wo_val;
				} else {
					$max_wo_val = $return_max_wo_val;
				}
                $query = "SELECT `wo`.*, `t`.`tenantName`, `t`.`tenantContact`, `bu`.`buildingName`, `cat`.`categoryName`, `cat`.`prioritySchedule`, `wop`.`wo_status`, `wop`.`internal_note`, `wop`.`wo_request`, `wop`.`created_at` AS `created_date`, `wop`.`updated_at` AS `updated_date`, `u`.`firstName`, `u`.`lastName`, `u`.`email`
							 FROM `work_order` AS `wo`
							 INNER JOIN `tenant` AS `t` ON t.id = wo.tenant
							 LEFT JOIN `buildings` AS `bu` ON bu.build_id = wo.building
							 LEFT JOIN `category` AS `cat` ON cat.cat_id = wo.category
							 LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1
							 LEFT JOIN `users` AS `u` ON wo.create_user = u.uid
							 WHERE (wo.building in (" . implode(',', $buildIds) . ")) " . $condition . "
							 ORDER BY `date_requested` DESC, `woId` DESC" . $woLimitCon;
                
                $wo_query = mysqli_query($this->db->connect(), $query);
                $work_rows = "";
                $wocnt = 0;
                $wocntnew = 0;
				$maxDate ='';
                while ($wo_row = mysqli_fetch_array($wo_query)) {
				
                    /******************* set timezone through building id ****************************/
					date_default_timezone_set('America/Los_Angeles');
					
					$currentqueryDate = date("Y-m-d");
					$todayMax = $currentqueryDate;
					$currentqueryDate = strtotime($currentqueryDate);
					$maxDateyesterday = strtotime('-1 day', $currentqueryDate);
					$maxDateyesterday = date('Y-m-d', $maxDateyesterday);
					$minDateWeek = strtotime('-7 day', $currentqueryDate);
					$minDateWeek = date('Y-m-d', $minDateWeek);
					$maxDateWeek = strtotime('-2 day', $currentqueryDate);
					$maxDateWeek = date('Y-m-d', $maxDateWeek);
					$minDateMonth = strtotime('-30 day', $currentqueryDate);
					$minDateMonth = date('Y-m-d', $minDateMonth);
					$maxDateMonth = strtotime('-7 day', $currentqueryDate);
					$maxDateMonth = date('Y-m-d', $maxDateMonth);
					$maxDateOldmon = strtotime('-30 day', $currentqueryDate);
					$maxDateOldmon = date('Y-m-d', $maxDateOldmon);
					
                    $ob = new Helper();
                    $ob->setTimezone($wo_row['building']);
					$currentDate = new DateTime($todayMax);
                    /******************* end - set timezone through building id ****************************/
					$requestedDate = new DateTime($wo_row['date_requested']);
					$days_old = $currentDate->diff($requestedDate);
				     $wo_row['days_old'] = $days_old->days; 
					
					$checkfield =0;
					if($wo_row['days_old'] == 0 ) {  
						$newwoquery = "SELECT COUNT(*) as newwocount FROM `work_order` AS `wo`INNER JOIN `tenant` AS `t` ON t.id = wo.tenant LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1 WHERE (wo.building in (" . implode(',', $buildIds) . "))  AND wop.wo_status in (1) AND wo.date_requested ='". $todayMax . "'". $condition ." ORDER BY `woId` DESC"; 
						$checkfield = 1;
						$oldName = 'Today'; 
						$quer = " AND date(wo.created_at) = date(sysdate())";
						$newwoqueryex = mysqli_query($this->db->connect(), $newwoquery);
						$newwoqueryresult = mysqli_fetch_array($newwoqueryex);
						$oldName .= '('.$newwoqueryresult['newwocount'].')';
						$requesttimeordate = date("h:i A", strtotime($wo_row['time_requested']));
						$newcount = $newwoqueryresult['newwocount'];
					
					} else if($wo_row['days_old'] == 1) {    
						$checkfield = 2;
						$oldName = 'Yesterday'; 
						$newwoquery = "SELECT COUNT(*) as newwocount FROM `work_order` AS `wo`INNER JOIN `tenant` AS `t` ON t.id = wo.tenant LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1 WHERE (wo.building in (" . implode(',', $buildIds) . ")) AND wop.wo_status in (1) AND wo.date_requested ='". $maxDateyesterday . "'". $condition ." ORDER BY `woId` DESC"; 
						$newwoqueryex = mysqli_query($this->db->connect(), $newwoquery);
						$newwoqueryresult = mysqli_fetch_array($newwoqueryex);
						$oldName .= '('. $newwoqueryresult['newwocount'] .')';
						$requesttimeordate = date("h:i A", strtotime($wo_row['time_requested']));
					
					
					} else if($wo_row['days_old'] < 7 && $wo_row['days_old'] >=2){
						$checkfield = 3;
						$oldName = 'Last 7 Days';
						$newwoquery = "SELECT COUNT(*) as newwocount FROM `work_order` AS `wo`INNER JOIN `tenant` AS `t` ON t.id = wo.tenant LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1 WHERE (wo.building in (" . implode(',', $buildIds) . ")) AND wop.wo_status in (1) AND wo.date_requested <='". $maxDateWeek . "' AND wo.date_requested >'". $minDateWeek . "'". $condition ." ORDER BY `woId` DESC"; 
						$newwoqueryex = mysqli_query($this->db->connect(), $newwoquery); 
						$newwoqueryresult = mysqli_fetch_array($newwoqueryex);
						$oldName .= '('. $newwoqueryresult['newwocount'].')';
						$requesttimeordate = date("m/y", strtotime($wo_row['date_requested']));
						
					
					} else if($wo_row['days_old'] < 30 && $wo_row['days_old'] >= 7) { 
							$checkfield = 4;
							$oldName = 'Last 30 Days';
							$newwoquery = "SELECT COUNT(*) as newwocount FROM `work_order` AS `wo`INNER JOIN `tenant` AS `t` ON t.id = wo.tenant LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1 WHERE (wo.building in (" . implode(',', $buildIds) . ")) AND wop.wo_status in (1) AND wo.date_requested <='". $maxDateMonth . "' AND wo.date_requested >'". $minDateMonth . "'". $condition ." ORDER BY `woId` DESC"; 
							$newwoqueryex = mysqli_query($this->db->connect(), $newwoquery);
							$newwoqueryresult = mysqli_fetch_array($newwoqueryex);
							$oldName .= '('.$newwoqueryresult['newwocount'].')';
							$requesttimeordate = date("m/y", strtotime($wo_row['date_requested']));
						
				   } else { 
							$checkfield = 5;
							$oldName = 'Older than 30 Days'; 
							$newwoquery = "SELECT COUNT(*) as newwocount FROM `work_order` AS `wo`INNER JOIN `tenant` AS `t` ON t.id = wo.tenant LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update = 1 WHERE (wo.building in (" . implode(',', $buildIds) . ")) AND wop.wo_status in (1) AND wo.date_requested <='". $maxDateOldmon . "'". $condition ." ORDER BY `woId` DESC";  
							$newwoqueryex = mysqli_query($this->db->connect(), $newwoquery);
							$newwoqueryresult = mysqli_fetch_array($newwoqueryex);
							$oldName .= '('.$newwoqueryresult['newwocount'].')';
							$requesttimeordate = date("m/y", strtotime($wo_row['date_requested']));
							
						}
						
				
					if($oldName != $preDate && (!in_array($checkfield, $exsting_header_val))) {
						$headerexist = true;
						if($buildingcount>1) {
							$work_rows .= $oldName;
						} else {
							$work_rows .=  $oldName;
						}
						if($wo_row['days_old'] == 0 || $wo_row['days_old'] == 1) {
							$work_rows .= date("m/d/y", strtotime($wo_row['date_requested']));
						} 
						
						
					}
					
					$preDate = $oldName;
					$tenantName = $wo_row['tenantName'];
                    $buildingName = $wo_row['buildingName'];
                    $description = $wo_row['work_order_request'];
                    $wo_status = $wo_row['wo_status'];
                    $wo_id = $wo_row['woId'];
                    $wo_number = $wo_row['wo_number'];
                    $wocnt++;

                    if ($wocnt % 2 == 0) {
					
						if($wo_status == 1) {
							$statusQuery = "SELECT `ws`.*, `ps`.`Time`, `ps`.`length` FROM `wo_schedule_status` AS `ws` INNER JOIN `priority_schedule` AS `ps` ON ps.id = ws.schedule_id WHERE (ws.current_status = '1') AND (ws.worder_id = $wo_id)";
							$statusResult = mysqli_query($this->db->connect(), $statusQuery);
							$statusResult = mysqli_fetch_assoc($statusResult);
							if($statusResult['ckey'] !='') {
								$ackUrl = '"'.BASE_URL.'/appcomplete/preDispatch/woId/'.$statusResult['worder_id'].'/sId/'.$statusResult['schedule_id'].'/ckey/'.$statusResult['ckey'].'/statusChange/tag/acknowledge/userId/'.$uid.'"'; 
							//	$ackbutton ="<button class='btn btn-danger btn-xs' onclick='acknowledgeWorkorder(event, ".$ackUrl.")'  type='button'>Ack</button>";
							}
						} else {
							//$ackbutton ='&nbsp;';
						}
						
				
                        $schedule_query = "SELECT `priority_schedule`.* FROM `priority_schedule` WHERE (priority_id = '" . $wo_row['prioritySchedule'] . "') AND (start_status = '" . $wo_status . "')";
                        $schedule_sql = mysqli_query($this->db->connect(), $schedule_query);
                        $schedule = mysqli_fetch_array($schedule_sql);

                        if ($wo_status == '1') {
                            $lastTime = $wo_row['date_requested'] . ' ' . $wo_row['time_requested'];
                        } else if ($wo_row['updated_at'] != '0000-00-00 00:00:00') {
                            $lastTime = $wo_row['updated_at'];
                        } else {
                            $lastTime = $wo_row['created_date'];
                        }
                        $currTime = date("Y-m-d h:i:s");
                        $lastTime = date("Y-m-d h:i:s", strtotime($lastTime));
                        $timeDiff = strtotime($currTime) - strtotime($lastTime);
						$wo_row['currTime'] = date("Y-m-d h:i:s");
			
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
                            $alert_rows = " <img src='img/alert.png' alt=''/>";
                        } else {
                            if ($wo_status == '1') {
								
								$alert_rows = " <img src='img/bell-icon.png' alt=''/>";
                            } 
                        }	
						if($wo_status == 1) 
						{ 
						    $workbkground = 'newworkorder'; $tdback = "background:#fff";  
						    
						} else { 
						    $workbkground = 'openworkorder'; $tdback = "background:#ddd";  }
						if($buildingcount > 1) {
							$leftbarstyle="background:#".$building_color[$wo_row['building']]."!important;";
						} else {
							$leftbarstyle="background:#".$building_color[$wo_row['building']]."!important; display:none";
						}
						
						$work_rows_array[$wocntnew]['preDa'] = $preDate;
						$work_rows_array[$wocntnew]['wo_id'] = $wo_id;
						$work_rows_array[$wocntnew]['wo_number'] = $wo_number;
						$work_rows_array[$wocntnew]['categoryName'] = $wo_row['categoryName'];
						$work_rows_array[$wocntnew]['alert_rows'] = $alert_rows;
						$work_rows_array[$wocntnew]['tenantName'] = $wo_row['tenantName'];
						$work_rows_array[$wocntnew]['buildingName'] = $buildingName;
						$work_rows_array[$wocntnew]['requesttimeordate'] = $requesttimeordate;
						$work_rows_array[$wocntnew]['wo_status'] = $allstatus[$wo_status];
						$work_rows_array[$wocntnew]['ackbutton'] = $ackUrl;
					
						//$work_rows .= "<tr class='".$workbkground." workorderbackground'><td rowspan='3' class='left_bar'  style='".$leftbarstyle."'></td><td class='work_order_Text firstorder' style='".$tdback."' rowspan='3' onclick='getDetail(" . $wo_id . ");' ><div class='maindata'><h4>".$wo_number."-".$wo_row['categoryName']."</h4></div><div class='alarmright'>".$alert_rows."</div><div class='clearfix'></div><h5>".$wo_row['tenantName']." - ".$buildingName."</h5><p>".$description."</p></td><td class='lasttd'>".$requesttimeordate."</td></tr><tr  class='".$workbkground."' ><td class='lasttd'>".$allstatus[$wo_status]."</td></tr><tr class='".$workbkground."'><td class='lasttd ackbutton'>".$ackbutton."</td></tr>";
					   	
                        
                    } else {
						if($wo_status == 1) {
							$statusQuery = "SELECT `ws`.*, `ps`.`Time`, `ps`.`length` FROM `wo_schedule_status` AS `ws` INNER JOIN `priority_schedule` AS `ps` ON ps.id = ws.schedule_id WHERE (ws.current_status = '1') AND (ws.worder_id = $wo_id)";
							$statusResult = mysqli_query($this->db->connect(), $statusQuery);
							$statusResult = mysqli_fetch_assoc($statusResult);
							//print_r($statusResult);
							if($statusResult['ckey'] !='') {
								$ackUrl = '"'.BASE_URL.'/appcomplete/preDispatch/woId/'.$statusResult['worder_id'].'/sId/'.$statusResult['schedule_id'].'/ckey/'.$statusResult['ckey'].'/statusChange/tag/acknowledge/userId/'.$uid.'"'; 
								$ackbutton ="<button class='btn btn-danger btn-xs' onclick='acknowledgeWorkorder(event, ".$ackUrl.")' type='button'>Ack</button>";
							}
						} else {
							$ackbutton ='&nbsp;';
						}
						
						 $schedule_query = "SELECT `priority_schedule`.* FROM `priority_schedule` WHERE (priority_id = '" . $wo_row['prioritySchedule'] . "') AND (start_status = '" . $wo_status . "')";
                        //echo $schedule_query;
                        $schedule_sql = mysqli_query($this->db->connect(), $schedule_query);
                        $schedule = mysqli_fetch_array($schedule_sql);

                        if ($wo_status == '1') {
                            $lastTime = $wo_row['date_requested'] . ' ' . $wo_row['time_requested'];
                        } else if ($wo_row['updated_at'] != '0000-00-00 00:00:00') {
                            $lastTime = $wo_row['updated_at'];
                        } else {
                            $lastTime = $wo_row['created_date'];
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
                            $alert_rows = " <img src='img/alert.png' alt=''/>";
                        } else {
                            if ($wo_status == '1') {
                                $alert_rows = " <img src='img/bell-icon.png' alt=''/>";
								
                            } 
                        }
						if($wo_status == 1) { $workbkground = 'newworkorder'; $tdback = "background:#fff";  } else { $workbkground = 'openworkorder';  $tdback = "background:#ddd"; }
						if($buildingcount > 1) {
							$leftbarstyle="background:#".$building_color[$wo_row['building']]."!important;";
							
						} else {
							$leftbarstyle="background:#".$building_color[$wo_row['building']]."!important; display:none";
							
						}
						$work_rows_array[$wocntnew]['preDa'] = $preDate;
						$work_rows_array[$wocntnew]['wo_id'] = $wo_id;
						$work_rows_array[$wocntnew]['wo_number'] = $wo_number;
						$work_rows_array[$wocntnew]['categoryName'] = $wo_row['categoryName'];
						$work_rows_array[$wocntnew]['alert_rows'] = $alert_rows;
						$work_rows_array[$wocntnew]['tenantName'] = $wo_row['tenantName'];
						$work_rows_array[$wocntnew]['buildingName'] = $buildingName;
						$work_rows_array[$wocntnew]['requesttimeordate'] = $requesttimeordate;
						$work_rows_array[$wocntnew]['wo_status'] = $allstatus[$wo_status];
						$work_rows_array[$wocntnew]['ackbutton'] = $ackUrl;
						
						
						//$work_rows .= "<tr class='".$workbkground." workorderbackground'><td rowspan='3' class='left_bar' style='".$leftbarstyle."'></td><td class='work_order_Text firstorder'  rowspan='3' style='".$tdback."' onclick='getDetail(" . $wo_id . ");' ><div class='maindata'><h4>".$wo_number."-".$wo_row['categoryName']."</h4></div><div class='alarmright'> ".$alert_rows."</div><div class='clearfix'>  </div><h5>".stripslashes($wo_row['tenantName'])." - ".stripslashes($buildingName)."</h5><p >".stripslashes($description)."</p></td><td class='lasttd'>".$requesttimeordate."</td></tr><tr class='".$workbkground."'><td class='lasttd' >".$allstatus[$wo_status]."</td></tr><tr class='".$workbkground."'><td class='lasttd ackbutton'>".$ackbutton."</td></tr>";
                       
                    }
                    $wocntnew++;
                }
                

            } 
			
            //}

            //$work_rows = "<tr class='vt_set_yellow'><td colspan='5'>Data not found for this User !</td></tr>";
       
		$wo_return['work_rows'] = $work_rows;
		$wo_return['work_rows_array'] = $work_rows_array;
		$wo_return['max_wo_val'] = $max_wo_val;
		$wo_return['headerexist'] = $headerexist;
		$wo_return['newcount'] = $newcount;
		echo "<pre>";
		print_r($wo_return);die;
        return $wo_return;
    }
	function deleteAttachment($wfId) 
	{	if($wfId != '') {
			try {
				$filequery = "SELECT file_name as attachment FROM `wo_files`
				WHERE (wfId='" . $wfId . "')";
				$filesql = mysqli_query($this->db->connect(), $filequery);
				$filedata = mysqli_fetch_assoc($filesql);
				$file = BASE_PATH.'public/work_order/'.$filedata['attachment'];
				$query = "DELETE FROM wo_files where wfId=".$wfId;
				$sql = mysqli_query($this->db->connect(), $query);
				unlink($file);
				echo json_encode(array('error'=>false));
			}
			catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
	}
	
	function uploadAttachment($file_name, $woId) 
	{
	
		if($file_name != '' && $woId != '') {
			try {
				$query = "INSERT INTO wo_files (file_title,file_name,woId) Values('". $file_name ."', '". $file_name ."' , ". $woId .")";
				$sql = mysqli_query($this->db->connect(), $query); 
				echo json_encode(array('error'=>false));
			} catch(Exception $e)  {
				echo  'Cought exception: ', $e->getMessage(), "\n";
			}
		}
	
	}

    function getWorkOrderDetail($role_id, $uid, $cust_id, $woid) {
        $query = "SELECT `wo`.*, `wop`.*, `t`.`tenantName`, `t`.`tenantContact`, `bu`.`build_id`, `bu`.`buildingName`, `cat`.`categoryName`, `cat`.`send_email`, `pt`.`priorityName`, `pt`.`pid`, `u`.`firstName`, `u`.`lastName`, `u`.`email`, `u`.`phoneNumber`, `tu`.`suite_location` AS `tenant_suite`
			FROM `work_order` AS `wo`
			INNER JOIN `tenant` AS `t` ON t.id = wo.tenant
			LEFT JOIN `buildings` AS `bu` ON bu.build_id = wo.building
			LEFT JOIN `category` AS `cat` ON cat.cat_id = wo.category
			LEFT JOIN `priority` AS `pt` ON pt.pid = cat.prioritySchedule
			LEFT JOIN `users` AS `u` ON wo.create_user = u.uid
			LEFT JOIN `work_order_update` AS `wop` ON wop.wo_id = wo.woId AND wop.current_update=1
			LEFT JOIN `tenantusers` AS `tu` ON wo.create_user = tu.userId
			
			WHERE (wo.woId='" . $woid . "') ";
        $sql = mysqli_query($this->db->connect(), $query);

        $data = mysqli_fetch_array($sql);
		
		
		$filequery = "SELECT wfId, file_name as attachment FROM `wo_files`
			WHERE (woId='" . $woid . "')";
        $filesql = mysqli_query($this->db->connect(), $filequery);
		$filename=array();
        while($filedata = mysqli_fetch_assoc($filesql)) {
			$filename[] = $filedata['attachment'].'|||'.$filedata['wfId'];
		}
		$filename = implode(",",$filename);
		$data['attachment'] = $filename;
		
		$query_closed = "SELECT status_closed from wo_parameter where building=".$data['build_id'];
		$sql_closed = mysqli_query($this->db->connect(), $query_closed);
		$status_closed = mysqli_fetch_assoc($sql_closed);
		if($status_closed['status_closed'] == 1) {
			//$data['wo_status'] = 7;
		}
		
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
        $sql = mysqli_query($this->db->connect(), $query);
        $cnt = mysqli_num_rows($sql);
        if ($cnt == 1) {
            $data = array();
            $data_arr = mysqli_fetch_array($sql);
            $data[] = $data_arr['woId'];
            return $data;
        } else if ($cnt > 1) {
            $data = array();
            while ($row = mysqli_fetch_array($sql)) {
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

        $sql = mysqli_query($this->db->connect(), $query);
        $cnt = mysqli_num_rows($sql);
        $evenodd = 0;
        $html = "";
        if ($cnt > 0) {
            while ($row = mysqli_fetch_array($sql)) {
                if (($evenodd % 2) == 0) {
                    $evenodd++;
                    $html .= "<tr style='background:#f1f1f1 ;'><td style='font-size:17px; weight:700; height:10px;'>" . $row['description'] . "</td></tr>";
                } else {
                    $evenodd++;
                    $html .= "<tr style='background:#f1f1f1 ;'><td style='font-size:17px; weight:700; height:10px;'>" . $row['description'] . "</td></tr>";
                }
            }  $html .= '<tr style="background:#f1f1f1;"><td style="text-align:center; font-size:17px; weight:700" ></td></tr>';;
        } else {
            $html .= '<tr style="background:#f1f1f1;"><td style="text-align:center; font-size:17px; weight:700" >Click the "Add/Modify" button to created</td></tr>';
        }

        return $html;

    }

    function getLabor($woid) {
		$totallabourcost = 0;
        $query = "SELECT br.brid,`l`.`lid`, `l`.`woId`, `l`.`emp_id`, `l`.`charge_hour` , `l`.`bl_id`, `l`.`rate_charge`, `l`.`job_time`, `u`.`uid`, `u`.`firstName`, `u`.`lastName`, `br`.`rate_name`, `br`.`description`, `br`.`multiplier`
			FROM `labor` AS `l`
			LEFT JOIN `users` AS `u` ON u.uid = l.emp_id
			LEFT JOIN `bill_rate` AS `br` ON l.rate_charge = br.brid
			WHERE (l.woId = '" . $woid . "')";

        //echo $query;
        //$rate_charge = 5;
        $sql = mysqli_query($this->db->connect(), $query);
        $cnt = mysqli_num_rows($sql);
        $evenodd = 0;
        $html = "";
        $total_labor_charge = 0.00;
        if ($cnt > 0) {
            while ($row = mysqli_fetch_array($sql)) {
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
                    $html .= "<tr style=\"background:#f9f9f9; border:1px solid #000 \" onclick=\"trEventClick()\"><td style=\" height:37px;\">" . $row['firstName'] . " " . $row['lastName'] . "</td><td>$" . $row['charge_hour'] . "</td><td>" . $row['rate_name'] . "</td><td>" . $row['job_time'] . "<div class='select_action'>
								<ul>
								   <li><a onclick=\"updateLabourDataTemp('" . $row['emp_id'] . "', '" . $row['bl_id'] . "',  '" . $row['charge_hour'] . "', '" . $row['rate_charge'] . "','" . $row['job_time'] . "', '" . $row['woId'] . "', '" . $row['lid'] . "')\">Edit</a></li>
								  <li><a onclick=\"deleteLabourTemp('" . $row['woId'] . "','" . $row['lid'] . "')\">Delete</a></li>
								  <li><a class=\"closeActionPopUp\">Close</a></li>
							  </ul>
							</div></td>
					</tr>";
                } else {
                    $evenodd++;
                    $html .= "<tr style=\"background:#f9f9f9; border:1px solid #000 \" onclick=\"trEventClick()\"><td style=\" height:37px;\" >" . $row['firstName'] . " " . $row['lastName'] . "</td><td>$" . $row['charge_hour'] . "</td><td>" . $row['rate_name'] . "</td><td>" . $row['job_time'] . "<div class='select_action'>
								<ul>
								   <li><a onclick=\"updateLabourDataTemp('" . $row['emp_id'] . "', '" . $row['bl_id'] . "',  '" . $row['charge_hour'] . "', '" . $row['rate_charge']  . "','" . $row['job_time'] . "', '" . $row['woId'] . "', '" . $row['lid'] . "')\">Edit</a></li>
								  <li><a onclick=\"deleteLabourTemp('" . $row['woId'] . "','" . $row['lid'] . "')\">Delete</a></li>
								  <li><a class=\"closeActionPopUp\">Close</a></li>
							  </ul>
							</div></td></tr>";
                }
            } $html.="<tr style=\"background:#f9f9f9; \" > <td colspan=\"5\"></td></tr>";
        } else {
            $html .= '<tr style="background:#f9f9f9;"><td style="text-align:center;" colspan="5">Click the "Create" to enter</td></tr>';
        }

        return compact("html", "total_labor_charge");

    }

    function getBuildingServices($woid) {

        $query = "SELECT `bs`.`bsId`, `bs`.`woId`, `bs`.`service`, `bs`.`charge`, `bs`.`amount_requested`, `bs`.`comment`, `bserv`.`service_name`, `bserv`.`unit_measure`, `bserv`.`minimum` , `bserv`.`building`
			FROM `building_service` AS `bs`
			LEFT JOIN `build_service` AS `bserv` ON bserv.bsid = bs.service
			WHERE (bs.woId = '" . $woid . "')";

        //echo $query; exit;

        $sql = mysqli_query($this->db->connect(), $query);
        $cnt = mysqli_num_rows($sql);
        $evenodd = 0;
        $html = "";
        $total_bs_charge = 0;
        if ($cnt > 0) {
            while ($row = mysqli_fetch_array($sql)) {
                $bs_charge = 0;
                $bs_charge = number_format(($row['charge'] * $row['amount_requested']), '2', '.', '');
                $total_bs_charge = $total_bs_charge + $bs_charge;

                if (($evenodd % 2)) {
                    $evenodd++;
                    $html .= "<tr style=\"background:#f9f9f9; border:1px solid #000;\" onclick=\"trEventClick()\"><td style=\"height:37px;\">" . $row['service_name'] . "</td><td>" . $row['amount_requested'] . "</td><td>" . $row['comment'] . "	<div class='select_action'>
								<ul>
								   <li><a onclick=\"updateBServiceDataTemp('" . $row['bsId'] . "', '" . $row['service'] . "', '" . $row['charge'] . "', '" . $row['amount_requested'] . "', '" . $row['comment'] . "', '" . $row['woId'] . "', '" . str_replace("'", ";-", $row['service_name']) . "', '" . $row['building'] . "')\">Edit</a></li>
								  <li><a onclick=\"deleteBserviceTemp('" . $row['bsId'] . "')\">Delete</a></li>
								  <li><a class=\"closeActionPopUp\">Close</a></li>
								
							  </ul>
							</div>
					</td>
					
					</tr>";
                } else {
                    $evenodd++;
                    $html .= "<tr style=\"background:#f9f9f9; border:1px solid #000;\" onclick=\"trEventClick()\"><td style=\"height:37px;\">" . $row['service_name'] . "</td><td>" . $row['amount_requested'] . "</td><td>" . $row['comment'] . "<div class='select_action'>
								<ul>
								  <li><a onclick=\"updateBServiceDataTemp('" . $row['bsId'] . "', '" . $row['service'] . "', '" . $row['charge'] . "', '" . $row['amount_requested'] . "', '" . $row['comment'] . "', '" . $row['woId'] . "', '" . str_replace("'", ";-", $row['service_name']) . "', '" . $row['building'] . "')\">Edit</a></li>
								  <li><a onclick=\"deleteBserviceTemp('" . $row['bsId'] . "')\">Delete</a></li>
								  <li><a class=\"closeActionPopUp\">Close</a></li>
							  </ul>
							</div></td>				
					</tr>";
                }
            } $html .= '<tr style="background:#f9f9f9;"><td style="text-align:center;" colspan="4"></td></tr>';
        } else {
            $html .= '<tr style="background:#f9f9f9;"><td style="text-align:center;" colspan="4">Click the "Create" to enter</td></tr>';
        }

        return compact("html", "total_bs_charge");

    }

    function getMaterials($woid) {

        $query = "SELECT `mc`.`mcId`,`m`.`mid`, `mc`.`woId`, `mc`.`material_id`, `mc`.`cost`, `mc`.`quantity`, `mc`.`markup`, `mc`.`tax`, `m`.`mid`, `m`.`description`
			FROM `material_charge` AS `mc`
			LEFT JOIN `material` AS `m` ON m.mid = mc.material_id
			WHERE (mc.woId = '" . $woid . "')";

        //echo $query;

        $sql = mysqli_query($this->db->connect(), $query);
        $cnt = mysqli_num_rows($sql);
        $evenodd = 0;
        $html = "";
        $total_material_charge = 0.00;
        $tot_material_tax = 0;
        $tot_mat_mkp = 0;
        if ($cnt > 0) {
            while ($row = mysqli_fetch_array($sql)) {
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
                    $html .= "<tr style=\"background:#f9f9f9; border: 1px solid #000\" onclick=\"trEventClick()\"><td style=\"height:37px;\">" . $row['description'] . "</td><td>" . $row['quantity'] . "<div class='select_action'>
							<ul>
							   <li><a onclick=\"updateMaterialsDataTemp('" . $row['cost'] . "', '" . $row['quantity'] . "', '" . $val_tax . "', '" . $row['markup'] . "','" . $row['mid'] . "', '" . $row['woId'] . "', '" . $row['mcId'] . "')\">Edit</a></li>
							  <li><a onclick=\"deleteMaterialTemp('" . $row['woId'] . "','" . $row['mcId'] . "')\">Delete</a></li>
							  <li><a class=\"closeActionPopUp\">Close</a></li>
						  </ul>
						</div></td></tr>";
                } else {
                    $evenodd++;
                    $html .= "<tr style=\"background:#f9f9f9; border: 1px solid #000\" onclick=\"trEventClick()\"><td style=\"height:37px;\">" . $row['description'] . "</td><td>" . $row['quantity'] ."<div class='select_action'>
						<ul>
						   <li><a onclick=\"updateMaterialsDataTemp('" . $row['cost'] . "', '" . $row['quantity'] . "', '" . $val_tax . "', '" . $row['markup'] . "','" . $row['mid'] . "', '" . $row['woId'] . "', '" . $row['mcId'] . "')\">Edit</a></li>
						  <li><a onclick=\"deleteMaterialTemp('" . $row['woId'] . "','" . $row['mcId'] . "')\">Delete</a></li>
						  <li><a class=\"closeActionPopUp\">Close</a></li>
					  </ul>
					</div> </td></tr>";
                }
            } $html .="<tr style=\"background:#f9f9f9;\" onclick=\"trEventClick()\"><td colspan=\"3\" ></td></tr>";
        } else {
            $html .= '<tr style="background:#f9f9f9;"><td style="text-align:center;" colspan="3">Click the "Create" to enter</td></tr>';
        }

        return compact("html", "total_material_charge");

    }

    function getOutsideServices($woid) {

        $query = "SELECT `os`.`osId`,v.vid, `os`.`woId`, `os`.`vendor`, `os`.`job_cost`, `os`.`job_description`, `os`.`markup`, `os`.`tax`, `v`.`vid`, `v`.`company_name`
			FROM `outside_service` AS `os`
			LEFT JOIN `vendor` AS `v` ON v.vid = os.vendor
			WHERE (os.woId = '" . $woid . "')";

        //echo $query;

        $sql = mysqli_query($this->db->connect(), $query);
        $cnt = mysqli_num_rows($sql);
        $evenodd = 0;
        $total_outside_charge = 0.00;
        $tot_outside_tax = 0;
        $tot_outside_mkp = 0;
        $html = "";
        if ($cnt > 0) {
            while ($row = mysqli_fetch_array($sql)) {
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
                    $html .= "<tr style=\"background:#f9f9f9;border:1px solid #000;\" onclick=\"trEventClick()\"><td style=\"height:37px;\">" . $row['company_name'] . "</td><td>" . $row['job_description'] . "</td><td>$" . $row['job_cost']  .  "<div class='select_action'>
							<ul>
							   <li><a onclick=\"updateOutsideServiceDataTemp('" . $row['vid'] . "', '" . $row['job_description'] . "', '" . $row['job_cost'] . "', '" . $row['markup'] . "','" . $tax . "', '" . $row['woId'] . "', '" . $row['osId'] . "')\">Edit</a></li>
							  <li><a onclick=\"deleteOutsideTemp('" . $row['woId'] . "','" . $row['osId'] . "')\">Delete</a></li>
							  <li><a class=\"closeActionPopUp\">Close</a></li>
						  </ul>
						</div></td>
					</tr>";
                } else {
                    $evenodd++;
                    $html .= "<tr style=\"background:#f9f9f9; border:1px solid #000;\" onclick=\"trEventClick()\"><td style=\"height:37px;\">" . $row['company_name'] . "</td><td>" . $row['job_description'] . "</td><td>$" . $row['job_cost'] .  "<div class='select_action'>
							<ul>
							   <li><a onclick=\"updateOutsideServiceDataTemp('" . $row['vid'] . "', '" . $row['job_description'] . "', '" . $row['job_cost'] . "', '" . $row['markup'] . "','" . $tax . "', '" . $row['woId'] . "', '" . $row['osId'] . "')\">Edit</a></li>
							  <li><a onclick=\"deleteOutsideTemp('" . $row['woId'] . "','" . $row['osId'] . "')\">Delete</a></li>
							  <li><a class=\"closeActionPopUp\">Close</a></li>
						  </ul>
						</div></td>
					</tr>";
                }
            } $html .= '<tr style="background:#f9f9f9;"><td style="text-align:center;" colspan="7"></td></tr>';
        } else {
            $html .= '<tr style="background:#f9f9f9;"><td style="text-align:center;" colspan="7">Click the "Create" to enter</td></tr>';
        }

        return compact("html", "total_outside_charge");

    }

    function getNotes($woid) {

        $query = "SELECT `wo_note`.* FROM `wo_note` WHERE (woId = '" . $woid . "' )";

        //echo $query;

        $sql = mysqli_query($this->db->connect(), $query);
        $cnt = mysqli_num_rows($sql);
        $evenodd = 0;
        $html = "";
        if ($cnt > 0) {
            while ($row = mysqli_fetch_array($sql)) {

                $date = date('m/d/Y', strtotime($row['note_date']));

                if ($row['internal'] == 1) {
                    $internal = "Y";
                } else {
                    $internal = "N";
                }

                if (($evenodd % 2)) {
                    $evenodd++;
                    $html .= "<tr style=\"background:#f9f9f9; border:1px solid #000\" onclick=\"trEventClick()\"><td style=\"height:37px;\">" . $date . "</td><td>" . $row['note'] . "</td><td>" . $internal . "<!--<div class='select_action'>
												<ul>
												   <li><a onclick=\"updateNotesDataTemp('" . $date . "', '" . $row['note'] . "', '" . $internal . "', '" . $row['woId'] . "', '" . $row['wnId'] . "')\">Edit</a></li>
												  <li><a onclick=\"deleteNoteTemp('" . $row['wnId'] . "')\">Delete</a></li>
												  <li><a class=\"closeActionPopUp\">Close</a></li>
											  </ul>
											</div>--></td>
					</tr>";
                } else {
                    $evenodd++;
					$html .= "<tr style=\"background:#f9f9f9;border:1px solid #000\" onclick=\"trEventClick()\"><td style=\"height:37px;\">" . $date . "</td><td>" . $row['note'] . "</td><td>" . $internal . "<!-- <div class='select_action'>
						<ul>
						   <li><a onclick=\"updateNotesDataTemp('" . $date . "', '" . $row['note'] . "', '" . $internal . "', '" . $row['woId'] . "', '" . $row['wnId'] . "')\">Edit</a></li>
						  <li><a onclick=\"deleteNoteTemp('" . $row['wnId'] . "')\">Delete</a></li>
						  <li><a class=\"closeActionPopUp\">Close</a></li>
					  </ul>
					</div>--></td>
					</tr>";
                }
            } $html .= '<tr style="background:#f9f9f9;"><td style="text-align:center;" colspan="5"></td></tr>';
        } else {
            $html .= '<tr style="background:#f9f9f9;"><td style="text-align:center;" colspan="5">Click the "Create" to enter</td></tr>';
        }

        return $html;

    }

    function updateWorkOrderStatus($woid, $role_id, $uid, $cust_id, $wo_status, $new_wo_status, $building_id) {

        
		$query_billable = "SELECT billable from wo_parameter where building=".$building_id;
		$sql_billable = mysqli_query($this->db->connect(), $query_billable);
		$status_billable = mysqli_fetch_assoc($sql_billable);
		/******************* set timezone through building id ****************************/
        $ob = new Helper();
        $ob->setTimezone($building_id);
        /******************* end - set timezone through building id ****************************/
        $created_at = date('Y-m-d H:i:s');
		$status =array(1);
		$curstatusquery = "SELECT wo_status FROM work_order_update where wo_id =". $woid . " AND current_update = 1";
		$curstatusrow = mysqli_query($this->db->connect(), $curstatusquery);
		$curstatusval = mysqli_fetch_assoc($curstatusrow);
		if($curstatusval['wo_status'] ==1) {
			$this->sendOnlyBadge($building_id,$status );
		}

        $query = "insert into work_order_update (wo_id, wo_status, billable_opt, created_at, current_update, user_id) values('" . $woid . "', '" . $new_wo_status . "', '" . $status_billable['billable'] . "', '" . $created_at . "', '1', '" . $uid . "')";
        $sql = mysqli_query($this->db->connect(), $query);

        if ($sql) {
            $q = "select upId from work_order_update where user_id = '" . $uid . "' ORDER BY upId DESC LIMIT 1 ";
            $q_sql = mysqli_query($this->db->connect(), $q);
            $q_row = mysqli_fetch_array($q_sql);

            $query = "update work_order_update set current_update = '0' where upId != '" . $q_row['upId'] . "' AND wo_id = '" . $woid . "'";

            $update_sql = mysqli_query($this->db->connect(), $query);

            if ($update_sql) {
                $query_history = "insert into wo_history_log (woId, log_type, current_value, change_value, user_id, created_at) values('" . $woid . "', 'status', '" . $wo_status . "', '" . $new_wo_status . "', '" . $uid . "', '" . $created_at . "')";

                $update_sql_history = mysqli_query($this->db->connect(), $query_history);

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
	$countquery = "SELECT count(*) as countbuilding
		FROM `buildings` AS `bu`
		LEFT JOIN `states` AS `st` ON st.state_code = bu.state_code
		LEFT JOIN `states` AS `bst` ON bst.state_code = bu.billState_code
		WHERE (bu.cust_id='" . $cust_id . "')
		AND (bu.status='1')
		AND bu.build_id IN (select DISTINCT building_id from user_building_module_access where user_id = '" . $uid . "')";
	$countsql = mysqli_query($this->db->connect(), $countquery);
	$countrow = mysqli_fetch_array($countsql);
        $query = "SELECT `bu`.*, `st`.`state` AS `statename`, `bst`.`state` AS `billstatename`
		FROM `buildings` AS `bu`
		LEFT JOIN `states` AS `st` ON st.state_code = bu.state_code
		LEFT JOIN `states` AS `bst` ON bst.state_code = bu.billState_code
		WHERE (bu.cust_id='" . $cust_id . "')
		AND (bu.status='1')
		AND bu.build_id IN (select DISTINCT building_id from user_building_module_access where user_id = '" . $uid . "')  order by bu.buildingName ASC ";

        $sql = mysqli_query($this->db->connect(), $query);
		if($countrow['countbuilding']==1) { 
			$option_building = '';
		} else { 
			$option_building = '<option value="">--select--</option>';
		}
		
        $cnt = 0;
        $first_building = "";
		$auto_cnt = 0;
		
        while ($row = mysqli_fetch_array($sql)) {
            $cnt++;
            if ($cnt == 1) {
                $first_building = $row['build_id'];
				$auto_cnt = 1;
            } else {
				$auto_cnt = 0;
			}
            $option_building .= "<option value='" . $row['build_id'] . "'>" . $row['buildingName'] . "</option>";
        }

        return $option_building . ":" . $first_building . ":" .$auto_cnt;
    }

    function getCompanyByUser($role_id, $uid, $cust_id, $build_id) {
		$countquery = "SELECT count(*) as countbuilding
		FROM `tenant` AS `t`
		LEFT JOIN `users` AS `u` ON u.uid = t.userId
		WHERE (t.buildingId='" . $build_id . "') AND (t.remove_status=0)";
		$countsql = mysqli_query($this->db->connect(), $countquery);
		$countrow = mysqli_fetch_array($countsql);
        $query = "SELECT `t`.*, `u`.`uid`, `u`.`firstName` AS `firstname`, `u`.`lastName` AS `lastname`, `u`.`phoneNumber` AS `phonenumber`, `u`.`email`, `u`.`role_id`
		FROM `tenant` AS `t`
		LEFT JOIN `users` AS `u` ON u.uid = t.userId
		WHERE (t.buildingId='" . $build_id . "') AND (t.remove_status=0) order by t.tenantName ASC";
        //$query = "select * from company where cust_id = '".$cust_id."'";
        //echo $query;
        $sql = mysqli_query($this->db->connect(), $query);
        $option_company = "";
       // $option_building .= "<option value=''>Select Building</option>";
        $cnt = 0;
		$auto_cnt = 0;
        $first_companyuser = "";
		if($countrow['countbuilding']==1) { 
			$option_company = '';
		} else {
			$option_company = '<option value="">--select--</option>';
		}
        while ($row = mysqli_fetch_array($sql)) {
            $cnt++;
            if ($cnt == 1) {
                $first_companyuser = $row['id'];
				$auto_cnt = 1;
            } else {
				$auto_cnt = 0;
			}
            $option_company .= "<option value='" . $row['id'] . "'>" . $row['tenantName'] . "</option>";
        }

        return $option_company . ":" . $first_companyuser. ":" .$auto_cnt;
    }

    function getTenants($role_id, $uid, $cust_id, $tnadmin_id) {
		$countquery = "SELECT count(*) as countbuilding
		FROM `users` AS `u`
		INNER JOIN `tenantusers` AS `tu` ON u.uid = tu.userId
		WHERE (u.remove_status = '0') AND (tu.tenantId = '" . $tnadmin_id . "')";
		$countsql = mysqli_query($this->db->connect(), $countquery);
		$countrow = mysqli_fetch_array($countsql);
        $query = "SELECT `u`.`uid`, `u`.`userName`, `u`.`firstName`, `u`.`lastName`, `u`.`email`, `u`.`role_id`, `u`.`phoneNumber`, `u`.`phoneExt`, `u`.`Title`, `u`.`status`, `tu`.`tenantId`, `tu`.`cc_enable`, `tu`.`send_as`, `tu`.`complete_notification`
		FROM `users` AS `u`
		INNER JOIN `tenantusers` AS `tu` ON u.uid = tu.userId
		WHERE (u.remove_status = '0') AND (tu.tenantId = '" . $tnadmin_id . "')";
        $sql = mysqli_query($this->db->connect(), $query);
		$auto_cnt = 0;
		if($countrow['countbuilding']==1) { 
			$option_company = '';
			$auto_cnt = 1;
		} else {
			$option_company = '<option value="">--select--</option>';
			$auto_cnt = 0;
		}
       
        while ($row = mysqli_fetch_array($sql)) {
            $option_company .= "<option value='" . $row['uid'] . "'>".$row['lastName'].", ".$row['firstName']."</option>";
        }

        return $option_company. ":" .$auto_cnt;
    }

    function getCompanyCategory($role_id, $uid, $cust_id, $build_id) {
		$countquery = "SELECT count(*) as countbuilding
		FROM `category`
		WHERE (building_id = '" . $build_id . "') AND (remove_status = 0) ORDER BY `categoryName` ASC";
		$countsql = mysqli_query($this->db->connect(), $countquery);
		$countrow = mysqli_fetch_array($countsql);
        $query = "SELECT `category`.*
		FROM `category`
		WHERE (building_id = '" . $build_id . "') AND (remove_status = 0) ORDER BY `categoryName` ASC";
        $sql = mysqli_query($this->db->connect(), $query);
		$auto_cnt = 0;
		if($countrow['countbuilding']==1) { 
			$company_category = '';
			$auto_cnt = 1;
		} else {
			$company_category = '<option value="">--select--</option>';
			$auto_cnt = 0;
		}
      
        while ($row = mysqli_fetch_array($sql)) {
            $company_category .= "<option value='" . $row['cat_id'] . "'>" . $row['categoryName'] . "</option>";
        }

        return $company_category. ":" .$auto_cnt;
    }

    function getBuildingServicesPopUp($build_id) {
        $query = "SELECT `build_service`.* FROM `build_service` WHERE (building = '" . $build_id . "' ) AND (status = '1' )";

        $sql = mysqli_query($this->db->connect(), $query);
        $building_service = "";
        $building_service .= "<option value=''>-Select-</option>";
        while ($row = mysqli_fetch_array($sql)) {
            $building_service .= "<option value='" . $row['bsid'] . "' flag_id='" . $row['unit_measure'] . "'>" . $row['service_name'] . "</option>";
        }
        return $building_service;
    }
    function getUpdateServicesCost($bsid) {
        $query = "SELECT `build_service`.* FROM `build_service` WHERE (bsid = '" . $bsid . "' ) AND (status = '1' )";
        $sql = mysqli_query($this->db->connect(), $query);
        $row = mysqli_fetch_array($sql);
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

    function createSaveWorkOrder($role_id, $uid, $cust_id, $buildings_list, $company_list, $tenantusers_list, $category_list, $current_date_field, $current_time_field, $internal_wo, $wo_request, $wo_notes, $file_name='') {
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
        $suite_query = "select suite_location from tenantusers where tenantId = '" . $company_list . "'";
        //echo $suite_query; exit;
        $suite_sql = mysqli_query($this->db->connect(), $suite_query);
        $suite_data = mysqli_fetch_array($suite_sql);
        $suite_location = $suite_data['suite_location'];
        /////end of getting suit location

        /////getting last wo Number
        $wo_number_query = "select wo_number from work_order where building = '" . $buildings_list . "' ORDER BY wo_number DESC LIMIT 1";
        $wo_number_sql = mysqli_query($this->db->connect(), $wo_number_query);
        $wo_number_data = mysqli_fetch_array($wo_number_sql);
        $wo_number = $wo_number_data['wo_number']; 
		if($wo_number) {
		    $wo_number = $wo_number + 1; 
		} else {
			$wo_number = 1001;
		} 
       
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
		'" . addslashes($suite_location ). "',
		'" . addslashes($suite_location ). "',
		'" . addslashes($tenantusers_list) . "',
		'" . $current_date_field . "',
		'" . $current_time_field . "',
		'" . $category_list . "',
		'1',
		'" . $master_internal_wo . "',
		'" . addslashes($wo_request) . "',
		'" . $created_at . "',
		'" . $wo_number . "',
		'" . $uid . "')";
        $res = mysqli_query($this->db->connect(), $query);
        $wo_id = "SELECT woId FROM `work_order` ORDER BY `woId` DESC LIMIT 1";
        $wo_id_sql = mysqli_query($this->db->connect(), $wo_id);
        $wo_id_data = mysqli_fetch_array($wo_id_sql);
        $last_id = $wo_id_data['woId'];
        
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
		if($wo_notes!='') {
			$insert_notes ="insert into wo_note(woId, note_date, note, internal, created_at, user_id) values('" . $last_id. "','" . $current_date. "','" . $wo_notes. "','" . 1 . "','" . $created_at. "','" . $uid. "')";
		    $res_notes = mysqli_query($this->db->connect(), $insert_notes);
		}
		
		
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
		'" . addslashes($wo_notes) . "',
		'1',
		'1',
		'" . date('Y-m-d H:i:s') . "',
		'" . $uid . "')";
        $res_update = mysqli_query($this->db->connect(), $query_update);
        $last_id_update = mysqli_insert_id($this->db->connect());

        //////insert work order schedule
		
        /////insert for workorder file upload
		if($file_name!='') {
			$explode_file = explode(',', $file_name);
			$countfile = count($explode_file); 
			for($i=0; $i<$countfile; $i++ ) {
			$query_fileUpload = "insert into wo_files (woId,file_title,file_name,created_at)
			values(
			'" . $last_id . "',
			'" . $explode_file[$i] . "',
		   '" .  $explode_file[$i] . "',
			'" . date('Y-m-d H:i:s') . "'
			)";
			$query_fileUploadres = mysql_query($query_fileUpload);
			}
		}
        ///end insert for workorder file upload

        //// getting priority
        $priority_query = "SELECT `pr`.*, `cat`.`cat_id`
		FROM `priority` AS `pr`
		INNER JOIN `category` AS `cat` ON cat.prioritySchedule = pr.pid
		WHERE (cat.cat_id = '" . $category_list . "')";
        $priority_sql = mysqli_query($this->db->connect(), $priority_query);
        $priority_res = mysqli_fetch_array($priority_sql);

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
        $schedule_sql = mysqli_query($this->db->connect(), $schedule_query);
        $schedule_res = mysqli_fetch_array($schedule_sql);
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

        $wo_shedule_status_sql = mysqli_query($this->db->connect(), $wo_schedule_status_query);

        //////end of insert work order schedule

     
        $get_tenant_res = [];
        $get_tenant_query = "SELECT `t`.*, `u`.`uid`, `u`.`firstName` AS `firstname`, `u`.`lastName` AS `lastname`, `u`.`phoneNumber` AS `phonenumber`, `u`.`email`, `u`.`role_id`, `st`.`state` AS `statename`
	FROM `tenant` AS `t`
	LEFT JOIN `users` AS `u` ON u.uid = t.userId
	LEFT JOIN `states` AS `st` ON st.state_code = t.state_code
	WHERE (t.id='" . $company_list . "')";
        $get_tenant_sql = mysqli_query($this->db->connect(), $get_tenant_query);
        $get_tenant_res = mysqli_fetch_array($get_tenant_sql); 
        $get_tenant_res['tenantId'] = $get_tenant_res['id'];
        $get_tenant_res['woId'] = $worder_id;
		$pushInfo = array();
		$pushInfo['tenantName'] = $get_tenant_res['tenantName'];
		$pushInfo['buildings_list'] = $buildings_list;
		$pushInfo['wo_number'] = $wo_number;
		$pushInfo['category_list'] = $category_list;
		$pushInfo['work_order_request'] = $wo_request;
		
		// Send push Notification for android
	
		//$this->sendPushNotification($pushInfo);

        return $get_tenant_res;
    }
	
	

    function getWoDescription($woid) {
        $query = "SELECT description from work_description where woId = '" . $woid . "' ORDER BY id DESC LIMIT 1";
        $sql = mysqli_query($this->db->connect(), $query);
        $cnt = mysqli_num_rows($sql);
        if ($cnt > 0) {
            $row = mysqli_fetch_array($sql);
            return $row['description'];
        } else {
            return false;
        }

    }
	function checkPushId($uid, $pushId, $device) {
		$query = "SELECT user_id, push_id, badges, status FROM push_tokens WHERE  push_id = '" . $pushId . "'";
		$query_sql = mysqli_query($this->db->connect(), $query);
		$data = mysqli_fetch_assoc($query_sql); 
		if(!$data) {
			$query_ins ="INSERT INTO push_tokens set user_id ='" . $uid . "', push_id ='" . $pushId . "', device='" . $device . "', status ='1'";
			mysqli_query($this->db->connect(), $query_ins);
			$this->sendBadgeAfterLogin($uid, $pushId, $device);
		} else {  
			if($data['user_id'] != $uid) { 
				$query_up ="UPDATE push_tokens set user_id ='". $uid . "' WHERE  push_id = '" . $pushId . "'";
				mysqli_query($this->db->connect(), $query_up);
				$this->sendBadgeAfterLogin($uid, $pushId, $device);
			}
			if($data['status'] == 0) {
			$query_up ="UPDATE push_tokens set status ='1' WHERE  push_id = '" . $pushId . "'";
			mysqli_query($this->db->connect(), $query_up);
			$this->sendBadgeAfterLogin($uid, $pushId, $device);
			}
			
			if($data['badges'] != '0') {
				echo $query_up ="UPDATE push_tokens set badges ='0' WHERE  push_id = '" . $pushId . "'";
				mysqli_query($this->db->connect(), $query_up);
			}
		}
		
		
		
		return $data;
	}
	
	function removepushId($uid, $pushId) {
		$query = "SELECT device from push_tokens WHERE push_id='" . $pushId . "'";
		$row = mysqli_query($this->db->connect(), $query);
		$device_token = mysqli_fetch_assoc($row);
		$this->removeBadge($pushId, $device_token['device']);
		$query = "UPDATE push_tokens set status ='0' where user_id = '" . $uid . "' AND push_id = '" . $pushId . "'";
		$query_sql = mysqli_query($this->db->connect(), $query);
		return $query_sql;
	}
	
	function sendBadgeAfterLogin($uid,$pushid,$device) { 
	
		    $res = $this->getWorkorderbystatus($uid, array(1));
			$badgescount = $res['count_workorder'];
			$registrationIds = $pushid;
			if($device == 1) {
				$rand = rand(1, 100);
				$rand = $rand+$badgescount;
				 $msg = array
				(
					//'message' 	=> 'Category- '.$categoryName .', Building- '.$buildingName,
					//'title'		=> 'Work order '. $wo_number. ' has been created',
					//'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
					'vibrate'	=> 1,
					'sound'		=> 1,
					'largeIcon'	=> 'small_icon',
					'smallIcon'	=> 'small_icon',
					'badge' => $badgescount,
					"notId" => $rand
				);
				$fields = array
				(
					'registration_ids' 	=>array( $registrationIds),
					'data'			=> $msg
				);
					 
				$headers = array
				(
					'Authorization: key=' . API_ACCESS_KEY,
					'Content-Type: application/json'
				);
				$ch = curl_init();
				curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
				curl_setopt( $ch,CURLOPT_POST, true );
				curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
				curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
				$result = curl_exec($ch );
				curl_close( $ch );
			} else if($device == 2)  {
				$tToken[0] = $pushid;
				$tHost = 'ssl://gateway.push.apple.com';
				$tPort = 2195;
				$tCert = BASE_PATH.'services_dev/include/ck2.pem';
				//$tCert = 'ck2.pem';
				$tPassphrase = '1choc3747*1'; 
				
				$tBadge = intval($badgescount);
				$tSound = 'default';
				$tPayload = 'APNS Message Handled by LiveCode';
				$tBody = array();
				$tBody['aps'] = array ('badge' => $tBadge);
				$tBody ['payload'] = $tPayload;
				$tBody = json_encode($tBody);
				$tContext = stream_context_create();
				stream_context_set_option($tContext, 'ssl', 'local_cert', $tCert);
				stream_context_set_option($tContext, 'ssl', 'passphrase', $tPassphrase);
				$tSocket = stream_socket_client($tHost.':'.$tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $tContext);
				if (!$tSocket)
					exit();
					//exit ("APNS Connection Failed: $error $errstr" . PHP_EOL);
				
					$tMsg = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '',$tToken[0])) . pack('n', strlen ($tBody)) . $tBody;
					$tResult = fwrite($tSocket, $tMsg, strlen($tMsg)); 
				
				//if ($tResult)
					//echo 'Delivered Message to APNS' . PHP_EOL;
				//else
					//echo 'Could not Deliver Message to APNS' . PHP_EOL;
// Close the Connection to the Server.
				fclose($tSocket); 
			
			} 
			
	}
	
	function sendOnlyBadge($buildings_list,$status)
	{
		
		 $query = 'SELECT DISTINCT u.uid FROM `users` as u, user_building_module_access as ub WHERE ub.user_id = u.uid AND ub.building_id = "'.$buildings_list.'"';
		 $query_ins = mysqli_query($this->db->connect(), $query);
		 $userList = array();
		 $androidPushIdList = array();
		 $iosPushIdList = array();
		 while($data = mysqli_fetch_assoc($query_ins)) {
			$res = $this->getWorkorderbystatus($data['uid'], $status);
			$badges[$data['uid']] = $res['count_workorder'];
			$userList[] = $data['uid'];
		 }
		 if (!empty($userList)) {
			 $userImp = implode(",",$userList);
			 $queryPush ="SELECT push_id, badges, user_id FROM push_tokens WHERE device = 1 AND status =1 AND user_id IN(".$userImp.")";	
			 $queryPushSql =  mysqli_query($this->db->connect(), $queryPush);
			 
			 while($queryPushData = mysqli_fetch_assoc($queryPushSql)) {
				$badgescount = $badges[$queryPushData['user_id']];		 
				$registrationIds = $queryPushData['push_id'];
				$rand = rand(1, 100);
				$rand = $rand+$badgescount;
				 $msg = array
				(
					//'message' 	=> 'Category- '.$categoryName .', Building- '.$buildingName,
					//'title'		=> 'Work order '. $wo_number. ' has been created',
					//'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
					'vibrate'	=> 1,
					'sound'		=> 1,
					'largeIcon'	=> 'small_icon',
					'smallIcon'	=> 'small_icon',
					'badge' => $badgescount-1,
					"notId" => $rand
				);
				$fields = array
				(
					'registration_ids' 	=>array( $registrationIds),
					'data'			=> $msg
				);
				 
				$headers = array
				(
					'Authorization: key=' . API_ACCESS_KEY,
					'Content-Type: application/json'
				);
				$ch = curl_init();
				curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
				curl_setopt( $ch,CURLOPT_POST, true );
				curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
				curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
				$result = curl_exec($ch );
				curl_close( $ch );
			
			
			
			}
			
			$queryPush = "SELECT push_id, badges, user_id FROM push_tokens WHERE device = 2 AND status =1 AND user_id IN(".$userImp.")";
			$queryIosPushSql =  mysqli_query($this->db->connect(), $queryPush);
			while($queryIosPushData = mysqli_fetch_assoc($queryIosPushSql)) { 
				$badgescount = $badges[$queryIosPushData['user_id']];
				$iosPushIdList = $queryIosPushData['push_id'];
				$tToken[0] = $iosPushIdList;
				$tHost = 'ssl://gateway.push.apple.com';
				$tPort = 2195;
				$tCert = BASE_PATH.'services_dev/include/ck2.pem';
				//$tCert = 'ck2.pem';
				$tPassphrase = '1choc3747*1'; 
				
				$tBadge = intval($badgescount-1); 
				$tSound = 'default';
				$tPayload = 'APNS Message Handled by LiveCode';
				$tBody = array();
				$tBody['aps'] = array('badge' => $tBadge);
				$tBody ['payload'] = $tPayload;
				$tBody = json_encode($tBody);
				$tContext = stream_context_create();
				stream_context_set_option($tContext, 'ssl', 'local_cert', $tCert);
				stream_context_set_option($tContext, 'ssl', 'passphrase', $tPassphrase);
				$tSocket = stream_socket_client($tHost.':'.$tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $tContext);
				if (!$tSocket)
					exit();
					//exit ("APNS Connection Failed: $error $errstr" . PHP_EOL);
				
					$tMsg = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '',$tToken[0])) . pack('n', strlen ($tBody)) . $tBody;
					$tResult = fwrite($tSocket, $tMsg, strlen($tMsg)); 
				
				//if ($tResult)
					//echo 'Delivered Message to APNS' . PHP_EOL;
				//else
					//echo 'Could not Deliver Message to APNS' . PHP_EOL;
// Close the Connection to the Server.
				fclose($tSocket); 
			}
			
			
			
	   } 
	}
	
   function removeBadge($pushid, $device) { 
   
		 if($device == 1) {
			 $msg = array
						(
							//'message' 	=> 'Category- '.$categoryName .', Building- '.$buildingName,
							//'title'		=> 'Work order '. $wo_number. ' has been created',
							//'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
							'vibrate'	=> 1,
							'sound'		=> 1,
							'largeIcon'	=> 'small_icon',
							'smallIcon'	=> 'small_icon',
							'badge' => 0,
							
						);
						$fields = array
						(
							'registration_ids' 	=>array( $pushid),
							'data'			=> $msg
						);
						 
						$headers = array
						(
							'Authorization: key=' . API_ACCESS_KEY,
							'Content-Type: application/json'
						);
						$ch = curl_init();
						curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
						curl_setopt( $ch,CURLOPT_POST, true );
						curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
						curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
						curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
						curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
						$result = curl_exec($ch);
						curl_close( $ch );
		} else if($device == 2) { 
			
				$tToken[0] = $pushid;
				$tHost = 'ssl://gateway.push.apple.com';
				$tPort = 2195;
				$tCert = BASE_PATH.'services_dev/include/ck2.pem';
				//$tCert = 'ck2.pem';
				$tPassphrase = '1choc3747*1'; 
				
				$tBadge = intval(0); 
				$tSound = 'default';
				$tPayload = 'APNS Message Handled by LiveCode';
				$tBody = array();
				$tBody['aps'] = array ('badge' => $tBadge);
				$tBody ['payload'] = $tPayload;
				$tBody = json_encode($tBody);
				$tContext = stream_context_create();
				stream_context_set_option($tContext, 'ssl', 'local_cert', $tCert);
				stream_context_set_option($tContext, 'ssl', 'passphrase', $tPassphrase);
				$tSocket = stream_socket_client($tHost.':'.$tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $tContext);
				if (!$tSocket)
					exit();
					//exit ("APNS Connection Failed: $error $errstr" . PHP_EOL);
				
					$tMsg = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '',$tToken[0])) . pack('n', strlen($tBody)) . $tBody;
					$tResult = fwrite($tSocket, $tMsg, strlen($tMsg)); 
				
				//if ($tResult)
					//echo 'Delivered Message to APNS' . PHP_EOL;
				//else
					//echo 'Could not Deliver Message to APNS' . PHP_EOL;
// Close the Connection to the Server.
				fclose($tSocket); 
		
		} 
				
		}
	
	
	public function getWorkorderbystatus($user_id,$status) { 
		if($user_id != '') {
			$buildingIds = array();
			$select =  "SELECT building_id FROM user_building_module_access where user_id = ".$user_id;
		    $buildingrow = mysqli_query($this->db->connect(), $select);
			
			while($building = mysqli_fetch_assoc($buildingrow)) {  
				$buildingIds[] = $building['building_id'];
			} 
			
			 $select = "SELECT COUNT(*) as count_workorder FROM work_order wo LEFT JOIN work_order_update wop ON  wop.wo_id = wo.woId AND wop.current_update=1 WHERE wo.building in (".implode(",", $buildingIds).") AND wop.wo_status in ('".implode(",",$status)."')";
			$workcountow = mysqli_query($this->db->connect(), $select);
			$countworkorder = mysqli_fetch_assoc($workcountow);
			return ($countworkorder && sizeof($countworkorder)>0)? $countworkorder : false ;
		}
	}
	public function sendPushNotification($pushInfo) { 
		 $buildings_list = $pushInfo['buildings_list'];
		 $category_list = $pushInfo['category_list'];
		 $wo_number = $pushInfo['wo_number'];
		 $tenantName = $pushInfo['tenantName'];
		 $work_order_request = $pushInfo['work_order_request'];
		 
		 $query = "SELECT buildingName FROM buildings WHERE build_id ='" .$buildings_list. "'";
		 $sqlBuilding = mysqli_query($this->db->connect(), $query);
		 $buildingName = mysqli_fetch_assoc($sqlBuilding);
		 $buildingName = $buildingName['buildingName'];
		 
		 $query = "SELECT categoryName FROM category WHERE cat_id = '" . $category_list . "'";
		 $selCategory = mysqli_query($this->db->connect(), $query);
		 $categoryName = mysqli_fetch_assoc($selCategory);
		 $categoryName = $categoryName['categoryName'];
		 
		 $status = array('1');
		 $query = 'SELECT DISTINCT u.uid FROM `users` as u, user_building_module_access as ub WHERE ub.user_id = u.uid AND ub.building_id = "'.$buildings_list.'"';
		 $query_ins = mysqli_query($this->db->connect(), $query);
		 $userList = array();
		 $androidPushIdList = array();
		 $iosPushIdList = array();
		 while($data = mysqli_fetch_assoc($query_ins)) {
			$res = $this->getWorkorderbystatus($data['uid'], $status);
			$badges[$data['uid']] = $res['count_workorder'];
			$userList[] = $data['uid'];
		 }
		 if (!empty($userList)) {
			 $userImp = implode(",",$userList);
			 $queryPush ="SELECT push_id, badges, user_id FROM push_tokens WHERE device = 1 AND status =1 AND user_id IN(".$userImp.")";	
			 $queryPushSql =  mysqli_query($this->db->connect(), $queryPush);
			 
			 while($queryPushData = mysqli_fetch_assoc($queryPushSql)) {
				$badgescount = $badges[$queryPushData['user_id']];
				$msg1 = $buildingName . " - " . $tenantName;
				$msg2 = $wo_number . " - " . $categoryName;
				$msg3 = $work_order_request;
				if(strlen($msg1) >=48) {
					$msg1 = $buildingName . " - " . $tenantName;
					$msg1 = substr($msg1,0,45);
					$msg1 = $msg1.'...';
				}
				if(strlen($msg2) >=48) {
					$msg2 = $wo_number . " - " . $categoryName;
					$msg2 = substr($msg2,0,45);
					$msg2 = $msg2.'...';
				}
				if(strlen($msg3) >=48) {
					$msg3 = $work_order_request;
					$msg3 = substr($msg3,0,45);
					$msg3 = $msg3.'...';
				}
							 
				$registrationIds = $queryPushData['push_id']; 
				$rand = rand(1, 100);
				$rand = $rand+$badgescount;
				
				 $msg = array
				(
					//'message' 	=> 'Category- '.$categoryName .', Building- '.$buildingName,
					//'title'		=> 'Work order '. $wo_number. ' has been created',
					'message'		=> $msg1 . "\n" . $msg2 . "\n" . $msg3,
					'title'		=> 'Vision Work Order',
					//'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
					'vibrate'	=> 1,
					'sound'		=> 1,
					'largeIcon'	=> 'small_icon',
					'smallIcon'	=> 'small_icon',
					'badge' => $badgescount,
					"notId" => $rand
				);
				$fields = array
				(
					'registration_ids' 	=>array( $registrationIds),
					'data'			=> $msg
				);
				 
				$headers = array
				(
					'Authorization: key=' . API_ACCESS_KEY,
					'Content-Type: application/json'
				);
				$ch = curl_init();
				curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
				curl_setopt( $ch,CURLOPT_POST, true );
				curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
				curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
				$result = curl_exec($ch );
				curl_close( $ch );
			
			
			
			}

			$queryPush = "SELECT push_id, badges, user_id FROM push_tokens WHERE device = 2 AND status =1 AND user_id IN(".$userImp.")";

			$queryIosPushSql =  mysqli_query($this->db->connect(), $queryPush);
			while($queryIosPushData = mysqli_fetch_assoc($queryIosPushSql)) { 
				$badgescount = $badges[$queryIosPushData['user_id']];
				$msg1 = $buildingName . " - " . $tenantName;
				$msg2 = $wo_number . " - " . $categoryName;
				$msg3 = $work_order_request;
				if(strlen($msg1) >=40) {
					$msg1 = $buildingName . " - " . $tenantName;
					$msg1 = substr($msg1,0,37);
					$msg1 = $msg1.'...';
				}
				if(strlen($msg2) >=40) {
					$msg2 = $wo_number . " - " . $categoryName;
					$msg2 = substr($msg2,0,37);
					$msg2 = $msg2.'...';
				}
				if(strlen($msg3) >=40) {
					$msg3 = $work_order_request;
					$msg3 = substr($msg3,0,37);
					$msg3 = $msg3.'...';
				}
				$iosPushIdList = $queryIosPushData['push_id'];
				
			//phpinfo();
				$tToken[0] = $iosPushIdList;
				$tHost = 'ssl://gateway.push.apple.com';
				$tPort = 2195;
				$tCert = BASE_PATH.'services_dev/include/ck2.pem';
				//$tCert = 'ck2.pem';
				$tPassphrase = '1choc3747*1'; 
				//$tPassphrase = 'Vision@ve'; 
				$tAlert = $msg1 . "\n" . $msg2 . "\n" . $msg3; 
				$tBadge = intval($badgescount); 
				$tSound = 'default';
				$tPayload = 'APNS Message Handled by LiveCode';
				$tBody = array();
				$tBody['aps'] = array('alert' => $tAlert, 'badge' => $tBadge,'sound' => $tSound);
				$tBody ['payload'] = $tPayload;
				$tBody = json_encode($tBody);

				
				$tContext = stream_context_create();
				stream_context_set_option($tContext, 'ssl', 'local_cert', $tCert);
				stream_context_set_option($tContext, 'ssl', 'passphrase', $tPassphrase);
				//$tSocket = stream_socket_client($tHost.':'.$tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $tContext);
				if (!$tSocket)
					exit();
					//exit ("APNS Connection Failed: $error $errstr" . PHP_EOL);
				
					$tMsg = chr (0) . chr (0) . chr (32) . pack ('H*', str_replace(' ', '',$tToken[0])) . pack ('n', strlen ($tBody)) . $tBody;
					$tResult = fwrite ($tSocket, $tMsg, strlen ($tMsg)); 
				
				//if ($tResult)
					//echo 'Delivered Message to APNS' . PHP_EOL;
				//else
					//echo 'Could not Deliver Message to APNS' . PHP_EOL;
// Close the Connection to the Server.
				fclose ($tSocket); 
			}
			
		} 
	}

}

class Helper {

    function __Construct() {
		
		require_once 'DBConnect.php';
        $this->db = new DBConnect();
        $this->db->connect();
	}

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
        $building_sql = mysqli_query($this->db->connect(), $building_query);
        $building = mysqli_fetch_array($building_sql);
        return $building;
    }

    function getTimeZone($time_zone_id) {
        $time_zone_query = "select * from time_zone where status = '1' AND id = '" . $time_zone_id . "' ";
        $time_zone_sql = mysqli_query($this->db->connect(), $time_zone_query);
        $time_zone = mysqli_fetch_array($time_zone_sql);
        return $time_zone;
    }
}

?>