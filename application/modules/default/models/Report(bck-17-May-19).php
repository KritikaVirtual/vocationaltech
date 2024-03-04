<?php
/**
 * Description of Role
 *
 * @author ivtidai
 */
class Model_Report extends Zend_Db_Table_Abstract {

   protected $_name = 'report';   
   protected $_tab_role = 'report';   
   public $_errorMessage='';
   
    public function insertReport($data) 
	{
         try{            
            $this->_errorMessage="";    	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }

    } 
	
	public function updateReport($data, $rid)
	{
		$this->_errorMessage="";
		$where = $this->getAdapter()->quoteInto('rid = ?', $rid);	
		unset($data['rid']);
		try {
			$this->update($data,$where);
			return true;
		}catch(Exception $e){    		
			echo $e->getMessage(); die();
		}
	}
	
	
	public function getDashboardMenu() 
	{
	    $db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		                    ->from(array('dm' => 'dashboard_menu'))
							->where('status=?','1');
		$res = $db->fetchAll( $select );
        return ($res && sizeof($res)>0)? $res : false ;
	}
	
	
	public function getReport($accounts=null, $dashboard_menu=null) 
	{
		$db=Zend_Db_Table::getDefaultAdapter();
		try{
			$select = $db->select()
								->from(array('re' => 'report'))
								 ->joinLeft(array('com' => 'company'),'re.accounts = com.cust_id',array('companyName'))
								 ->joinInner(array('dash' => 'dashboard_menu'),'re.dashboard_menu = dash.did',array('menu_name'))
								-> where('re.status=?','1');
								if($dashboard_menu!=null) { 
									$select	-> where('re.dashboard_menu IN(?)',array($dashboard_menu));
								}
								if($accounts!=null) {
									$select	-> where('re.accounts IN(?)',array($accounts,0));
								}
								$select->order('re.rid DESC');
			$res = $db->fetchAll( $select );
			return ($res && sizeof($res)>0)? $res :false;
		} catch(Exception $e){
			return false;
		}
	}
	
	public function deleteReport($rid) 
	{
		if(!empty($rid) && $rid !=0){
				   try{
					  $this->delete('rid = '.$rid);
					  return true;
					}catch(Exception $e){
						return false;
					}
		}
	}
    
    public function editReport($rid) 
	{
		$db=Zend_Db_Table::getDefaultAdapter();
		try{
			$select = $db->select()
								->from(array('re' => 'report'))
								-> where('status=?','1')
								-> where('rid=?',$rid);
			$res = $db->fetchAll( $select );
			return ($res && sizeof($res)>0)? $res :false;
		} catch(Exception $e){
			return false;
		}
	}
	
	
	public function getWorkOrderByBuilding($buildID,$order,$dir){
		if($buildID){
			//$select = $this->select()->where('status=?','1') ;
			$orderBy = $order.' '.$dir;
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
                      ->from(array('wo' => 'work_order'),array('wo_number'))
                      ->joinLeft(array('bu'=>'buildings'),'bu.build_id = wo.building',array())                        
                      ->where('wo.building=?',$buildID);
                      
            $select = $select->order(array($orderBy));                      
            $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
		}else
		 return false;
	}
    
	public function invoiceByBatch($buildID,$order,$dir) {
		if($buildID){
				$orderBy = $order.' '.$dir;
				$db = Zend_Db_Table::getDefaultAdapter();
				$select = $db->select()
						  ->from(array('wo' => 'work_order'),array('wo_number'))
						  ->joinLeft(array('wop'=>'work_order_update'),'wop.wo_id = wo.woId AND wop.current_update = 1',array())
						  ->where('wo.building=?', $buildID)
						  ->where('wop.billable_opt=?',1)
						   ->where('wop.current_update=?',1)
						  ->where('wop.wo_status=?',7);
						  $select = $select->order(array($orderBy));
						  $res = $db->fetchAll( $select ); 
						  return ($res && sizeof($res)>0)? $res : false ;
		}else
			 return false;
	}
	
	public function getBillableInvoice($woId) {
		if($woId){
	            $db = Zend_Db_Table::getDefaultAdapter();
				$select = $db->select()
				->from(array('wop' => 'work_order_update'),array('wo_id','upId'))
						  ->where('wop.wo_id=?', $woId)
						  ->where('wop.billable_opt=?',1)
						  ->where('wop.current_update=?',1)
						  ->where('wop.wo_status=?',7);
				$res = $db->fetchAll( $select ); 
				return ($res && sizeof($res)>0)? $res : false ;
		}else
			 return false;
	}
   
}
