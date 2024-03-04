<?php
/**
 * Description of Material
 *
 * @author brijesh
 */
class Model_Material extends Zend_Db_Table_Abstract {

   protected $_name = 'material';   
   protected $_tab_role = 'material';  
   public $_errorMessage='';
   
   /* Get all material list */
    public function getMaterial($mid = "") {       
        
        $select = $this->select();
        if(!empty($mid)){
            $select = $select->where( 'mid = ? ', $mid );
        }
        $select = $select->where( 'remove_status = ? ', '0' );
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    }
    
    /***********Get Material List by building Id *********/ 
    
    public function getMaterialByBid($bid,$order,$dir,$search = array()) {       
        if(!empty($bid)){
			$orderBy = $order.' '.$dir;
            $select = $this->select();        
            $select = $select->where( 'buildingId = ? ', $bid ); 
            $select = $select->where( 'remove_status = ? ', '0' );
            if(isset($search['search_by']) && $search['search_by']=='description'){
				$select = $select->where( "description Like'%$search[search_value]%'" );
			}
			
			if(isset($search['search_by']) && $search['search_by']=='service'){
				$select = $select->where( "service = '$search[search_value]'" );
			}
			
			if(isset($search['search_by']) && $search['search_by']=='vendor'){
				$select = $select->where( "vendor = '$search[search_value]'" );
			}
			
			if(isset($search['search_by']) && $search['search_by']=='vendor_part'){
				$select = $select->where( "vendor_part Like'%$search[search_value]%'" );
			}  
			
			if(isset($search['search_by']) && $search['search_by']=='manufacturer'){
				$select = $select->where( "manufacturer Like'%$search[search_value]%'" );
			} 
			
			if(isset($search['search_by']) && $search['search_by']=='mfg'){
				$select = $select->where( "mfg Like'%$search[search_value]%'" );
			}
			$select = $select->order(array($orderBy));     
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;

    }
    
         /* Save Material */
    public function insertMaterial($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    
    /* Update Material */
    public function updateMaterial($data, $mid) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('mid = ?', $mid);	
        unset($data['mid']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }    
   
   
   /* Delete Material */
   public function deleteMaterial($mid){
	    $where = $this->getAdapter()->quoteInto('mid = ?', $mid);	       
        try {
            $this->update(array('remove_status'=>'1'),$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }
   }
   
   public function getActiveMaterialByBId($bid){
	   if(!empty($bid)){			
            $select = $this->select();        
            $select = $select->where( 'buildingId = ? ', $bid );
            $select = $select->where( 'status = ? ', '1' );    
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false; 
   }
   
}
