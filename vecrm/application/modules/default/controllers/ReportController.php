<?php

class ReportController extends Ve_Controller_Base
{

    public function init() 
    {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');  
    }
	// Call befor any action and check is user login or not
    public function preDispatch()
    {    	
		if (!Zend_Auth::getInstance()->hasIdentity()) $this->_redirect('/index');
		 $level=(Zend_Auth::getInstance()->getStorage()->read())?Zend_Auth::getInstance()->getStorage()->read()->role_id:'';    	     	
    	$this->userId=Zend_Auth::getInstance()->getStorage()->read()->uid;
    	$this->roleId=Zend_Auth::getInstance()->getStorage()->read()->role_id;
    	$this->cust_id=Zend_Auth::getInstance()->getStorage()->read()->cust_id;
    }
    
    public function workorderAction(){
		$companyListing ='';
		$buildingMapper=new  Model_Building();
		if($this->roleId=='9'){
			 $companyListing = $buildingMapper->getCompanyBuilding($this->cust_id);
			}else{
			$user_build_mod = new Model_UserBuildingModule();
			$buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
			if($buildinglists){
				$build_id_array = array();
				foreach($buildinglists as $buildlist)
				  $build_id_array[] = $buildlist['building_id'];
				$companyListing = $buildingMapper->getBuildingList($build_id_array);			
			}
	     }
	     
	    $page=$this->_getParam('page',1);
		$order=$this->_getParam('order','woId');
		$dir=$this->_getParam('dir','DESC'); 
	    $wolist = '';
		$build_ID = $this->_getParam('bid','');
		$select_build_id =$build_ID;
		/*********set building in cookie **********/
		$set_cookie = setcookie('build_cookie',$build_ID,time() + (86400/24), "/");
		
		//$_COOKIE['build_cookie'];
		
		$woModel = new Model_WorkOrder();
		$search_array = array();
		if(isset($_REQUEST['search_status']) && $_REQUEST['search_status']!='')
		 $search_array['search_status'] = $_REQUEST['search_status'];
		 
		 if(isset($_REQUEST['category_name']) && $_REQUEST['category_name']!='')
		 $search_array['category_name'] = $_REQUEST['category_name'];
		 
		 if(isset($_REQUEST['tenant_name']) && $_REQUEST['tenant_name']!='')
		 $search_array['tenant_name'] = $_REQUEST['tenant_name'];
		 
		 if(isset($_REQUEST['search_wo']) && $_REQUEST['search_wo']!='')
		 $search_array['search_wo'] = $_REQUEST['search_wo'];
		 
		 if(isset($_REQUEST['from_date']) && $_REQUEST['from_date']!='')
		 $search_array['from_date'] = date("Y-m-d",strtotime($_REQUEST['from_date']));
		 
		 if(isset($_REQUEST['to_date']) && $_REQUEST['to_date']!='')
		 $search_array['to_date'] = date("Y-m-d",strtotime($_REQUEST['to_date']));
		
		if($companyListing!=''){			
				if($build_ID==''){
					$buildIds = array();
					 foreach($companyListing as $cl){
						 $buildIds[] = $cl['build_id'];
					 }	
					  
					  $wolist = $woModel->getWorkOrderByBuilIds($buildIds,$order,$dir,$search_array);
				  }else{
					  $wolist = $woModel->getBuildingWorkOrder($build_ID,$order,$dir,$search_array);
				  }
		   
		 }
		 
		 $pageObj=new Ve_Paginator();
		 $paginator = $pageObj->fetchPageDataResult($wolist,$page,10);		 
		 $this->view->page = $page;
		 $view_type = $this->_getParam('view_type','line');
		 $this->view->custID = $this->cust_id;
		 $this->view->companyListing = $companyListing;
		 $this->view->select_build_id = $select_build_id;
		 $this->view->wolist = $paginator;
		 $this->view->order = $order;
		 $this->view->dir = $dir;
		 $this->view->view_type = $view_type;
	}// end of work report
    
    
    public function dlworkreportAction(){
		$companyListing ='';
		$buildingMapper=new  Model_Building();
		if($this->roleId=='9'){
			 $companyListing = $buildingMapper->getCompanyBuilding($this->cust_id);
			}else{
			$user_build_mod = new Model_UserBuildingModule();
			$buildinglists = $user_build_mod->getUserBuildingIds($this->userId);
			if($buildinglists){
				$build_id_array = array();
				foreach($buildinglists as $buildlist)
				  $build_id_array[] = $buildlist['building_id'];
				$companyListing = $buildingMapper->getBuildingList($build_id_array);			
			}
	     }
	     
	    $type=$this->_getParam('type','csv');
		$order=$this->_getParam('order','woId');
		$dir=$this->_getParam('dir','DESC');
		$build_ID = $this->_getParam('bid','');
		$search_array = array();
		$woModel = new Model_WorkOrder();
		if($companyListing!=''){
				
				$buildIds = array();
				 foreach($companyListing as $cl){
					 $buildIds[] = $cl['build_id'];
				 }	
				  
				  $wolist = $woModel->getWorkOrderByBuilIds($buildIds,$order,$dir,$search_array);

		   
		 }
		  $ssModel = new Model_ScheduleStatus();
		  $status_list = $ssModel->getScheduleStatus();
		  $status_array= array();
		  foreach($status_list as $sl){
				if($sl['ssID']!=8)
				$status_array[$sl['ssID']] = $sl['title'];
			}
			
		  $report_template = new Zend_View();
          $report_template->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/report/');
          $report_template->assign('wolist',  $wolist);
          $bodyText = $report_template->render('dlworkorder.phtml');
           
         if($type=='excel'){
			 $file_name = 'WOReport-'.date("Y-m-d"); 
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment;Filename=".$file_name.".xls");
            echo   $bodyText;	
	     }
	     if($type=='pdf'){
		        $file_name = 'WOReport-'.date("Y-m-d").'.pdf';				  
				require_once(APPLICATION_PATH . '/../library/Ve/tcpdf/tcpdf.php');
				$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				// set document information
				$pdf->SetCreator(PDF_CREATOR);
				$pdf->SetAuthor('Vocational');
				$pdf->SetTitle('Work Order Report');
				$pdf->SetSubject('PDF Report');
				$logo_image = BASEURL.'public/images/logo.png';

				// set default header data
				$pdf->SetHeaderData('logo.png', "40", 'Work Order Report', 'PDF Report');

				// set header and footer fonts
				$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
				$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

				// set default monospaced font
				//$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
                $pdf->SetFont('helvetica', '', 8, '', 'false'); 
				// set margins
				$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
				$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
				$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

				// set auto page breaks
				$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
				$pdf->AddPage();
				$pdf->writeHTML($bodyText, true, 0, true, 0);
				$pdf->lastPage();
				$pdf->Output($file_name, 'D');
		 
	      }
		 exit(0);
	}// end of download work report
	
	

}

