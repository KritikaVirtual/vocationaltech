<?php
/**
 * Description of Work order Category Log
 *
 * @author Brijesh Kumar
 */
class Model_WoHistoryLog extends Zend_Db_Table_Abstract {

   protected $_name = 'wo_history_log';   
   protected $_tab_role = 'wo_history_log';
   public $_errorMessage='';
   
  public function insertHistoryLog($data){				
        try{            
            $this->_errorMessage="";    	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
     
    
    
    public function getHistoryLog($whId = "") {        
        
        if(!empty($whId)){
            $select = $select->where( 'whId = ? ', $whId );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
    public function getWoHistoryLog($woId){
		if(!empty($woId)){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
                      ->from(array('whl' => 'wo_history_log'))
                      //->joinInner(array('cat' => 'category'),'wcl.category = cat.cat_id',array('categoryName','cat_id'))
                      ->joinLeft(array('us'=>'users'),'whl.user_id = us.uid',array('firstName','lastName','email'))
                      ->joinLeft(array('r'=>'role'),'r.roleID = us.role_id',array('role_title'=>'r.title'))                                            
                      ->where('whl.woId=?',$woId);                  
            $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
        }else
        return false;
	}
	
	   
        
   
}

