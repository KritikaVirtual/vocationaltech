<?php 
class Model_Building extends Zend_Db_Table_Abstract {

   protected $_name = 'buildings';   
   //protected $_tab_role = 'buildings';   
   public $_errorMessage='';
   

	/* Get all users/staff detail */
	public function getbuilding($companyID='')
	{
		$select=$this->select();
		if($companyID){$select = $this->select()->where('cust_id=?',$companyID);}	
		$res=$this->fetchAll($select);
		
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
			
	} 
	
	public function getCompanyBuilding($companyID)
	{
		
		if($companyID){
			
			$select = $this->select()->where('cust_id=?',$companyID);
			$select = $select->where('status=?','1');
		    $res=$this->fetchAll($select);
			
			//$sql = $select->__toString();			
			$res=$this->fetchAll($select);		
		    return ($res && sizeof($res)>0)? $res->toArray() : false ;
			}else{
				return false;
			}	
		
			
	} 
	
	public function getbuildingbyid($buildingID='')
	{
	//	$select=$this->select()->where('status=?','1') ;
		if($buildingID){$select = $this->select()->where('build_id=?',$buildingID);}	
		$res=$this->fetchAll($select);
		
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
			
	} 
	 	
	function addBuilding($datacontent)
	{
		try{
	
		return $this->insert($datacontent);		
		   
		}catch(Exception $e)	{	
		echo $e->getMessage();die;
		return false;
		}
	
	}
	
		/*delete clients */
	public function deleteBuilding($id){   		
       	try{
    		$this->delete('build_id = '.$id);
    		return true;
    	}catch(Exception $e){
    		return false;
    	}
	}
	
	/* Update  users detail by ID */		
	public function updateBuilding($data,$id){
		$this->_errorMessage="";
    	$where = $this->getAdapter()->quoteInto('build_id = ?', $id);	
	//	unset($data['id']);
   	   	//Check is user is duplicate or not
		
       	try{
    		$this->update($data,$where);
    		return true;
    	}catch(Exception $e){    		
			echo $e->getMessage();die;
			return false;
    	}	
	
	
	}
	/** ========================================================================= */
	
	
	public function getBuildingList($buildingIds = array()) {

            $select = $this->select()->from('buildings',array('build_id','cust_id','buildingName','user_id'))->where('status=?','1') ;

            

            if( !empty ( $buildingIds ) ) {

                $select = $select->where( 'build_id in ('.implode(",", $buildingIds).")");

            }	

            $res = $this->fetchAll($select);



            return ($res && sizeof($res)>0)? $res->toArray() : false ;

        }
	
       
       public function deleteCompanyBuildings($companyID){
		   if(!empty($companyID) && $companyID !=0){
			   try{
				  $this->delete('cust_id = '.$companyID);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	   }
       
       
       /*
        * check building by name
        */
        public function getBuildingByName($buildingName,$cmpId,$id=''){
			if($buildingName!='' && !empty($buildingName)){
				$select=$this->select()->where('buildingName=?',$buildingName) ;
				if($id!=''){$select=$select->where('build_id<>?',$id);}
				$select=$select->where('cust_id=?',$cmpId);
				$res=$this->fetchAll($select);
				return ($res && sizeof($res)>0)? $res->toArray() : false;
			}
			
		}	
		
     /*
        * get building by cost center
        */
     public function getbuildingByCostCenter($costCenter)
	 {	
		$select = $this->select()->where('uniqueCostCenter=?',$costCenter);	
		$res=$this->fetchAll($select);		
		return ($res && sizeof($res)>0)? $res->toArray() : false ;			
	 } 
	 
}
