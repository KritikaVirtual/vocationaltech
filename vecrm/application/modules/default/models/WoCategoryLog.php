<?php
/**
 * Description of Work order Category Log
 *
 * @author Brijesh Kumar
 */
class Model_WoCategoryLog extends Zend_Db_Table_Abstract {

   protected $_name = 'wo_category_log';   
   protected $_tab_role = 'wo_category_log';  
   public $_errorMessage='';
   
  public function insertCategoryLog($data){				
        try{            
            $this->_errorMessage="";    	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
     
    
    
    public function getWoCatLog($woId){
		if(!empty($woId)){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
                      ->from(array('wcl' => 'wo_category_log'))
                      ->joinInner(array('cat' => 'category'),'wcl.category = cat.cat_id',array('categoryName','cat_id'))
                      ->joinLeft(array('us'=>'users'),'wcl.user_id = us.uid',array('firstName','lastName','email'))                                            
                      ->where('wcl.woId=?',$woId);                  
            $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
        }else
        return false;
	}
	
	   
        
   
}
