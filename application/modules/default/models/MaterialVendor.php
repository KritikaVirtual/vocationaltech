<?php
/**
 * Description of Material Vendor
 *
 * @author brijesh
 */
class Model_MaterialVendor extends Zend_Db_Table_Abstract {

   protected $_name = 'material_vendor';   
   protected $_tab_role = 'material_vendor';  
   public $_errorMessage='';
   
   /* Get all vendor list */
    public function getMaterialVendor($mvId = "") {       
        
        $select = $this->select();
        if(!empty($mvId)){
            $select = $select->where( 'mvId = ? ', $mvId );
        }
        $select = $select->where( 'remove_status = ? ', '0' );
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
    
         /* Save Vendor */
    public function insertMaterialVendor($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Vendor */
    public function updateMaterialVendor($data, $mvId) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('mvId = ?', $mvId);	
        unset($data['mvId']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    
    /********get Contact By Vendor Id ******/
    public function getContactByMid($mid){
		 if(!empty($mid)){
		      $select = $this->select();        
              $select = $select->where( 'material = ? ', $mid ); 
              $select = $select->where( 'remove_status = ? ', '0' );        
              $res = $this->fetchAll( $select );        
              return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
    
   public function checkVContactByEmail($email,$mvId =""){
	   $select=$this->select()->where('email = ?', $email);
	   if(!empty($mvId)){
			$select = $select->where('mvId <> ?', $mvId);
		}	  			
       $res=$this->fetchAll($select);
       return ($res && sizeof($res)>0)? $res->toArray() : false ;
   }
   
   public function deleteVendorContact($mvId){
	    $where = $this->getAdapter()->quoteInto('mvId = ?', $mvId);	       
        try {
            $this->update(array('remove_status'=>'1'),$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }
   }
   
   public function deleteVendorByMId($mId){		
		if(!empty($mId)){
			try{
				$this->delete('material = '.$mId);
				return true;
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}else
		 return false;
	}
	
	public function checkVendorByMid($vid,$mid){
	   $select=$this->select()->where('material = ?', $mid);
	   $select=$select->where('vendor_id = ?', $vid);
	   $res=$this->fetchAll($select);
       return ($res && sizeof($res)>0)? $res->toArray() : false ;
   }
   public function deleteVendorByMidvid($vid,$mid){
	  if($mid!='' && $vid!=''){
			try{
				$this->delete(array('vendor_id = ?' => $vid, 'material = ?' => $mid) );
				return true;
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}else
		 return false;
   }
}
