function hideEmailUser(gid) {
    $('#open_div_' + gid).show();
    $('#close_div_' + gid).hide();
    $(".message").html('');
    $(".error-msg").html('');
    $('#loadtenant_' + gid).html('');
    $('#trId_' + gid).hide();

}
function hideAllTenant() {
    $('.open_plus').show();
    $('.open_close').hide();
    $('.tremail-class').hide();
    $('.tdemail-class').html('');
}
function loadEmailUser(gid, bid) {
    hideAllTenant();
    $('#open_div_' + gid).hide();
    $('#close_div_' + gid).show();
    $("#tenantuser_popup_" + gid).hide();
    if (gid != '') {
        $('.loader').show();
        $.ajax({
            url: baseUrl + "emails/loademailgroupuser",
            type: "post",
            datatype: 'json',
            data: {gid: gid, bid: bid},
            success: function (data) {
                $('.loader').hide();
                if (data) {
                    $('#loademail_' + gid).html(data);
                    $('#trId_' + gid).show();
                } else {
                    jAlert('There was an error', 'Vision Work Orders');
                }
            },
            error: function () {
                $('.loader').hide();
                jAlert('There was an error', 'Vision Work Orders');
            }

        });
    } else {
        jAlert('Some error occurred', 'Vision Work Orders');
    }
}

function deleteEmailGroup(gid, bid) {
    var cntUsers = $('#group_user_count_' + gid).val();
    if (cntUsers > 1) {
        jConfirm('Do you really want to delete the user from group?', 'Confirmation Dialog', function (r) {
            if (r == true) {
                $('.loader').show();
                $.ajax({
                    url: baseUrl + "emails/deleteemailgroup",
                    type: "post",
                    datatype: 'json',
                    data: {
                        gid: gid, bid: bid
                    },
                    success: function (result) {
                        $('.loader').hide();
                        if (result == true) {
                            $('.message').html('Email Group deleted successfully.');
                            window.location = baseUrl + 'emails/console';
                        } else {
                            $('.error-txt').html('Some error occurred.');
                            $('.loader').hide();
                        }
                    }
                });
            }
        });
    } else {
        jAlert("There is only one user in email group. To delete the user, Email group must have more than one user.", 'Vision Work Orders');
    }
}

function deleteEmailUser(id, gid, bid) {
    var countUsers = $('#user_count').val();
    if (countUsers > 1) {
        jConfirm('Do you really want to delete the user from group?', 'Confirmation Dialog', function (return_value) {
            if (return_value) {
                $('.loader').show();
                $.ajax({
                    url: baseUrl + "emails/deletetuserfromgroup",
                    type: "post",
                    datatype: 'json',
                    data: {
                        id: id, gid: gid
                    },
                    success: function (result) {
                        if (result == true) {
                            $('.message').html('Email user deleted successfully.');
                        } else {
                            $('.error-txt').html('Some error occurred.');
                        }
                        loadEmailUser(gid, bid);
                        $('.loader').hide();

                    }
                });
            }
        });

    } else {
        jAlert("There is only one user in email group. To delete the user, Email group must have more than one user.", "Vision Work Orders");
    }

}


function cancelUser() {
    var groupId = $('#groupId').val();
    var bId = $('#buildingId').val();
    window.location.href = baseUrl + 'emails/console/gid/' + groupId + '/bid' + bId;
}


function move_list_items(sourceid, destinationid)
{
    var select_item = $("#" + sourceid + "  option:selected").val();
    if (select_item == '' || select_item == null || select_item == undefined) {
        jAlert('Please select item', 'Vision Work Orders');
        return false;
    }
    $("#" + sourceid + "  option:selected").appendTo("#" + destinationid);
}

//this will move all selected items from source list to destination list
function move_list_items_all(sourceid, destinationid)
{
    $("#" + sourceid + " option").appendTo("#" + destinationid);
}
function  checkEditEmailGroup() {
    parent.CheckForSessionpop(baseUrl);
    var group = $("#group").val();
    var building = $("#bid").val();
    var gid = $("#gid").val();
    if (group.length == 0) {
        $("#group-error").html("Please enter group name.");
    } else {
        $.ajax({
            url: baseUrl + "emails/checkeditgroup",
            type: "post",
            data: {
                group_name: group, building: building, gid: gid
            },
            success: function (result) {
                if (result != 'true') {
                    $("#group-error").html("");
                    editEmailGroup();
                } else {
                    $("#group-error").html("Group Name already in use.");
                }

            }
        });

    }
}
function editEmailGroup() {

    $('#to_select_list option').prop('selected', true);
    $('#old_user option').prop('selected', true);
    var group = $("#group").val();
    var gactive = $("#group_active").val();
    var status = $("#active").val();
    var to_select_list = [];

    $('#to_select_list option').each(function () {
        to_select_list.push($(this).val());
    });

    var isError = false;
    if (gactive == 'false' && status == '0') {
        $("#error_msg").html("This group can not be inactive because this group is used in category.");
        isError = true;
    } else {
        $("#error_msg").html("");
    }

    if (group.length == 0) {
        $("#group-error").html("Please enter group name.");
        isError = true;
    } else {
        $("#group-error").html("");
    }

    if (to_select_list.length == 0) {
        $("#list-error").html("Please add atleast one user in distribution group.");
        isError = true;
    } else {
        $("#list-error").html("");
    }

    if (!isError) {
        //document.getElementById("edit_group").submit();
        var fdata = $("form#edit_group").serialize();
        var action = $("form#edit_group").attr('action');
        $.post(action, fdata, function (data) {
            data = $.parseJSON(data);
            $('div.success_message').html(data.msg);
            var main_url = baseUrl + "emails/console";
            setInterval(function () {
                window.parent.location.href = main_url;
            }, 1000);
        });
    }
}

function checkGroupName() {
    $('#emailsave').attr('disabled', true);
    parent.CheckForSessionpop(baseUrl);
    var group = $("#group").val();
    var building = $("#bid").val();
    if (group.length == 0) {
        $("#group-error").html("Please enter group name.");
        $('#emailsave').attr('disabled', false);
    } else {
        $.ajax({
            url: baseUrl + "emails/checkgroup",
            type: "post",
            data: {
                group_name: group, building: building
            },
            success: function (result) {
                if (result != 'true') {
                    $("#group-error").html("");
                    saveEmailGroup();
                } else {
                    $("#group-error").html("Group Name already in use.");
                }

            }
        });

    }
}

function saveEmailGroup() {
    $('#to_select_list option').prop('selected', true);
    var group = $("#group").val();
    var to_select_list = [];

    $('#to_select_list option').each(function () {
        to_select_list.push($(this).val());
    });

    var isError = false;
    if (group.length == 0) {
        $("#group-error").html("Please enter group name.");
        isError = true;
    } else {
        $("#group-error").html("");
    }

    if (to_select_list.length == 0) {
        $("#list-error").html("Please add atleast one user in distribution group.");
        isError = true;
    } else {
        $("#list-error").html("");
    }

    if (!isError) {
        //document.getElementById("email_group").submit();		
        var fdata = $("form#email_group").serialize();
        var action = $("form#email_group").attr('action');
        $.post(action, fdata, function (data) {
            data = $.parseJSON(data);
            $('div.success_message').html(data.msg);
            var main_url = baseUrl + "emails/console";
            setInterval(function () {
                window.parent.location.href = main_url;
            }, 1000);
        });
    } else {
        $('#emailsave').attr('disabled', false);
    }
}
function checkEditUserGroup() {
    parent.CheckForSessionpop(baseUrl);
    var fdata = $("form#editUser").serialize();
    var action = $("form#editUser").attr('action');
    $.post(action, fdata, function (data) {
        data = $.parseJSON(data);
        $('div.success_message').html(data.msg);
        var main_url = baseUrl + data.url;
        setInterval(function () {
            window.parent.location.href = main_url;
        }, 1000);
    });
}
$(function () {
    /*$("#save").on("click", function(){
     $('#to_select_list option').prop('selected', true);
     var group = $("#group").val();
     var to_select_list = [];
     
     $('#to_select_list option').each(function() {
     to_select_list.push($(this).val());
     });
     
     var isError = false;
     if(group.length == 0) {
     $("#group-error").html("Please enter group name.");
     isError = true;
     }else {
     $("#group-error").html("");
     }
     
     if(to_select_list.length == 0) {
     $("#list-error").html("Please add atleast one user in distribution group.");
     isError = true;
     } else {
     $("#list-error").html("");
     }
     
     if(!isError) {
     document.getElementById("email_group").submit();
     } 
     });*/


    $("#saveEmailData").on("click", function () {

        var title = $("#title").val();
        var subject = $("#subject").val();
        var role = $("#user_role").val();
        var content = CKEDITOR.instances.email_content.getData(); //$("#email_content").val(); //tinymce.get('content').getContent();
        $("#email_content").text(content);
        // alert(content);
        // return false;

        var isError = false;
        if (title.length == 0) {
            $("#title-error").html("Please enter title ");
            isError = true;
        } else {
            $("#title-error").html("");
        }


        if (subject.length == 0) {
            $("#subject-error").html("Please enter subject");
            isError = true;
        } else {
            $("#subject-error").html("");
        }

        if (content.length == 0) {
            $("#content-error").html("Please enter content");
            isError = true;
        } else {
            $("#content-error").html("");
        }

        if (!isError) {
            document.getElementById("template_form").submit();
        }
    });


    $("#saveEmail").on("click", function () {
        var title = $("#title_name").val();
        var subject = $("#subject").val();
        var role = $("#user_role").val();
        var content = $("#content").val();
        var content = CKEDITOR.instances.content.getData(); //$("#email_content").val(); //tinymce.get('content').getContent();
        $("#content").text(content);

        var isError = false;
        if (title.length == 0) {
            $("#title-error").html("Please enter title ");
            isError = true;
        } else {
            $("#title-error").html("");
        }


        if (subject.length == 0) {
            $("#subject-error").html("Please enter subject");
            isError = true;
        } else {
            $("#subject-error").html("");
        }

        if (content.length == 0) {
            $("#content-error").html("Please enter content");
            isError = true;
        } else {
            $("#content-error").html("");
        }

        if (!isError) {
            document.getElementById("template_form").submit();
        }
    });

});
$(document).ready(function () {
    $('[data-toggle="popover"]').popover();
    $(".popover").css("max-width", 300);
});
$(function () {
    $(".modalbox").fancybox({'openEffect': 'none', fitToView: true});
    $.fancybox.hideLoading();
});
function addNewDisGroup(url) {
    CheckForSessionpop(baseUrl);
    $('a[href="#addNewDisGroup"]').fancybox({
        type: 'iframe',
        href: url,
        width: 800,
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
function editNewDisGroup(url) {
    CheckForSessionpop(baseUrl);
    $('a[href="#editNewDisGroup"]').fancybox({
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

function editEmailUser(url) {
    CheckForSessionpop(baseUrl);
    $('a[href="#editEmailUser"]').fancybox({
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

function cancelEmailGroup() {
    parent.jQuery.fancybox.close();
}
