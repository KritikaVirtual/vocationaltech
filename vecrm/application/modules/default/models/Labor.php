<?php
/**
 * Description of Labor
 *
 * @author brijesh
 */
class Model_Labor extends Zend_Db_Table_Abstract {

   protected $_name = 'labor';   
   protected $_tab_role = 'labor'; 
   public $_errorMessage='';
   
   /* Get all Labor list */
    public function getLabor($lid = "") {       
        
        $select = $this->select();
        if(!empty($lid)){
            $select = $select->where( 'lid = ? ', $lid );
        }      
       
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
         /* Save Labor */
    public function insertLabor($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Labor */
    public function updateLabor($data, $lid) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('lid = ?', $lid);	
        unset($data['lid']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }    
    
    
    public function getLaborByWoId($woId){
		if($woId){
			$db = Zend_Db_Table::getDefaultAdapter(); 				 
			$select = $db->select()
				 ->from(array('l' => 'labor'), array('lid','woId','emp_id','charge_hour','rate_charge','job_time'))
				 ->joinLeft(array('u' => 'users'), 'u.uid = l.emp_id', array('uid','firstName','lastName'))
				 ->joinLeft(array('br' => 'bill_rate'), 'l.rate_charge = br.brid', array('description','multiplier'))
				 ->where('l.woId = ?', $woId);				 			 				 
			$res = 	 $db->fetchAll($select);
			return ($res && sizeof($res)>0)? $res : false ;
		}else
		   return false;
	}
    
    /********delete Labor********/	
	public function deleteLabor($lid){
		if(!empty($lid) && $lid !=0){
			   try{
				  $this->delete('lid = '.$lid);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
        
   
}
