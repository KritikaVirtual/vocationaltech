<?php
/**
 * Description of Work Order Parameter
 *
 * @author brijesh
 */
class Model_WoParameter extends Zend_Db_Table_Abstract {

   protected $_name = 'wo_parameter';   
   protected $_tab_role = 'wo_parameter';
   public $_errorMessage='';
   
   /* Get all work order parameter list */
    public function getWoParameter($wpId = "") {       
        
        $select = $this->select();
        if(!empty($wpId)){
            $select = $select->where( 'wpId = ? ', $wpId );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    }
    
    /***********Get work order parameter List by building Id *********/ 
    
    public function getWoParameterByBid($bid) {       
        if(!empty($bid)){			
            $select = $this->select();        
            $select = $select->where( 'building = ? ', $bid );	    
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;

    }
    
         /* Save work order parameter */
    public function insertWoParameter($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update work order parameter */
    public function updateWoParameter($data, $wpId) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('wpId = ?', $wpId);	
        unset($data['wpId']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }    
   
   
   /* Delete work order parameter */
   public function deleteWoParameter($wpId){
	    $where = $this->getAdapter()->quoteInto('wpId = ?', $wpId);	       
        try {
            $this->delete($where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }
   }
   
}
