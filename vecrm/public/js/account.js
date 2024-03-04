//Delete company
function deleteCompany(id)
{
var cid =id;
if(confirm('Are you sure you want to delete this company?'))
	{
		$.ajax({        
			type: "POST",
			url: baseUrl + "account/deletecompany",
			dataType:'json',
			data: {
				companyID: cid
			},
			beforeSend: function () {
				 //$('.loader').show();
			},
			success: function (msg) {

				if(msg.status=='error')
				{
					alert(msg.message);
					return false;
				}else if(msg.status=='success')
				{
					$('#compID-'+id).remove();
					var msgId = 3;
					filtercompany(msgId);
				}
			}

		});
	}
}
//EDIT COMPANY
function editCompanyy(id)
{
	$('.welcome_text').html('');
	var statusaction=1;
	var statusaction1=1;
	var statusaction2=1;
	var accountNumberr= $('#editAccountNum-'+id).val();
	var companyNamee= $('#editCName-'+id).val();
	var activationDatee= $('#editActivateDate-'+id).val();
	var isactivee= $('#editActiveInactive-'+id).val();
	
	
	if(accountNumberr=='')
	{
	//	$('#acNumErr').html('Enter account number');
		$('#editAccountNum-'+id).addClass("inputErr");
		alert('Account Number Required');
		$('#editAccountNum-'+id).focus();
		return false;
		statusaction1=0;
		
	}else
	{
		$('#acNumErr').html('');
		statusaction1=1;
	}
	
	if(companyNamee=='')
	{
	//	$('#cNameErr').html('Enter company name');
		$('#editCName-'+id).addClass("inputErr");
		alert('Company Name Required');
		$('#editCName-'+id).focus();
		statusaction=0;
		return false;
	}else{
		$('#cNameErr').html('');
		statusaction=1;
	}
	if(activationDatee=='')
	{
	//	$('#acDateErr').html('Enter activation name');
		$('#editActivateDate-'+id).addClass("inputErr");
		alert('Activation Date Required');
		$('#editActivateDate-'+id).focus();
		return false;
		statusaction2=0;
	}else
	{
		$('#acDateErr').html('');
		statusaction2=1;
	}
	if(statusaction==1 && statusaction1==1 && statusaction2==1)
	{
		$('.loader').show();
		$.ajax({        
			type: "POST",
			url: baseUrl + "account/editcompany",
			dataType:'json',
			data: {
				cid:id,
				companyName: companyNamee,
				accountNumber: accountNumberr,
				activationDate: activationDatee,
				isactive: isactivee
			},
			beforeSend: function () {
				 $('.loader').show();
			},
			success: function (msg) {
				 $('.loader').hide();
				 $('#show_edit_row').val('yes');
				if(msg.status=='error')
				{
					alert(msg.message);
					return false;
				}else if(msg.status=='success')
				{
					var msgId = 2;
					filtercompany(msgId);
				}
			}

		});
	}

}
function showEditCompany(id)
{
	/*var show_edit = $('#show_edit_row').val();
	if(show_edit=='no'){
		alert("You can modify one Account at a time");
		return false;
	}*/
	hideAllForm();
	$('.editComapny').hide();
	$('.showCompany').show();
	$('#show_edit_row').val('no');
	$('#compID-'+id).hide();
	$('#editcompID-'+id).show();
	$("#editActivateDate-"+id).datepicker({changeMonth: true,
            changeYear: true,minDate: 0});
}
function hideEditCompany(id)
{
	$('#editcompID-'+id).hide();
	$('#compID-'+id).show();
	$('#show_edit_row').val('yes');
}

/*********check company exists or not **********/

function checkCompany(){
	$('.loader').show();
	var checkComp = false;
	var cname = $.trim($('#companynamee').val());
	//alert('cname'+cname);
	if(cname!=''){
		$.ajax({
				type: "POST",
				url: baseUrl+'account/getvalidatecompany',
				data: {cname: cname },
				beforeSend: function () {
						 //$('.loader').show();
				},
				success: function (msg) {
					    $('.loader').hide();
						if(msg != 'true'){
							$('#cNameErr').html(""); 
							$('#companynamee').removeClass('inputErr');
							checkAccount();						
						}else{
							//alert('Company is not existed');
							$('#cNameErr').html("Company name already in use."); 
							$('#companynamee').addClass('inputErr');
							$('#companynamee').focus();							
							return false;
						} 			
				}
			  });
          
    }else{
		$('.loader').hide();
		$('#cNameErr').html("Company Name Required"); 
		$('#companynamee').addClass('inputErr');
		$('#companynamee').focus();
		return false;
	}
         
}

/*********check Account Number exists or not **********/

function checkAccount(){
	$('.loader').show();
	var checkComp = false;
	var acNumber= $.trim($('#accountNumber').val());
	//alert('cname'+cname);
	if(acNumber!=''){		
		$.ajax({
				type: "POST",
				url: baseUrl+'account/getvalidateaccount',
				data: {acNumber: acNumber },				
				success: function (msg) {
					    $('.loader').hide();
						if(msg != 'true'){							
							$('#acNumErr').html(""); 
							$('#accountNumber').removeClass('inputErr');
							addCompany();						
						}else{
							//alert('Company is not existed');
							$('#acNumErr').html("Account Number already in use."); 
							$('#accountNumber').addClass('inputErr');
							$('#accountNumber').focus();							
							return false;
						} 			
				}
			  });
          
    }else{
		$('.loader').hide();
		$('#acNumErr').html("Account Number Required"); 
		$('#accountNumber').addClass('inputErr');
		$('#accountNumber').focus();
		return false;
	}
         
}

function uploadLogo(){

	var file_action2=1;
	var file_val = $('#company_logo1').val();

	if(file_val!=''){
		//var logo_file = $('#company_logo1')[0].files[0];
		var logo_file=document.querySelector("input[type='file']"); 
		var file_size = logo_file.size;
		var file_name = logo_file.name;
		var dotIndex = file_name.lastIndexOf('.');
        var ext = $('#company_logo1').val().split('.').pop().toLowerCase();
        var validFileExtensions = ["jpg", "jpeg", "gif", "png"];
		if( $.inArray( ext, validFileExtensions ) == -1){			
			$('#logoError').html('Upload logo only in jpg, png or gif format.');
			file_action2=0;
			return false;
		}
		
		if(file_size >(1024*150)){			
			$('#logoError').html('file size must be less than 150kb.');
			file_action2 =0;
			return false;
		}
	}else{
		$('#logoError').html('Select logo file.');
			file_action2=0;
			return false;
	}	

	if(file_action2==1)
	{ 
		document.getElementById("uploadlogoform").submit();
	}
}

function addCompany()
{  	
	$('.welcome_text').html('');
	var statusaction=1;
	var statusaction1=1;
	var statusaction2=1;
	var file_action2=1;
	var setfocus = null;
	var companyName= $.trim($('#companynamee').val());
	var accountNumber= $.trim($('#accountNumber').val());
	var activationDate= $('#activationDate').val();
	var isactive= $('#isactive').val();	
	var file_val = $('#company_logo').val();
	
	if(companyName=='')
	{
		$('#cNameErr').html('Company Name Required');
		$('#companynamee').addClass('inputErr');
		setfocus=$('#companynamee');		
		statusaction=0;
		return false;
	}else{
		$('#cNameErr').html('');
		$('#companynamee').removeClass('inputErr');
		statusaction=1;
		
	}
	if(accountNumber=='')
	{
		$('#acNumErr').html('Account Number Required');
		$('#accountNumber').addClass("inputErr");
		//$('#accountNumber').focus();
		if(setfocus==null){setfocus = $('#accountNumber');}
		statusaction1=0;
		return false;
	}else{
		$('#acNumErr').html('');
		$('#accountNumber').removeClass('inputErr');
		statusaction1=1;
	}
	
	if(activationDate=='')
	{
		$('#acDateErr').html('Activation Date Required');
		$('#activationDate').addClass("inputErr");
		//$('#activationDate').focus();
		if(setfocus==null){setfocus = $('#activationDate');}
		statusaction2=0;
		return false;
	}else{
		$('#acDateErr').html('');
		$('#activationDate').removeClass('inputErr');
		statusaction2=1;
	}
	if(file_val!=''){
		//var logo_file = $('#company_logo')[0].files[0];
		var logo_file=document.querySelector("input[type='file']"); 
		var file_size = logo_file.size;
		var file_name = logo_file.name;
		var dotIndex = file_name.lastIndexOf('.');
        var ext = $('#company_logo').val().split('.').pop().toLowerCase();
        var validFileExtensions = ["jpg", "jpeg", "gif", "png"];
		if( $.inArray( ext, validFileExtensions ) == -1){			
			$('#logoErr').html('Upload logo only in jpg, png or gif format.');
			file_action2=0;
			return false;
		}
		
		if(file_size >(1024*150)){			
			$('#logoErr').html('file size must be less than 150kb.');
			file_action2 =1;
			return false;
		}
	}	
	if(setfocus!=null){setfocus.focus();}
	
	if(statusaction==1 && statusaction1==1 && statusaction2==1 && file_action2==1)
	{ 
		document.getElementById("addcompanyform").submit();
		/*$('.loader').show();
		$.ajax({        
			type: "POST",
			url: baseUrl + "account/addcompany",
			dataType:'json',
			data: {
				companyName: $('#companynamee').val(),
				accountNumber: $('#accountNumber').val(),
				activationDate: $('#activationDate').val(),
				isactive: $('#isactive').val()
			},
			beforeSend: function () {
				 //$('.loader').show();
			},
			success: function (msg) {

				if(msg.status=='error')
				{
					if(msg.message=='Company name already exists')
					{
						$('#cNameErr').html("Company Name already in use.");
						$('.loader').hide();
						return false;
					}else if(msg.message=='Account name already exists')
					{
						$('#acNumErr').html("Account Number already in use.");
						$('.loader').hide();
						return false;
					}
					
				}else if(msg.status=='success')
				{
					document.addcompanyform.reset();
					var msgId = 1;
					filtercompany(msgId);
					$('.loader').hide();
				}
			}

		});*/
	}
	
}
function filtercompany(msgId)
{
	var page = $('#page').val();
	window.location.href= baseUrl + "account/index/page/"+page+"/mId/"+msgId;
	/*$.ajax({        
			type: "POST",
			url: baseUrl + "account/filtercompany",
			dataType:'json',
			data: {
				
			},
			beforeSend: function () {
				 //$('.loader').show();
			},
			success: function (msg) {

				if(msg.status=='error')
				{
					alert(msg.message);
					return false;
				}else if(msg.status=='success')
				{
					$('#companyListData').html(msg.message);
				}
			}

		});*/

}
$(document.body).on('click', '#openaddcompanydiv' ,function(){
	hideAllForm();
  $('#cNameErr').html('');
  $('#companynamee').removeClass('inputErr');
  $('#acNumErr').html('');
  $('#accountNumber').removeClass('inputErr');
  $('#acDateErr').html('');
  $('#logoErr').html('');
  $('#activationDate').removeClass('inputErr');
  $("#acc_company_block").show();
  var height = $("#companyListData").height();	
  var tot_height = parseInt(parseInt(height)+400);
  $("#ac_company_popup").css('height',tot_height+'px');
  $("#ac_company_popup").show();
  $('#addcompanydiv').show();

});
$(document.body).on('click', '#canceladdcompany' ,function(){
	$('.welcome_text').html('');
	document.addcompanyform.reset();
	$("#acc_company_block").hide();
	$("#ac_company_popup").hide();
	$('#addcompanydiv').hide();
});
function showCalen(obj){
	//var id = $(obj).attr('id');
	//$("#"+id).datepicker("show");
}

function isNumberKey(evt)
{
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 46 && charCode > 31
	&& (charCode < 48 || charCode > 57)){		
	  return false;
  }else
	return true;
}

function findCompany(obj){
	var cname_val = $.trim($(obj).val());
	var id = $(obj).attr('id');
	if(cname_val.length < 1) {
		
		$('#'+id).focus();
		$('#'+id).addClass("inputErr");
		return false;
	}
	$.ajax({        
			type: "POST",
			url: baseUrl + "company/findcompanyname",
			data: {
				companyName:cname_val,
			},
			beforeSend: function () {
				 $('.loader').show();
			},
			success: function (msg) {
			$('.loader').hide();
				if(msg == 'found'){
					$('#cNameErr').html("Company Name already in use.");
					$(obj).focus();
					$('#'+id).addClass("inputErr");
				}
				
			}

		});
	return false;
}
function findAccNo(obj){
	var acc_val = $(obj).val();
	var id = $(obj).attr('id');
	if(acc_val.length < 1) {
		
		$('#'+id).focus();
		$('#'+id).addClass("inputErr");
		return false;
	}
	$.ajax({        
			type: "POST",
			url: baseUrl + "company/findaccountnumber",
			data: {
				accountNumber:acc_val,
			},
			beforeSend: function () {
				 $('.loader').show();
			},
			success: function (msg) {
			$('.loader').hide();
				if(msg == 'found'){
					$('#acNumErr').html("Account Number already in use.");
					$(obj).focus();
					$('#'+id).addClass("inputErr");
				}
			}

		});
	return false;
}

function changePassword(){
	var newpwd= $.trim($('#newpwd').val());
	var repwd= $.trim($('#repwd').val());
	if(newpwd==''){
		$('#newpwdErr').html('New Password Required');
		$('#newpwd').addClass("inputErr");
		$('#newpwd').focus();
		return false;
	}else{
		$('#newpwdErr').html('');
		$('#newpwd').removeClass("inputErr");
	}
	
	if(newpwd!=''){
		var decimal=  /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,15}$/;
			if(!newpwd.match(decimal))   
			{   
				$('#newpwdErr').html("Password is not in the correct format.");				
				$('#newpwd').addClass('inputErr');  
				return false;  
			} 
	}else{
		$('#newpwdErr').html('');
		$('#newpwd').removeClass("inputErr");
	}
	
	if(newpwd!=repwd){
		$('#repwdErr').html('Passwords are not matching.');
		$('#repwd').addClass("inputErr");
		$('#repwd').focus();
		return false;
	}else{
		$('#repwdErr').html('');
		$('#repwd').removeClass("inputErr");
	}
	
	var passkey = $('#passkey').val();
	//alert(passkey);
	$.ajax({        
			type: "POST",
			url: baseUrl + "account/changepassword",
			data: {
				cpass:newpwd,
				passkey:passkey,
			},
			beforeSend: function () {
				 $('.loader').show();
			},
			success: function (msg) {
			$('.loader').hide();
			//alert('password change'+msg);
			$('#msg').html('Password have been changed successfully.');
			document.changepasswordform.reset();
				/*if(msg == 'found'){
					$('#cNameErr').html("Company Name already in use.");
					$(obj).focus();
					$('#'+id).addClass("inputErr");
				}*/
				
			}

		});
	
}

$(document.body).on('click', '#cancelchangepassword' ,function(){
document.changepasswordform.reset();
});

$(function() {
$( "#activationDate" ).datepicker({changeMonth: true,
            changeYear: true,minDate: 0});

//called when key is pressed in textbox
$("#accountNumber").keypress(function (e) {
 //if the letter is not digit then display error and don't type anything
 if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
	//display error message
	$("#acNumErr").html("Digits Only").show().fadeOut("slow");
		   return false;
}
});

});


$(document.body).on('click', '#openuploadlogoform' ,function(){
  hideAllForm();
  $('#cust_id').attr('value',this.name);	
  var height = $("#companyListData").height();	
  var tot_height = parseInt(parseInt(height)+400);
  $(".upload_com_logo").show();
  $("#ac_upload_popup").css('height',tot_height+'px'); 
  $("#ac_upload_popup").show();
  $("#uploadcompanydiv").show();


});

$(document.body).on('click', '#canceluploadlogoform' ,function(){
	$('.welcome_text').html('');
	document.uploadlogoform.reset();
	$(".upload_com_logo").hide();
	$("#ac_upload_popup").hide();
  	$("#uploadcompanydiv").hide();
  	$('#logoError').html('');
});


function hideAllForm(){
	
	$('.welcome_text').html('');
	/***********hide add company form *********/
	document.addcompanyform.reset();
	$("#acc_company_block").hide();
	$("#ac_company_popup").hide();
	$('#addcompanydiv').hide();
	
	/*******hide logo form *****/
	document.uploadlogoform.reset();
	$(".upload_com_logo").hide();
	$("#ac_upload_popup").hide();
  	$("#uploadcompanydiv").hide();
  	$('#logoError').html('');
}
