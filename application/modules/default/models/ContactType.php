<?php
/**
 * Description of cotact type
 *
 * @author brijesh
 */
class Model_ContactType extends Zend_Db_Table_Abstract {

   protected $_name = 'contact_type';   
   protected $_tab_role = 'contact_type';  
   public $_errorMessage='';
   
   /* Get all contact type list */
    public function getContactType($cid = "") {       
        
        $select = $this->select();
        if(!empty($cid)){
            $select = $select->where( 'cid = ? ', $cid );
        }
        $select = $select->order(array('contact ASC'));
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
    /* Get Building contact type list */
    public function getContactTypeByBuilding($bId) {       
        
        $select = $this->select();
        if(!empty($bId)){
            $select = $select->where( 'building = ? ', $bId );
            $select = $select->orwhere( 'building = ? ', '0' );
        }
        $select = $select->order(array('contact ASC'));
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    }
    
         /* Save Contact Type */
    public function insertContactType($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Contact Type */
    public function updateContactType($data, $cid) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('cid = ?', $cid);	
        unset($data['cid']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
        
   public function checkContactByName($contact,$bId, $cid=""){
	   $select=$this->select()->where('contact = ?', $contact);
	   $select = $select->where('building = ?', $bId);	   
	   if(!empty($cid)){
		   $select = $select->where('cid <>?', $cid);
	   }				
       $res=$this->fetchAll($select);
       return ($res && sizeof($res)>0)? $res->toArray() : false ;
	}
}
