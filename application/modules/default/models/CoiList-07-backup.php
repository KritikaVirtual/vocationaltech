<?php
/**
 * Description of Build Service
 *
 * @author brijesh
 */
class Model_CoiList extends Zend_Db_Table_Abstract {

   protected $_name = 'coi_au_tenant';   
   protected $_tab_role = 'build_service';
   public $_errorMessage='';
   
   /* Get all Build Service list */
    public function getcoiList($cid = "") {       
        $select = $this->select();
        if(!empty($cid)){
            $select = $select->where( 'coi_au_tenant_id = ? ', $cid );
        }      
       
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    public function geteditcoiList($cid = "") {       
        $db = Zend_Db_Table::getDefaultAdapter();
        if(!empty($cid)){
			$select = $db->select()
                         ->from(array('cat'=>'coi_au_tenant'), array('cat.*'))
                         ->joinLeft(array('tn' => 'tenant'),'tn.id = cat.tenant_Id',array('tn.tenantName'))
                         ->where('cat.coi_au_tenant_id=?',$cid);
            $select = $select->where( 'coi_au_tenant_id = ? ', $cid );
        }      
       
        $res = $db->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res : false ;

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
    public function updateBuildService($data, $cid) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('coi_au_tenant_id = ?', $cid);	
        unset($data['coi_au_tenant_id']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getBuildServiceByBId($bId, $order='',$search=array()){
		if(!empty($bId)){
		    $select = $this->select();        
                    $select = $select->where( 'building = ? ', $bId );
                    if(!empty($search)){
                      $select = $select->where("".$search['search_by']." like '".$search['search_value']."%'");  
                    }
                    $select = $select->order($order);  
                    $res = $this->fetchAll( $select );        
                    return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else{
            return false;
        }
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
    
    /********delete Coi List********/	
	public function deletecoiList($cid){
		if(!empty($cid) && $cid !=0){
			   try{
				  $this->delete('coi_au_tenant_id = '.$cid);
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
	
	public function deleteBuildServiceByBId($bId){		
		if(!empty($bId)){
			try{
				$this->delete('building = '.$bId);
				return true;
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}else
		 return false;
	}
public function getUniqueCostByBId($bId) {
		if($bId !='') {
                    $db = Zend_Db_Table::getDefaultAdapter(); 
                    $select = $db->select()
                        ->from(array('build' => 'buildings'), array('uniqueCostCenter'));
                    $select=$select->where("build.build_id = ?",$bId);
					
                    $res = $db->fetchAll( $select ); 
                    return ($res && sizeof($res)>0)? $res : false ;
		}else{
                    return false;
		}	
	} 
// Haseeb code
public function AllonemonthExpire($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();
		$fullDate=date("Y-m-d");
		$fullDate1=time();
		$select = $db->select()
                         ->from(array('tn'=>'tenant'), array('tenantName','id', 'tenant_number','buildingId'))
                         ->joinLeft(array('cat' => 'coi_au_tenant'),'cat.tenant_Id = tn.id',array('cat.*'))
                         ->where('cat.building_id=?',$uid)
						 ->where('cat.coi_au_date_to >= ?', $fullDate)
						 ->where('cat.coi_au_date_to < ?', date('Y-m-d', strtotime('+30 day', $fullDate1)));
			
		
         $res=$db->fetchAll($select);

        return ($res && sizeof($res)>0)? $res : false ; 		
	}
public function AlltwomonthExpire($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();
		$fullDate=date("Y-m-d");
		$fullDate1=time();
		$select = $db->select()
                         ->from(array('tn'=>'tenant'), array('tenantName','id', 'tenant_number','buildingId'))
                         ->joinLeft(array('cat' => 'coi_au_tenant'),'cat.tenant_Id = tn.id',array('cat.*'))
                         ->where('cat.building_id=?',$uid)
						 ->where('cat.coi_au_date_to >= ?', $fullDate)
						 ->where('cat.coi_au_date_to > ?', date('Y-m-d', strtotime('+30 day', $fullDate1)))
						 ->where('cat.coi_au_date_to < ?', date('Y-m-d', strtotime('+60 day', $fullDate1)));
			
		
         $res=$db->fetchAll($select);

        return ($res && sizeof($res)>0)? $res : false ; 		
	}
public function AllthreemonthExpire($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();
		$fullDate=date("Y-m-d");
		$fullDate1=time();
		$select = $db->select()
                         ->from(array('tn'=>'tenant'), array('tenantName','id', 'tenant_number','buildingId'))
                         ->joinLeft(array('cat' => 'coi_au_tenant'),'cat.tenant_Id = tn.id',array('cat.*'))
                         ->where('cat.building_id=?',$uid)
						 ->where('cat.coi_au_date_to >= ?', $fullDate)
						 ->where('cat.coi_au_date_to > ?', date('Y-m-d', strtotime('+60 day', $fullDate1)))
						 ->where('cat.coi_au_date_to < ?', date('Y-m-d', strtotime('+90 day', $fullDate1)));
			
		 
         $res=$db->fetchAll($select);

        return ($res && sizeof($res)>0)? $res : false ; 		
	}
public function AllafterThreemonthExpire($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();
		$fullDate=date("Y-m-d");
		$fullDate1=time();
		$select = $db->select()
                         ->from(array('tn'=>'tenant'), array('tenantName','id', 'tenant_number','buildingId'))
                         ->joinLeft(array('cat' => 'coi_au_tenant'),'cat.tenant_Id = tn.id',array('cat.*'))
                         ->where('cat.building_id=?',$uid)
						 ->where('cat.coi_au_date_to >= ?', $fullDate)
						 ->where('cat.coi_au_date_to > ?', date('Y-m-d', strtotime('+90 day', $fullDate1)));
						 
			
		 
         $res=$db->fetchAll($select);

        return ($res && sizeof($res)>0)? $res : false ; 		
	}
public function AllAgoExpire($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();
		$fullDate=date("Y-m-d");
		$select = $db->select()
                         ->from(array('tn'=>'tenant'), array('tenantName','id', 'tenant_number','buildingId'))
                         ->joinLeft(array('cat' => 'coi_au_tenant'),'cat.tenant_Id = tn.id',array('cat.*'))
                         ->where('cat.building_id=?',$uid)
						 ->where('cat.coi_au_date_to < ?', $fullDate);
			
			 
         $res=$db->fetchAll($select);

        return ($res && sizeof($res)>0)? $res : false ; 		
	}

public function AllNoCOI($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();
		 $select = $db->select()
                          ->from(array('tn'=>'tenant'), array('tenantName','id', 'tenant_number','buildingId'))
						  ->where('ID NOT IN (SELECT tenant_Id FROM coi_au_tenant)')
                          ->where('tn.buildingId=?',$uid);
			
			/* $sql = $select->__toString();
			echo "<pre>";
			print_r($sql);die; */ 
         $res=$db->fetchAll($select);

        return ($res && sizeof($res)>0)? $res : false ; 		
	}


public function countOneMonth($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();		
		$sql="SELECT count(*) as total FROM coi_au_tenant WHERE `building_id` = ".$uid." AND `coi_au_date_to`>= CURRENT_DATE() AND`coi_au_date_to` < CURRENT_DATE() + INTERVAL 30 DAY";
		$db->query($sql);
		$res = $db->fetchRow($sql);		
		return $res;		
	}
public function countTwoMonth($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();		
		$sql="SELECT count(*) as total FROM coi_au_tenant WHERE `building_id` = ".$uid." AND `coi_au_date_to`>= CURRENT_DATE() AND `coi_au_date_to` > CURRENT_DATE() + INTERVAL 30 DAY AND `coi_au_date_to` < CURRENT_DATE() + INTERVAL 60 DAY";
		$db->query($sql);
		$res = $db->fetchRow($sql);		
		return $res;		
	}
public function countThreeMonth($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();		
		$sql="SELECT count(*) as total FROM coi_au_tenant WHERE `building_id` = ".$uid." AND `coi_au_date_to`>= CURRENT_DATE() AND `coi_au_date_to` > CURRENT_DATE() + INTERVAL 60 DAY AND `coi_au_date_to` < CURRENT_DATE() + INTERVAL 90 DAY";
		$db->query($sql);
		$res = $db->fetchRow($sql);		
		return $res;		
	}
public function countAfterThreeMonth($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();		
		$sql="SELECT count(*) as total FROM coi_au_tenant WHERE `building_id` = ".$uid." AND `coi_au_date_to`>= CURRENT_DATE() AND `coi_au_date_to` > CURRENT_DATE() + INTERVAL 90 DAY ";
		$db->query($sql);
		$res = $db->fetchRow($sql);		
		return $res;		
	}
public function countAgoAll($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();		
		$sql="SELECT count(*) as total FROM `coi_au_tenant` WHERE building_id=".$uid."  AND `coi_au_date_to` < CURRENT_DATE()";
		$db->query($sql);
		$res = $db->fetchRow($sql);		
		return $res;		
	}
	public function countNoCOI($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();		
		$sql="SELECT count(*) as total FROM tenant WHERE ID NOT IN (SELECT tenant_Id FROM coi_au_tenant) and `buildingId`=".$uid;
		$db->query($sql);
		$res = $db->fetchRow($sql);		
		return $res;		
	}
public function countAll($uid){		
		$db = Zend_Db_Table::getDefaultAdapter();		
		$sql="SELECT count(*) as total FROM `tenant` WHERE buildingId=".$uid;
		$db->query($sql);
		$res = $db->fetchRow($sql);		
		return $res;		
	}
}
