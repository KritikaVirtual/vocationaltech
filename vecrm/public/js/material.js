function validateMaterial(){
	date_created = $("#date_created").val();
	description = $("#description").val();
	service = $("#services").val();
	cost = $("#cost").val();
	vendor = $("#vendor").val();	
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
	
	if(vendor==''){
		$("#vendor-error").html("Please select Vendor");
			isError = true;
	}else{
		$("#vendor-error").html('');
	}	
	
	
	if(!isError){
		document.addNewMaterial.submit();
		//validateVendorName(company_name,bid);
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
	 window.location.href = baseUrl+"material/index/bid/"+bId;
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
	var service = $('#service_opt').val();
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




function deleteMaterial(mid){
	var check_delete='YES';
	var bid = $('#building_id').val();
	var return_value = prompt("For Deleting Material, Enter Yes in Capital letter.");
	if(return_value!=null){	
		if(check_delete === return_value){
			$.ajax({
					url         : baseUrl+"material/removematerial",
					type        : "post",
					datatype    : 'json',                
					data        : {
								   mid:mid
								   },
					success : function( result ) {
						
						if(result=='true'){
							$('.success_message').html('Material deleted successfully.');
							window.location.href=baseUrl+"material/index/bid/"+bid;
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
	var mid = $("#mid").val();
	var bid = $("#building").val();
	window.location.href = baseUrl+"material/index/bid/"+bid+"/mid/"+mid;
}

function deleteVendorContact(mvId,mid){
	var check_delete='YES';
	var bid = $('#building_id').val();
	var return_value = prompt("For Deleting Alternate Vendor Contact, Enter Yes in Capital letter.");
	if(return_value!=null){	
		if(check_delete === return_value){
			$.ajax({
					url         : baseUrl+"material/removevcontact",
					type        : "post",
					datatype    : 'json',                
					data        : {
								   mvId:mvId
								   },
					success : function( result ) {						
						if(result=='true'){
							$('.success_message').html('Alternate Vendor Contact deleted successfully.');
							window.location.href=baseUrl+"material/index/bid/"+bid+"/mid/"+mid;
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
