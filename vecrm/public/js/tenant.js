function hideTenantUser(tId){
	$('#open_div_'+tId).show();
	$('#close_div_'+tId).hide();
	$(".message").html(''); 
	$(".error-msg").html('');
	$('#loadtenant_'+tId).html('');
	$('#trId_'+tId).hide();
	
}
function hideAllTenant(){
	$('.open_plus').show();
	$('.open_close').hide();
	$('.trtenant-class').hide();
	$('.tdtenant-class').html('');
}
function loadTenantUser(tId){
	hideAllTenant();
	$('#open_div_'+tId).hide();
	$('#close_div_'+tId).show();
	$("#tenantuser_popup_"+tId).hide();	  
	if(tId!=''){
		$('.loader').show();
		$.ajax({
                url         : baseUrl+"tenant/loadtenantuser",
                type        : "post",
                datatype    : 'json',
                data        : {tId:tId},
                success     : function( data ) {
					$('.loader').hide(); 
                   if(data){					                        
                       $('#loadtenant_'+tId).html(data);                       
                       $('#trId_'+tId).show();
                   }else{                     
                      alert('There was an error');
                   }
                },
                error       : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });
	}else{
		alert('Some error occurred.');
	}
}

function showAddForm(tId){
	$(".message").html(''); 
	$(".error-msg").html('');
	var height = $("#tenantUserInfo_"+tId).height();	
	var tot_height = parseInt(parseInt(height)+15)
	$("#tenantuser_popup_"+tId).css('height',tot_height+'px');	 
	$("#tenantuser_popup_"+tId).show();
	$('#add-newuser-td-'+tId).show();
}

function cancelUser(){
	var tenantId = $('#tenantId').val();
	var building = $('#building').val();
	var role_id =  $('#role_id').val();
	if(role_id == 5){
		window.location.href = baseUrl+'tenant/tenantuser';
	}else
	window.location.href = baseUrl+'tenant/users/bid/'+building+'/tId/'+tenantId;
	/*$('#email_'+tId).removeClass('inputErr');
	$('#firstname_'+tId).removeClass('inputErr');
	$('#lastname_'+tId).removeClass('inputErr');
	$('#phone_'+tId).removeClass('inputErr');
	$('.uemailErr').html("");
	$('.ufirstErr').html("");
	$('.ulastErr').html("");
	$('.uofficeErr').html("");
	$('.moduleErr').html("");
	$('#add-newuser-td-'+tId).hide();
	$("#tenantuser_popup_"+tId).hide();*/
}

/*********check company exists or not **********/

function checkTUser(tId){
	$('.loader').show();
	var checkComp = false;
	var email = $.trim($('#email').val());
	//alert('cname'+cname);
	if(email!=''){
		if(!validateEmail(email)){
				$('.uemailErr').html("E-Mail Address Invalid"); 
				$('#email').addClass('inputErr'); 
				$('#email').focus();		
			}else{
				$.ajax({
						type: "POST",
						url: baseUrl+'tenant/checktenant',
						data: {email: email },
						beforeSend: function () {
								 //$('.loader').show();
						},
						success: function (msg) {
								$('.loader').hide();
								if(msg != true){    
									$('#email-error').html(""); 
									$('#email').removeClass('inputErr');
									createUser(tId);						
								}else{
									//alert('Company is not existed');
									$('#email-error').html("Email already in use."); 
									$('#email').addClass('inputErr');
									$('#email').focus();							
									return false;
								} 			
						}
					  });
			}  
          
    }else{
		$('.loader').hide();
		$('#email-error').html("Enter the email-id"); 
		$('#email').addClass('inputErr');
		$('#email').focus();
		return false;
	}
         
}
function createUser(tId){
	$('.loader').show();	
	var submit_flag=0;	
	var email = $('#email');
	var email_value = email.val();
	var firstname = $('#firstname');
	var fname_value = firstname.val();
	var lastname = $('#lastname');
	var lname_value = lastname.val();
	var suite_location = $('#suite_location');
	var suite_location_value = suite_location.val();
	/*var title	 = $('#title');
	var title_value = title.val();*/
	var access	 = $('#access').val();	
	
	var phone = $('#phone');
	var phone_value = phone.val();
	//var modulee = $('#modulee').val();
	
	if(email_value == ''){		
		$('.uemailErr').html("E-Mail Address Required"); 
		email.addClass('inputErr'); 
		email.focus();
		submit_flag = 1;				
	}
	else{
		$('.uemailErr').html(''); 
		email.removeClass('inputErr');
		}
	
	
	if(fname_value == ''){
		$('.ufirstErr').html("First Name Required"); 
		firstname.addClass('inputErr');
		firstname.focus();
		submit_flag = 1;
		}
	else{
		$('.ufirstErr').html('');
		firstname.removeClass('inputErr');
		}
	
	if(lname_value == ''){
		$('.ulastErr').html("Last Name Required");
		lastname.addClass('inputErr');
		lastname.focus();
		submit_flag = 1;
		}
	else{
		$('.ulastErr').html(''); 
		lastname.removeClass('inputErr');
		}
		
		if(suite_location_value == ''){
		$('.usuiteErr').html("Suite/Location Required");
		suite_location.addClass('inputErr');
		suite_location.focus();
		submit_flag = 1;
		}
	else{
		$('.usuiteErr').html(''); 
		suite_location.removeClass('inputErr');
		}
		
		if(phone_value == ''){
			$('.uofficeErr').html("Phone Number Required");
			phone.addClass('inputErr');
			phone.focus();
			submit_flag = 1;
		}
	else{
			$('.uofficeErr').html('');
			phone.removeClass('inputErr');
		}
			
		if(submit_flag == 1){
			$('.loader').hide();
			return false;
		}else{
		  document.getElementById("addNewUser").submit();
			/*var buildId = $('#building_id').val();
			$.ajax({
                url         : baseUrl+"tenant/addtenanttuser",
                type        : "post",
                datatype    : 'json',
                data        : {
					           email:email_value,
					           firstName:fname_value,
					           lastName:lname_value,
					           title:title_value,
					           phone:phone_value,
					           modules:modules,
					           buildId:buildId,
					           access:access,
					           tenantId:tId
					           },
                success : function(result) {
					var data = $.parseJSON(result);
					$('.loader').hide(); 
                   if(data.status=='success'){                       
                       //$('#tenant_user_load_'+tId).html(data);
                       loadTenantUser(tId);
                       $('.message').html(data.msg);                       
                   }else if(data.status == 'email_error'){
					   $('.uemailErr').html(data.msg); 
			            email.addClass('inputErr');
				   }else{
					   loadTenantUser(tId);
					   $('.error_message').html(data.msg);                     
                      //alert('There was an error');
                   }
                },
                error : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });*/
		}
}

function validateEmail(sEmail) {
	    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	    if (filter.test(sEmail)) {
	        return true;
	    }
	    else {
	        return false;
	    }
 }	
 
 function showEditTUser(userId, tId){
	$(".message").html(''); 
	$(".error-msg").html('');
	 if(tId!='' && userId!=''){
		$('.loader').show();
		$.ajax({
                url         : baseUrl+"tenant/loadedittuser",
                type        : "post",
                datatype    : 'json',
                data        : {tuserId:userId,
					           tId:tId},
                success     : function( data ) {
					$('.loader').hide(); 
                   if(data){
					    var height = $("#tenantUserInfo_"+tId).height();	
						var tot_height = parseInt(parseInt(height)+215)
						$("#tenantuser_popup_"+tId).css('height',tot_height+'px');	 
						$("#tenantuser_popup_"+tId).show();
					   $('#edit-tuser-'+tId).show();                       
                       $('#edit_user_load_'+tId).html(data);                       
                   }else{                     
                      alert('There was an error');
                   }
                },
                error       : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });
	}else{
		alert('Some error occurred.');
	}
 }

function cancelEditUser(tId){
	$('#edit-tuser-'+tId).hide();                       
    $('#edit_user_load_'+tId).html(''); 
    $("#tenantuser_popup_"+tId).hide();
}

function deleteTenantUser(tId,uId,cnt){
	var check_delete='YES';
	var role_id =  $('#role_id').val();
	if(cnt > 1){
		var return_value = prompt("For Deleting Tenant User, Enter Yes in Capital letter.");	
		if(check_delete === return_value){
			$.ajax({
	                url         : baseUrl+"tenant/deletetuser",
	                type        : "post",
	                datatype    : 'json',                
	                data        : {
						           uId:uId,tId:tId
						           },
	                success : function( result ) {
						var data = $.parseJSON(result);
						//alert(data.msg);
						if(data.msg=='true'){
							$('.message').html('Tenant user deleted successfully.');
						}else{
							$('.error-txt').html('Some error occurred.');
						}
						if(role_id == 5){
							window.location.href = baseUrl+'tenant/tenantuser';
						}else
						loadTenantUser(tId);
					}
				});	
		}else{
			//alert('You have entered wrong word.');
			$('.error-txt').html('You have entered wrong word.');
		}
	}
	else{
		alert('There must be more than one user. Please add one more user to delete the user of tenant.');
	}
	
	
}

function deleteTenant(tId){
	var check_delete='YES';
	var bid = $('#building_id').val();
	var return_value = prompt("For Deleting Tenant, Enter Yes in Capital letter.");
	if(return_value!=null){	
		if(check_delete === return_value){
			$.ajax({
					url         : baseUrl+"tenant/deletetenant",
					type        : "post",
					datatype    : 'json',                
					data        : {
								   tId:tId
								   },
					success : function( result ) {
						var data = $.parseJSON(result);
						if(data.msg=='true'){
							$('.message').html('Tenant deleted successfully.');
							window.location.href=baseUrl+"tenant/users/bid/"+bid+"/msg/3";
						}else{
							$('.error-txt').html('Some error occurred.');
						}					
					}
				});	
		}else{
			//alert('You have entered wrong word.');
			$('.error-txt').html('You have entered wrong word.');
		}
	}
	
}

/**
 * check user name of tenant user
 */
 
function checkUserName(){
	var userName = $('#userName').val();
	var uid = $('#uid').val();
	if(userName!=''){
		if(userName.length < 3){
				$('.unameErr').html("User name should be at least 3 character."); 
				$('#userName').addClass('inputErr'); 
				$('#userName').focus();		
			}else{
				$.ajax({
						type: "POST",
						url: baseUrl+'tenant/checkusername',
						data: {userName: userName, uid:uid },						
						success: function (msg) {
								$('.loader').hide();
								if(msg != true){    
									$('.unameErr').html(""); 
									$('#userName').removeClass('inputErr');
									checkUserEmail();						
								}else{
									//alert('Company is not existed');
									$('.unameErr').html("User name already in use."); 
									$('#userName').addClass('inputErr');
									$('#userName').focus();							
									return false;
								} 			
						}
					  });
			}  
          
    }else{
		$('.loader').hide();
		$('.unameErr').html("Enter the user name"); 
		$('#userName').addClass('inputErr');
		$('#userName').focus();
		return false;
	}
}

/**
 * check email-id of tenant user
 */
 
function checkUserEmail(){
	$('.loader').show();
	var checkComp = false;
	var email = $.trim($('#email').val());
	var uid = $('#uid').val();
	//alert('cname'+cname);
	if(email!=''){
		if(!validateEmail(email)){
				$('.uemailErr').html("E-Mail Address Invalid"); 
				$('#email').addClass('inputErr'); 
				$('#email').focus();		
			}else{
				$.ajax({
						type: "POST",
						url: baseUrl+'tenant/checkuseremail',
						data: {email: email,uid:uid },						
						success: function (msg) {
								$('.loader').hide();
								if(msg != true){    
									$('#email-error').html(""); 
									$('#email').removeClass('inputErr');
									editTUser();						
								}else{
									//alert('Company is not existed');
									$('#email-error').html("Email already in use."); 
									$('#email').addClass('inputErr');
									$('#email').focus();							
									return false;
								} 			
						}
					  });
			}  
          
    }else{
		$('.loader').hide();
		$('#email-error').html("Enter the email-id"); 
		$('#email').addClass('inputErr');
		$('#email').focus();
		return false;
	}
         
}

function sendtenantemail(tid){
	$('.loader').show();
	$('.error-txt').html('');
	$('.message').html("");
	$.ajax({
			type: "POST",
			url: baseUrl+'tenant/sendtenantemail',
			data: {tid: tid},						
			success: function (data) {
					$('.loader').hide();
					if(data==true){
						$('.message').text("Notification email successfully sent to all users.");
						$('.message').show();
					}	
					else{		
						//alert('There was an error');			
						$('.error-txt').html('Error occured during sending welcome letter.');
					 }	
						
			},

            error: function(){
				$('.loader').hide();
                //alert('There was an error');
                $('.error-txt').html('Error occured during sending welcome letter.');
            }  

		  });
}

function sendemail(tid, uid){
	$('.loader').show();
	$('.error-txt').html('');
	$('.message').html("");
	$.ajax({
			type: "POST",
			url: baseUrl+'tenant/sendemail',
			data: {tid: tid,uid:uid },						
			success: function (data) {
					$('.loader').hide();
					if(data==true){
						$('.message').text("Notification email successfully sent.");
						$('.message').show();
					}	
					else{		
						//alert('There was an error');
						$('.error-txt').html('Error occured during sending welcome letter.');
					}
			},

            error: function(){
				$('.loader').hide();
                //alert('There was an error');
                $('.error-txt').html('Error occured during sending welcome letter.');
            }  

		  });
}

function editTUser(){
	var submit_flag=0;
	var firstname = $('#firstname').val();	
	var lastname = $('#lastname').val();
	var suite_location = $('#suite_location').val();
	var phone = $('#phone').val();
	var userId = $('#user_id').val();
	var password = $('#password').val();
	var confirm_password = $('#confirm_password').val();
	if(firstname == ''){
		$('.ufirstErr').html("First Name Required"); 
		$('#firstname').addClass('inputErr');		
		submit_flag = 1;
		}
	else{
		$('.ufirstErr').html('');
		$('#firstname').removeClass('inputErr');
		}
	
	if(lastname == ''){
		$('.ulastErr').html("Last Name Required");
		$('#lastname').addClass('inputErr');		
		submit_flag = 1;
		}
	else{
		$('.ulastErr').html(''); 
		$('#lastname').removeClass('inputErr');
		}
		
	if(suite_location == ''){
		$('.usuiteErr').html("Suite/Location Required");
		$('#suite_location').addClass('inputErr');		
		submit_flag = 1;
		}
	else{
		$('.usuiteErr').html(''); 
		$('#suite_location').removeClass('inputErr');
		}
		
		if(phone == ''){
			$('.uofficeErr').html("Phone Number Required");
			$('#phone').addClass('inputErr');			
			submit_flag = 1;
		}
	else{
			$('.uofficeErr').html('');
			$('#phone').removeClass('inputErr');
		}
	
	if (password != confirm_password) {
            $('.passwordErr').html("Password does not match.");
			$('#password').addClass('inputErr');
			$('#confirm_password').addClass('inputErr');
			submit_flag = 1;
        }
    else{
    		$('.passwordErr').html("");
    		$('#password').removeClass('inputErr');
			$('#confirm_password').removeClass('inputErr');
    }    		
		
		if(submit_flag == 1){
			$('.loader').hide();
			return false;
		}else{
			document.getElementById("editUser").submit();
			/*var buildId = $('#building_id').val();
			$.ajax({
                url         : baseUrl+"tenant/edittuser",
                type        : "post",
                datatype    : 'json',                
                data        : {
					           firstName:firstname,
					           lastName:lastname,					           
					           phone:phone,
					           userId:userId
					           },
                success : function( result ) {
					var data = $.parseJSON(result);
					$('.loader').hide();					
                   if(data.status == 'success'){                                              
                       loadTenantUser(tId);
                       $('.message').html(data.msg);                       
                   }
                   else{                     
                      //alert('There was an error');
                      $('.message').html(data.msg);
                   }
                },
                error : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });*/
		}
}


function hideTenantInactiveUser(tId){
	$('#open_div_'+tId).show();
	$('#close_div_'+tId).hide();
	$(".message").html(''); 
	$(".error-msg").html('');
	$('#loadtenant_'+tId).html('');
	$('#trId_'+tId).hide();
	
}

function loadTenantInactiveUser(tId,bid){
	hideAllTenant();
	$('#open_div_'+tId).hide();
	$('#close_div_'+tId).show();
	$("#tenantuser_popup_"+tId).hide();	  
	if(tId!=''){
		$('.loader').show();
		$.ajax({
                url         : baseUrl+"tenant/loadtenantinactiveuser",
                type        : "post",
                datatype    : 'json',
                data        : {tId:tId,bId:bid},
                success     : function( data ) {
					$('.loader').hide(); 
                   if(data){					                        
                       $('#loadtenant_'+tId).html(data);                       
                       $('#trId_'+tId).show();
                   }else{                     
                      alert('There was an error');
                   }
                },
                error       : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });
	}else{
		alert('Some error occurred.');
	}
}


function activeTenantRecoveryUser(tId,bid){

	var confirmUser = confirm("Do you really want to active tenant and it's user?");
	hideAllTenant();
	if(confirmUser){
		if(tId!=''){
			$('.loader').show();
			$.ajax({
	                url         : baseUrl+"tenant/recoveruser",
	                type        : "post",
	                datatype    : 'json',
	                data        : {id:tId},
	                success     : function( data ) {
						 
						//alert(data);
	                   if(data){
	                   		$('.loader').hide();
	                       window.location = baseUrl+"tenant/tenantrecovery/bid/"+bid+"/msg/1";
	                   }else{  
	                   	$('.loader').hide();                   
	                      alert('There was an error');
	                   }
	                },
	                error       : function(){
						$('.loader').hide();
	                    alert('There was an error');
	                }  
	                
	             });
		}else{
			alert('Some error occurred.');
		}
	}
	
}


$(function() {
            
	$("#auto").on('click',function(){
		if(this.checked){
			$("#auto_generate").hide();
			$('#password').val("");
			$('#confirm_password').val("");
			$('#welcome_letter').val("1");
		}
		else{
			$("#auto_generate").show();
			$('#password').val("");
			$('#confirm_password').val("");
			$('#welcome_letter').val("0")
		}
	}); 

});

function validateRecoverUser(f){
	var checkedIds = $(".recover-user:checkbox:checked").map(function() {
                return this.id;
            }).get();
      if(checkedIds.length==0){
		  alert("No tenant user is selected for recover.");
		  return false;
	  }else{
		  var confirmUser = confirm("Do you really want to active selected tenant user?");
		  if(confirmUser){
			  return true;
		  }else		  
		  return false;
	  }      
}
