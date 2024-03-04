function cancelForm(){	
	window.location.href = baseUrl+'building';
}

function nextBuildingBilling()
{
var cname = $('#cname').val();
//var comExist = checkCompany(cname);
//alert('hello check company'+comExist);
var buildingName = $('#buildingName').val();
var address = $('#address').val();
var address2 = $('#address2').val();
var city = $('#city').val();
var state = $('#state').val();
var postalCode = $('#postalCode').val();
var phoneNumber = $('#phoneNumber').val();
var faxNumber = $('#faxNumber').val();

if($.trim(cname) == ''){$('.cname-err').html("Company Name Required"); $('#cname').addClass('inputErr');$('#cname').focus();return false;}
else{$('.cname-err').html(''); $('#cname').removeClass('inputErr');}
if($.trim(buildingName) == ''){$('.building-err').html("Building Name Required"); $('#buildingName').addClass('inputErr');$('#buildingName').focus();return false;}
else{$('.building-err').html(''); $('#buildingName').removeClass('inputErr');}
if($.trim(address) == ''){$('.address-err').html("Address Required"); $('#address').addClass('inputErr');$('#address').focus();return false;}
else{$('.address-err').html(''); $('#address').removeClass('inputErr');}
//if($.trim(address2) == ''){$('.address2-err').html("Address2 Required"); $('#address2').addClass('inputErr');return false;}
if($.trim(city) == ''){$('.city-err').html("City Required"); $('#city').addClass('inputErr'); $('#city').focus();return false;}
else{$('.city-err').html(''); $('#city').removeClass('inputErr');}
if($.trim(state) == ''){$('.state-err').html("State Required"); $('#state').addClass('inputErr');$('#state').focus();return false;}
else{$('.state-err').html(''); $('#state').removeClass('inputErr');}
if($.trim(postalCode) == ''){$('.postal-err').html("Postal Code Required"); $('#postalCode').addClass('inputErr');$('#postalCode').focus();return false;}
else{$('.postal-err').html(''); $('#postalCode').removeClass('inputErr');}
if($.trim(phoneNumber) == ''){$('.phone-err').html("Phone Number Required"); $('#phoneNumber').addClass('inputErr'); $('#phoneNumber').focus();return false;}
else{$('.phone-err').html(''); $('#phoneNumber').removeClass('inputErr');}
/*if($.trim(faxNumber) == ''){$('.fax-err').html("Fax Number Required"); $('#faxNumber').addClass('inputErr');$('#faxNumber').focus();return false;}
else{$('.fax-err').html(''); $('#faxNumber').removeClass('inputErr');}*/

$('#billCName').val($('#cname').val());
$('#billaddress').val($('#address').val());
$('#billaddress2').val($('#address2').val());
$('#billcity').val($('#city').val());
$('#billstate').val($('#state').val());
$('#billpostalCode').val($('#postalCode').val());
$('#billphoneNumber').val($('#phoneNumber').val());
$('#billPhoneExt').val($('#ext').val());
$('#billfaxNumber').val($('#faxNumber').val());

$('#step1').hide();
$('#step2').show();
return false;
}

/************validate building **********/
function validateBuilding(){
	if(checkCompany()){
		if(checkBuilding())
		{
			nextBuildingBilling();
		}else
		return false;
	}else
	return false;
}

/*********check Cost Center exists or not **********/

function checkCostCenter(){
	var checkCost = false;
	var cost = $('#uniqueCostCenter').val();
	//alert('cname'+cname);
	$('.loader').show();
	if(cost!=''){
		$.ajax({
				type: "POST",
				url: baseUrl+'building/checkcostcenter',
				data: {cost: cost },				
				success: function (msg) {
					  $('.loader').hide();
						if(msg == 'true'){
							$('.ccenterErr').html("Cost Center already in use."); 
							$('#uniqueCostCenter').addClass('inputErr');
							$('#uniqueCostCenter').focus();							
							return false;					
						}else{							
							$('.ccenterErr').html(""); 
							$('#uniqueCostCenter').removeClass('inputErr');
							checkCompany();
						} 			
				}
			  });
          
    }else{
		$('.loader').hide();
		$('.ccenterErr').html("Cost Center Required");
		$('#uniqueCostCenter').addClass('inputErr');
		$('#uniqueCostCenter').focus();
		return false;
	}
         
}

/*********check company exists or not **********/

function checkCompany(){
	$('.loader').show();
	var checkComp = false;
	var cname = $('#cname').val();
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
						if(msg == 'true'){
							$('.cname-err').html(""); 
							$('#cname').removeClass('inputErr');
							checkBuilding();						
						}else{
							//alert('Company is not existed');
							$('.cname-err').html("Company name does not exist."); 
							$('#cname').addClass('inputErr');
							$('#cname').focus();
							$('#cname').val('');
							return false;
						} 			
				}
			  });
          
    }else{
		$('.loader').hide();
		$('.cname-err').html("Company Name Required"); 
		$('#cname').addClass('inputErr');
		$('#cname').focus();
		return false;
	}
         
}

/*********check building exists or not **********/

function checkBuilding(){
	$('.loader').show();
	var checkComp = false;
	var buildingName = $('#buildingName').val();
	var cust_id = $('#cust_id').val();
	//alert('cname'+cname);
	if(buildingName!=''){
		$.ajax({
				type: "POST",
				url: baseUrl+'building/checkbuilding',
				data: {buildingName: buildingName,cust_id:cust_id },				
				success: function (msg) {					
					$('.loader').hide();
						if(msg == 'true'){
							$('.building-err').html("Building Name already in use."); 
							$('#buildingName').addClass('inputErr');
							$('#buildingName').focus();							
							return false;
												
						}else{
							$('.building-err').html(""); 
							$('#buildingName').removeClass('inputErr');							
							nextBuildingBilling();	
						} 			
				}
			  });
          
    }else{
		$('.loader').hide();
		$('.building-err').html("Building Name Required."); 
		$('#buildingName').addClass('inputErr');
		$('#buildingName').focus();
		return false;
	}
         
}


function nextAdminSetp()
{
//validation code 
var billCName = $('#billCName').val();
var billaddress = $('#billaddress').val();
var billaddress2 = $('#billaddress2').val();
var billsuite = $('#billsuite').val();
var billcity = $('#billcity').val();
var billstate = $('#billstate').val();
var billpostalCode = $('#billpostalCode').val();
var billphoneNumber = $('#billphoneNumber').val();
var billfaxNumber = $('#billfaxNumber').val();
var attention = $('#attention').val();

if($.trim(billCName) == ''){$('.billCName-Err').html("Billing Company Name Required"); $('#billCName').addClass('inputErr');$('#billCName').focus();return false;}
else{$('.billCName-Err').html(''); $('#billCName').removeClass('inputErr');}

if($.trim(billaddress) == ''){$('.billaddress-Err').html("Billing Address Required"); $('#billaddress').addClass('inputErr');$('#billaddress').focus();return false;}
else{$('.billaddress-Err').html(''); $('#billaddress').removeClass('inputErr');}

//if($.trim(billaddress2) == ''){$('.billaddress2-Err').html("Billing Address2 Required"); $('#billaddress2').addClass('inputErr');return false;}
if($.trim(billsuite) == ''){$('.billsuite-Err').html("Billing Suite Required"); $('#billsuite').addClass('inputErr');$('#billsuite').focus();return false;}
else{$('.billsuite-Err').html(''); $('#billsuite').removeClass('inputErr');}

if($.trim(billcity) == ''){$('.billcity-Err').html("Billing City Required"); $('#billcity').addClass('inputErr');$('#billcity').focus();return false;}
else{$('.billcity-Err').html(''); $('#billcity').removeClass('inputErr');}

if($.trim(billstate) == ''){$('.billstate-Err').html("Billing State Required"); $('#billstate').addClass('inputErr');$('#billstate').focus();return false;}
else{$('.billstate-Err').html(''); $('#billstate').removeClass('inputErr');}

if($.trim(billpostalCode) == ''){$('.billpostalCode-Err').html("Billing Postal Code Required"); $('#billpostalCode').addClass('inputErr');$('#billpostalCode').focus();return false;}
else{$('.billpostalCode-Err').html(''); $('#billpostalCode').removeClass('inputErr');}

if($.trim(billphoneNumber) == ''){$('.billphoneNumber-Err').html("Billing Phone Number Required"); $('#billphoneNumber').addClass('inputErr');$('#billphoneNumber').focus();return false;}
else{$('.billphoneNumber-Err').html(''); $('#billphoneNumber').removeClass('inputErr');}

/*if($.trim(billfaxNumber) == ''){$('.billfaxNumber-Err').html("Billing Fax Number Required"); $('#billfaxNumber').addClass('inputErr');$('#billfaxNumber').focus();return false;}
else{$('.billfaxNumber-Err').html(''); $('#billfaxNumber').removeClass('inputErr');}*/

if($.trim(attention) == ''){$('.attention-Err').html("Billing Attention Required"); $('#attention').addClass('inputErr');$('#attention').focus();return false;}
else{$('.attention-Err').html(''); $('#attention').removeClass('inputErr');}

$('#step2').hide();
$('#step3').show();
}
function backAdminSetp()
{
$('#step2').hide();
$('#step1').show();
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
function finalSubmit()
{
	$('.loader').show();
	$('#finalsubmit').attr('disabled',true);
	var submit_flag = 0;
	var adminEmailAddress = $('#adminEmailAddress').val();
	var fname = $('#fname').val();
	var lname = $('#lname').val();
	var username = $('#username').val();
	var uPass = $('#uPass').val();
	var uConfPass = $('#uConfPass').val();
	var phone = $('#phone').val();
	//var modulee = $('#modulee').val();
	var check_auto = $('#auto_generate').prop("checked");
	var modules = '';
		$("input:checkbox[name='modules[]']").each(function(){
			if($(this).prop('checked')){
				modules = modules + ((modules!='')?',':'')+ $(this).val();
			}
		});	
	if($.trim(adminEmailAddress) == ''){
		$('.adminEmailAddressErr').html("E-Mail Address Required"); 
		$('#adminEmailAddress').addClass('inputErr'); 
		$('#adminEmailAddress').focus(); 
		submit_flag = 1;
	}
	else{
		$('.adminEmailAddressErr').html(''); 
		$('#adminEmailAddress').removeClass('inputErr');
		}
	
	if(adminEmailAddress!=''){ 
		if(!validateEmail(adminEmailAddress)){
			$('.adminEmailAddressErr').html("E-Mail Address Invalid"); 
			$('#adminEmailAddress').addClass('inputErr'); 
			$('#adminEmailAddress').focus();
			submit_flag = 1;
			}
		}
	else{
		$('.adminEmailAddressErr').html(''); 
		$('#adminEmailAddress').removeClass('inputErr');
		}
	
	if($.trim(fname) == ''){
		$('.fnameErr').html("First Name Required"); 
		$('#fname').addClass('inputErr');
		$('#fname').focus();
		submit_flag = 1;
		}
	else{
		$('.fnameErr').html('');
		$('#fname').removeClass('inputErr');
		}
	
	if($.trim(lname) == ''){
		$('.lnameErr').html("Last Name Required");
		$('#lname').addClass('inputErr');
		$('#lname').focus();
		submit_flag = 1;
		}
	else{
		$('.lnameErr').html(''); 
		$('#lname').removeClass('inputErr');
		}
	
	/*if($.trim(username) == ''){$('.unameErr').html("Username Required"); $('#username').addClass('inputErr');return false;}*/
	if($.trim(uPass) == '' && check_auto == false){
		$('.uPassErr').html("User Password Required"); 
		$('#uPass').addClass('inputErr'); 
		$('#uPass').focus(); 
		submit_flag = 1;
		}
	else{
		$('.uPassErr').html('');
		$('#uPass').removeClass('inputErr');
		}
	/*********check password**********/
	var uId = $("#uid").val();
	if(uId =='' && $.trim(uPass) != '' && adminEmailAddress==uPass && check_auto == false){
		$('.uPassErr').html("Password must be differnet from email id."); 
		$('#uPass').addClass('inputErr');
		submit_flag = 1;		
	}else{
		$('.uPassErr').html('');
		$('#uPass').removeClass('inputErr');
	}
	
	var decimal=  /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,15}$/;
	if(!uPass.match(decimal) && uId =='' && check_auto == false && uPass!='')   
	  {   
		 $('.uPassErr').html("Password is not in the correct format.");		
		 $('#uPass').addClass('inputErr');  
		 submit_flag = 1;			
	   }else{
		   $('.uPassErr').html('');
		   $('#uPass').removeClass('inputErr');
	   }
	   
	if($.trim(uConfPass) == '' && check_auto == false){
		$('.uConfErr').html("Password Confirm Required");
		$('#uConfPass').addClass('inputErr');
		$('#uConfPass').focus();
		submit_flag = 1;
	}
	else{
		$('.uConfErr').html('');
		$('#uConfPass').removeClass('inputErr');
		}
	
	if($.trim(uPass) != $.trim(uConfPass)){
		$('.uConfErr').html("Passwords does not match!");		
		$('#uConfPass').addClass('inputErr');
		$('#uConfPass').focus();
		submit_flag = 1;
	}
	else{
		$('.uConfErr').html('');
		$('#uConfPass').removeClass('inputErr');
		}
	
	if($.trim(phone) == ''){
		$('.phoneErr').html("Phone Number Required");
		$('#phone').addClass('inputErr');
		$('#phone').focus();
		submit_flag = 1;
		}
	else{
		$('.phoneErr').html('');
		$('#phone').removeClass('inputErr');
		}
	
		if(modules==''){
			$('#select_mod').html("Select Modules");		
			submit_flag = 1;
		}else{
			$('#select_mod').html('');
		}
    if(submit_flag == 1){
		$('.loader').hide();
		$("#finalsubmit").attr('disabled',false);
		return false;
	}else{
		$.ajax({
				type: "POST",
				url: baseUrl + "building/registernewbuilding",
				dataType:'json',
				data: {
						buildingPerinfo: $('#regBuildingPInfo').serialize(),
						billinginfo: $('#regBuildingBillInfo').serialize(),
						adminuserinfo: $('#regBuildingAdminInfo').serialize()
				},
				beforeSend: function () {
						 $('.loader').show();
				},
				success: function (msg) {
					$('.loader').hide();                	
					if(msg.status=='error')
					{
						$('#error_msg').html(msg.message);
						$("#finalsubmit").attr('disabled',false);
					}else if(msg.status=='success'){
						$('#success_msg').html(msg.message);
						 window.location.href = baseUrl+"building"
					}else if(msg.status=='email_error'){
						$("#finalsubmit").attr('disabled',false);
						$('#error_msg').html(msg.message);
					}			
				}
				

			});
	}
}
$(function(){
//Auto Complete

$( "#cname" ).autocomplete({	
	source: function( request, response ) {
		$.ajax({
			url: baseUrl+'account/getcompanynamee',
			dataType: "jsonp",
			data: {
				q: request.term
			},
			success: function( data ) {
				response( data );
			}
		});
	},
	minLength: 1,
	select: function( event, ui ) {
		if(ui.item.cust_id!=-1)	{
                $("#cust_id").val(ui.item.cust_id);
				$("#cname").val(ui.item.companyName);
				
				return false;
		}		
	},
	open: function() {
		$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
	},
	close: function() {
		$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
	}
 });
 
 $[ "ui" ][ "autocomplete" ].prototype["_renderItem"] = function( ul, item) {
return $( "<li></li>" ) 
  .data( "item.autocomplete", item )
  .append( $( "<a></a>" ).html( item.label ) )
  .appendTo( ul );
};
 //Auto complete
         $( "#adminEmailAddress" ).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: baseUrl+'user/getUserDetail',
                    dataType: "jsonp",
                    data: {
                        q: request.term,
                        cid:$("#cust_id").val()
                    },
                    success: function( data ) {
						
                        if (data== false) {
							//alert('not found');
							resetUserForm();
						}else
                        response( data );
                    }
                });
            },
            minLength: 1,
            select: function( event, ui ) {
                $("#uid").val(ui.item.uid);
                
                $("#adminEmailAddress").val(ui.item.email);
                
                $("#fname").val(ui.item.firstName);
                $("#fname").attr("readonly", "readonly");
                
                $("#lname").val(ui.item.lastName);
                $("#lname").attr("readonly", "readonly");
                
                /*$("#username").val(ui.item.userName);
                $("#username").attr("readonly", "readonly");*/

                $("#uPass").val('12234656');
                $("#uPass").attr("readonly", "readonly");
                $('#password_section').hide();
                $('#auto_generate').attr("disabled", "disabled");
	            $("#uConfPass").val('12234656');
	            $("#uConfPass").attr("readonly", "readonly");
	            
                $("#phone").val(ui.item.phoneNumber);
                $("#phone").attr("readonly", "readonly");
                $("#aphoneExt").val(ui.item.phoneExt);
                $("#aphoneExt").attr("readonly", "readonly");
                
                $("#accesslevel").val(ui.item.role_id);
                //$("#accesslevel").attr("readonly", "readonly");
                $('#accesslevel').attr("disabled", true); 
            },
            open: function() {
                $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
            },
            close: function() {
                $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
            }
         });
         
         $('#auto_generate').click(function(){
		   if($(this).prop("checked") == true){
                $("#uPass").val('');
                $("#uConfPass").val('');
                $("#uPass").attr("readonly", true);
                $("#uConfPass").attr("readonly", true);

            }

            else if($(this).prop("checked") == false){

                $("#uPass").attr("readonly", false);
                $("#uConfPass").attr("readonly", false);

            }
			 });
 
 });
 
 function resetUserForm(){
	 if($("#uid").val()!=''){
	 $("#uid").val('');
	 $("#fname").val('');
	 $("#lname").val('');
	 $("#phone").val('');
	 $("#accesslevel").val('');
	 $("#uPass").val('');
	 $("#uConfPass").val('');
	 $("#aphoneExt").val('');
	  
  }
   $('#password_section').show();
  $('#auto_generate').attr("disabled", false);
  $("#fname").attr("readonly", false);
  $("#lname").attr("readonly", false);
  $("#phone").attr("readonly", false);  
  //$("#uPass").attr("readonly", false);
  $("#aphoneExt").attr("readonly", false);
  //$("#uConfPass").attr("readonly", false);
  $('#accesslevel').attr("disabled", false);
  
	 
 }
 function isNumberKey(evt)
{
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 46 && charCode > 31
	&& (charCode < 48 || charCode > 57))
	return false;
	 
	return true;
}
 $(function() {
			$("#postalCode").mask("99999");
			$("#phoneNumber").mask("999.999.9999");
			$("#faxNumber").mask("999.999.9999");
			$("#billpostalCode").mask("99999");
			$("#billphoneNumber").mask("999.999.9999");
			$("#billfaxNumber").mask("999.999.9999");
			$("#phone").mask("999.999.9999");
			});

