<?php
/**
 * Description of Build Service
 *
 * @author brijesh
 */
class Model_BuildService extends Zend_Db_Table_Abstract {

   protected $_name = 'build_service';   
   protected $_tab_role = 'build_service';
   public $_errorMessage='';
   
   /* Get all Build Service list */
    public function getBuildService($bsid = "") {       
        
        $select = $this->select();
        if(!empty($bsid)){
            $select = $select->where( 'bsid = ? ', $bsid );
        }      
       
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
         /* Save Build Service */
    public function insertBuildService($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Build Service */
    public function updateBuildService($data, $bsid) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('bsid = ?', $bsid);	
        unset($data['bsid']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getBuildServiceByBId($bId, $order=''){
		if(!empty($bId)){
		    $select = $this->select();        
            $select = $select->where( 'building = ? ', $bId );
            $select = $select->order($order);       
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
    
    public function getActiveBuildServiceByBId($bId){
		if(!empty($bId)){
		    $select = $this->select();        
            $select = $select->where( 'building = ? ', $bId );
            $select = $select->where( 'status = ? ', '1' ); 
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
    
    /********delete Build Service********/	
	public function deleteBuildService($bsid){
		if(!empty($bsid) && $bsid !=0){
			   try{
				  $this->delete('bsid = '.$bsid);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
    
    public function checkServiceName($sname,$build,$bsid=''){
		if(!empty($sname) && !empty($build)){
		    $select = $this->select();
		    $select = $select->where( 'service_name = ? ', $sname );
		    if($bsid!='') {
				$select = $select->where( 'bsid != ? ', $bsid );
			}       
            $select = $select->where( 'building = ? ', $build );       
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
	
	
	public function getBuildServiceByCompany($cust_id,$bid){
		$db = Zend_Db_Table::getDefaultAdapter(); 
		$select = $db->select()
				 ->from(array('bs' => 'build_service'), array('bsid','service_name'))
				 ->joinInner(array('b' => 'buildings'), 'bs.building = b.build_id', array('build_id'))
				 ->where('b.cust_id = ?', $cust_id)
				 ->where('bs.global_template = ?', '1')
				 ->where('bs.building != ?', $bid)
				 ->where('bs.status = ?', '1');			 				 
			$res = 	 $db->fetchAll($select);
        return ($res && sizeof($res)>0)? $res : false ;
	}    
   
}
