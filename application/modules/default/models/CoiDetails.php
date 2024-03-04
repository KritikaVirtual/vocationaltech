<?php

/**
 * Description of Work Order
 *
 * @author brijesh
 */
class Model_CoiDetails extends Zend_Db_Table_Abstract {
	 protected $_name = 'coi_au_details';
     public $_errorMessage = '';

    public function insertCoidetails($data) {
        //print_r($data);
        try {
            $this->_errorMessage = "";
            return $this->insert($data);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }
        public function getcoidetailsById($id = null) {
        $db = Zend_Db_Table::getDefaultAdapter();
        try {
            $select = $db->select()
                    ->from(array('cad' => 'coi_au_details'))
                    ->where('cad.coi_au_details_ID=?', $id);
                        
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } catch (Exception $e) {
            return false;
        }
    }
	
	public function getCoidetails($select_build_id = null) {
        $db = Zend_Db_Table::getDefaultAdapter();
        try {
            $select = $db->select()
                    ->from(array('cad' => 'coi_au_details'))
                    //->joinLeft(array('com' => 'company'), 're.accounts = com.cust_id', array('companyName'))
                    //->joinInner(array('dash' => 'dashboard_menu'), 're.dashboard_menu = dash.did', array('menu_name'))
					->where('cad.Building_ID=?', $select_build_id);
                    //->where('cad.status=?', '1');
            
            
            //$select->order('cad.id DESC');
			//$sql = $select->__toString();
			//echo "<pre>";
			//print_r($sql);die;
            $res = $db->fetchAll($select);
            return ($res && sizeof($res) > 0) ? $res : false;
        } catch (Exception $e) {
            return false;
        }
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
    public function updatecoidetils($data, $id)
    {
     
	 try {
            $where = $this->getAdapter()->quoteInto('coi_au_details_ID = ?', $id);
            $this->_errorMessage = "";           
            return $this->update($data, $where);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

	public function deleteCoiDetails($cid) {
        if (!empty($cid) && $cid != 0) {
            try {
                $this->delete('id = ' . $cid);
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
}
