<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Vendor
 *
 * @author brijesh
 */
class Model_VendorContact extends Zend_Db_Table_Abstract {

   protected $_name = 'vendor_contact';   
   protected $_tab_role = 'vendor_contact';  
   public $_errorMessage='';
   
   /* Get all vendor list */
    public function getVendorContact($vcId = "") {       
        
        $select = $this->select();
        if(!empty($vcId)){
            $select = $select->where( 'vcId = ? ', $vcId );
        }
        $select = $select->where( 'remove_status = ? ', '0' );
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
    
         /* Save Vendor */
    public function insertVendorContact($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Vendor */
    public function updateVendorContact($data, $vcId) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('vcId = ?', $vcId);	
        unset($data['vcId']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    
    /********get Contact By Vendor Id ******/
    public function getContactByVid($vid){
		 if(!empty($vid)){
		      $select = $this->select();        
              $select = $select->where( 'vid = ? ', $vid ); 
              $select = $select->where( 'remove_status = ? ', '0' );        
              $res = $this->fetchAll( $select );        
              return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
    
   public function checkVContactByEmail($email,$vcId =""){
	   $select=$this->select()->where('email = ?', $email);
	   if(!empty($vcId)){
			$select = $select->where('vcId <> ?', $vcId);
		}	  			
       $res=$this->fetchAll($select);
       return ($res && sizeof($res)>0)? $res->toArray() : false ;
   }
   
   public function deleteVendorContact($vcId){
	    $where = $this->getAdapter()->quoteInto('vcId = ?', $vcId);	       
        try {
            $this->update(array('remove_status'=>'1'),$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }
   }
}
