<?php
/**
 * Description of Setting
 *
 * @author brijesh
 */
class Model_SystemDefault extends Zend_Db_Table_Abstract {

   protected $_name = 'system_default';   
   //protected $_tab_role = 'system_default';
   public $_errorMessage='';
   
   
    public function getSystemDefault() {
        $select = $this->select();      
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
     public function insertSystemDefault($data) {
        try{
	
			return $this->insert($data);		
			   
		   }catch(Exception $e)	{
			
			return false;
		   }

    } 
    
    public function updateSystemDefault($data,$sd_id){
		if(!empty($sd_id)){
			$where = $this->getAdapter()->quoteInto('sd_id = ?', $sd_id);
			try{
				$this->update($data,$where);
				return true;
			}catch(Exception $e){
				return false;
			}
		}
	}
        
   
}
