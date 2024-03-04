<?php
/**
 * Description of Work Description
 *
 * @author brijesh
 */
class Model_WorkDescription extends Zend_Db_Table_Abstract {

   protected $_name = 'work_description';   
   protected $_tab_role = 'work_description'; 
   public $_errorMessage='';
   
   /* Get all Description list */
    public function getDescription($id = "") {       
        
        $select = $this->select();
        if(!empty($id)){
            $select = $select->where( 'id = ? ', $id );
        }      
       
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
         /* Save Description */
    public function insertDescription($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Description */
    public function updateDescription($data, $id) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('id = ?', $id);	
        unset($data['id']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getDescByWoId($woId){
		if(!empty($woId)){
		    $select = $this->select();        
            $select = $select->where( 'woId = ? ', $woId );       
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
    
    
    /********delete Description********/	
	public function deleteDescription($id){
		if(!empty($id) && $id !=0){
			   try{
				  $this->delete('id = '.$id);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
        
   
}

