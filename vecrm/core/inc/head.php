<?php
	require_once 'constant.php'; 
	session_start();    
 
	$path = 'lib';		
	$dir = dir($path);	
	while($file = $dir->read()){
		if($file == '.' || $file == '..')
			continue;
		$target = $path.'/'.$file;
		if(file_exists($target))						
			require_once $target;			
	}
	$dir->close();
		
	require_once 'inc/config.php';
	
	$conn = new cls_connection();	
	$conn->db_server = $conf['db_server'];
	$conn->db_name = $conf['db_name'];
	$conn->db_user = $conf['db_user'];
	$conn->db_pass = $conf['db_pass'];	
	$conn->connect();
	
	if(!is_logged_in() && !$pg_prm['skip_vld'])
		url_redir('log-in.php');
	
	if(!isset($pg_prm['skip_html'])){
		ob_start();


    			if($_SESSION['role']==md5('A')){
				
					require_once('layouta.php');
				
				} elseif($_SESSION['role']==md5('S')){
				
					require_once('layouts.php');
				
				} elseif($_SESSION['role']==md5('D')){
				
					require_once('layoutd.php');
				
				}else{
				  
					
					require_once('layout.php');
				}
?>
 
            <div id="sct_head">
                <div class="t"></div>
                <div class="m"><a href="index.php"><?php echo $conf['title']; ?></a><?php echo $pg_prm['title']?' - '.$pg_prm['title']:''; ?></div>
                <div class="b"></div>
            </div>
            <div id="sct_body">
                <div class="t"></div>
                <div class="m">
<?php } ?>