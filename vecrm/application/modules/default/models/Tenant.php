<?php 
class Model_Tenant extends Zend_Db_Table_Abstract {

   protected $_name = 'tenant';   
   protected $_tab_role = 'tenant';   
   public $_errorMessage='';
   
    

    /* Save user/client */
    public function insertTenant($data) {				
        try{
           
            $this->_errorMessage="";   	   	
            //$data['password']=md5($data['uPass']);
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    /**
     * isTenantExist check user exist or not
     * 
     * @param String $username
     * 
     * @return Array (it tenant exist reture tenant detail other wise empty array)
     * @author : Anuj Kumar
     */
    public function isTenantExist($username) {
        $select=$this->select()->where('userName = ?', $username);				
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;	
    }

    public function getTenantById($id, $recoverFlag=false){
        
        $db = Zend_Db_Table::getDefaultAdapter(); 
        if($recoverFlag){
          $select = $db->select()
                         ->from(array('t'=>'tenant'))
                         ->joinLeft(array('u'=>'users'),'u.uid = t.userId',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id'))
                         ->where('t.id=?',$id)
                         ->where('t.remove_status=1 OR t.userStatus = 1 OR t.history_remove=0');

        }
        else{			
          $select = $db->select()
                         ->from(array('t'=>'tenant'))
                         ->joinLeft(array('u'=>'users'),'u.uid = t.userId',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id'))
                         ->where('t.id=?',$id); 


        }
        $matchjobRes=$db->fetchAll($select);

        return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;  
   }
    
   public function getTenantByBuildingId($buildId, $recoverFlag=false){
        
        $db = Zend_Db_Table::getDefaultAdapter(); 

        if($recoverFlag){
          $select = $db->select()
                         ->from(array('t'=>'tenant'))
                         ->joinLeft(array('u'=>'users'),'u.uid = t.userId',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id'))
                         ->where('t.buildingId=?',$buildId)
                         ->where('t.remove_status=1 OR t.userStatus = 1 OR t.history_remove=0');
                    
        }
        else{
          $select = $db->select()
                         ->from(array('t'=>'tenant'))
                         ->joinLeft(array('u'=>'users'),'u.uid = t.userId',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id'))
                         ->where('t.buildingId=?',$buildId)
                         ->where('t.remove_status=?',0);

        }

        $matchjobRes=$db->fetchAll($select);

        return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;  
   }

   public function updateTenant($data,$id){
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('id = ?', $id);    
        unset($data['uid']);
        unset($data['module']);
        unset($data['building']);
        unset($data['password']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){           
            echo $e->getMessage(); die();
        }   
   }
   
   public function updateTStatusByBid($data,$buildId){
	   $where = $this->getAdapter()->quoteInto('buildingId = ?', $buildId);
	   try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){           
            echo $e->getMessage(); die();
        } 
   }

  /***
   * Check tenant name
   */ 
   
   public function checkTenantByName($tName,$building, $tId=''){
	   $select=$this->select()->where('tenantName = ?', $tName);
	   $select = $select->where('buildingId = ?', $building);
	   if(!empty($tId)){
		   $select = $select->where('id <>?', $tId);
	   }				
       $res=$this->fetchAll($select);
       return ($res && sizeof($res)>0)? $res->toArray() : false ;
   }
   
   public function deleteTenant($tId){
		if(!empty($tId) && $tId !=0){
			$where = $this->getAdapter()->quoteInto('id = ?', $tId);			
			try {
				$tmodel= new Model_TenantUser();
				$tenantusers = $tmodel->getTenantUsers($tId);
				$userMapper = new Model_User();
				foreach($tenantusers as $tuser){
					$deleteUser = $userMapper->deleteUser($tuser->uid);
				}	
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
	
	public function getTenantByUser($uid){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db->select()
                         ->from(array('t'=>'tenant'))
                         ->join(array('tuser'=>'tenantUsers'),'tuser.tenantId = t.id',array('tuId'=>'tuser.id','tuserId'=>'tuser.id','tenantId'=>'tuser.tenantId','tenantuserId'=>'tuser.userId','suite_location'=>'tuser.suite_location','cc_enable'=>'tuser.cc_enable','send_as'=>'tuser.send_as','complete_notification'=>'tuser.complete_notification'))
                         ->joinLeft(array('u'=>'users'),'u.uid = tuser.userId',array('uid'=>'u.uid','firstName'=>'u.firstName','lastName'=>'u.lastName','userpNumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id','userName'=>'u.userName'))
                         ->where('tuser.userId=?',$uid);
         $matchjobRes=$db->fetchAll($select);

        return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;                
	}
	
	public function getTenantByUid($uid){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db->select()
                         ->from(array('t'=>'tenant'))
                         ->join(array('tuser'=>'tenantUsers'),'tuser.tenantId = t.id',array('tuId'=>'tuser.id'))                         
                         ->where('tuser.userId=?',$uid);
         $matchjobRes=$db->fetchAll($select);

        return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;                
	}
	
	public function getTenantByWoId($woId){
		 $db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db->select()
                         ->from(array('t'=>'tenant'))
                         ->join(array('wo'=>'work_order'),'wo.tenant = t.id',array('tenantId'=>'wo.tenant'))                         
                         ->where('wo.woId=?',$woId);
         $matchjobRes=$db->fetchAll($select);

        return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;
	}

    public function getRecoveryTeants(){
		$date_6back = date("Y-m-d", strtotime("-6 months"));
		$select=$this->select()->where('remove_status = ?', '1');
		$select = $select->where('remove_status <= ?', $date_6back);
		$select = $select->where('history_remove = ?', '0');				
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
		
	}
	
	public function getHistoryTeants(){
		$date_5yback = date("Y-m-d", strtotime("-5 years"));
		$select=$this->select()->where('history_remove = ?', '1');
		$select = $select->where('history_date <= ?', $date_5yback);						
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
		
	}
	
	public function moveRemoveToHistory(){
		$removeTenants = $this->getRecoveryTeants();
		if($removeTenants){
			$tmodel= new Model_TenantUser();
			foreach($removeTenants as $rt){
				if(isset($rt['id'])&& $rt['history_remove']=='0'){
					$tId = $rt['id'];
					try{						
						$tenantusers = $tmodel->getTenantUsers($tId);
						$userMapper = new Model_User();
						foreach($tenantusers as $tuser){
							$historyUser = $userMapper->historyUser($tuser->uid);
						}	
						$histData = array();
						$histData['history_remove']=1;
						$histData['history_date']=date('Y-m-d');
						$moveHistory = $this->updateTenant($histData,$tId);
					}catch(Exception $e){
						echo $e->getMessage();
					}
				}
			}
		}
		
	}
	
	/********** Delete Tenant History record *********/
	
	public function deleteTenantHistory(){
		$historyTenants = $this->getHistoryTeants();
		if($historyTenants){
			$tmodel= new Model_TenantUser();
			foreach($historyTenants as $ht){
				if(isset($ht['id'])&& $ht['history_remove']=='1'){
					$tId = $ht['id'];
					try{						
						$tenantusers = $tmodel->getTenantUsers($tId);
						$userMapper = new Model_User();
						foreach($tenantusers as $tuser){
							$historyUser = $userMapper->deleteHistoryUser($tuser->uid);
						}
						$deleteTuser = $tmodel->deleteTenantUserBytId($tId);	
						$this->delete('id = '.$tId);
					}catch(Exception $e){
						echo $e->getMessage();
					}
				}
			}
		}
		
	}
	
   
}	

?>
