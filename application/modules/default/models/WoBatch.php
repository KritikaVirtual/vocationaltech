<?php
/**
 * This class relate to the WoBatch database table
 *
 * @author Gurubaksh Singh
 */
class Model_WoBatch extends Zend_Db_Table_Abstract {

   protected $_name = 'wo_batch';   
   protected $_tab_role = 'wo_batch';  
   public $_errorMessage='';
   
    public function getMaxBatchNumber($bid, $tuid){
        if(!empty($bid)){
	        $select = $this->select()
            ->from(array('wob' => 'wo_batch'),array(new Zend_Db_Expr('MAX(batch_number) as maxwnum')))
            ->where( 'building_id = ? ', $bid );
			$row = $this->fetchRow($select);
		    if(!$row){
			   return false;
			}else{
				$tData = $row->toArray();
				return $tData ['maxwnum'];
			}
        } else
        return false;
		  
	  }
	  
	public function updateBatch($data,$woId) {
		 $this->_errorMessage=""; 			
        try{ 
			if(isset($woId) && !empty($woId)){ 
			    $where = $this->getAdapter()->quoteInto('batchid = ?', $woId);
			    $this->update($data,$where);				   	
			    return true;
		   } else {
		        return false;		
		   }
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
   
	public function generateBatch($data) {
	    try{            
            $this->_errorMessage=""; 			
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }      
	}
	
 /**
  *
  * Created By- Gurubaksh Singh
  *
  * This function show the listing of already created batches.
  *
  * @param string $bid stores the selected building id 
  * @param string $tuid stores the selected tenant id
  *
  * @return batch list
  */
    public function showBatch($bid, $tuid=null) {
	    try {
		    $this->_errorMessage="";
			$select = $this->select()
            ->where( 'building_id = ? ', $bid );
			if($tuid!='' & $tuid!='null') {
			    $select->where( 'tenant_id = ? ', $tuid );
			}
			$res = $this->fetchAll($select);
			return ($res && sizeof($res)>0)? $res->toArray() : false ;
		} catch(Exception $e){	
            echo $e->getMessage();die;
        }  
	}
	
  /**
  *
  * Created By- Gurubaksh Singh
  *
  * This function show the listing of already created batches.
  *
  * @param string $buildingid stores the selected building id 
  * @param string $batchid stores the selected batch Number 
  *
  * @return work orderlist according to batch 
  */
	
	public function loadBatches($batchid, $buildingid) {
	    try {
		    $this->_errorMessage="";
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('wo' => 'work_order'))
			->joinInner(array('t' => 'tenant'),'t.id = wo.tenant',array('tenantName','tenantContact'))
            ->joinLeft(array('bu'=>'buildings'),'bu.build_id = wo.building',array('buildingName'))
            ->joinLeft(array('cat'=>'category'),'cat.cat_id = wo.category',array('categoryName','prioritySchedule'))
			->where( 'wo.building =? ', $buildingid)
			->where( 'wo.wo_batch =? ', $batchid);
			$res=  $db->fetchAll($select);
			return ($res && sizeof($res)>0)?$res : false ;
		} catch(Exception $e) {
		      echo $e->getMessage(); die;
	    }
	}
	
	/**
  *
  * Created By- Gurubaksh Singh
  *
  * This function return the all work order of those are not exiting in batch and they are billable.
  *
  * @return List of work order
  */
	
	
	public function batchListWorkorder($tuid, $buildingid) {
	    try {
		    $this->_errorMessage="";
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('wo' => 'work_order'),array('wo.woId', 'wo.wo_number'))
			->joinLeft(array('wop'=>'work_order_update'),'wop.wo_id = wo.woId AND wop.current_update=1',array('wop.wo_status', 'wop.internal_note', 'wop.wo_request','created_date'=>'wop.created_at','updated_date'=>'wop.updated_at'))
			->where( 'building =? ', $buildingid)
			->where( 'tenant =? ', $tuid)
			->where('wop.billable_opt=?',1)
			->where('wo.wo_batch=?',0);
			$res=  $db->fetchAll($select);
			return ($res && sizeof($res)>0)?$res : false ;
		} catch(Exception $e) {
		      echo $e->getMessage(); die;
	    }
	}
	
	  
}
