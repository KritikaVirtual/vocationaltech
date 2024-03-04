<?php

/**
 * Description of Material
 *
 * @author Brijesh
 */
class MaterialController extends Ve_Controller_Base {
   
   
    public function init()  {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');   
       $this->materialModel = new Model_Material();
       $this->mm = new Zend_Session_Namespace('material_message'); 
       $this->accessHelper = $this->_helper->access;
       $this->material_location = 16;        
    }// close of init function
    
    
    public function preDispatch()
    {    	
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/index');
        }
        
        $level=(Zend_Auth::getInstance()->getStorage()->read())? Zend_Auth::getInstance()->getStorage()->read()->role_id:'';                
        $this->userId=Zend_Auth::getInstance()->getStorage()->read()->uid;
        $this->roleId=Zend_Auth::getInstance()->getStorage()->read()->role_id;
        $this->cust_id = Zend_Auth::getInstance()->getStorage()->read()->cust_id;
        $this->email = Zend_Auth::getInstance()->getStorage()->read()->email;
    }
    
    public function indexAction(){
		$build_ID = $this->_getParam('bid','');		
		$order=$this->_getParam('order','description');
		$dir=$this->_getParam('dir','ASC');		
		$buildingMapper=new  Model_Building();
		$search_array= array();
		if($this->getRequest()->getMethod() == 'POST'){
                    $data = $this->getRequest()->getPost();
                    if($data['search_by']=="service"){
                            $search_array['search_by']= $data['search_by'];
                            $search_array['search_value']= $data['service_value'];
                    }
                    else if($data['search_by']=="vendor"){
                            $search_array['search_by']= $data['search_by'];
                            $search_array['search_value']= $data['vendor_value'];
                    }
                    else{
                            $search_array['search_by']= $data['search_by'];
                            $search_array['search_value']= $data['search_value'];
                    }			
		}
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
		
		if(empty($build_ID) && (isset($_COOKIE['build_cookie']) && in_array($_COOKIE['build_cookie'], $buildIds)))
                    $build_ID = $_COOKIE['build_cookie'];
		else
                    $set_cookie = setcookie('build_cookie',$build_ID,time() + (86400/24), "/");
		$materialList = false;
                $select_bid = '';
                if($build_ID!=''){
                    $select_bid = $build_ID;			   
                }else{ 
                    if($companyListing!=''){
                        $select_bid = $companyListing[0]['build_id'];
                    }
		}
		if(!empty($select_bid)){
                    $search_array = array_map("addslashes", $search_array);
                    $search_array = array_map("addslashes", $search_array);
                    $search_array = array_map("addslashes", $search_array);
                    $materialList = $this->materialModel->getMaterialByBid($select_bid,$order,$dir,$search_array);
                }
                $page = $this->_getParam('page', 1);
                $pageObj = new Ve_Paginator();
                $show = $this->_getParam('show', '');
                if($show==""){
                   $show=15; 
                }
                if($show!=='all'){                    
                    $materialList = $pageObj->fetchPageDataResult($materialList, $page, $show);      
                }else{
                    $materialList = $pageObj->fetchPageDataResult($materialList, $page, $show);
                }

                $this->view->show=$show;
                $mid = $this->_getParam('mid','');	 
                $this->view->materialList = $materialList;	 
                $this->view->select_build_id = $select_bid;
                $this->view->companyListing = $companyListing;
                $this->view->custID = $this->cust_id;
                $this->view->roleId     = $this->roleId;
                $this->view->mm = $this->mm;
                $this->view->mId = $mid;
                $this->view->search = $search_array;
                $this->view->order = $order;
                $this->view->dir = $dir;
                $this->view->acessHelper = $this->accessHelper;        
                $this->view->material_location = $this->material_location;
                $this->view->userId = $this->userId;

	}
	
	public function creatematerialAction(){
            $this->_helper->layout()->setLayout('popuplayout');	
            if($this->getRequest()->getMethod() == 'POST'){
                $data = $this->getRequest()->getPost();
                
                $submit_flag = true;
                if($data['description']=='' || $data['services']=='' || $data['cost']==''){
                        $submit_flag = false;
                }
                if($submit_flag){						
                    $mData = array();
                    $mData['date_created']=date('Y-m-d');
                    $mData['description']=$data['description'];
                    $mData['service']=$data['services'];
                    $mData['cost']=$data['cost'];
                    $mData['markup']=$data['markup'];
                    if(!empty($data['vendor'])){
                    $mData['vendor']= $data['vendor'];
                    }else{
                       $mData['vendor']= '0'; 
                    }
                    $mData['vendor_part']=$data['vendor_part'];
                    $mData['manufacturer']=$data['manufacturer'];
                    $mData['mfg']=$data['mfg'];			
                    $mData['buildingId']=$data['buildingId'];
                    $mData['global_template']=$data['global_template'];
                  
                    try{
                            $insertMaterial = $this->materialModel->insertMaterial($mData);
                            $this->mm->success = "Material Successfully Created!";
                            $this->_redirect('/material/index/bid/'.$data['buildingId']);
                    }catch(Exception $e){
                            echo $e->getMessage();
                            $this->mm->error = "Error Occurred During Creation of Material!";
                    }
                }else{
                    $build_ID = $data['buildingId'];
                    $this->view->build_id = $build_ID;
                    $this->mm->error = "Fill the form properly.";
                }
            }else{
                $build_ID = $this->_getParam('bid','');
            $this->view->build_id = $build_ID;
            }
	}// close create material method
	
	public function updatematerialAction(){
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
            {
                $id = $this->getRequest()->getPost('pk');
                $key = $this->getRequest()->getPost('name');
                $value = $this->getRequest()->getPost('value');
		$value = addslashes($value);
                $data= array(
                    $key => $value,
                    'updated_at'=> date('Y-m-d H:i:s')
                );
                if($key!='' && !empty($key)){        
                   $res = $this->materialModel->updateMaterial($data,$id);
                }
            }
            exit;
		
	} // close update material method
	
	
	
	public function loadmaterialAction(){
		$this->_helper->layout()->disableLayout();
		$data = $this->getRequest()->getPost();
		if(isset($data['mid']) && $data['mid']!=''){
                    $mdata = $this->materialModel->getMaterial($data['mid']);
                    $mdata[0] = array_map("stripslashes", $mdata[0]);
                    $this->view->mdata = $mdata[0];
                    $order=$this->_getParam('order','company_name');
		    $dir=$this->_getParam('dir','ASC');  
		    $search_array=array();
		    $vendorModel = new Model_Vendor();
		    $vendorList = $vendorModel->getVendorByBid($mdata[0]['buildingId'],$order,$dir,$search_array);                    
		    $this->view->vendorList =$vendorList;
		}else{
                    echo 'Error in the vendor id.';
		}
		
		$this->view->roleId     = $this->roleId;
		$this->view->userId = $this->userId;
		$this->view->acessHelper = $this->accessHelper;        
                $this->view->material_location = $this->material_location;
                
	}// close show material method
	
	
	
	
	
	public function removematerialAction(){
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$mid = $data['mid'];
			if($mid!=''){				
				try{					
					$vdelete = $this->materialModel->deleteMaterial($mid);
					echo 'true';
					$this->mm->success = "Material Successfully Deleted!";
				}catch(Exception $e){
					echo 'error';
				}
			}
		}
		exit(0);
	} // close remove material method	
	
	public function addvendorcontactAction(){
		$this->_helper->layout()->setLayout('popuplayout');   		
		
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$submit_flag = true;
			
			if($submit_flag){
				$vcData = array();
				if($data['altenatevendorid']!='') {
				    $vcModel = new Model_Vendor();
				    $vdata = $vcModel->getVendor($data['altenatevendorid']);
					
				    $vcData['material']= $this->_getParam('mid');
				    $vcData['vendor_id']= $data['altenatevendorid'];
				    //$vcData['part_number']= $data['part_number'];
					try{
					    $vcModel = new Model_MaterialVendor();
					    $insertContact = $vcModel->insertMaterialVendor($vcData);
					    $this->mm->success = "Alternate Vendor Successfully Created!";
					    //$this->_redirect('/material/index/bid/'.$data['building'].'/mid/'.$data['mid']);
						//$this->_redirect('/material/index/bid/'.$vdata[0]['buildingId'].'/mid/'.$vcData['material']);
						$json_data['msg']=$this->mm->success;
					    $json_data['url']='/material/index/bid/'.$vdata[0]['buildingId'].'/mid/'.$vcData['material'];
					    echo json_encode($json_data);
					    exit;
				      }catch(Exception $e){
					       $this->mm->error = "Error Occurred During Creation of Alternat Vendor Contact !";
				}
				
				} 
			}
		}
		
		$mid = $this->_getParam('mid');	
		
		if(!empty($mid)){
		   $materialData = $this->materialModel->getMaterial($mid);
		   $this->view->materialData = $materialData;
	    }	
	    $this->view->mid = $mid;
	}// close add vendor contact
	
	
	public function editvendorcontactAction(){
		$this->_helper->layout()->setLayout('popuplayout');   		
		$vcModel = new Model_MaterialVendor();
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$submit_flag = true;
			if($data['vendor_name']=='' || $data['contact']==''){
				$submit_flag = false;
				$this->mm->error = "Please fill all required field.";
				
			}
			if($submit_flag){
				$vcData = array();
				//$vcData['vid']= $data['vid'];
				$vcData['vendor_name']= $data['vendor_name'];
				$vcData['contact']= $data['contact'];
				$vcData['email']= $data['email'];
				$vcData['phone_number']= $data['phone_number'];
				$vcData['cell_number']= $data['cell_number'];
				$vcData['part_number']= $data['part_number'];
				try{
					$mvModel = new Model_MaterialVendor();
					$updateMVendor = $mvModel->updateMaterialVendor($vcData,$data['mvId']);
					$this->mm->success = "Alternate Vendor Contact Updated!";
					//$this->_redirect('/material/index/bid/'.$data['building'].'/mid/'.$data['mid']);
					$json_data['msg']=$this->mm->success;
					$json_data['url']='/material/index/bid/'.$data['building'].'/mid/'.$data['mid'];
					echo json_encode($json_data);
					exit;
				}catch(Exception $e){
					$this->mm->error = "Error Occurred During Updation of Alternat Vendor Contact !";
				}
			}
		}
		$mvId = $this->_getParam('mvId');
		if(!empty($mvId)){
		   $mvData = $vcModel->getMaterialVendor($mvId);
		   if($mvData){
			   $materialData = $this->materialModel->getMaterial($mvData[0]['material']);
		       $this->view->materialData = $materialData;
		   }
		   $this->view->mvData = $mvData;
	    }
	    $this->view->mvId = $mvId;
	}// close edit vendor contact
	
	public function removevcontactAction(){
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$mvId = $data['mvId'];
			if($mvId!=''){				
				try{
					$mvModel = new Model_MaterialVendor();
					$res = $mvModel->deleteVendorContact($mvId);
					$this->mm->success = "Alternate Vendor Contact Successfully Deleted!";
					echo 'true';
				}catch(Exception $e){
					echo 'error';
				}
			}
		}
		exit(0);
	} // close remove vendor contact
	
	
	public function vendorlistingmaterialAction(){
	        $this->_helper->layout()->disableLayout();
	        $order=$this->_getParam('order','company_name');
		    $dir=$this->_getParam('dir','ASC');
            $buliding_id=$this->_getParam('bid');			
		    $search_array=array();
		    $vendorModel = new Model_Vendor();
		    $vendorList = $vendorModel->getVendorByBid($buliding_id,$order,$dir,$search_array);
		    $this->view->vendorList =$vendorList;
			$this->view->bidajax =$buliding_id;
			$this->view->midajax =$this->_getParam('mid');
		
	} 
	
	public function checkvendorAction(){
	    $vid = $this->_getParam('altenatevendorselected');
		$mid = $this->_getParam('vendor_mid');
		if($vid !='' && $mid!= '') {
		    $mMaterialVendor =new Model_MaterialVendor();
			$vendorDetail = $mMaterialVendor->checkVendorByMid($vid,$mid);
			if(!empty($vendorDetail)) 
				echo 'true';
			else{
				echo 'false';
			} 
			
		}else {
		 echo 'true';
		 }
		 exit(0);
	}
	
	public function removevendorAction(){
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$vid = $data['vid'];
			$mid = $data['mid'];
			if($vid!='' && $mid!=''){				
				try{
					$vcModel = new Model_MaterialVendor();
					$vcList = $vcModel->deleteVendorByMidvid($vid,$mid);
					echo 'true';
					//$this->vm->success = "Vendor Successfully Deleted!";
				}catch(Exception $e){
					echo 'error';
				}
			}
		}
		exit(0);
	}
	
	public function creatematerialtemplateAction(){
       $this->_helper->layout()->setLayout('popuplayout');   		
		
		if($this->getRequest()->getMethod() == 'POST'){
			
			$data = $this->getRequest()->getPost();  
			if($data['vendor_type_text']!='') {
				$vmodel = new Model_Vendor();
				$vendordetails = $vmodel->getVendor($data['vendor']);
				$vendordetails = $vendordetails[0];
				$vdata = array();
				$vdata['company_name'] = $vendordetails['company_name'];
				$vdata['first_name'] = $vendordetails['first_name'];
				$vdata['last_name'] = $vendordetails['last_name'];
				$vdata['services'] = $vendordetails['services'];
				$vdata['contact_type'] = $vendordetails['contact_type'];
				$vdata['phone_number'] = $vendordetails['phone_number'];
				$vdata['cell_number'] = $vendordetails['cell_number'];
				$vdata['email'] = $vendordetails['email'];
				$vdata['account_number'] = $vendordetails['account_number'];
				$vdata['address1'] = $vendordetails['address1'];
				$vdata['address2'] = $vendordetails['address2'];
				$vdata['city'] = $vendordetails['city'];
				$vdata['state'] = $vendordetails['state'];
				$vdata['state_code'] = $vendordetails['state_code'];
				$vdata['postal_code'] = $vendordetails['postal_code'];
				$vdata['emergency_contact'] = $vendordetails['emergency_contact'];
				$vdata['status'] = $vendordetails['status'];
				$vdata['buildingId'] = $data['buildingId'];
				$vdata['notes'] = $vendordetails['notes'];
				$vdata['remove_status'] = $vendordetails['remove_status'];
				$vdata['import_template'] = 1;
				$insertVendor = $vmodel->insertVendor($vdata);
			}
			
			$submit_flag = true;
			//echo "<pre>";
			//print_r($data); die;
			
			if($data['description']=='' || $data['services']=='' || $data['cost']==''){
				$submit_flag = false;
			}
			
			if($submit_flag){						
				$mData = array();
				$mData['date_created']=date('Y-m-d');
				$mData['description']=$data['description'];
				//$mData['service']=$data['services'];
				$mData['cost']=$data['cost'];
				$mData['markup']=$data['markup'];
				if($data['vendor_type_text']!='') {
					$mData['vendor']=$insertVendor;
				} else {
					$mData['vendor']=$data['vendor'];
				}
				$mData['vendor_part']=$data['vendor_part'];
				$mData['manufacturer']=$data['manufacturer'];
				$mData['mfg']=$data['mfg'];			
				$mData['buildingId']=$data['buildingId'];
				$mData['import_template']=1;
				$mData['service']=$data['services'];
				
				try{
				
				if($data['service_type_text']!='') {
					$sarray = array();
					$sarray['service'] = addslashes($data['service_type_text']);
					$sarray['building'] = $data['buildingId'];
					try{
						$serviceModel = new Model_Services();
						$existService = $serviceModel->checkServiceByName($data['service_type_text'],$data['buildingId']);
					if(!$existService){
						$insert_service = $serviceModel->insertServices($sarray);
						 $mData['service']=$insert_service; 
						
					 }
					}catch(Exception $e){
						echo $e->getMessage();
						$this->vm->error = "Error Occurred During Creation of Vendor!";
					}
				}
				
				
					$insertMaterial = $this->materialModel->insertMaterial($mData);
					$this->mm->success = "Material Successfully Created!";
					$this->_redirect('/material/index/bid/'.$data['buildingId']);
				}catch(Exception $e){
					echo $e->getMessage();
					$this->mm->error = "Error Occurred During Creation of Material!";
				}
			}else{
				$build_ID = $data['buildingId'];
                $this->view->build_id = $build_ID;
				$this->mm->error = "Fill the form properly.";
			}
			
		}else{
			$build_ID = $this->_getParam('bid','');
            $this->view->build_id = $build_ID;
			$this->view->cust_id = $this->cust_id;
		}
	}// close create material method
	
	
	public function getmaterialAction() {
		$this->_helper->layout()->disableLayout();
		if($this->getRequest()->getMethod() == 'POST'){
			$data = $this->getRequest()->getPost();
			$materialModel=new Model_Material();
			$materialDetails=$materialModel->getMaterial($data['mid']);
			$json_data=$materialDetails[0];
			$json_data['msg'] = 'success';
			echo json_encode($json_data);
			exit(0);
			
		} else {
			echo json_encode(array('msg'=>'error'));
		}
	}
	
	
}// close class
