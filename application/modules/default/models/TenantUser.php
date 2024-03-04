<?php 
class Model_TenantUser extends Zend_Db_Table_Abstract {

   protected $_name = 'tenantusers';   
   protected $_tab_role = 'tenantusers';   
   public $_errorMessage='';
   
    

    /* Save user/client */
    public function insertTenantUser($data) {				
        try{
           
            $this->_errorMessage="";   	   	
            //$data['password']=md5($data['uPass']);
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /******Update tenant user data *****/
    public function updateTenantUser($data,$id){
        $this->_errorMessage="";        
        $where = $this->getAdapter()->quoteInto('id = ?', $id);	       
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getTenantUsers($tenantId, $buildingId = '', $recoverFlag=false){
		$db = Zend_Db_Table::getDefaultAdapter(); 
		if($recoverFlag){
			$select = $db->select()
				 ->from(array('u' => 'users'), array('uid','userName','firstName','lastName','email','role_id','phoneNumber','phoneExt','Title','status','note_notification'))
				 ->joinInner(array('tu' => 'tenantusers'), 'u.uid = tu.userId', array('id','tenantId','cc_enable','send_as','complete_notification','main_contact','suite_location'  ))
				 ->where('u.remove_status = ?', '1')				 
				 ->where('tu.tenantId = ?', $tenantId);
		}
		else{
			$select = $db->select()
				 ->from(array('u' => 'users'), array('uid','userName','firstName','lastName','email','role_id','phoneNumber','phoneExt','Title','status','note_notification'))
				 ->joinInner(array('tu' => 'tenantusers'), 'u.uid = tu.userId', array('id','tenantId','cc_enable','send_as','complete_notification','main_contact','suite_location','is_location_removed'))
				 ->where('u.remove_status = ?', '0')
				 ->where('tu.is_location_removed = ?', '0')	
				 ->where('tu.tenantId = ?', $tenantId);
			if($buildingId!=''){
				$select = $select->joinInner(array('ubm' => 'user_building_module_access'), 'u.uid = ubm.user_id')
				           ->where('ubm.building_id = ?', $buildingId);
			}
		}
                // ->where('cp.status = ?', '1');				 				 
		$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;
		
	}
	
	public function deleteTenantUserBytId($tId){
		if(!empty($tId) && $tId !=0){			   		
				try {
					$this->delete('tenantId = '.$tId);
				}catch(Exception $e){    		
					echo $e->getMessage(); die();
				}
			}
	}
    
    /******Tenant user info by user id******/
    
    public function getTenantUserById($uId){
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
				 ->from(array('u' => 'users'), array('uid','userName','firstName','lastName','email','role_id','phoneNumber','phoneExt','Title','status','note_notification','user_img'))
				 ->joinInner(array('tu' => 'tenantusers'), 'u.uid = tu.userId', array('id','tenantId','suite_location','cc_enable','send_as','complete_notification'))
				 ->where('u.remove_status = ?', '0')				 
				 ->where('u.uid = ?', $uId);
			$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;		
	}
	
	public function getTenantUserBuilding($uid){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				 ->from(array('t' => 'tenant'))
				 ->joinInner(array('tu' => 'tenantusers'), 't.id = tu.tenantId', array('id','tenantId','suite_location','cc_enable','send_as','complete_notification'))
				 ->where('t.remove_status = ?', '0')				 
				 ->where('tu.userId = ?', $uid);
		$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;
	}
	
	public function updateMainTenant($tuserId,$tId){ 
        $this->_errorMessage="";        
               
        try {
			$where = $this->getAdapter()->quoteInto('tenantId = ?', $tId);	
            $this->update(array('main_contact' => 0),$where);
			$where = $this->getAdapter()->quoteInto('userId = ?', $tuserId);
			$this->update(array('main_contact' => 1),$where);
			return true;
        } catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
		
    }

	public function getMutiluserInfo($data){

		$uid = $data['userId'];
		$tenantId = $data['tenantId'];
		$suite_location = $data['suite_location'];
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				 ->from(array('tu' => 'tenantusers'))	
				 ->where('tu.tenantId = ?', $tenantId)
				 ->where('tu.suite_location = ?', $suite_location)			 
				 ->where('tu.userId = ?', $uid);
		$res = 	 $db->fetchAll($select);
		//echo $select;
        return ($res && sizeof($res)>0)? $res : false ;
	}

   
}	
