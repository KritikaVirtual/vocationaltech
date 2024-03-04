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
                         ->joinLeft(array('u'=>'users'),'u.uid = t.userId',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','userName'=>'u.userName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id', 'note_notification'=>'u.note_notification'))
						 ->joinLeft(array('st' => 'states'),'st.state_code = t.state_code',array('st.state as statename'))
                         ->where('t.id=?',$id)
                         ->where('t.remove_status=1 OR t.userStatus = 1 OR t.history_remove=0');

        }
        else{			
          $select = $db->select()
                         ->from(array('t'=>'tenant'))
                         ->joinLeft(array('u'=>'users'),'u.uid = t.userId',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','userName'=>'u.userName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id', 'note_notification'=>'u.note_notification'))
						 ->joinLeft(array('st' => 'states'),'st.state_code = t.state_code',array('st.state as statename'))
                         ->where('t.id=?',$id); 


        }
        $matchjobRes=$db->fetchAll($select);

        return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;  
   }
    

   public function checkTenantUserEmail($email,$bid){
    $db = Zend_Db_Table::getDefaultAdapter(); 
    $select = $db->select()
            ->from(array('b'=>'buildings'))
            ->joinRight(array('c'=>'company'),'c.cust_id = b.cust_id',array('Building_Name'=>'b.buildingName','Management_Company'=>'c.companyName','BuildingId'=>'b.build_id'))
            ->joinRight(array('t'=>'tenant'),'b.build_id = t.buildingId',array('Tenant_Name'=>'t.tenantName','Tenant_Suite'=>'t.suite'))
            ->joinLeft(array('tu'=>'tenantusers'),'t.id = tu.tenantId',array('User_Suit_Location'=>'tu.suite_location','TenantId'=>'t.id'))
            ->joinLeft(array('u'=>'users'),'tu.userId = u.uid',array('User_EMail'=>'u.email','User_First_Name'=>'u.firstName','User_Last_Name'=>'u.lastName','User_User_Name'=>'u.userName','UserID'=>'u.uid'))
            ->where('u.email=?',$email)
            ->where('t.buildingId=?',$bid);
           // echo $select;

            $matchjobRes=$db->fetchAll($select);
            return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;  
                   
 }

 public function filterTenentMultiUserList($data){
   
    $db = Zend_Db_Table::getDefaultAdapter(); 
    $select = $db->select()
            ->from(array('u'=>'users'))
            ->joinRight(array('to'=>'au_tenant_options'),'to.UserID = u.uid',array('User_EMail'=>'u.email','User_First_Name'=>'u.firstName','User_Last_Name'=>'u.lastName','User_User_Name'=>'u.userName','UserID'=>'u.uid'))
            //->joinRight(array('tu'=>'tenantusers'),'tu.UserId = to.UserId')
            ->joinRight(array('t'=>'tenant'),'t.id = to.tenantID')
            ->joinRight(array('b'=>'buildings'),'b.build_id = t.buildingId');
           if(isset($data['email']) && !empty($data['email'])){
             $select->where('u.email=?',$data['email']);
           }
         //  if(isset($data['bid'])){
            // $select->where('t.buildingId=?',$data['bid']);
             $select->where('to.BuidlingID=?',$data['bid']);
          // }
           if ($data['id'] == '0') {
               $select->order('u.userName DESC');
               $select->order('b.buildingName ASC');
               $select->order('t.tenantName ASC');
            } else {
                $select->order('u.userName ASC');
                $select->order('b.buildingName ASC');
                $select->order('t.tenantName ASC');
            }
           // $select->group ( array ("tu.id") );

            //echo $select->__toString()."\n";
           // echo $select ;
          
            $matchjobRes=$db->fetchAll($select);
            return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;  
                   
 }


 public function checktenantAllowOption($email){
   
    $db = Zend_Db_Table::getDefaultAdapter(); 
    $select = $db->select()
            ->from(array('u'=>'users'))
            ->joinRight(array('to'=>'au_tenant_options'),'to.UserID = u.uid',array('User_EMail'=>'u.email','User_First_Name'=>'u.firstName','User_Last_Name'=>'u.lastName','User_User_Name'=>'u.userName','UserID'=>'u.uid'))
            ->joinInner(array('t'=>'tenant'),'t.id = to.TenantID')
             ->joinInner(array('b'=>'buildings'),'b.build_id = to.BuidlingID');
           // ->joinInner(array('c'=>'company'),'c.cust_id = b.cust_id',array('Building_Name'=>'b.buildingName','Management_Company'=>'c.companyName','BuildingId'=>'b.build_id'))
           // ->joinInner(array('u'=>'users'),'tu.userId = u.uid',array('User_EMail'=>'u.email','User_First_Name'=>'u.firstName','User_Last_Name'=>'u.lastName','User_User_Name'=>'u.userName','UserID'=>'u.uid'));
           // ->joinInner(array('to'=>'au_tenant_options'),'to.TenantID = t.id');
           if($email){
                $select->where('u.email=?',$email);
           }
            //echo $select->__toString()."\n";
            $matchjobRes=$db->fetchAll($select);
            return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;  
                   
 }



 public function getTenatUserByUserid($tenantid){

    $db = Zend_Db_Table::getDefaultAdapter(); 
    $select = $db->select()
            ->from(array('u'=>'users'))
            ->joinRight(array('to'=>'au_tenant_options'),'to.UserID = u.uid',array('User_EMail'=>'u.email','User_First_Name'=>'u.firstName','User_Last_Name'=>'u.lastName','User_User_Name'=>'u.userName','UserID'=>'u.uid'))

            ->joinLeft(array('tu'=>'tenantusers'),'tu.UserID = u.uid',array('User_EMail'=>'u.email','User_First_Name'=>'u.firstName','User_Last_Name'=>'u.lastName','User_User_Name'=>'u.userName','UserID'=>'u.uid','TenantUserId'=>'tu.id','Tenant_Suit_Location'=>'tu.suite_location','is_location_removed'=>'tu.is_location_removed'))
             
            ->joinLeft(array('t'=>'tenant'),'tu.tenantId = t.id',array('Tenant_Remove_Status'=>'t.remove_status','tenantName'=>'t.tenantName'))
          
            

            ->joinLeft(array('b'=>'buildings'),'b.build_id = to.BuidlingID');
             //$select->where('tu.tenantId=?',$tenantid);
             $select->where('tu.userId=?',$tenantid);
            $matchjobRes=$db->fetchAll($select);
            return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ; 
            
 }
 


   public function getTenantByBuildingId($buildId, $recoverFlag=false, $search=array() ){
        
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
                    //->joinLeft(array('tu'=>'tenantusers'),'t.id = tu.tenantId')

                    ->joinLeft(array('u'=>'users'),'u.uid = t.userId',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id'))
                    
                    ->where('t.buildingId=?',$buildId)
                    ->where('t.remove_status=?',0);
                   // ->where('tu.is_location_removed=?',0);

                    if(isset($search['search_by']) && $search['search_by']=='tenantName'){
                           $select = $select->where( "t.tenantName Like'%$search[search_value]%'" );
                    }
                    if(isset($search['search_by']) && $search['search_by']=='first_name'){
                           $select = $select->where( "u.firstname Like'%$search[search_value]%'" );
                    }
                    if(isset($search['search_by']) && $search['search_by']=='last_name'){
                           $select = $select->where( "u.lastname Like'%$search[search_value]%'" );
                    }
                    if(isset($search['search_by']) && $search['search_by']=='email'){
                           $select = $select->where( "u.email Like'%$search[search_value]%'" );
                    }
        }
        // echo $select;
        // die;
        $select = $select->order(array('t.tenantName ASC'));
        $matchjobRes=$db->fetchAll($select);

        return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;  
   }
   
   
    public function gettenantsearchresult($buildId, $search=array() ){
        
        $db = Zend_Db_Table::getDefaultAdapter(); 

        $select = $db->select()
                  ->from(array('t'=>'tenant'));

                  if(isset($search['search_by']) && $search['search_by']=='tenantName'){
                        $select = $select->joinInner(array('tu'=>'tenantusers'),'t.id= tu.tenantId',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id','is_location_removed'=>'tu.is_location_removed'));
                        $select = $select->joinLeft(array('u'=>'users'),'u.uid = t.userId',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id'));
                        $select = $select->where('t.buildingId=?',$buildId);
                        $select = $select->where('t.remove_status=?',0);
                        $select = $select->where( "t.tenantName Like'$search[search_value]%'" );
                        $select = $select->where('tu.is_location_removed=?',0);

                  }
                  if(isset($search['search_by']) && $search['search_by']=='first_name'){
                        $select = $select->joinInner(array('tu'=>'tenantusers'),'t.id= tu.tenantId',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id','is_location_removed'=>'tu.is_location_removed'));
                        $select = $select->joinInner(array('u'=>'users'),'u.uid = tu.userId');
                        $select = $select->where('t.buildingId=?',$buildId);
                        $select = $select->where('t.remove_status=?',0);
                        $select = $select->where('tu.is_location_removed=?',0);

                        $select = $select->where( "u.firstname Like'$search[search_value]%'" );
                  }
                  if(isset($search['search_by']) && $search['search_by']=='last_name'){
                        $select = $select->joinInner(array('tu'=>'tenantusers'),'t.id= tu.tenantId',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id','is_location_removed'=>'tu.is_location_removed'));
                        $select = $select->joinInner(array('u'=>'users'),'u.uid = tu.userId');
                        $select = $select->where('t.buildingId=?',$buildId);
                        $select = $select->where('t.remove_status=?',0);
                        $select = $select->where('tu.is_location_removed=?',0);

                        $select = $select->where( "u.lastname Like'$search[search_value]%'" );
                  }
                  if(isset($search['search_by']) && $search['search_by']=='email'){
                        $select = $select->joinInner(array('tu'=>'tenantusers'),'t.id= tu.tenantId',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id','is_location_removed'=>'tu.is_location_removed'));
                        $select = $select->joinInner(array('u'=>'users'),'u.uid = tu.userId');
                        $select = $select->where('t.buildingId=?',$buildId);
                        $select = $select->where('t.remove_status=?',0);
                        $select = $select->where('tu.is_location_removed=?',0);

                        $select = $select->where( "u.email Like'$search[search_value]%'" );
                  }
        $select = $select->order(array('t.tenantName ASC'));
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
					"remove_status"=> 1,
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
                         ->join(array('tuser'=>'tenantusers'),'tuser.tenantId = t.id',array('tuId'=>'tuser.id','tuserId'=>'tuser.id','tenantId'=>'tuser.tenantId','tenantuserId'=>'tuser.userId','suite_location'=>'tuser.suite_location','cc_enable'=>'tuser.cc_enable','send_as'=>'tuser.send_as','complete_notification'=>'tuser.complete_notification'))
                         ->joinLeft(array('u'=>'users'),'u.uid = tuser.userId',array('uid'=>'u.uid','firstName'=>'u.firstName','lastName'=>'u.lastName','userpNumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id','userName'=>'u.userName','note_notification'=>'u.note_notification', 'userPhoneNumber'=>'u.phoneNumber'))
						 ->joinLeft(array('st' => 'states'),'st.state_code = t.state_code',array('st.state as statename'))
                         ->where('tuser.userId=?',$uid);
         $matchjobRes=$db->fetchAll($select);

        return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;                
	}

    public function getTenanyUserByTenantGroup($tid){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db->select()
                         ->from(array('t'=>'tenant'))
                         ->join(array('tuser'=>'tenantusers'),'tuser.tenantId = t.id',array('tuId'=>'tuser.id','tuserId'=>'tuser.id','tenantId'=>'tuser.tenantId','tenantuserId'=>'tuser.userId','suite_location'=>'tuser.suite_location','cc_enable'=>'tuser.cc_enable','send_as'=>'tuser.send_as','complete_notification'=>'tuser.complete_notification'))
                         ->joinLeft(array('u'=>'users'),'u.uid = tuser.userId',array('uid'=>'u.uid','firstName'=>'u.firstName','lastName'=>'u.lastName','userpNumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id','userName'=>'u.userName','note_notification'=>'u.note_notification', 'userPhoneNumber'=>'u.phoneNumber'))
						 ->joinLeft(array('st' => 'states'),'st.state_code = t.state_code',array('st.state as statename'))
                        // ->where('tuser.userId=?',$uid)
                         ->where('tuser.tenantId=?',$tid);
         $matchjobRes=$db->fetchAll($select);

        return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;                
	}
	public function getTenantCompanies($uid){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db->select()
                         ->from(array('t'=>'tenant'))
                         ->join(array('tuser'=>'tenantusers'),'tuser.tenantId = t.id',array('tuId'=>'tuser.id','tuserId'=>'tuser.id','tenantId'=>'tuser.tenantId','tenantuserId'=>'tuser.userId','suite_location'=>'tuser.suite_location','cc_enable'=>'tuser.cc_enable','send_as'=>'tuser.send_as','complete_notification'=>'tuser.complete_notification'))
                         ->joinLeft(array('u'=>'users'),'u.uid = tuser.userId',array('uid'=>'u.uid','firstName'=>'u.firstName','lastName'=>'u.lastName','userpNumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id','userName'=>'u.userName','note_notification'=>'u.note_notification', 'userPhoneNumber'=>'u.phoneNumber'))
						// ->joinLeft(array('st' => 'states'),'st.state_code = t.state_code',array('st.state as statename'))
                        
                        ->where('tuser.userId=?',$uid)
                        ->where('tuser.is_location_removed=?',0);
                         $select = $select->order(array('t.tenantName ASC'));

         $matchjobRes=$db->fetchAll($select);

        return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;                
	}
	public function getTenantByUid($uid){
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db->select()
                         ->from(array('t'=>'tenant'))
                         ->join(array('tuser'=>'tenantusers'),'tuser.tenantId = t.id')                         
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
	
	public function deleteTenantByBId($bId){
		if(!empty($bId)){
			try{
				$select=$this->select()->where('buildingId = ?', $bId);
				$res=$this->fetchAll($select);
				if($res){
					try{
						foreach($res as $re){
							$tuserModel = new Model_TenantUser();
							$deleteTUser = $tuserModel->deleteTenantUserBytId($re->tenantId);
							$this->delete('id = '.$re->tenantId);
						}
					  return true;	
					}catch(Exception $e){
						echo $e->getMessage();
					}	
				}else
				return false;
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}else
		return false;
	}
	
	public function getMultipleTenants($tenants) {
	if($tenants){
			   $db = Zend_Db_Table::getDefaultAdapter();                
			   $select = $db->select()
						  ->from(array('tn' => 'tenant'), array('id', 'tenantName'));
			   $select = $select->where("tn.id in ($tenants)");
			   $select = $select->where('tn.status=?','1');
			   $res = $db->fetchAll( $select );   
			   
				return ($res && sizeof($res)>0)? $res : false ;
			}else
			   return false;
	}
	
	public function getTenantByNameDuplicate($tenantName,$buildId, $orderby){
			if($tenantName!='' && !empty($tenantName)){
				$select=$this->select()->where("tenantName LIKE '%".$tenantName."%'") ;
				if($buildId!=''){$select=$select->where('buildingId=?',$buildId);}
				$select = $select->order(array($orderby)) 
				->limit(1);			
				$res=$this->fetchAll($select);
				return ($res && sizeof($res)>0)? $res->toArray() : false;
			}
			
		}
	public function getChildTenantByUserId($userId) {
		if($userId !='') {
                    
			$select=$this->select()->where("userId = ?",$userId);
			$res=$this->fetchAll($select);
			return ($res && sizeof($res)>0)? $res->toArray() : false;
		}
	
	}
        
        public function getTenantNameById($tenantId) {
		if($tenantId !='') {
                    $db = Zend_Db_Table::getDefaultAdapter(); 
                    $select = $db->select()
                        ->from(array('tn' => 'tenant'), array('tenantName','id', 'tenant_number','buildingId'));
                    $select=$select->where("tn.id = ?",$tenantId);
					
                    $res = $db->fetchAll( $select ); 
                    return ($res && sizeof($res)>0)? $res : false ;
		}else{
                    return false;
		}	
	}
	public function getTenantByBId($uid, $search, $tenantorder){
		 
		 $db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db->select()
                         ->from(array('tn'=>'tenant'), array('tenantName','id', 'tenant_number','buildingId'))
                         ->joinLeft(array('cat' => 'coi_au_tenant'),'cat.tenant_Id = tn.id',array('cat.*'))
                         ->joinLeft(array('u' => 'users'),'u.uid = tn.userId',array('email'))
                         ->where('tn.buildingId=?',$uid)
                         ->where('tn.remove_status=0');
			
                   if(!empty($search)){
                      $select = $select->where("tn.tenantName like '".$search['search_value']."%'");  
                    }
					
			       if($tenantorder!='') {   
			   	       $select = $select->order("tn.tenantName $tenantorder"); 
			       } else {
				        $select = $select->order('tn.tenantName ASC');
			       }
			 
         $res=$db->fetchAll($select);

        return ($res && sizeof($res)>0)? $res : false ;               
	}
    
    function getTenantCoiByBId($uid,$tenantId){

        $db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db->select()
                         ->from(array('tn'=>'tenant'), array('tenantName','id', 'tenant_number','buildingId'))
                         ->joinLeft(array('cat' => 'coi_au_tenant'),'cat.tenant_Id = tn.id',array('cat.*'))
                         ->joinLeft(array('u' => 'users'),'u.uid = tn.userId',array('email'))
                         ->where('tn.buildingId=?',$uid)
                         ->where('tn.userId=?',$tenantId)
                         ->where('tn.remove_status=0');	                  
			 
                    $res=$db->fetchAll($select);

                  return ($res && sizeof($res)>0)? $res : false ;  
    }

    public function getAllTenantsByBuildingId($buildId, $role_id,$tenantId){
        
        $db = Zend_Db_Table::getDefaultAdapter();   
        $select = $db->select()
        ->from(array('u'=>'users'))
        ->joinLeft(array('tu'=>'tenantusers'),'tu.userId = u.uid',array('uid'=>'u.uid','firstName'=>'u.firstName','lastName'=>'u.lastName','phonenumber'=>'u.phoneNumber','email'=>'u.email','role_id'=>'u.role_id','note_notification'=>'u.note_notification','status'=>'u.status'))
        ->joinLeft(array('t'=>'tenant'),'t.id = tu.tenantId',array('cc_enable'=>'tu.cc_enable','send_as'=>'tu.send_as','complete_notification'=>'tu.complete_notification'))
        ->where('t.buildingId=?',$buildId)
        ->where('tu.tenantId=?',$tenantId)
        ->where('u.role_id=?',$role_id)
        ->where('u.remove_status=0'); 
            //->where('t.remove_status=1 OR t.userStatus = 1 OR t.history_remove=0');    
            $select = $select->order(array('t.tenantName ASC'));
            $matchjobRes=$db->fetchAll($select);
    
        return ($matchjobRes && sizeof($matchjobRes)>0)? $matchjobRes : false ;  
    }
   
}	

?>
