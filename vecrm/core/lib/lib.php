<?php
	function url_redir($url){
		header('location:'.$url);
		exit;
	}

	function form_get($str){
		if(!isset($_GET[$str]))
			return false;
		return read_var(urldecode($_GET[$str]));
	}

	function form_post($str, $parse = true){
		if(!isset($_POST[$str]))
			return false;
		if($parse)
			return read_var($_POST[$str]);
		return $_POST[$str];
	}

	function read_var($str){
		$res = $str;
		$res = str_replace('\\\'','\'',$res);
		$res = str_replace('\\\\','\\',$res);
		$res = str_replace('\\"','"',$res);
		return $res;
	}
		
	function sql_quote($value){
		$value = str_replace('\\','\\\\',$value);
		$value = str_replace('\'','\\\'',$value);
		return '\''.$value.'\'';		
	}

	function str_esc($str){
		return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
	}

	function err_thr($err){
		throw new Exception($err);
	}

	function eml_vld($email){	
		if($email == '')
			return true;
        $section = split('@', $email);
        if(count($section) != 2)
            return false;
	
        $username = $section[0];
        $domain = $section[1];
        if($username == '') return false;
        if($domain == '') return false;
		
		if(function_exists('checkdnsrr')){
			if(checkdnsrr($domain))
				return true;
			else
				return false;
		}else
			return true;
	}

	function phn_vld($num){
		$vld_chr = str_split('01234567890');
		$num = str_replace('+', '', $num);
		$num = str_replace(' ', '', $num);
		if(strlen($num) != 11)
			return false;
		$arr_num = str_split($num);
		foreach($arr_num as $val){
			if(!in_array($val, $vld_chr))
				return false;
		}
		return true;
	}

	function uk_phn_vld($phn_num) {
		$tmp_phn_num = str_replace (' ', '', $phn_num);

		if (empty($tmp_phn_num))
			err_thr('Telephone number not provided');

		if (preg_match('/^(\+)[\s]*(.*)$/',$tmp_phn_num))
			return 'Please do not included the country code';
		
		$tmp_phn_num = str_replace ('-', '', $tmp_phn_num);
		
		if (!preg_match('/^[0-9]{10,11}$/',$tmp_phn_num))
			err_thr("Wrong length, please include the area code.\nYour phone numbers should be 10 or 11 digits");
		
		if (!preg_match('/^0[0-9]{9,10}$/',$tmp_phn_num))
			return 'You have entered an invalid phone number';
				
		$tnexp[0] = '/^(0113|0114|0115|0116|0117|0118|0121|0131|0141|0151|0161)(4960)[0-9]{3}$/';
		$tnexp[1] = '/^02079460[0-9]{3}$/';
		$tnexp[2] = '/^01914980[0-9]{3}$/';
		$tnexp[3] = '/^02890180[0-9]{3}$/';
		$tnexp[4] = '/^02920180[0-9]{3}$/';
		$tnexp[5] = '/^01632960[0-9]{3}$/';
		$tnexp[6] = '/^07700900[0-9]{3}$/';
		$tnexp[7] = '/^08081570[0-9]{3}$/';
		$tnexp[8] = '/^09098790[0-9]{3}$/';
		$tnexp[9] = '/^03069990[0-9]{3}$/';
		
		foreach ($tnexp as $regexp){	
			if (preg_match($regexp,$tmp_phn_num, $matches))
				return 'You have entered an invalid phone number';
		}
		
		if (!preg_match('/^(01|02|03|05|070|071|072|073|074|075|07624|077|078|079)[0-9]+$/',$tmp_phn_num))
			return 'You have entered an invalid phone number';
				
		return true;	
	}
?>