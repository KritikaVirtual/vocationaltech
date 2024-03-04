<?php

class Model_Email extends Zend_Db_Table_Abstract {

    protected $_name = 'email_templates';
    protected $_tab_role = 'email_templates';
    public $_errorMessage = '';

    /* Save email template */

    public function insertEmail($data) {
        try {

            $this->_errorMessage = "";
            $data['updated_date'] = date('Y-m-d H:i:s');
            return $this->insert($data);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    /* update email template */

    public function updateEmail($data, $id) {
        try {
            $where = $this->getAdapter()->quoteInto('id = ?', $id);
            $this->_errorMessage = "";
            $data['updated_date'] = date('Y-m-d H:i:s');
            return $this->update($data, $where);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    /**
     * Fetch email templates
     */
    public function loadEmailTemplate($eid = '', $userId = '', $emailLocation='' , $buildId='') {
        $select = $this->select();
        if ($eid != '') {
            $select = $select->where('id=?', $eid);
        }
        if ($userId != '') {
            $select = $select->where('user_id=?', $userId);
        }
        if($buildId!=''){
            $select = $select->where('build_id=?', $buildId);
        }
        if ($emailLocation != '') {
            $select = $select->where('email_location=?', $emailLocation);
            //$select = $select->where('status=?', 1);
        }
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }

    // durgesh chaubey 19 dec 2022 add function for gettemplete id by template name.
    public function getidbytemplatename($name = '') {
        $select = $this->select();
        if ($name != '') {
            $select = $select->where('email_title=?', $name);
        }
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }
    
    public function loadEmailTemplatebyid($eid = '', $userId = '', $emailLocation='' , $buildId='') {
        $select = $this->select();
        if ($eid != '') {
            $select = $select->where('id=?', $eid);
        }
        if ($userId != '') {
            $select = $select->where('user_id=?', $userId);
        }
        if($buildId!=''){
            $select = $select->where('build_id=?', $buildId);
        }
        if ($emailLocation != '') {
            $select = $select->where('email_location=?', $emailLocation);
            //$select = $select->where('status=?', 1);
        }
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }
    

    public function deleteEmail($eid) {
        try {
            if (isset($eid) && $eid != 0) {
                $where = $this->getAdapter()->quoteInto('id = ?', $eid);
                $this->delete($where);
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    //Disable email template
    public function disableEmail($data, $user_id, $email_location) {
        try {
            $where = array();
            $where[] = $this->getAdapter()->quoteInto('user_id = ?', $user_id);
            $where[] = $this->getAdapter()->quoteInto('email_location = ?', $email_location);
            $this->_errorMessage = "";
            $data['updated_date'] = date('Y-m-d H:i:s');
            return $this->update($data, $where);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }
    
    public function getCustomtemplate($userId, $emailLocation,$build_id="",$type="") {
         $select = $this->select();
        
        if ($userId != '') {
            $select = $select->where('user_id=?', $userId);
        }
        if($build_id !=""){
            $select = $select->where('build_id=?', $build_id);
        }
        if ($emailLocation != '') {
            $select = $select->where('email_location=?', $emailLocation);
        }
        if($type !=""){
            $select = $select->where('type=?', $type);
        }else{
            $select = $select->where('type=?', 0);
        }

        $select = $select->where('status=?', 1);

        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }
    
    
    public function emailtemplateChecker($build_id = "",$emailLocation = "",$type){
        $select = $this->select();
        
        
        if ($userId != '') {
            $select = $select->where('user_id=?', $userId);
        }
        if($build_id !=""){
            $select = $select->where('build_id=?', $build_id);
        }
        if ($emailLocation != '') {
            $select = $select->where('email_location=?', $emailLocation);
        }
        if($type !=""){
            $type = 1;
            $select = $select->where('type=?', $type);
        }else{
            $select = $select->where('type=?', 0);
        }
        $select = $select->where('status=?', 1);
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }
      public function getMultipleTemplate() {
			   $db = Zend_Db_Table::getDefaultAdapter();                
			   $select = $db->select()
						  ->from(array('et' => 'email_templates'), array('id', 'email_title'));
			   $select = $select->where('et.build_id=?','0');
			   $select = $select->where('et.status=?','1');
			   $res = $db->fetchAll( $select );   
			   
				return ($res && sizeof($res)>0)? $res : false ;
			
	}
}
