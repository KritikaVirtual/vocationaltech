<?php
/**
 * Description of Vendor
 *
 * @author brijesh
 */
class Model_Vendor extends Zend_Db_Table_Abstract {

   protected $_name = 'vendor';   
   protected $_tab_role = 'vendor';  
   public $_errorMessage='';
   
   /* Get all vendor list */
    public function getVendor($vid = "") {       
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
		->from(array('ve' => 'vendor'))
		->joinLeft(array('st' => 'states'),'st.state_code = ve.state_code',array('st.state as statename'))
		->joinLeft(array('sr' => 'services'),'sr.sid = ve.services',array('sr.service as servicename'))
		->joinLeft(array('cn' => 'contact_type'),'cn.cid = ve.contact_type',array('cn.contact as contactname'));
        if(!empty($vid)){
            $select = $select->where( 'vid = ? ', $vid );
        }
        $select = $select->where( 'remove_status = ? ', '0' );  //echo $select; die;
        $res = $db->fetchAll( $select );
        $res=json_encode($res);
		$res=json_decode($res, true);
        return ($res && sizeof($res)>0)? $res : false ;

    }
    
    /***********Get Vendor List by building Id *********/ 
    
    public function getVendorByBidRecovery($bid,$order,$dir,$search = array()) {       
        if(!empty($bid)){
			$orderBy = $order.' '.$dir;
            $select = $this->select();        
            $select = $select->where( 'buildingId = ? ', $bid );
            if(isset($search['search_by']) && $search['search_by']=='company_name'){
				$select = $select->where( "company_name Like'%$search[search_value]%'" );
			}
			
			if(isset($search['search_by']) && $search['search_by']=='first_name'){
				$select = $select->where( "first_name Like'%$search[search_value]%'" );
			}
			
			if(isset($search['search_by']) && $search['search_by']=='phone_number'){
				$select = $select->where( "phone_number Like'%$search[search_value]%' OR cell_number Like'%$search[search_value]%'" );
			}
			
			if(isset($search['search_by']) && $search['search_by']=='email'){
				$select = $select->where( "email Like'%$search[search_value]%'" );
			}  
			
			if(isset($search['search_by']) && $search['search_by']=='services'){
				$select = $select->where( "services = '$search[search_value]'" );
			} 
			
			if(isset($search['search_by']) && $search['search_by']=='account_number'){
				$select = $select->where( "account_number = '$search[search_value]'" );
			}
			$select = $select->order(array($orderBy));   		
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;

    }
    
    public function getVendorByBid($bid,$order,$dir,$search = array()) {       
        if(!empty($bid)){
			$orderBy = $order.' '.$dir;
            $select = $this->select();        
            $select = $select->where( 'buildingId = ? ', $bid ); 
            $select = $select->where( 'remove_status = ? ', '0' );
            if(isset($search['search_by']) && $search['search_by']=='company_name'){
				$select = $select->where( "company_name Like'%$search[search_value]%'" );
			}
			
			if(isset($search['search_by']) && $search['search_by']=='first_name'){
				$select = $select->where( "first_name Like'%$search[search_value]%'" );
			}
			
			if(isset($search['search_by']) && $search['search_by']=='phone_number'){
				$select = $select->where( "phone_number Like'%$search[search_value]%' OR cell_number Like'%$search[search_value]%'" );
			}
			
			if(isset($search['search_by']) && $search['search_by']=='email'){
				$select = $select->where( "email Like'%$search[search_value]%'" );
			}  
			
			if(isset($search['search_by']) && $search['search_by']=='services'){
				$select = $select->where( "services = '$search[search_value]'" );
			} 
			
			if(isset($search['search_by']) && $search['search_by']=='account_number'){
				$select = $select->where( "account_number = '$search[search_value]'" );
			}
			$select = $select->order(array($orderBy));   		
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;

    }
    
    public function getVendorListByBid($bid){
		if(!empty($bid)){
			 $select = $this->select();        
             $select = $select->where( 'buildingId = ? ', $bid ); 
             $select = $select->where( 'status = ? ', '1' );
             $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
		}else
		return false;
	}
    
         /* Save Vendor */
    public function insertVendor($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Vendor */
    public function updateVendor($data, $vid) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('vid = ?', $vid);	
        unset($data['vid']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function checkVendorByName($vName,$building, $vid=''){
	   $select=$this->select()->where('company_name = ?', $vName);
	   $select = $select->where('buildingId = ?', $building);
	   if(!empty($vid)){
		   $select = $select->where('vid <>?', $vid);
	   }				
       $res=$this->fetchAll($select);
       return ($res && sizeof($res)>0)? $res->toArray() : false ;
   }
   
   
   public function checkVendorByEmail($email,$vid=''){
	   $select=$this->select()->where('email = ?', $email);
	   if(!empty($vid)){
		   $select = $select->where('vid <>?', $vid);
	   }	  			
       $res=$this->fetchAll($select);
       return ($res && sizeof($res)>0)? $res->toArray() : false ;
   }
   
   public function deleteVendor($vid){
	    $where = $this->getAdapter()->quoteInto('vid = ?', $vid);	       
        try {
            $this->update(array('remove_status'=>'1'),$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }
   }
   
   public function getVendorListByBuild($bId){
	   if(!empty($bid)){			
            $select = $this->select()->from('vendor',array('vid','company_name'));        
            $select = $select->where( 'buildingId = ? ', $bid );            
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false; 
   }
   
   public function deleteVendorByBId($bId){		
		if(!empty($bId)){
			try{
				$vendorList = $this->getVendorListByBuild($bId);
				if($vendorList){
					$vcModel = new Model_VendorContact();
					foreach($vendorList as $vl){
						$deleteVendor = $vcModel->deleteVendorByVId($vl['vid']);
					    $this->delete('vid = '.$vl['vid']);
					 }  
					return true;
				}else
				return false;
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}else
		 return false;
	}
	
	public function materialVendorDetail($vendorListing) {
	   if(!empty($vendorListing)) {
	        try {
			$select=$this->select();
			$select=$select->where('vid in ('.implode(",", $vendorListing).')');
			$res=$this->fetchall($select);
			return ($res && sizeof($res)>0)? $res->toArray():false;
			} catch(Exception $e) {
			    echo $e->getMessage();
			}
		}
	}
	
	public function getVendorByCompany($cust_id,$bid) {
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
				 ->from(array('bs' => 'vendor'), array('vid','company_name'))
				 ->joinInner(array('b' => 'buildings'), 'bs.buildingId = b.build_id', array('build_id'))
				 ->where('b.cust_id = ?', $cust_id)
				 ->where('bs.global_template = ?', '1')
				 ->where('bs.buildingId != ?', $bid)
				 ->where('bs.status = ?', '1');	  				 
			$res = 	 $db->fetchAll($select); 
        return ($res && sizeof($res)>0)? $res : false ;
	
	}
  public function getVendorByCBId($uid, $search, $vendororder){
		 
		 $db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db->select()
                         ->from(array('vd'=>'vendor'), array('vid','company_name', 'account_number','buildingId','email'))
                         ->joinLeft(array('cat' => 'coi_au_tenant'),'cat.tenant_Id = vd.vid AND cat.coi_au_Ten_or_Vendor="'.V.'"', array('cat.*'))
                         ->where('vd.buildingId=?',$uid)
                         ->where('vd.remove_status=0');
						 //->where('cat.coi_au_Ten_or_Vendor=?', 'V');
			
                   if(!empty($search)){
                      $select = $select->where("vd.company_name like '".$search['search_value']."%'");  
                    }
			       if($vendororder!='') {   
			              $select = $select->order("vd.company_name $vendororder"); 
		           } else {
				         $select = $select->order('vd.company_name ASC');
			       }
			 
         $res=$db->fetchAll($select);

        return ($res && sizeof($res)>0)? $res : false ;               
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
	
	public function getVendorNameById($vendorId) {
		if($vendorId !='') {
                    $db = Zend_Db_Table::getDefaultAdapter(); 
                    $select = $db->select()
                        ->from(array('vd'=>'vendor'), array('vid','company_name', 'account_number','buildingId'));
                    $select=$select->where("vd.vid = ?",$vendorId);
					
                    $res = $db->fetchAll( $select ); 
                    return ($res && sizeof($res)>0)? $res : false ;
		}else{
                    return false;
		}	
	} 

	public function getvendorById($uId){
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
				 ->from(array('v' => 'vendor'), array('*'))				  				 
				 ->where('v.vid = ?', $uId);
			$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;		
	}
}
