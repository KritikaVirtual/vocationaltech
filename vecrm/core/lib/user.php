<?php
	function get_uid(){
		return intval($_SESSION['uid']);
	}
	
	function is_logged_in(){
		global $conn;
		
		if(!get_uid())
			return false;
		$sql = '
			select role
			from admin_usr
			where sts=1 and  id = '.sql_quote(get_uid()).'
		';
		$role = ($conn->quick_select($sql, 0));
		return ($role==md5('A'))?true:false;
	}
?>