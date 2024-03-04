<?php 
class Model_Access extends Zend_Db_Table_Abstract {

   protected $_name = 'location_access';
   public $_errorMessage='';
   

	/* Get all access info */
	public function getcompany()
	{
		//$select=$this->select()->where('status=?',1) ;
		 $data = array(2,3,4,5,6);
		$this->select();	
		$select=$this->select()->where('role IN(?)', $data)->order('location_id ASC')->order('role ASC');
		$res=$this->fetchAll($select);
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
			
	}
	

	function checkAccess($role, $location_id)
	{
		try{
	
		$select=$this->select('id')->where('role=?',$role)->where('location_id=?',$location_id);				
		$res=$this->fetchRow($select);
		return ($res && sizeof($res)>0)? true : false ;	
		   
		}catch(Exception $e)	{	
		//echo $e->getMessage();die;
		return false;
		}
	
	}

	function updateAccess($data)
	{
		$where = array(
		    'role = ?' => $data['role'],
		    'location_id = ?' => $data['location_id']
		);
		$dataSet = array(
			'is_access' => $data['is_access'],
		    'is_read' => $data['is_read'],
		    'is_write' => $data['is_write']
		);

       	try{
    		$this->update($dataSet,$where);
    		return true;
    	}catch(Exception $e){
    		return false;
    	}
	}
	
	function addAccess($data){
		try{
			return $this->insert($data);
		}catch(Exception $e){
			return false;
		}
	}

	public function getUserAccess($role){
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
	         		   ->from(array('b'=>'location_access'), array('is_access'=>'b.is_access','is_read'=>'b.is_read','is_write'=>'b.is_write','location_id'=>'b.location_id'))
	         		   ->joinLeft(array('l'=>'location'),'l.id = b.location_id',array('name'=>'l.name'))
	         		   ->where('b.role  = ?',$role);

		$res=$db->fetchAll($select);
		return $res;
    
	}

	public function getUserAccessForModule($role,$location_id){
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
	         		   ->from(array('b'=>'location_access'), array('is_access'=>'b.is_access','is_read'=>'b.is_read','is_write'=>'b.is_write'))
	         		   ->joinLeft(array('l'=>'location'),'l.id = b.location_id',array('name'=>'l.name'))
	         		   ->where('b.role  = ?',$role)
	         		   ->where('b.location_id  = ?',$location_id);

		$res=$db->fetchRow($select);
		return $res;
    
	}

	
}
