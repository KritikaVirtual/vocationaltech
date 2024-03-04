<?php 
class Model_TenantOption extends Zend_Db_Table_Abstract {

   protected $_name = 'au_tenant_options';   
   protected $_tab_role = 'au_tenant_options';   
   public $_errorMessage='';
   
    

    /* Save user/client */
    public function insertTenantOption($data) {	
      
        try{
           
            $this->_errorMessage="";   	   	
        
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }

    public function checkMutiOptionByBuidingID($tenentdata){
        $select=$this->select()->where('tenantName = ?', $tenentdata['UserID']);
        $select = $select->where('buildingId = ?', $tenentdata['BuidlingID']);
        $select = $select->where('buildingId = ?', $tenentdata['TenantID']);
        				
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
    }

    /**
     * isTenantExist check user exist or not
     * 
     * @param String $username
     * 
     * @return Array (it tenant exist reture tenant detail other wise empty array)
     * @author : Anuj Kumar
     */
    public function isTenantExist($username) {
        $select=$this->select()->where('userName = ?', $username);				
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;	
    }

   
}	

?>
