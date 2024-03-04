<?php

ob_start();

class CoiController extends Ve_Controller_Base {

    public function init() {
        parent::init();
        $this->_helper->layout()->setLayout('newlayout');
        $this->accessHelper = $this->_helper->access;
    }

    public function indexAction() {
        $templateName = "";
        $designationName = "";
        $data = $this->_request->getPost();
        $template = new Model_CioTemplate();
        $templatedata = array();         
        $tempdata= $template->GetAllTemplatecoName();
        $tempdatasecond= $template->GetAllTemplatecoNamesecontab();
        $templatteumbrella=$template->GetAllTemplatecoUmbrella();
        $templatteWorkers=$template->GetAllTemplatecoWorkers();
        
        $this->view->templatedetails = $tempdata;
        $this->view->templatedetailsseconnd =$tempdatasecond;
        $this->view->templatedetailsthird=$templatteumbrella; 
        $this->view->templatteWorkers=$templatteWorkers;
    }
    public function editecioAction(){
        $query = $this->_request->getParams();
        if(isset($query['id'])){
            $eioedite = new Model_CioTemplate();
            $editeciodata = $eioedite->loadcioTemplate($query['id']);
            $this->view->editeciodata = $editeciodata[0];
        }else{
            $this->_redirect('/coi');
        }
    }

    public function updatecioAction(){
        $query = $this->_request->getParams();
        $id = $query['id'];
        if (isset($query['id'])) {
            $updateData = array();
            $updateData['coi_vt_defaults_Tenant'] = str_replace(',', '', $query['coi_vt_defaults_Tenant']);
            $updateData['coi_vt_defaults_Vendor'] = str_replace(',', '', $query['coi_vt_defaults_Vendor']);
            $updateData['coi_vt_default_description'] = $query['coi_vt_default_description'];
           
            $ciomodel = new Model_CioTemplate();
            $res = $ciomodel->updatecio($updateData, $id);
            $this->_redirect('/coi');
        }else{
            $this->_redirect('/coi');
        }
    }

// Coi Requirement code start Here 

    public function requirementsAction() {
        $templateName = "";
        $designationName = "";
        $data = $this->_request->getPost();
		
		$user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $cust_id = $_SESSION['Zend_Auth']['storage']->cust_id;		
        $role_id = $_SESSION['Zend_Auth']['storage']->role_id;
		
        $companyListing = '';
        $buildingMapper = new Model_Building();
        $rModel = new Model_Report();
        if($role_id == '9'){
            $companyListing = $buildingMapper->getCompanyBuilding($cust_id);
        }else{
            $user_build_mod = new Model_UserBuildingModule();
            $buildinglists = $user_build_mod->getUserBuildingIds($user_id);
            if($buildinglists){
                $build_id_array = array();
                foreach ($buildinglists as $buildlist)
                    $build_id_array[] = $buildlist['building_id'];
                $companyListing = $buildingMapper->getBuildingList($build_id_array);
            }
        }
        foreach ($companyListing as $cl) {
            $buildIds[] = $cl['build_id'];
        }

        $build_ID = $this->_getParam('bid', '');
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds))) {
            $build_ID = $_COOKIE['build_cookie'];
        } else {
            setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        }

        if ($companyListing != '') {
            if ($build_ID != '')
                $select_build_id = $build_ID;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }
		
        $_SESSION['current_building'] = $select_build_id;
		
		
		
		$template = new Model_CioRequirement(); 
		$tempdata= $template->GetAllGeneralRequirment($select_build_id);		
        $tempdatasecond= $template->GetAllAutomobileRequirment($select_build_id);
        $templatteumbrella=$template->GetAllUmbrellaRequirment($select_build_id);
        $templatteWorkers=$template->GetAllWorkersRequirment($select_build_id);
        
		
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
        $this->view->templatedetails = $tempdata;
        $this->view->templatedetailsseconnd =$tempdatasecond;
        $this->view->templatedetailsthird=$templatteumbrella; 
        $this->view->templatteWorkers=$templatteWorkers;
        $this->view->roleId = $role_id;
        $this->view->acessHelper = $this->accessHelper;
		$this->view->userId = $user_id;
        //to set the access of Building Information
        $this->view->user_info_id = 33;
    }

public function editrequirementAction(){
		
        $query = $this->_request->getParams();		
        if(isset($query['id'])){
            $requirementedite = new Model_CioRequirement();
            $editerequirementdata = $requirementedite->loadrequirementTemplate($query['id']);
            $this->view->editequirementdata = $editerequirementdata[0];           
        }else{
            $this->_redirect('/coi/requirements');
        }
    }

public function updaterequirementAction()
{
    $query = $this->_request->getParams();
        $id = $query['id'];
           if(isset($query['id'])) {        
                $updateData = array();
                $updateData['coi_au_defaults_Tenant'] = str_replace(',', '', $query['coi_au_defaults_Tenant']);
                $updateData['coi_au_defaults_Vendor'] = str_replace(',', '', $query['coi_au_defaults_Vendor']);               

                $requirementmodel = new Model_CioRequirement();
				$res = $requirementmodel->updaterequirement($updateData, $id);
                $this->_redirect('/coi/requirements');
            }else{
                $this->_redirect('/coi/requirements');
            }
}

	//COI Details start Here

function shownewcoidetailsAction() {
        $this->_helper->layout()->setLayout('popuplayout');
        try {
            
        } catch (Exception $e) {
            $message['status'] = 'error';
            $message['msg'] = 'Error occurred during form submit.';
        }
    }

function createnewcoidetailsAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
			
		    
            if ($data['coi_au_details_holder'] == '' && $data['coi_au_details_specialterms'] == '') {
                $message['status'] = 'error';
                $message['msg'] = 'Fill the Form Properly';
            } else {
                try {
                     $cdModel = new Model_CoiDetails(); 
                     $buildingId = $_SESSION['current_building'];
		             if(!empty($buildingId)){
			         $buildingMapper = new Model_Building();
			         $getcostcenter = $buildingMapper->getcostcenterByBuildingId($buildingId);
			         $data['Building_ID'] = $buildingId;
			         $data['uniqueCostCenter'] = $getcostcenter[0]->uniqueCostCenter;					 
		             }
                   
						$insertid = $cdModel->insertCoidetails($data);					  
                        $message['status'] = 'success';
                        $message['msg'] = 'New Certificate Holder information has been saved successfully.';

                } catch (Exception $e) {
                    $message['status'] = 'error';
                    $message['msg'] = 'Error occurred during form submit.';
                }
            }
        }
        echo json_encode($message);
        exit(0);
    }
public function coidetailsAction()
{
	     $user_id = $_SESSION['Zend_Auth']['storage']->uid;
        $cust_id = $_SESSION['Zend_Auth']['storage']->cust_id;		
        $role_id = $_SESSION['Zend_Auth']['storage']->role_id;
		
        $companyListing = '';
        $buildingMapper = new Model_Building();
        $rModel = new Model_Report();
        if($role_id == '9'){
            $companyListing = $buildingMapper->getCompanyBuilding($cust_id);
        }else{
            $user_build_mod = new Model_UserBuildingModule();
            $buildinglists = $user_build_mod->getUserBuildingIds($user_id);
            if($buildinglists){
                $build_id_array = array();
                foreach ($buildinglists as $buildlist)
                    $build_id_array[] = $buildlist['building_id'];
                $companyListing = $buildingMapper->getBuildingList($build_id_array);
            }
        }
        foreach ($companyListing as $cl) {
            $buildIds[] = $cl['build_id'];
        }

        $build_ID = $this->_getParam('bid', '');
        if (empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds))) {
            $build_ID = $_COOKIE['build_cookie'];
        } else {
            setcookie('build_cookie', $build_ID, time() + (86400 / 24), "/");
        }

        if ($companyListing != '') {
            if ($build_ID != '')
                $select_build_id = $build_ID;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }
		
        $_SESSION['current_building'] = $select_build_id;
	    
        /* $parent_id = $this->_getParam('parent_id', 1);		
        $page = $this->_getParam('page', 1);		
        $pageObj = new Ve_Paginator(); */
        $cdModel = new Model_CoiDetails();		
        $coiDetails = $cdModel->getCoidetails($select_build_id);		
        
        $this->view->coiDetails = $coiDetails;
		$this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
		$this->view->acessHelper = $this->accessHelper;
		$this->view->roleId = $role_id;
		$this->view->userId = $user_id;
        //to set the access of Building Information
        $this->view->user_info_id = 34;
       
}
function editcoidetailsAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            if ($data['id'] == '') {
                $message['status'] = 'error';
            } else {
                try {
                    $cdModel = new Model_CoiDetails();		
                    $coiDetails = $cdModel->getcoidetailsById($data['id']);	
                    $this->view->coiDetails = $coiDetails[0];
                    $message['status'] = 'success';
                } catch (Exception $e) {
                    $message['status'] = 'error';
                }
            }
        }
    }

function updatecoidetailstestAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
			
			
            if ($data['coi_au_details_holder'] == '' && $data['coi_au_details_specialterms'] == '') {
                $message['status'] = 'error';
                $message['msg'] = 'Fill the Form Properly';
            } else {
                try {
                        $cdModel = new Model_CoiDetails(); 
						$insertid = $cdModel->updatecoidetils($data, $data['coi_au_details_ID']);					  
                        $message['status'] = 'success';
                        $message['msg'] = 'New Certificate Holder information has been updated successfully.';

                } catch (Exception $e) {
                    $message['status'] = 'error';
                    $message['msg'] = 'Error occurred during form submit.';
                }
            }
        }
        echo json_encode($message);
        exit(0);
    }	
	
function deletecoidetailsAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
            if ($data['cid'] == '') {
                $message['status'] = 'error';
            } else {
                try {
                    $cdModel = new Model_CoiDetails();
                    $deleteId = $cdModel->deleteCoiDetails($data['cid']);
                    $message['status'] = 'success';
                } catch (Exception $e) {
                    $message['status'] = 'error';
                }
            }
        }
        echo json_encode($message);
        exit(0);
    }	
}
?>