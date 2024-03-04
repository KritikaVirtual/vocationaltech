<?php 	ob_start();
		require_once 'inc/constant.php';
	session_start();
	unset($_SESSION['uid']); 		header('location: '.BASEURL.'/logout');	 
?>
 