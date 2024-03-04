<?php
/**
 * Description of Bill Labor
 *
 * @author brijesh
 */
class Model_BillLabor extends Zend_Db_Table_Abstract {

   protected $_name = 'bill_labor';   
   protected $_tab_role = 'bill_labor';
   public $_errorMessage='';
   
   /* Get all Bill Labor list */
    public function getBillLabor($blid = "") {       
        
        $select = $this->select();
        if(!empty($blid)){
            $select = $select->where( 'blid = ? ', $blid );
        }      
       
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
         /* Save Bill Labor */
    public function insertBillLabor($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Bill Labor */
    public function updateBillLabor($data, $blid) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('blid = ?', $blid);	
        unset($data['blid']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
     /* Update Bill Labor by building*/
    public function updateBillLaborByBId($data, $bId) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('building = ?', $bId);	
        unset($data['blid']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getBillLaborByBId($bId,$order=''){
		if(!empty($bId)){
		    $select = $this->select();        
            $select = $select->where( 'building = ? ', $bId ); 
            $select = $select->order($order);       
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
	
	public function getActiveBillLaborByBId($bId){
		if(!empty($bId)){
		    $select = $this->select();        
            $select = $select->where( 'building = ? ', $bId ); 
            $select = $select->where( 'status = ? ', '1' );     
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
    
    
    /********delete Bill Labor********/	
	public function deleteBillLabor($blid){
		if(!empty($blid) && $blid !=0){
			   try{
				  $this->delete('blid = '.$blid);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
	
	public function checkLaborDesc($desc,$build,$blid=''){
		if(!empty($desc) && !empty($build)){
		    $select = $this->select();
		    $select = $select->where( 'description = ? ', $desc );
		    if($blid!='') {
				$select = $select->where( 'blid != ? ', $blid );
			}       
            $select = $select->where( 'building = ? ', $build );       
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
	
	public function getLaborChargeByCompany($cust_id,$bid){
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
				 ->from(array('bl' => 'bill_labor'), array('blid','description'))
				 ->joinInner(array('b' => 'buildings'), 'bl.building = b.build_id', array('build_id'))
				 ->where('b.cust_id = ?', $cust_id)
				 ->where('bl.global_template = ?', '1')
				 ->where('bl.building != ?', $bid)
				 ->where('bl.status = ?', '1');			 				 
			$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;
	}
	
	public function  getAssignByBId($bId){
		if(!empty($bId)){
		    $select = $this->select()->from('bill_labor',array('assign_to'));        
            $select = $select->where( 'building = ? ', $bId ); 
            $select = $select->where( 'assign_to != ? ', '' );
            $select = $select->where( 'set_default != ? ', '1' );                    
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
        
   
}
