<?php
/**
 * Description of Send As
 *
 * @author Brijesh
 */
class Model_SendAs extends Zend_Db_Table_Abstract { 

   protected $_name = 'send_as';   
   protected $_tab_role = 'send_as';   
   public $_errorMessage='';
   
   /* Get all send as option */
    public function getSendAs($sid = "") {
        $select = $this->select()->where('status=?','1') ;
        
        if(!empty($roleId)){
            $select = $select->where( 'sid = ? ', $sid );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    }
    
    
    /** 
    *author:Anuj Kumar
    */
    public function getData()
    {
      $select=$this->select();
      $res=$this->fetchAll($select);
      return ($res && sizeof($res)>0)? $res->toArray() : false ;

     }
        
   
}
