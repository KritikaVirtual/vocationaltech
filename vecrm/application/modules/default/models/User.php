<?php 
class Model_User extends Zend_Db_Table_Abstract {

   protected $_name = 'users';   
   protected $_tab_role = 'users';   
   public $_errorMessage='';
   
    /* Get all users/staff detail */
    public function getUserByName($name,$companyID='') {
        $select=$this->select()->where('userName like ?',"%$name%");
        if($companyID){
			$select = $select->where('cust_id=?',$companyID);
			$select = $select->where('role_id!=?',9);
			$select = $select->where('role_id!=?',5);
			$select = $select->where('role_id!=?',7);
		}
		//echo $select->__toString();				
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;	
    } 
    
    public function getUserById($uid) {
       
        if($uid){
			 $select=$this->select()->where('uid =?',$uid);					
             $res=$this->fetchAll($select);
           return ($res && sizeof($res)>0)? $res : false ;
        }else return false;	
    } 

    
    /* Update  users detail by ID */		
    public function updateUser($data,$id){
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('uid = ?', $id);	
        unset($data['uid']);
        unset($data['module']);
        unset($data['building']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }

    /* Save user/client */
    public function insertUser($data) {				
        try{
            unset($data['uid']);
            unset($data['module']);
            unset($data['building']);
            $this->_errorMessage="";   	   	
            //$data['password']=md5($data['uPass']);		
            $data['regDate']=date('Y-m-d H:i:s');	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    /**
     * isUserExist check user exist or not
     * 
     * @param String $username
     * 
     * @return Array (it user exist reture user detail other wise empty array)
     * @author : Brijesh Kumar
     */
    public function isUserExist($username) {
        $select=$this->select()->where('userName = ?', $username);
        $select = $select->orwhere('email= ?',$username);			
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;	
    }
    
    /**
     * setPasskey is used to set the passkey for reset password
     * 
     * @param string $passkey, Int $id
     * 
     * @return boolean True/False
     */
    public function setPasskey($passkey, $id) {
        $where = $this->getAdapter()->quoteInto('uid = ?', $id);
        try {
            $this->update(array(
                "passkey"           => $passkey,
                "passkeyStatus"     => 1,
                "passkeyTime"      => date('Y-m-d h:i:s')
            ), $where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }
    }
	 public function passkey($passkey) {
        $select=$this->select()->where('passkey = ?', $passkey);				
        $res=$this->fetchAll($select);
		//echo sizeof($res);exit;
        return ($res && sizeof($res)>0)? $res->toArray() : false ;	
    }
	// Update Password
	 public function updateresetpassword($password,$passkey) {
        $where = $this->getAdapter()->quoteInto('passkey = ?', $passkey);
        try {
            $this->update(array(
                "password"           => $password,
                "passkey"           => '',
                "passkeyStatus"     => 0                
            ), $where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }
    }
	public function getUsersByCompany($companyID){
	  if(!empty($companyID)){	
	        $select=$this->select()->where('cust_id = ?', $companyID);				
            $res=$this->fetchAll($select);
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
	    }else
	    return false;
	}
	
	public function changePassword($password,$id){
		$this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('uid = ?', $id);	
        
        try {
            $this->update(array(
                "password"           => md5($password)                
            ),$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
	}
	
	public function deleteCompanyUser($companyID){
		if(!empty($companyID) && $companyID !=0){
			   try{
				  $this->delete('cust_id = '.$companyID);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
	
	public function getCompanyAdminUser(){
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
				 ->from(array('u' => 'users'), array('uid','userName','firstName','lastName','email'))
				 ->joinInner(array('cp' => 'company'), 'u.cust_id = cp.cust_id', array('companyName'))
				 ->where('u.remove_status = ?', '0')
				 ->where('u.role_id = ?', '9')
				 ->where('cp.status = ?', '1');				 				 
			$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;
	}


     public function tenantUserExist($username, $roleId) {
        $select=$this->select()->where('userName = ?', $username)->where('role_id = ?', $roleId);                
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? true : false ;  
    }
    
    public function getAllUserOfBuildingAdmin($user_id){
		if($user_id){
			$db = Zend_Db_Table::getDefaultAdapter(); 				 
			$query = 'SELECT DISTINCT u.uid, u.firstName, u.lastName, u.email FROM `users` as u, 
					  user_building_module_access as ub WHERE ub.user_id = u.uid AND 
					  ub.building_id IN (SELECT ubm.building_id FROM user_building_module_access as ubm  
					  WHERE ubm.user_id="'.$user_id.'" ) AND u.uid!='.$user_id;		 
													 
				$res = 	 $db->fetchAll($query);
			return ($res && sizeof($res)>0)? $res : false ;
		}else
		   return false;
	}
	
	public function getAllUserOfBuilding($building){
		if($building){
			$db = Zend_Db_Table::getDefaultAdapter(); 				 
			$query = 'SELECT DISTINCT u.uid, u.firstName, u.lastName, u.email FROM `users` as u, 
					  user_building_module_access as ub WHERE ub.user_id = u.uid AND 
					  ub.building_id = "'.$building.'"';		 
													 
				$res = 	 $db->fetchAll($query);
			return ($res && sizeof($res)>0)? $res : false ;
		}else
		   return false;
	}


    public function getAllAccountUserOfBuilding($building){
        if($building){
            $db = Zend_Db_Table::getDefaultAdapter();                
            $query = 'SELECT DISTINCT u.uid, u.firstName, u.lastName, u.email FROM `users` as u, 
                      user_building_module_access as ub WHERE ub.user_id = u.uid AND 
                      ub.building_id = "'.$building.'"';         
                                                     
                $res =   $db->fetchAll($query);
            return ($res && sizeof($res)>0)? $res : false ;
        }else
           return false;
    }
	
	public function deleteUser($user_id){
		if(!empty($user_id) && $user_id !=0){
			$where = $this->getAdapter()->quoteInto('uid = ?', $user_id);			
			try {
				$this->update(array(
					"remove_status"           => 1,
					"remove_date" =>date('Y-m-d')                
				),$where);
				return true;
			}catch(Exception $e){    		
				echo $e->getMessage(); die();
			}
		}
	}
	
	/********Move delete user in history record *******/
	public function historyUser($user_id){
		if(!empty($user_id) && $user_id !=0){
			$where = $this->getAdapter()->quoteInto('uid = ?', $user_id);			
			try {
				$this->update(array(
					"history_remove"           => 1,
					"history_date" =>date('Y-m-d')                
				),$where);
				return true;
			}catch(Exception $e){    		
				echo $e->getMessage(); die();
			}
		}
	}
	
	/******** delete history user *******/
	public function deleteHistoryUser($user_id){
		if(!empty($user_id) && $user_id !=0){					
			try {
				$this->delete('uid = '.$user_id);
				return true;
			}catch(Exception $e){    		
				echo $e->getMessage(); die();
			}
		}
	}
	
	/**
	 * check user name
	 */ 
	 
	 public function checkUserName($userName,$uid=''){
		$select=$this->select()->where('userName = ?', $userName);
        if(!empty($uid)){
			$select = $select->where('uid <> ?', $uid);
		}			
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
	 }
	 
	 /**
	 * check user email
	 */ 
	 
	 public function checkUserEmail($email,$uid=''){
		$select=$this->select()->where('email = ?', $email);
        if(!empty($uid)){
			$select = $select->where('uid <> ?', $uid);
		}			
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
	 }
	 
	 /********get User by Ids in set ******/
	 
	 public function getUserBySetIds($ids){		 
        if(!empty($ids)){
		$select=$this->select();
		$select = $select->where("uid IN ($ids)");
		$select = $select->where('status=?','1');
		$select = $select->where('remove_status=?','0');						
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        false;
	 }
	 
	 public function getUserInfo($userId){
		 if($userId){
           $db = Zend_Db_Table::getDefaultAdapter();                
           $select = $db->select()
                      ->from(array('us' => 'users'))                      
                      ->joinLeft(array('ro'=>'role'),'ro.roleID = us.role_id',array('role_title'=>'ro.title'))                      
                      ->where('us.uid=?',$userId);
           $res = $db->fetchAll( $select );      
            return ($res && sizeof($res)>0)? $res : false ;
        }else
           return false;
		}
}	

