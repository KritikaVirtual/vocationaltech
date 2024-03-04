<?php
/**
 * Description of services
 *
 * @author brijesh
 */
class Model_Services extends Zend_Db_Table_Abstract {

   protected $_name = 'services';   
   protected $_tab_role = 'services';  
   public $_errorMessage='';
   
   /* Get all services list */
    public function getServices($sid = "") {       
        
        $select = $this->select();
        if(!empty($sid)){
            $select = $select->where( 'sid = ? ', $sid );
        }
        
        $select = $select->order(array('service ASC'));
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
         /* Save Service */
    public function insertServices($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Services */
    public function updateServices($data, $sid) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('sid = ?', $sid);	
        unset($data['sid']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    
    public function checkServiceByName($service,$sid=""){
	   $select=$this->select()->where('service = ?', $service);	   
	   if(!empty($sid)){
		   $select = $select->where('sid <>?', $sid);
	   }				
       $res=$this->fetchAll($select);
       return ($res && sizeof($res)>0)? $res->toArray() : false ;
	}
        
   
}
