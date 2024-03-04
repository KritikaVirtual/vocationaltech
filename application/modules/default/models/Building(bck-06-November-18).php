<?php

class Model_Building extends Zend_Db_Table_Abstract {

    protected $_name = 'buildings';
    //protected $_tab_role = 'buildings';   
    public $_errorMessage = '';

    /* Get all users/staff detail */

    public function getbuilding($companyID = '') {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(array('bu' => 'buildings'))
                ->joinLeft(array('st' => 'states'), 'st.state_code = bu.state_code', array('st.state as statename'));
        if ($companyID) {
            $select = $db->select()
                    ->from(array('bu' => 'buildings'))
                    ->joinLeft(array('st' => 'states'), 'st.state_code = bu.state_code', array('st.state as statename'))
                    ->joinLeft(array('bst' => 'states'), 'bst.state_code = bu.billState_code', array('bst.state as billstatename'))
                    ->where('bu.cust_id=?', $companyID);
        }
        $res = $db->fetchAll($select);
        $res = json_encode($res);
        $res = json_decode($res, true);
        return ($res && sizeof($res) > 0) ? $res : false;
    }

    public function getCompanyBuilding($companyID) {

        if ($companyID) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('bu' => 'buildings'))
                    ->joinLeft(array('st' => 'states'), 'st.state_code = bu.state_code', array('st.state as statename'))
                    ->joinLeft(array('bst' => 'states'), 'bst.state_code = bu.billState_code', array('bst.state as billstatename'))
                    ->where('bu.cust_id=?', $companyID);
            $select = $select->where('bu.status=?', '1');
            $res = $db->fetchAll($select);
            $res = json_encode($res);
            $res = json_decode($res, true);
            return ($res && sizeof($res) > 0) ? $res : false;
        } else {
            return false;
        }
    }

    public function getbuildingbyid($buildingID = '') {
        //	$select=$this->select()->where('status=?','1') ;
        if ($buildingID) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('bu' => 'buildings'))
                    ->joinLeft(array('st' => 'states'), 'st.state_code = bu.state_code', array('st.state as statename'))
                    ->joinLeft(array('bst' => 'states'), 'bst.state_code = bu.billState_code', array('bst.state as billstatename'))
                    ->where('build_id=?', $buildingID);
            $res = $db->fetchAll($select);
            $res = json_encode($res);
            $res = json_decode($res, true);
            return ($res && sizeof($res) > 0) ? $res : false;
        }

        return false;
    }

    function addBuilding($datacontent) {
        try {

            return $this->insert($datacontent);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
            return false;
        }
    }

    /* delete clients */

    public function deleteBuilding($id) {
        try {
            $woModel = new Model_WorkOrder();
            $checkWO = $woModel->getActiveWorkOrderByBId($id);
            if (!$checkWO) {
                /*                 * *********delete dependents of building *********** */
                $tntModel = new Model_Tenant();
                $catModel = new Model_Category();
                $priorityModel = new Model_Priority();
                $egroupModel = new Model_EmailGroup();
                $materialModel = new Model_Material();
                $vendorModel = new Model_Vendor();
                $bServiceModel = new Model_BuildService();
                $bLaborModel = new Model_BillLabor();
                $bRateModel = new Model_BillRate();

                $deleteTenant = $tntModel->deleteTenantByBId($id);
                $deletePreority = $priorityModel->deletePreorityByBId($id);
                $deletCategory = $catModel->deleteCategoryByBId($id);
                $deleteEGroup = $egroupModel->deleteGroupByBId($id);
                $deleteMaterial = $materialModel->deleteMaterialByBId($id);
                $deleteVendor = $vendorModel->deleteVendorByBId($id);
                $deleteBService = $bServiceModel->deleteBuildServiceByBId($id);
                $deleteBillLabor = $bLaborModel->deleteBillLaborByBId($id);
                $deleteBillRate = $bRateModel->deleteBillRateByBId($id);

                $this->delete('build_id = ' . $id);
                return true;
            } else
                return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /* Update  users detail by ID */

    public function updateBuilding($data, $id) {
        $this->_errorMessage = "";
        $where = $this->getAdapter()->quoteInto('build_id = ?', $id);
        //	unset($data['id']);
        //Check is user is duplicate or not

        try {
            $this->update($data, $where);
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
            return false;
        }
    }

    /** ========================================================================= */
    public function getBuildingList($buildingIds = array()) {

        $select = $this->select()->from('buildings', array('build_id', 'cust_id', 'buildingName', 'user_id', 'uniqueCostCenter'))->where('status=?', '1');



        if (!empty($buildingIds)) {

            $select = $select->where('build_id in (' . implode(",", $buildingIds) . ")");
        }

        $res = $this->fetchAll($select);



        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }

    public function deleteCompanyBuildings($companyID) {
        if (!empty($companyID) && $companyID != 0) {
            try {
                $this->delete('cust_id = ' . $companyID);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /*
     * check building by name
     */

    public function getBuildingByName($buildingName, $cmpId, $id = '') {
        if ($buildingName != '' && !empty($buildingName)) {
            $select = $this->select()->where('buildingName=?', $buildingName);
            if ($id != '') {
                $select = $select->where('build_id<>?', $id);
            }
            $select = $select->where('cust_id=?', $cmpId);
            $res = $this->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res->toArray() : false;
        }
    }

    public function getBuildingByOnlyName($buildingName) {
        if ($buildingName != '' && !empty($buildingName)) {
            $select = $this->select()->where('buildingName=?', $buildingName);
            $res = $this->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res->toArray() : false;
        }
    }

    public function getCompanyByOnlyName($buildingName) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = 'SELECT c.companyName, c.corp_account_number,c.cust_id FROM `buildings` as b inner join company as c on c.cust_id=b.cust_id WHERE `buildingName`="' . $buildingName . '"';

        $res = $db->fetchAll($query);
        return ($res && sizeof($res) > 0) ? $res : false;
    }

    public function getWoClosedDesc($woid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = 'SELECT description FROM `work_description` WHERE woId="' . $woid . '"';

        $res = $db->fetchAll($query);
        return ($res && sizeof($res) > 0) ? $res : false;
    }

    /*
     * get building by cost center
     */

    public function getbuildingByCostCenter($costCenter) {
        $select = $this->select()->where('uniqueCostCenter=?', $costCenter);
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }
	
	public function getbuildingId($costCenter) {
        $select = $this->select()->where('uniqueCostCenter=?', $costCenter);
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }

}
