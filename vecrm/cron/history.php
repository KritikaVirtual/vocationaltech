<?php
/*require_once '/home/ve/public_html/vecrm/index.php';

/*********change in the tenant record section********/

/***move tenant remove record into history record****/
$tenantModel = new Model_Tenant();

$tenantModel->moveRemoveToHistory();



/******remove tenant history record*******/

$tenantModel->deleteTenantHistory();


/******change in the category record section********/


 $catModel = new Model_Category();
 
 $moveCatHistory = $catModel->moveRemoveToHistory();
 
 /**********remove category history record *********/

 $removeCatHistory = $catModel->deleteCategoryHistory();
