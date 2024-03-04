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
						$buildingDetail = $building->getBuildingList($build_id_array);
						//print_r($build_data);
					}
		   }        
	    
        $this->view->role = $this->roleId;
        $this->view->cust_id = $this->cust_id;
        $this->view->companyListing = array(
            "roles"     => $roleDetail,
            "modules"   => $modulesDetail,
            "buildings" => $buildingDetail,
        );

        $this->view->acesshelper = $this->accessHelper;
        // to set the access of Remit to Information
         $this->view->user_wizard_location = 8;
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
        $userDetail = $userModel->getUserByName($query['q'],$this->cust_id);
        if(!empty($userDetail)) {
            foreach($userDetail as $key => $user) {
                $userDetail[$key]['label'] = $user['email'];
                $userDetail[$key]['value'] = $user['email'];
            }
        }
        echo $query["callback"]."(".json_encode($userDetail).")";
        exit(0);
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
            
            $userBuildingAccess = array();
             $detail['building'] = array(); 
        if(isset($data['building']) && count($data['building'])>0){ 
			 
            foreach($data['building'] as $key => $building ) {
                $userBuildingAccess[] = array(
                    "user_id"           => $data['uid'],
                    "building_id"       => $building,
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
                
                ///$Model_User_Building_Module->updateBuildingModule($userBuildingAccess);
                $Model_User_Building_Module->updateUserBuildingModule($userBuildingAccess);
            }else{
				
				$Model_User_Building_Module->deleteData($data['uid']);
			}     
                       
        }
         
        if($mail_flag){
			 $res = $this->sendMail($detail);
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
              }
              else{
                    $logData['email_status'] =  0;
                    $email_log->insertLog($logData);
              }
		  
		}        
        echo json_encode($detail);
        
        exit();
    }
    
    
   public function sendMail($detail){	   
	   $emial_template = new Zend_View();
                $emial_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/email/');
                $emial_template->assign('name', $detail['name']);
				$emial_template->assign('access', $detail['access']);
				$emial_template->assign('email', $detail['email']);
				$emial_template->assign('password', $detail['gpass']);
				$emial_template->assign('building',$detail['building']);
								
				$mail = new Zend_Mail('utf-8');
				// render view
				  $bodyText = $emial_template->render('newUserRegistation.phtml');
				//echo $bodyText;exit;
							
				// configure base stuff
				$mail->addTo($detail['email'], $detail['email']);
				$mail->setSubject('New User Registration Conformation');
				$mail->setFrom('info@virtualemployee.com','Vecrm');
				$mail->setBodyHtml($bodyText);
				$res = $mail->send();				
                return $res;
			
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
        $userDetail = $userModel->isUserExist($query['email']);        
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
    
}

?>
