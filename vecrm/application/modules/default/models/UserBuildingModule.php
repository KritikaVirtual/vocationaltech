<?php
/**
 * Description of Model_User_Building_Module
 *
 * @author brijesh
 */
class Model_UserBuildingModule extends Zend_Db_Table_Abstract {

   protected $_name = 'user_building_module_access';   
   protected $_tab_role = 'user_building_module_access';   
   public $_errorMessage='';
   
   /* Save user building module */
    public function updateBuildingModule($data) {
        try{         
            //$this->deleteData($data[0]['user_id']);
            foreach($data as $key => $building) { 
				$buildData = $this->getUserBuild($building['user_id'],$building['building_id']);
				if(!$buildData)              
                  $this->insert($building);
                 else{
					 $where = array();
					 $where[] = "user_id = '".$building['user_id']."'";
                     $where[] = "building_id = '".$building['building_id']."'";
					 $this->update($building,$where);
				 } 
            }
        } catch(Exception $e) {	
            return false;
        }
    }
    
    /* Update user building module */
    public function updateUserBuildingModule($data) {
        try{         
            $this->deleteData($data[0]['user_id']);
            foreach($data as $key => $building) {
				  $this->insert($building); 				
            }
        } catch(Exception $e) {	
            return false;
        }
    }


    public function deleteData($id){   		
       	try {
            $this->delete('user_id = '.$id);
            return true;
    	} catch(Exception $e){
            return false;
    	}
    }
    
    public function getUserBuildingIds($user_id){
		//$cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
		$select = $this->select()->where('user_id=?',$user_id);		
		$res = $this->fetchAll($select);
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
	}
	
	public function getUserBuild($user_id,$build_id){
		$select = $this->select()->where('user_id=?',$user_id);
		$select = $select->where('building_id=?',$build_id);		
		$res = $this->fetchAll($select);
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
	}
	
	public function deteleUserBuild($user_id){
		if(!empty($user_id) && $user_id !=0){
			   try{
				  $this->delete('user_id = '.$user_id);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
	
	public function deleteBuildingUser($userId,$buildId){
		if(!empty($userId) && !empty($buildId)){
			$condition = array(
				'user_id = ' . $userId,
				'building_id = ' . $buildId
			);
			try{
				  $this->delete($condition);
				  return true;
				}catch(Exception $e){
					return false;
				}   
	  }   
	}
	
}
