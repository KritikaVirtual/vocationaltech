<?php
/**
 * Description of Material Charge
 *
 * @author brijesh
 */
class Model_MaterialCharge extends Zend_Db_Table_Abstract {

   protected $_name = 'material_charge';   
   protected $_tab_role = 'material_charge'; 
   public $_errorMessage='';
   
   /* Get all Material Charge list */
    public function getMaterialCharge($mcId = "") {       
        
        $select = $this->select();
        if(!empty($mcId)){
            $select = $select->where( 'mcId = ? ', $mcId );
        }      
       
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
         /* Save Material Charge */
    public function insertMaterialCharge($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Material Charge */
    public function updateMaterialCharge($data, $mcId) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('mcId = ?', $mcId);	
        unset($data['mcId']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getMaterialChargeByWoId($woId){
		if(!empty($woId)){
		    $db = Zend_Db_Table::getDefaultAdapter(); 				 
			$select = $db->select()
				 ->from(array('mc' => 'material_charge'), array('mcId','woId','material_id','cost','quantity','markup','tax'))
				 ->joinLeft(array('m' => 'material'), 'm.mid = mc.material_id', array('mid','description'))				 
				 ->where('mc.woId = ?', $woId);				 			 				 
			$res = 	 $db->fetchAll($select);
			return ($res && sizeof($res)>0)? $res : false ;
        }else
        return false;
	}
    
    
    /********delete Material Charge********/	
	public function deleteMaterialCharge($mcId){
		if(!empty($mcId) && $mcId !=0){
			   try{
				  $this->delete('mcId = '.$mcId);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
        
   
}

