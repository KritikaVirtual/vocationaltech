function checkFormValidation(){
	var tenant = $('#tenant').val();		
	var create_user = $('#create_user').val();
	var date_requested = $('#date_requested').val();
	var hour = $('#hour').val();
	var minute = $('#minute').val();
	var building = $('#building').val();
	var category = $('#category').val();
	var work_order_request = $('#work_order_request').val();
	var file_val = $('#wo_file').val();
	$('input[type="submit"]').attr('disabled','disabled');
	
	if(building==''){
		$('#message').html('Select Building');
		$('input[type="submit"]').removeAttr('disabled');		
		return false;
	}else{
		$('#message').html('');
	}
	
	if(tenant==''){
		$('#message').html('Select Company');
		$('#tenant').addClass("inputErr");
		$('input[type="submit"]').removeAttr('disabled');
		return false;
	}else{
		$('#message').html('');
		$('#tenant').removeClass("inputErr");
	}
	
	/*if(suite_location==''){
		$('#message').html('Suite Required');
		$('#suite_location').addClass("inputErr");
		return false;
	}else{
		$('#message').html('');
		$('#suite_location').removeClass("inputErr");
	}*/
	
	if(create_user==''){
		$('#message').html('Select Request By');
		$('#create_user').addClass("inputErr");
		$('input[type="submit"]').removeAttr('disabled');
		return false;
	}else{
		$('#message').html('');
		$('#create_user').removeClass("inputErr");
	}
	
	if(category==''){
		$('#message').html('Select Category');
		$('#category').addClass("inputErr");
		$('input[type="submit"]').removeAttr('disabled');		
		return false;
	}else{
		$('#message').html('');
		$('#category').removeClass("inputErr");		
	}
	
	if(date_requested==''){
		$('#message').html('Requested Date Required');
		$('#date_requested').addClass("inputErr");
		return false;
	}else{
		$('#message').html('');
		$('#date_requested').removeClass("inputErr");
	}
	
	if(hour=='' || minute==''){
		$('#message').html('Requested Time Required');
		$('#hour').addClass("inputErr");	
		$('input[type="submit"]').removeAttr('disabled');	
		return false;
	}else{
		$('#message').html('');
		$('#hour').removeClass("inputErr");		
	}
	
	
	
	
	if(work_order_request==''){
		$('#message').html('Fill Work Order Request');
		$('#work_order_request').addClass("inputErr");
		$('input[type="submit"]').removeAttr('disabled');
		return false;
	}else{
		$('#message').html('');
		$('#work_order_request').removeClass("inputErr");
		//return true;
	}
	
	if(file_val!=''){
		//var logo_file = $('#wo_file')[0].files[0];
		var logo_file=document.querySelector("input[type='file']"); 
		var file_size = logo_file.size;
		/*var file_name = logo_file.name;
		var dotIndex = file_name.lastIndexOf('.');
        var ext = $('#company_logo').val().split('.').pop().toLowerCase();
        var validFileExtensions = ["jpg", "jpeg", "gif", "png"];
		if( $.inArray( ext, validFileExtensions ) == -1){			
			$('#logoErr').html('Upload logo only in jpg, png or gif format.');
			file_action2=0;
			return false;
		}*/
		
		if(file_size >(1024*1024*2)){			
			$('#message').html('file size must be less than 2 Mb.');
			file_action2 =1;
			$('input[type="submit"]').removeAttr('disabled');
			return false;
		}
		else
		 return true;
	}else{
		return true;
	}
}

function cancelUser(){	
    window.location.href = baseUrl+'dashboard/workorder';
}

function changeBuilding(bid){
		showCategory(bid);
		showTenant(bid);
		showTimeZone(bid);
		$('#building').val(bid);
	}
    function showCategory(bid){
			$.ajax({        			
			url: baseUrl + "dashboard/selectcategory/bid/"+bid,
			success: function (content) {
                //$('.loader').hide();
				$('#show_category').html(content);
				//$('#workrequest').show();
			}

		});
	}
	
	 function showTimeZone(bid){
			$.ajax({        			
			url: baseUrl + "dashboard/timezone/bid/"+bid,
			success: function (content) {
                //$('.loader').hide();
				$('#time_request_div').html(content);				
			}

		});
	}
	
	function showTenant(bid){
			$.ajax({        			
			url: baseUrl + "dashboard/selecttenant/bid/"+bid,
			success: function (content) {
                //$('.loader').hide();
				$('#show_tenant').html(content);
				//$('#workrequest').show();
			}

		});
	}
	function showTenantUser(tId){
			$.ajax({        			
			url: baseUrl + "dashboard/selecttnuser/tId/"+tId,
			success: function (content) {
                //$('.loader').hide();
				$('#show_tenant_user').html(content);
				//$('#workrequest').show();
			}

		});
	}
	
	function createUser(){
		var email = $("#email").val();
		var firstName = $("#firstName").val();
        var lastName = $("#lastName").val();
        var suite_location = $("#suite_locat").val();        
        var phoneNumber = $("#phoneNumber").val();
        var tenantId = $("#tenant").val();
        var isError = false;
         if(tenantId==''){
			$("#error_msg").html("Company is not selected.");
			isError = true; 
		}else{
			$("#error_msg").html("");
		}
        if(firstName==''){
			$("#firstName-error").html("Please enter First Name");
			isError = true; 
		}else{
			$("#firstName-error").html(''); 
		}
		
		if(lastName == ''){
			$("#lastName-error").html("Please enter Last Name");
			isError = true; 
		}else{
			$("#lastName-error").html(""); 
		}		
		
		if(email.length == 0) {
                $("#email-error").html("Please enter E-mail address"); 
                isError = true;              
            } else if( !isValidEmailAddress( email ) ) {
                $("#email-error").html("Please enter valid E-mail address");                
                isError = true;
            } else {
                 $("#email-error").html("");
            }
           
       if(suite_location == ''){		   
			$("#suite_location-error").html("Please enter Suite/Location"); 
			isError = true;
		}else{
			$("#suite_location-error").html(""); 
		}
		
		if(phoneNumber == ''){
			$("#phone-error").html("Please enter Phone Number");
			isError = true; 
		}else{
			$("#phone-error").html(""); 
		}
		
		if(!isError){
			$('.loader').show();
			 $.ajax({
                url         : baseUrl+"tenant/createtuser",
                type        : "post",
                datatype    : 'json',
                data        : {firstName:firstName,lastName:lastName,email:email,suite_location:suite_location,phoneNumber:phoneNumber,tenantId:tenantId},
                success     : function( content ) { 					
					 $('.loader').hide();
					 var data = JSON.parse(content);
					 //alert(data.status); 					
                   if(data.status == 'error'){
					   $('#error_msg').html(data.msg);                     
                   }else if(data.status =='success'){
					   $('#success_msg').html(data.msg);
					   resetForm();
					   showTenantUser(tenantId);
					   $('#add_new_div').hide();
				   }
                },
                error       : function(){
					$('.loader').hide();					 
                    alert('There was an error');
                }  
                
             });
		}   
	}
	
	$(function() {
	$( "#date_requested" ).datepicker({
		 dateFormat:'mm/dd/yy',
		 changeMonth: true,
		 changeYear: true
		 });
	$("#phoneNumber").mask("999.999.9999");	 
    		 		 
		 
});

function showAddNew(){
	document.getElementById("new_user_form").reset();
	$("#error_msg").html("");
	$("#firstName-error").html("");
	$("#lastName-error").html("");
	$("#email-error").html("");
	$("#suite_location-error").html("");
	$("#phone-error").html("");
	$('#add_new_div').show();
}
function cancelForm(){
	resetForm();
	$('#add_new_div').hide();
}
function resetForm(){
	document.getElementById("new_user_form").reset();
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
}
