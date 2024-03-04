<?php
	session_start();
	
	global $conn;
	
	$path = '../lib';		
	$dir = dir($path);	
	while($file = $dir->read()){
		if($file == '.' || $file == '..')
			continue;
		$target = $path.'/'.$file;
		if(file_exists($target))						
			require_once $target;			
	}
	$dir->close();
		
	require_once '../inc/config.php';
	
	$conn = new cls_connection();	
	$conn->db_server = $conf['db_server'];
	$conn->db_name = $conf['db_name'];
	$conn->db_user = $conf['db_user'];
	$conn->db_pass = $conf['db_pass'];	
	$conn->connect();
?>