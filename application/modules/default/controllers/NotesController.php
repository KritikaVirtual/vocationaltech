<?php

/**
 * Description of NotesController
 *
 * @author Brijesh Kumar
 */
class NotesController extends Ve_Controller_Base {
    
    public function init()  {
       parent::init();
       $this->_helper->layout()->setLayout('newlayout');  
       $this->notesModel = new Model_Notes();
       $this->nm = new Zend_Session_Namespace('notes_message');
       $this->accessHelper = $this->_helper->access;
       $this->note_location = 22;
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
    
    
    public function noteslistAction(){
		$nid = '';
		$notesList = $this->notesModel->getNotes($nid,$this->cust_id);
		$this->view->notesList = $notesList;
		$this->view->nm = $this->nm;
		$this->view->roleId = $this->roleId;
		$this->view->userId = $this->userId;
        $this->view->acessHelper = $this->accessHelper;        
        $this->view->note_location = $this->note_location;
		//Zend_Session::namespaceUnset("notes_message");
		
		
	}
	
	public function addnotesAction(){
		$data = $this->getRequest()->getPost();		
		if(isset($data['notes']) && $data['notes']!=''){
			$note_arr = array();
			$note_arr['notes'] = $data['notes'];
			$note_arr['status'] = $data['status'];
			$note_arr['cust_id'] = $this->cust_id;
			$insetNote = $this->notesModel->insertNotes($note_arr);
			$this->nm->success = 'Notes predifined Added successfully!';
		}else{
			$this->nm->error = 'Error occurred during add notes!';
		}
		$this->_redirect('/notes/noteslist/');
		exit(0);
	}
	
	public function editnotesAction(){
		$data = $this->getRequest()->getPost();		
		if(isset($data['notes']) && $data['notes']!=''){
			$note_arr = array();
			$note_arr['notes'] = $data['notes'];
			$note_arr['status'] = $data['status'];
			$updateNote = $this->notesModel->updateNotes($note_arr,$data['nid']);
			echo 'true';
			$this->nm->success = 'Notes predefined Updated successfully!';
		}else{
			$this->nm->error = 'Error occurred during update notes!';
		}		
		exit(0);
	}
	
	public function deletenotesAction(){
		$data = $this->getRequest()->getPost();		
		if(isset($data['nid']) && $data['nid']!=''){
			$deletNote = $this->notesModel->deleteNotes($data['nid']);
			echo 'true';
			$this->nm->success = 'Notes predifined Deleted successfully!';
	     }		
		exit(0);
	}
    
}    
