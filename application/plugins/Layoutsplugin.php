<?php
class application_plugins_Layoutsplugin extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
 
       $module = $request->getModuleName();
       $controller = $request->getControllerName();
       $layout = Zend_Layout::getMvcInstance();

        // check module and automatically set layout
        $layoutsDir = $layout->getLayoutPath();
        // check if module layout exists else use default
        $layout->setLayout("layout");
		
        /*if (Zend_Auth::getInstance()->hasIdentity()) {        	 
            	
				$level=isset(Zend_Auth::getInstance()->getStorage()->read()->role)?Zend_Auth::getInstance()->getStorage()->read()->role:'';
    			

        }else{
					$layout->setLayout("layout");
			 
        }*/		
		
		
    }
	
}	
?>
