<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Model_Category
 *
 * @author ivtidai
 */
class Model_Category extends Zend_Db_Table_Abstract  {
    protected $_name = 'category';   
    protected $_tab_role = 'category';   
    public $_errorMessage='';
    
    
    public function getCerrentId() {
        $res = $this->fetchAll(
            $this->select()->from($this, array(
                new Zend_Db_Expr('max(cat_id) as cat_id')
            ))
        );
       // echo '<pre>';  var_dump($res); die;
        return ($res && sizeof($res)>0)? $res->toArray() : false;
    }
    
    /* Save Schedule */
    public function insertCategory($data) {
        try{	
            return $this->insert($data);		
        } catch(Exception $e)	{	
            echo $e->getMessage();die;
        }
    }
    

    public function checkname($name, $build_id, $cat_id = '') {
        try{

        if($cat_id=='')
            $select=$this->select('cat_id')->where('categoryName LIKE ?',$name)->where('building_id=?',$build_id);                
        else
            $select=$this->select('cat_id')->where('categoryName LIKE ?',$name)->where('building_id=?',$build_id)->where('cat_id !=?',$cat_id);
        $res=$this->fetchRow($select);
        
        return ($res && sizeof($res)>0)? true : false ; 
           
        }catch(Exception $e)    {   
        //echo $e->getMessage();die;
        return false;
        }
    }

    /* Update Schedule */
    public function updateCategory($data, $id) {        
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('cat_id = ?', $id);	
        unset($data['cat_id']);
        try {
            $this->update($data,$where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }	
    }
    
    public function getAllCategory($pid = "") {
        try {  
            $select = $this->select();
            if(!empty($pid)) {
                $select = $select->where('cat_id = ?', $pid);
            }
            $select = $select->where('remove_status = ?', 0);
            $res=$this->fetchAll($select);
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        } catch (Exception $e) {
            
            print_r($e->getMessage()); die;
        }
    }
    
    public function getRecoverCategory($bid) {
        try {  
            $select = $this->select();
            $select = $select->where('building_id = ?', $bid);            
            $select = $select->where('remove_status = ?', 1);
            $select = $select->where('history_remove = ?', 0);            
            $res=$this->fetchAll($select);
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        } catch (Exception $e) {
            
            print_r($e->getMessage()); die;
        }
    }
    
    public function activateCategory($id) {
               $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('cat_id = ?', $id);	
        try {
           $this->update(array(
					"remove_status"           => 0                
				),$where);
				return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }
    }
    
    public function deleteCategory($id) {
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('cat_id = ?', $id);	
        try {
           $this->update(array(
					"remove_status"           => 1,
					"remove_date" => date('Y-m-d')                
				),$where);
				return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }
    }
    
    public function getBuildingCategoryList($buildingId){
		try { 
			$res='';
			if(!empty($buildingId)) { 
            $select = $this->select();            
            $select = $select->where('building_id = ?', $buildingId);
            $select = $select->where('remove_status = ?', 0);
            $select= $select->order('cat_id DESC');                          
            $res=$this->fetchAll($select);
		    }
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        } catch (Exception $e) {
            
            print_r($e->getMessage()); die;
        }
	}
   
   
   /********get priorotiy category list**********/
   public function getPriorityCategory($priorityId){
	   try { 
			$res='';
			if(!empty($priorityId)) { 
            $select = $this->select();            
            $select = $select->where('prioritySchedule = ?', $priorityId);
            $select = $select->where('status = ?', '1');
            $select = $select->where('remove_status = ?', 0);
            $select= $select->order('cat_id DESC');              
            $res=$this->fetchAll($select);
		    }
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        } catch (Exception $e) {
            
            print_r($e->getMessage()); die;
        }
   }
   
    public function deletePriorityCategory($priorityId) {
        $this->_errorMessage="";
        $where = $this->getAdapter()->quoteInto('prioritySchedule = ?', $priorityId);	
        try {
            $this->delete($where);
            return true;
        }catch(Exception $e){    		
            echo $e->getMessage(); die();
        }
    }
    
    public function getCategoryName($categoryId){
		try {  
            $select = $this->select(array('categoryName'))->where('cat_id = ?', $categoryId);           
            $res=$this->fetchAll($select);
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        } catch (Exception $e) {            
            echo $e->getMessage(); die();
        }
	}
	
	public function getCategoryByUser($userId,$buildingId=''){
		try {  
             $select = $this->select();
             if(!empty($buildingId))          
             $select = $select->where('building_id = ?', $buildingId);
             $select = $select->where("FIND_IN_SET($userId,account_user)");                        
             $res=$this->fetchAll($select);
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        } catch (Exception $e) {            
            echo $e->getMessage(); die();
        }
	}
	
	public function getCategoryByEmailUser($userId,$buildingId=''){
		try{
		$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
                      ->from(array('cat' => 'category'))
                      ->joinInner(array('egu' => 'email_group_users'),'FIND_IN_SET(egu.group_id,send_email)',array('egu.group_id'))                      
                      ->where('egu.user_id =?', $userId);
             if(!empty($buildingId))          
             $select = $select->where('cat.building_id = ?', $buildingId);                             
             //$select = $select->order(array($orderBy));                                  
            $res = $db->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res : false ;
		}catch(Exception $e){
			return false;
		}
	}
	
	public function getRemoveCategory() {
		$date_6back = date("Y-m-d", strtotime("-6 months"));
        try {  
            $select = $this->select();                       
            $select = $select->where('remove_status = ?', 1);
            $select = $select->where('history_remove = ?', 0);            
            $res=$this->fetchAll($select);
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        } catch (Exception $e) {
            
            print_r($e->getMessage()); die;
        }
    }
    
    public function getHistoryCategory(){
		$date_5yback = date("Y-m-d", strtotime("-5 years"));
		$select=$this->select()->where('history_remove = ?', '1');
		$select = $select->where('history_date <= ?', $date_5yback);						
        $res=$this->fetchAll($select);
        return ($res && sizeof($res)>0)? $res->toArray() : false ;
		
	}
	
	public function moveRemoveToHistory(){
		$removeCateogry = $this->getRemoveCategory();
		if($removeCateogry){
			foreach($removeCateogry as $rc){
				if(isset($rc['cat_id'])&& $rc['history_remove']=='0'){
					$histData = array();
					$histData['history_remove']=1;
					$histData['history_date']=date('Y-m-d');
					$moveHistory = $this->updateCategory($histData,$rc['cat_id']);
				}
			}
		}
	}
	
	
	public function deleteCategoryHistory(){
		$historyCateogry = $this->getHistoryCategory();
		if($historyCateogry){
			foreach($historyCateogry as $hc){
				if(isset($hc['cat_id'])&& $hc['history_remove']=='1'){
					$this->delete('cat_id = '.$hc['cat_id']);
				}
			}
		}
	}
	

}
