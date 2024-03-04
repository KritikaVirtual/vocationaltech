function frm_log_in_rst(){
	frm_log_in_lck(false);
	$("#cmd_log_in").html("Log In");	
}

function frm_log_in_lck(lck){
	$("#txt_usr_nm").attr("disabled", lck);
	$("#pwd_usr_pwd").attr("disabled", lck);
}

function frm_log_in_vld(){
	if($("#txt_usr_nm").val() == "")
		err_ctc("User Name must not be empty");
	if($("#pwd_usr_pwd").val() == "")
		err_ctc("Password must not be empty");
}

function frm_log_in(){
	if($("#cmd_log_in").html() != "Log In")
		return;
	try{
		$("#cmd_log_in").html("Processing");
		err_clr("msg_log_in");
		frm_log_in_lck(true);
		frm_log_in_vld();	
		$.ajax({
			type:"POST",
			url:"ajax/log-in-validate.php",
			cache:false,
			data:({
				usr_nm:enc_elm("txt_usr_nm"),
				usr_pwd:enc_elm("pwd_usr_pwd"),
			}),
			dataType:"json",
			success:function(dat){
				if(dat.err.length > 0){
					err_dsp(dat.err.join("<br/>"), "msg_log_in");
					frm_log_in_rst();
				}else
					location.href = "index.php";
			},
			error:function(dat, sts, err){
				err_dsp("Cannot log in. Please try again.", "msg_log_in");
				frm_log_in_rst();
			}
		});
	}catch(e){
		err_dsp(e, "msg_log_in");
		frm_log_in_rst();
	}
}