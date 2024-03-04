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

	public function get_email_group_by_building_id($bid){

		$select = $this->select()->where( 'building_id = ? ', $bid )->where('status=?','1');
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
	 
	
}	

