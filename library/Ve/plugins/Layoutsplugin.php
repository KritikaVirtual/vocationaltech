<?php
class Ve_plugins_Layoutsplugin extends Zend_Controller_Plugin_Abstract
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
		
        if (Zend_Auth::getInstance()->hasIdentity()) {
        	 
            	
				$level=isset(Zend_Auth::getInstance()->getStorage()->read()->role)?Zend_Auth::getInstance()->getStorage()->read()->role:'';
    			if($level==md5('A')){
				
					$layout->setLayout('layouta');
				
				} elseif($level==md5('S')){
				
					 $layout->setLayout("layouts");
				
				} elseif($level==md5('D')){
				
					 $layout->setLayout("layoutd");
				
				}else{
				  
					$layout->setLayout("layout");
				}	

        }else{
					$layout->setLayout("layout");
			 
        }		
		
		
    }
	
}	
?>
