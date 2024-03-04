<?php

/**
 * Description of Work Order
 *
 * @author brijesh
 */
class Model_CioTemplate extends Zend_Db_Table_Abstract {
	 protected $_name = 'coi_vt_defaults';

public function GetAllTemplatecoName($search = "") {
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()
                ->from(array('coi_vt_defaults'));
        if ($search != "") {
            $select = $select->where("coi_vt_defaults like '%" . $search . "%'");
        }
          $wvv='General';
        $select = $select->where("coi_vt_defaults_tab like '%" . $wvv . "%'");

        
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }



public function GetAllTemplatecoNamesecontab($search = "") {
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()
                ->from(array('coi_vt_defaults'));
        if ($search != "") {
            $select = $select->where("coi_vt_defaults like '%" . $search . "%'");
        }
          $wvv='Automobile';
        $select = $select->where("coi_vt_defaults_tab like '%" . $wvv . "%'");

        
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
    //


    public function GetAllTemplatecoUmbrella($search = "") {
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()
                ->from(array('coi_vt_defaults'));
        if ($search != "") {
            $select = $select->where("coi_vt_defaults like '%" . $search . "%'");
        }
        $ute='Umbrella';
        $select = $select->where("coi_vt_defaults_tab like '%" . $ute . "%'");

        
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }

     public function GetAllTemplatecoWorkers($search = "") {
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()
                ->from(array('coi_vt_defaults'));
        if ($search != "") {
            $select = $select->where("coi_vt_defaults like '%" . $search . "%'");
        }
          $ute='Workers';
        $select = $select->where("coi_vt_defaults_tab like '%" . $ute . "%'");

        
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }


    //Edite Dtat


    public function loadcioTemplate($id) {


        $select = $this->select();

        if ($id!= '') {
            $select = $select->where('coi_vt_default_ID=?', $id);
        }
        
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }
    public function updatecio($data, $id)
    {

    
        try {
            $where = $this->getAdapter()->quoteInto('coi_vt_default_ID = ?', $id);
            $this->_errorMessage = "";
           // $data['updated_date'] = date('Y-m-d H:i:s');
           
            return $this->update($data, $where);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

public function GetAllData($search = "") {
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()
                ->from(array('coi_vt_defaults'));
        if ($search != "") {
            $select = $select->where("coi_vt_defaults like '%" . $search . "%'");
        }
        
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }	
	
}
