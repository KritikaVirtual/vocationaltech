<?php 
class Model_Email extends Zend_Db_Table_Abstract {

   protected $_name = 'email_templates';   
   protected $_tab_role = 'email_templates';   
   public $_errorMessage='';
   
    
    
  
    

    /* Save email template */
    public function insertEmail($data) {				
        try{
          
            $this->_errorMessage="";   	   	            		
            $data['updated_date']=date('Y-m-d H:i:s');	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* update email template */
   public function updateEmail($data,$id) {				
        try{
            $where = $this->getAdapter()->quoteInto('id = ?', $id);
            $this->_errorMessage="";            		
            $data['updated_date']=date('Y-m-d H:i:s');	
            return $this->update($data,$where);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    } 
    /**
     * Fetch email templates
     */ 
    public function loadEmailTemplate($eid=''){
		$select = $this->select()->where('status=?','1');
		if($eid!=''){
			$select = $select->where('id=?',$eid);
		}
		$res = $this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
	}
   
   public function deleteEmail($eid){
	   try{
		   if(isset($eid) && $eid!=0){
			   $where = $this->getAdapter()->quoteInto('id = ?', $eid);                       		
			   $this->delete($where);
				return true;	
          }else{
			  return false;
		  }
	   }catch(Exception $e){
		   echo $e->getMessage(); die();
	   }
   }
}	
