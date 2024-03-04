<?php
/**
 * Description of Model_User_Building_Module
 *
 * @author Gurubaksh
 */
class Model_UserAccess extends Zend_Db_Table_Abstract {

   protected $_name = 'user_access';   
   protected $_tab_role = 'user_access';   
   public $_errorMessage='';
   
   public function insertUserAccess($data='') {
	 try{	
			if($data != '') {
				$this->_errorMessage=""; 	
				return $this->insert($data);
			}			
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }   
   }
   
   public function isUserAccessExist($user_id='', $location_id='') {
		try {
			$select = $this->select();
			if($user_id != '') {
				$select = $select->where('user_id=?',$user_id);
			}
			if($location_id != '') {
				$select = $select->where('location_id=?',$location_id);
			}
			$res = $this->fetchAll($select);
			  return ($res && sizeof($res)>0)? $res->toArray() : false ;
			
		} catch(Exception $e) {
			echo $e->getMessage();die;
		}
   }
   
   public function updateUserAccess($data='', $user_id='', $location_id='') {
		try{
			if($data !='' && $user_id !='' && $location_id != ''){
				$where = array();
				$where[] = $this->getAdapter()->quoteInto('user_id = ?', $user_id);		
				$where[] = $this->getAdapter()->quoteInto('location_id = ?', $location_id);
				try {
					$this->update($data, $where);
					return true;
				}catch(Exception $e){    		
					echo $e->getMessage(); die();
				}
			}
		} catch(Exception $e) {
			echo $e->getMessage(); die;
		}
   
   }
   
   public function getUserCustomAccess($user_id='', $location_id='') {
	   try {
				$select = $this->select();
				if($user_id != '') {
					$select = $select->where('user_id=?',$user_id);
				}
				if($location_id != '') {
					$select = $select->where('location_id=?',$location_id);
				}
				$res = $this->fetchAll($select);
				  return ($res && sizeof($res)>0)? $res : false ;
				
			} catch(Exception $e) {
				echo $e->getMessage();die;
			}
   
   }
   
   public function removeUserCustomAccess($user_id='') {
       if($user_id!='') {
            try {
                $this->delete('user_id = '.$user_id);
		return true;
            } catch (Exception $ex) {
                return false;
            }
        }
   }
}
