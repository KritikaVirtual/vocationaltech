<?php
class Ve_Paginator extends Zend_Paginator 
{

    private $_total_count;
	
    public function __construct() {
		
    }
    public function fetchPageData($model,$page,$countPerPage=10,$where=null,$order=null)
    {
		//$oder=id desc
    	$zend=new Zend_Db_Table();
    	// $select = $this->select();
	    //$result =  $model->getMapper()->getDbTable()->fetchAll($where, $order);
	    $result =  $model->fetchAll($where, $order);
	    
	    $this->setTotalCount(count($result));	    
	    $paginator = Ve_Paginator::factory($result);
	    $paginator->setItemCountPerPage($countPerPage);
	    $paginator->setCurrentPageNumber($page);
	   // echo '<pre>'; print_r($paginator);
	   //exit;
	    return $paginator;	
    }
	
    
    public function fetchPageDataModified($model,$page,$countPerPage=10,$where=null,$order=null)
    {
		
    	$zend=new Zend_Db_Table();
	    $result =  $model->fetchEntries();
	    $this->setTotalCount(count($result));	    
	    $paginator = Ve_Paginator::factory($result);
	    $paginator->setItemCountPerPage($countPerPage);
	    $paginator->setCurrentPageNumber($page);
	   // print_r($paginator);
	   // exit;
	    return $paginator;	
    }
    
    public function fetchPageDataRaw($sql,$page,$countPerPage=10)
    {
    	$db=Zend_Registry::get('db');
    	$result =  $db->fetchAll($sql);
	    $this->setTotalCount(count($result));	    
	    $paginator = Ve_Paginator::factory($result);
	    $paginator->setItemCountPerPage($countPerPage);
	    $paginator->setCurrentPageNumber($page);
	    return $paginator;
    }
    
    public function fetchPageDataResult($result,$page,$countPerPage=10)
    {
        $paginator ='';
        if($result!=false && !empty($result) && count($result)>0){
                $this->setTotalCount(count($result));   
                $paginator = Ve_Paginator::factory($result);
                $paginator->setItemCountPerPage($countPerPage);
                $paginator->setCurrentPageNumber($page);
        }

        return $paginator;
    }
    
    public function fetchPageDataResultNew($result,$count,$page,$countPerPage=10)
    {
        $paginator ='';
        if($result!=false && !empty($result) && $count>0){
                $this->setTotalCount($count);   
                $paginator = Ve_Paginator::factory($result);
                $paginator->setItemCountPerPage($countPerPage);
                $paginator->setCurrentPageNumber($page);
        }

        return $paginator;
    }
    
    public function getTotalCount()
    {
    	return $this->_total_count;
    }
    public function setTotalCount($total_count)
    {
    	$this->_total_count=$total_count;
    }
	
    public function fetchBlogData($sql,$page,$countPerPage=10)
    {
    	$db = Zend_Registry::get('db');
    	$result =  $db->fetchAll($sql);
		$filteredReult = array();
		
		if(count($result)>0)
		{
			foreach($result AS $blog)
			{
				//now check logged in user connection, permission from user to logged in user
				$userNs 			= new Zend_Session_Namespace('members');
				$loggedin_id		= $userNs->userId;
				
				$view_my_journal 	= false;
				
				/*
				$userM				= new Application_Model_User();
				$view_my_journal 	= $userM->checkUserPrivacySettings($blog->user_id, $loggedin_id, 4);
				*/
				//above code is commented by Mahipal on 19-jan-2011 as we don't need to check user permissions
				
				//check new blog permission
				$blogM				= new Application_Model_Blog();
				$view_my_journal	= $blogM->checkBlogPrivacySettings($blog->user_id, $loggedin_id, $blog->status);
				
				if($view_my_journal)
				{
					$filteredReult[] = $blog;
				}				 
			}
		}
		//echo "Total Blog==>".count($filteredReult);
	    $this->setTotalCount(count($filteredReult));
	    $paginator = Ve_Paginator::factory($filteredReult);
		$paginator->setItemCountPerPage($countPerPage);
	    $paginator->setCurrentPageNumber($page);
	    return $paginator;
    }
}
