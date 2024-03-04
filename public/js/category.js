$(function () {
    $(".modalbox").fancybox({'openEffect': 'none', fitToView: true});
});
/* function showEditPriority(pid){
 $('.edit-priority').hide();
 $('.show-priority').show();
 $('#show_priority_id_'+pid).hide();
 $('#edit_priority_id_'+pid).show();
 } */

function hideEditPriority(pid) {
    $('#edit_priority_id_' + pid).hide();
    $('#show_priority_id_' + pid).show();
}


function deletePriority(pid) {
    //var priorityId =pid;
    $('.loader').show();
    $.ajax({
        url: baseUrl + "category/isprioritydeletable",
        type: "post",
        datatype: 'json',
        data: {pid: pid},
        success: function (result) {
            $('.loader').hide();
            var data = $.parseJSON(result);

            if (parseInt(data.total) > 0) {
                //$('.message').html('<font color="red">Sorry! This priority is already associated with category.</font>');
                //$('.message').show();
                jAlert('Sorry! This priority is already associated with category.', 'Vision Work Orders');
                ;
                setTimeout(function () {
                    $(".message").hide('blind', {}, 500)
                }, 5000);
            } else {
                jConfirm('Are you sure, you want to delete the priority?', 'Vision Work Orders', function (r) {
                    if (r == true)
                    {
                        $('.loader').show();
                        building_id = $('#building_id').val();
                        $.ajax({
                            type: "POST",
                            url: baseUrl + "category/deletepriority",
                            dataType: 'json',
                            data: {
                                pid: pid
                            },
                            success: function (msg) {
                                $('.loader').hide();
                                if (msg.status == 'error')
                                {
                                    jAlert(msg.message, 'Vision Work Orders');
                                    return false;
                                } else if (msg.status == 'success')
                                {
                                    $('#show_priority_id_' + pid).remove();
                                    $('#edit_priority_id_' + pid).remove();
                                    showPriorityList(building_id);
                                    showCategoryList(building_id);
                                    //var msgId = 3;
                                    //filtercompany(msgId);
                                }
                            }

                        });
                    }
                });

            }
        },
        error: function () {
            jAlert('There was an error', 'Vision Work Orders');
        }

    });

}

function  addPriority() {
    $('.loader').show();
    $('#save_priority').attr('disabled', true);
    var checkComp = false;
    var priorityName = $('#priority_name').val();
    var building_id = $('#building_id').val();
    //alert('hello'+priorityName);
    //alert('cname'+cname);
    if (priorityName != '') {
        $.ajax({
            type: "POST",
            url: baseUrl + 'category/checkpriority',
            data: {priorityName: priorityName,
                building_id: building_id},
            success: function (msg) {

                $('.loader').hide();
                if (msg == 'true') {
                    $("#save_priority").attr('disabled', false);
                    $("#priority-name-error").html("Priority Name already in use.");
                    $('#priority_name').addClass('inputErr');
                    $('#priority_name').focus();
                    return false;

                } else {
                    $('#priority-name-error').html("");
                    $('#priority_name').removeClass('inputErr');
                    savePriority();
                }
            }
        });

    } else {
        $('.loader').hide();
        $("#save_priority").attr('disabled', false);
        $("#priority-name-error").html("Please enter priority name");
        $('#priority_name').addClass('inputErr');
        $('#priority_name').focus();
        return false;
    }
}

function showPriorityForm() {
    //var height = $("#priority_info").height();	
    //var tot_height = parseInt(parseInt(height)+215)
    //$("#priority_popup").css('height',tot_height+'px');	 
    //$("#priority_popup").show();
    $("#priority-name-error").html("");
    $('#priority_name').removeClass('inputErr');
    $("#priority-description-error").html("");
    $('#priority_description').removeClass('inputErr');
    $('#priority_name').val('');
    $('#priority_description').val('');
    //$('#add-priority-td').show();
    $('#add-priority-td_href').trigger('click');
    //$('form#addNewPriority input#priority_name').focus();
    //$('.loader').show();	

}
function hidePriorityForm() {
    $("div.fancybox-close").trigger("click");
    $('#priority_name').val('');
    $('#priority_description').val('');
    $('#save_priority').attr('disabled', false);
    $('#add-priority-td').hide();
    $("#priority_popup").hide();
}


function savePriority() {
    $('#save_priority').attr('disabled', true);
    var priorityName = $('#priority_name').val();
    var priorityDescription = $('#priority_description').val();
    var status = $('#status').val();
    var email_status = $('#email_status').val();
    var global_template = $('#global_template').val();
    var valid_true = true;
    if (priorityName == '') {
        $("#priority-name-error").html("Please enter priority name");
        $('#priority_name').addClass('inputErr');
        valid_true = false;
    } else {
        $("#priority-name-error").html("");
        $('#priority_name').removeClass('inputErr');
    }
    if (priorityDescription == '') {
        $("#priority-description-error").html("Please enter priority description");
        $('#priority_description').addClass('inputErr');
        valid_true = false;
    } else {
        $("#priority-description-error").html("");
        $('#priority_description').removeClass('inputErr');
    }

    if (valid_true == false) {
        $('#save_priority').attr('disabled', false);
        return false;
    } else {
        $('.loader').show();
        building_id = $('#building_id').val();
        $.ajax({
            type: "POST",
            url: baseUrl + "category/savepriority",
            dataType: 'json',
            data: {
                priorityName: priorityName,
                priorityDescription: priorityDescription,
                status: status,
                building_id: building_id,
                email_status: email_status,
                global_template: global_template
            },
            success: function (msg) {
                if (msg.status == 'error')
                {
                    //$('.message').html(msg.message);
                    showMessage(msg.message, 0)
                    hidePriorityForm();
                    $('.loader').hide();

                } else if (msg.status == 'success')
                {
                    //$('.message').html(msg.message);
                    showMessage(msg.message, 1)
                    showPriorityList(building_id);
                    hidePriorityForm();
                }
            }

        });
    }
}

function editPriority(pid) {
    var priority_name = $('#edit_priority_name').val();
    var priority_description = $('#edit_priority_description').val();
    var priority_status = $('#edit_status').val();
    var email_status = $('#edit_email_status').val();
    var buildingId = $('#building_id').val();
    var global_template = $('#edit_global_template').val();
    if (priority_name == '') {
        jAlert('Please enter the priority name', 'Vision Work Orders');
        return false;
    }

    if (priority_description == '') {
        jAlert('Please enter the priority description', 'Vision Work Orders');
        return false;
    } else {
        $('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + "category/editpriority",
            dataType: 'json',
            data: {
                priorityName: priority_name,
                priorityDescription: priority_description,
                status: priority_status,
                pid: pid,
                building_id: buildingId,
                email_status: email_status,
                global_template: global_template
            },
            beforeSend: function () {
                //$('.loader').show();
            },
            success: function (msg) {
                $('.loader').hide();
                if (msg.status == 'error')
                {


                } else if (msg.status == 'success')
                {
                    //$('.message').html(msg.message);
                    showMessage(msg.message, 1);
                    showPriorityList(buildingId);
                    hidePriorityForm();

                } else if (msg.status == 'priority_error') {
                    jAlert(msg.message, 'Vision Work Orders');
                }
            }

        });
    }
}

function showPriorityList(buildingId, order) {
    var order = (typeof order === "undefined") ? "default" : order;
    if (buildingId != '') {
        var page = $('#priority_page').val();
        $('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + "category/showprioritylist",
            dataType: 'json',
            data: {
                buildingId: buildingId,
                page: page,
                order: order
            },
            success: function (response) {
                $('.loader').hide();
                $("#priority_popup").hide();
                $('#priority_info').html('');
                $('#priority_info').html(response.content);

            }

        });
    } else {
        jAlert('No Building selected', 'Vision Work Orders');
    }
}

function priorityPagination(page) {
    $('#priority_page').val(page);
    var building_id = $('#building_id').val();
    showPriorityList(building_id);
}

function categoryPagination(page) {
    $('#category_page').val(page);
    var building_id = $('#building_id').val();
    showCategoryList(building_id);
}

function showPriorityScheduleList(pid, open, order) {
    var className = $('#open_div_' + pid).attr('class');
    var order = (typeof order === "undefined") ? "default" : order;
    $('.show-tr-schlist').each(function () {
        var eId = $(this).attr('id');
        var sId = eId.split('-');
        //alert(sId[1]);
        var runId = sId[1];
        if (runId != pid) {
            $('#open_div_' + runId).addClass("open_plus");
            $('#open_div_' + runId).removeClass("open_close");
            $('#show_priority_schedule_' + runId).html('');
            $(this).hide();
        }
    });
    if (className == 'open_plus' || open == 'open') {
        $('#open_div_' + pid).addClass("open_close");
        $('#open_div_' + pid).removeClass("open_plus");

    } else {
        $('#open_div_' + pid).addClass("open_plus");
        $('#open_div_' + pid).removeClass("open_close");
        $('#show_priority_schedule_' + pid).html('');
        $('#tr_schecdule-' + pid).hide();
        return false;
    }
    if (pid != '') {
        $('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + "category/showpriorityschedulelist",
            data: {
                pid: pid,
                order: order
            },
            success: function (response) {
                $('.loader').hide();
                $("#priority_popup").hide();
                $('#show_priority_schedule_' + pid).html(response);
                $('#tr_schecdule-' + pid).show();
                setTimeout(function () {
                    $('div#add-schedule-td').not(':first').remove();
                }, 1000);
            }

        });
    } else {
        jAlert('No Building selected', 'Vision Work Orders');

    }
}


function showEditSchedule1(sid) {
    $('.edit-schedule').hide();
    $('.show-schedule').show();
    $('#show_schedule_id_' + sid).hide();
    $('#edit_schedule_id_' + sid).show();
}

function hideEditSchedule(sid) {
    $('#edit_schedule_id_' + sid).hide();
    $('#show_schedule_id_' + sid).show();
}

function showScheduleForm() {
    $("#end-status-error").html("");
    $("#time-error").html("");
    $('#Time').removeClass('inputErr');
    $('#Time').val('');
    $("#priority_popup").show();
    //$('#add-schedule-td').show();
    $('#add-schedule-td_href').trigger('click');
}


function hideScheduleForm() {
    $("div.fancybox-close").trigger("click");
    $("#priority_popup").hide();
    $('#add-schedule-td').hide();
    setTimeout(function () {
        $('div#add-schedule-td').not(':first').remove();
    }, 1000);
}

function saveSchedule() {
    //$('#save_schedule').attr('disabled',true);
    var start_status = $('#start_status').val();
    var end_status = $('#end_status').val();
    var parent_email_status = $('#parent_sch_email_status').val();
    var Time = $('#Time').val();
    var length = $('#length').val();
    var access_days = $('#access_days').val();
    var status = $('#sch_status').val();
    var parent_status = $('#sch_status').val();
    var email_status = $('#sch_email_status').val();
    var start_time_active = $('#start_time_active').val();
    var end_time_active = $('#end_time_active').val();
    if ($('#all_day').is(':checked')) {
        var all_day = $('#all_day').val();
    } else {
        var all_day = '';
    }

    var dt = new Date("November 1, 2015 " + start_time_active);
    dt = dt.getTime();
    var dt2 = new Date("November 1, 2015 " + end_time_active);
    dt2 = dt2.getTime();
    if ((dt > dt2) && all_day == '') {
        jAlert('End Time must be greater then start time', 'Vision Work Orders');
        return false;
    }


    var valid_true = true;
    if (start_status == end_status) {
        $("#end-status-error").html("Status can not be same.");
        valid_true = false;
    } else {
        $("#end-status-error").html("");
    }
    if (Time == '' || Time == 0) {
        $("#time-error").html("Please enter the time.");
        $('#Time').addClass('inputErr');
        valid_true = false;
    } else {
        $("#time-error").html("");
        $('#Time').removeClass('inputErr');
    }

    if (Time == '' || Time == 0) {
        $("#time-error").html("Please enter the time.");
        $('#Time').addClass('inputErr');
        valid_true = false;
    } else {
        $("#time-error").html("");
        $('#Time').removeClass('inputErr');
    }

    if (Time == '' || Time == 0) {
        $("#time-error").html("Please enter the time.");
        $('#Time').addClass('inputErr');
        valid_true = false;
    } else {
        $("#time-error").html("");
        $('#Time').removeClass('inputErr');
    }

    if (parent_status == '0' && status == '1') {
        //jAlert('Priority Name must be active', 'Vision Work Orders');
        $("#sch_status_error").html("Priority Name must be active.");
        $('#sch_status_error').addClass('inputErr');
        return false;
    } else {
        $("#sch_status_error").html("");
        $('#sch_status_error').removeClass('inputErr');
    }


    if (parent_email_status == '0' && email_status == '1') {
        //jAlert('Priority Name E-Mail Alert must be active to sending the email', 'Vision Work Orders');
        $("#sch_email_status_error").html("Priority Name E-Mail Alert must be active to sending the email.");
        $('#sch_email_status_error').addClass('inputErr');
        return false;
    } else {
        $("#sch_email_status_error").html("");
        $('#sch_email_status_error').removeClass('inputErr');
    }


    if (valid_true == false) {
        $('#save_schedule').attr('disabled', false);
        return false;
    } else {
        $('.loader').show();
        priority_id = $('#priority_id').val();
        $.ajax({
            type: "POST",
            url: baseUrl + "category/saveschedule",
            dataType: 'json',
            data: {
                start_status: start_status,
                end_status: end_status,
                Time: Time,
                length: length,
                access_days: access_days,
                status: status,
                priority_id: priority_id,
                email_status: email_status,
                end_time_active: end_time_active,
                start_time_active: start_time_active,
                all_day_active: all_day
            },
            success: function (msg) {
                if (msg.status == 'error')
                {
                    $('.message').html(msg.message);
                    showMessage(msg.message, 0);
                    $('.loader').hide();

                } else if (msg.status == 'success')
                {
                    $('.message').html(msg.message);
                    showMessage(msg.message, 1);
                    showPriorityScheduleList(priority_id, 'open');
                    hideScheduleForm();
                }
            }

        });
    }

}


function editSchedule(sId) {


    var start_time_active = $('#edit_start_time_active').val();
    var end_time_active = $('#edit_end_time_active').val();

    if ($('#edit_all_day').is(':checked')) {
        var all_day = $('#edit_all_day').val();
    } else {
        var all_day = '';
    }
    var dt = new Date("November 1, 2015 " + start_time_active);
    dt = dt.getTime();
    var dt2 = new Date("November 1, 2015 " + end_time_active);
    dt2 = dt2.getTime();
    if ((dt > dt2) && all_day == '') {
        $("#end_time-error").html("End Time must be greater then start time.");
        $('#end_time-error').addClass('inputErr');
        //jAlert('End Time must be greater then start time', 'Vision Work Orders');
        return false;
    }
    var start_status = $('#edit_start_status').val();
    var end_status = $('#edit_end_status').val();

    var Time = $('#edit_time').val();
    var length = $('#edit_length').val();
    var access_days = $('#edit_access_days').val();
    var status = $('#edit_sch_status').val();
    var parent_status = $('#parent_status').val();
    var parent_email_status = $('#parent_email_status').val();
    var email_status = $('#edit_sch_email_status').val();
    var valid_true = true;
    if (start_status == end_status) {
        //jAlert('Start Status and End Status can not be same', 'Vision Work Orders');
        $("#edit_end-status-error").html("Status can not be same.");
        valid_true = false;
    }

    if (Time == '' || Time == 0) {
        //jAlert('Please enter the time', 'Vision Work Orders');
        $("#edit_time-error").html("Please enter the time.");
        $('#edit_time-error').addClass('inputErr');
        valid_true = false;
    } else {
        $('#edit_time-error').removeClass('inputErr');
    }

    if (parent_status == '0' && status == '1') {
        //jAlert('Priority Name must be active', 'Vision Work Orders');
        $("#edit_sch_status_error").html("Priority Name must be active.");
        $('#edit_sch_status_error').addClass('inputErr');
        return false;
    } else {
        $('#edit_sch_status_error').html('');
        $('#edit_sch_status_error').removeClass('inputErr');
    }

    if (parent_email_status == '0' && email_status == '1') {
        //jAlert('Priority Name E-Mail Alert must be active to sending the email', 'Vision Work Orders');
        $("#edit_sch_email_status_error").html("Priority Name E-Mail Alert must be active to sending the email.");
        $('#edit_sch_email_status_error').addClass('inputErr');
        return false;
    } else {
        $('#edit_sch_status_error').html('');
        $('#edit_sch_status_error').removeClass('inputErr');
    }

    if (valid_true == false) {
        return false;
    } else {
        $('.loader').show();
        priority_id = $('#priority_id').val();
        $.ajax({
            type: "POST",
            url: baseUrl + "category/editschedule",
            dataType: 'json',
            data: {
                start_status: start_status,
                end_status: end_status,
                Time: Time,
                length: length,
                access_days: access_days,
                status: status,
                id: sId,
                email_status: email_status,
                end_time_active: end_time_active,
                start_time_active: start_time_active,
                all_day_active: all_day

            },
            success: function (msg) {
                if (msg.status == 'error')
                {
                    $('.message').html(msg.message);
                    $('.loader').hide();

                } else if (msg.status == 'success')
                {
                    $('.message').html(msg.message);
                    showPriorityScheduleList(priority_id, 'open');
                    hideScheduleForm();
                }
            }

        });
    }

}

function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31
            && (charCode < 48 || charCode > 57)) {
        return false;
    } else
        return true;
}

function deleteSchedule(sid) {
    $('.loader').show();
    $.ajax({
        url: baseUrl + "category/isscheduledeletable",
        type: "post",
        datatype: 'json',
        data: {sid: sid},
        success: function (result) {
            $('.loader').hide();
            var data = $.parseJSON(result);

            if (parseInt(data.total) > 0) {
                //$('.message').html('<font color="red">Sorry! This schedule is already associated with work order.</font>');
                jAlert('Sorry! This schedule is already associated with work order', 'Vision Work Orders');
                //$('.message').show();
            } else {
                jConfirm('Are you sure, you want to delete the schedule?', 'Confirmation Dialog', function (r) {

                    if (r == true)
                    {
                        $('.loader').show();
                        priority_id = $('#priority_id').val();
                        $.ajax({
                            type: "POST",
                            url: baseUrl + "category/deleteschedule",
                            dataType: 'json',
                            data: {
                                id: sid
                            },
                            beforeSend: function () {
                                //$('.loader').show();
                            },
                            success: function (msg) {
                                $('.loader').hide();
                                if (msg.status == 'error')
                                {
                                    jAlert(msg.message, 'Vision Work Orders');
                                    return false;
                                } else if (msg.status == 'success')
                                {
                                    showPriorityScheduleList(priority_id, 'open');

                                }
                            }

                        });
                    }
                });

            }
        },
        error: function () {
            jAlert('There was an error', 'Vision Work Orders');
        }

    });


}


function showEditcategory(cid) {
    /*$('.edit-category').hide();
     $('.show-category').show();
     $('#show_category_id_'+cid).hide();*/
    CheckForSessionpop(baseUrl);
    $('.loader').show();
    var building_id = $('#building_id').val();
    $.ajax({
        type: "POST",
        url: baseUrl + "category/editcategory",
        data: {
            building_id: building_id,
            cid: cid,
        },
        success: function (msg) {
            //var height = $("#category_info").height();	
            //var tot_height = parseInt(parseInt(height)+700)
            //$("#category_popup").css('height',tot_height+'px');	 
            //$("#category_popup").show();
            $('.loader').hide();
            //$('#show_cat_tr').show();
            $("#show_cat_tr").html(msg);
            //$("#open_cat_"+cid).hide();
            //$("#close_cat_"+cid).show();	
            $('#show_cat_tr_href').trigger('click');
        },
        error: function () {
            setInterval(function () {
                jAlert('error', 'Vision Work Orders');
            }, 1100);

        }
    });
}

function hideEditcategory(cid) {
    $("div.fancybox-close").trigger("click");
    $('#edit_category_id_' + cid).hide();
    $('#show_category_id_' + cid).show();
}

function showCategroyFrom() {
    CheckForSessionpop(baseUrl);
    $('.loader').show();
    var building_id = $('#building_id').val();
    $.ajax({
        type: "POST",
        url: baseUrl + "category/addcategory",
        data: {
            actionType: "addNew",
            building_id: building_id,
        },
        beforeSend: function () {
        },
        success: function (msg) {
            var height = $("#category_info").height();
            var tot_height = parseInt(parseInt(height) + 700)
            $("#category_popup").css('height', tot_height + 'px');
            //$("#category_popup").show();
            $('.loader').hide();
            //$('#show_cat_tr').show();
            $("#show_cat_tr").html(msg);
            $('#show_cat_tr_href').trigger('click');
            $('#category_name').focus();

        },
        error: function () {
            setInterval(function () {
                jAlert('error', 'Vision Work Orders');
            }, 1100);

        }
    });
}

function cancelCategroyFrom() {
    $("div.fancybox-close").trigger("click");
    $("#show_cat_tr").hide();
    $("#category_popup").hide();
    $("#show_cat_tr").html('');
    $(".edit-category").hide();
    $(".open_plus").show();
    $(".open_close").hide();
    $(".edit-cat-td").html('');
}
function saveCategory() {
    parent.CheckForSessionpop(baseUrl);
    var catName = $('#category_name');


    var prioritySchedule = $('#priority');
    var isActive = $('#status');
    var building_id = $('#building_id').val();
    var global_template = $('#category_global_template').prop('value');
    /*********Select all options ************/
    $('#tenant_to_list option').prop('selected', true);
    $('#send_to_list option').prop('selected', true);
    $('#user_to_list option').prop('selected', true);
    var dataSet = new Object;
    dataSet.categoryName = catName.val();
    dataSet.prioritySchedule = prioritySchedule.val();
    dataSet.status = isActive.val();
    dataSet.building_id = building_id;
    dataSet.global_template = global_template;
    var include_exclude = '';
    var send_email = '';
    var account_user = '';
    if ($('#tenant_to_list').val() != '' && $('#tenant_to_list').val() != null)
        include_exclude = ($('#tenant_to_list').val()).join();
    dataSet.include_exclude = include_exclude;
    if ($('#send_to_list').val() != '' && $('#send_to_list').val() != null)
        send_email = ($('#send_to_list').val()).join();
    dataSet.send_email = send_email;

    if ($('#user_to_list').val() != '' && $('#user_to_list').val() != null)
        account_user = ($('#user_to_list').val()).join();
    dataSet.account_user = account_user;
    dataSet.visible_status = $('input[name="visible_status"]:checked').val();

    if (!catName.val()) {
        $('#category_name').addClass('inputErr');
        $('#name_error').html('Please enter name');
        $('#category_name').focus();
        $('#saveCat').attr('disabled', false);
        return false;
    } else {
        $('#name_error').html('');
        $('#category_name').removeClass('inputErr');
    }
    $('#saveCat').attr('disabled', true);
    $('.loader').show();
    $.ajax({
        url: baseUrl + "category/createcat",
        dataType: "json",
        type: "post",
        data: dataSet,
        beforeSend: function (xhr) {
            return true;
        },
        success: function (data) {
            $('.loader').hide();

            if (data == 3) {
                $('.loader').hide();
                $('#name_error').html('Name is already exist.');
                $('#category_name').focus();
                $('#saveCat').attr('disabled', false);
                return false;
            } else if (data == true) {
                $('.loader').hide();
                $('#name_error').text('');
                $('#saveCat').attr('disabled', false);

                showMessage("Category has been added successfully.", 1);
                window.location.reload();
                showCategoryList(building_id);
                cancelCategroyFrom();

            } else {
                $('.loader').hide();
                jAlert('We are unable to process you request this time. Please try later!', 'Vision Work Orders');
            }
            $('#add_cat_form').html('');
        }
    });
}


function editCategory(cid) {
    $('#tenant_to_list option').prop('selected', true);
    $('#send_to_list option').prop('selected', true);
    $('#user_to_list option').prop('selected', true);
    var include_exclude = '';
    var send_email = '';
    var account_user = '';
    if ($('#tenant_to_list').val() != '' && $('#tenant_to_list').val() != null)
        include_exclude = ($('#tenant_to_list').val()).join();

    if ($('#send_to_list').val() != '' && $('#send_to_list').val() != null)
        send_email = ($('#send_to_list').val()).join();

    if ($('#user_to_list').val() != '' && $('#user_to_list').val() != null)
        account_user = ($('#user_to_list').val()).join();

    var visible_status = $('input[name="visible_status"]:checked').val();
    var building_id = $('#building_id').val();
    var status = $('#status-' + cid).val();
    var prioritySchedule = $('#priority-' + cid).val();
    var global_template = $('#edit_category_global_template').val();

    $('.loader').show();
    $.ajax({
        type: "POST",
        url: baseUrl + "category/editcat",
        dataType: 'json',
        data: {
            include_exclude: include_exclude,
            send_email: send_email,
            visible_status: visible_status,
            account_user: account_user,
            status: status,
            prioritySchedule: prioritySchedule,
            cat_id: cid,
            global_template: global_template
        },
        success: function (msg) {
            if (msg == 3) {
                $('.loader').hide();
                //$('#name-error').text('Name is already exist.');
                jAlert('Name is already exist', 'Vision Work Orders');
            } else if (msg == true) {
                $('.loader').hide();
                $('#name-error').text('');


                showMessage("Category has been updated successfully.", 1);
                window.location.reload();
                //showCategoryList(building_id);
                cancelCategroyFrom();

            } else {
                $('.loader').hide();
                jAlert('We are unable to process you request this time. Please try later!', 'Vision Work Orders');
            }
            $('#add_cat_form').html('');
        }

    });

}
function showMessage(msg, chk) {
    // chk==1(success) chk==0(error)	
    if (chk == 1) {
        $(".message").html("<span class='success-txt'>" + msg + "</span>");
    } else {
        $(".message").html("<span class='error-txt'>" + msg + "</span>");
    }
    $(".message").show();
    setInterval(function () {
        $(".message").hide();
    }, 10000);
}


function deleteCategory(cid) {
    $('.loader').show();

    $.ajax({
        url: baseUrl + "category/iscategorydeletable",
        type: "post",
        datatype: 'json',
        data: {cid: cid},
        success: function (result) {
            $('.loader').hide();
            var data = $.parseJSON(result);
            if (parseInt(data.total) > 0) {
                jAlert('Sorry! This category is already associated with work order.', 'Vision Work Orders');
                //$('.message').html('<font color="red">Sorry! This category is already associated with work order.</font>');
                //$('.message').show();
                setTimeout(function () {
                    $(".message").hide('blind', {}, 500)
                }, 5000);
            } else {
                var check_delete = 'YES';
                //var con = confirm("Do you really want to delete category?");
                //var return_value = prompt("For Deleting Category, Enter Yes in Capital letters.","");
                jPrompt('For Deleting Category, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
                    if (return_value != null) {
                        if (check_delete === return_value) {
                            var building_id = $('#building_id').val();
                            $('.loader').show();
                            $.ajax({
                                type: "POST",
                                url: baseUrl + "category/deletecat",
                                dataType: 'json',
                                data: {
                                    cat_id: cid
                                },
                                beforeSend: function () {
                                },
                                success: function (msg) {
                                    if (msg) {
                                        $('.message').html('Category has been deleted successfully.');
                                        window.location.reload();
                                        showCategoryList(building_id);

                                        $('.loader').hide();
                                        $('#add_cat_form').html('');
                                    }
                                }

                            });
                        } else {
                            //$('.error-txt').html('You have entered wrong word.');
                            jAlert('You have entered wrong word.', 'Vision Work Orders');
                        }
                    }
                });
            }
        },
        error: function () {
            jAlert('There was an error', 'Vision Work Orders');
        }

    });
}

function recoverCategory(cId) {
    jConfirm('Do you really want to recover this category?', 'Confirmation Dialog', function (con) {
        if (con && cId != '' && cId != null) {
            window.location.href = baseUrl + "category/activatecategory/cId/" + cId;
        }
    });
}

function showCategoryList(buildingId) {
    var page = $('#category_page').val();
    var cn_order = $('#cn_order').val();
    if (buildingId != '') {
        $('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + "category/showcategorylist",
            dataType: 'json',
            data: {
                buildingId: buildingId,
                page: page,
                cn_order: cn_order
            },
            success: function (response) {
                $('.loader').hide();
                $("#category_popup").hide();
                $('#category_info').html(response.content);

            }

        });
    } else {
        jAlert('No Building selected', 'Vision Work Orders');
    }
}

/***** move single item *****/
function move_list_items(sourceid, destinationid)
{
    var select_item = $("#" + sourceid + "  option:selected").val();
    if (select_item == '' || select_item == null || select_item == undefined) {
        jAlert('Please select item', 'Vision Work Orders');
        return false;
    } else {
        $("#" + sourceid + "  option:selected").appendTo("#" + destinationid);
    }
}

/***** move all item *****/
function move_list_items_all(sourceid, destinationid)
{
    $("#" + sourceid + " option").appendTo("#" + destinationid);
}

function selectAllDay() {
    if ($('#all_day').is(':checked')) {
        $('.time_active').prop('disabled', true);
    } else {
        $('.time_active').prop("disabled", false);
        ;
    }
}


function showPriorityTemplate(url) {
    CheckForSessionpop(baseUrl);
    $('a[href="#showPriorityTemplate"]').fancybox({
        type: 'iframe',
        href: url,
        width: 650,
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
function cancelPriority() {
    parent.jQuery.fancybox.close();

}

function  addPriorityTemplate() {
    $('.loader').show();
    $('#save_priority').attr('disabled', true);
    var checkComp = false;
    var priorityName = $('#priority_name').val();
    var building_id = $('#building_id').val();
    //alert('hello'+priorityName);
    //alert('cname'+cname);
    if (priorityName != '') {
        $.ajax({
            type: "POST",
            url: baseUrl + 'category/checkpriority',
            data: {priorityName: priorityName,
                building_id: building_id},
            success: function (msg) {

                $('.loader').hide();
                if (msg == 'true') {
                    $("#save_priority").attr('disabled', false);
                    $("#priority-name-error").html("Priority Name already in use.");
                    $('#priority_name').addClass('inputErr');
                    $('#priority_name').focus();
                    return false;

                } else {
                    $('#priority-name-error').html("");
                    $('#priority_name').removeClass('inputErr');
                    savePriorityTemplate();
                }
            }
        });

    } else {
        $('.loader').hide();
        $("#save_priority").attr('disabled', false);
        $("#priority-name-error").html("Please enter priority name");
        $('#priority_name').addClass('inputErr');
        $('#priority_name').focus();
        return false;
    }
}


function savePriorityTemplate() {
    $('#save_priority').attr('disabled', true);
    var priorityName = $('#priority_name').val();
    var over_write_priority = $('#over_write_priority').val();
    var existing_priority = $('#existing_priority').val();
    var existing_priority_name = $('#existing_priority option:selected').text();
    var new_priority_name = $('#new_priority_name').val();
    var pid = $('#priority_name').find('option:selected');
    pid = pid.attr("data-rel");
    var building_id = $('#building_id').val();
    var valid_true = true;
    if (priorityName == '') {
        $("#priority-name-error").html("Please enter priority name");
        $('#priority_name').addClass('inputErr');
        valid_true = false;
    } else {
        $("#priority-name-error").html("");
        $('#priority_name').removeClass('inputErr');
    }
    if (over_write_priority == 0) {
        if (new_priority_name == '') {
            $("#priority-description-error").html("Please enter priority description");
            $('#priority_description').addClass('inputErr');
            valid_true = false;
        } else {
            $("#priority-description-error").html("");
            $('#priority_description').removeClass('inputErr');
        }
    } else {
        if (existing_priority == '') {
            $("#priority-description-error").html("Please enter priority description");
            $('#priority_description').addClass('inputErr');
            valid_true = false;
        } else {
            $("#priority-description-error").html("");
            $('#priority_description').removeClass('inputErr');
        }
    }


    if (valid_true == false) {
        $('#save_priority').attr('disabled', false);
        return false;
    } else {
        $('.loader').show();
        building_id = $('#building_id').val();
        $.ajax({
            type: "POST",
            url: baseUrl + "category/saveprioritytemplate",
            dataType: 'json',
            data: {
                priorityName: priorityName,
                over_write_priority: over_write_priority,
                existing_priority: existing_priority,
                new_priority_name: new_priority_name,
                pid: pid,
                building_id: building_id,
                existing_priority_name: existing_priority_name
            },
            success: function (msg) {
                /* if(msg.status=='error')
                 {
                 //$('.message').html(msg.message);
                 showMessage(msg.message, 0)
                 hidePriorityForm();
                 $('.loader').hide();	
                 
                 }else if(msg.status=='success')
                 {
                 //$('.message').html(msg.message);
                 showMessage(msg.message, 1)
                 showPriorityList(building_id);					
                 hidePriorityForm();
                 } */

                parent.showPriorityList(building_id);
                parent.showCategoryList(building_id);
                cancelPriority();
            }

        });
    }
}


function showEditPriority(pid) {
    /*$('.edit-category').hide();
     $('.show-category').show();
     $('#show_category_id_'+cid).hide();*/
    CheckForSessionpop(baseUrl);
    $('.loader').show();
    $.ajax({
        type: "POST",
        url: baseUrl + "category/showeditpriority",
        data: {pid: pid,
        },
        success: function (msg) {
            $('.loader').hide();
            $("#edit-priority-td").html(msg);
            $('#edit-priority-td_href').trigger('click');
        },
        error: function () {
            setInterval(function () {
                jAlert('error', 'Vision Work Orders');
            }, 1100);

        }
    });
}


function showEditSchedule(sid) {
    /*$('.edit-category').hide();
     $('.show-category').show();
     $('#show_category_id_'+cid).hide();*/
    CheckForSessionpop(baseUrl);
    $('.loader').show();
    $.ajax({
        type: "POST",
        url: baseUrl + "category/showeditschedule",
        data: {sid: sid,
        },
        success: function (msg) {
            $('.loader').hide();
            $("#edit-priority-td").html(msg);
            $('#edit-priority-td_href').trigger('click');
        },
        error: function () {
            setInterval(function () {
                jAlert('error', 'Vision Work Orders');
            }, 1100);

        }
    });
}

