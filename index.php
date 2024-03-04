<?php

//error_reporting(E_ALL);
ini_set('display_errors', '0');
//date_default_timezone_set("Asia/Calcutta"); 
// Define path to application directory
//error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define application environment
// Define application environment
//defined('APPLICATION_ENV')
   // || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
   
   defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
     
)));
    
 

/** Zend_Application */
require_once 'Zend/Application.php';
//require_once 'Servicefreak/Application.php';

require_once 'constant.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$session = new Zend_Session_Namespace( 'Zend_Auth' );
$session->setExpirationSeconds( 1440 ); // 24 minutes time duration for session
 
$application->bootstrap()
            ->run();
            