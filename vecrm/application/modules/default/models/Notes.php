<?php
/**
 * Description of notes
 *
 * @author brijesh
 */
class Model_Notes extends Zend_Db_Table_Abstract {

   protected $_name = 'notes_predefined';   
   protected $_tab_role = 'notes_predefined'; 
   public $_errorMessage='';
   
   /* Get all notes list */
    public function getNotes($nid = "") {       
        
        $select = $this->select();
        if(!empty($nid)){
            $select = $select->where( 'nid = ? ', $nid );
        }      
       
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
         /* Save Notes */
    public function insertNotes($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Notes */
    public function updateNotes($data, $nid) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('nid = ?', $nid);	
        unset($data['nid']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    
    /********delete Notes********/	
	public function deleteNotes($nid){
		if(!empty($nid) && $nid !=0){
			   try{
				  $this->delete('nid = '.$nid);
				  return true;
				}catch(Exception $e){
					return false;
				}
			}
	}
        
   
}

