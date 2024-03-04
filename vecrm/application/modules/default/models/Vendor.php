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
        
        $select = $this->select();
        if(!empty($vid)){
            $select = $select->where( 'vid = ? ', $vid );
        }
        $select = $select->where( 'remove_status = ? ', '0' );
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    }
    
    /***********Get Vendor List by building Id *********/ 
    
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
   
}
