<?php 
class Model_EmailGroup extends Zend_Db_Table_Abstract {

   protected $_name = 'email_group'; 
   protected $_name_quote = 'email_group'; 
   protected $_primary = 'id';
   public $_errorMessage='';

	
	/** 
	*get groups
	*/
	public function getGroups($id='')
	{
		$select = $this->select();
        
        if(!empty($id)){
            $select = $select->where( 'id = ? ', $id );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
		
	}
	
	public function validateGroupName($group_name,$building,$gid=''){
		if($group_name!='' && $building!=''){
			$select = $this->select();        
			$select = $select->where( 'group_name = ? ', $group_name );    
			$select = $select->where( 'building_id = ? ', $building );
			if(!empty($gid)){
               $select = $select->where( 'id != ? ', $gid );
            }
			$res = $this->fetchAll( $select );        
			return ($res && sizeof($res)>0)? $res->toArray() : false ;
	   }else
	   return false;
	}
    
    public function getGroupIds($ids)
	{
		$select = $this->select();
        
        if(!empty($ids)){
            $select = $select->where( "id in ($ids) " );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
		
	}
	
	public function saveEmailGroup($data){
		try{
			return $this->insert($data);		
		}catch(Exception $e){	
			echo $e->getMessage();die;
			return false;
		}
	
	}

	public function updateGroup($data,$id){
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('id = ?', $id);    
       
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){           
            echo $e->getMessage(); die();
        }   
    }

	public function get_email_group_by_building_id($bid,$is_default=1){

		$select = $this->select()->where( 'building_id = ? ', $bid )->where('status=?','1');		
		if($is_default==0) 	$select = $select->where('is_default=?','0');
		$res = $this->fetchAll( $select );        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
	}
	
    public function get_email_group_by_building_id_PM($bid,$is_default=1){

		$select = $this->select()->where( 'building_id = ? ', $bid )->where('status=?','1')->where('group_name=?','PM-WorkOrders');		
		if($is_default==0) $select = $select->where('is_default=?','0');
		$res = $this->fetchAll( $select );        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
	}
    
        public function get_email_group_by_building_id_searching($bid,$is_default=1,$search=array()){
            try{ 
             $db = Zend_Db_Table::getDefaultAdapter();
             $select = $db->select()
                            ->from(array('eg' => 'email_group'))
                            ->where( 'eg.building_id = ? ', $bid )->where('eg.status=?','1');
                if($is_default==0) 
                      $select = $select->where('eg.is_default=?','0');
               // print_R($search);
	        if(!empty($search)){
                    $select = $select->joinInner('email_group_users as egu','egu.group_id=eg.id',array());
                    $select = $select->joinInner('users as u','egu.user_id=u.uid',array());
                    
                   $select = $select->where("".$search['search_by']." like '".$search['search_value']."%'");
                }   
            $res = $db->fetchAssoc( $select );

            return ($res && sizeof($res)>0)? $res : false ;
		}catch(Exception $e){
			return false;
		}
                
        }

        public function get_default_email_building_id($bid){

		$select = $this->select()->where( 'building_id = ? ', $bid )->where('status=?','1');
		
		$select = $select->where('is_default=?','1');

		$res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

	}
	
	public function getCategoryByGroupId($groupId){
		try{
		$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
                      ->from(array('eg' => 'email_group'))
                      ->joinInner(array('cat' => 'category'),"FIND_IN_SET($groupId,cat.send_email)",array('cat.cat_id'))                      
                      ->where('eg.id =?', $groupId);                                        
                                           
            $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
		}catch(Exception $e){
			return false;
		}
    }
	
	
	public function deleteGroupByBId($bId){
		if(!empty($bId)){
			try{
				$select = $this->select()->where( 'building_id = ? ', $bId );
		        $resgroup = $this->fetchAll( $select );
		        if($resgroup){
					$guModel = new Model_EmailGroupUsers();
					foreach($resgroup as $rg){
						$deletGUser = $guModel->deleteGroupUser($rg->id);
						$this->delete('id ='.$rg->id);
					}
				}
			   return true;	
			}catch(Exception $e){
				echo $e->getMessage();
			}
			
		}else
		return false;
	} 
	
	public function getCompeleteNotficationUsers($bId) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$emailUsers = new Model_EmailGroupUsers();
			//replaced data type string to array by Dadhi Vallabh.
			$usersArray=array();
			// (UnComment this from privious status. durgesh Chaubey)
			$select = $db->select()
			->from(array('wog' => 'email_group'),array('id'))
		   ->where( 'building_id = ? ', $bId )->where('complete_notification=?','1')->where('status=?','1');
		    $groups = $db->fetchAll( $select );
			foreach ($groups as $value) {
				// $select1 = $db->select()
			   // ->from(array('wou' => 'email_group_users'),array('id'))
				//->where( 'group_id = ? ', $value->id );
				// $usersArray[] = $db->fetchAll( $select1 );
				$usersArray[]=$emailUsers->getGroupUsers($value->id);
			}
			return ($usersArray && sizeof($usersArray)>0)? $usersArray : false ;
			//if(!empty($usersArray)){
		    //    return $usersArray ;
			//}
			// (Comment this from privious status. durgesh Chaubey)
	}
	
	public function getMultipleGroup($send_email) {
		if($send_email){
			   $db = Zend_Db_Table::getDefaultAdapter();                
			   $select = $db->select()
						  ->from(array('eg' => 'email_group'), array('id', 'group_name'));
			   $select = $select->where("eg.id in ($send_email)");
			   $select = $select->where('eg.status=?','1');
			   $res = $db->fetchAll( $select );      
				return ($res && sizeof($res)>0)? $res : false ;
			}else
			   return false;
	}
	
	public function getGruopByNameDuplicate($group_name,$buildId, $orderby){
			if($group_name!='' && !empty($group_name)){
				$select=$this->select()->where("group_name LIKE '%".$group_name."%'") ;
				if($buildId!=''){$select=$select->where('building_id=?',$buildId);}
				$select = $select->order(array($orderby)) 
				->limit(1);	
			
				$res=$this->fetchAll($select);
				return ($res && sizeof($res)>0)? $res->toArray() : false;
			}
	}
	
}	

