function validateMaterial(){
	$('#creatematerial').attr('disabled',true);
	parent.CheckForSessionpop(baseUrl);
	date_created = $("#date_created").val();
	description = $("#description").val();
	service = $("#services").val();
	cost = $("#cost").val();
	//vendor = $("#vendor").val();	
	bid =$('#buildingId').val();
	var isError = false;
	if(date_created==''){
		$("#date-error").html("Date created can't be blank.");
			isError = true;
	}else{
		$("#date-error").html('');
	}
	
	if(description==''){
		$("#description-error").html("Please enter Description");
			isError = true;
	}else{
		$("#description-error").html('');
	}
	
	if(service==''){
		$("#services-error").html("Please select Service");
			isError = true;
	}else{
		$("#services-error").html('');
	}
	
	if(cost==''){
		$("#cost-error").html("Please enter Cost");
			isError = true;
	}else{
		$("#cost-error").html('');
	}
	
	if(cost!=''){
		if(!validateDecimal(cost)){
			$("#cost-error").html("Invalid cost value");
			isError = true;
		}else{
			$("#cost-error").html('');
		}
	}
	
/*	if(vendor==''){
		$("#vendor-error").html("Please select Vendor");
			isError = true;
	}else{
		$("#vendor-error").html('');
	}	
	
*/	if(isError){
		$('#creatematerial').attr('disabled',false);
	}
	
	if(!isError){
		//document.addNewMaterial.submit();
		//validateVendorName(company_name,bid);
		var fdata	=	$("form#addNewMaterial").serialize();
		var action		=	$("form#addNewMaterial").attr('action');
		  $.post( action, fdata, function( data ) {
			$('.success_message').html('Material added successfully.');							
			setInterval(function(){ parent.location.reload(); }, 2000);							
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

			
function validateEmail(sEmail) {
	    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	    if (filter.test(sEmail)) {
	        return true;
	    }
	    else {
	        return false;
	    }
 }
 
 $(function() {
			$("#postal_code").mask("99999");
			$("#phone_number").mask("999.999.9999");
			$("#cell_number").mask("999.999.9999");			
			});
 function cancelMaterial(bId){
	parent.jQuery.fancybox.close();
	//window.location.href = baseUrl+"material/index/bid/"+bId;
 }
 
 function hideMaterial(mid){
	$('#open_div_'+mid).show();
	$('#close_div_'+mid).hide();
	$(".message").html(''); 
	$(".error-msg").html('');
	$('#material_data_'+mid).html('');
	$('#material_tr_'+mid).hide();
	
}
function hideAllMaterial(){
	$('.open_plus').show();
	$('.open_close').hide();
	$('.trmaterial-class').hide();
	$('.tdmaterial-class').html('');
}
function loadMaterial(mid){
	hideAllMaterial();
	$('#open_div_'+mid).hide();
	$('#close_div_'+mid).show();	 
	if(mid!=''){
		$('.loader').show();
		$.ajax({
                url         : baseUrl+"material/loadmaterial",
                type        : "post",
                datatype    : 'json',
                data        : {mid:mid},
                success     : function( data ) {
					$('.loader').hide(); 
                   if(data){					                        
                       $('#material_data_'+mid).html(data);                       
                       $('#material_tr_'+mid).show();
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
		jAlert('Some error occurred.', 'Vision Work Orders');		
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
	var service = $('#service_opt').val();
	var building = $('#buildingId').val(); 
	if(service==''){
		$('#add-service-error').html('Please Enter New Service Name');
	}else if(building==''){
		$('#add-service-error').html('Building Id is missing');
	}else{
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
				data        : {building:building},
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




function deleteMaterial(mid){
	var check_delete='YES';
	var bid = $('#building_id').val();
	jPrompt('For Deleting Material, Enter Yes in Capital letters.', '', 'Vision Work Orders', function(return_value) {
	if(return_value!=null){	
		if(check_delete === return_value){
			$('.loader').show();
			$.ajax({
					url         : baseUrl+"material/removematerial",
					type        : "post",
					datatype    : 'json',                
					data        : {
								   mid:mid
								   },
					success : function( result ) {
						$('.loader').hide();
						
						if(result=='true'){
							$('.success_message').html('Material deleted successfully.');
							window.location.href=baseUrl+"material/index/bid/"+bid;
						}else{
							$('.error-txt').html('Some error occurred.');
						}					
					}
				});	
		}else{
			jAlert('You have entered wrong word.', 'Vision Work Orders');
		}
	}
	});

	
}

function checkSearch(){
	var search_key = $('#search_by').val();
	var search_value = $('#search_value').val();
	var service_value = $('#service_value').val();
	var vendor_value = $('#vendor_value').val();
	var submit_flag = false;	
	if(search_key!='service' &&  search_key!='vendor'){
		if(search_value==''){
			$('#search_message').html('Enter serach value.');
		}else
		submit_flag = true;
	}else{
		if(service_value=='' && search_key=='service'){
			$('#search_message').html('Select Option.');
		}else if(vendor_value=='' && search_key=='vendor'){
			$('#search_message').html('Select Option.');
			}else
		submit_flag = true;
	}
	
	if(submit_flag){
		return true;
	}else{
		return false;
	}
}

function showSVDropdown(sh){
	if(sh.value=='service'){
		$('#search_txt').hide();
		$('#vendor_select').hide();
		$('#service_select').show();
	}else if(sh.value=='vendor'){
		$('#search_txt').hide();
		$('#vendor_select').show();
		$('#service_select').hide();
	}else{
		$('#search_txt').show();
		$('#service_select').hide();
		$('#vendor_select').hide();
	}
}


function validateDecimal(value){
        var RE = /^\d*\.?\d*$/;
        if(RE.test(value)){
           return true;
        }else{
           return false;
        }
    }
    
    function addVendorContact(){
		vendor_name = $("#vendor_name").val();
		contact = $("#contact").val();	
		phone_number = $("#phone_number").val();
		email = $("#email").val();
		var isError =false;
		if(vendor_name==''){
			$("#vendor_name-error").html("Please enter Vendor Name");
				isError = true;
		}else{
			$("#vendor_name-error").html('');
		}
		
		if(contact==''){
			$("#contact-error").html("Please enter Contact");
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
		if(!isError){
			if(email==''){
				//document.addNewVContact.submit();
				submitAddNewVContactFrom();
			}else{
				checkVendorContactEmail(email);
			}
			
		}
	}
function submitAddNewVContactFrom(){
	var fdata	=	$("form#addNewVContact").serialize();
	var action		=	$("form#addNewVContact").attr('action');
	  $.post( action, fdata, function( data ) {
		data=$.parseJSON(data);
		$('div.atd_success_message').html(data.msg);							
		var mid = $("#mid").val();
		var bid = $("#building").val();
		var main_url = baseUrl+"material/index/bid/"+bid+"/mid/"+mid;

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
					  submitAddNewVContactFrom();
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
function cancelVendorContact(){
	parent.jQuery.fancybox.close();
	//var mid = $("#mid").val();
	//var bid = $("#building").val();
	//window.location.href = baseUrl+"material/index/bid/"+bid+"/mid/"+mid;
}

function deleteVendorContact(mvId,mid){
	var check_delete='YES';
	var bid = $('#building_id').val();

	jPrompt('For Deleting Alternate Vendor Contact, Enter Yes in Capital letters.', '', 'Vision Work Orders', function(return_value) {
	if(return_value!=null){
		if(check_delete === return_value){
			$('.loader').show();
			$.ajax({
					url         : baseUrl+"material/removevcontact",
					type        : "post",
					datatype    : 'json',                
					data        : {
								   mvId:mvId
								   },
					success : function( result ) {
						$('.loader').hide();						
						if(result=='true'){
							$('.success_message').html('Alternate Vendor Contact deleted successfully.');							
							window.location.href=baseUrl+"material/index/bid/"+bid+"/mid/"+mid;
						}else{
							$('.error-txt').html('Some error occurred.');
						}					
					}
				});	
		}else{
			jAlert('You have entered wrong word.', 'Vision Work Orders');
		}
	}
	});	
	
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
			//document.addNewVContact.submit();
			submitAddNewVContactFrom();
		}else{
			checkVendorContactEmail(email);
		}
		
	}
}
$(function() {
	$(".modalbox").fancybox({'openEffect': 'none',fitToView: true});
    $.fancybox.hideLoading();
});
function addMaterial(url){
   CheckForSessionpop(baseUrl);
	$.fancybox.hideLoading();
	$('a[href="#addnewmaterial"]').fancybox({
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

function addAltVendor(url){
	$('a[href="#addaltvendor"]').fancybox({
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

function editAltVendor(url){
	$('a[href="#editaltvendor"]').fancybox({
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

function showAltVendorList(url){
  $('.loader').show();
	$.ajax({
                url         : url,
                type        : "get",
                datatype    : 'json',
                success     : function( data ) {
				    $('#show_alt_vendor_list_div').html( data );
					$('#show_alt_vendor_list_div_href').trigger('click');
					var altenatevendorselected= $('#selectedVendor').val();
					$('.altenatevendorselected').val(altenatevendorselected);
					$('.loader').hide();
                },
                error       : function(){
					$('.loader').hide();
 					jAlert('There was an error', 'Vision Work Orders');
                }  
             });
}




function cancelAltVendorList(){
	$("#fancybox-overlay").remove();
	$("div.fancybox-close").trigger("click");
	$("#services option:first-child").attr('selected',true);
	$("#services").prop("selectedIndex", 0);
	$('#show_alt_vendor_list_div').hide();
}



function addNewVendor(url){
	parent.CheckForSessionpop(baseUrl);
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
 
function loadVendor(vid, mid){
	hideAllVendor();
	$('#mat_open_div_'+vid).hide();
	$('#mat_close_div_'+vid).show();	 
	if(vid!=''){
		$('.loader').show();
		$.ajax({
                url         : baseUrl+"vendor/loadvendormaterial",
                type        : "post",
                datatype    : 'json',
                data        : {vid:vid, mid:mid},
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

function hideAllVendor(){

	$('.mat_open_plus').show();
	$('.mat_open_close').hide();
	$('.trvendor-class').hide();
	$('.tdvendor-class').html('');
}

 function hideVendor(vid){
	$('#mat_open_div_'+vid).show();
	$('#mat_close_div_'+vid).hide();
	$(".message").html(''); 
	$(".error-msg").html('');
	$('#vendor_data_'+vid).html('');
	$('#vendor_tr_'+vid).hide();
	
}

 function changeSelectedValue(selectedVendor) {
$('#selectedVendor').val(selectedVendor);
}

 function addVendorAltContact(mid) {

    var altenatevendorselected = $("#altenatevendorselected_"+mid).val();
	var vendor_mid = $("#vendor_mid_"+mid).val();
	var isError = false;
	if(vendor_mid!='') {
	   if(altenatevendorselected==''){
		   $("#altenatevendorselected-error_"+mid).html("Please Select a Vendor");
			   isError = true;
	   } else {
		    $("#altenatevendorselected-error_"+mid).html('');
	   }
	   if(!isError){
	      validateVendorAltContact(altenatevendorselected, vendor_mid);
	   }
	}
	
 }
 function validateVendorAltContact(altenatevendorselected, vendor_mid ) {
  $('.loaderpop').show();
    $.ajax({
                url         : baseUrl+"material/checkvendor",
                type        : "post",
                datatype    : 'json',
                data        : {vendor_mid:vendor_mid,altenatevendorselected:altenatevendorselected},
                success     : function( data ) {
					$('.loader').hide();					 
                   if(data=='true'){	
					  $('.loader').hide();
                      $("#altenatevendorselected-error_"+vendor_mid).html("Vendor is already added.");
                   }else{                  
                         //document.addNewVendor.submit();
						 insertVendorAltContact(vendor_mid);
				       }
                },
                error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');
                }  
                
             });
 }
 
 
 function insertVendorAltContact(vendor_mid) {
    var fdata	=	$("form#altvendorform_"+vendor_mid).serialize();				
	var action		=	$("form#altvendorform_"+vendor_mid).attr('action');				  
	$.post( action, fdata, function( data ) {
	    $('.loader').hide();
		data=$.parseJSON(data);
		$('div.success_message_vendor').html(data.msg);
		var main_url = baseUrl+data.url;
		setInterval(function(){ window.parent.location.href = main_url; }, 1000);
		
	});
 }
 
 
 function deleteVendor(vid, mid){
	var check_delete='YES';
	var bid = $('#building_id').val();
	jPrompt('For Deleting Vendor, Enter Yes in Capital letters', '', 'Vision Work Orders', function(return_value) {
	if(return_value!=null){	
		if(check_delete === return_value){
			$('.loader').show();						 			
			$.ajax({
					url         : baseUrl+"material/removevendor",
					type        : "post",
					datatype    : 'json',                
					data        : {
								   vid:vid, 
								   mid:mid
								   },
					success : function( result ) {
						$('.loader').hide();						 						
						if(result=='true'){
							$('.success_message').html('Vendor deleted successfully.');
							window.location.href=baseUrl+"material/index/bid/"+bid+"/mid/"+mid;
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

function addMaterialTemplate(url){
   CheckForSessionpop(baseUrl);
	$.fancybox.hideLoading();
	$('a[href="#addMaterialTemplate"]').fancybox({
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

function getMaterial() {
var mid= $('#description').find('option:selected'); 
mid=mid.attr("data-rel");


if(mid!=''){		
		$('.loader').show();
		$.ajax({
                url         : baseUrl+"material/getmaterial",
                type        : "post",
                datatype    : 'json',
                data        : {mid:mid},
                success     : function( data ) { data=$.parseJSON(data);  
				
				
					$('.loader').hide();					 
                   if(data.msg=='success'){	
						$('#cost').val(data.cost);
						$('#markup').val(data.markup);
						$('#vendor_part').val(data.vendor_part);
						$('#manufacturer').val(data.manufacturer);
						$('#mfg').val(data.mfg);
						$('#vendor').val(data.vendor);
						var servicesexist= $("#services option:contains("+data.servicename+")").length;
						if(servicesexist <= 0) {
						if(data.servicename !=null) {
							$("#services").append('<option value="0" selected>'+data.servicename+'</option>');
							$('#service_type_text').val(data.servicename);
						}
						} else {
							$('#service_type_text').val('');
							$('#services option').filter(function() { 
								return ($(this).text() == data.servicename); 
								}).prop('selected', true); 
						}
						
						var vendorexist= $("#vendor option:contains("+data.companyName+")").length; 
						if(vendorexist <= 0 ) { 
						if(data.companyName !=null) {
							$("#vendor").append('<option value="'+data.vendor+'" selected>'+data.companyName+'</option>');
							$('#vendor_type_text').val(data.companyName);
							} else { 
								 $("#vendor").prop('selectedIndex', 0);
							}
						} else {  
							$('#vendor_type_text').val('');
							$('#vendor option').filter(function() { 
								return ($(this).text() == data.companyName); 
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

