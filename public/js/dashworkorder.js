$(function () {
    $(".modalbox").fancybox({'openEffect': 'none', fitToView: true});
});

(function ($) {

    $.fn.toggleClick = function () {
        var functions = arguments;
        return this.click(function () {
            var iteration = $(this).data('iteration') || 0;
            functions[iteration].apply(this, arguments);
            iteration = (iteration + 1) % functions.length;
            $(this).data('iteration', iteration);
        });
    };

})(jQuery);
function showWorkOrder(woId) {

    if (document.getElementById('workrequest_' + woId).style.display != "none") {
        document.getElementById('workrequest_' + woId).style.display = "none";
        $('#plus_' + woId).addClass("fa-plus");
        $('#plus_' + woId).removeClass("fa-minus");
        location.hash = '';
    } else {
        $('.loader').show();
        $('.plus_min_icon').removeClass("fa-minus");
        $('.plus_min_icon').addClass("fa-plus");
        $('.tr-order').hide();
        $('.order-detail').html('');
        $('#plus_' + woId).removeClass("fa-plus");
        $('#plus_' + woId).addClass("fa-minus"); 
        $.ajax({
            url: baseUrl + "dashboard/orderdetail/woId/" + woId,
            success: function (content) {
                //$('.loader').hide();
                $('#order_content_' + woId).html(content);
                $('#workrequest_' + woId).show();
                $('.loader').hide();
                location.hash = '#' + woId;
            }

        });
    }
}

function showByStatus(status) {
    document.search_form.submit();
}

function showStatus() {

    $('.multicheckdd').prop('checked', false);
    document.search_mini_form.submit();
}
function showStatusValidation() {
    //var status=''	
    //$(".middle_btn").find('.multicheckdd:checked').each(function() {
    // status=1;
    //});
    //if(status!='') {
    //	document.search_mini_form.submit();
    //} else { 
    $('.my_middle_btn #search_wo').val('');
    document.search_mini_form.submit();
    //}
}

function resetStatus() {
    document.getElementById("search_mini_form").reset();
    $('.multicheckdd').attr('checked', false);
    $('input[name = "search_wo"]').val('');
    document.search_mini_form.submit();
}

function showStatusPop() {
    $('#search_wo').val('');
    $('#category_name').val('');
    $('#tenant_name').val('');
    $('#from_date').val('');
    $('#to_date').val('');
    document.search_form.submit();
}

function cancelShowStatus() {
    document.getElementById("search_form").reset();
    $('.multicheckdd').prop('checked', false);
    $('#search_wo').val('');
    $('#category_name').val('');
    $('#tenant_name').val('');
    $('#from_date').val('');
    $('#to_date').val('');
    document.search_form.submit();
}
$(document).ready(function () {
    $(".uncheckstatus").click(function () {

        if (this.checked) {
            $(this).closest("ul").addClass("spchkboxul");
        } else {
            $(this).closest("ul").removeClass("spchkboxul");
        }

    });
});

$(function () {
    $("#from_date").datepicker({
        dateFormat: 'mm/dd/yy',
        changeMonth: true,
        changeYear: true
    });
    $("#to_date").datepicker({
        dateFormat: 'mm/dd/yy',
        changeMonth: true,
        changeYear: true
    });
    $search_show_form = $('#search_show_form');
    $("#show_hide").toggleClick(function () {
        $search_show_form.animate({'right': '-2px'}, 1000);

    }, function () {
        $search_show_form.animate({'right': '-' + $search_show_form.outerWidth()}, 1000);
    });

});

function updateWorkorder(woId) {
    var order_status = $('#order_status_' + woId).val();
    var internal_note = $('#internal_note_' + woId).val();
    var exist_schedule = $('#exist_schedule_' + woId).val();
    var priority = $('#priority_' + woId).val();
    var update_flag = 'false';
    var time = '';
    var slength = '';
    var insert_schedule = '0';
    var eschedule = exist_schedule.split(',');
    var current_status = $('#current_wstatus_' + woId).val();
    if (current_status > 1 && order_status == 1) {
        $('#wstatus_error_' + woId).html("Work order cann't be assign as new.");
        return false;
    } else if (current_status == order_status) {
        $('#wstatus_error_' + woId).html("Work order cann't be assign as same status again.");
        return false;
    } else {
        $('#wstatus_error_' + woId).html('');
    }

    if (eschedule.indexOf(order_status) == '-1') {
        $('#time_length_div_' + woId).show();
        time = $('#Time_' + woId).val();
        slength = $('#length_' + woId).val();
        if (time == '') {
            //alert('Please enter Time');
            $('#schedule_error').html('Please enter Time');
        } else {
            insert_schedule = 1;
            update_flag = 'true';
        }
    } else
    {
        update_flag = 'true';
    }
    if (order_status != '' && update_flag == 'true') {
        $('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + 'dashboard/updateajaxorder',
            data: {order_status: order_status, current_status: current_status, internal_note: internal_note, woId: woId, time: time, slength: slength, insert_schedule: insert_schedule, priority: priority},
            success: function (msg) {
                $('.loader').hide();
                if (msg == 'true') {
                    $('.workorder_success').html("Work order update successfully.");
                    location.reload();
                } else {
                    $('.workorder_error').html("Error occurred during work order update.");
                }
            }
        });

    }
}

function ValidateForm() {

   // var order_status = $('#order_status').val();
    var order_status = $('#order_status').attr('selected','selected').val();
    var exist_schedule = $('#exist_schedule').val();
    var priority = $('#priority').val();

    var update_flag = 'false';
    var time = '';
    var slength = '';
    var current_status = $('#current_wstatus').val();
    if (current_status > 1 && order_status == 1) {
        $('#wstatus_error').html("Work order cann't be assign as new.");
        return false;
    } else if (current_status == order_status) {
        $('#wstatus_error').html("Work order cann't be assign as same status again.");
        return false;
    } else {
        $('#wstatus_error').html('');
    }
    var insert_schedule = '0';
    var eschedule = exist_schedule.split(',');
    if (eschedule.indexOf(order_status) == '-1') {
        $('#time_length_div').show();
        time = $('#Time').val();
        slength = $('#length').val();
        if (time == '') {
            //alert('Please enter Time');
            $('#schedule_error').html('Please enter Time');
        } else {
            //insert_schedule=1;
            $('#insert_schedule').val('1');
            $('#schedule_error').html('');
            update_flag = 'true';
        }
    } else
    {
        update_flag = 'true';
    }
    if (order_status != '' && update_flag == 'true') {
        
        
        time = $('#Time').val();
        slength = $('#length').val();
        //order_status = $('#order_status').val(); //code commented by @dvk on 11 Dec 2023
        priority = $('#priority').val();
        work_order_id = $('#work_order_id').val();
        
        //document.worequestform.submit();
        $('.loader').show();
                $.ajax({
                    type: "POST",
                    url: baseUrl + 'dashboard/updateorder',
                    data: {work_order_id:work_order_id, priority:priority, length: slength, Time: time,order_status:order_status,current_wstatus:current_status},
                    success: function (msg) {
                        $('.loader').hide();
                        $.ajax({    
                                    type: "POST",
                                    url: baseUrl + 'dashboard/reloadworkorder/',
                                    data: {},
                                    success: function (response) {
                                        $('.loader').hide();               
                                        $("#reloadworkorder").html(response);
                                      }
                             });
                        //console.log(msg);
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
$(function () {
    // building select menu
    $('<select />').appendTo('.tabmenu');



    $('.tabmenu ul li a').each(function () {
        var target = $(this);
        if (target.attr('class') == 'resp-tab-active') {
            $('<option />', {
                'selected': 'selected',
                'value': target.attr('href'),
                'text': target.text()
            }).appendTo('.tabmenu select');
        } else {
            $('<option />', {
                'value': target.attr('href'),
                'text': target.text()
            }).appendTo('.tabmenu select');
        }
    });

    // on clicking on link
    $('.tabmenu select').on('change', function () {
        window.location = $(this).find('option:selected').val();
    });
});

// show and hide sub menu
$(function () {
    $('.tabmenu ul li').hover(
            function () {
                //show its submenu
                $('ul', this).slideDown(150);
            },
            function () {
                //hide its submenu
                $('ul', this).slideUp(150);
            }
    );
});


function reassignCategory(woId) {
    if (woId != '') {
        var curr_cat = $('#curr_cat_' + woId).val();
        var change_cat = $('#assign_category_' + woId).val();
        if (change_cat != '') {
            if (change_cat != curr_cat) {
                $('.loader').show();
                $.ajax({
                    type: "POST",
                    url: baseUrl + 'dashboard/changecat',
                    data: {woId: woId, curr_cat: curr_cat, change_cat: change_cat},
                    success: function (msg) {
                        $('.loader').hide();
                        if (msg == 'true') {
                            $('.workorder_success').html("Category reassign successfully.");
                            //location.reload();
                                
                        } else {
                            $('.loader').hide();
                            $('#assign_error_' + woId).html("Error occurred during reassign category.");
                        }
                    }
                });
            } else {
                $('#assign_error_' + woId).html('Reassign category should be different from current category.');
            }
        } else {
            $('#assign_error_' + woId).html('Select Category for Reassign');
        }
    } else {
        $('#assign_error_' + woId).html('Error Occurred');
    }
}

function showCatLog(woId) {
    $('#hide_hist_' + woId).show();
    $('#show_hist_' + woId).hide();
    $('#catlog_' + woId).show(100);
}

function hideCatLog(woId) {
    $('#hide_hist_' + woId).hide();
    $('#show_hist_' + woId).show();
    $('#catlog_' + woId).hide();
}

function checkSchedule(chk, wo_id) {
    if (chk == "workorder") {
        var order_status = $('#order_status_' + wo_id).val();
        var exist_schedule = $('#exist_schedule_' + wo_id).val();
        if (exist_schedule == undefined) {
        }
        var insert_schedule = '0';
        var eschedule = exist_schedule.split(',');
        if (eschedule.indexOf(order_status) == '-1') {
            $('#time_length_div_' + wo_id).show();
            time = $('#Time_' + wo_id).val();
            slength = $('#length_' + wo_id).val();
            if (time == '') {
                //alert('Please enter Time');
                $('#time_length_div_' + wo_id + ' #schedule_error').html('Please enter Time');
            } else {
                //insert_schedule=1;
                //$('#insert_schedule').val('1');
                $('#time_length_div_' + wo_id + ' #schedule_error').html('');
                //update_flag='true';
            }
        } else
        {
            $('#time_length_div_' + wo_id).hide();
            //update_flag='true';
        }


    } else {
        var order_status = $('#order_status').val();
        var exist_schedule = $('#exist_schedule').val();
        var insert_schedule = '0';
        var eschedule = exist_schedule.split(',');
        if (eschedule.indexOf(order_status) == '-1') {
            $('#time_length_div').show();
            time = $('#Time').val();
            slength = $('#length').val();
            if (time == '') {
                //alert('Please enter Time');
                $('#schedule_error').html('Please enter Time');
            } else {
                //insert_schedule=1;
                $('#insert_schedule').val('1');
                $('#schedule_error').html('');
                //update_flag='true';
            }
        } else
        {
            $('#time_length_div').hide();
            //update_flag='true';
        }

    }
}
$(function () {
    $('#ms,#ms2').change(function () {
        console.log($(this).val());
    }).multipleSelect({
        width: '100%'
    });
});


function cancelWoDesc() {
    $("div.fancybox-close").trigger("click");
    //$('#edit_wo_form').hide();
    $('#wo_desc').html('');
    $('#wo_desc').hide();
    $('.fade_default_opt').hide();
}


function historyoflabor(bId, whId) {
    $('.loader').show();
    $.ajax({
        url: baseUrl + 'complete/historyoflabor',
        type: 'post',
        data: {
            bId: bId,
            whId: whId
        },
        success: function (data) {
            $("#batch_error").html("Work order added successfully");
            $('.loader').hide();
            $("#add_new_labor_div").html(data);
            $('#add_new_labor_div_href').trigger('click');
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }
    });
}

function historyofBuilding(bId, whId) {
    $('.loader').show();
    $.ajax({
        url: baseUrl + 'complete/historyofbuilding',
        type: 'post',
        data: {
            bId: bId,
            whId: whId
        },
        success: function (data) {
            $("#batch_error").html("Work order added successfully");
            $('.loader').hide();
            $("#add_history_building_div").html(data);
            $('#add_history_building_div_href').trigger('click');
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }
    });
}

function historyofMaterial(bId, whId) {
    $('.loader').show();
    $.ajax({
        url: baseUrl + 'complete/historyofmaterial',
        type: 'post',
        data: {
            bId: bId,
            whId: whId,
        },
        success: function (data) {
            $("#batch_error").html("Work order added successfully");
            $('.loader').hide();
            $("#add_history_material_div").html(data);
            $('#add_history_material_div_href').trigger('click');
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }
    });
}

function historyofoutside(bId, whId) {
    $('.loader').show();
    $.ajax({
        url: baseUrl + 'complete/historyofoutside',
        type: 'post',
        data: {
            bId: bId,
            whId: whId,
        },
        success: function (data) {
            $("#batch_error").html("Work order added successfully");
            $('.loader').hide();
            $("#add_history_outside_div").html(data);
            $('#add_history_outside_div_href').trigger('click');
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }
    });
}

function historyofnotes(bId, whId) {
    $('.loader').show();
    $.ajax({
        url: baseUrl + 'complete/historyofnotes',
        type: 'post',
        data: {
            bId: bId,
            whId: whId
        },
        success: function (data) {
            $("#batch_error").html("Work order added successfully");
            $('.loader').hide();
            $("#add_history_notes_div").html(data);
            $('#add_history_notes_div_href').trigger('click');
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }
    });
}


function hostoryofworkorder(bId, woId, current_value, change_value) {
    $('.loader').show();
    $.ajax({
        url: baseUrl + 'complete/historyofworkorder',
        type: 'post',
        data: {
            bId: bId,
            woId: woId,
            current_value: current_value,
            change_value: change_value
        },
        success: function (data) {
            $('.loader').hide();
            $('#add_histroy_workorder_div').html(data);
            $('#add_histroy_workorder_div_href').trigger('click');
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }
    });
}


function cancelLaborForm() {
    $("div.fancybox-close").trigger("click");
    $('#labor_form').html('');
    $('#labor_form').hide();
    $('.loader').hide();
    $('.fade_default_opt').hide();
    //deleteTempLabor();

}

function acknowledgeWorkorder(url) {
  // alert("sanjay");
    $.ajax({
        url: url,
        type: "post",
        data: {url: url},
        success: function (content) {
            //location.reload();
             $('.loader').show(); 
             $.ajax({    
                        type: "POST",
                        url: baseUrl + 'dashboard/reloadworkorder/',
                        data: {},
                        success: function (response) {
                            $('.loader').hide();               
                            $("#reloadworkorder").html(response);
                          }
                 });
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error 11');
        }

    });


}






function showNoteForm(woId) {

    $('.loader').show();
    $.ajax({
        url: baseUrl + "dashboard/addnote",
        type: "post",
        datatype: 'json',
        data: {woId: woId},
        success: function (data) {
            $('.loader').hide();
            $('#note_form').html('');
            //$('#edit_wo_form').show();
            $('#note_form').html(data);
            $('#note_form_href').trigger('click');
            //$('#note_form').show();
            //$('.fade_default_opt').show();
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }

    });

}


function saveNote(woId) {
    var notify_tenant = 0;
    var notify_account_user = 0;
    if ($('#notify_tenant').is(":checked")) {
        notify_tenant = 1;
    }
    if ($('#notify_account_user').is(":checked")) {
        notify_account_user = 1;
    }

    $('#save_note').attr('disabled', 'disabled');
    var note_date = $('#note_date').val();
    var internal = $('#internal').val();
    var note = $('#note').val();

    var submit_flag = true;

    if (note_date == '') {
        $('#cwerr_date').html('Date can not be blank.');
        submit_flag = false;
    } else {
        $('#cwerr_date').html('');
    }

    if (note == '') {
        $('#cwerr_note').html('Note can not be blank.');
        submit_flag = false;
    } else {
        $('#cwerr_note').html('');
    }



    if (submit_flag == false) {
        $('#save_note').attr('disabled', false);
        return false;
    } else {
        $('.loader').show();
        $.ajax({
            url: baseUrl + "complete/savenote",
            type: "post",
            datatype: 'json',
            data: {
                note_date: note_date, internal: internal, note: note,
                woId: woId, notify_account_user: notify_account_user, notify_tenant: notify_tenant
            },
            success: function (data) {
                //$('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('#success_msg').html(content.msg);
                    //location.reload();
                    $.ajax({    
                                type: "POST",
                                url: baseUrl + 'dashboard/reloadworkorder/',
                                data: {},
                                success: function (response) {
                                    //alert(response);
                                    parent.jQuery.fancybox.close();
                                    $("#reloadworkorder").html(response);
                                    $('.loader').hide(); 
                                    

                                }
                         });
                    // reloadPage('#note_tab');
                } else {
                    //alert('Error occurred');
                    $('#error_msg').html(content.msg);
                    $('#save_note').attr('disabled', false);
                }
            },
            error: function () {
                $('.loader').hide();
                alert('There was an error');
            }

        });
    }

}


function FillPredefinedNotes(sel) {

    if (sel.options[sel.selectedIndex].value != '') {
        var notes = sel.options[sel.selectedIndex].text;
        $('#internal_note').val(notes);
        $('#note').val(notes);
        $('#edit_note').val(notes);
    } else {
        $('#internal_note').val('');
        $('#note').val('');
        $('#edit_note').val(notes);
    }
}


function showEditNote(wnId) {
    $('.loader').show();
    $.ajax({
        url: baseUrl + "dashboard/editnote",
        type: "post",
        datatype: 'json',
        data: {wnId: wnId},
        success: function (data) {
            $('.loader').hide();
            //$('#edit_wo_form').show();
            $('#edit_note_form').html('');
            $('#edit_note_form').html(data);
            $('#edit_note_form_href').trigger('click');
            //$('#note_form').show();
            //$('.fade_default_opt').show();
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }

    });
}

function cancelNote() {
    $("div.fancybox-close").trigger("click");
    $('#note_form').html('');
    $('#edit_note_form').html('');
    $('#note_form').hide();
    $('.fade_default_opt').hide();
}

function updateNote(wnId) {
    $('#save_note').attr('disabled', 'disabled');
    var note_date = $('#edit_note_date').val();
    var internal = $('#edit_internal').val();
    var note = $('#edit_note').val();
    var woId = $('#edit_woId').val();

    var submit_flag = true;

    if (note_date == '') {
        $('#edit_cwerr_date').html('Date can not be blank.');
        submit_flag = false;
    } else {
        $('#edit_cwerr_date').html('');
    }

    if (note == '') {
        $('#edit_cwerr_note').html('Note can not be blank.');
        submit_flag = false;
    } else {
        $('#edit_cwerr_note').html('');
    }



    if (submit_flag == false) {
        $('#save_note').attr('disabled', false);
        return false;
    } else {
        //$('.loader').show();
        $.ajax({
            url: baseUrl + "complete/savenote",
            type: "post",
            datatype: 'json',
            data: {
                note_date: note_date, internal: internal, note: note,
                wnId: wnId, woId: woId
            },
            success: function (data) {
                // $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('#success_msg').html(content.msg);
                    location.reload();
                    //reloadPage('#note_tab');
                } else {
                    //alert('Error occurred');
                    $('#error_msg').html(content.msg);
                    $('#save_note').attr('disabled', false);
                }
            },
            error: function () {
                // $('.loader').hide();
                alert('There was an error');
            }

        });
    }

}


function deleteNote(wnId, woId) {
    var check_delete = 'YES';
    jPrompt('For Deleting Note, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {
            if (check_delete === return_value) {
                $.ajax({
                    type: "POST",
                    url: baseUrl + "complete/deletenote",
                    dataType: 'json',
                    data: {
                        wnId: wnId,
                        woId: woId
                    },
                    success: function (msg) {
                        $('.loader').hide();
                        if (msg == true) {
                            $('#cw_success').html('Note has been deleted successfully.');
                            location.reload();
                            // reloadPage();
                        } else {
                            $('#cw_error').html('Error Occurred during deletion of Note.');
                        }
                    }

                });
            } else {
                //alert('You have entered wrong word.');
                jAlert('You have entered wrong word', 'Vision Work Orders');
            }
        }
    });
}



