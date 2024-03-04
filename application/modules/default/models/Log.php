<?php 
class Model_Log extends Zend_Db_Table_Abstract {

   protected $_name = 'email_log';   
   protected $_tab_role = 'email_log';   
   public $_errorMessage='';
   
    /* Save email log */
    public function insertLog($data) {				
        try{
            $this->_errorMessage="";   	   	            		
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }

    public function selectLogByEmail($email) {        
        $select=$this->select()->where('email=?',$email);
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
    }
}	

?>
