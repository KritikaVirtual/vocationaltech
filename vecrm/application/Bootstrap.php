<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	
	protected function _initPlugins()
	{
			// Access plugin
			$front = Zend_Controller_Front::getInstance(); 
			$front->registerPlugin(new application_plugins_Layoutsplugin());
			
	}
	
	
	protected function _initAutoLoad()
	{
		Zend_Loader_Autoloader::getInstance()->registerNamespace('Servicefreak'); 
		$autoloader = new Zend_Application_Module_Autoloader(array(
			'basePath'  => APPLICATION_PATH,
			'namespace' => '',
		 ));
		 $autoloader->addResourceType('form', 'modules/default/forms/', 'Form');
		 $autoloader->addResourceType('plugin', 'modules/default/plugins/', 'Plugin');
		 $autoloader->addResourceType('model', 'modules/default/models/', 'Model');
  	     return $autoloader;
  	}

  	/**
	 * Initialize helpers path
	 */
	protected function _initHelper(){
	    Zend_Controller_Action_HelperBroker::addPath(
	    APPLICATION_PATH .'/modules/default/controllers/helpers');
	}
	
 	public function _initCustomRoute()
	{
    	
    	$router = Zend_Controller_Front::getInstance()->getRouter();
		
		
    	$route = new Zend_Controller_Router_Route('admin', array(
        	'module'     => 'admin',
        	'controller' => 'index',
    		'action'	=> 'index'
    	));
		$router->addRoute('signup2', $route);		
		
    	$route = new Zend_Controller_Router_Route('registration', array(
        	'module'     => 'default',
        	'controller' => 'index',
    		'action'	=> 'registration'
    	));
		$router->addRoute('signup', $route);
		
    	$route = new Zend_Controller_Router_Route('design', array(
        	'module'     => 'default',
        	'controller' => 'design',
    		'action'	=> 'index'
    	));		
    	$router->addRoute('des', $route);	 		
    		 
    	$router = Zend_Controller_Front::getInstance()->getRouter();
    	$route = new Zend_Controller_Router_Route('logout', array(
        	'module'     => 'default',
        	'controller' => 'index',
    		'action'	=> 'logout'
    	));
    	$router->addRoute('logout', $route);
		
		$router = Zend_Controller_Front::getInstance()->getRouter();
    	$route = new Zend_Controller_Router_Route('accountsetting', array(
        	'module'     => 'default',
        	'controller' => 'account',
    		'action'	=> 'accountsetting'
    	));
    	$router->addRoute('accountsetting', $route);		

    } 
	protected function _initRegistry(){		
	    $this->bootstrap('db');
	    $db = $this->getResource('db');		
	    $db->setFetchMode(Zend_Db::FETCH_OBJ);		
	    Zend_Registry::set('db', $db);
	} 
		
}