$(function () {
    $(".modalbox").fancybox({'openEffect': 'none', fitToView: true});
});


function removeUser(userId, buildId, totalUser, deleteEgroup) {
    var check_delete = 'YES';
    var role_id = $('#role_id').val();
    if (totalUser > 1) {
        if (deleteEgroup == 'Yes') {
            jPrompt('For Deleting Building User, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
                if (return_value != null) {
                    if (check_delete === return_value) {
                        var group_Ids = $('#group_Ids_' + userId).val();
                        $.ajax({
                            url: baseUrl + "company/deletebuildinguser",
                            type: "post",
                            datatype: 'json',
                            data: {
                                userId: userId, buildId: buildId, group_Ids: group_Ids
                            },
                            success: function (result) {
                                var data = $.parseJSON(result);
                                //alert(data.msg);
                                if (data.msg == 'true') {
                                    //$('.message').html('User has been deleted successfully from building.');
                                    jAlert('User has been deleted successfully from building', 'Alert Dialog');
                                } else {
                                    //$('.message').html('Some error occurred.');
                                    jAlert('Some error occurred.', 'Alert Dialog');
                                }
                                location.reload();
                                parent.location.reload();
                            }
                        });
                    } else {
                        //$('.message').html('You have entered wrong word.');
                        jAlert('You have entered wrong word.', 'Alert Dialog');
                    }
                }
            });
        } else {
            var distribution_group = $('#distribution_group_' + userId).val();
            //alert(distribution_group);
            jAlert('Account User cannot be removed from the system because they are the only one listed under the ' + distribution_group, 'Alert Dialog');
        }
    } else {
        jAlert('There must be more than one user. Please add one more user to delete the user from building.', 'Alert Dialog');
    }
}

function editAccountUsers(url) {
    CheckForSessionpop(baseUrl);
    $('a[href="#editAccountUsers"]').fancybox({
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
            $('iframe.fancybox-iframe').contents().find("#company_name").attr("tabindex", 1).focus();
        }
    });
}

function sendemail(uid, bid) {
    $('.loader').show();
    $('.error-txt').html('');
    $('.message').html("");
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'company/sendemail',
        data: {uid: uid, bid: bid},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                $('.error-txt').html("Notification email successfully sent.");
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

function updateAccountUser(uid, bid)
{


    var note_notification = 0;
    var alert_notification = 0;
    var ccwelcomeletter = 0;
    var auto_generate = 0;
    var firstName = $.trim($('#firstName').val());
    var lastName = $.trim($('#lastName').val());
    var title = $.trim($('#title').val());
    var phoneNumber = $.trim($('#phoneNumber').val());
    var phoneExt = $.trim($('#phoneExt').val());
    var email = $.trim($('#email').val());
    var userName = $.trim($('#userName').val());
    var resetPassword = $.trim($('#resetPassword').val());
    var confirmPass = $.trim($('#confirmPass').val());
    var access = $.trim($('#access').val());
    if ($('#note_notification').is(':checked:')) {
        note_notification = 1;
    }
    if ($('#alert_notification').is(':checked')) {
        alert_notification = 1;
    }
    if ($('#ccwelcomeletter').is(':checked')) {
        ccwelcomeletter = 1;
    }
    
    if ($('#autoGenerate').is(':checked')) {
        auto_generate = 1;
    }
    var access = $.trim($('#access').val());
    var check_auto = $('#autoGenerate').prop("checked");
    var submit_flag = true;

    if (firstName == '') {
        $('#ufName_error').html("First Name can't be blank");
        $('#firstName').focus();
        submit_flag = false;
    } else {
        $('#ufName_error').html("");
    }

    if (lastName == '') {
        $('#ulName_error').html("Last Name can't be blank");
        $('#lastName').focus();
        submit_flag = false;
    } else {
        $('#ulName_error').html("");
    }

    if (title == '') {
        $('#utitle_error').html("Title can't be blank");
        $('#title').focus();
        submit_flag = false;
    } else {
        $('#utitle_error').html("");
    }
    if (phoneNumber == '') {
        $('#phone-error').html("Phone Number Required");
        $('#phoneNumber').focus();
        submit_flag = false;
    } else if (phoneNumber.length < 12) {
        $('#phone-error').html("10 Digits Phone Number Required");
        $('#phoneNumber').focus();
        submit_flag = false;
    } else {
        $('#phone-error').html('');
    }

    if (email == '') {
        $('#email-error').html("Email can't be blank");
        $('#email').focus();
        submit_flag = false;
    } else if (!isValidEmailAddress(email)) {
        $("#email-error").html("Please enter valid E-mail address");
        submit_flag = false;
    } else {
        $('#email-error').html("");
    }

    if (userName == '') {
        $('#userName-error').html("User Name can't be blank");
        $('#userName').focus();
        submit_flag = false;
    } else if (userName.length < 4) {
        $('#userName-error').html("User Name can't be less than 4 words");
        submit_flag = false;
    } else {
        $('#userName-error').html("");
    }



    var decimal = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,15}$/;
    if (!resetPassword.match(decimal) && check_auto != false && resetPassword != '') {
        $('#resetPassword-error').html("Password is not in the correct format");
        $('#resetPassword').focus();
        submit_flag = false;
    } else {
        $('#resetPassword-error').html("");
    }

    if (confirmPass == '' && check_auto == false && resetPassword != '') {
        $('#confirmpass-error').html("Confirm password can't be blank");
        $('#confirmPass').focus();
        submit_flag = false;
    } else {
        $('#confirmpass-error').html("");
    }
    if (confirmPass != resetPassword) {
        $('#confirmpass-error').html("Passwords does not match!");
        $('#confirmPass').focus();
        submit_flag = false;
    }

    var userData = new FormData();
    if ($('#user_img').val() != '') {
        var userData1 = $('#user_img').prop('files')[0];
        userData.append('file', userData1);
    }
    userData.append('firstName', firstName);
    userData.append('lastName', lastName);
    userData.append('Title', title);
    userData.append('email', email);
    userData.append('userName', userName);
    userData.append('resetPassword', resetPassword);
    userData.append('confirmPass', confirmPass);
    userData.append('note_notification', note_notification);
    userData.append('ccwelcomeletter', ccwelcomeletter);
    userData.append('alert_notification', alert_notification);
    userData.append('auto_generate', auto_generate);
    userData.append('phoneNumber', phoneNumber);
    userData.append('uid', uid);
    userData.append('bid', bid);
    userData.append('role_id', access);
    userData.append('phoneExt', phoneExt);

    if (submit_flag) {
        $('#updateUser').attr('disabled', 'disabled');
        $('.loader').show();
        $.ajax({
            url: baseUrl + "user/checkuser",
            type: "post",
            datatype: 'json',
            data: {email: email, uid: uid},
            success: function (data) {
                $('.loader').hide();
                if (data == 'false') {
                    insertUser(userData);
                } else if (data == 'true') {
                    $('#email-error').html("Email id is already exist");
                    $('#updateUser').attr('disabled', 'false');
                } else {
                    var content = $.parseJSON(data);
                    if (content[0].user_id == uid) {
                        insertUser(userData);
                    } else {
                        $('#updateUser').attr('disabled', 'false');
                        $('#email-error').html("Email id is already exist");
                    }
                }
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
function isValidEmailAddress(emailAddress) {
    var pattern = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    return pattern.test(emailAddress);
}


function selectAutogenerate(data) {
    if ($(data).prop("checked") == true) {
        $("#resetPassword").val('');
        $("#confirmPass").val('');
        $("#resetPassword").attr("readonly", true);
        $("#confirmPass").attr("readonly", true);
        $('.hidepassword').hide();
    } else if ($(data).prop("checked") == false) {
        $("#resetPassword").attr("readonly", false);
        $("#confirmPass").attr("readonly", false);
        $('.hidepassword').show();
    }
}

function insertUser(userData) {
    $.ajax({
        url: baseUrl + "company/updateaccountuser",
        type: "post",
        datatype: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: userData,
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            //console.log(content);
            //return false;
            if (content.status == 'success') {
                $('.success_message').html(content.msg);
            } else {
                alert('There was an error');
            }
            setInterval(function () {
                parent.jQuery.fancybox.close();
                //window.location = window.location.href;
                //parent.location.reload();
            }, 1100);
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }
    });


}

function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31
            && (charCode < 48 || charCode > 57))
        return false;

    return true;
}
function cancelUser() {
    $("div.fancybox-close").trigger("click");
    parent.jQuery.fancybox.close();
}

function openAccessMaterix(uid,bid) {
    parent.CheckForSessionpop(baseUrl);
    $.ajax({
        url: baseUrl + "company/useraccess",
        type: "post",
        datatype: 'json',
        data: {'uid': uid, bid : bid},
        success: function (data) {
           // alert(data);
            $('.loader').hide();
            $('#show_user_access').html(data);
            $('#show_user_access_href').trigger('click');
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }
    });

}

function cancelAccessFrom() {
    $("div.fancybox-close").trigger("click");
    $("#show_user_access").hide();
    $("#show_user_access").val('');
}

function saveAccess()
{
    $('#user_access_save').attr('disabled', true);
    parent.CheckForSessionpop(baseUrl);
    var uid = $('#uid').val();
    var access_location = {};
    $(".read_user:checked").each(function () {
        var location_id = $(this).val();
        access_location[location_id] = {is_read: 1, is_write: 0, is_access: 0, user_id: uid};
    });
    $(".write_user:checked").each(function () {
        var location_id = $(this).val();
        access_location[location_id] = {is_read: 1, is_write: 1, is_access: 0, user_id: uid};
    });
    $(".no_access_user:checked").each(function () {
        var location_id = $(this).val();
        access_location[location_id] = {is_read: 0, is_write: 0, is_access: 1, user_id: uid};
    });

    $.ajax({
        url: baseUrl + 'company/saveuseraccess',
        type: 'post',
        datatype: 'json',
        data: access_location,
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                $('.success_message').html(content.msg);
                $("#access option[value='']").remove();
                $('#access').append($("<option></option>").attr({'value': '', 'selected': true}).text('Customized'));
            } else {
                alert('There was an error');
            }
            setTimeout(function () {
                cancelAccessFrom();
            }, 1100);
        },
        error: function () {
            $('.loader').hide();
            $('#user_access_save').attr('disabled', false);
            alert('There was an error');
        }

    });


}
function hidepassword() {
    if ($('#auto').is(':checked')) {
        $('.hidepassword').hide();
    } else {
        $('.hidepassword').show();
    }
}
$(function () {
    $("#phoneNumber").mask("?999.999.9999");
});


/* Logout Time Changs */
function logouttime(uid){
    var time = $('#logout_time').val();
    $.ajax({
        url: baseUrl + 'company/logouttime',
        type: 'post',
        data: {
            uid: uid,time: time
        },
        success:function(data){
            console.log(data);
            var content = $.parseJSON(data);
            $('.logouttime_msg').html(content.msg);
        }
    });
    
}