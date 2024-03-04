function frm_my_acc_rst(){
	frm_my_acc_lck(false);
	$("#cmd_my_acc").html("Update");	
}

function frm_my_acc_lck(lck){
	$("#txa_eml").attr("disabled", lck);
	$("#pwd_usr_pwd").attr("disabled", lck);
}

function frm_my_acc_vld(){
	if($("#txa_eml").val() == "")
		err_ctc("Email must not be empty");
	if($("#pwd_usr_pwd").val() == "")
		err_ctc("Password must not be empty");
}

function frm_my_acc(){
	if($("#cmd_my_acc").html() != "Update")
		return;
	try{
		$("#cmd_my_acc").html("Processing");
		err_clr("msg_my_acc");
		frm_my_acc_lck(true);
		frm_my_acc_vld();	
		$.ajax({
			type:"POST",
			url:"ajax/account-update.php",
			data:({
				eml:get_elm_val("txa_eml"),
				usr_pwd:get_elm_val("pwd_usr_pwd"),
			}),
			dataType:"json",
			success:function(dat){
				if(dat.err.length > 0)
					err_dsp(dat.err.join("<br/>"), "msg_my_acc");
				else
					err_dsp(dat.msg.join("<br/>"), "msg_my_acc");
				frm_my_acc_rst();
			},
			error:function(dat, sts, err){
				err_dsp("Cannot update. Please try again.", "msg_my_acc");
				frm_my_acc_rst();
			}
		});
	}catch(e){
		err_dsp(e, "msg_my_acc");
		frm_my_acc_rst();
	}
}