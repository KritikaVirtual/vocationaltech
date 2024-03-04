<?php
/**
 * Description of Bill Rate
 *
 * @author brijesh
 */
class Model_BillRate extends Zend_Db_Table_Abstract {

   protected $_name = 'bill_rate';   
   protected $_tab_role = 'bill_rate';
   public $_errorMessage='';
   
   /* Get all Bill Rate list */
    public function getBillRate($brid = "") {       
        
        $select = $this->select();
        if(!empty($brid)){
            $select = $select->where( 'brid = ? ', $brid );
        }      
       
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
         /* Save Bill Rate */
    public function insertBillRate($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Bill Rate */
    public function updateBillRate($data, $brid) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('brid = ?', $brid);	
        unset($data['brid']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    /* Update Bill Rate by building */
    public function updateBillRateByBId($data, $bId) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('building = ?', $bId);	
        unset($data['brid']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getBillRateByBId($bId, $order=''){
		if(!empty($bId)){
		    $select = $this->select();        
            $select = $select->where( 'building = ? ', $bId );           
            $select = $select->order($order);       
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
	
	public function getActiveBillRateByBId($bId){
		if(!empty($bId)){
		    $select = $this->select();        
            $select = $select->where( 'building = ? ', $bId );
            $select = $select->where( 'status = ? ', '1' );                   
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
    
    
    /********delete Bill Rate********/	
	public function deleteBillRate($brid){
		if(!empty($brid) && $brid !=0){
			   try{
				  $this->delete('brid = '.$brid);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
    
    public function checkRateName($rname,$build,$brid=''){
		if(!empty($rname) && !empty($build)){
		    $select = $this->select();
		    $select = $select->where( 'rate_name = ? ', $rname );
		    if($brid!='') {
				$select = $select->where( 'brid != ? ', $brid );
			}       
            $select = $select->where( 'building = ? ', $build );       
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
	
	
	public function getRateChargeByCompany($cust_id,$bid){
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
				 ->from(array('br' => 'bill_rate'), array('brid','rate_name','description'))
				 ->joinInner(array('b' => 'buildings'), 'br.building = b.build_id', array('build_id'))
				 ->where('b.cust_id = ?', $cust_id)
				 ->where('br.global_template = ?', '1')
				 ->where('br.building != ?', $bid)
				 ->where('br.status = ?', '1');			 				 
			$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;
	}    
   
}
