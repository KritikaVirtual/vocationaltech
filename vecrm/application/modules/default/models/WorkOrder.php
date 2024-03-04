<?php
/**
 * Description of Role
 *
 * @author brijesh
 */
class Model_WorkOrder extends Zend_Db_Table_Abstract {

   protected $_name = 'work_order';   
   protected $_tab_role = 'work_order';  
   public $_errorMessage='';
   
   /* Get all users/staff detail */
    public function getWorkOrder($woId = "") {
        $select = $this->select()->where('status=?','1') ;
        
        if(!empty($woId)){
            $select = $select->where( 'woId = ? ', $woId );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
    public function getWorkOrderInfo($woId){
		
		if(!empty($woId)){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
                      ->from(array('wo' => 'work_order'))
                      ->joinInner(array('t' => 'tenant'),'t.id = wo.tenant',array('tenantName','tenantContact'))
                      ->joinLeft(array('bu'=>'buildings'),'bu.build_id = wo.building',array('buildingName'))                      
                      ->joinLeft(array('cat'=>'category'),'cat.cat_id = wo.category',array('categoryName','send_email'))
                      ->joinLeft(array('pt'=>'priority'),'pt.pid = cat.prioritySchedule',array('priorityName','pid'))                                           
                      ->joinLeft(array('u'=>'users'),'wo.create_user = u.uid',array('firstName','lastName','email','phoneNumber'))                      
                      ->joinLeft(array('tu'=>'tenantUsers'),'wo.create_user = tu.userId',array('tenant_suite'=>'tu.suite_location'))
                      ->where('wo.woId=?',$woId);
           $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
		}else
		 return false;            
	}
    
    public function insertWorkOrder($data) {				
        try{            
            $this->_errorMessage="";    	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
     public function updateWorkOrder($data,$woId) {
		 $this->_errorMessage=""; 			
        try{ 
			if(isset($woId) && !empty($woId)){ 
			 $where = $this->getAdapter()->quoteInto('woId = ?', $woId);
			 $this->update($data,$where);				   	
			 return true;
		   }else{
		     return false;		
		   }
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
       
    public function getTenantWorkOrder($tenantId,$order,$dir,$userId=''){
		if($tenantId){
			//$select = $this->select()->where('status=?','1') ;
			$orderBy = $order.' '.$dir;
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
                      ->from(array('wo' => 'work_order'))
                      ->joinInner(array('t' => 'tenant'),'t.id = wo.tenant',array('tenantName','tenantContact'))
                      ->joinLeft(array('bu'=>'buildings'),'bu.build_id = wo.building',array('buildingName'))
                      ->joinLeft(array('cat'=>'category'),'cat.cat_id = wo.category',array('categoryName'))
                      ->joinLeft(array('wop'=>'work_order_update'),'wop.wo_id = wo.woId AND wop.current_update=1',array('wop.wo_status','wop.internal_note'))                                                          
                      ->joinLeft(array('u'=>'users'),'wo.create_user = u.uid',array('firstName','lastName','email'))                      
                      ->where('wo.tenant=?',$tenantId);
            if($userId!=''){
				$select = $select->where('wo.create_user=?',$userId);
			}          
             $select = $select->order(array($orderBy));                      
            $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
		}else
		 return false;
	}
	
	public function getBuildingWorkOrder($buildID,$order,$dir,$search_array=array()){
		if($buildID){
			//$select = $this->select()->where('status=?','1') ;
			$orderBy = $order.' '.$dir;
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
                      ->from(array('wo' => 'work_order'))
                      ->joinInner(array('t' => 'tenant'),'t.id = wo.tenant',array('tenantName','tenantContact'))
                      ->joinLeft(array('bu'=>'buildings'),'bu.build_id = wo.building',array('buildingName'))
                      ->joinLeft(array('cat'=>'category'),'cat.cat_id = wo.category',array('categoryName','prioritySchedule'))                      
                      ->joinLeft(array('wop'=>'work_order_update'),'wop.wo_id = wo.woId AND wop.current_update=1',array('wop.wo_status', 'wop.internal_note', 'wop.wo_request','created_date'=>'wop.created_at','updated_date'=>'wop.updated_at'))                     
                      ->joinLeft(array('u'=>'users'),'wo.create_user = u.uid',array('firstName','lastName','email'))                      
                      ->where('wo.building=?',$buildID);
                      
               if(isset($search_array['search_status']) && $search_array['search_status']!='')
               {
				  $select = $select->where('wop.wo_status=?',$search_array['search_status']);
			   } 			   
			   
			   if(isset($search_array['category_name']) && $search_array['category_name']!='')
               {
				  $select = $select->where("cat.categoryName LIKE '%".$search_array['category_name']."%'");
			   }
			   
			   if(isset($search_array['tenant_name']) && $search_array['tenant_name']!='')
               {
				  $select = $select->where("t.tenantName LIKE '%".$search_array['tenant_name']."%'");
			   }
			   
			   if(isset($search_array['search_wo']) && $search_array['search_wo']!='')
               {
				  $select = $select->where('wo.wo_number=?',$search_array['search_wo']);
			   }
			   
			   if(isset($search_array['from_date']) && $search_array['to_date']!='')
               {
				  $select = $select->where("DATE(wo.created_at) BETWEEN '".$search_array['from_date'] ."' AND '".$search_array['to_date']."'");
			   }                          
             $select = $select->order(array($orderBy));                      
            $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
		}else
		 return false;
	}
	
	public function getWorkOrderByBuilIds($buildingIds,$order,$dir,$search_array=array()){
		if(!empty($buildingIds)){
			//$select = $this->select()->where('status=?','1') ;
			//print_r($search_array);
			//exit;
			$orderBy = $order.' '.$dir;
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
                      ->from(array('wo' => 'work_order'))
                      ->joinInner(array('t' => 'tenant'),'t.id = wo.tenant',array('tenantName','tenantContact'))
                      ->joinLeft(array('bu'=>'buildings'),'bu.build_id = wo.building',array('buildingName'))
                      ->joinLeft(array('cat'=>'category'),'cat.cat_id = wo.category',array('categoryName','prioritySchedule'))                      
                      ->joinLeft(array('wop'=>'work_order_update'),'wop.wo_id = wo.woId AND wop.current_update = 1',array('wop.wo_status','wop.internal_note','wop.wo_request','created_date'=>'wop.created_at','updated_date'=>'wop.updated_at'))
                      ->joinLeft(array('u'=>'users'),'wo.create_user = u.uid',array('firstName','lastName','email'))                      
                      ->where('wo.building in ('.implode(",", $buildingIds).')');
              if(isset($search_array['search_status']) && $search_array['search_status']!='')
               {
				  $select = $select->where('wop.wo_status=?',$search_array['search_status']);
			   }
			   if(isset($search_array['category_name']) && $search_array['category_name']!='')
               {
				  $select = $select->where("cat.categoryName LIKE '%".$search_array['category_name']."%'");
			   }
			   
			   if(isset($search_array['tenant_name']) && $search_array['tenant_name']!='')
               {
				  $select = $select->where("t.tenantName LIKE '%".$search_array['tenant_name']."%'");
			   }
			   
			   if(isset($search_array['search_wo']) && $search_array['search_wo']!='')
               {
				  $select = $select->where('wo.wo_number=?',$search_array['search_wo']);
			   }
			   if(isset($search_array['from_date']) && $search_array['to_date']!='')
               {
				  $select = $select->where("DATE(wo.created_at) BETWEEN '".$search_array['from_date'] ."' AND '".$search_array['to_date']."'");
			   }                             
             $select = $select->order(array($orderBy));
                                  
            $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
		}else
		 return false;
	}  
	
	/*********work order by category ids **********/
	public function getWorkOrderByCatIds($catIds){
		if(!empty($catIds)){
			//$select = $this->select()->where('status=?','1') ;
			//$orderBy = $order.' '.$dir;
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
                      ->from(array('wo' => 'work_order'))
                      ->joinInner(array('t' => 'tenant'),'t.id = wo.tenant',array('tenantName','tenantContact'))
                      ->joinLeft(array('bu'=>'buildings'),'bu.build_id = wo.building',array('buildingName'))
                      ->joinLeft(array('cat'=>'category'),'cat.cat_id = wo.category',array('categoryName'))
                      ->joinLeft(array('wop'=>'work_order_update'),'wop.wo_id = wo.woId AND wop.current_update=1',array('wop.wo_status','wop.wo_request'))
                      ->joinLeft(array('u'=>'users'),'wo.create_user = u.uid',array('firstName','lastName','email'))                      
                      ->where('wo.category in ('.implode(",", $catIds).')');                     
             //$select = $select->order(array($orderBy));
             //echo $select->__toString();
             //exit;                      
            $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
		}else
		 return false;
	} 
    
    /*********get work order id by wo number and building id *********/    
    
    public function getWoIdByNoNBId($wo_number, $bId){
		if(!empty($wo_number) && !empty($bId)){
			$select = $this->select()->from(array('work_order'),array('woId'));
			$select = $select->where( 'wo_number = ? ', $wo_number );
			$select = $select->where( 'building = ? ', $bId );
			$res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
		}else
		 return false;
	}
   /*delete work order */
	public function deleteWorkOrder($woId){   		
       	try{
    		$this->delete('woId = '.$woId);
    		return true;
    	}catch(Exception $e){
    		return false;
    	}
	}
	
	/**
	 * send email function
	 */
		 
	 public function sendWorkOrderEmail($woId,$tenantData){
		 /********* get tenant users *******/
		 $tenantId = $tenantData['tenantId'];
		 $buildId = $tenantData['buildingId'];
		 /******** get comapny info *******/
		 $accountModel = new Model_Account();
		 $accoundDetail = $accountModel->getCompanyByBuilding($buildId);
		 $accounData = (array)$accoundDetail[0];
		 /******* get work order info **********/
		 $companyName = $accounData['companyName'];
		 $woDetail = $this->getWorkOrderInfo($woId);
		 $woData = (array)$woDetail[0];
		 $tuserMapper = new Model_TenantUser();
		 $tuserList = $tuserMapper->getTenantUsers($tenantId);
		 
		 $wssModel = new Model_WoScheduleStatus();
		 $wssDetail = $wssModel->getCurrentWs($woId);
		 $wssData = $wssDetail[0];
		 $sendEmail = array();
		 //var_dump($tenantData);		 
		 foreach($tuserList as $tuser){
			 if($tuser->role_id==5){
				 //echo $tuser->email;
				 $sendEmail[] = $tuser->email;				 				
				 $send_as = $tuser->send_as;
				$htmlContent = $this->getHtmlContent($send_as,$woData,$tenantData,$accounData);	
				//print_r($htmlContent);
				$acknowledge = '';
				$htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content'] );
				$this->sendNotificationMail($woData['create_user'],$tuser->uid,$tuser->email,$htmlContent['subject'],$htmlContent['content']);			 
			 }else{				 
				 if($tuser->cc_enable=='1' || $tuser->uid == $woData['create_user']){
					 //echo $tuser->email;
					$sendEmail[] = $tuser->email;
				    $send_as = $tuser->send_as;
				    $htmlContent = $this->getHtmlContent($send_as,$woData,$tenantData,$accounData);
				    //print_r($htmlContent);
				    $acknowledge = '';
				   $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content'] );
				    $this->sendNotificationMail($woData['create_user'],$tuser->uid,$tuser->email,$htmlContent['subject'],$htmlContent['content']);
			     }
			 }
		 }
		 
		/**********get user from category code *********/
		$categoryId = $woData['category'];
		$catModel = new Model_Category();
		$catDetail = $catModel->getAllCategory($categoryId);
		//var_dump($catDetail);
		$catData = $catDetail[0];
		$accountUser = $catData['account_user'];
		$distGroup = $catData['send_email'];
		if($accountUser!=''){
			$userModel = new Model_User();
			$acuserList = $userModel->getUserBySetIds($accountUser);
			//var_dump($acuserList);
			foreach($acuserList as $acuser){
				if(!in_array($acuser['email'],$sendEmail)){
					//echo $acuser['email'];
					$sendEmail[] = $acuser['email'];
				   $htmlContent = $this->getHtmlContent(1,$woData,$tenantData,$accounData);
				   //print_r($htmlContent);
				   $activateURL = BASEURL.'/workstatus/change/woId/'.$woData['woId'].'/sId/'.$wssData['schedule_id'].'/ckey/'.$wssData['ckey'];
				   $acknowledge = '<a href="'.$activateURL.'" target="_blank">Click here to acknowledge this work order</a>';
				   $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content'] );
				   //print_r($htmlContent);
				   $this->sendNotificationMail($woData['create_user'],$acuser['uid'],$acuser['email'],$htmlContent['subject'],$htmlContent['content']);
				}
			}		
		}
		if($distGroup!=''){
			$disGpArray = explode(",",$distGroup);
			foreach($disGpArray as $distGP){
					$eguModel = new Model_EmailGroupUsers();
					$guserList = $eguModel->getGroupUsers($distGP);
					//var_dump($guserList);
					foreach($guserList as $gpuser){
						if(!in_array($gpuser->email,$sendEmail) && $this->getDayAvailable($gpuser->days_of_week)){
							//echo $gpuser->email;
						   $sendEmail[] = $gpuser->email;
						   $htmlContent = $this->getHtmlContent($gpuser->sid,$woData,$tenantData,$accounData);
						   //print_r($htmlContent);
						   $activateURL = BASEURL.'/workstatus/change/woId/'.$woData['woId'].'/sId/'.$wssData['schedule_id'].'/ckey/'.$wssData['ckey'];
				           $acknowledge = '<a href="'.$activateURL.'" target="_blank">Click here to acknowledge this work order</a>';
				           $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content'] );
				           
						   $this->sendNotificationMail($woData['create_user'],$gpuser->uid,$gpuser->email,$htmlContent['subject'],$htmlContent['content']);
						}
				   }
			  }
		  }		
	  }
	  
	  /****** resend work order email ******/
	  public function resendWorkOrderEmail($woId,$tenantData){
		 /********* get tenant users *******/
		 $tenantId = $tenantData['tenantId'];
		 $buildId = $tenantData['buildingId'];
		 /******** get comapny info *******/
		 $accountModel = new Model_Account();
		 $accoundDetail = $accountModel->getCompanyByBuilding($buildId);
		 $accounData = (array)$accoundDetail[0];
		 /******* get work order info **********/
		 $companyName = $accounData['companyName'];
		 $woDetail = $this->getWorkOrderInfo($woId);
		 $woData = (array)$woDetail[0];
		 $tuserMapper = new Model_TenantUser();
		 $tuserList = $tuserMapper->getTenantUsers($tenantId);
		 
		 $wssModel = new Model_WoScheduleStatus();
		 $wssDetail = $wssModel->getCurrentWs($woId);
		 $wssData = $wssDetail[0];
		 $sendEmail = array();		
		 
		/**********get user from category code *********/
		$categoryId = $woData['category'];
		$catModel = new Model_Category();
		$catDetail = $catModel->getAllCategory($categoryId);
		//var_dump($catDetail);
		$catData = $catDetail[0];
		$accountUser = $catData['account_user'];
		$distGroup = $catData['send_email'];
		if($accountUser!=''){
			$userModel = new Model_User();
			$acuserList = $userModel->getUserBySetIds($accountUser);
			//var_dump($acuserList);
			foreach($acuserList as $acuser){
				if(!in_array($acuser['email'],$sendEmail)){
					//echo $acuser['email'];
					$sendEmail[] = $acuser['email'];
				   $htmlContent = $this->getHtmlContent(1,$woData,$tenantData,$accounData);
				   //print_r($htmlContent);
				   $activateURL = BASEURL.'/workstatus/change/woId/'.$woData['woId'].'/sId/'.$wssData['schedule_id'].'/ckey/'.$wssData['ckey'];
				   $acknowledge = '<a href="'.$activateURL.'" target="_blank">Click here to acknowledge this work order</a>';
				   $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content'] );
				   //print_r($htmlContent);
				   $this->sendNotificationMail($woData['create_user'],$acuser['uid'],$acuser['email'],$htmlContent['subject'],$htmlContent['content']);
				}
			}		
		}
		if($distGroup!=''){
			$disGpArray = explode(",",$distGroup);
			foreach($disGpArray as $distGP){
					$eguModel = new Model_EmailGroupUsers();
					$guserList = $eguModel->getGroupUsers($distGP);
					//var_dump($guserList);
					foreach($guserList as $gpuser){
						if(!in_array($gpuser->email,$sendEmail) && $this->getDayAvailable($gpuser->days_of_week)){
							//echo $gpuser->email;
						   $sendEmail[] = $gpuser->email;
						   $htmlContent = $this->getHtmlContent(1,$woData,$tenantData,$accounData);
						   //print_r($htmlContent);
						   $activateURL = BASEURL.'/workstatus/change/woId/'.$woData['woId'].'/sId/'.$wssData['schedule_id'].'/ckey/'.$wssData['ckey'];
				           $acknowledge = '<a href="'.$activateURL.'" target="_blank">Click here to acknowledge this work order</a>';
				           $htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content'] );
				           
						   $this->sendNotificationMail($woData['create_user'],$gpuser->uid,$gpuser->email,$htmlContent['subject'],$htmlContent['content']);
						}
				   }
			  }
		  }		
	  }
	 /******get html content email***/ 
	 public function getHtmlDoc($woData,$tenantData,$accounData){		 
		$emailMapper = new Model_Email();
		$htmlDocId = 6; // email template id
		$loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
		if($loadTemplate){
			
			$emailContent = $loadTemplate[0];
            $emailSubject = $emailContent['email_subject'];
            $emailBody = $emailContent['email_content'];
            $timeZone = $this->getBuildingTimeZone($woData['building']);
            $requestDate = date('m/d/Y',strtotime($woData['date_requested'])).' '.$woData['time_requested'].' '.$timeZone;
            $emailBody = str_replace('[[++requestDate]]', $requestDate, $emailBody );
			$emailBody = str_replace('[[++companyName]]', $accounData['companyName'], $emailBody);
			$emailBody = str_replace('[[++address1]]', $accounData['address'], $emailBody);
			$emailBody = str_replace('[[++address2]]', $accounData['address2'], $emailBody);
			$emailBody = str_replace('[[++city]]', $accounData['city'], $emailBody);
			$emailBody = str_replace('[[++state]]', $accounData['state'], $emailBody);
			$emailBody = str_replace('[[++postalCode]]', $accounData['postalCode'], $emailBody);
			$emailBody = str_replace('[[++phone]]', $accounData['phoneNumber'], $emailBody);
			if($accounData['phoneExt']!='')
			 $emailBody = str_replace('[[++phoneExt]]', '( '.$accounData['phoneExt'].' )', $emailBody);
			 else
			 $emailBody = str_replace('[[++phoneExt]]', '', $emailBody);
			 
			$emailBody = str_replace('[[++tenantName]]', $tenantData['tenantName'], $emailBody);
			$emailBody = str_replace('[[++requestedBy]]', $woData['firstName'].' '.$woData['lastName'], $emailBody);
			$emailBody = str_replace('[[++phoneNumber]]', $tenantData['phoneNumber'], $emailBody);
			$emailBody = str_replace('[[++suite]]', $woData['tenant_suite'], $emailBody);
			$emailBody = str_replace('[[++email]]', $woData['email'], $emailBody);
			$emailBody = str_replace('[[++category]]', $woData['categoryName'], $emailBody);
			$emailBody = str_replace('[[++woDescription]]', $woData['work_order_request'], $emailBody);
			
			$emailSubject = str_replace('[[++tenantName]]', $tenantData['tenantName'], $emailSubject );
			$emailSubject = str_replace('[[++category]]', $woData['categoryName'], $emailSubject);
			$descText = strip_tags($woData['work_order_request']);
			$descText = str_replace("&nbsp;", ' ', $descText);
			$shortDescription = substr($descText,0,40).'...';
			$emailSubject = str_replace('[[++shortDescription]]', $shortDescription, $emailSubject);
			
			$htmlContent = array('subject'=>$emailSubject,'content'=>$emailBody);
			return $htmlContent;
		}
		
	 }
	 
	 /******get html basic e-mail content email***/ 
	 public function getHtmBasiclDoc($woData,$tenantData,$accounData){
		$emailMapper = new Model_Email();
		$htmlDocId = 8; // email template id
		$loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
		if($loadTemplate){
			$emailContent = $loadTemplate[0];
            $emailSubject = $emailContent['email_subject'];
            $emailBody = $emailContent['email_content'];
            
            $timeZone = $this->getBuildingTimeZone($woData['building']);
            $requestDate = date('m/d/Y',strtotime($woData['date_requested'])).' '.$woData['time_requested'].' '.$timeZone;
            $emailBody = str_replace('[[++requestDate]]', $requestDate, $emailBody );
			$emailBody = str_replace('[[++companyName]]', $accounData['companyName'], $emailBody);
			$emailBody = str_replace('[[++address1]]', $accounData['address'], $emailBody);
			$emailBody = str_replace('[[++address2]]', $accounData['address2'], $emailBody);
			$emailBody = str_replace('[[++city]]', $accounData['city'], $emailBody);
			$emailBody = str_replace('[[++state]]', $accounData['state'], $emailBody);
			$emailBody = str_replace('[[++postalCode]]', $accounData['postalCode'], $emailBody);
			$emailBody = str_replace('[[++phone]]', $accounData['phoneNumber'], $emailBody);
			if($accounData['phoneExt']!='')
			 $emailBody = str_replace('[[++phoneExt]]', '( '.$accounData['phoneExt'].' )', $emailBody);
			 else
			 $emailBody = str_replace('[[++phoneExt]]', '', $emailBody);
			 
			$emailBody = str_replace('[[++tenantName]]', $tenantData['tenantName'], $emailBody);
			$emailBody = str_replace('[[++requestedBy]]', $woData['firstName'].' '.$woData['lastName'], $emailBody);
			$emailBody = str_replace('[[++phoneNumber]]', $tenantData['phoneNumber'], $emailBody);
			$emailBody = str_replace('[[++suite]]', $woData['tenant_suite'], $emailBody);
			$emailBody = str_replace('[[++email]]', $woData['email'], $emailBody);
			$emailBody = str_replace('[[++category]]', $woData['categoryName'], $emailBody);
			$emailBody = str_replace('[[++woDescription]]', $woData['work_order_request'], $emailBody);
			
			$emailSubject = str_replace('[[++tenantName]]', $tenantData['tenantName'], $emailSubject );
			$emailSubject = str_replace('[[++category]]', $woData['categoryName'], $emailSubject);
			$descText = strip_tags($woData['work_order_request']);
			$descText = str_replace("&nbsp;", ' ', $descText);
			$shortDescription = substr($descText,0,40).'...';
			$emailSubject = str_replace('[[++shortDescription]]', $shortDescription, $emailSubject);
			
			$htmlContent = array('subject'=>$emailSubject,'content'=>$emailBody);
			return $htmlContent;
		}
		
	 }
	 
	 /******get html text e-mail content email***/ 
	 public function getHtmlTextDoc($woData,$tenantData,$accounData){
		$emailMapper = new Model_Email();
		$htmlDocId = 9; // email template id
		$loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
		if($loadTemplate){
			$emailContent = $loadTemplate[0];
            $emailSubject = $emailContent['email_subject'];
            $emailBody = $emailContent['email_content'];            
            
            $requestDate = date('m/d/Y',strtotime($woData['date_requested']));
            $emailBody = str_replace('[[++requestDate]]', $requestDate, $emailBody );
            $emailBody = str_replace('[[++requestTime]]', $woData['time_requested'], $emailBody );
			$emailBody = str_replace('[[++companyName]]', $accounData['companyName'], $emailBody);
			$emailBody = str_replace('[[++address1]]', $accounData['address'], $emailBody);
			$emailBody = str_replace('[[++address2]]', $accounData['address2'], $emailBody);
			$emailBody = str_replace('[[++city]]', $accounData['city'], $emailBody);
			$emailBody = str_replace('[[++state]]', $accounData['state'], $emailBody);
			$emailBody = str_replace('[[++postalCode]]', $accounData['postalCode'], $emailBody);
			$emailBody = str_replace('[[++phone]]', $accounData['phoneNumber'], $emailBody);
			if($accounData['phoneExt']!='')
			 $emailBody = str_replace('[[++phoneExt]]', '( '.$accounData['phoneExt'].' )', $emailBody);
			 else
			 $emailBody = str_replace('[[++phoneExt]]', '', $emailBody);
			
			$emailBody = str_replace('[[++tenantName]]', $tenantData['tenantName'], $emailBody);
			$emailBody = str_replace('[[++requestedBy]]', $woData['firstName'].' '.$woData['lastName'], $emailBody);
			$emailBody = str_replace('[[++phoneNumber]]', $tenantData['phoneNumber'], $emailBody);
			$emailBody = str_replace('[[++suite]]', $woData['tenant_suite'], $emailBody);
			$emailBody = str_replace('[[++email]]', $woData['email'], $emailBody);
			$emailBody = str_replace('[[++category]]', $woData['categoryName'], $emailBody);
			$emailBody = str_replace('[[++woDescription]]', $woData['work_order_request'], $emailBody);
			
			$emailSubject = str_replace('[[++tenantName]]', $tenantData['tenantName'], $emailSubject );
			$emailSubject = str_replace('[[++category]]', $woData['categoryName'], $emailSubject);
			$descText = strip_tags($woData['work_order_request']);
			$descText = str_replace("&nbsp;", ' ', $descText);
			$shortDescription = substr($descText,0,40).'...';
			$emailSubject = str_replace('[[++shortDescription]]', $shortDescription, $emailSubject);
			
			$htmlContent = array('subject'=>$emailSubject,'content'=>$emailBody);
			return $htmlContent;
		}
		
	 }
	 
	 /******get Text e-mail content email***/ 
	 public function getTextEmailDoc($woData,$tenantData,$accounData){
		$emailMapper = new Model_Email();
		$htmlDocId = 10; // email template id
		$loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
		if($loadTemplate){
			$emailContent = $loadTemplate[0];
            $emailSubject = $emailContent['email_subject'];
            $emailBody = $emailContent['email_content'];         
            
            $requestDate = date('m/d/Y',strtotime($woData['date_requested'])).'-'.$woData['time_requested'];
            $emailBody = str_replace('[[++date]]', $requestDate, $emailBody );
			/*$emailBody = str_replace('[[++companyName]]', $accounData['companyName'], $emailBody);
			$emailBody = str_replace('[[++address1]]', $accounData['address'], $emailBody);
			$emailBody = str_replace('[[++address1]]', $accounData['address2'], $emailBody);
			$emailBody = str_replace('[[++city]]', $accounData['city'], $emailBody);
			$emailBody = str_replace('[[++state]]', $accounData['state'], $emailBody);
			$emailBody = str_replace('[[++postalCode]]', $accounData['postalCode'], $emailBody);
			$emailBody = str_replace('[[++phone]]', $accounData['phoneNumber'], $emailBody);
			if($accounData['phoneExt']!='')
			 $emailBody = str_replace('[[++phoneExt]]', '( '.$accounData['phoneExt'].' )', $emailBody);
			 else
			 $emailBody = str_replace('[[++phoneExt]]', '', $emailBody);
			*/
			$emailBody = str_replace('[[++tenantName]]', $tenantData['tenantName'], $emailBody);
			$emailBody = str_replace('[[++requestedBy]]', $woData['firstName'].' '.$woData['lastName'], $emailBody);
			$emailBody = str_replace('[[++phoneNumber]]', $tenantData['phoneNumber'], $emailBody);
			$emailBody = str_replace('[[++suite]]', $woData['tenant_suite'], $emailBody);
			$emailBody = str_replace('[[++email]]', $woData['email'], $emailBody);
			$emailBody = str_replace('[[++category]]', $woData['categoryName'], $emailBody);
			$emailBody = str_replace('[[++description]]', $woData['work_order_request'], $emailBody);
			
			$emailSubject = str_replace('[[++tenantName]]', $tenantData['tenantName'], $emailSubject );
			$emailSubject = str_replace('[[++category]]', $woData['categoryName'], $emailSubject);
			$descText = strip_tags($woData['work_order_request']);
			$descText = str_replace("&nbsp;", ' ', $descText);
			$shortDescription = substr($descText,0,40).'...';
			$emailSubject = str_replace('[[++shortDescription]]', $shortDescription, $emailSubject);
			
			$htmlContent = array('subject'=>$emailSubject,'content'=>$emailBody);
			return $htmlContent;
		}
		
	 }
	 
	 
	 public function getHtmlContent($send_as,$woData,$tenantData,$accounData){
		 $htmlContent ='';
		  if($send_as=='1'){
				 $htmlContent = $this->getHtmlDoc($woData,$tenantData,$accounData);
			 }else if($send_as=='2'){
				 $htmlContent = $this->getHtmBasiclDoc($woData,$tenantData,$accounData);
			 }else if($send_as=='3'){
				 $htmlContent = $this->getHtmlTextDoc($woData,$tenantData,$accounData);
			 }else{
				 $htmlContent = $this->getTextEmailDoc($woData,$tenantData,$accounData);
			 }
		return $htmlContent;
	 }
	 
	 public function sendNotificationMail($suId,$tuId,$to,$subject,$ebody){	
		try{ 	
				$mail = new Zend_Mail('utf-8');	
				$mail->addTo($to);		   
				//$mail->addTo('brijeshkumar@virtualemployee.com');
				$mail->setSubject($subject);
				$mail->setFrom('info@virtualemployee.com','Vecrm');
				$mail->setBodyHtml($ebody);
				if($mail->send()){
					$this->saveEmailLog($suId,$tuId,$to,$subject,true);
					return true;
				}else{
					$this->saveEmailLog($suId,$tuId,$to,$subject,false);
					return false;				
				}
			}catch(Exception $e){
				$this->saveEmailLog($suId,$tuId,$to,$subject,false);
			}			
			
   }
	 public function saveEmailLog($suId,$tuId,$email,$message,$mail_status){
		   try{
		        $email_log = new Model_Log();
				$logData = array();
				$logData['email_sent_by'] = $suId;
			    $logData['userId'] = $tuId;
				$logData['log_type'] = 'email';
				$logData['email'] = $email;
				$logData['log_message'] = $message;

				if($mail_status){
				  	$logData['email_status'] = 1;
				  	$email_log->insertLog($logData);
				}
				else{
				  	$logData['email_status'] = 0;
				  	$email_log->insertLog($logData);
				}
			}catch(Exception $e){
				echo $e->getMessage();
			}	
	 }
	 
	 
	 public function sendReminderNotification($woId,$scheduleId){
		   $woDetail = $this->getWorkOrderInfo($woId);
		   $woData = (array)$woDetail[0];
		   
		   /*********** get schedule data ***********/
		    $schData = '';
		    $schModel = new Model_Schedule();
		    $schDetail = $schModel->getScheduleById($scheduleId);		    
		    if($schDetail)
		    $schData = $schDetail[0];
		   /**********get user from category code *********/
			$categoryId = $woData['category'];
			$catModel = new Model_Category();
			$catDetail = $catModel->getAllCategory($categoryId);
			$sendEmail = array();
			//var_dump($catDetail);
			$catData = $catDetail[0];
			$accountUser = $catData['account_user'];
			$distGroup = $catData['send_email'];
			if($accountUser!=''){
				$userModel = new Model_User();
				$acuserList = $userModel->getUserBySetIds($accountUser);
				//var_dump($acuserList);
				foreach($acuserList as $acuser){
					if(!in_array($acuser['email'],$sendEmail)){
						//echo $acuser['email'];
						$sendEmail[] = $acuser['email'];
					   $htmlContent = $this->getReminderContent($schData);
					   //print_r($htmlContent);
					   //$acknowledge = '<a href="">Click here to acknowledge this work order</a>';
					   //$htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content'] );
					   $this->sendNotificationMail($woData['create_user'],$acuser['uid'],$acuser['email'],$htmlContent['subject'],$htmlContent['content']);
					}
				}		
			}
			if($distGroup!=''){
				$disGpArray = explode(",",$distGroup);
				foreach($disGpArray as $distGP){
						$eguModel = new Model_EmailGroupUsers();
						$guserList = $eguModel->getGroupUsers($distGP);
						//var_dump($guserList);
						foreach($guserList as $gpuser){
							if(!in_array($gpuser->email,$sendEmail) && $this->getDayAvailable($gpuser->days_of_week)){
								//echo $gpuser->email;
							   $sendEmail[] = $gpuser->email;
							   $htmlContent = $this->getReminderContent($schData);
							   //print_r($htmlContent);
							   //$acknowledge = '<a href="">Click here to acknowledge this work order</a>';
							   //$htmlContent['content'] = str_replace('[[++acknowledge]]', $acknowledge, $htmlContent['content'] );
							   $this->sendNotificationMail($woData['create_user'],$gpuser->uid,$gpuser->email,$htmlContent['subject'],$htmlContent['content']);
							}
					   }
				  }
			  }
	 }
	 
	 public function getReminderContent($schData){		   
		    $emailMapper = new Model_Email();
			$htmlDocId = 12; // email template id
			$ssModel = new Model_ScheduleStatus();
			$status_list = $ssModel->getScheduleStatus();
			$status_array= array();
			foreach($status_list as $sl){
				$status_array[$sl['ssID']] = $sl['title'];
			}			
		     $loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
		     if($loadTemplate){
				$emailContent = $loadTemplate[0];
				$emailSubject = $emailContent['email_subject'];
				$emailBody = $emailContent['email_content'];
				/*********change content from template ********/
				$emailBody = str_replace('[[++startStatus]]', $status_array[$schData['start_status']], $emailBody);
				$emailBody = str_replace('[[++endStatus]]', $status_array[$schData['end_status']], $emailBody);
				
				$htmlContent = array('subject'=>$emailSubject,'content'=>$emailBody);
				return $htmlContent;
			 }
	  }
	  
	  /********Get Building Time Zone ********/
	  public function getBuildingTimeZone($bid){
		  $buildModel = new Model_Building();
		  $build_data = $buildModel->getbuildingbyid($bid);
			if($build_data){
				$btimezone = $build_data[0]['timezone'];
				 if($btimezone!=0){
					
					 $tModel = new Model_TimeZone();
					 $tzonelist = $tModel->getTimeZoneById($btimezone);					
					$time_zone = $tzonelist[0]['time_value'];
					 return $time_zone;
				 }else{
				 $timeZone = date_default_timezone_get();
				 return $timeZone;
			   }
             }else{
				 $timeZone = date_default_timezone_get();
				 return $timeZone;
			 }
	  }
	  
	  public function getDayAvailable($wd){
		  $day = date('l');
		  if($wd==1){
			  return true;
		  }else if($wd==2 && $day!='Sunday' && $day !='Saturday'){
			  return true;
		  }else if($wd==3 && ($day=='Sunday' || $day =='Saturday')){
			  return true;
		  }else if($wd==4 && ($day=='Monday' || $day =='Wednesday' || $day =='Friday')){
			  return true;
		  }else if($wd==5 && ($day=='Tuesday' || $day =='Thursday')){
			  return true;
		  }else if($wd==6 && $day=='Monday'){
			  return true;
		  }else if($wd==7 && $day=='Tuesday'){
			  return true;
		  }else if($wd==8 && $day=='Wednesday'){
			  return true;
		  }else if($wd==9 && $day=='Thursday'){
			  return true;
		  }else if($wd==10 && $day=='Friday'){
			  return true;
		  }else if($wd==11 && $day=='Saturday'){
			  return true;
		  }else if($wd==12 && $day=='Sunday'){
			  return true;
		  }else
		  return false;		  
	  }
	  
	  public function getMaxWoNumber($bid){
		  if(!empty($bid)){
			 $select = $this->select()
                          ->from(array('t' => 'work_order'),array(new Zend_Db_Expr('MAX(wo_number) as maxwnum')));
            $select = $select->where( 'building = ? ', $bid );      
            $row = $this->fetchRow($select);
				if(!$row){
					return false;
				}else{
					$tData = $row->toArray();
					return $tData ['maxwnum'];
				}
        }else
        return false;
		  
	  }
	  
	  public function sendClosedNotification($woId,$cur_user){
		
		$woInfo = $this->getWorkOrderInfo($woId);
		if($woInfo){
			$woData =$woInfo[0]; 
			$htmlContent = $this->getCloseContent($woData);
			$this->sendNotificationMail($cur_user,$woData->create_user,$woData->email,$htmlContent['subject'],$htmlContent['content']);
			
		}  
	  }
	  
	  public function getCloseContent($woData){
		   $emailMapper = new Model_Email();
		   $htmlDocId = 14; // email template id
		   $loadTemplate = $emailMapper->loadEmailTemplate($htmlDocId);
		     if($loadTemplate){
				$emailContent = $loadTemplate[0];
				$emailSubject = $emailContent['email_subject'];
				$emailBody = $emailContent['email_content'];
				/*********change content from template ********/
				$emailBody = str_replace('[[++buildingName]]', $woData->buildingName, $emailBody);
				$emailBody = str_replace('[[++wo_number]]', $woData->wo_number, $emailBody);
				
				$htmlContent = array('subject'=>$emailSubject,'content'=>$emailBody);
				return $htmlContent;
			 }
	  }
	  
}
