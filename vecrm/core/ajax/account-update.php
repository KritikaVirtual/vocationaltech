<?php		
	require_once '../inc/ajax.php';

	$res = array();
				
	$val = array(
		'eml' => form_post('eml'),
		'usr_pwd' => form_post('usr_pwd'),
	);
	
	$err = array();
	$msg = array();
	
	if(!is_logged_in())
		$err[] = 'Login required';
	if($val['eml'] == '')
		$err[] = 'Please fill in your Email Address';
	if($val['usr_pwd'] == '')
		$err[] = 'Please fill in your Password';
		
	if(count($err) == 0){
		$sql = '
			update admin_usr
			set 
				eml = '.sql_quote($val['eml']).',
				usr_pwd = md5('.sql_quote($val['usr_pwd']).')
			where id = '.sql_quote(get_uid()).'
		';
		$conn->execute($sql);
		
		$msg[] = 'Updated successfully';
	}
	
	$res['err'] = $err;
	$res['msg'] = $msg;
	
	echo json_encode($res);
?>