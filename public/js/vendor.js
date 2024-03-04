function validateVendor(redirectpage){
$('#savevendor').attr('disabled',true);
parent.CheckForSessionpop(baseUrl);
	company_name = $("#company_name").val();
	first_name = $("#first_name").val();
	last_name = $("#last_name").val();
	services = $("#services").val();
	contact_type = $("#contact_type").val();
	phone_number = $("#phone_number").val();
	cell_number = $("#cell_number").val();
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
	} else if(phone_number.length < 12){
		$("#phone_number-error").html("Please enter 10 digits Phone Number");
			isError = true;
	} else{
		$("#phone_number-error").html('');
	}
	
	if(cell_number.length > 0 && cell_number.length < 12) {
		$("#cell_number-error").html("Please enter 10 digits Cell Number");
		isError = true;
	} else {
	$("#cell_number-error").html('');
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
	} else if(postal_code.length < 5){
		$("#postal-error").html("Please enter 5 digits Postal Code");
			isError = true;
	}else{
		$("#postal-error").html('');
	}
	
	if(!isError){
		//document.addNewVendor.submit();
		validateVendorName(company_name,bid,redirectpage);
	} else {
		$('#savevendor').attr('disabled',false);
	}
}

function validateVendorName(cname,bid,redirectpage){	
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
                         //document.addNewVendor.submit();
						 addVendor(redirectpage);
				       }else{
						   checkVendorEmail(email);
					   }
                   }
                },
                error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');
                }  
                
             });
	}
}
function addVendor(redirectpage){ 
	
	var fdata	=	$("form#addNewVendor").serialize();				
	var action		=	$("form#addNewVendor").attr('action');				  
	$.post( action, fdata, function( data ) {
		data=$.parseJSON(data);
		$('div.success_message').html(data.msg);
		var main_url = baseUrl+data.url;
		if(redirectpage=='material') {
            $("#vendorredirect").trigger("click");		
		} 
		else {
		    setInterval(function(){ window.parent.location.href = main_url; }, 1000);
		}
	});
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
					  //document.addNewVendor.submit();                    
					  addVendor();
                   }else if(data=='false'){  
					  $('#email-error').html("E-Mail Address Already in use.");	
                   }else{
				   }
                },
                error       : function(){
					$('.loader').hide();
 					jAlert('There was an error', 'Vision Work Orders');
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


// $(function() {
// 			$("#postal_code").mask("?99999");
// 			$("#phone_number").mask("?999.999.9999");
// 			$("#cell_number").mask("?999.999.9999");			
// 			});
			
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
	//window.location.href = baseUrl+"vendor/index/bid/"+bId;
        parent.jQuery.fancybox.close();
	//parent.jQuery("#show_alt_vendor_list_div_href").fancybox();
	//parent.jQuery('#show_alt_vendor_list_div_href').trigger('click');
	
	window.parent.jQuery('#show_alt_vendor_list_func').trigger('click');;
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
					  jAlert('There was an error', 'Vision Work Orders');
                   }
                },
                error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');
                }  
                
             });
	}else{
		jAlert('Some error occurred', 'Vision Work Orders');
	}
}	


function showServiceForm(){
	$('#add-service-error').html('');
	$('#service').val('');	
	//$('#add_new_service_div').show();
        $('#add_new_service_div_href').trigger('click');
}

function cancelAddService(){	
	$("div.fancybox-close").trigger("click");
	//$("#services option:first-child").attr('selected',true);
	$("#services").prop("selectedIndex", 0);
	$('#add_new_service_div').hide();
}

function addService(){
	var service = $('#service').val();
	var building = $('#buildingId').val();
	if(service==''){
		$('#add-service-error').html('Please Enter New Service Name');
	}else if(building==''){
		$('#add-service-error').html('Building Id is missing');
	}
	else{
		$.ajax({
                url         : baseUrl+"vendor/addservice",
                type        : "post",
                datatype    : 'json',
                data        : {service:service,building:building},
                success     : function( data ) {
					$('.loader').hide(); 
                   if(data=='true'){
					   cancelAddService();					                        
                       showVendorService(building);
                   }else if(data=='false'){                     
                      $('#add-service-error').html('This Service Name already in use.');
                   }else{
					   $('#add-service-error').html('Error Occurred during insert service.');
				   }
                },
                error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');

                }  
                
             });		
	}	
}

function showVendorService(building){ 
	$.ajax({
                url         : baseUrl+"vendor/showservice",
                type        : "post",
                datatype    : 'json',
				data 		: { building:building },
                success     : function( data ) {
					$('.loader').hide(); 
                   $('#service_dropdown').html(data);
                },
                error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');
                }  
                
             });
}

function showContactType(building){
	$.ajax({
                url         : baseUrl+"vendor/showcontact",
                type        : "post",
                datatype    : 'json',
				data        : { building:building },              
                success     : function( data ) {
					$('.loader').hide(); 
                   $('#contact_dropdown').html(data);
                },
                error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');
                }  
                
             });
}
function showContactForm(){	
	$('#add-contact-error').html('');
	//$('#contact').val('');	
	//$('#add_new_contact_div').show();
	$('#add_new_contact_div_href').trigger('click');
}

function cancelContact(){
	$("div.fancybox-close").trigger("click");
	$("#contact_type").prop("selectedIndex", 0);
	$('#add_new_contact_div').hide();
}

function addContact(){
	var contact = $('#contact').val();
	var building = $('#buildingId').val();
	if(contact==''){
		$('#add-contact-error').html('Please Enter New Contact Type');
	}else if(building==''){
		$('#add-contact-error').html('Building Id is missing');
	}else{
		$.ajax({
                url         : baseUrl+"vendor/addcontact",
                type        : "post",
                datatype    : 'json',
                data        : {contact:contact,building:building},
                success     : function( data ) {
					$('.loader').hide(); 
                   if(data=='true'){
					   cancelContact();					                        
                       showContactType(building);
                   }else if(data=='false'){                     
                      $('#add-contact-error').html('This Contact Type already in use.');
                   }else{
					   $('#add-contact-error').html('Error Occurred during insert contact type.');
				   }
                },
                error: function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');
                }  
                
             });		
	}	
}

function addVendorContact(){
	$('#savevendorcontact').attr('disabled',true);
	parent.CheckForSessionpop(baseUrl);
	first_name = $("#first_name").val();
	last_name = $("#last_name").val();	
	phone_number = $("#phone_number").val();
	email = $("#email").val();
	cell_number = $("#cell_number").val();
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
	} else if(phone_number.length < 12){
		$("#phone_number-error").html("Please enter 10 digits Phone Number");
			isError = true;
	}else{
		$("#phone_number-error").html('');
	}
	if(cell_number.length > 0 && cell_number.length < 12){
		$("#cell_number-error").html("Please enter 10 digits Cell Number");
			isError = true;
	} else{
		$("#cell_number-error").html('');
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
			//document.addNewVContact.submit();
			addNewVContact();
		}else{
			checkVendorContactEmail(email);
		}
		
	} else {
		$('#savevendorcontact').attr('disabled',false);
	}
}
function addNewVContact(){
	
	$('#savevendorcontact').attr('disabled',true);
	var fdata	=	$("form#addNewVContact").serialize();				
	var action		=	$("form#addNewVContact").attr('action');				  
	$.post( action, fdata, function( data ) {
		data=$.parseJSON(data);
		$('div.success_message').html(data.msg);
		var main_url = baseUrl+data.url;
		setInterval(function(){ window.parent.location.href = main_url; }, 1000);
	});	
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
					//document.addNewVContact.submit();     
					addNewVContact();					  
                   }else if(data=='false'){  
					  $('#email-error').html("E-Mail Address Already in use.");	
					  $('#savevendorcontact').attr('disabled',false);
                   }else{
				   }
                },
                error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');
                }  
                
             });
	}
}

function cancelVendorContact(){
	$("div.fancybox-close").trigger("click");
	var vid = $("#vid").val();
	var bid = $("#building").val();
	//window.location.href = baseUrl+"vendor/index/bid/"+bid+"/vid/"+vid;
	parent.jQuery.fancybox.close();
}

function editVendorContact(){
	parent.CheckForSessionpop(baseUrl);
	first_name = $("#first_name").val();
	last_name = $("#last_name").val();	
	phone_number = $("#phone_number").val();
	cell_number = $("#cell_number").val();
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
	} else if(phone_number.length < 12){
		$("#phone_number-error").html("Please enter 10 digits Phone Number");
			isError = true;
	}else{
		$("#phone_number-error").html('');
	}
	
	if(cell_number.length > 0 && cell_number.length < 12){
		$("#cell_number-error").html("Please enter 10 digits Cell Number");
			isError = true;
	} else {
	$("#cell_number-error").html('');
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
			//document.addNewVContact.submit();
			addNewVContact();
		}else{
			checkVendorContactEmail(email);
		}
		
	}
}


function deleteVendor(vid){
	var check_delete='YES';
	var bid = $('#building_id').val();
	jPrompt('For Deleting Vendor, Enter Yes in Capital letters', '', 'Vision Work Orders', function(return_value) {
	if(return_value!=null){	
		if(check_delete === return_value){
			$('.loader').show();						 			
			$.ajax({
					url         : baseUrl+"vendor/removevendor",
					type        : "post",
					datatype    : 'json',                
					data        : {
								   vid:vid
								   },
					success : function( result ) {
						$('.loader').hide();						 						
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
			jAlert('You have entered wrong word', 'Vision Work Orders');
		}
	}
	});	

	
}

function deleteVendorContact(vcId,vid){
	var check_delete='YES';
	var bid = $('#building_id').val();
	jPrompt('For Deleting Alternate Vendor Contact, Enter Yes in Capital letters.', '', 'Vision Work Orders', function(return_value) {
		if(return_value!=null){	
			if(check_delete === return_value){
				$('.loader').show();
				$.ajax({
						url         : baseUrl+"vendor/removevcontact",
						type        : "post",
						datatype    : 'json',                
						data        : {
									   vcId:vcId
									   },
						success : function( result ) {	
							$('.loader').hide();						
							if(result=='true'){
								$('.success_message').html('Alternate Vendor Contact deleted successfully.');
								window.location.href=baseUrl+"vendor/index/bid/"+bid+"/vid/"+vid;
							}else{
								$('.error-txt').html('Some error occurred.');
							}					
						}
					});	
			}else{
				jAlert('You have entered wrong word.','Vision Work Orders');				
			}
		}
	});
	
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
// $(function() {
// 	$(".modalbox").fancybox({'openEffect': 'none',fitToView: true});
// });

function addNewVendor(url){
    CheckForSessionpop(baseUrl);
	$('a[href="#addNewVendor"]').fancybox({	
		type: 'iframe',
		href: url,
		width: 750,
		height: 600,
		'beforeClose' : function(){
			$('.loader').hide();						 
		},
		'afterLoad': function() {
			$.fancybox.hideLoading();
			$('.loader').show();	
			setInterval(function() {$('.loader').hide();}, 5000);		
		},
		afterShow: function () {
			$('iframe.fancybox-iframe').contents().find("#company_name").attr("tabindex",1).focus();
		}		
	});			
}

function addNewVendorTemplate(url){
    CheckForSessionpop(baseUrl);
	$('a[href="#addNewVendorTemplate"]').fancybox({	
		type: 'iframe',
		href: url,
		width: 750,
		height: 600,
		'beforeClose' : function(){
			$('.loader').hide();						 
		},
		'afterLoad': function() {
			$.fancybox.hideLoading();
			$('.loader').show();	
			setInterval(function() {$('.loader').hide();}, 5000);		
		},
		afterShow: function () {
			$('iframe.fancybox-iframe').contents().find("#company_name").attr("tabindex",1).focus();
		}		
	});			
}


function addVendorNewContact(url){
	$('a[href="#addVendorNewContact"]').fancybox({	
		type: 'iframe',
		href: url,
		width: 800,
		height: 600,
		'beforeClose' : function(){
			$('.loader').hide();						 
		},
		'afterLoad': function() {
			$.fancybox.hideLoading();
			$('.loader').show();	
			setInterval(function() {$('.loader').hide();}, 5000);		
		},
		afterShow: function () {
			$('iframe.fancybox-iframe').contents().find("#first_name").attr("tabindex",1).focus();
		}		
	});				
}

function editVendorNewContact(url){
    CheckForSessionpop(baseUrl);
	$('a[href="#editVendorNewContact"]').fancybox({	
		type: 'iframe',
		href: url,
		width: 800,
		height: 600,
		'beforeClose' : function(){
			$('.loader').hide();						 
		},
		'afterLoad': function() {
			$.fancybox.hideLoading();
			$('.loader').show();	
			setInterval(function() {$('.loader').hide();}, 5000);		
		} 		
	});				
}

function getVendor() {
var vid= $('#company_name').find('option:selected'); 
vid=vid.attr("data-rel");
if(vid!=''){		
		$('.loader').show();
		$.ajax({
                url         : baseUrl+"vendor/getvendor",
                type        : "post",
                datatype    : 'json',
                data        : {vid:vid},
                success     : function( data ) { data=$.parseJSON(data);  
				
				
					$('.loader').hide();					 
                   if(data.msg=='success'){	
				    if(data.last_name!='') {
						$('#last_name').val(data.last_name);
					}
					if(data.contact_type) {
						$('#contact_type').val(data.contact_type);
					}
					if(data.cell_number) {
						$('#cell_number').val(data.cell_number);
					}
					if(data.account_number) {
						$('#account_number').val(data.account_number);
					}
					if(data.city) {
						$('#city').val(data.city);
					}
					if(data.postal_code) {
						$('#postal_code').val(data.postal_code);
					}
					if(data.first_name) {
						$('#first_name').val(data.first_name);
					}
					if(data.phone_number) {
						$('#phone_number').val(data.phone_number);
					}
					if(data.email) {
						$('#email').val(data.email);
					}
					if(data.address1) {
						$('#address1').val(data.address1);
					}
					if(data.address2) {
						$('#address2').val(data.address2);
					}
					if(data.state_code) {
						$('#state').val(data.state_code);
					}
					if(data.emergency_contact) {
						$('#emergency_contact').val(data.emergency_contact);
					}
					
					var servicesexist= $("#services option:contains("+data.servicename+")").length;
					if(servicesexist <= 0) {
						$("#services").append('<option value="0" selected>'+data.servicename+'</option>');
						$('#service_type_text').val(data.servicename);
					} else {
						$('#services option').filter(function() { 
							return ($(this).text() == data.servicename); 
							}).prop('selected', true); 
					}
					var contact_typeexist= $("#contact_type option:contains("+data.contactname+")").length;  
                                        //alert(contact_typeexist);
					if(contact_typeexist <= 0) {  
						$("#contact_type").append('<option value="0" selected>'+data.contactname+'</option>');
						$('#contact_type_text').val(data.contactname);
					} else { $('#contact_type option').filter(function() { 
							return ($(this).text() == data.contactname); 
							}).prop('selected', true); 
					}
			   
                   }else {  
					  jAlert('There was an error', 'Vision Work Orders');
                   }
                },
                error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');
                }  
                
             });
	}

}

// Vendor Template


function validateVendorTemplate(redirectpage){
	$('#saveemailtemplate').attr('disabled',true);
	company_name = $("#company_name").val();
	first_name = $("#first_name").val();
	last_name = $("#last_name").val();
	services = $("#services").val();
	contact_type = $("#contact_type").val();
	phone_number = $("#phone_number").val();
	cell_number = $("#cell_number").val();
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
	} else if(phone_number.length < 12){
		$("#phone_number-error").html("Please enter 10 digits Phone Number");
			isError = true;
	} else{
		$("#phone_number-error").html('');
	}
	
	if(cell_number.length > 0 && cell_number.length < 12) {
	$("#cell_number-error").html("Please enter 10 digits Cell Number");
		isError = true;
	} else {
	$("#cell_number-error").html('');
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
	} else if(postal_code.length < 5){
		$("#postal-error").html("Please enter 5 digits Postal Code");
			isError = true;
	}else{
		$("#postal-error").html('');
	}
	
	if(!isError){
		//document.addNewVendor.submit();
		validateVendorNameTemplate(company_name,bid,redirectpage);
	} else {
		$('#saveemailtemplate').attr('disabled',false);
	}
}

function validateVendorNameTemplate(cname,bid,redirectpage){	
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
                         //document.addNewVendor.submit();
						 addVendorTemplate(redirectpage);
				       }else{
						   checkVendorEmailTemplate(email);
					   }
                   }
                },
                error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');
                }  
                
             });
	}
}
function addVendorTemplate(redirectpage){
	var fdata	=	$("form#addNewVendor").serialize();				
	var action		=	$("form#addNewVendor").attr('action');				  
	$.post( action, fdata, function( data ) {
		data=$.parseJSON(data);
		$('div.success_message').html(data.msg);
		var main_url = baseUrl+data.url;
		if(redirectpage=='material') {
            $("#vendorredirect").trigger("click");		
		} 
		else {
		    setInterval(function(){ window.parent.location.href = main_url; }, 1000);
		}
	});
}

function checkVendorEmailTemplate(email){	
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
					  //document.addNewVendor.submit();                    
					  addVendor();
                   }else if(data=='false'){  
					  $('#email-error').html("E-Mail Address Already in use.");	
					  $('#saveemailtemplate').attr('disabled',false);
                   }else{
				   }
                },
                error       : function(){
					$('.loader').hide();
 					jAlert('There was an error', 'Vision Work Orders');
                }  
                
             });
	}
}

// for vendorerecovery created bt durgesh

function hideVendorInactiveUser(vId) {
	debugger
    $('#open_div_' + vId).show();
    $('#close_div_' + vId).hide();
    $(".message").html('');
    $(".error-msg").html('');
    $('#loadvendor_' + vId).html('');
    $('#trId_' + vId).hide();

}

function hideAllTenant() {
    $('.open_plus').show();
    $('.open_close').hide();
    $('.trvendor-class').hide();
    $('.tdvendor-class').html('');
}

function activeVendorRecoveryUser(tId, bid) {
    jConfirm("Do you really want to active Vendor and it's user?", 'Confirmation Dialog', function (r) {
        hideAllTenant();
        if (r == true) {
            if (tId != '') {
                $('.loader').show();
                $.ajax({
                    url: baseUrl + "vendor/recoveruser",
                    type: "post",
                    datatype: 'json',
                    data: {id: tId},
                    success: function (data) {
                        if (data) {
                            $('.loader').hide();
                            window.location = baseUrl + "vendor/vendorrecovery/bid/" + bid + "/msg/1";
                        } else {
                            $('.loader').hide();
                            jAlert('There was an error');
                        }
                    },
                    error: function () {
                        $('.loader').hide();
                        jAlert('There was an error');
                    }

                });
            } else {
                jAlert('Some error occurred.');
            }
        }
    });

}

function loadVendorInactiveUser(vId, bid) {
	debugger
    hideAllVendor();
    $('#open_div_' + vId).hide();
    $('#close_div_' + vId).show();
    $("#vendoruser_popup_" + vId).hide();
    if (vId != '') {
        $('.loader').show();
        $.ajax({
            url: baseUrl + "vendor/loadvendorinactiveuser",
            type: "post",
            datatype: 'json',
            data: {vId: vId, bId: bid},
            success: function (data) {
                $('.loader').hide();
                if (data) {
                    $('#loadvendor_' + vId).html(data);
                    $('#trId_' + vId).show();
                } else {
                    jAlert('There was an error');
                }
            },
            error: function () {
                $('.loader').hide();
                jAlert('There was an error');
            }

        });
    } else {
        jAlert('Some error occurred.');
    }
}