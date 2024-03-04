<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserController
 *
 * @author ivtidai
 */
class UserController extends Ve_Controller_Base {
    
    public function init()  {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');
       $this->accessHelper = $this->_helper->access; 
    }
    
    // Call befor any action and check is user login or not
    public function preDispatch()
    {    	
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/index');
        }
        
        $level=(Zend_Auth::getInstance()->getStorage()->read())? Zend_Auth::getInstance()->getStorage()->read()->role_id:'';    	     	
    	$this->userId=Zend_Auth::getInstance()->getStorage()->read()->uid;
    	$this->roleId=Zend_Auth::getInstance()->getStorage()->read()->role_id;
    	$this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
    }	
    
    public function indexAction() {
        $roleMapper = new Model_Role();
        $roleDetail = $roleMapper->getRole();
         $building = new Model_Building();
        $modules = new Model_Module();
        $modulesDetail = $modules->getModule();
        /* Get all destribution group */
        $emailGroup = new Model_EmailGroup();        
        $buildingDetail =array();
        if($this->roleId=='9'){
            $buildingDetail = $building->getCompanyBuilding($this->cust_id);
        }else{
           $user_build_mod = new Model_UserBuildingModule();
           $buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
           if($buildinglists){
               $build_id_array = array();
               foreach($buildinglists as $buildlist)
                   $build_id_array[] = $buildlist['building_id'];
                   //$emailGroup = 
                   $buildingDetail = $building->getBuildingList($build_id_array);
                   //$buildingDetail = 'sanjay';
                   //print_r($build_data);
           }
        }
        $buildingDetail1 =array();           
        foreach($buildingDetail as $data){
            //$buildingDetail1[] = $data;
            $datadesc = array();
            $data['dist-'.$data['build_id']] = $emailGroup->get_email_group_by_building_id($data['build_id']);
            //array_push($data,$datadesc);
            $buildingDetail1[] = $data;
            //array_push($buildingDetail1,$data);
        }
                   
        //echo "<pre>";
        //print_r($buildingDetail1);
        // die;
        
        
        $this->view->role = $this->roleId;
        $this->view->cust_id = $this->cust_id;
        $this->view->companyListing = array(
            "roles"     => $roleDetail,
            "modules"   => $modulesDetail,
            "buildings" => $buildingDetail1,
        );

        $this->view->acesshelper = $this->accessHelper;
        
        // to set the access of Remit to Information
        $this->view->user_wizard_location = 8;
	$this->view->userId = $this->userId;
    }
    
    
    public function getuserdetailAction() {
        $query = $this->_request->getParams();
        $userModel = new Model_User();
        
        $userDetail = $userModel->getUserByName($query['q'],$query['cid']);
        if(!empty($userDetail)) {
            foreach($userDetail as $key => $user) {
                $userDetail[$key]['label'] = $user['email'];
                $userDetail[$key]['value'] = $user['email'];
            }
        }
        echo $query["callback"]."(".json_encode($userDetail).")";
        exit(0);
    }
    
    public function getcompanyuserdetailAction() {
        $query = $this->_request->getParams();
        $userModel = new Model_User();
        $userDetail = $userModel->getUserByEmail($query['q'],$this->cust_id);
        if(!empty($userDetail)) {
            foreach($userDetail as $key => $user) {
                $userDetail[$key]['label'] = $user['email'];
                $userDetail[$key]['value'] = $user['email'];
            }
        }
        echo $query["callback"]."(".json_encode($userDetail).")";
        exit(0);
    }
    public function getcompanyuserdetail2Action() {
        $query = $this->_request->getParams();
        $userModel = new Model_User();
        $userDetail = $userModel->getUserByEmail($query['q'],$this->cust_id);
	$userdata=array();
        if(!empty($userDetail)) {
            foreach($userDetail as $key => $user) {
                $userdata['cust_id'] = $user['cust_id'];
                $userdata['uid'] = $user['uid'];
                $userdata['phoneNumber'] = $user['phoneNumber'];
                $userdata['firstName'] = $user['firstName'];
                $userdata['lastName'] = $user['lastName'];
                $userdata['email'] = $user['email'];
                $userdata['title'] = $user['Title'];
                $userdata['status'] = $user['role_id'];
            }
        }
	$userdata = array_map("stripslashes", $userdata);
        echo json_encode($userdata);
        exit();
    }	
    public function saveuserdetailAction() {
        if( $this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')  {
            $data = $this->getRequest()->getPost();
            
            $userModel = new Model_User();
            $mail_flag=0;
            if(isset($data['building']) && count($data['building'])>0){				
				$building = new Model_Building();
				$buildingDetail = $building->getBuildingList($data['building']);
		    }
            if(isset($data['module']) && count($data['module'])>0){
               $modules = new Model_Module();
               $modulesDetail = $modules->getModuleListing($data['module']);
		    }
            $roleMapper = new Model_Role();
            $roleDetail = $roleMapper->getRole($data['role_id']);
            
            $detail = array(
                "name"          => $data['firstname']." ".$data['lastname'],
                "title"         => $data['title'],
                "office_phone"  => $data['phoneNumber'],
                "email"         => $data['email'],
                "username"      => $data['userName'],
                "userPassowd"   => "**************",
                "access"        => $roleDetail[0]['title'],
                "firstName"     => $data['firstname'],
                "lastName"      => $data['lastname']
            );            
            
            if(!empty($data['uid'])) {
                $userModel->updateUser($data, $data['uid']);
            } else {
                        $userDetail = $userModel->isUserExist($data['email']);
                        if(!$userDetail){						
                                $gpass= $this->generateRandomString();
                                $detail['gpass']	= $gpass;
                                $data['password'] = md5($gpass);
                                $data['cust_id']= $this->cust_id;
                                $data['uid'] = $userModel->insertUser($data);
                                $mail_flag =1;

                        }else{
                                $data['uid'] = $userDetail[0]['uid'];
                        }
            }
           // print_r($data);
            //die;
            $distri = $data['distribution'];
            $dist =array();
            foreach($distri as $val){
                $rs = explode("-",$val);
                $dist[$rs[0]] = $rs[1];
            }
            
            $userBuildingAccess = array();
            $detail['building'] = array(); 
        if(isset($data['building']) && count($data['building'])>0){ 
			 
            foreach($data['building'] as $key => $building ) {
                
                $userBuildingAccess[] = array(
                    "user_id"           => $data['uid'],
                    "building_id"       => $building,
                    "distributiongroup_id"  =>implode(",",$distri), //$dist[$building],
                    "modules_id"        => implode(",", $data['module']),
                    "assigned_date"     => date('Y-m-d H:i:s'),
                    "last_update_date"  => date('Y-m-d H:i:s'),
                );
            }
		}
            if(isset($data['module']) && count($data['module'])>0 && isset($data['building'])){
              foreach($modulesDetail as $key => $module) {
                  foreach($data['building'] as $key => $building ) {
                      $detail['building'][] = array(
                          "building"  =>   $this->getBuildingName($buildingDetail, $building),
                          "module"    =>   $module['module_name']
                      );
                  }
              }
            }
            $Model_User_Building_Module = new Model_UserBuildingModule();
            if(!empty($userBuildingAccess)) {
		       $building = new Model_Building();
		       if($this->roleId=='9'){
			       $buildingDetail = $building->getCompanyBuilding($this->cust_id);
			    }else{
				    $buildinglists = $Model_User_Building_Module->getUserBuildingIds($this->userId);
				    if($buildinglists){
					    $build_id_array = array();
					     foreach($buildinglists as $buildlist)
						$build_id_array[] = $buildlist['building_id'];
						$buildingDetail = $building->getBuildingList($build_id_array);
						//print_r($build_data);
					}
		        }
                
                /// $Model_User_Building_Module->updateBuildingModule($userBuildingAccess);
                //print_r($userBuildingAccess);
                //die;
                $Model_User_Building_Module->updateUserBuildingModule($userBuildingAccess, $buildingDetail);
            }else{
                    if($isDelete!='skip') { 
                       $Model_User_Building_Module->deleteData($data['uid']);
                    }
            }     
                       
        }
        $group = new Model_EmailGroupUsers();
        

        foreach($dist as $val){
            $group->Adduserindistributiongroup($data['uid'],$val);
        }
        if($mail_flag){
                
                $email = new Model_EmailGroupUsers();
//                $AllEmails = array();
//                if(!empty($dist)){
//                    foreach($dist as $val){
//                         $result = $email->getGroupUsers($val);
//                         //print_r($result);
//                         foreach($result as $nval){
//                             array_push($AllEmails,$nval->email);
//                         }
//                    }
//                   
//                }

                //$detail['allemails'] =  $AllEmails;
               // die;
                $res = $userModel->sendUserMail($detail);
                $email_log = new Model_Log();
                $logData = array();
                $logData['email_sent_by'] =  $this->userId;
                $logData['userId'] =  $data['uid'];
                $logData['email'] =  $detail['email'];
                $logData['log_type'] =  'email';
                $logData['log_message'] =  'New user created by using add new user wizard';

                if($res){
                    $logData['email_status'] =  1;
                    $email_log->insertLog($logData);
                }else{
                    $logData['email_status'] =  0;
                    $email_log->insertLog($logData);
                }
	}        
        echo json_encode($detail);
        exit();
    }
    
    
    function generateRandomString($length = 8) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
    public function getBuildingName($buildingDetail, $building) {
        foreach ( $buildingDetail as $key => $data ) {
            if($data['build_id'] == $building) {
                return $data['buildingName'];
            }
        }
    }
    public function userinfoAction(){
		$roleMapper = new Model_Role();
        $roleDetail = $roleMapper->getRole();		
	}
	
	public function checkuserAction(){
		$query = $this->_request->getParams();
        $userModel = new Model_User();
        $userBuildingModule = new Model_UserBuildingModule();
		if(isset($query['uid'])) {
			$userDetail = $userModel->checkUserName($query['email'], $query['uid']);   
		} else {
			$userDetail = $userModel->checkUserName($query['email']); 
		}		
        if($userDetail){ 
			$cust_id = $userDetail[0]['cust_id'];
			$role_id = $userDetail[0]['role_id'];
			$notallow = array(1,5,7,9);
			if(!in_array($role_id,$notallow)){
				$userBuilding = $userBuildingModule->getUserBuildingIds($userDetail[0]['uid']);
				if($this->cust_id!=$cust_id){
				  echo 'true';
				}else{ 
				  print_r(json_encode($userBuilding));
				}
			}else
			echo 'true';
		}else
		echo 'false';
		 exit(0);
	}
	public function validatebuildinguserAction(){
		$data=$this->getRequest()->getPost();	

		if($data['build_id']){
			$userModel	= 	new Model_UserBuildingModule();
			$d			= 	$userModel->getTotalUser($data['build_id']);

			if(count($d)) $user_id=(int)$d[0]['user_id'];
			else $user_id=0;
			
			$total=count($d);
			
			echo json_encode(array("total"=>$total,"user_id"=>$user_id,"status"=>200));
			exit;			
		}else{
			echo json_encode(array("total"=>0,"user_id"=>0,"status"=>400));
			exit;						
		}		
	}
    public function checksessionAction(){
        if ($this->getRequest()->isXmlHttpRequest()) {
            //$data=$this->getRequest()->getPost();
            if (Zend_Session::sessionExists()) {
                //Yes session exist!
                //echo $session_time = ini_get('session.gc_maxlifetime');                
                echo 1;
            }else{
                $userModel = new Model_User();                    
                $uid = $_SESSION['Admin_User']['user_id'];
                $u= $userModel->getUserById($uid);
                $IP = $u[0]->ip;
                if ($IP == NULL || empty($IP) ) {
                    echo 2;
                }else{
                    echo 0;			
                }
            }
        }
        exit;
	}
        
        Public function checkuseripAction(){
            $userModel = new Model_User();                    
            $uid = $_SESSION['Admin_User']['user_id'];
            $u= $userModel->getUserById($uid);
            $IP = $u[0]->ip;
            //if($updateUser)
            if ($IP == NULL || empty($IP) ) {
			//Yes session exist!
			//echo $session_time = ini_get('session.gc_maxlifetime');
			
			echo 1;
		}else{
		
			// Sorry session not exist
			echo 0;			
		}		
		exit;
        }

}

?>
