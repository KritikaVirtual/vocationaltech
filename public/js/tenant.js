function hideTenantUser(tId) {
    $('#open_div_' + tId).show();
    $('#close_div_' + tId).hide();
    $(".message").html('');
    $(".error-msg").html('');
    $('#loadtenant_' + tId).html('');
    $('#trId_' + tId).hide();

}
function hideAllTenant() {
    $('.open_plus').show();
    $('.open_close').hide();
    $('.trtenant-class').hide();
    $('.tdtenant-class').html('');
}
function loadTenantUser(tId) {
    hideAllTenant();
    $('#open_div_' + tId).hide();
    $('#close_div_' + tId).show();
    $("#tenantuser_popup_" + tId).hide();
    if (tId != '') {
        $('.loader').show();
        $.ajax({
            url: baseUrl + "tenant/loadtenantuser",
            type: "post",
            datatype: 'json',
            data: {tId: tId},
            success: function (data) {
                $('.loader').hide();
                if (data) {
                    $('#loadtenant_' + tId).html(data);
                    $('#trId_' + tId).show();
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

function showAddForm(tId) {
    $(".message").html('');
    $(".error-msg").html('');
    var height = $("#tenantUserInfo_" + tId).height();
    var tot_height = parseInt(parseInt(height) + 15)
    $("#tenantuser_popup_" + tId).css('height', tot_height + 'px');
    $("#tenantuser_popup_" + tId).show();
    $('#add-newuser-td-' + tId).show();
}

function cancelUser() {
    var tenantId = $('#tenantId').val();
    var building = $('#building').val();
    var role_id = $('#role_id').val();
    if (role_id == 5) {
        //window.location.href = baseUrl+'tenant/tenantuser';
        parent.jQuery.fancybox.close();
    } else{
        //window.location.href = baseUrl+'tenant/users/bid/'+building+'/tId/'+tenantId;
        parent.jQuery.fancybox.close();
    }

}

/*********check company exists or not *********
 * 
*/

function checkTUser(tId) {
    var redirectPage = false;
    $('#addtenantuser').attr('disabled', true);
    parent.CheckForSessionpop(baseUrl);
    $('.loader').show();
    var checkComp = false;
    var email = $.trim($('#email').val());
  //  alert('cname');
    if (email != '') {
        if (!validateEmail(email)) {
            $('.uemailErr').html("E-Mail Address Invalid");
            $('#email').addClass('inputErr');
            $('#email').focus();
            $('#addtenantuser').attr('disabled', false);
        } else {
            // check user exist in tenat option
            var allowedMulitiUser = false;

            $.ajax({
                type: "POST",
                url: baseUrl + 'tenant/checkmultiusers',
                data: {
                    email: $("#email").val(),
                    bid: $("#building").val()
            },
                
                success: function (data) {
                   
                   var finalData =  JSON.parse(data);
                    if(finalData.UserID){                   
                        var tenantId = $('#tenantId').val();  
                        var buildingID = $('#building').val(); 
                        var user_id = finalData.UserID;                     
                        var suitelocation = $('#suite_location').val();
                        var completenotification =  $('#complete_notification').val(); 
                        var ccenable =  $('#cc_enable').val(); 
                        var status  =  $('#status').val();                              
                        validateMultiuser(tenantId,buildingID,user_id,suitelocation,completenotification,ccenable);
                     
                        setTimeout(function(){
                        var checkVaue = $('#add-tenant-data').attr('uservalidate');
                        if(checkVaue=="true"){                               
                            $('#email-error').html("This User is already assign at this location.");
                            $('#email').addClass('inputErr');
                            $('#suite_location').focus();
                            $('#addtenantuser').attr('disabled', false);
                            }else{
                                //create new user
                                mapTenantUser(tenantId,buildingID,user_id,suitelocation,completenotification,ccenable);
                                //redirect page.
                                parent.location.reload();

                            }
                        }, 600);    

                    
                   }else{

                    $.ajax({
                        type: "POST",
                        url: baseUrl + 'tenant/checktenant',
                        data: {email: email},
                        beforeSend: function () {
                            //$('.loader').show();
                        },
                        success: function (msg) {
                            $('.loader').hide();
                            if (msg != true) {
                                $('#email-error').html("");
                                $('#email').removeClass('inputErr');
                                redirectPage = true;
                                createUser(tId);

                               // location.reload();
                            } else {
                                //alert('Company is not existed');
                                $('#email-error').html("Email already in use.");
                                $('#email').addClass('inputErr');
                                $('#email').focus();
                                $('#addtenantuser').attr('disabled', false);
                                return false;
                            }
                        }
                    });

                    if(redirectPage){
                        alert('redirecting page');
                        location.reload();
                    }

                   }
                   
                }
            });

            
        }

    } else {
        $('.loader').hide();
        $('#email-error').html("Enter the email-id");
        $('#email').addClass('inputErr');
        $('#email').focus();
        return false;
    }

}


function checktenantInfo(tId){
    if(tId!=''){
            $.ajax({
                type: "POST",
                url: baseUrl + 'tenant/checktenantinfo',

                data: {tid: tId},
                beforeSend: function () {
                    //$('.loader').show();
                },
                success: function (msg) {
                console.log(msg);
                }
            });

    }
}

function mapTenantUser(tenantId,buildingID,user_id,suitelocation,completenotification,ccenable){
    
    $.ajax({
         url         : baseUrl+"tenant/addtenantusers",
         type        : "post",
         datatype    : 'json',
         data        : {
            bid:buildingID,
            uid:user_id,
            tid:tenantId,
            suitelocation:suitelocation,
            ccenable:ccenable,
            completenotification:completenotification
        
         },
         beforeSend: function () {
         
        },
        success: function (response) {         
        
        }
         
         });
}

function validateMultiuser(tenantId,buildingID,user_id,suitelocation){
    $('#add-tenant-data').attr('uservalidate','');
    $.ajax({
        url         : baseUrl+"tenant/getmultiluserbylocation",
        type        : "post",
        datatype    : 'json',
        data        : {
            bid:buildingID,
            userId:user_id,
            tenantId:tenantId,
            suite_location:suitelocation        
        },       
        success: function (response) {          
           if(response =="1"){
              $('#add-tenant-data').attr('uservalidate','true');
           }else{
             $('#add-tenant-data').attr('uservalidate','');
           }
          }
        
        });
}

function createUser(tId) {
    $('.loader').show();
    var submit_flag = 0;
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
    var access = $('#access').val();

    var phone = $('#phone');
    var phone_value = phone.val();
    //var modulee = $('#modulee').val();

    if (email_value == '') {
        $('.uemailErr').html("E-Mail Address Required");
        email.addClass('inputErr');
        email.focus();
        submit_flag = 1;
    } else {
        $('.uemailErr').html('');
        email.removeClass('inputErr');
    }


    if (fname_value == '') {
        $('.ufirstErr').html("First Name Required");
        firstname.addClass('inputErr');
        firstname.focus();
        submit_flag = 1;
    } else {
        $('.ufirstErr').html('');
        firstname.removeClass('inputErr');
    }

    if (lname_value == '') {
        $('.ulastErr').html("Last Name Required");
        lastname.addClass('inputErr');
        lastname.focus();
        submit_flag = 1;
    } else {
        $('.ulastErr').html('');
        lastname.removeClass('inputErr');
    }

    if (suite_location_value == '') {
        $('.usuiteErr').html("Suite/Location Required");
        suite_location.addClass('inputErr');
        suite_location.focus();
        submit_flag = 1;
    } else {
        $('.usuiteErr').html('');
        suite_location.removeClass('inputErr');
    }

    if (phone_value == '') {
        $('.uofficeErr').html("Phone Number Required");
        phone.addClass('inputErr');
        phone.focus();
        submit_flag = 1;
    } else if (phone_value.length < 12) {
        $('.uofficeErr').html("Please enter 10 digits Phone Number");
        phone.addClass('inputErr');
        phone.focus();
        submit_flag = 1;
    } else {
        $('.uofficeErr').html('');
        phone.removeClass('inputErr');
    }

    if (submit_flag == 1) {
        $('.loader').hide();
        $('#addtenantuser').attr('disabled', false)
        return false;
    } else {
        //document.getElementById("addNewUser").submit();
        var fdata = $("form#addNewUser").serialize();
        var action = $("form#addNewUser").attr('action');
        $.post(action, fdata, function (data) {
            //data = $.parseJSON(data);
            data = JSON.parse(data);
            $('div.success_message').html(data.msg);
            // setInterval(function () {
            //     window.parent.location.href = main_url;
            // }, 1000);
        });
        setInterval(function () {
                var buildId = $('#building').val();
                var role_id = $('#role_id').val();
                if(role_id==5)
                window.parent.location.href = baseUrl+"tenant/tenantuser";
                else
                window.parent.location.href = baseUrl+"tenant/users/bid/"+buildId;
        }, 1000);

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
    //var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    //var filter = "/^([a-zA-Z0-9_.-'])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/";
    var filter = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;
    if (filter.test(sEmail)) {
        return true;
    } else {
        return false;
    }
}

function showEditTUser(userId, tId) {
    $(".message").html('');
    $(".error-msg").html('');
    if (tId != '' && userId != '') {
        $('.loader').show();
        $.ajax({
            url: baseUrl + "tenant/loadedittuser",
            type: "post",
            datatype: 'json',
            data: {tuserId: userId,
                tId: tId},
            success: function (data) {
                $('.loader').hide();
                if (data) {
                    var height = $("#tenantUserInfo_" + tId).height();
                    var tot_height = parseInt(parseInt(height) + 215)
                    $("#tenantuser_popup_" + tId).css('height', tot_height + 'px');
                    $("#tenantuser_popup_" + tId).show();
                    $('#edit-tuser-' + tId).show();
                    $('#edit_user_load_' + tId).html(data);
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

function cancelEditUser(tId) {
    $('#edit-tuser-' + tId).hide();
    $('#edit_user_load_' + tId).html('');
    $("#tenantuser_popup_" + tId).hide();
}

function deleteTenantUser(tId, uId, cnt) {
    var check_delete = 'YES';
    var role_id = $('#role_id').val();
    if (cnt > 1) {
        jPrompt('For Deleting Tenant User, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (r) {
            if (r != null) {
                if (check_delete === r) {
                    $('.loader').show();
                    $.ajax({
                        url: baseUrl + "tenant/deletetuser",
                        type: "post",
                        datatype: 'json',
                        data: {
                            uId: uId, tId: tId
                        },
                        success: function (result) {
                            $('.loader').hide();
                            var data = $.parseJSON(result);
                            //alert(data.msg);
                            if (data.msg == 'true') {
                                $('.message').html('Tenant user deleted successfully.');
                            } else {
                                $('.error-txt').html('Some error occurred.');
                            }
                            if (role_id == 5) {
                                window.location.href = baseUrl + 'tenant/tenantuser';
                            } else
                                loadTenantUser(tId);
                        }
                    });
                } else {
                    //$('.error-txt').html('You have entered wrong word.');
                    jAlert('You have entered wrong word.');
                }
            }
        });

    } else {
        jAlert('There must be more than one user. Please add one more user to delete the user of tenant.');
    }


}

function deleteTenant(tId) {
    var check_delete = 'YES';
    var bid = $('#building_id').val();
    //var return_value = prompt("For Deleting Tenant, Enter Yes in Capital letters.");
    jPrompt('For Deleting Tenant, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (r) {
        if (r) {
            if (check_delete === r) {
                $('.loader').show();
                $.ajax({
                    url: baseUrl + "tenant/deletetenant",
                    type: "post",
                    datatype: 'json',
                    data: {
                        tId: tId
                    },
                    success: function (result) {
                        $('.loader').hide();
                        var data = $.parseJSON(result);
                        if (data.msg == 'true') {
                            $('.message').html('Tenant deleted successfully.');
                            window.location.href = baseUrl + "tenant/users/bid/" + bid + "/msg/3";
                        } else {
                            $('.error-txt').html('Some error occurred.');
                        }
                    }
                });
            } else {
                //alert('You have entered wrong word.');
                //$('.error-txt').html('You have entered wrong word.');
                jAlert('You have entered wrong word.', 'Vision Work Orders');

            }
        }

    });


}

/**
 * check user name of tenant user
 */

function checkUserName() {
    parent.CheckForSessionpop(baseUrl);
    var userName = $('#userName').val();
    var uid = $('#uid').val();
    if (userName != '') {
        if (userName.length < 3) {
            $('.unameErr').html("User name should be at least 3 character.");
            $('#userName').addClass('inputErr');
            $('#userName').focus();
        } else {
            $.ajax({
                type: "POST",
                url: baseUrl + 'tenant/checkusername',
                data: {userName: userName, uid: uid},
                success: function (msg) {
                    //alert(msg);
                    $('.loader').hide();
                    if (msg != true) {
                        $('.unameErr').html("");
                        $('#userName').removeClass('inputErr');
                        checkUserEmail();
                    } else {
                        //alert('Company is not existed');
                        $('.unameErr').html("User name already in use.");
                        $('#userName').addClass('inputErr');
                        $('#userName').focus();
                        return false;
                    }
                }
            });
        }

    } else {
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

function checkUserEmail() {
    $('.loader').show();
    var checkComp = false;
    var email = $.trim($('#email').val());
    var uid = $('#uid').val();
    //alert('cname'+cname);
    if (email != '') {
        if (!validateEmail(email)) {
            $('.uemailErr').html("E-Mail Address Invalid");
            $('#email').addClass('inputErr');
            $('#email').focus();
        } else {
            $.ajax({
                type: "POST",
                url: baseUrl + 'tenant/checkuseremail',
                data: {email: email, uid: uid},
                success: function (msg) {
                    $('.loader').hide();
                    if (msg != true) {
                        $('#email-error').html("");
                        $('#email').removeClass('inputErr');
                        editTUser();
                    } else {
                        //alert('Company is not existed');
                        $('#email-error').html("Email already in use.");
                        $('#email').addClass('inputErr');
                        $('#email').focus();
                        return false;
                    }
                }
            });
        }

    } else {
        $('.loader').hide();
        $('#email-error').html("Enter the email-id");
        $('#email').addClass('inputErr');
        $('#email').focus();
        return false;
    }

}

function sendtenantemail(tid) {
    $('.loader').show();
    $('.error-txt').html('');
    $('.message').html("");
    $.ajax({
        type: "POST",
        url: baseUrl + 'tenant/sendtenantemail',
        data: {tid: tid},
        success: function (data) {
            $('.loader').hide();
            if (data == true) {
                $('.message').text("Notification email successfully sent to all users.");
                $('.message').show();
            } else {
                //alert('There was an error');			
                $('.error-txt').html('Error occured during sending welcome letters.');
            }

        },
        error: function () {
            $('.loader').hide();
            //alert('There was an error');
            $('.error-txt').html('Error occured during sending welcome letters.');
        }

    });
}

function sendemail(tid, uid) {
    $('.loader').show();
    $('.error-txt').html('');
    $('.message').html("");
    $.ajax({
        type: "POST",
        url: baseUrl + 'tenant/sendemail',
        data: {tid: tid, uid: uid},
        success: function (data) {
            $('.loader').hide();
            if (data == true) {
                $('.message').text("Notification email successfully sent.");
                $('.message').show();
            } else {
                //alert('There was an error');
                $('.error-txt').html('Error occured during sending welcome letters.');
            }
        },
        error: function () {
            $('.loader').hide();
            //alert('There was an error');
            $('.error-txt').html('Error occured during sending welcome letters.');
        }

    });
}

function editTUser() {
    var submit_flag = 0;
    var userName = $('#userName').val();
    var email = $('#email').val();
    var firstname = $('#firstname').val();
    var lastname = $('#lastname').val();
    var suite_location = $('#suite_location').val();
    var phone = $('#phone').val();
    var send_as = $('#send_as').val();
    var access = $('#access').val();
    var userId = $('#user_id').val();
    var password = $('#password').val();
    var tenantId = $('#tenantId').val();
    var building = $('#building').val();
    var uid = $('#uid').val();
    var id = $('#id').val();
    var role_id = $('#role_id').val();
    var confirm_password = $('#confirm_password').val();
    var auto = 0;
    var complete_notification = 0;
    var note_notification = 0;
    var cc_enable = 0;
    var status = 0;
    var welcome_letter = 0;
    
    if (firstname == '') {
        $('.ufirstErr').html("First Name Required");
        $('#firstname').addClass('inputErr');
        submit_flag = 1;
    } else {
        $('.ufirstErr').html('');
        $('#firstname').removeClass('inputErr');
    }

    if (lastname == '') {
        $('.ulastErr').html("Last Name Required");
        $('#lastname').addClass('inputErr');
        submit_flag = 1;
    } else {
        $('.ulastErr').html('');
        $('#lastname').removeClass('inputErr');
    }

    if (suite_location == '') {
        $('.usuiteErr').html("Suite/Location Required");
        $('#suite_location').addClass('inputErr');
        submit_flag = 1;
    } else {
        $('.usuiteErr').html('');
        $('#suite_location').removeClass('inputErr');
    }

    if (phone == '') {
        $('.uofficeErr').html("Phone Number Required");
        $('#phone').addClass('inputErr');
        submit_flag = 1;
    } else if (phone.length < 12) {
        $('.uofficeErr').html("Please enter 10 digits Office Phone number");
        $('#phone').addClass('inputErr');
        submit_flag = 1;
    } else {
        $('.uofficeErr').html('');
        $('#phone').removeClass('inputErr');
    }

    if (password != confirm_password) {
        $('.passwordErr').html("Password does not match.");
        $('#password').addClass('inputErr');
        $('#confirm_password').addClass('inputErr');
        submit_flag = 1;
    } else {
        $('.passwordErr').html("");
        $('#password').removeClass('inputErr');
        $('#confirm_password').removeClass('inputErr');
    }

    if ($('#complete_notification').is(":checked")) {
        complete_notification = 1;
    }
    if ($('#note_notification').is(":checked")) {
        note_notification = 1;
    }
    if ($('#cc_enable').is(":checked")) {
        cc_enable = 1;
    }
    if ($('#status').is(":checked")) {
        status = 1;
    }
    if ($('#welcome_letter').is(":checked")) {
        welcome_letter = 1;
    }
    if ($('#auto').is(":checked")) {
        auto = 1;
    }


    if (submit_flag == 1) {
        $('.loader').hide();
        return false;
    } else {

        var userData = new FormData();
        if ($('#user_img').val() != '') {
            var userData1 = $('#user_img').prop('files')[0];
            userData.append('file', userData1);
        }
        userData.append('userName', userName);
        userData.append('email', email);
        userData.append('firstname', firstname);
        userData.append('lastname', lastname);
        userData.append('suite_location', suite_location);
        userData.append('phone', phone);
        userData.append('access', access);
        userData.append('send_as', send_as);
        userData.append('complete_notification', complete_notification);
        userData.append('note_notification', note_notification);
        userData.append('cc_enable', cc_enable);
        userData.append('status', status);
        userData.append('welcome_letter', welcome_letter);
        userData.append('welcome_letter', welcome_letter);
        userData.append('password', password);
        userData.append('confirm_password', confirm_password);
        userData.append('auto', auto);
        userData.append('tenantId', tenantId);
        userData.append('building', building);
        userData.append('uid', uid);
        userData.append('id', id);
        userData.append('role_id', role_id);


        //console.log(userData);

        var action = $("form#editUser").attr('action');
        $.ajax({
            url: action,
            type: "post",
            datatype: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: userData,
            success: function (data) {
                data = $.parseJSON(data);
                $('.loader').hide();
                $('div.success_message').html(data.msg);
                var main_url = baseUrl + data.url;
                var locationuser = $('#locationuser').val();
                if(locationuser =="yes"){
                    
                    setInterval(function () {
                        window.parent.location.href = baseUrl+'tenant/tenantoptions/id/'+id;
                        tenantOptionfilterData(email);
                    }, 1000);
                       
                }else{
                setInterval(function () {
                    window.parent.location.href = main_url;
                }, 1000);
              }
            },
            error: function () {
                $('.loader').hide();
                alert('There was an error');
            }
        });



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



function hideTenantInactiveUser(tId) {
    $('#open_div_' + tId).show();
    $('#close_div_' + tId).hide();
    $(".message").html('');
    $(".error-msg").html('');
    $('#loadtenant_' + tId).html('');
    $('#trId_' + tId).hide();

}

function loadTenantInactiveUser(tId, bid) {
    hideAllTenant();
    $('#open_div_' + tId).hide();
    $('#close_div_' + tId).show();
    $("#tenantuser_popup_" + tId).hide();
    if (tId != '') {
        $('.loader').show();
        $.ajax({
            url: baseUrl + "tenant/loadtenantinactiveuser",
            type: "post",
            datatype: 'json',
            data: {tId: tId, bId: bid},
            success: function (data) {
                $('.loader').hide();
                if (data) {
                    $('#loadtenant_' + tId).html(data);
                    $('#trId_' + tId).show();
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


function activeTenantRecoveryUser(tId, bid) {

    jConfirm("Do you really want to active tenant and it's user?", 'Confirmation Dialog', function (r) {
        //var confirmUser = confirm("Do you really want to active tenant and it's user?");
        hideAllTenant();
        if (r == true) {
            if (tId != '') {
                $('.loader').show();
                $.ajax({
                    url: baseUrl + "tenant/recoveruser",
                    type: "post",
                    datatype: 'json',
                    data: {id: tId},
                    success: function (data) {

                        //alert(data);
                        if (data) {
                            $('.loader').hide();
                            window.location = baseUrl + "tenant/tenantrecovery/bid/" + bid + "/msg/1";
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


$(function () {

    $("#auto").on('click', function () {
        if (this.checked) {
            $("#auto_generate").hide();
            $('#password').val("");
            $('#confirm_password').val("");
            $('#welcome_letter').val("1");
        } else {
            $("#auto_generate").show();
            $('#password').val("");
            $('#confirm_password').val("");
            $('#welcome_letter').val("0")
        }
    });

});

function validateRecoverUser() {
    var checkedIds = $(".recover-user:checkbox:checked").map(function () {
        return this.value;
    }).get();

    if (checkedIds.length == 0) {
        jAlert("No tenant user is selected for recover.");
        return false;
    }
    var ra = jConfirm('Do you really want to active selected tenant user?', 'Confirmation Dialog', function (r) {
        if (r == true) {
            $("form#tenant_recovery_form").submit();
        }
    });

}
$(function () {
    $(".modalbox").fancybox({'openEffect': 'none', fitToView: true});
});
function addNewTenant(url) {
    CheckForSessionpop(baseUrl);
    //$('form#first_form input#tenantName').trigger('click');
    $('a[href="#addNewTenant"]').fancybox({
        type: 'iframe',
        href: url,
        width: 750,
        height: 600,
        'beforeClose': function () {
            $('.loader').hide();
        },
        'afterLoad': function () {
            $.fancybox.hideLoading();
            $('.loader').show();
            $('form#first_form input#tenantName').focus();
            $(this.content).attr("tabindex", 1).focus();
            setInterval(function () {
                $('.loader').hide();
            }, 5000);
        },
        afterShow: function () {
            $('iframe.fancybox-iframe').contents().find("#tenantName").attr("tabindex", 1).focus();
        }
    });
}
function addNewUser(url) {
    CheckForSessionpop(baseUrl);
    //$('form#addNewUser input#email').trigger('click');
    $('a[href="#addNewUser"]').fancybox({
        type: 'iframe',
        href: url,
        width: 750,
        height: 600,
        'beforeClose': function () {
            $('.loader').hide();
        },
        'afterLoad': function () {
            $.fancybox.hideLoading();
            $('.loader').show();
            setInterval(function () {
                $('.loader').hide();
            }, 5000);
        },
        afterShow: function () {
            $('iframe.fancybox-iframe').contents().find("input#email").attr("tabindex", 1).focus();
        }
    });
}
function editNewUser(url) {
    CheckForSessionpop(baseUrl);
    $('a[href="#editNewUser"]').fancybox({
        type: 'iframe',
        href: url,
        width: 750,
        height: 600,
        'beforeClose': function () {
            $('.loader').hide();
        },
        'afterLoad': function () {
            $.fancybox.hideLoading();
            $('.loader').show();
            setInterval(function () {
                $('.loader').hide();
            }, 5000);
        }
    });
}

function editTenant(url) {
    CheckForSessionpop(baseUrl);
    $('a[href="#editTenant"]').fancybox({
        type: 'iframe',
        href: url,
        width: 750,
        height: 600,
        'beforeClose': function () {
            $('.loader').hide();
        },
        'afterLoad': function () {
            $.fancybox.hideLoading();
            $('.loader').show();
            setInterval(function () {
                $('.loader').hide();
            }, 5000);
        }
    });
}

function editTenantAdmin() {
    parent.CheckForSessionpop(baseUrl);
    var tenantName = $.trim($('#tenantName').val());
    var main_contact = $.trim($('#main_contact').val());
    var suite_location = $.trim($('#suite_location').val());
    var phone = $.trim($('#phone').val());
    var billtoAddress = $.trim($('#billtoAddress').val());
    var tId = $.trim($('#tId').val());
    var tenantContact = $.trim($('#tenantContact').val());
    var submit_flag = true;
    var focus_flag = false;
    if (tenantName == '') {
        $('#taname-error').html("Tenant Name can't be blank");
        $('#tenantName').focus();
        submit_flag = false;
        focus_flag = true;
    } else {
        $('#taname-error').html("");
    }
    // if (main_contact == '') {
    //     $('#maincontact-error').html("Main contact can't be blank");
    //     if (!focus_flag) {
    //         $('#main_contact').focus();
    //         focus_flag = true;
    //     }
    //     submit_flag = false;
    // } else {
    //     $('#maincontact-error').html("");
    // }
    // if (suite_location == '') {
    //     $('#suite-error').html("Suite/Location can't be blank");
    //     if (!focus_flag) {
    //         $('#suite_location').focus();
    //         focus_flag = true;
    //     }
    //     submit_flag = false;
    // } else {
    //     $('#suite-error').html("");
    // }
    if (phone == '') {
        $('#office-phone-error').html("Main Contact Phone Number can't be blank");
        if (!focus_flag) {
            $('#phone').focus();
            focus_flag = true;
        }
        submit_flag = false;
    } else if (phone.length < 12) {
        $('#office-phone-error').html("10 Digits Phone Number Required");
        if (!focus_flag) {
            $('#phone').focus();
            focus_flag = true;
        }
        submit_flag = false;
    } else {
        $('#office-phone-error').html("");
    }




    if (billtoAddress == '') {
        $('#office-bill-error').html("Billing Address can't be blank");
        if (!focus_flag) {
            $('#billtoAddress').focus();
            focus_flag = true;
        }
        submit_flag = false;
    } else {
        $('#office-bill-error').html("");
    }
    if (submit_flag) {
        $('#edit_tenant_admin').attr('disabled', 'disabled');
        $.ajax({
            url: baseUrl + 'tenant/updatetenantinfo',
            type: 'post',
            datatype: 'json',
            data: {tenantName: tenantName, main_contact: main_contact,tenantContact:tenantContact, suite_location: suite_location, phone_number: phone, billtoAddress: billtoAddress, tId: tId},
            success: function (data) {
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                } else {
                    alert('There was an error');
                }
                setInterval(function () {
                    parent.location.reload();
                }, 1100);
                //setInterval(function(){ cancelAccessFrom(); }, 1100);
            },
            error: function () {
                $('.loader').hide();
                alert('There was an error');
            }

        });
    }


}

function openUploadImg() {
    document.getElementById('user_img').click();
}

function fileSelected(input) {
    document.getElementById('open_upload_pic').value = "File: " + input.files[0].name
}
function readImg(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#show_img').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function hidepassword() {
    if ($('#auto').is(':checked')) {
        $("#resetPassword").val('');
        $("#confirmPass").val('');
        $("#resetPassword").attr("readonly", true);
        $("#confirmPass").attr("readonly", true);
        $('.hidepassword').hide();
    } else {
        $("#resetPassword").attr("readonly", false);
        $("#confirmPass").attr("readonly", false);
        $('.hidepassword').show();
    }
}

function showTenantEditCoi(cid){
	if(cid!=''){
		$('.loader').show();
		$.ajax({
				url         : baseUrl+"tenant/editttenantservice",
				type        : "post",
				datatype    : 'json',
				data        : {cid:cid},
				success     : function( data ) {
					$('.loader').hide(); 
					//$('#edit_wo_form').show();
					$('#edit_service_form').html(data);
					//$('#edit_service_form').show();
					//$('.service_popup_form').show();
					$('#edit_service_form_href').trigger('click');
				},
				error       : function(){
					$('.loader').hide();
					alert('There was an error');
				}  
				
			 });
	 }
} 


function showAddCoiTenant(tId){
	if(tId!=''){
		$('.loader').show();
		$.ajax({
				url         : baseUrl+"coilist/addservice",
				type        : "post",
				datatype    : 'json',
				data        : {tId:tId},
				success     : function( data ) {
					$('.loader').hide(); 
					//$('#edit_wo_form').show();
					$('#add_service_form').html(data);
					//$('#add_service_form').show();
					//$('.service_popup_form').show();
					$('#add_service_form_href').trigger('click');
				},
				error       : function(){
					$('.loader').hide();
					alert('There was an error');
				}  
				
			 });
	 }
}


/** Tenant Add/Edit popup */

function updateCoi(cid){
	parent.CheckForSessionpop(baseUrl);
	if(cid!=''){
		
		var submit_flag=true;
		$('#save_service').attr('disabled','disabled');
		var userData = new FormData();	
		var buliding_id = $('#buliding_id').val();
		var unique_cost = $('#unique_cost').val();				
		var tenant_number = $('#tenant_number').val();
		var tenant_id = $('#tenant_id').val();		
		var coi_date_from = $('#coi_date_from').val();
		var coi_type = $('#coi_type').val();
		var edit_expiration_date = $('#edit_expiration_date').val();
		var equipmentmenual = $("#equipmentmenual").val().trim();
      
        if(edit_expiration_date==''){			
			$('#cerror_expiration').html('Please enter Expiration Date.');
			submit_flag = false;
		}else{
			$('#cerror_expiration').html('');
		}
        
        var manual = $("#equipmentmenual").prop('files')[0];
		var flag_val = escape($("#equipmentmenual").attr('name'));
		
        if (typeof manual !== 'undefined') {
        var ext = manual.name.split(".");
        var type_ext = ext[ext.length - 1];
        //console.log(type_ext);
        if (type_ext !== 'pdf' && type_ext !== 'PDF') {
            $("#equipmentmenual").focus();
            $("#equipmentmenual").addClass('error-border');
            $("#equipmentmenual_error").html("Please upload Only a PDF File");
            submit_flag = false;
        } else {
            $("#equipmentmenual_error").html("");
            $("#equipmentmenual").removeClass('error-border');
        }
    }
   
         if(submit_flag==false){			 
			$('#save_service').attr('disabled',false);
			return false; 
		 }else{			 
			   
			   if (typeof manual !== 'undefined') {
			    userData.append('file[' + flag_val + ']', manual);  
			   }
                userData.append('building_id', buliding_id);
				userData.append('uniquecostcenter', unique_cost);
				userData.append('tenant_number', tenant_number);
				userData.append('tenant_Id', tenant_id);
				userData.append('coi_au_date_from', coi_date_from);
				userData.append('coi_au_date_to', edit_expiration_date);
				userData.append('coi_au_Ten_or_Vendor', coi_type);
				userData.append('coi_au_tenant_id', cid);
			 
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"tenant/updateservice",
						type        : "post",
						datatype    : 'json',
						cache       : false,
                        contentType : false,
                        processData : false,
                        data        : userData,
						success     : function( data ) {
							$('.loader').hide(); 
							var content = $.parseJSON(data);
							if(content.status=='success'){
								$('#error_msg').html('');
								$('#success_msg').html(content.msg);
								location.reload();
							}else{
								//alert('Error occurred');
								$('#success_msg').html('');
								$('#error_msg').html(content.msg);
								$('#save_service').attr('disabled',false);
							}
						},
						error       : function(){
							$('.loader').hide();
							setInterval(function(){ jAlert('There was an error', 'Vision Work Orders'); }, 1000);
						}  
						
					 });
		 }
        
        
	}else{
		alert('Error Occurred');
		return false;
	}
}

/** Cancel or close Add/Edit popup */
function cancelEditService(){
	$('#edit_service_form').html('');
	$('#edit_service_form').hide();
	$('.service_popup_form').hide();
    $('.fancybox-wrap').hide();
    $('#fancybox-overlay').hide();
}


function editTenantAdminSetting(url) {
    CheckForSessionpop(baseUrl);
    $('a[href="#editTenantAdminSetting"]').fancybox({
        type: 'iframe',
        href: url,
        width: 750,
        height: 600,
        'beforeClose': function () {
            $('.loader').hide();
        },
        'afterLoad': function () {
            $.fancybox.hideLoading();
            $('.loader').show();
            setInterval(function () {
                $('.loader').hide();
            }, 5000);
        }
    });
}

/**
 * check user name of tenant user
 */

 function checkTenantUserName() {
    parent.CheckForSessionpop(baseUrl);
    var userName = $('#userName').val();
    var uid = $('#uid').val();
    var panel_role_id = $('#panel_role_id').val();
    if (userName != '') {
        if (userName.length < 3) {
            $('.unameErr').html("User name should be at least 3 character.");
            $('#userName').addClass('inputErr');
            $('#userName').focus();
        } else {
            $.ajax({
                type: "POST",
                url: baseUrl + 'tenant/checkusername',
                data: {userName: userName, uid: uid, panel_role_id:panel_role_id},
                success: function (msg) {
                    //alert(msg);
                    $('.loader').hide();
                    if (msg != true) {
                        $('.unameErr').html("");
                        $('#userName').removeClass('inputErr');
                        checkTenantUserEmail();
                    } else {
                        //alert('Company is not existed');
                        $('.unameErr').html("User name already in use.");
                        $('#userName').addClass('inputErr');
                        $('#userName').focus();
                        return false;
                    }
                }
            });
        }

    } else {
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

 function checkTenantUserEmail() {
    $('.loader').show();
    var checkComp = false;
    var email = $.trim($('#email').val());
    var uid = $('#uid').val();
    //alert('cname'+cname);
    if (email != '') {
        if (!validateEmail(email)) {
            $('.uemailErr').html("E-Mail Address Invalid");
            $('#email').addClass('inputErr');
            $('#email').focus();
        } else {
            $.ajax({
                type: "POST",
                url: baseUrl + 'tenant/checkuseremail',
                data: {email: email, uid: uid},
                success: function (msg) {
                    $('.loader').hide();
                    if (msg != true) {
                        $('#email-error').html("");
                        $('#email').removeClass('inputErr');
                        editTeantUser();
                    } else {
                        //alert('Company is not existed');
                        $('#email-error').html("Email already in use.");
                        $('#email').addClass('inputErr');
                        $('#email').focus();
                        return false;
                    }
                }
            });
        }

    } else {
        $('.loader').hide();
        $('#email-error').html("Enter the email-id");
        $('#email').addClass('inputErr');
        $('#email').focus();
        return false;
    }

}


function editTeantUser() {   
    var submit_flag = 0;
    var userName = $('#userName').val();
    var email = $('#email').val();
    var firstname = $('#firstname').val();
    var lastname = $('#lastname').val();
    var phone = $('#phone').val();
    var send_as = $('#send_as').val();
    var access = $('#access').val();
    var userId = $('#user_id').val();
    var password = $('#password').val();
    var tenantId = $('#tenantId').val();
    var building = $('#building').val();
    var uid = $('#uid').val();
    var id = $('#id').val();
    var role_id = $('#role_id').val();
    var panel_role_id = $('#panel_role_id').val();
    var suite_location = $('#suite_location').val();
    var confirm_password = $('#confirm_password').val();
    var auto = 0;
    var complete_notification = 0;
    var note_notification = 0;
    var cc_enable = 0;
    var status = 0;
    var welcome_letter = 0;
    if (firstname == '') {
        $('.ufirstErr').html("First Name Required");
        $('#firstname').addClass('inputErr');
        submit_flag = 1;
    } else {
        $('.ufirstErr').html('');
        $('#firstname').removeClass('inputErr');
    }

    if (lastname == '') {
        $('.ulastErr').html("Last Name Required");
        $('#lastname').addClass('inputErr');
        submit_flag = 1;
    } else {
        $('.ulastErr').html('');
        $('#lastname').removeClass('inputErr');
    }
    
    
    if (phone == '') {
        $('.uofficeErr').html("Phone Number Required");
        $('#phone').addClass('inputErr');
        submit_flag = 1;
    } else if (phone.length < 12) {
        $('.uofficeErr').html("Please enter 10 digits Office Phone number");
        $('#phone').addClass('inputErr');
        submit_flag = 1;
    } else {
        $('.uofficeErr').html('');
        $('#phone').removeClass('inputErr');
    }
    
    if ($('#complete_notification').is(":checked")) {
        complete_notification = 1;
    }
    if ($('#note_notification').is(":checked")) {
        note_notification = 1;
    }

    if ($('#cc_enable').is(":checked")) {
        cc_enable = 1;
    }
     
    if ($('#auto').is(":checked")) {
        auto = 1;
    }


    if (submit_flag == 1) {
        $('.loader').hide();
        return false;
    } else {

        var userData = new FormData();       
        userData.append('userName', userName);
        userData.append('email', email);
        userData.append('firstname', firstname);
        userData.append('lastname', lastname);
        userData.append('phone', phone);
        userData.append('send_as', send_as);
        userData.append('suite_location', suite_location);
        userData.append('complete_notification', complete_notification);
        userData.append('note_notification', note_notification);

        if(panel_role_id==5){
         userData.append('cc_enable', cc_enable);
        // userData.append('welcome_letter', welcome_letter);
        // userData.append('welcome_letter', welcome_letter);
         userData.append('status', status);
         userData.append('auto', auto);
        }    
       
        userData.append('tenantId', tenantId);
        userData.append('building', building);
        userData.append('uid', uid);
        userData.append('id', id);
        userData.append('role_id', role_id);



        var action = $("form#editUser").attr('action');
        $.ajax({
            url: action,
            type: "post",
            datatype: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: userData,
            success: function (data) {
                data = $.parseJSON(data);
                $('.loader').hide();
                $('div.success_message').html(data.msg);
                var main_url = baseUrl + 'tenant/myaccountsetting';
                setInterval(function () {
                    window.parent.location.href = main_url;
                }, 1000);
            },
            error: function () {
                $('.loader').hide();
                alert('There was an error');
            }
        });



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

/** delete tenant user by tenant Admin */
function deleteTUserByTadmin(tId, uId) {
    var check_delete = 'YES';
    if (uId) {
        jPrompt('For Deleting Tenant User, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (r) {
            if (r != null) {
                if (check_delete === r) {
                    $('.loader').show();
                    $.ajax({
                        url: baseUrl + "tenant/deletetuser",
                        type: "post",
                        datatype: 'json',
                        data: {
                            uId: uId, tId: tId
                        },
                        success: function (result) {
                            $('.loader').hide();
                            var data = $.parseJSON(result);
                            //alert(data.msg);
                            if (data.msg == 'true') {
                                $('.message').html('Tenant user deleted successfully.');
                            } else {
                                $('.error-txt').html('Some error occurred.');
                            }
                           
                            window.location.href = baseUrl + 'tenant/currentusers';
                        
                        }
                    });
                } else {
                    //$('.error-txt').html('You have entered wrong word.');
                    jAlert('You have entered wrong word.');
                }
            }
        });

    } else {
        jAlert('There must be more than one user. Please add one more user to delete the user of tenant.');
    }

}

