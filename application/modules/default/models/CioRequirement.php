<?php

/**
 * Description of Work Order
 *
 * @author brijesh
 */
class Model_CioRequirement extends Zend_Db_Table_Abstract {
	 protected $_name = 'coi_au_requirements';

public function insertCoirequirement($updateData) {
	//echo "<pre>";print_r($updateData);die;
        try {
            $this->_errorMessage = "";
            return $this->insert($updateData);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }
	 
    public function CheckByBuildingID($buildingID = "") {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $select = $this->select();

        if ($buildingID!= '') {
            $select = $select->where('Building_ID=?', $buildingID);
        }
        
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    } 
	
	public function GetAllGeneralRequirment($select_build_id="") {
        $db = Zend_Db_Table::getDefaultAdapter();
     	  
        $select = $db->select()
                ->from(array('cvd' =>'coi_vt_defaults'))
				->joinLeft(array('car' => 'coi_au_requirements'), 'cvd.coi_vt_default_ID = car.coi_vt_default_ID');
        if ($select_build_id != "") {
            $select1 = $db->select()
            ->from(array('cr'=>'coi_au_requirements'))
            ->where('cr.Building_ID = ?', $select_build_id);
            $databyid = $db->fetchAll($select1);
            if(!empty($databyid)){
                $select = $select->where('car.Building_ID = ?', $select_build_id);
            }
        }
        $wvv='General';
        $select = $select->where("cvd.coi_vt_defaults_tab like '%" . $wvv . "%'")->group('cvd.coi_vt_default_ID');
        
        $res = $db->fetchAll($select);

        return ($res && sizeof($res) > 0) ? $res : false;
    }

	
	public function GetAllAutomobileRequirment($select_build_id = "") {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $select = $db->select()
                ->from(array('cvd' =>'coi_vt_defaults'))
				->joinLeft(array('car' => 'coi_au_requirements'), 'cvd.coi_vt_default_ID = car.coi_vt_default_ID');
        if ($select_build_id != "") {
            $select1 = $db->select()
            ->from(array('cr'=>'coi_au_requirements'))
            ->where('cr.Building_ID = ?', $select_build_id);
            $databyid = $db->fetchAll($select1);
            if(!empty($databyid)){
                $select = $select->where('car.Building_ID = ?', $select_build_id);
            }
        }
        $wvv='Automobile';
        $select = $select->where("coi_vt_defaults_tab like '%" . $wvv . "%'")->group('cvd.coi_vt_default_ID');

        
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
	
	public function GetAllUmbrellaRequirment($select_build_id = "") {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $select = $db->select()
                ->from(array('cvd' =>'coi_vt_defaults'))
				->joinLeft(array('car' => 'coi_au_requirements'), 'cvd.coi_vt_default_ID = car.coi_vt_default_ID');
        if ($select_build_id != "") {
            $select1 = $db->select()
            ->from(array('cr'=>'coi_au_requirements'))
            ->where('cr.Building_ID = ?', $select_build_id);
            $databyid = $db->fetchAll($select1);
            if(!empty($databyid)){
                $select = $select->where('car.Building_ID = ?', $select_build_id);
            }
        }
          $wvv='Umbrella';
         $select = $select->where("coi_vt_defaults_tab like '%" . $wvv . "%'")->group('cvd.coi_vt_default_ID');

        
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
	
	public function GetAllWorkersRequirment($select_build_id = "") {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $select = $db->select()
                ->from(array('cvd' =>'coi_vt_defaults'))
				->joinLeft(array('car' => 'coi_au_requirements'), 'cvd.coi_vt_default_ID = car.coi_vt_default_ID');
        if ($select_build_id != "") {
            $select1 = $db->select()
            ->from(array('cr'=>'coi_au_requirements'))
            ->where('cr.Building_ID = ?', $select_build_id);
            $databyid = $db->fetchAll($select1);
            if(!empty($databyid)){
                $select = $select->where('car.Building_ID = ?', $select_build_id);
            }
        }
          $wvv='Workers';
         $select = $select->where("coi_vt_defaults_tab like '%" . $wvv . "%'")->group('cvd.coi_vt_default_ID');

        
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
	
    public function loadrequirementTemplate($id) {

        $select = $this->select()
		          ->from(array('car' =>'coi_au_requirements'))
				  ->join(array('cvd' => 'coi_vt_defaults'), 'car.coi_vt_default_ID = cvd.coi_vt_default_ID');

        if ($id!= '') {
            $select = $select->where('coi_au_requir_ID=?', $id)
			        ->setIntegrityCheck(false);
        }
        
        $res = $this->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res->toArray() : false;
    }
    public function updaterequirement($data, $id)
    {


        try {
            $where = $this->getAdapter()->quoteInto('coi_au_requir_ID = ?', $id);
            $this->_errorMessage = "";
           // $data['updated_date'] = date('Y-m-d H:i:s');
           
            return $this->update($data, $where);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }
   public function deleteCoi($buildingID) {
        if (!empty($buildingID) && $buildingID != 0) {
            try {
                $this->delete('Building_ID = ' . $buildingID);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }
    public function getReportByBId($bId){
		
		if(!empty($bId)){
		    $select = $this->select();        
            $select = $select->where('Building_ID = ? ', $bId );
           			
            $res = $this->fetchAll( $select );        
            return ($res && sizeof($res)>0)? $res->toArray() : false ;
        }else
        return false;
	}
	public function getRequirementData($id) {

        $db = Zend_Db_Table::getDefaultAdapter();
        
        $select = $db->select()
                ->from(array('cvd' =>'coi_vt_defaults'))
				->joinLeft(array('car' => 'coi_au_requirements'), 'cvd.coi_vt_default_ID = car.coi_vt_default_ID');
        if ($id != "") {
            		 $select = $select->where('Building_ID = ?', $id);
        }
                  
        $res = $db->fetchAll($select);
        return ($res && sizeof($res) > 0) ? $res : false;
    }
}