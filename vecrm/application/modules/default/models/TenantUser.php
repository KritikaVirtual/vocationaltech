<?php 
class Model_TenantUser extends Zend_Db_Table_Abstract {

   protected $_name = 'tenantUsers';   
   protected $_tab_role = 'tenantUsers';   
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
				 ->from(array('u' => 'users'), array('uid','userName','firstName','lastName','email','role_id','phoneNumber','phoneExt','Title','status'))
				 ->joinInner(array('tu' => 'tenantUsers'), 'u.uid = tu.userId', array('tenantId','cc_enable','send_as','complete_notification'))
				 ->where('u.remove_status = ?', '1')				 
				 ->where('tu.tenantId = ?', $tenantId);
		}
		else{
			$select = $db->select()
				 ->from(array('u' => 'users'), array('uid','userName','firstName','lastName','email','role_id','phoneNumber','phoneExt','Title','status'))
				 ->joinInner(array('tu' => 'tenantUsers'), 'u.uid = tu.userId', array('tenantId','cc_enable','send_as','complete_notification'))
				 ->where('u.remove_status = ?', '0')				 
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
				 ->from(array('u' => 'users'), array('uid','userName','firstName','lastName','email','role_id','phoneNumber','phoneExt','Title','status'))
				 ->joinInner(array('tu' => 'tenantUsers'), 'u.uid = tu.userId', array('id','tenantId','suite_location','cc_enable','send_as','complete_notification'))
				 ->where('u.remove_status = ?', '0')				 
				 ->where('u.uid = ?', $uId);
				 						 				 
			$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;
		
	}
	
	public function getTenantUserBuilding($uid){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				 ->from(array('t' => 'tenant'))
				 ->joinInner(array('tu' => 'tenantUsers'), 't.id = tu.tenantId', array('id','tenantId','suite_location','cc_enable','send_as','complete_notification'))
				 ->where('t.remove_status = ?', '0')				 
				 ->where('tu.userId = ?', $uId);
		$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;
	}
   
}	
