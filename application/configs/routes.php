<?php
$route = new Zend_Controller_Router_Route(
    'author',
    array(
        'controller' => 'appcomplete',
        'action'     => 'sendResetInstruction'
    ) 
);

$router->addRoute('author', $route);
?>