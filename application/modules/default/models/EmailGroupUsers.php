<?php 
class Model_EmailGroupUsers extends Zend_Db_Table_Abstract {

   protected $_name = 'email_group_users'; 
   protected $_name_quote = 'email_group_users'; 
   protected $_primary = 'id';
   public $_errorMessage='';

	
	/** 
	*author:Anuj Kumar
	*/
	public function getUsers($id="")
	{
		$select = $this->select();
        
        if(!empty($id)){
            $select = $select->where( 'id = ? ', $id );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
		
	}


	public function getUsersByGid($gid="")
	{
		$select = $this->select();
        
        if(!empty($gid)){
            $select = $select->where( 'group_id = ? ', $gid );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
		
	}

	public function saveEmailGroupUsers($data,$group_id){

		$email_group_users_data['group_id'] = $group_id;
			
		$email_group_users_data['send_as'] = $data['send_as'];
		$email_group_users_data['days_of_week'] = $data['days_of_the_week'];
		$email_group_users_data['complete_notification'] = $data['complete_notification'];
		$email_group_users_data['updated_date'] = date('Y-m-d H:i:s');
		$i=0;
		foreach ($data['to_select_list'] as $key => $value) {

			$email_group_users_data['user_id'] = $value;

			$res[$i++] = $this->insert($email_group_users_data);
		}

		if($i == count($res)){
			return true;
		}
		else
			return false;
	}

	public function saveGroupUsers($data){

		$res = $this->insert($data);
		
		if($res)
			return true;
		else
			return false;
	}


	public function getGroupUsers($gid){

      	$db = Zend_Db_Table::getDefaultAdapter(); 

      	if(!empty($gid)){

            $select = $db->select()
                         ->from(array('e'=>'email_group_users'))
                         ->join(array('u'=>'users'),'u.uid = e.user_id',array('uid'=>'u.uid','firstname'=>'u.firstName','lastname'=>'u.lastName','email'=>'u.email','role_id'=>'u.role_id','status'=>'u.status','note_notification'=>'u.note_notification','alert_notification' => 'u.alert_notification'))
                         ->joinLeft(array('s'=>'send_as'),'s.sid = e.send_as',array('sid'=>'s.sid','send_as_title'=>'s.title'))
                         ->joinLeft(array('w'=>'week_days'),'w.wdID = e.days_of_week',array('wdID'=>'w.wdID','week_days'=>'w.title'))
                         ->where('e.group_id=?',$gid)
                         ->order('u.firstName ASC');
             $res = $db->fetchAll($select);
            return ($res && sizeof($res)>0)? $res : false ; 
            //return $res;             (Comment this from privious status. durgesh Chaubey)
        }else
         return false;

      	 
	}

	
	public function deleteEmailUser($id,$gid){
		if(!empty($id) && $id !=0){
			$condition = array(
			    'group_id = ?' => $gid,
			    'user_id = ?' => $id
			);
			try {
				$this->delete($condition);
				  return true;
			}catch(Exception $e){    		
				echo $e->getMessage(); die();
			}
		}
	}


	public function updateEmailGroupUser($data,$id){
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('id = ?', $id);    
       
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){           
            echo $e->getMessage(); die();
        }   
   }
   
   public function updateByGroup($data,$groupId){
	    $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto("group_id = ?",$groupId);    
       
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){           
            echo $e->getMessage(); die();
        }
   }
   public function getGroupByUserId($user_id){
	    $db = Zend_Db_Table::getDefaultAdapter(); 

      	if(!empty($user_id)){

            $select = $db->select()
                         ->from(array('e'=>'email_group_users'))
                         ->join(array('eg'=>'email_group'),'eg.id = e.group_id')                         
                         ->where('e.user_id=?',$user_id)
                         ->where('eg.status=?','1');                        
        }

      	$res = $db->fetchAll($select);
        
        return ($res && sizeof($res)>0)? $res : false ;  
   }
   
   public function getGroupIdByUser($user_id){
	   $select = $this->select();
        
        if(!empty($user_id)){
            $select = $select->where( 'user_id = ? ', $user_id );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
   }
   
   public function deleteGroupUser($gId){
	   if(!empty($gId)){
		   try{
			   $this->delete('group_id = '.$gId);
			   return true;
		   }catch(Exception $e){
			   echo $e->getMessage();
		   }
	   }else
	   return false;
	   
   }
   
   
   public function Adduserindistributiongroup($uid,$gid){
              try{                  
                $data = array("group_id"=>$gid,"user_id"=>$uid,"send_as"=>1,"days_of_week"=>1); 
                return $this->insert($data);		
            } catch(Exception $e)	{	
                echo $e->getMessage();
                die;
            }
          }
 
	
}	
