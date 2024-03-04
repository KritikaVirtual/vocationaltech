<?php

/**
 * Description of Coi List Controller
 *
 * @author Haseeb Alam
 */
class CoilistController extends Ve_Controller_Base {
    
    public function init()  {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');  
       $this->notesModel = new Model_Notes();
       $this->nm = new Zend_Session_Namespace('bserv_message');
       $this->accessHelper = $this->_helper->access;
        
    }
    
    // Call befor any action and check is user login or not
    public function preDispatch()
    {    	
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/index');
        }
        
        $level=(Zend_Auth::getInstance()->getStorage()->read())? Zend_Auth::getInstance()->getStorage()->read()->role_id:'';    	     	
    	$this->userId=Zend_Auth::getInstance()->getStorage()->read()->uid;
    	$this->roleId=Zend_Auth::getInstance()->getStorage()->read()->role_id;
    	$this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
    }
    
    
    public function indexAction(){
        $search_array = array();
        if($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
			
            $search_array['search_value'] = $data['search_value'];
			
            $this->view->search = $search_array;
        }
        $tenant_order = $this->_getParam('tenant_order', 'ASC');
        $tenant_dir = $this->_getParam('tenant_dir', 'tenantName');		
        $this->view->tenant_order = $tenant_order;
        $this->view->tenant_dir = $tenant_dir;
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
        foreach($companyListing as $cl){
            $buildIds[] = $cl['build_id'];
        }

        $select_build_id = $this->_getParam('bid','');		
        if(empty($select_build_id) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $select_build_id = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie',$select_build_id,time() + (86400/24), "/");
        if($companyListing!=''){
            if($select_build_id!='')
                $select_build_id = $select_build_id;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }

        $page = $this->_getParam('page', 1);
        $pageObj = new Ve_Paginator();
        $show = $this->_getParam('show', '');
        if($show==""){
           $show=10; 
        }
       		
        //$order ='';
		$tModel = new Model_Tenant();
        $bsList = $tModel->getTenantByBId($select_build_id, $search_array, $tenant_order);	
		

		$cModel = new Model_CoiList();
		$ListAll = $cModel->countAll($select_build_id);
		$listOneMonth = $cModel->countOneMonth($select_build_id);
		$listTwoMonth = $cModel->countTwoMonth($select_build_id);
		$listThreeMonth = $cModel->countThreeMonth($select_build_id);
		$listAfterThreeMonth = $cModel->countAfterThreeMonth($select_build_id);
		$listAgoAll = $cModel->countAgoAll($select_build_id);
		$listNoCoiAll = $cModel->countNoCOI($select_build_id);
		$listNoCoiData = $cModel->AllNoCOI($select_build_id, $tenant_order);
		$listagoexpireData = $cModel->AllAgoExpire($select_build_id, $tenant_order);
		$listoneexpireData = $cModel->AllonemonthExpire($select_build_id, $tenant_order);
		$listTwoexpireData = $cModel->AlltwomonthExpire($select_build_id, $tenant_order);
		$listThreeexpireData = $cModel->AllthreemonthExpire($select_build_id, $tenant_order);
		$listAfterThreeexpireData = $cModel->AllafterThreemonthExpire($select_build_id, $tenant_order);
	  
		
        if($show!='all'){            
            $bsList = $pageObj->fetchPageDataResult($bsList, $page, $show);
            		
        }else{
            $bsList = $pageObj->fetchPageDataResult($bsList, $page, $show);
			
        }
        
        $this->view->show=$show;
        $this->view->bsList = $bsList;
		$this->view->ListAll = $ListAll;
		$this->view->listAgoAll = $listAgoAll;
		$this->view->listNoCoiAll = $listNoCoiAll;
		$this->view->listOneMonth = $listOneMonth;
		$this->view->listTwoMonth = $listTwoMonth;
		$this->view->listThreeMonth = $listThreeMonth;
		$this->view->listAfterThreeMonth = $listAfterThreeMonth;
		$this->view->listNoCoiData = $listNoCoiData;
		$this->view->listagoexpireData = $listagoexpireData;
		$this->view->listoneexpireData = $listoneexpireData;
		$this->view->listTwoexpireData = $listTwoexpireData;
		$this->view->listThreeexpireData = $listThreeexpireData;
		$this->view->listAfterThreeexpireData = $listAfterThreeexpireData;
        $this->view->custID = $this->cust_id; 
        $this->view->roleId = $this->roleId;
        $this->view->acessHelper = $this->accessHelper;
		$this->view->userId = $this->userId;
        //to set the access of Building Information
        $this->view->user_info_id = 32;
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
               
    }// close index
	
	public function addserviceAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		$tenantId = $data['tId'];
		if(!empty($tenantId)){
		$tModel = new Model_Tenant();
        $bsList = $tModel->getTenantNameById($tenantId);
		}
		
		$this->view->bsList= $bsList;
		
	}// close add service
	
	public function saveserviceAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			
			 if($data['tenant_Id']== '' || $data['coi_au_date_to']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';				 
			 }else{				 
				    
				    $clModel = new Model_CoiList();
				    if(!empty($data['building_id'])){
				    $uniquecost = $clModel->getUniqueCostByBId($data['building_id']);
				    $data['uniquecostcenter'] = $uniquecost[0]->uniqueCostCenter;
				    }
					//Date formate change
                    $data['coi_au_date_to'] = date("Y-m-d", strtotime($data['coi_au_date_to']));
					
                    //	PDF upload Code Here				
                    $filename = basename($_FILES['file']['name']['equipmentmenual']);
					if(!empty($filename)){
				    $uploaddir = IMAGE_UPLOAD_DIR . '/public/coi/';
					$fileNewName = $data['tenant_Id'].".".pathinfo($filename, PATHINFO_EXTENSION);							
					$da = move_uploaded_file($_FILES['file']['tmp_name']['equipmentmenual'], $uploaddir . '' . $fileNewName);
                    $data['coi_au_pdf_upload'] = $fileNewName;
					}
					
					try{
						$submitBuildingService = $clModel->insertBuildService($data);						
						$message['status'] = 'success';
				        $message['msg']='COI List save successfully.';
					 }catch(Exception $e){
					    $message['status'] = 'error';
				        $message['msg']='Error Occurred during the save labor charge';
					 }
				 
			 }
			 
			echo json_encode($message);
		 }
		 exit(0);
	}// close save service
	
	public function editserviceAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		//$this->view->bsid= $data['bsid'];
		$this->view->bId= $data['cid'];
		$cModel = new Model_CoiList();
        $serviceData = $cModel->geteditcoiList($data['cid']);
		$this->view->serviceData = $serviceData;
		
	}// close edit service
	
	public function updateserviceAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 
			 
			 if($data['coi_au_tenant_id']== '' || $data['coi_au_date_to']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';				 
			 }else{
				    $cModel = new Model_CoiList();
                    $fileData = $cModel->getcoiList($data['coi_au_tenant_id']);	
            				
                     //	PDF upload Code Here				
                    $filename = basename($_FILES['file']['name']['equipmentmenual']);
					if(!empty($filename)){
					$uploaddir = IMAGE_UPLOAD_DIR . '/public/coi/';
					
					//delete PDF from the folder Code Here
					$file_name = $fileData[0]['coi_au_pdf_upload'];
                        $uploadfile = $uploaddir . '' . $file_name;
						
                        if (file_exists($uploadfile)) {
                            unlink($uploadfile);
                        }					
					$fileNewName = $data['tenant_Id'].".".pathinfo($filename, PATHINFO_EXTENSION);							
					$da = move_uploaded_file($_FILES['file']['tmp_name']['equipmentmenual'], $uploaddir . '' . $fileNewName);
                    $data['coi_au_pdf_upload'] = $fileNewName;
				    }else{
						$data['coi_au_pdf_upload'] = $fileData[0]['coi_au_pdf_upload'];
					}			
				    $data['coi_au_date_to'] = date("Y-m-d", strtotime($data['coi_au_date_to']));
				
					 
					 try{
						 $submitBuildingService = $cModel->updateBuildService($data,$data['coi_au_tenant_id']);
						 $message['status'] = 'success';
				         $message['msg']='Coi List Updated successfully.';
					 }catch(Exception $e){
					    $message['status'] = 'error';
				        $message['msg']='Error Occurred during the update Coi List';
					 }
				 
			 }
			 
			echo json_encode($message);
		 }
		 exit(0);
	}// close update service
	
	public function deletecoiAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 if($data['cid']!=''){
				 try{
					$cModel = new Model_CoiList();
                    $fileData = $cModel->getcoiList($data['cid']);					
                    if ($fileData) {
                        //$uploaddir = BASE_PATH . 'public/coi/';
						$uploaddir = IMAGE_UPLOAD_DIR . '/public/coi/';
                        $file_name = $fileData[0]['coi_au_pdf_upload'];
                        $uploadfile = $uploaddir . '' . $file_name;
						
                        if (file_exists($uploadfile)) {
                            unlink($uploadfile);
                        }
				    }
				     $deleteLabor = $cModel->deletecoiList($data['cid']);
				     echo 'true';				     
				    }catch(Exception $e){
					 echo 'false';
				 }
				 
			 }
		 }
		exit(0); 
	}// close delete service
	
// Vendor code start from Here
	
	public function vendorAction(){
        $search_array = array();
        if($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->getPost();
			
            $search_array['search_value'] = $data['search_value'];
			
            $this->view->search = $search_array;
        }
        $vendor_order = $this->_getParam('vendor_order', 'ASC');
        $vendor_dir = $this->_getParam('vendor_dir', 'vendorName');		
        $this->view->vendor_order = $vendor_order;
        $this->view->vendor_dir = $vendor_dir;
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
        foreach($companyListing as $cl){
            $buildIds[] = $cl['build_id'];
        }

        $select_build_id = $this->_getParam('bid','');		
        if(empty($select_build_id) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
            $select_build_id = $_COOKIE['build_cookie'];
        else
            $set_cookie = setcookie('build_cookie',$select_build_id,time() + (86400/24), "/");
        if($companyListing!=''){
            if($select_build_id!='')
                $select_build_id = $select_build_id;
            else
                $select_build_id = $companyListing[0]['build_id'];
        }

        $page = $this->_getParam('page', 1);
        $pageObj = new Ve_Paginator();
        $show = $this->_getParam('show', '');
        if($show==""){
           $show=10; 
        }
       		
        //$order ='';
		$vModel = new Model_Vendor();
        $bsList = $vModel->getVendorByCBId($select_build_id, $search_array, $vendor_order);	
		

		$cModel = new Model_CoiList();
		$ListAll = $cModel->countvendorAll($select_build_id);
		$listOneMonth = $cModel->countOneMonthVendor($select_build_id);
		$listTwoMonth = $cModel->countTwoMonthVendor($select_build_id);
		$listThreeMonth = $cModel->countThreeMonthVendor($select_build_id);
		$listAfterThreeMonth = $cModel->countAfterThreeMonthVendor($select_build_id);
		$listAgoAll = $cModel->countAgoAllVendor($select_build_id);
		$listNoCoiAll = $cModel->countvendorNoCOI($select_build_id);
		$listNoCoiData = $cModel->AllNoCOIVendor($select_build_id, $vendor_order);
		$listagoexpireData = $cModel->AllAgoExpireVendor($select_build_id, $vendor_order);
		$listoneexpireData = $cModel->AllonemonthExpireVendor($select_build_id, $vendor_order);
		$listTwoexpireData = $cModel->AlltwomonthExpireVendor($select_build_id, $vendor_order);
		$listThreeexpireData = $cModel->AllthreemonthExpireVendor($select_build_id, $vendor_order);
		$listAfterThreeexpireData = $cModel->AllafterThreemonthExpireVendor($select_build_id, $vendor_order);
	  
		
        if($show!='all'){            
            $bsList = $pageObj->fetchPageDataResult($bsList, $page, $show);
            		
        }else{
            $bsList = $pageObj->fetchPageDataResult($bsList, $page, $show);
			
        }
        
        $this->view->show=$show;
        $this->view->bsList = $bsList;
		$this->view->ListAll = $ListAll;
		$this->view->listAgoAll = $listAgoAll;
		$this->view->listNoCoiAll = $listNoCoiAll;
		$this->view->listOneMonth = $listOneMonth;
		$this->view->listTwoMonth = $listTwoMonth;
		$this->view->listThreeMonth = $listThreeMonth;
		$this->view->listAfterThreeMonth = $listAfterThreeMonth;
		$this->view->listNoCoiData = $listNoCoiData;
		$this->view->listagoexpireData = $listagoexpireData;
		$this->view->listoneexpireData = $listoneexpireData;
		$this->view->listTwoexpireData = $listTwoexpireData;
		$this->view->listThreeexpireData = $listThreeexpireData;
		$this->view->listAfterThreeexpireData = $listAfterThreeexpireData;
        $this->view->custID = $this->cust_id; 
       $this->view->acessHelper = $this->accessHelper;
		$this->view->roleId = $this->roleId;
		$this->view->userId = $this->userId;
        //to set the access of Building Information
        $this->view->user_info_id = 32;
        $this->view->companyListing = $companyListing;
        $this->view->select_build_id = $select_build_id;
               
    }// close vendor
	
	public function addvendorAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		$vendorId = $data['vId'];
		if(!empty($vendorId)){
		$vModel = new Model_Vendor();
        $bsList = $vModel->getVendorNameById($vendorId);
		}
		
		$this->view->bsList= $bsList;
		
	}// close add vendor
	
	public function savevendorAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			
			 if($data['tenant_Id']== '' || $data['coi_au_date_to']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';				 
			 }else{				 
				    
				    $clModel = new Model_CoiList();
				    if(!empty($data['building_id'])){
				    $uniquecost = $clModel->getUniqueCostByBId($data['building_id']);
				    $data['uniquecostcenter'] = $uniquecost[0]->uniqueCostCenter;
				    }
					//Date formate change
                    $data['coi_au_date_to'] = date("Y-m-d", strtotime($data['coi_au_date_to']));
					$data['coi_au_Ten_or_Vendor'] = 'V';
					
                    //	PDF upload Code Here				
                    $filename = basename($_FILES['file']['name']['equipmentmenual']);
					if(!empty($filename)){
				    $uploaddir = IMAGE_UPLOAD_DIR . '/public/coi/vendor/';
					$fileNewName = $data['tenant_Id'].".".pathinfo($filename, PATHINFO_EXTENSION);							
					$da = move_uploaded_file($_FILES['file']['tmp_name']['equipmentmenual'], $uploaddir . '' . $fileNewName);
                    $data['coi_au_pdf_upload'] = $fileNewName;
					}
					
					
					try{
						$submitBuildingService = $clModel->insertBuildService($data);						
						$message['status'] = 'success';
				        $message['msg']='COI List save successfully.';
					 }catch(Exception $e){
					    $message['status'] = 'error';
				        $message['msg']='Error Occurred during the save labor charge';
					 }
				 
			 }
			 
			echo json_encode($message);
		 }
		 exit(0);
	}// close save vendor
	
public function editvendorAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		//$this->view->bsid= $data['bsid'];
		$this->view->bId= $data['vid'];		
		$cModel = new Model_CoiList();
        $serviceData = $cModel->geteditcoivendorList($data['vid']);
		$this->view->serviceData = $serviceData;
		
	}// clo
	
public function updatevendorAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 $message = array();
			 
			 
			 if($data['coi_au_tenant_id']== '' || $data['coi_au_date_to']==''){
				 $message['status'] = 'error';
				 $message['msg']='Fill the Form Properly';				 
			 }else{
				    $cModel = new Model_CoiList();
                    $fileData = $cModel->getcoiList($data['coi_au_tenant_id']);	
            				
                     //	PDF upload Code Here				
                    $filename = basename($_FILES['file']['name']['equipmentmenual']);
					if(!empty($filename)){
					$uploaddir = IMAGE_UPLOAD_DIR . '/public/coi/vendor/';
					
					//delete PDF from the folder Code Here
					$file_name = $fileData[0]['coi_au_pdf_upload'];
                        $uploadfile = $uploaddir . '' . $file_name;
						
                        if (file_exists($uploadfile)) {
                            unlink($uploadfile);
                        }					
					$fileNewName = $data['tenant_Id'].".".pathinfo($filename, PATHINFO_EXTENSION);							
					$da = move_uploaded_file($_FILES['file']['tmp_name']['equipmentmenual'], $uploaddir . '' . $fileNewName);
                    $data['coi_au_pdf_upload'] = $fileNewName;
				    }else{
						$data['coi_au_pdf_upload'] = $fileData[0]['coi_au_pdf_upload'];
					}			
				    $data['coi_au_date_to'] = date("Y-m-d", strtotime($data['coi_au_date_to']));
				
					 
					 try{
						 $submitBuildingService = $cModel->updateBuildService($data,$data['coi_au_tenant_id']);
						 $message['status'] = 'success';
				         $message['msg']='Coi List Updated successfully.';
					 }catch(Exception $e){
					    $message['status'] = 'error';
				        $message['msg']='Error Occurred during the update Coi List';
					 }
				 
			 }
			 
			echo json_encode($message);
		 }
		 exit(0);
	}// close update service
	
public function deletecoivendorAction(){
		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		 {
			 $data = $this->_request->getPost();
			 if($data['vid']!=''){
				 try{
					$cModel = new Model_CoiList();
                    $fileData = $cModel->getcoiList($data['vid']);					
                    if ($fileData) {
                        //$uploaddir = BASE_PATH . 'public/coi/';
						$uploaddir = IMAGE_UPLOAD_DIR . '/public/coi/vendor/';
                        $file_name = $fileData[0]['coi_au_pdf_upload'];
                        $uploadfile = $uploaddir . '' . $file_name;
						
                        if (file_exists($uploadfile)) {
                            unlink($uploadfile);
                        }
				    }
				     $deleteLabor = $cModel->deletecoiList($data['vid']);
				     echo 'true';				     
				    }catch(Exception $e){
					 echo 'false';
				 }
				 
			 }
		 }
		exit(0); 
	}// close delete service
	
	public function sendcoivendoremailAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->getRequest()->getPost();
		
        $tenantId = $data['tid'];
		$coiId = $data['cid'];
         
       // $tenantUserModel = new Model_TenantUser();
        $vendor = new Model_Vendor();
		$coilistD = new Model_CoiList();        
		
        $vendorData = $vendor->getvendorById($tenantId);
       // $tenantData = $tenant->getTenantById($tenantId);
		$coilistDetail = $coilistD->getcoiList($coiId);
		    if(!empty($coilistDetail))
		    {
			   if(!empty($coilistDetail[0]['coi_au_date_to'])) {
									        
				    $date1 = date_create(date('Y-m-d'));
                    $date2 = date_create($coilistDetail[0]['coi_au_date_to']);
                    //difference between two dates
                    $diff = date_diff($date1,$date2);
					$cout = $diff->format("%a");
										
					if($cout >= 0 && $cout <= 30 ){ 
					$color = '#ffff00';
                    $value = 'Expires - '.$cout.' day';										
					}
					if($cout >= 31 && $cout <= 60 ){ 
					$color = '#ebf1df';
                    $value = 'Expires - '.$cout.' day';									
					}
					if($cout >= 61 && $cout <= 90 ){ 
					$color = '#92d050'; 
					$value = 'Expires - '.$cout.' day';
					}
					if($cout > 90 ){ 
					$color = '#00b050';
                    $value = 'Current';										
					}
					$agoCD = $date1 <= $date2;									
					if($agoCD != '1'){
					$diff = date_diff($date1,$date2);
					$cout = $diff->format("%a");	
					$color = '#ff0000';
                    $value = 'Expires - '.$cout.' day ago';
					}
			   }
			   $detail['status'] = $value;
			   $detail['expirationDate'] = date("d-m-Y", strtotime($coilistDetail[0]['coi_au_date_to']));
		    }
			  
               //$detail['tenantName'] = $vendorData[0]->first_name." ".$vendorData[0]->last_name;
               $detail['tenantName'] = $vendorData[0]->company_name;
               $detail['firstnamelastname'] = $vendorData[0]->first_name." ".$vendorData[0]->last_name;
                //$detail['TenAddress1'] = $vendorData[0]->address1;
                //$detail['TenAddress2'] = $vendorData[0]->address2;
                 $detail['city'] = $vendorData[0]->city;
               $detail['state'] = $vendorData[0]->state;
               $detail['postalCode'] = $vendorData[0]->postal_code; 
              
               
               $detail['email'] = $vendorData[0]->email;					
               $detail['address1'] = $vendorData[0]->address1;
               $detail['address2'] = $vendorData[0]->address2;
               $detail['suite'] = "";
               $detail['city'] = $vendorData[0]->city;
               $detail['state'] = $vendorData[0]->state;
               $detail['postalCode'] = $vendorData[0]->postal_code; 
        
        if (isset($vendorData[0]->buildingId)) {
            $building = new Model_Building();
            $buildingDetail = $building->getbuildingbyid($vendorData[0]->buildingId);
            if ($buildingDetail) {
			$buildingData = $buildingDetail[0];
            $detail['building_name'] = $buildingData['buildingName'];
			$detail['building_phoneNumber'] = $buildingData['phoneNumber'];
			$detail['building_address1'] = $buildingData['address'];
			$detail['building_address2'] = $buildingData['address2'];
			$detail['building_city'] = $buildingData['city'];
			$detail['building_state'] = $buildingData['state'];
			$detail['building_postalCode'] = $buildingData['postalCode'];
            }
			$Brequirement = new Model_CioRequirement();
            $buildingReqDetail = $Brequirement->getRequirementData($vendorData[0]->buildingId);
			if(!empty($buildingReqDetail)){	
				foreach($buildingReqDetail as $key=>$BR){					
					$detail[$key]['coi_vt_default_description'] = $BR->coi_vt_default_description;
					$detail[$key]['coi_au_defaults_Vendor'] = $BR->coi_au_defaults_Vendor;
					$detail[$key]['coi_vt_defaults_tab'] = $BR->coi_vt_defaults_tab;
				
				}
			}
			$coiDetails = new Model_CoiDetails();
            $buildingcoiDetails = $coiDetails->getReportByBId($vendorData[0]->buildingId);			
            if ($buildingcoiDetails) {
			$buildingcoiData = $buildingcoiDetails[0];
            $detail['certificate_holder'] = $buildingcoiData['coi_au_details_holder'];
			$detail['details_specialterms'] = $buildingcoiData['coi_au_details_specialterms'];    
			$detail['coi_au_details_send_certificate_to'] = $buildingcoiData['coi_au_details_send_certificate_to'];
            }
        }
        
        
        $res = $this->sendVendorMail($detail);
        
        if (count($res) == '1')
            echo true;
        else
            echo false;
        exit();
    }

	public function sendVendorMail($detail) {
         
        try {
            $email_data = $this->getCOIVendorTemplate($detail);			
		
            $mail = new Zend_Mail('utf-8');
            $mail->addTo($detail['email']);  
			// $mail->addTo('mark.lucas@voc-tech.com');
            // $mail->addTo('rob.palermo@voc-tech.com');
            $mail->addTo('durgeshchaubey@virtualemployee.com');
            //$mail->addTo('dadhikuriyal@virtualemployee.com');
            $esubject = $email_data['subject'];
            $econtent = $email_data['content'];
           
            $mail->setSubject($esubject);
	    
				$mail->setFrom('no-reply@visionworkorders.com','Vision Work Order');
				$return_path = new Zend_Mail_Transport_Sendmail('-fno-reply@visionworkorders.com');
			
			
            Zend_Mail::setDefaultTransport($return_path);
            $mail->setBodyHtml($econtent);
            if ($mail->send()){
                return true;
            }else{
                return false;
            }
         } catch (Exception $e) {
            return false;
        } 
    }

	public function getCOIVendorTemplate($detail) {
		
        $emailMapper = new Model_Email();
		$coi_temp = "COI Template";
		$temp_id= $emailMapper->getidbytemplatename($coi_temp);
        $loadTemplate = $emailMapper->loadEmailTemplate($temp_id[0]['id']);
		//echo "<pre>"; print_r($loadTemplate);die;
        if ($loadTemplate) {
            $emailTemplate = $loadTemplate[0];
            $subject = $emailTemplate['email_subject'];
            $content = $emailTemplate['email_content'];
            /*             * ****get Company Name ***** */
            $accoutMapper = new Model_Account();
            $company = $accoutMapper->getcompany($this->cust_id);
            $companyName = $company[0]['companyName'];            
             
            $header_data = $this->getHeaderData($company);
            $footer_data = $this->getFooterData();
			
			
            $roleManager = 'Company Admin';
            /* * ***change the key with value in the content *** */
            $currDate = date('F d, Y');

            // Change Email subject

            $subject = str_replace('[[++currDate]]', 'COI Template', $subject);
           
             
            // End Email subject

            ///// header 
            $content = str_replace('[[++companyLogo]]', $header_data['building_logo_src'], $content);
            $content = str_replace('[[++voctechLogo]]', $header_data['voctech_logo_src'], $content);
            $content = str_replace('[[++currDate]]', $header_data['date'], $content);
            $content = str_replace('[[++costNumber]]', $header_data['corp_account_number'], $content);
            ///// end header
			
            $content = str_replace('[[++currDate]]', $currDate, $content);
			$content = str_replace('[[++companyName]]', $companyName, $content);
            $content = str_replace('[[++buildingName]]', $detail['building_name'], $content);
            $content = str_replace('[[++buildingPhoneNumber]]', $detail['building_phoneNumber'], $content);
            $content = str_replace('[[++buildingAddress1]]', $detail['building_address1'], $content);
            $content = str_replace('[[++buildingAddress2]]', $detail['building_address2'], $content);
            $content = str_replace('[[++buildingCity]]', $detail['building_city'], $content);
			$content = str_replace('[[++buildingState]]', $detail['building_state'], $content);
			$content = str_replace('[[++buildingPostalCode]]', $detail['building_postalCode'], $content);
            $content = str_replace('[[++tenantName]]', $detail['tenantName'], $content);
            $content = str_replace('[[++TenAddress1]]', $detail['address1'], $content);
            $content = str_replace('[[++TenAddress2]]', $detail['address2'], $content);
            $content = str_replace('[[++TenCity]]', $detail['city'], $content);
            $content = str_replace('[[++TenState]]', $detail['state'], $content);
            $content = str_replace('[[++TenPostalCode]]', $detail['postalCode'], $content);
			$content = str_replace('[[++TenSuite]]', $detail['suite'], $content);
            
			
			$content = str_replace('[[++Status]]', $detail['status'], $content);
			$content = str_replace('[[++ExpDate]]', $detail['expirationDate'], $content);
			
			foreach($detail as $DR ){
			if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'Each Occurrence')
			  $content = str_replace('[[++GLEachOcc]]', number_format($DR['coi_au_defaults_Vendor']), $content);
			else if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'Damage to Rented Premises')
			  $content = str_replace('[[++GLDRP]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'Med EXP (Any One Person)')
			  $content = str_replace('[[++GLMedExp]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'Personal & ADV Injury')
			  $content = str_replace('[[++GLPersonal]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'General Aggregate')
			  $content = str_replace('[[++GLGeneral]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'Products - Comp/OP AGG')
			  $content = str_replace('[[++GLProducts]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'Employee Benefits')
			  $content = str_replace('[[++GLEmployeeBen]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Automobile Liability' && trim($DR['coi_vt_default_description']) == 'Combined Single Limit')
			  $content = str_replace('[[++ALComSingleLimit]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Automobile Liability' && trim($DR['coi_vt_default_description']) == 'Bodily Injury (Per person)')
			  $content = str_replace('[[++ALBodilyPerPerson]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Automobile Liability' && trim($DR['coi_vt_default_description']) == 'Bodily Injury (Per accident)')
			  $content = str_replace('[[++ALBodilyPerAccident]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Automobile Liability' && trim($DR['coi_vt_default_description']) == 'Property Damage (Per accident)')
			  $content = str_replace('[[++ALPropPerAccident]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Automobile Liability' && trim($DR['coi_vt_default_description']) == 'Underinsured motorist BI split')
			  $content = str_replace('[[++ALUnderinsuredMot]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Umbrella Liability' && trim($DR['coi_vt_default_description']) == 'Each Occurrence')
			  $content = str_replace('[[++ULEachOccurance]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Umbrella Liability' && trim($DR['coi_vt_default_description']) == 'Aggregate')
			  $content = str_replace('[[++ULAggregate]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Workers Compensation' && trim($DR['coi_vt_default_description']) == 'E.L. Each Accident')
			  $content = str_replace('[[++WCEachAccident]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Workers Compensation' && trim($DR['coi_vt_default_description']) == 'E.L. Disease - EA Employee')
			  $content = str_replace('[[++WCEachEmployee]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Workers Compensation' && trim($DR['coi_vt_default_description']) == 'E.L. Disease – Policy Limit')
			  $content = str_replace('[[++WCPolocyLimit]]', number_format($DR['coi_au_defaults_Vendor']), $content);
		    		    
			}
            
            $content = str_replace('[[++firstnamelastname]]', $detail['firstnamelastname'], $content);
			$content = str_replace('[[++CertificateHolder]]', $detail['certificate_holder'], $content);
			$content = str_replace('[[++SpecialTerms]]', $detail['details_specialterms'], $content);
			$content = str_replace('[[++coi_au_details_send_certificate_to]]', $detail['coi_au_details_send_certificate_to'], $content);
			
			///// Footer 
            $content = str_replace('[[++footerInfo]]', $footer_data['footer_info'], $content);
            ///// End Footer
			
			
            return array('content' => $content, 'subject' => $subject);
        } else
            return false;
    }
	

    public function sendcoiemailAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = $this->getRequest()->getPost();
		
        $tenantId = $data['tid'];
		$coiId = $data['cid'];
         
        $tenantUserModel = new Model_TenantUser();
        $tenant = new Model_Tenant();
		$coilistD = new Model_CoiList();        
		
        $userData = $tenantUserModel->getTenantUserById($tenantId);
       
        $tenantData = $tenant->getTenantById($tenantId);
         //print_r($tenantData);
		$coilistDetail = $coilistD->getcoiList($coiId);
		    if(!empty($coilistDetail))
		    {
			   if(!empty($coilistDetail[0]['coi_au_date_to'])) {
									        
				    $date1 = date_create(date('Y-m-d'));
                    $date2 = date_create($coilistDetail[0]['coi_au_date_to']);
                    //difference between two dates
                    $diff = date_diff($date1,$date2);
					$cout = $diff->format("%a");
										
					if($cout >= 0 && $cout <= 30 ){ 
					$color = '#ffff00';
                    $value = 'Expires - '.$cout.' day';										
					}
					if($cout >= 31 && $cout <= 60 ){ 
					$color = '#ebf1df';
                    $value = 'Expires - '.$cout.' day';									
					}
					if($cout >= 61 && $cout <= 90 ){ 
					$color = '#92d050'; 
					$value = 'Expires - '.$cout.' day';
					}
					if($cout > 90 ){ 
					$color = '#00b050';
                    $value = 'Current';										
					}
					$agoCD = $date1 <= $date2;									
					if($agoCD != '1'){
					$diff = date_diff($date1,$date2);
					$cout = $diff->format("%a");	
					$color = '#ff0000';
                    $value = 'Expires - '.$cout.' day ago';
					}
			   }
			   $detail['status'] = $value;
			   $detail['expirationDate'] = date("d-m-Y", strtotime($coilistDetail[0]['coi_au_date_to']));
		    }
			  
               $detail['tenantName'] = $tenantData[0]->tenantName;
               // add by durgesh for username and firstnamelastname
                $detail['useName'] = $tenantData[0]->userName;
                $detail['firstnamelastname'] = $tenantData[0]->firstname." ".$tenantData[0]->lastname;
               
               $detail['email'] = $tenantData[0]->email;					
               $detail['address1'] = $tenantData[0]->address1;
               $detail['address2'] = $tenantData[0]->address2;
               $detail['suite'] = $tenantData[0]->suite;
               $detail['city'] = $tenantData[0]->city;
               $detail['state'] = $tenantData[0]->state;
               $detail['postalCode'] = $tenantData[0]->postalCode; 
        
        if (isset($tenantData[0]->buildingId)) {
            $building = new Model_Building();
            $buildingDetail = $building->getbuildingbyid($tenantData[0]->buildingId);
            if ($buildingDetail) {
			$buildingData = $buildingDetail[0];
            $detail['building_name'] = $buildingData['buildingName'];
			$detail['building_phoneNumber'] = $buildingData['phoneNumber'];
			$detail['building_address1'] = $buildingData['address'];
			$detail['building_address2'] = $buildingData['address2'];
			$detail['building_city'] = $buildingData['city'];
			$detail['building_state'] = $buildingData['state'];
			$detail['building_postalCode'] = $buildingData['postalCode'];
            }
			$Brequirement = new Model_CioRequirement();
            $buildingReqDetail = $Brequirement->getRequirementData($tenantData[0]->buildingId);
			if(!empty($buildingReqDetail)){	
				foreach($buildingReqDetail as $key=>$BR){					
					$detail[$key]['coi_vt_default_description'] = $BR->coi_vt_default_description;
					$detail[$key]['coi_au_defaults_Tenant'] = $BR->coi_au_defaults_Tenant;
					$detail[$key]['coi_vt_defaults_tab'] = $BR->coi_vt_defaults_tab;
				
				}
			}
			$coiDetails = new Model_CoiDetails();
            $buildingcoiDetails = $coiDetails->getReportByBId($tenantData[0]->buildingId);			
            if ($buildingcoiDetails) {
			$buildingcoiData = $buildingcoiDetails[0];
            $detail['certificate_holder'] = $buildingcoiData['coi_au_details_holder'];
			$detail['details_specialterms'] = $buildingcoiData['coi_au_details_specialterms'];   
			// add by durgesh for coi_au_details_send_certificate_to
			$detail['coi_au_details_send_certificate_to'] = $buildingcoiData['coi_au_details_send_certificate_to'];
            }
        }
        //print_r($detail);
        $res = $this->sendTenantMail($detail);
        
        
        if (count($res) == '1')
            echo true;
        else
            echo false;
        exit();
    }
	public function sendTenantMail($detail) {
         
        try {
            $email_data = $this->getCOITemplate($detail);			
		
            $mail = new Zend_Mail('utf-8');
            $mail->addTo($detail['email']);
            // $mail->addTo('mark.lucas@voc-tech.com');
            // $mail->addTo('rob.palermo@voc-tech.com');
			$mail->addTo('durgeshchaubey@virtualemployee.com');			
            $mail->addTo('dadhikuriyal@virtualemployee.com');		
            $esubject = $email_data['subject'];
            $econtent = $email_data['content'];
           
            $mail->setSubject($esubject);
	     	/*$setModel = new Model_Setting();
			$setData = $setModel->getSetting();
			
			if($setData){
				$setting = $setData[0];
				$mail->setFrom($setting['from_email'],$setting['from_name']);
				$return_path = new Zend_Mail_Transport_Sendmail('-f'.$setting['from_email']);
			}else{*/
				$mail->setFrom('no-reply@visionworkorders.com','Vision Work Order');
				$return_path = new Zend_Mail_Transport_Sendmail('-fno-reply@visionworkorders.com');
			//}
			
            Zend_Mail::setDefaultTransport($return_path);
            $mail->setBodyHtml($econtent);
            if ($mail->send()){
                return true;
            }else{
                return false;
            }
         } catch (Exception $e) {
            return false;
        } 
    }
	/**
     * Get COI Template
     */
    public function getCOITemplate($detail) {
		
        $emailMapper = new Model_Email();
		// durgesh chaubey 19 dec 2022 add function for gettemplete id by template name.
		$coi_temp = "COI Template";
		$temp_id= $emailMapper->getidbytemplatename($coi_temp);
        $loadTemplate = $emailMapper->loadEmailTemplate($temp_id[0]['id']);
        if ($loadTemplate) {
            $emailTemplate = $loadTemplate[0];
            $subject = $emailTemplate['email_subject'];
            $content = $emailTemplate['email_content'];
            /*             * ****get Company Name ***** */
            $accoutMapper = new Model_Account();
            $company = $accoutMapper->getcompany($this->cust_id);
            $companyName = $company[0]['companyName'];            
             
            $header_data = $this->getHeaderData($company);
            $footer_data = $this->getFooterData();
			
            $roleManager = 'Company Admin';
            /* * ***change the key with value in the content *** */
            $currDate = date('F d, Y');

            // Change Email subject

            $subject = str_replace('[[++currDate]]', 'COI Template', $subject);
           
             
            // End Email subject

            ///// header 
            $content = str_replace('[[++companyLogo]]', $header_data['building_logo_src'], $content);
            $content = str_replace('[[++voctechLogo]]', $header_data['voctech_logo_src'], $content);
            $content = str_replace('[[++currDate]]', $header_data['date'], $content);
            $content = str_replace('[[++costNumber]]', $header_data['corp_account_number'], $content);
            ///// end header
			
            $content = str_replace('[[++currDate]]', $currDate, $content);
			$content = str_replace('[[++companyName]]', $companyName, $content);
            $content = str_replace('[[++buildingName]]', $detail['building_name'], $content);
            $content = str_replace('[[++buildingPhoneNumber]]', $detail['building_phoneNumber'], $content);
            $content = str_replace('[[++buildingAddress1]]', $detail['building_address1'], $content);
            $content = str_replace('[[++buildingAddress2]]', $detail['building_address2'], $content);
            $content = str_replace('[[++buildingCity]]', $detail['building_city'], $content);
			$content = str_replace('[[++buildingState]]', $detail['building_state'], $content);
			$content = str_replace('[[++buildingPostalCode]]', $detail['building_postalCode'], $content);
			
			$content = str_replace('[[++tenantuseName]]', $detail['firstname'], $content);
            $content = str_replace('[[++tenantName]]', $detail['tenantName'], $content);
            
            $content = str_replace('[[++useName]]', $detail['useName'], $content);
            $content = str_replace('[[++firstnamelastname]]', $detail['firstnamelastname'], $content);
            
            $content = str_replace('[[++TenAddress1]]', $detail['address1'], $content);
            $content = str_replace('[[++TenAddress2]]', $detail['address2'], $content);
            $content = str_replace('[[++TenCity]]', $detail['city'], $content);
            $content = str_replace('[[++TenState]]', $detail['state'], $content);
            $content = str_replace('[[++TenPostalCode]]', $detail['postalCode'], $content);
			$content = str_replace('[[++TenSuite]]', $detail['suite'], $content);
            
			
			$content = str_replace('[[++Status]]', $detail['status'], $content);
			$content = str_replace('[[++ExpDate]]', $detail['expirationDate'], $content);
			
			foreach($detail as $DR ){
			if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'Each Occurrence')
			  $content = str_replace('[[++GLEachOcc]]', number_format($DR['coi_au_defaults_Tenant']), $content);
			else if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'Damage to Rented Premises')
			  $content = str_replace('[[++GLDRP]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'Med EXP (Any One Person)')
			  $content = str_replace('[[++GLMedExp]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'Personal & ADV Injury')
			  $content = str_replace('[[++GLPersonal]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'General Aggregate')
			  $content = str_replace('[[++GLGeneral]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'Products - Comp/OP AGG')
			  $content = str_replace('[[++GLProducts]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'General Liability' && trim($DR['coi_vt_default_description']) == 'Employee Benefits')
			  $content = str_replace('[[++GLEmployeeBen]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Automobile Liability' && trim($DR['coi_vt_default_description']) == 'Combined Single Limit')
			  $content = str_replace('[[++ALComSingleLimit]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Automobile Liability' && trim($DR['coi_vt_default_description']) == 'Bodily Injury (Per person)')
			  $content = str_replace('[[++ALBodilyPerPerson]]', $DR['coi_au_defaults_Tenant'], $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Automobile Liability' && trim($DR['coi_vt_default_description']) == 'Bodily Injury (Per accident)')
			  $content = str_replace('[[++ALBodilyPerAccident]]', $DR['coi_au_defaults_Tenant'], $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Automobile Liability' && trim($DR['coi_vt_default_description']) == 'Property Damage (Per accident)')
			  $content = str_replace('[[++ALPropPerAccident]]', $DR['coi_au_defaults_Tenant'], $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Automobile Liability' && trim($DR['coi_vt_default_description']) == 'Underinsured motorist BI split')
			  $content = str_replace('[[++ALUnderinsuredMot]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Umbrella Liability' && trim($DR['coi_vt_default_description']) == 'Each Occurrence')
			  $content = str_replace('[[++ULEachOccurance]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Umbrella Liability' && trim($DR['coi_vt_default_description']) == 'Aggregate')
			  $content = str_replace('[[++ULAggregate]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Workers Compensation' && trim($DR['coi_vt_default_description']) == 'E.L. Each Accident')
			  $content = str_replace('[[++WCEachAccident]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Workers Compensation' && trim($DR['coi_vt_default_description']) == 'E.L. Disease - EA Employee')
			  $content = str_replace('[[++WCEachEmployee]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    else if(trim($DR['coi_vt_defaults_tab']) == 'Workers Compensation' && trim($DR['coi_vt_default_description']) == 'E.L. Disease – Policy Limit')
			  $content = str_replace('[[++WCPolocyLimit]]', number_format($DR['coi_au_defaults_Tenant']), $content);
		    		    
			}
            
			$content = str_replace('[[++CertificateHolder]]', $detail['certificate_holder'], $content);
			$content = str_replace('[[++SpecialTerms]]', $detail['details_specialterms'], $content);
			// add by durgesh for coi_au_details_send_certificate_to
			$content = str_replace('[[++coi_au_details_send_certificate_to]]', $detail['coi_au_details_send_certificate_to'], $content);
			
			///// Footer 
            $content = str_replace('[[++footerInfo]]', $footer_data['footer_info'], $content);
            ///// End Footer
			
			
            return array('content' => $content, 'subject' => $subject);
        } else
            return false;
    }
public function getHeaderData($company) {
		
        $uri = BASEURL;
        /* * *****Get voc-tech logo******* */
        $sdModel = new Model_SystemDefault();
        $sdData = $sdModel->getSystemDefault();
        $emailContent = $sdData[0];
        $voc_logo = $emailContent['voc_logo'];

        if (isset($voc_logo) && !empty($voc_logo)) {
            $voctech_logo_src = '<img src="' . $uri . 'public/images/uploads/' . $voc_logo . '" style="max-height:150px">';
        } else {
            $voctech_logo_src = "";
        }

        /* * *****Get Company Data******* */

        $accData = $company;
        $aData = $accData[0];

        $building_logo_src = "";

        // Company logo
        if (isset($aData['company_logo']) && !empty($aData['company_logo'])) {
            $building_logo_src = '<img src="' . $uri . 'public/images/clogo/' . $aData['company_logo'] . '" style="max-height:150px" >';
        } else {
            //$building_logo_src	=	'<img src="'.$uri.'/public/images/logo.png">';				
            $building_logo_src = '';
        }

        $data['building_logo_src'] = $building_logo_src;
        $data['voctech_logo_src'] = $voctech_logo_src;
        $data['corp_account_number'] = $aData['corp_account_number'];
        $data['date'] = $this->getDateFormat();		
        return $data;
    }
public function getFooterData() {
        $uri = BASEURL;
        /*         * *****Get voc-tech logo******* */
        $sdModel = new Model_SystemDefault();
        $sdData = $sdModel->getSystemDefault();
        $emailContent = $sdData[0];
        $footer_info = $emailContent['footer_info'];
        $emailSubject = $emailContent['subject'];

        $data['footer_info'] = $footer_info;
        //$data['subject']		=	$emailSubject;
        return $data;
}

public function getDateFormat($data = null) {
        if ($data == null)
            $data = date("Y-m-d h:i:s");

        //return date("Y-m-d h:i:s", strtotime($data));
		return date("F-d-Y", strtotime($data));
}

	
}    
