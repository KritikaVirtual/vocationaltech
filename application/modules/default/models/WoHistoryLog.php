<?php
/**
 * Description of Work order Category Log
 *
 * @author Brijesh Kumar
 */
class Model_WoHistoryLog extends Zend_Db_Table_Abstract {

   protected $_name = 'wo_history_log';   
   protected $_tab_role = 'wo_history_log';
   public $_errorMessage='';
   
  public function insertHistoryLog($data){				
        try{            
            $this->_errorMessage="";    	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
     
    
    
    public function getHistoryLog($whId = "") {        
        
        if(!empty($whId)){
            $select = $select->where( 'whId = ? ', $whId );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
	
	 public function getHistoryLogComplete($whId = "") {        
        
        if(!empty($whId)){
            $select = $this->select()->where( 'whId = ? ', $whId );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    }
    
    public function getWoHistoryLog($woId){
		if(!empty($woId)){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
                      ->from(array('whl' => 'wo_history_log'))
                      //->joinInner(array('cat' => 'category'),'wcl.category = cat.cat_id',array('categoryName','cat_id'))
                      ->joinLeft(array('us'=>'users'),'whl.user_id = us.uid',array('firstName','lastName','email'))
                      ->joinLeft(array('r'=>'role'),'r.roleID = us.role_id',array('role_title'=>'r.title'))                                            
                      ->where('whl.woId=?',$woId);                  
            $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
        }else
        return false;
	}
	
	   
    public function getWoHistoryLogByLog($woId, $logType, $userId) {
	    if($woId!='' & $logType!='') {
		    $select = $this->select()
            ->where( 'woId = ? ', $woId )
		    ->where( 'log_type = ? ', $logType)
			 ->order('whId DESC')
			 ->limit(1);
		    $res = $this->fetchAll( $select );
		    return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }
    }  

    public function getUnitmessaure() {
        $db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bs' => 'build_service'),array('unit_measure','bsid'));
		$res = $db->fetchAll( $select );
		return ($res && sizeof($res)>0)? $res : false ;
    }

    public function getWoCurrentLog($woId){
		if(!empty($woId)){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
                      ->from(array('wo' => 'work_order'),array('woId','tenant','date_requested','time_requested','create_user','status','category','work_order_request'))                                          
                      ->where('wo.woId=?',$woId);                  
            $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
        }else
        return false;
	}
	
	public function getWoHistoryLogByLabor($lid) {
	    $db = Zend_Db_Table::getDefaultAdapter();
	    if($lid!='') {
		    $select = $db->select()
			->from(array('wl' => 'labor'))
            ->where( 'lId = ? ', $lid );
		    $res = $db->fetchAll( $select );
		    return ($res && sizeof($res)>0)? $res : false ;
        }
    } 
	
	public function getWoHistoryLogByBuilding($bsId) {
	    $db = Zend_Db_Table::getDefaultAdapter();
	    if($bsId!='') {
		    $select = $db->select()
			->from(array('wl' => 'building_service'))
            ->where( 'bsId = ? ', $bsId );
		    $res = $db->fetchAll( $select );
		    return ($res && sizeof($res)>0)? $res : false ;
        }
    }
	
	public function getWoHistoryLogByMaterial($mcId) {
	    $db = Zend_Db_Table::getDefaultAdapter();
	    if($mcId!='') {
		    $select = $db->select()
			->from(array('wl' => 'material_charge'))
            ->where( 'mcId = ? ', $mcId );
		    $res = $db->fetchAll( $select );
		    return ($res && sizeof($res)>0)? $res : false ;
        }
    }
	
	public function getWoHistoryLogByOutside($osId) {
	    $db = Zend_Db_Table::getDefaultAdapter();
	    if($osId!='') {
		    $select = $db->select()
			->from(array('wl' => 'outside_service'))
            ->where( 'osId = ? ', $osId );
		    $res = $db->fetchAll( $select );
		    return ($res && sizeof($res)>0)? $res : false ;
        }
    }
	
	public function getWoHistoryLogByNotes($wnId) {
	    $db = Zend_Db_Table::getDefaultAdapter();
	    if($wnId!='') {
		    $select = $db->select()
			->from(array('wl' => 'wo_note'))
            ->where( 'wnId = ? ', $wnId );
		    $res = $db->fetchAll( $select );
		    return ($res && sizeof($res)>0)? $res : false ;
        }
    }
   
  }

