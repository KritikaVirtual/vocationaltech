<?php

/**
 * Description of module
 *
 * @author ivtidai
 */
class Model_Module extends Zend_Db_Table_Abstract {

    protected $_name = 'modules';
    protected $_tab_role = 'modules';
    public $_errorMessage = '';

    /* Get all users/staff detail */

    public function getModule() {
        $select = $this->select();
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }

    public function getModuleListing($moduleids = array(), $search = array()) {
        $select = $this->select();
        if (!empty($moduleids)) {
            $select = $select->where('module_id in (' . implode(",", $moduleids) . ')');
        }
        if (isset($search['search_by']) && $search['search_by'] == 'module_name') {
            $select = $select->where("module_name Like'%$search[search_value]%'");
        }
        $res = $this->fetchAll($select);

        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }

    public function updateModule($data, $module_id) {
        $this->_errorMessage = "";
        $where = $this->getAdapter()->quoteInto('module_id = ?', $module_id);
        unset($data['module_id']);
        try {
            $this->update($data, $where);
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    public function deletemodule($module_id) {
        if (!empty($module_id) && $module_id != 0) {
            try {
                $this->delete('module_id = ' . $module_id);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }

}
