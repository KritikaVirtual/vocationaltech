<?php
/**
 * Description of time zone
 *
 * @author brijesh
 */
class Model_TimeZone extends Zend_Db_Table_Abstract {

   protected $_name = 'time_zone';   
   protected $_tab_role = 'time_zone';  
   public $_errorMessage='';
   
   /* Get list of time zone */
    public function getTimeZone($id="") {       
        
        $select = $this->select();
		$select = $select->where('status=?',1);
        if(!empty($id)){       
			$select = $select->where('id=?',$id);
		}
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    }
    
    public function getTimeZoneById($id) { 
        
        
        if(!empty($id)){ 
			$select = $this->select(); 
            $select = $select->where('status=?',1);			
			$select = $select->where('id=?',$id);
			$res = $this->fetchAll( $select );
        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
		}else
		return false;
        

    }  
    
     public function setTimezone($building_id){
		$build_model= new Model_Building();
		$tz_build_data = $build_model->getbuildingbyid($building_id);

		if(isset($tz_build_data[0]['timezone']) && $tz_build_data[0]['timezone']!=0){								  
			$timezone_data	=	$this->getTimeZone($tz_build_data[0]['timezone']);
			if($timezone_data) {
			    $timezone=$timezone_data[0]['time_value'];	
                date_default_timezone_set($timezone);	
			}				
		} else if ($tz_build_data[0]['timezone']==0) {
		           date_default_timezone_set(DEFAULT_TIMEZONE);
		}
	 }
        
   
}
