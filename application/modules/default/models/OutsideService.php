<?php
/**
 * Description of Outside Service
 *
 * @author brijesh
 */
class Model_OutsideService extends Zend_Db_Table_Abstract {

   protected $_name = 'outside_service';   
   protected $_tab_role = 'outside_service'; 
   public $_errorMessage='';
   
   /* Get all Outside Service list */
    public function getOutsideService($osId = "") {       
        
        $select = $this->select();
        if(!empty($osId)){
            $select = $select->where( 'osId = ? ', $osId );
        }      
       
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
         /* Save Outside Service */
    public function insertOutsideService($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Outside Service */
    public function updateOutsideService($data, $osId) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('osId = ?', $osId);	
        unset($data['osId']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getOutsideServiceByWoId($woId){
		if(!empty($woId)){
		     $db = Zend_Db_Table::getDefaultAdapter(); 				 
			 $select = $db->select()
				 ->from(array('os' => 'outside_service'), array('osId','woId','vendor','job_cost','job_description','markup','tax'))
				 ->joinLeft(array('v' => 'vendor'), 'v.vid = os.vendor', array('vid','company_name'))				 
				 ->where('os.woId = ?', $woId);				 			 				 
			$res = 	 $db->fetchAll($select);
			return ($res && sizeof($res)>0)? $res : false ;
        }else
        return false;
	}
    
    
    /********delete Outside Service********/	
	public function deleteOutsideService($osId){
		if(!empty($osId) && $osId !=0){
			   try{
				  $this->delete('osId = '.$osId);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
        
   
}

