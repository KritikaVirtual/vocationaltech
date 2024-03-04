<?php
/**
 * Description of Work Order Note
 *
 * @author brijesh
 */
class Model_WoFiles extends Zend_Db_Table_Abstract {

   protected $_name = 'wo_files';   
   protected $_tab_role = 'wo_files'; 
   public $_errorMessage='';
   
   /* Get all Work Order Note list */
    public function getWoFiles($wfId = "") {       
        
        $select = $this->select();
        if(!empty($wfId)){
            $select = $select->where( 'wfId = ? ', $wfId );
        }      
       
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
         /* Save Work Order Note */
    public function insertWoFile($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Work Order Note */
    public function updateWoFile($data, $wfId) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('wfId = ?', $wfId);	
        unset($data['wfId']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getWoFilesByWoId($woId){
		if(!empty($woId)){
		    $select = $this->select();        
            $select = $select->where( 'woId = ? ', $woId );       
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
    
    
    /********delete Work Order Note********/	
	public function deleteWoFile($wfId){
		if(!empty($wfId) && $wfId !=0){
			   try{
				  $this->delete('wfId = '.$wfId);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
        
   
}
