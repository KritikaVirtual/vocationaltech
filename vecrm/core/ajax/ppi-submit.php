<?php
	require_once '../inc/ajax.php';
	$res = '';
	try{			
		$txt_name = trim(form_post('txt_name'));
		$txt_phone = trim(form_post('txt_phone'));
		$txt_email = trim(form_post('txt_email'));
		$txt_ip = $_SERVER['REMOTE_ADDR'];
                $aff = trim(form_post('txt_aff'));
                if(empty($aff))
                { 
                  $aff_id = "Direct Visitor";
                } else { 
                  $aff_id = $aff;
                }
		$txt_product = "PPI_Claims";
		$txt_resource = "PPI_CPPI_SMS_IS_SOURCE";

		if($txt_name == '')
			err_thr('Please type in your Name');
		if($txt_phone == '')
			err_thr('Please type in your Phone');
		$phn_vld = uk_phn_vld($txt_phone);
		if($phn_vld !== true)
			err_thr($phn_vld);
		if($txt_email == '')
			err_thr('Please type in your Email');
		if(!eml_vld($txt_email))
			err_thr('Email is not valid');		

		$new_phn = true;
		$sql = '
			select id
			from admin_ppi
			where phn = '.sql_quote($txt_phone).'
		';
		$rid = intval($conn->quick_select($sql, 0));
		if(!$rid){
			$sql = '
				select id
				from admin_ppi
				where phn = '.sql_quote($txt_phone).'
			';
			$rid = intval($conn->quick_select($sql, 0));
			if($rid)
				$new_phn = false;	
		}else			
			$new_phn = false;	

		$sql = '
			insert into admin_ppi(
				nme, 
				phn, 
				eml, 
				ip, 
				aff,
				dte_add
			)values(
				'.sql_quote($txt_name).',
				'.sql_quote($txt_phone).',
				'.sql_quote($txt_email).',
				'.sql_quote($txt_ip).',
				'.sql_quote($aff_id).',
				now()
			)
		';
		$conn->execute($sql);
		


if(!empty($aff)){

// The sale amount. You get this value from your payment gateway or the shopping cart
$sale_amt = 1;

// The Post URL (Get this value from the settings menu of this plugin)
$postURL = "http://www.compensationppi.net/wp-content/plugins/wp-affiliate-platform/api/post.php";

// The Secret key (Get this value from the settings menu of this plugin)
$secretKey = "698a5bef0caf2";

// Retrieve the value from the browser's cookie to find out who the referrer was
$affiliate_id = $aff;

// Prepare the data
$data = array ();
$data['secret'] = $secretKey;
$data['ap_id'] = $affiliate_id;
$data['sale_amt'] = $sale_amt;

// send data to post URL to award the commission
$ch = curl_init ($postURL);
curl_setopt ($ch, CURLOPT_POST, true);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
$returnValue = curl_exec ($ch);
curl_close($ch);

}

	
//extract data from the post
extract($_POST);

//set POST variables
$fields = array(
     'Forename' => $txt_name
	,'Surname'    => $txt_name
    ,'PhoneNumber'      => $txt_phone
    ,'EmailAddress'      => $txt_email
    ,'LeadSource' => $txt_resource
    ,'Product' => $txt_product
);

$fields_string = http_build_query( $fields );

$url = 'http://www.moneysavingteam.com/ImportService/vImporter.asmx/ImportLead';

//open connection
$ch = curl_init();
//set the url, number of POST vars, POST data
curl_setopt( $ch, CURLOPT_URL, $url);
curl_setopt( $ch, CURLOPT_POST, count($fields ) );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//execute post
$result = curl_exec( $ch );

//close connection
curl_close( $ch );

		$sql = '
			select eml
			from admin_usr
			where id = 1
		';
		$dat_usr = $conn->read($conn->select($sql));

		$mail_msg = 'Hi,
		
Here is a new lead for you:
Name    : '.$txt_name.'
Phone   : '.$txt_phone.'
Email   : '.$txt_email;
		$arr_eml = explode("\n", str_replace("\r", '', $dat_usr['eml']));
		
		foreach($arr_eml as $eml){
			if(trim($eml) != '')
				mail($eml, 'New Lead Submission on CompensationPPI.net', $mail_msg);
		}	
		
		$res = '<info>Success</info>';
	}catch(Exception $e){
		$res = '<error>'.$e->getMessage().'</error>';
	}

	header('Content-Type: text/xml');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
	$xml = '<response>'.$res.'</response>';			
	echo $xml;
?>