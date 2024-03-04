<?php		
	require_once '../inc/ajax.php';

	$res = array();
				
	$val = array(
		'usr_nm' => form_post('usr_nm'),
		'usr_pwd' => form_post('usr_pwd'),
	);
	
	$err = array();
	$msg = array();
	
	if($val['usr_nm'] == '')
		$err[] = 'Please fill in your User Name';
	if($val['usr_pwd'] == '')
		$err[] = 'Please fill in your Password';
				
	if(count($err) == 0){
		$sql = '
			select id 
			from usr
			where 
				usr_nm = '.sql_quote($val['usr_nm']).' and
				usr_pwd = md5('.sql_quote($val['usr_pwd']).') and
				sts = 1
		';
		$uid = intval($conn->quick_select($sql, 0));
		if(!$uid)
			$err[] = 'Wrong User Name/Password<br/>or your account has been expired';
		else
			$_SESSION['uid'] = $uid;		
	}
	
	$res['err'] = $err;
	
	echo json_encode($res);
?>