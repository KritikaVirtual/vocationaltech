<?php 
class Model_Account extends Zend_Db_Table_Abstract {

   protected $_name = 'company';   
   protected $_tab_role = 'company';   
   public $_errorMessage='';
   

	/* Get all users/staff detail */
	public function getcompany($companyID='')
	{
		$select = $this->select();//->where('status=?',1) ;
		if($companyID){$select = $select->where('cust_id=?',$companyID);}	
		$select = $select->order('cust_id DESC');
		$res=$this->fetchAll($select);
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
			
	}
	
	/* Get all company listing */
	public function getCompanyList($order,$dir)
	{
		$order = ($order=='acnum')?'corp_account_number':$order;
		$orderBy = $order.' '.$dir;
		$select = $this->select();		
		$select = $select->order($orderBy);
		$res = $this->fetchAll($select);
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
			
	}
	public function getCompanyByName($txtstr='')
	{
		$select=$this->select()->where('companyName like ?',"%$txtstr%");				
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;	
			
	}	
	
	/* Get all users/staff detail */
	public function validatecompanyName($companyName='')
	{
		$select=$this->select()->where('companyName=?',$companyName);
		$select=$select->where('status=?','1');
		$res=$this->fetchAll($select);
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
			
	} 
	public function validateaccountNumber($acconumber='')
	{
		$select=$this->select()->where('corp_account_number=?',$acconumber) ;
		$res=$this->fetchAll($select);
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
			
	} 
/* Get all users/staff detail */
	public function validatecompanyNameNotID($companyName='',$id='')
	{
		$select=$this->select()->where('companyName=?',$companyName) ;
		if($id!=''){$select=$select->where('cust_id<>?',$id) ;}
		$res=$this->fetchAll($select);
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
			
	} 
	public function validateaccountNumberNotID($acconumber='',$id='')
	{
		$select=$this->select()->where('corp_account_number=?',$acconumber) ;
		if($id!=''){$select=$select->where('cust_id<>?',$id) ;}
		$res=$this->fetchAll($select);
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
			
	}  	
	function addcompany($datacontent)
	{
		try{
	
		return $this->insert($datacontent);		
		   
		}catch(Exception $e)	{	
		//echo $e->getMessage();die;
		return false;
		}
	
	}
	function editcompany($data,$cid)
	{
		$where = $this->getAdapter()->quoteInto('cust_id = ?', $cid);
       	try{
    		$this->update($data,$where);
    		return true;
    	}catch(Exception $e){
    		return false;
    	}
	}
	function deletecompany($companyid)
	{
		try{
			if(!empty($companyid) && $companyid !=0){
				$builMod = new Model_Building();
				$delete_building = $builMod->deleteCompanyBuildings($companyid);
				if($delete_building){
					$userMod = new Model_User();
					$delete_user = $userMod->deleteCompanyUser($companyid);
					$this->delete('cust_id = '.$companyid);			
				    return true;
    		   }else{
				   return false;
			   }
		   }
    	}catch(Exception $e){
    		return false;
    	}
	}
	
	/** ========================================================================= */
	
	/* Get all users/staff detail */
	public function getUserById($id)
	{
		$select=$this->select()->where('cust_id=?',$id);			
		$res=$this->fetchRow($select);
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
			
	}  	
	
	/* Get all users/staff detail */
	public function getUsersExceptOne($role)
	{
		$select=$this->select()->where('role<>?',$role);
		$res=$this->fetchAll($select);
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
			
	}
	
	/* Update  users detail by ID */		
	public function updateUser($data,$id,$email=0){
		$this->_errorMessage="";
    	$where = $this->getAdapter()->quoteInto('id = ?', $id);	
		unset($data['id']);
   	   	//Check is user is duplicate or not
		if($email==0){
			if(!$this->checkExistingRecord($data['usr_nm'],$id)){
				 $this->_errorMessage .="* This user is already registered with us please try another .<br> ";
				return false;
			}    	
		}
       	try{
    		$this->update($data,$where);
    		return true;
    	}catch(Exception $e){    		
			echo $e->getMessage();die;
			return false;
    	}	
	
	
	}
	
	/* check existing user for save or update */	
   private function checkExistingRecord($email,$id=0){
   		
	   	$select=$this->select()->where('usr_nm=?',$email) ;

	   	if($id>0) $select=$select->where('id<>?',$id);	
		//echo $select;		
	   	$rs=$this->fetchAll($select);
	    
	   	return ($rs && sizeof($rs)>0) ? false:true;
   	
   } 	
   
   
	/* Save user/client */
	public function addUser($data) {
	  try{
	  
		$this->_errorMessage="";
   	   	//Check is uiser is duplicate or not
   	   	if(!$this->checkExistingRecord($data['usr_nm'])){
	   		$this->_errorMessage .="* This user name is already registered with us please try another .<br> ";
	   		return false;
	   	} 	  
		$data['sts']=1;
		$data['usr_pwd']=md5($data['password']);
		unset($data['password']) ;
		$data['created_date']=date('Y-m-d H:i:s');	

		return $this->insert($data);		
		   
	 }catch(Exception $e)	{	
		//echo $e->getMessage();die;
		return false;
	 }
	}
	
	
	 
	
	/* Update status f */
	public function setStatus($id,$status){
   		$where = $this->getAdapter()->quoteInto('id = ?', $id);
       	try{
    		$this->update(array('sts'=>$status),$where);
    		return true;
    	}catch(Exception $e){
    		return false;
    	}
	}
	
	/*delete clients */
	public function deleteUser($id){   		
       	try{
    		$this->delete('id = '.$id . " and role<>'" . md5('A')."'");
    		return true;
    	}catch(Exception $e){
    		return false;
    	}
	}	
	
	
	/* update password users */
	public function setPassword($data){
	
	
	     $where = $this->getAdapter()->quoteInto('id = ?', base64_decode($data['id']));		
 	
       	try{
    		$this->update(array('usr_pwd'=>md5(($data['password']))),$where);
    		return true;
    	}catch(Exception $e){    		
			 echo $e->getMessage();die;
			return false;
    	}	
	
	
	}
	public function findCompanyName($companyName){
   		
	   	$select=$this->select()->where('companyName=?',$companyName) ;
	   	$res=$this->fetchAll($select);
	    
	  return ($res && sizeof($res)>0)? $res->toArray() : false ;
   	
   } 
	public function findAccountNumber($accountNumber){
   		
	   	$select=$this->select()->where('corp_account_number=?',$accountNumber) ;
	   	$res=$this->fetchAll($select);
	    
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
   	
   } 


   /* Update status f */
	public function updateLogo($id,$comapny_logo){

   		$where = $this->getAdapter()->quoteInto('cust_id = ?', $id);
   		
       	try{
    		$this->update(array('company_logo'=>$comapny_logo),$where);
    		return true;
    	}catch(Exception $e){
    		return false;
    	}
	}
	
	/******Get Company detail by building Id ****/
	
	public function getCompanyByBuilding($buildId){
		  if($buildId){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
                      ->from(array('co' => 'company'))
                      ->joinInner(array('b' => 'buildings'),'b.cust_id = co.cust_id',array('buildingName','cust_id','address','address2','city','state','postalCode','phoneNumber'))
					  ->joinLeft(array('st' => 'states'),'st.state_code = b.state_code',array('st.state as statename'))
                      ->where('b.build_id=?',$buildId);
             $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ; 
		}       
	}
	
  /**
  *
  * Created By- Gurubaksh Singh
  *
  * This function get list of states.
  * 
  * @return list of states
  */
	public function getStates() {
	   $db = Zend_Db_Table::getDefaultAdapter();
	   $select=$db->select()
	   ->from(array('st'=>'states'));
	   $res =$db->fetchAll($select);
	   return ($res && sizeof($res)>0)? $res : false ; 
	}
	
  /**
  *
  * Created By- Gurubaksh Singh
  *
  * This function get state name by the state code.
  * 
  * @return state name
  */
	public function getStatesByCode($statecode) {
	   $db = Zend_Db_Table::getDefaultAdapter();
	   $select=$db->select()
	   ->from(array('st'=>'states'),array('st.state'))
	   ->where( 'st.state_code =? ', $statecode);
	   $res =$db->fetchAll($select);
	   return ($res && sizeof($res)>0)? $res : false ; 
	}
 
	
}	

