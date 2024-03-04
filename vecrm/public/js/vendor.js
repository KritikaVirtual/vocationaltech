function validateVendor(){
	company_name = $("#company_name").val();
	first_name = $("#first_name").val();
	last_name = $("#last_name").val();
	services = $("#services").val();
	contact_type = $("#contact_type").val();
	phone_number = $("#phone_number").val();
	email = $("#email").val();
	address1 = $("#address1").val();
	city = $("#city").val();
	state = $("#state").val();
	postal_code = $("#postal_code").val();
	bid =$('#buildingId').val();
	var isError = false;
	if(company_name==''){
		$("#company-error").html("Please enter Company Name");
			isError = true;
	}else{
		$("#company-error").html('');
	}
	
	if(first_name==''){
		$("#first_name-error").html("Please enter First Name");
			isError = true;
	}else{
		$("#first_name-error").html('');
	}
	
	if(last_name==''){
		$("#last_name-error").html("Please enter Last Name");
			isError = true;
	}else{
		$("#last_name-error").html('');
	}
	
	if(services==''){
		$("#services-error").html("Please select Service");
			isError = true;
	}else{
		$("#services-error").html('');
	}
	
	if(contact_type==''){
		$("#contact-error").html("Please select contact type");
			isError = true;
	}else{
		$("#contact-error").html('');
	}
	
	
	if(phone_number==''){
		$("#phone_number-error").html("Please enter Phone Number");
			isError = true;
	}else{
		$("#phone_number-error").html('');
	}
	
	if(email!=''){ 
		if(!validateEmail(email)){
			  $('#email-error').html("E-Mail Address Invalid");			
			isError = true;
			}else{
			  $('#email-error').html("");
			}
		}
	if(address1==''){
		$("#address-error").html("Please enter Address");
			isError = true;
	}else{
		$("#address-error").html('');
	}
	
	if(city==''){
		$("#city-error").html("Please enter City");
			isError = true;
	}else{
		$("#city-error").html('');
	}
	
	if(state==''){
		$("#state-error").html("Please enter State");
			isError = true;
	}else{
		$("#state-error").html('');
	}
	
	if(postal_code==''){
		$("#postal-error").html("Please enter Postal Code");
			isError = true;
	}else{
		$("#postal-error").html('');
	}
	
	if(!isError){
		//document.addNewVendor.submit();
		validateVendorName(company_name,bid);
	}
}

function validateVendorName(cname,bid){	
	if(bid!=''){
		email = $("#email").val();
		$('.loader').show();
		$.ajax({
                url         : baseUrl+"vendor/checkvendor",
                type        : "post",
                datatype    : 'json',
                data        : {bid:bid,cname:cname},
                success     : function( data ) {
					$('.loader').hide();					 
                   if(data=='true'){					                        
                      $("#company-error").html("Company Name already in use.");
                   }else{  
					   if(email==""){                   
                         document.addNewVendor.submit();
				       }else{
						   checkVendorEmail(email);
					   }
                   }
                },
                error       : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });
	}
}

function checkVendorEmail(email){	
	if(email!=''){		
		$('.loader').show();
		$.ajax({
                url         : baseUrl+"vendor/checkemail",
                type        : "post",
                datatype    : 'json',
                data        : {email:email},
                success     : function( data ) {
					$('.loader').hide();					 
                   if(data=='true'){
					  document.addNewVendor.submit();                    
                   }else if(data=='false'){  
					  $('#email-error').html("E-Mail Address Already in use.");	
                   }else{
				   }
                },
                error       : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });
	}
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


$(function() {
			$("#postal_code").mask("99999");
			$("#phone_number").mask("999.999.9999");
			$("#cell_number").mask("999.999.9999");			
			});
			
function validateEmail(sEmail) {
	    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	    if (filter.test(sEmail)) {
	        return true;
	    }
	    else {
	        return false;
	    }
 }
 
 
 function cancelVendor(bId){
	 window.location.href = baseUrl+"vendor/index/bid/"+bId;
 }
 
 function hideVendor(vid){
	$('#open_div_'+vid).show();
	$('#close_div_'+vid).hide();
	$(".message").html(''); 
	$(".error-msg").html('');
	$('#vendor_data_'+vid).html('');
	$('#vendor_tr_'+vid).hide();
	
}
function hideAllVendor(){
	$('.open_plus').show();
	$('.open_close').hide();
	$('.trvendor-class').hide();
	$('.tdvendor-class').html('');
}
function loadVendor(vid){
	hideAllVendor();
	$('#open_div_'+vid).hide();
	$('#close_div_'+vid).show();	 
	if(vid!=''){
		$('.loader').show();
		$.ajax({
                url         : baseUrl+"vendor/loadvendor",
                type        : "post",
                datatype    : 'json',
                data        : {vid:vid},
                success     : function( data ) {
					$('.loader').hide(); 
                   if(data){					                        
                       $('#vendor_data_'+vid).html(data);                       
                       $('#vendor_tr_'+vid).show();
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


function showServiceForm(){
	$('#add-service-error').html('');
	$('#service').val('');	
	$('#add_new_service_div').show();
}

function cancelAddService(){	
	//$("#services option:first-child").attr('selected',true);
	$("#services").prop("selectedIndex", 0);
	$('#add_new_service_div').hide();
}

function addService(){
	var service = $('#service').val();
	if(service==''){
		$('#add-service-error').html('Please Enter New Service Name');
	}else{
		$.ajax({
                url         : baseUrl+"vendor/addservice",
                type        : "post",
                datatype    : 'json',
                data        : {service:service},
                success     : function( data ) {
					$('.loader').hide(); 
                   if(data=='true'){
					   cancelAddService();					                        
                       showVendorService();
                   }else if(data=='false'){                     
                      $('#add-service-error').html('This Service Name already in use.');
                   }else{
					   $('#add-service-error').html('Error Occurred during insert service.');
				   }
                },
                error       : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });		
	}	
}

function showVendorService(){
	$.ajax({
                url         : baseUrl+"vendor/showservice",
                type        : "post",
                datatype    : 'json',
                success     : function( data ) {
					$('.loader').hide(); 
                   $('#service_dropdown').html(data);
                },
                error       : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });
}

function showContactType(){
	$.ajax({
                url         : baseUrl+"vendor/showcontact",
                type        : "post",
                datatype    : 'json',                
                success     : function( data ) {
					$('.loader').hide(); 
                   $('#contact_dropdown').html(data);
                },
                error       : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });
}
function showContactForm(){	
	$('#add-contact-error').html('');
	$('#contact').val('');	
	$('#add_new_contact_div').show();
}

function cancelContact(){
	$("#contact_type").prop("selectedIndex", 0);
	$('#add_new_contact_div').hide();
}

function addContact(){
	var contact = $('#contact').val();
	if(contact==''){
		$('#add-contact-error').html('Please Enter New Contact Type');
	}else{
		$.ajax({
                url         : baseUrl+"vendor/addcontact",
                type        : "post",
                datatype    : 'json',
                data        : {contact:contact},
                success     : function( data ) {
					$('.loader').hide(); 
                   if(data=='true'){
					   cancelContact();					                        
                       showContactType();
                   }else if(data=='false'){                     
                      $('#add-contact-error').html('This Contact Type already in use.');
                   }else{
					   $('#add-contact-error').html('Error Occurred during insert contact type.');
				   }
                },
                error: function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });		
	}	
}

function addVendorContact(){
	first_name = $("#first_name").val();
	last_name = $("#last_name").val();	
	phone_number = $("#phone_number").val();
	email = $("#email").val();
	var isError =false;
	if(first_name==''){
		$("#first_name-error").html("Please enter First Name");
			isError = true;
	}else{
		$("#first_name-error").html('');
	}
	
	if(last_name==''){
		$("#last_name-error").html("Please enter Last Name");
			isError = true;
	}else{
		$("#last_name-error").html('');
	}	
	
	if(phone_number==''){
		$("#phone_number-error").html("Please enter Phone Number");
			isError = true;
	}else{
		$("#phone_number-error").html('');
	}
	
	if(email!=''){ 
		if(!validateEmail(email)){
			  $('#email-error').html("E-Mail Address Invalid");			
			isError = true;
			}else{
			  $('#email-error').html("");
			}
	}
	if(!isError){
		if(email==''){
			document.addNewVContact.submit();
		}else{
			checkVendorContactEmail(email);
		}
		
	}
}


function checkVendorContactEmail(email){	
	if(email!=''){		
		$('.loader').show();
		$.ajax({
                url         : baseUrl+"vendor/checkemail",
                type        : "post",
                datatype    : 'json',
                data        : {email:email},
                success     : function( data ) {
					$('.loader').hide();					 
                   if(data=='true'){
					  document.addNewVContact.submit();                    
                   }else if(data=='false'){  
					  $('#email-error').html("E-Mail Address Already in use.");	
                   }else{
				   }
                },
                error       : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });
	}
}

function cancelVendorContact(){
	var vid = $("#vid").val();
	var bid = $("#building").val();
	window.location.href = baseUrl+"vendor/index/bid/"+bid+"/vid/"+vid;
}

function editVendorContact(){
	first_name = $("#first_name").val();
	last_name = $("#last_name").val();	
	phone_number = $("#phone_number").val();
	email = $("#email").val();
	var current_email = $("#current_email").val();
	var isError =false;
	if(first_name==''){
		$("#first_name-error").html("Please enter First Name");
			isError = true;
	}else{
		$("#first_name-error").html('');
	}
	
	if(last_name==''){
		$("#last_name-error").html("Please enter Last Name");
			isError = true;
	}else{
		$("#last_name-error").html('');
	}	
	
	if(phone_number==''){
		$("#phone_number-error").html("Please enter Phone Number");
			isError = true;
	}else{
		$("#phone_number-error").html('');
	}
	
	if(email!=''){ 
		if(!validateEmail(email)){
			  $('#email-error').html("E-Mail Address Invalid");			
			isError = true;
			}else{
			  $('#email-error').html("");
			}
	}
	if(!isError){
		if(email=='' || (current_email == email)){
			document.addNewVContact.submit();
		}else{
			checkVendorContactEmail(email);
		}
		
	}
}


function deleteVendor(vid){
	var check_delete='YES';
	var bid = $('#building_id').val();
	var return_value = prompt("For Deleting Vendor, Enter Yes in Capital letter.");
	if(return_value!=null){	
		if(check_delete === return_value){
			$.ajax({
					url         : baseUrl+"vendor/removevendor",
					type        : "post",
					datatype    : 'json',                
					data        : {
								   vid:vid
								   },
					success : function( result ) {
						
						if(result=='true'){
							$('.success_message').html('Vendor deleted successfully.');
							window.location.href=baseUrl+"vendor/index/bid/"+bid;
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

function deleteVendorContact(vcId,vid){
	var check_delete='YES';
	var bid = $('#building_id').val();
	var return_value = prompt("For Deleting Alternate Vendor Contact, Enter Yes in Capital letter.");
	if(return_value!=null){	
		if(check_delete === return_value){
			$.ajax({
					url         : baseUrl+"vendor/removevcontact",
					type        : "post",
					datatype    : 'json',                
					data        : {
								   vcId:vcId
								   },
					success : function( result ) {						
						if(result=='true'){
							$('.success_message').html('Alternate Vendor Contact deleted successfully.');
							window.location.href=baseUrl+"vendor/index/bid/"+bid+"/vid/"+vid;
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

function checkSearch(){
	var search_key = $('#search_by').val();
	var search_value = $('#search_value').val();
	var service_value = $('#service_value').val();
	var submit_flag = false;	
	if(search_key!='services'){
		if(search_value==''){
			$('#search_message').html('Enter serach value.');
		}else
		submit_flag = true;
	}else{
		if(service_value==''){
			$('#search_message').html('Select Service Option.');
		}else
		submit_flag = true;
	}
	
	if(submit_flag){
		return true;
	}else{
		return false;
	}
}

function showServiceDropdown(sh){
	if(sh.value=='services'){
		$('#search_txt').hide();
		$('#service_select').show();
	}else{
		$('#search_txt').show();
		$('#service_select').hide();
	}
}
