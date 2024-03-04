<?php
/**
 * Description of Building Service
 *
 * @author brijesh
 */
class Model_BuildingService extends Zend_Db_Table_Abstract {

   protected $_name = 'building_service';   
   protected $_tab_role = 'building_service'; 
   public $_errorMessage='';
   
   /* Get all Building Service list */
    public function getBuildingService($bsId = "") {       
        
        $select = $this->select();
        if(!empty($bsId)){
            $select = $select->where( 'bsId = ? ', $bsId );
        }      
       
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
         /* Save Building Service */
    public function insertBuildingService($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Building Service */
    public function updateBuildingService($data, $bsId) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('bsId = ?', $bsId);	
        unset($data['bsId']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getBuildingServiceByWoId($woId){
		if(!empty($woId)){
		    $db = Zend_Db_Table::getDefaultAdapter(); 				 
			$select = $db->select()
				 ->from(array('bs' => 'building_service'), array('bsId','woId','service','charge','amount_requested','comment'))
				 ->joinLeft(array('bserv' => 'build_service'), 'bserv.bsid = bs.service', array('service_name','unit_measure','minimum'))				 
				 ->where('bs.woId = ?', $woId);				 			 				 
			$res = 	 $db->fetchAll($select);
			return ($res && sizeof($res)>0)? $res : false ;
        }else
        return false;
	}
    
    
    /********delete Building Service********/	
	public function deleteBuildingService($bsId){
		if(!empty($bsId) && $bsId !=0){
			   try{
				  $this->delete('bsId = '.$bsId);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
        
   
}

