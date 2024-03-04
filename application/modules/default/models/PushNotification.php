<?php
/**
 * Sending push notification
 *
 * @author Gurubaksh Singh
 */
class Model_PushNotification extends Zend_Db_Table_Abstract {

   protected $_name = 'push_tokens';   
   protected $_tab_role = 'push_tokens';  
   public $_errorMessage='';
   
  public function insertWoSchedule($data) {				
        try{            
            $this->_errorMessage="";    	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
     public function updateWoSchedule($data,$wssId) {
		 $this->_errorMessage=""; 			
        try{ 
			if(isset($wssId) && !empty($wssId)){ 
			 $where = $this->getAdapter()->quoteInto('wssId = ?', $wssId);
			 $this->update($data,$where);				   	
			 return true;
		   }else{
		     return false;		
		   }
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    
    public function getWoSchedule($woId,$sId){
		if(!empty($woId)){
			$select = $this->select();
            $select = $select->where( 'worder_id = ? ', $woId );  
            $select = $select->where( 'schedule_id = ? ', $sId );
            $select = $select->where( 'status = ? ', '1' );           
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
    
    public function getPushId($uid='', $pushId='') {
		$select = $this->select();
		if($pushId!=''){
			$select = $select->where('push_id = ? ', $pushId);
		}
		if($uid !='' && (!empty($uid))) {
			$select = $select->where('user_id in ('.implode(",",$uid).')');
		}
		$select = $select->where('status = ? ',1);
		$res = $this->fetchAll( $select ); 
		return ($res && sizeof($res)>0)? $res->toArray() : false ;
		
	}
	
	public function updatePushId($data,$pushId='') {
		try{
			if($pushId!='') {
				$where = $this->getAdapter()->quoteInto('push_id = ?', $pushId);
				$select = $this->select();
				$this->update($data,$where);				   	
				 return true;
			} else {
				return false;
			}
		} catch(Exception $e)	{	
				echo $e->getMessage();die;
			}
			
		}
}

