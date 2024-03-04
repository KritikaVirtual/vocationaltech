$( document ).ready(function() {

    var tenantData = new Object;
    var baseurl;

    baseurl = $('#baseurl').val();

    $('.module').on('click',function(){
        var checkedIds = $(".module:checkbox:checked").map(function() {
            return this.id;
        }).get();
        if(checkedIds.length==0){
            $('.buildings').attr('checked',false);
            $('.nextLast').attr('disabled',true);
        }
        else{
            $('.nextLast').attr('disabled',false);
        }
    });

    $('.buildings').on('click',function(){
            var checkedIds = $(".buildings:checkbox:checked").map(function() {
                return this.id;
            }).get();
            if(checkedIds.length==0){
                $('.module').attr('checked',false);
                $('#nextSecond').attr('disabled',true);
            }
            else{
                $('#nextSecond').attr('disabled',false);
            }
        });

    $('.nextFirst').on('click', function(){
            $('#nextFirst').attr('disabled',true);
            tenantData.email = $("#email").val();
            tenantData.firstName = $("#firstname").val();
            tenantData.lastName = $("#lastname").val();
            tenantData.phoneNumber = $('#office-phone').val();
            tenantData.access = $('#accessRole').val();
            tenantData.building = $('#building').val();
            var modules = '';
			$("input:checkbox[name='modules[]']").each(function(){
				if($(this).prop('checked')){
					modules = modules + ((modules!='')?',':'')+ $(this).val();
				}
			});
			tenantData.modules = modules;
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
            
            if(tenantData.phoneNumber.length == 0) {
                $("#office-phone-error").html("Please enter phone number");
                isError = true;
            } else {
                $("#office-phone-error").html("");
            }
            if(modules==''){
				$('.moduleErr').html("Select Modules");
				isError = true;
			}else{
				$('.moduleErr').html("");
			}
            
            if(!isError) {				
              $.ajax({
                url         : baseUrl+"tenant/checktenant",
                type        : "post",
                datatype    : 'json',
                data        : {email:tenantData.email},
                success     : function( data ) {
					 
                   if(data){
					   $('#nextFirst').attr('disabled',false);                       
                       $("#email-error").html("This email-id is already existed.");
                       return false;
                   }else{                     
                      //$("#first_form").css({"display" : "none"});
                      //$("#third_form").fadeIn(500);
                      submitUser(tenantData);                      
                   }
                },
                error       : function(){
                    alert('There was an error');
                    $('#nextFirst').attr('disabled',false);
                }  
                
             });
                
            }else{
				$('#nextFirst').attr('disabled',false);
			} 

    });

});

function submitUser(tData){
	$('.loader').show();
	$.ajax({
			url         : baseUrl+"tenant/createtenantsusers",
			type        : "post",
			datatype    : 'json',
			data        : tData,
			success     : function( data ) {
			$('.loader').hide();	  
			   if(data){                       
				   var detail = jQuery.parseJSON(data);
					console.log(detail)
					createUserComplete(detail);
			   }else{                     
				  alert('Unable to register the user. Please try again.');
			   }
			},
			error       : function(){
				$('.loader').hide();
				alert('There was an error');
			}  
			
		 });
}


function createUserComplete(data){
    var baseurl;

    baseurl = $('#baseurl').val();
    var bid = $('#select_building').val();
        var str = '<section style="z-index:9999" class="w-48 fr ch-home-form" id="first"><section class="ch-form-header "><h3>New User Setup Complete</h3></section><table class="newuser_setup"><tr><td colspan="2" class="grayhead">Setup Complete!</td></tr><tr style="display:none;"><td colspan="2"><br></td></tr><tr><th align="right">Name : </th><td align="left">'+data.uname+'</td></tr><tr><th align="right">Office phone : </th><td align="left">'+data.office_phone+'</td></tr><tr><th align="right">E-mail : </th><td align="left">'+data.email+'</td></tr><tr><th align="right">Username : </th><td align="left">'+data.username+'</td></tr><tr><th align="right">User password : </th><td align="left">'+data.userPassowd+'</td></tr><tr><th align="right">User access : </th><td align="left">'+data.access+'</td></tr><tr><td class="grayhead" colspan="2">Access of the following building(s) has been granted</td></tr><tr><th>Building Name(s)</th><th>Module(s)</th></tr>';

                        $.each(data.building, function(key, item){
                            str += '<tr><td>'+item.building+'</td><td>'+item.module+'</td></tr>';
                        });

            str += '</table></section><div class="finish_btn"><div><a href="'+baseUrl+'tenant/userinfo/bid/'+bid+'">Finish</a></div></div>';
            $("#user-Complete").html(str);
    }

function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31
    && (charCode < 48 || charCode > 57))
    return false;
     
    return true;
}


function cancelUser(){
    var baseurl;
    baseurl = $('#baseurl').val();
    var bid = $('#select_building').val();
    window.location.href = baseUrl+'tenant/userinfo/bid/'+bid;
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
}
    
$(function() {
            $("#postalcode").mask("99999");
            $("#office-phone").mask("999.999.9999");
            $("#faxnumber").mask("999.999.9999");
           
            });
