<?php

/**
 * Description of Model_User_Building_Module
 *
 * @author brijesh
 */
class Model_UserBuildingModule extends Zend_Db_Table_Abstract {

    protected $_name = 'user_building_module_access';
    protected $_tab_role = 'user_building_module_access';
    public $_errorMessage = '';

    /* Save user building module */

    public function updateBuildingModule($data) {
        try {
            //$this->deleteData($data[0]['user_id']);
            foreach ($data as $key => $building) {
                $buildData = $this->getUserBuild($building['user_id'], $building['building_id']);
                if (!$buildData)
                    $this->insert($building);
                else {
                    $where = array();
                    $where[] = "user_id = '" . $building['user_id'] . "'";
                    $where[] = "building_id = '" . $building['building_id'] . "'";
                    $this->update($building, $where);
                }
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /* Update user building module */

    public function updateUserBuildingModule($data, $buildingDetail) {
        try {

            foreach ($buildingDetail as $key) {
                $condition = array(
                    'user_id = ' . $data[0]['user_id'],
                    'building_id = ' . $key['build_id']
                );
                $this->delete($condition);
            }
            foreach ($data as $key => $building) {
                $this->insert($building);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteData($id) {
        try {
            $this->delete('user_id = ' . $id);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getUserBuildingIds($user_id) {
        //$cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "SELECT ubma.*, (select count(*) from user_building_module_access where building_id=ubma.building_id) as total FROM `user_building_module_access` as ubma WHERE ubma.user_id=" . $user_id;
        //$db->query($sql);
        //$res = $db->fetchAll($sql);		
        $stmt = $db->query($sql);
        $stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
        $res = $stmt->fetchAll();
        //$select = $this->select()->where('user_id=?',$user_id);		
        //$res = $this->fetchAll($select);

        return ($res && sizeof($res) > 0) ? $res : false;
    }

    public function getUserBuild($user_id, $build_id) {
        $select = $this->select()->where('user_id=?', $user_id);
        $select = $select->where('building_id=?', $build_id);
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }

    public function deteleUserBuild($user_id) {
        if (!empty($user_id) && $user_id != 0) {
            try {
                $this->delete('user_id = ' . $user_id);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function deleteBuildingUser($userId, $buildId) {
        if (!empty($userId) && !empty($buildId)) {
            $condition = array(
                'user_id = ' . $userId,
                'building_id = ' . $buildId
            );
            try {
                $this->delete($condition);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function getTotalUser($build_id) {
        $select = $this->select()->where('building_id=?', $build_id);
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }

    public function insertModuleAcsess($building) {
        try {
            $this->insert($building);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getBuildingByUserId($user_id) {
        $select = $this->select()->where('user_id=?', $user_id);
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }

    // update and insert module access to building
    public function updateOnlyBuildingModule($data) {
        try {
            //$this->deleteData($data[0]['user_id']); 
            $db = Zend_Db_Table::getDefaultAdapter();
            $condition = array('building_id = ' . $data['building_id']);
            $db->delete('building_module_access', $condition);
            foreach ($data['module_id'] as $key => $modules) {
                $values = array('building_id' => $data['building_id'], 'module_id' => $modules, 'last_update_date' => $data['last_update_date'], 'assigned_date' => $data['assigned_date'], 'status' => 1);
                $inserted = $db->insert('building_module_access', $values);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function assignedBuildingToModule($module_id, $cust_id) {

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('bu' => 'buildings'), array('bu.buildingName', 'bu.state', 'bu.city', 'bu.phoneNumber', 'bu.status', 'bu.address', 'bu.cust_id', 'bu.build_id'))
                    ->joinInner(array('moac' => 'building_module_access'), 'bu.build_id = moac.building_id', array('moac.module_id'))
                    ->where('module_id=?', $module_id)
                    ->where('cust_id=?', $cust_id);
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } catch (Exception $ex) {

            return false;
        }
    }

    public function getCompanyOfModuleByBuilding($module_id) {

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()
                    ->from(array('bu' => 'buildings'), array('bu.cust_id'))
                    ->joinInner(array('moac' => 'building_module_access'), 'bu.build_id = moac.building_id', array('moac.module_id'))
                    ->joinLeft(array('cu' => 'company'), 'cu.cust_id = bu.cust_id', array('cu.companyName'))
                    ->where('module_id=?', $module_id)
                    ->group('cu.companyName')
                    ->order('cu.companyName');
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } catch (Exception $ex) {

            return false;
        }
    }

    public function deleteModuleBuilding($module_id, $building_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!empty($module_id) && !empty($building_id)) {
            $condition = array(
                'module_id = ' . $module_id,
                'building_id = ' . $building_id
            );
            try {
                $db->delete('building_module_access', $condition);
                return true;
            } catch (Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }

    public function getModuleByBuildingId($building_id = '', $module_id = '') {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($building_id != '') {
            $select = $db->select()
                    ->from(array('buac' => 'building_module_access'), array('buac.module_id'))
                    ->joinInner(array('mo' => 'modules'), 'buac.module_id = mo.module_id', array('mo.module_name'))
                    ->where('building_id=?', $building_id)
                    ->where('mo.status=?', 1);
            if ($module_id != '') {
                $select = $select->where('buac.module_id=?', $module_id);
            }

            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }
	
	public function getModuleByBuildingId1($building_id = '', $module_id = '') {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($building_id != '') {
            $select = $db->select()
                    ->from(array('buac' => 'building_module_access'), array('buac.module_id'))
                    ->joinInner(array('mo' => 'modules'), 'buac.module_id = mo.module_id', array('mo.module_name'))
                    ->where('building_id=?', $building_id);
                   // ->where('mo.status=?', 1);
            if ($module_id != '') {
                $select = $select->where('buac.module_id=?', $module_id);
            }

            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        }
        return false;
    }
	public function getModulestatus($m_id){
		$db = Zend_Db_Table::getDefaultAdapter();        
		$select = $db->select()
				->from(array('buac' => 'modules'), array('buac.module_id','buac.status'))
				->where('buac.module_id=?', $m_id);			    
		$res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
	}

}
