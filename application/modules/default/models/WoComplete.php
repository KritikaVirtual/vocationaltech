<?php
/**
 * Description of Work Order Complete
 *
 * @author brijesh
 */
class Model_WoComplete extends Zend_Db_Table_Abstract {

   protected $_name = 'wo_complete';   
   protected $_tab_role = 'wo_complete';
   public $_errorMessage='';
   
   /* Get all Work Order Complete list */
    public function getWoComplete($wnId = "") {       
        
        $select = $this->select();
        if(!empty($wcId)){
            $select = $select->where( 'wcId = ? ', $wcId );
        }      
       
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
         /* Save Work Order Complete */
    public function insertWoComplete($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Work Order Complete */
    public function updateWoComplete($data, $wcId) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('wcId = ?', $wcId);	
        unset($data['wcId']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getWoCompleteByWoId($woId){
		if(!empty($woId)){
		    $select = $this->select();        
            $select = $select->where( 'woId = ? ', $woId );       
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
    
    
    /********delete Work Order Complete********/	
	public function deleteWoComplete($wcId){
		if(!empty($wcId) && $wnId !=0){
			   try{
				  $this->delete('wcId = '.$wcId);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
        
   
}
