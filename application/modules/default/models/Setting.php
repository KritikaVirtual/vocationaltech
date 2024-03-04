<?php
/**
 * Description of Setting
 *
 * @author brijesh
 */
class Model_Setting extends Zend_Db_Table_Abstract {

   protected $_name = 'setting';   
   protected $_tab_role = 'setting';
   public $_errorMessage='';
   
   
    public function getSetting() {
        $select = $this->select();      
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
     public function insertSetting($data) {
        try{
	
			return $this->insert($data);		
			   
		   }catch(Exception $e)	{
			
			return false;
		   }

    } 
    
    public function updateSetting($data,$sid){
		if(!empty($sid)){
			$where = $this->getAdapter()->quoteInto('setting_id = ?', $sid);
			try{
				$this->update($data,$where);
				return true;
			}catch(Exception $e){
				return false;
			}
		}
	}
        
   
}
