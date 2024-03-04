<?php
	require_once '../inc/ajax.php';
	sleep(2);
	$res = '';
	try{			
		$txt_name = trim(form_post('txt_name'));
		$txt_address = trim(form_post('txt_address'));
		$txt_post_code = trim(form_post('txt_post_code'));
		$txt_phone = trim(form_post('txt_phone'));
		$txt_email = trim(form_post('txt_email'));
		$cbo_age = trim(form_post('cbo_age'));
		$txt_year = intval(form_post('txt_year'));
		$txt_renewal = trim(form_post('txt_renewal'));
		$cbo_best_time = trim(form_post('cbo_best_time'));		
		
		if($txt_name == '')
			err_thr('Please type in your Name');
		if($txt_address == '')
			err_thr('Please type in your First line of address');
		if($txt_post_code == '')
			err_thr('Please type in your Post Code');
		if($txt_phone == '')
			err_thr('Please type in your Phone');
		if($txt_email == '')
			err_thr('Please type in your Email');
		if(!eml_vld($txt_email))
			err_thr('Email is not valid');		
		if($cbo_age == '')
			err_thr('Place holder age is not valid');
		if($txt_year < 1900)
			err_thr('Please type a valid Year of manufacture');
		if($txt_renewal == '')
			err_thr('Please type in your Renewal date');	
		if($cbo_best_time == '')
			err_thr('Best time to call is not valid');

		$sql = '
			insert into lds(
				nme, 
				adr, 
				zip, 
				phn, 
				eml, 
				age, 
				yer_mft, 
				dte_rnw, 
				cal_tme, 
				dte_add
			)values(
				'.sql_quote($txt_name).',
				'.sql_quote($txt_address).',
				'.sql_quote($txt_post_code).',
				'.sql_quote($txt_phone).',
				'.sql_quote($txt_email).',
				'.sql_quote($cbo_age).',
				'.sql_quote($txt_year).',
				'.sql_quote($txt_renewal).',
				'.sql_quote($cbo_best_time).',
				now()
			)
		';
		$conn->execute($sql);

		$sql = '
			select eml
			from admin_usr
			where id = 1
		';
		$dat_usr = $conn->read($conn->select($sql));

		$mail_msg = 'Dear Admin,
		
Here is a new submitted lead:
Name                              : '.$txt_name.'
First line of address             : '.$txt_address.'
Post Code                         : '.$txt_post_code.'
Phone                             : '.$txt_phone.'
Email                             : '.$txt_email.'
Place Holder age                  : '.$cbo_age.'
Year of manufacture               : '.$txt_year.'
Renewal date                      : '.$txt_renewal.'
Best time to call                 : '.$cbo_best_time;
		mail($dat_usr['eml'], 'New Lead Submission', $mail_msg);
		
		$res = '<info>Success</info>';
	}catch(Exception $e){
		$res = '<error>'.$e->getMessage().'</error>';
	}

	header('Content-Type: text/xml');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
	$xml = '<response>'.$res.'</response>';			
	echo $xml;
?>