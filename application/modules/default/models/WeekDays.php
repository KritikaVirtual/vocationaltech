<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Role
 *
 * @author Anuj Kumar
 */
class Model_WeekDays extends Zend_Db_Table_Abstract {

   protected $_name = 'week_days';   
   protected $_tab_role = 'week_days';  
   public $_errorMessage='';
   
   /* Get all users/staff detail */
    public function getWeekDays($wdID = "") {
        $select = $this->select()->where('status=?','1') ;
        
        if(!empty($wdID)){
            $select = $select->where( 'wdID = ? ', $wdID );
        }
        
        $res = $this->fetchAll( $select );
        
        return ($res && sizeof($res)>0)? $res->toArray() : false ;

    } 
    
    
        
   
}
