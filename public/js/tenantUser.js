var tenantData = new Object;
function checkTenant(){ 
	parent.CheckForSessionpop(baseUrl);
	var tenantName = $('#tenantName').val();
	var building = $('#building').val();
	if(tenantName!=''){
		 $.ajax({
                url         : baseUrl+"tenant/checktenantname",
                type        : "post",
                datatype    : 'json',
                data        : {tenantName:tenantName,building:building},
                success     : function( data ) {
					console.log("start123");
					$('.loader').hide();  
                   if(data!=true){ 
					  $("#tName_error").html("");
					  $("#tenantName").removeClass("inputErr");                      
                       movetenantUser();
                       
                   }else{                     
                      $("#tName_error").html("Tenant name already in use.");
					 // $("#tenantName").addClass("inputErr");
					  return false;
                   }
                },
                error       : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });
	}else{
		$("#tName_error").html("Please enter tenant name");
		//$("#tenantName").addClass("inputErr");
		return false;
	}
}
function movetenantUser(){
		tenantData.tenantName = $("#tenantName").val();
		tenantData.tenantContact = $("#tenantContact").val();
		tenantData.address1 = $("#address1").val();
		tenantData.address2 = $("#address2").val();
		tenantData.suite = $('#suite').val();
		tenantData.city = $('#city').val();
		tenantData.state = $('#state').val();
		tenantData.postalCode = $('#postalcode').val();
		tenantData.phoneNumber = $('#phoneNumber').val();
		tenantData.phoneExt = $('#phoneExt').val();
		tenantData.billtoAddress = $('#billtoAddress').val();
		tenantData.status = $('#status').val();
		
		
                
                //console.log()
		var isError = false;
		if(tenantData.tenantContact.length == 0) {
			$("#tContact_error").html("Please enter tenant contact");
			isError = true;
		} else {
			$("#tContact_error").html("");
		}
		
		if(tenantData.address1.length == 0) {
			$("#address1-error").html("Please enter first address");
			isError = true;
		} else {
			$("#address1-error").html("");
		}	
		
		
		if(tenantData.suite.length == 0) {
			$("#suite-error").html("Please enter suite");
			isError = true;
		} else {
			$("#suite-error").html("");
		}

		if(tenantData.city.length == 0) {
			$("#city-error").html("Please enter city");
			isError = true;
		} else {
			$("#city-error").html("");
		}

		if(tenantData.state.length == 0) {
			$("#state-error").html("Please enter state");
			isError = true;
		} else {
			$("#state-error").html("");
		}

		if(tenantData.postalCode.length == 0) {
			$("#postal-error").html("Please enter postal code");
			isError = true;
		} else if(tenantData.postalCode.length < 5) {
			$("#postal-error").html("Please enter 5 digits postal code");
			isError = true;
		} else {
			$("#postal-error").html("");
		}
		
		if(tenantData.phoneNumber.length == 0) {
			$("#phone-error").html("Please enter phone number");
			isError = true;
		} else if(tenantData.phoneNumber.length < 12) {
			$("#phone-error").html("Please enter 10 digits phone number");
			isError = true;
		} else {
			$("#phone-error").html("");
		}
		
		
		if(isError) {
	 
		  //$(".nextLast").attr('disabled',false);
		  return false;		 
		}else{
			$("#first_step").css({"display" : "none"});
                        $("#second_step").fadeIn(500);
			$("#email").focus();
                        
                        // Auto feel value end second steps
                        var names = tenantData.tenantContact.split(' ');
                        $("#firstname").val(names[0]);
                        if(names.length > 1)
                        $("#lastname").val(names[1]);
                        $("#suite_location").val(tenantData.suite);
                        $("#office-phone").val(tenantData.phoneNumber);
                        //document.getElementById("firstname").value = "sanjay";
                        //console.log("sasasd");
			parent.$('.fancybox-inner').height($('#wrap').height()+425);
		}
		
}
/*
 * Check tenant Admin
 */ 
function checkTenantAdmin(){
	parent.CheckForSessionpop(baseUrl);
	$('#save').attr('disabled',true);
	var email = $('#email').val();
	if(email.length == 0) {
                $("#email-error").html("Please enter E-mail address");
                $('#save').attr('disabled',false);
                return false;
     } else if( !isValidEmailAddress(email ) ) {
                $("#email-error").html("Please enter valid E-mail address");
                $('#save').attr('disabled',false);
				parent.$('.fancybox-inner').height($('#wrap').height()+425);
                return false;
      } else {
                $.ajax({
                url         : baseUrl+"tenant/checktenant",
                type        : "post",
                datatype    : 'json',
                data        : {email:email},
                success     : function( data ) {
					$('.loader').hide();  
                   if(data!=true){ 
					  $("#email-error").html("");				                        
                       createTenant();
                       
                   }else{                     
                      $("#email-error").html("This email-id is already existed.");
                      $('#save').attr('disabled',false);					  
					  return false;
                   }
                },
                error  : function(){
					$('.loader').hide();
                    alert('There was an error');
                }  
                
             });
       }	
	
}

/*
 * create Tenant
 */
 
function createTenant(){	        
		tenantData.email = $("#email").val();
		tenantData.firstName = $("#firstname").val();
		tenantData.lastName = $("#lastname").val();
		tenantData.title = $("#title").val();
		tenantData.suite_location = $("#suite_location").val();
		tenantData.officeNumber = $('#office-phone').val();
		tenantData.building = $('#building').val();
		tenantData.access = $('#accessRole').val();
		/*var modules = '';
		$("input:checkbox[name='modules[]']").each(function(){
			if($(this).prop('checked')){
				modules = modules + ((modules!='')?',':'')+ $(this).val();
			}
		});
		tenantData.modules = modules;*/
		var isError = false;
		if(tenantData.email.length == 0) {
			$("#email-error").html("Please enter E-mail address");
			isError = true;
		} else if( !isValidEmailAddress( tenantData.email ) ) {
			$("#email-error").html("Please enter valid E-mail address");
			isError = true;
		} else {
			 $("#email-error").html("");
		}
		
		
		if(tenantData.firstName.length == 0) {
			$("#firstname-error").html("Please enter first name");
			isError = true;
		} else {
			$("#firstname-error").html("");
		}
		
		if(tenantData.lastName.length == 0) {
			$("#lastname-error").html("Please enter last name");
			isError = true;
		} else {
			$("#lastname-error").html("");
		}
		
		if(tenantData.suite_location.length == 0) {
			$("#suite_location-error").html("Please enter suite/location");
			isError = true;
		} else {
			$("#suite_location-error").html("");
		}
		
		if(tenantData.officeNumber.length == 0) {
			$("#office-phone-error").html("Please enter Office Phone number");
			isError = true;
		} else if(tenantData.officeNumber.length < 12) {
			$("#office-phone-error").html("Please enter 10 digits Office Phone number");
			isError = true;
		} else {
			$("#office-phone-error").html("");
		}
		
		/*if(modules==''){
			$('.moduleErr').html("Select Modules");
			isError = true;
		}else{
			$('.moduleErr').html("");
		}*/
		if(!isError){
			$('#save').attr('disabled',false)
		}
		
		if(!isError){
			$('#save').attr('disabled',true);
			$('.loader').show();
			 $.ajax({
                url         : baseUrl+"tenant/createtenant",
                type        : "post",
                datatype    : 'json',
                data        : tenantData,
                success     : function( data ) { 
					 $('.loader').hide(); 
					 //alert(data);
                   if(data){					                        
                       showWelcomeLetter(data);
                       
                   }
                },
                error       : function(){
					$('.loader').hide(); 
					$('#save').attr('disabled',false); 
                    alert('There was an error');
                }  
                
             });
		}else{
			$('.loader').hide(); 
			$('#save').attr('disabled',false); 
		}
} 

function showWelcomeLetter(data){
	$("#first_step").css({"display" : "none"});
    $("#second_step").css({"display" : "none"});
    $("#third_step").fadeIn(500); 
	parent.$('.fancybox-inner').height($('#wrap').height()+600);
    if(data){
		$("#show_welcome_text").html(data);
		$("#user_Complete").fadeIn(500);
	}else{
		$("#user_error").fadeIn(500);
	}
   
	
}
function createUserComplete(data){
    var baseurl;

    baseurl = $('#baseurl').val();
    var bid = $('#select_building').val();
        var str = '<section style="z-index:9999" class="w-48 fr ch-home-form" id="first"><section class="ch-form-header "><h3>New Tenant User Setup Complete</h3></section><table class="newuser_setup"><tr><td colspan="2" class="grayhead">Setup Complete!</td></tr><tr style="display:none;"><td colspan="2"><br></td></tr><tr><th align="right">Name : </th><td align="left">'+data.uname+'</td></tr><tr><th align="right">Office phone : </th><td align="left">'+data.office_phone+'</td></tr><tr><th align="right">E-mail : </th><td align="left">'+data.email+'</td></tr><tr><th align="right">Username : </th><td align="left">'+data.username+'</td></tr><tr><th align="right">User password : </th><td align="left">'+data.userPassowd+'</td></tr><tr><th align="right">User access : </th><td align="left">'+data.access+'</td></tr><tr><th align="right">Company Name : </th><td align="left">'+data.companyName+'</td></tr><tr><th align="right">Address1 : </th><td align="left">'+data.address1+'</td></tr><tr><th align="right">Address2 : </th><td align="left">'+data.address2+'</td></tr><tr><th align="right">Suite : </th><td align="left">'+data.suite+'</td></tr><tr><th align="right">State : </th><td align="left">'+data.state+'</td></tr><tr><th align="right">Postal Code : </th><td align="left">'+data.postalCode+'</td></tr><tr><th align="right">Fax Number : </th><td align="left">'+data.faxNumber+'</td></tr><tr><th align="right">Attention : </th><td align="left">'+data.attention+'</td></tr><tr><td class="grayhead" colspan="2">Access of the following building(s) has been granted</td></tr><tr><th>Building Name(s)</th><th>Module(s)</th></tr>';

                        $.each(data.building, function(key, item){
                            str += '<tr><td>'+item.building+'</td><td>'+item.module+'</td></tr>';
                        });

            str += '</table></section><div class="finish_btn"><div><a href="'+baseurl+'tenant/users/bid/'+bid+'">Finish</a></div></div>';
            $("#user-Complete").html(str);
    }

function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31
    && (charCode < 48 || charCode > 57))
    return false;
     
    return true;
}


function cancelUser(){
    var baseurl;
    baseurl = $('#baseurl').val();
    var bid = $('#select_building').val();
    //window.location.href = baseurl+'tenant/users/bid/'+bid;
	parent.jQuery.fancybox.close();
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
}
    
$(function() {
            $("#postalcode").mask("?99999");
            $("#office-phone").mask("?999.999.9999");
            $("#faxnumber").mask("?999.999.9999");
            $("#phoneNumber").mask("?999.999.9999");
            $("#ext").mask("?99999");
           
            });
$(function() {
	$(".modalbox").fancybox({'openEffect': 'none',fitToView: true});
});

function sendwelcomeForm(){
	var fdata	=	$("form#sendwelcome").serialize();				
	var action		=	$("form#sendwelcome").attr('action');				  
	$.post( action, fdata, function( data ) {
		data=$.parseJSON(data);
		$('div.success_message').html(data.msg);
		var main_url = baseUrl+data.url;
                console.log("redirecturl"+main_url);
		setInterval(function(){ 
                    window.parent.location.href = main_url; 
                    }, 1000);
	});	
}

