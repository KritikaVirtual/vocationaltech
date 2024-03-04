$(function () {
    $(".modalbox").fancybox({'openEffect': 'none', fitToView: true});
    
    $(".loader").ajaxStart(function(){
        //$(this).show();
    }).ajaxComplete(function(){
        //$(this).hide();
    });
	
	
});

function createConRoom(url) {
	
	
    CheckForSessionpop(baseUrl);
    $('a[href="#CreateNewCon"]').fancybox({
        type: 'iframe',
        href: url,
        width: 700,
        height: 600,
        'beforeClose': function () {
            $('.loader').hide();
        },
        'afterLoad': function () {
            $.fancybox.hideLoading();
            $('.loader').show();
            setInterval(function () {
                $('.loader').hide();
            }, 500);
        }
    });

}

function createMultiConRoom(url)
{
    CheckForSessionpop(baseUrl);
    $('a[href="#CreateNewMultiCon"]').fancybox({
        type: 'iframe',
        href: url,
        width: 700,
        height: 600,
        'beforeClose': function () {
            $('.loader').hide();
        },
        'afterLoad': function () {
            $.fancybox.hideLoading();
            $('.loader').show();
            setInterval(function () {
                $('.loader').hide();
            }, 500);
        }
    });
}

function addNewSchedule(url)
{
    CheckForSessionpop(baseUrl);
    $('a[href="#AddNewSchedule"]').fancybox({
        type: 'iframe',
        href: url,
        width: 680,
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

function EditCSchedule(url)
{
    CheckForSessionpop(baseUrl);
    $('a[href="#EditCSchedule"]').fancybox({
        type: 'iframe',
        href: url,
        width: 680,
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

function cancelConRoom()
{
    parent.jQuery.fancybox.close();
}

function searchCRoom() {
    // $('.loader').show();
    var search_by = $.trim($('#search_by').val());
    var search_value = $.trim($('#search_value').val());
    var building_id = $('#building_id').val();
    showCRoomList(building_id, search_by, search_value);

}
function searchCSchedule() {
    // $('.loader').show();
    var search_by = $.trim($('#search_by_sch').val());
    var search_value = $.trim($('#search_value_sch').val());
    var building_id = $('#building_id').val();
    //console.log("search"+search_value);
    showCScheduleList(building_id, search_by, search_value);

}

function checkCRoom(id) {
    parent.CheckForSessionpop(baseUrl);
    var room_name = $.trim($('#room_name').val());
    var building_id = $.trim($('#building_id').val());
    if(room_name !='') {
        $.ajax({
                type: "POST",
                url: baseUrl + 'conference/checkcroom',
                data: {room_name: room_name,bid:building_id},
                beforeSend: function () {
                    //$('.loader').show();
                },
                success: function (msg) {
                    $('.loader').hide();
                    if (msg != true) {
                        $('#room-name-error').html("");
                        if(id == 'Single') {
                            validateConRoom();
                        } else if(id == 'Multi') {
                            validateMultiConRoom();
                        } 
                    } else {
                        
                        $('#room-name-error').html("Conference Name already in use.");
                        $('#room_name').focus();
                        return false;
                    }
                }
            });
    } else {
        $('#room-name-error').html('Schedule Name Required');
        $('#room_name').focus();
    }
}

function checkEditCRoom(id) {
    parent.CheckForSessionpop(baseUrl);
    var room_name = $.trim($('#room_name').val());
    var cid = $.trim($('#cid').val());
    var bid = $.trim($('#build_id').val());    
    
    if(room_name !='') {
        $.ajax({
                type: "POST",
                url: baseUrl + 'conference/checkeditcroom',
                data: {room_name: room_name,cid:cid,bid:bid},
                beforeSend: function () {
                    //$('.loader').show();
                },
                success: function (msg) {
                   // return false;
                    $('.loader').hide();
                    if (msg != true) {
                        $('#room-name-error').html("");
                        if(id == 'Single') {
                        updateCRoom();
                    } else if(id == 'Multi') {
                        validateMultiConRoom();
                    } 
                    } else {
                        
                        $('#room-name-error').html("Conference Name already in use.");
                        $('#room_name').focus();
                        return false;
                    }
                }
            });
    } else {
        $('#room-name-error').html('Schedule Name Required');
        $('#room_name').focus();
    }
}
function validateConRoom()
{
    var room_name = $.trim($('#room_name').val());
    var location = $.trim($('#location').val());
    var schedule_name = $.trim($('#schedule_name').val());
    var building_id = $.trim($('#building_id').val());
    
    
    /* Recurrence */
    
    if($('#rec_building_users').is(':checked')){
        var rec_building_users = 1;
    }else{
        var rec_building_users = 0;
    }
    
    if($('#rec_tenant_admin').is(':checked')){
        var rec_tenant_admin = 1;
    }else{
        var rec_tenant_admin = 0;
    }
    
    if($('#rec_tenant_users').is(':checked')){
        var rec_tenant_users = 1;
    }else{
        var rec_tenant_users = 0; 
    }
    /* End Recurrence*/
    
    var tenant_admin = 1;
    if ($('#tenant_admin').is(':checked')){
        tenant_admin = 1;
    } else {
        tenant_admin = 0;
    }
    var tenant_user = 0;
    if ($('#tenant_user').is(':checked')) {
        tenant_user = 1;
    } else {
        tenant_user = 0;
    }
    var auto_billing = 0;
    if ($('#auto_billing').is(':checked')) {
        auto_billing = 1;
    } else {
        auto_billing = 0;
    }
    var status = $.trim($('#status').val());
    var isValid = true;
    var isFocus = false;

    if (room_name == '') {
        $('#room-name-error').html('Room Name Required');
        $('#room_name').focus();
        isValid = false;
        isFocus = true;
    } else {
        $('#room-name-error').html('');
    }

    if (location == '') {
        $('#location-error').html('Location Required');
        isValid = false;
        if (!isFocus) {
            $('#location').focus();
            isFocus = true;
        }
    } else {
        $('#location-error').html('');
    }
    if (schedule_name == '') {
        $('#schedule-error').html('Availability Schedule Required');
        isValid = false;
        if (!isFocus) {
            $('#schedule_name').focus();
            isFocus = true;
        }
    } else {
        $('#schedule-error').html('');
    }

    var rate_sch = new Array();
    var i = 0;
    $(".plan_status").each(function () {
        if ($(this).is(':checked')) {
            var plan_id = $(this).val();
            var cost_id = $('#cost_' + plan_id).val();
            var min_val = $('#min_' + plan_id).val();
            var max_val = $('#max_' + plan_id).val();
            rate_sch[i] = {plan: plan_id, cost: cost_id, min: min_val, max: max_val}
            i++;
        }
    });
    
    if (i == 0) {
        $('#rate-error').html('Rate Schedule Required');
        isValid = false;
        if (!isFocus) {
            $(".cost_max:eq(0)").focus();
            isFocus = true;
        }
    } else {        
        var rCount = rate_sch.length;
        for (var j = 0; j < rCount; j++) {
            if (rate_sch[j].min == '') {
                $('#rate-error').html('Minimum must be greter than 0');
                if (!isFocus) {
                    $("#min_" + rate_sch[j].plan).focus();
                    isFocus = true;
                }
                isValid = false;

            }
            if(rate_sch[j].cost == ''){
                $('#rate-error').html('Please Enter The Price Plan');
                if (!isFocus) {
                    $("#cost_" + rate_sch[j].plan).focus();
                    isFocus = true;
                }
                isValid = false;
            }
            if(rate_sch[j].max == ''){
                $('#rate-error').html('Maximum must be greter than Minimum');
                if (!isFocus) {
                    $("#max_" + rate_sch[j].plan).focus();
                    isFocus = true;
                }
                isValid = false;
            }
            if(rate_sch[j].max != '') {
                if (parseInt(rate_sch[j].min) > parseInt(rate_sch[j].max)) {
                    $('#rate-error').html('Maximum must be greter than Minimum');
                    $("#min_" + rate_sch[j].plan).focus();
                    isFocus = true;
                    isValid = false;
                }
            } else {
                //$('#rate-error').html('');
            }
        }
        if(isValid){
            $('#rate-error').html('');
        }

    }

    var design_file = new Array();
    var design_file1 = '';
    var i = 0;
	var images_error= false;
    var userData = new FormData();
    $('.design_file').each(function () {
		
        design_file = $(this).prop('files')[0];
        var flag_val = escape($(this).attr('flag'));
		var filename=$(this).val();
		var Extension = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
		if (Extension == "pdf" || Extension == "png" | Extension == "jpeg" || Extension == "jpg"){
			userData.append('file[' + flag_val + ']', design_file);
		}else{
			images_error=true;			
		}
        i++;
    });
	if(images_error){
		jAlert('Please Upload JPEG, PNG, PDF, JPG format only', 'File upload type Error');
		return false;
	}
	
    userData.append('room_name', room_name);
    userData.append('location', location);
    userData.append('tenant_admin', tenant_admin);
    userData.append('tenant_user', tenant_user);
    userData.append('auto_billing', auto_billing);
    
    userData.append('recurrence_building_user', rec_building_users);
    userData.append('recurrence_tenant_admin', rec_tenant_admin);
    userData.append('recurrence_tenant_user', rec_tenant_users);
    
    userData.append('status', status);

    userData.append('building_id', building_id);
    userData.append('schedule_id', schedule_name);
    var rate_sch = JSON.stringify(rate_sch);
    userData.append('rate_sch', rate_sch);
    if (isValid) {
		 $('.loader').show();
		 $('#emailsave').attr('disabled','disabled');
        $.ajax({
            url: baseUrl + "conference/savecroom",
            type: "post",
            datatype: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: userData,
            success: function (data) {
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    setInterval(function () {	
                        parent.location.reload();
                    }, 1500);
                } else {
					$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }

            },
            error: function () {
                $('.loader').hide();
                alert('There was an error');
            }
        });
    }
}
function selectAllDay() {
    if ($('#all_day').is(':checked')) {
        $('.time_active').prop('disabled', true);
    } else {
        $('.time_active').prop("disabled", false);
        ;
    }
}

function validateScheduler() {
    parent.CheckForSessionpop(baseUrl);
    var schedule_name = $.trim($('#schedule_name').val());
    var build_id = $('#build_id').val();
    if(schedule_name !='') {
        $.ajax({
                type: "POST",
                url: baseUrl + 'conference/checkschedule',
                data: {schedule_name: schedule_name,bid:build_id},
                beforeSend: function () {
                    //$('.loader').show();
                },
                success: function (msg) {
                    $('.loader').hide();
                    if (msg != true) {
                        $('#schedule_name_error-error').html("");
                        validateConSchedule();
                    } else {
                        
                        $('#schedule_name_error').html("Schedule Name already in use.");
                        $('#schedule_name').focus();
                        return false;
                    }
                }
            });
    } else {
        $('#schedule_name_error').html('Schedule Name Required');
        $('#schedule_name').focus();
    }
    
    
}



function validateConSchedule() {
    var schedule_name = $.trim($('#schedule_name').val());
    var week_days_id = $.trim($('#week_days_id').val());
    var status = $.trim($('#status').val());
    var start_time = $.trim($('#start_time').val());
    var end_time = $.trim($('#end_time').val());
    var building_id = $.trim($('#building_id').val());
    var sid = $.trim($('#sid').val());
    var focus = true;
    var isValid = true;
    if (schedule_name == '') {
        $('#schedule_name_error').html('Schedule Name Required');
        isValid = false;
        if (focus) {
            $('#schedule_name').focus();
            focus = false;
        }

    } else {
        $('#schedule_name_error').html('');
    }
    if (week_days_id == '') {
        isValid = false;
        $('#schedule_week_days_error').html('Please select of  week of week');
        if (focus) {
            $('#week_days_id').focus();
            focus = false;
        }

    } else {

        $('#schedule_week_days_error').html('');
    }
    if (status == '') {
        isValid = false;
        $('#schedule_status_error').html('Schedule status');
        if (focus) {
            $('#status').focus();
            focus = false;
        }
    } else {
        $('#schedule_status_error').html('');
    }
    if ($('#all_day').is(':checked')) {
        var all_day = $('#all_day').val();
    } else {
        var all_day = '';
    }
    var dt = new Date("November 1, 2015 " + start_time);
    dt = dt.getTime();
    var dt2 = new Date("November 1, 2015 " + end_time);
    dt2 = dt2.getTime();
    if ((dt > dt2) && all_day == '') {
        isValid = false;

        jAlert('End Time must be greater then start time', 'Vision Work Orders');
    }
    if (!isValid) {
        return false;
    } else {

        $('.loader').show();
        $.ajax({
            type: "POST",
            datatype: 'json',
            url: baseUrl + 'conference/savenewschedule',
            data: {schedule_name: schedule_name, week_days_id: week_days_id, status: status, all_day: all_day, start_time: start_time, end_time: end_time, building_id: building_id, sid: sid},
            success: function (data) {

                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    setTimeout(function () {
                        parent.location.reload();
                    }, 1000);


                } else {
                    $('#success_message').html(content.msg);

                }
            }
        });

    }


}

function deleteCScheduler(sid)
{
    var check_delete = 'YES';
    jPrompt('For Deleting Scheduler, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {
            if (check_delete === return_value) {
                $('.loader').show();
                $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'conference/deletecschedule',
                    data: {sid: sid},
                    success: function (data) {

                        $('.loader').hide();
                        var content = $.parseJSON(data);
                        if (content.status == 'success') {
                            $('.success_message').html(content.msg);
                            setTimeout(function () {
                                parent.location.reload();
                            }, 1000);


                        } else {
                            $('#success_message').html(content.msg);

                        }
                    }
                });
            } else {
                jAlert('You have entered wrong word.', 'Vision Work Orders');
            }
        }
    });
}

function showUploadForm(bId) {
    $('.loader').show();
    $.ajax({
        url: baseUrl + "conference/addfile",
        type: "post",
        datatype: 'json',
        data: {bId: bId},
        success: function (data) {
            $('.loader').hide();
            //$('#edit_wo_form').show();
            $('#file_form').html(data);
            $('#file_form_href').trigger('click');
            //$('#file_form').show();
            //$('.fade_default_opt').show();
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }

    });
}

function cancelFile() {
    $("div.fancybox-close").trigger("click");
    $('#file_form').html('');
    $('#file_form').hide();
    $('.fade_default_opt').hide();
}
var trid = 1;
var urldata;
function uploadFile() {

    isValid = false;

    if ($("#design_name").val().length < 1) {
        $("#cwerr_title").html("Design Name Required");
        isValid = true;
    } else {
        $('#cwerr_title').html('');
    }

    if ($("#design_file").val().length < 1) {
        $("#cwerr_file").html("Select File Required");
        isValid = true;
    } else {
        $('#cwerr_file').html('');
    }
    if (isValid) {
        return false;
    }

    var attachmentTitle = $('#design_name').val();
    var attachmentVal = $('#design_file').val();

		var Extension = attachmentVal.substring(attachmentVal.lastIndexOf('.') + 1).toLowerCase();
		
		if (Extension == "pdf" || Extension == "png" | Extension == "jpeg" || Extension == "jpg")
		{			
			$('#table_attachment').append('<tr id="trid_' + trid + '"><td id="tdtitle_' + trid + '">' + attachmentTitle + '</td><td id="tdurl_' + trid + '">' + attachmentVal + '</td><td><a onclick="editAttach(' + trid + ')" href="javascript:void(0);" class="editCat"><img src="' + baseUrl + 'public/images/edit.png"></a><a title="Delete"  class="delCat" onclick="removeAttach(' + trid + ')" href="javascript:void(0);"><img src="' + baseUrl + 'public/images/delete.png"></a> </td></tr>');
			//$("#design_file").clone().attr("flag", attachmentTitle).appendTo("#trid_" + trid);
			$("#design_file").attr("flag", attachmentTitle).appendTo("#trid_" + trid);
			$('#table_attachment ' + ' #trid_' + trid + ' .design_file').removeAttr('id');
			$('#table_attachment .design_file').hide();
			$('#no_design').hide();
		}else{
			jAlert('Please Upload JPEG, PNG, PDF, JPG format only', 'File upload type Error');
			return false;
		}

    trid++;
    cancelFile();
}
function updateFile() {
    isValid = false;

    if ($("#design_name").val().length < 1) {
        $("#cwerr_title").html("Design Name Required");
        isValid = true;
    } else {
        $('#cwerr_title').html('');
    }

    if ($("#design_file").val().length < 1 && $("#check_image").val().length < 1) {
        $("#cwerr_file").html("Select File Required");
        isValid = true;
    } else {
        $('#cwerr_file').html('');
    }
    if (isValid) {
        return false;
    }

    var tblid = $('#tblid').val();
    var name = $('#design_name').val();
    var file = $('#design_file').val();

    if (name.length > 0) {
        $('#tdtitle_' + tblid).html(name);
    }
    if (file.length > 0) {
        $('#tdurl_' + tblid).html(file);
    }

    //console.log(tblid);
    //$("#design_name").appendTo("#trid_r_" + tblid);
    //$('#table_attachment ' + ' #trid_r_' + tblid + ' .design_file').attr('id',tblid);
    //$('#table_attachment #design_name').hide();
    $("#design_file").attr("flag", name).appendTo("#trid_r_" + tblid);
    $('#table_attachment ' + ' #trid_r_' + tblid + ' .design_file').attr('id', tblid);
    $('#table_attachment .design_file').hide();
    $('#no_design').hide();
    cancelFile();

    //$('#trid_'+ tblid).append("<td>etst</td>");	

}

function editAttach(trid) {
    $('.loader').show();

    var name = $('#tdtitle_' + trid).text();
    var imageurl = $('#tdurl_' + trid).text();
    $.ajax({
        url: baseUrl + "conference/addfile",
        type: "post",
        datatype: 'json',
        data: {room_id: trid, name: name, url: imageurl},
        success: function (data) {
            $('.loader').hide();

            $('#file_form').html(data);
            $('#file_form_href').trigger('click');

        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }

    });

}
function removeAttach(id)
{
    $('#trid_' + id).remove();
}
function removeAttachDb(r_id, id)
{	
    var check_delete = 'YES';
    jPrompt('For Deleting Design Layout, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {
            if (check_delete === return_value) {
                $("#delete_attachment").append('<input type="hidden" class="delete_attachment" name="delete_attachment" value="' + id + '" >');
                $('#trid_' + r_id).remove();
            }

        } else {
            jAlert('You have entered wrong word.', 'Vision Work Orders');
        }

    });
}

function updateCRoom()
{
    
    var room_name = $.trim($('#room_name').val());
    var location = $.trim($('#location').val());
    var schedule_name = $.trim($('#schedule_name').val());
    var cid = $.trim($('#cid').val());
    
      /* Recurrence */
    
    if($('#rec_building_users').is(':checked')){
        var rec_building_users = 1;
    }else{
        var rec_building_users = 0;
    }
    
    if($('#rec_tenant_admin').is(':checked')){
        var rec_tenant_admin = 1;
    }else{
        var rec_tenant_admin = 0;
    }
    
    if($('#rec_tenant_users').is(':checked')){
        var rec_tenant_users = 1;
    }else{
        var rec_tenant_users = 0; 
    }
    /* End Recurrence*/
    
    
    var tenant_admin = 1;
    if ($('#tenant_admin').is(':checked')) {
        tenant_admin = 1;
    } else {
        tenant_admin = 0;
    }
    var tenant_user = 0;
    if ($('#tenant_user').is(':checked')) {
        tenant_user = 1;
    } else {
        tenant_user = 0;
    }
    var auto_billing = 0;
    if ($('#auto_billing').is(':checked')) {
        auto_billing = 1;
    } else {
        auto_billing = 0;
    }
    var status = $.trim($('#status').val());
    var isValid = true;
    var isFocus = false;

    if (room_name == '') {
        $('#room-name-error').html('Room Name Required');
        $('#room_name').focus();
        isValid = false;
        isFocus = true;
    } else {
        $('#room-name-error').html('');
    }

    if (location == '') {
        $('#location-error').html('Location Required');
        isValid = false;
        if (!isFocus) {
            $('#location').focus();
            isFocus = true;
        }
    } else {
        $('#location-error').html('');
    }
    if (schedule_name == '') {
        $('#schedule-error').html('Availability Schedule Required');
        isValid = false;
        if (!isFocus) {
            $('#schedule_name').focus();
            isFocus = true;
        }
    } else {
        $('#schedule-error').html('');
    }

    var rate_sch = new Array();
    var i = 0;
    $(".plan_status").each(function () {
        if ($(this).is(':checked')) {
            var plan_id = $(this).val();
            var cost_id = $('#cost_' + plan_id).val();
            var min_val = $('#min_' + plan_id).val();
            var max_val = $('#max_' + plan_id).val();
            rate_sch[i] = {plan: plan_id, cost: cost_id, min: min_val, max: max_val}
            i=i+1;
        }
    });
    //console.log(rate_sch);  
    
    if (i == 0) {
        $('#rate-error').html('Rate Schedule Required');
        isValid = false;
        if (!isFocus) {
            $(".cost_max:eq(0)").focus();
            isFocus = true;
        }
    } else {
        var rCount = rate_sch.length;
        for (var j = 0; j < rCount; j++) {
            if (rate_sch[j].min == '') {
                $('#rate-error').html('Minimum must be greter than 0');
                if (!isFocus) {
                    $("#min_" + rate_sch[j].plan).focus();
                    isFocus = true;
                }
                isValid = false;

            }
            if(rate_sch[j].cost == ''){
                $('#rate-error').html('Please Enter The Price Plan');
                if (!isFocus) {
                    $("#cost_" + rate_sch[j].plan).focus();
                    isFocus = true;
                }
                isValid = false;
            }
             if (rate_sch[j].max != '') {
                if (parseInt(rate_sch[j].min) > parseInt(rate_sch[j].max)) {
                    $('#rate-error').html('Maximum must be greter than Minimum');
                    $("#max_" + rate_sch[j].plan).focus();
                    isFocus = true;
                    isValid = false;
                }
            } else {
                $('#rate-error').html('');
            }
        }

    }
    //console.log(rate_sch);
    //return false;
    var design_file = new Array();
    var design_file1 = '';
    var i = 0;
    var myArray = new Array();
    var design_name = [];
    var images_error= false;

    var userData = new FormData();
    $('.design_file').each(function () {
        design_file = $(this).prop('files')[0];
        var flag_val = escape($(this).attr('flag'));
		var filename=$(this).val();                
                if(filename.length > 1){
                    var Extension = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
                    if (Extension == "pdf" || Extension == "png" | Extension == "jpeg" || Extension == "jpg")
                    {
                        if (typeof $(this).attr("id") !== "undefined") {
                                id = $(this).attr("id");
                                if (design_file)
                                        userData.append('file[' + id + ']', design_file);
                                        userData.append('file[' + id + ']', flag_val);
                        } else {
                                userData.append('file[' + flag_val + ']', design_file);
                        }
                    }else{
                        images_error= true;
                    }
                }else{
                    id = $(this).attr("id");
                    userData.append('file[' + id + ']', flag_val);
                }
		i++;
    });
	if(images_error){
		jAlert('Please Upload JPEG, PNG, PDF, JPG format only', 'File upload type Error');
		return false;
	}
    var delete_id = '';
    $('.delete_attachment').each(function () {
        if ($(this).val() != '') {
            delete_id += $(this).val() + ',';
        }
    });



    userData.append('room_name', room_name);
    userData.append('location', location);
    userData.append('tenant_admin', tenant_admin);
    userData.append('tenant_user', tenant_user);
    userData.append('auto_billing', auto_billing);
    
    userData.append('recurrence_building_user', rec_building_users);
    userData.append('recurrence_tenant_admin', rec_tenant_admin);
    userData.append('recurrence_tenant_user', rec_tenant_users);
    
    userData.append('status', status);
    userData.append('schedule_id', schedule_name);
    userData.append('delete_id', delete_id);
    userData.append('cid', cid);
    var rate_sch = JSON.stringify(rate_sch);
    userData.append('rate_sch', rate_sch);    
     //console.log(isValid);
    if (isValid) {
         $('.loader').show();
        $.ajax({
            url: baseUrl + "conference/updatecroom",
            type: "post",
            datatype: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: userData,
            success: function (data) {
               // alert(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    setInterval(function () {
                        parent.location.reload();
                    }, 1500);
                } else {
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }

            },
            error: function () {
                $('.loader').hide();
                alert('There was an error');
            }
        });
    }
}


function updateCMultiRoom()
{

    var room_name = $.trim($('#room_name').val());
    var location = $.trim($('#location').val());
    var schedule_name = $.trim($('#schedule_name').val());
    var cid = $.trim($('#cid').val());
    
    /* Recurrence */
    
    if($('#rec_building_users').is(':checked')){
        var rec_building_users = 1;
    }else{
        var rec_building_users = 0;
    }
    
    if($('#rec_tenant_admin').is(':checked')){
        var rec_tenant_admin = 1;
    }else{
        var rec_tenant_admin = 0;
    }
    
    if($('#rec_tenant_users').is(':checked')){
        var rec_tenant_users = 1;
    }else{
        var rec_tenant_users = 0; 
    }
    /* End Recurrence*/
    
    var tenant_admin = 1;
    if ($('#tenant_admin').is(':checked')) {
        tenant_admin = 1;
    } else {
        tenant_admin = 0;
    }
    var tenant_user = 0;
    if ($('#tenant_user').is(':checked')) {
        tenant_user = 1;
    } else {
        tenant_user = 0;
    }
    var auto_billing = 0;
    if ($('#auto_billing').is(':checked')) {
        auto_billing = 1;
    } else {
        auto_billing = 0;
    }
    var status = $.trim($('#status').val());
    var isValid = true;
    var isFocus = false;

    if (room_name == '') {
        $('#room-name-error').html('Room Name Required');
        $('#room_name').focus();
        isValid = false;
        isFocus = true;
    } else {
        $('#room-name-error').html('');
    }

    if (location == '') {
        $('#location-error').html('Location Required');
        isValid = false;
        if (!isFocus) {
            $('#location').focus();
            isFocus = true;
        }
    } else {
        $('#location-error').html('');
    }
    if (schedule_name == '') {
        $('#schedule-error').html('Availability Schedule Required');
        isValid = false;
        if (!isFocus) {
            $('#schedule_name').focus();
            isFocus = true;
        }
    } else {
        $('#schedule-error').html('');
    }

    var rate_sch = new Array();
    var i = 0;
    $(".plan_status").each(function () {
        if ($(this).is(':checked')) {
            var plan_id = $(this).val();
            var cost_id = $('#cost_' + plan_id).val();
            var min_val = $('#min_' + plan_id).val();
            var max_val = $('#max_' + plan_id).val();
            rate_sch[i] = {plan: plan_id, cost: cost_id, min: min_val, max: max_val}
            i++;
        }
    });
    if (i == 0) {
        $('#rate-error').html('Rate Schedule Required');
        isValid = false;
        if (!isFocus) {
            $(".cost_max:eq(0)").focus();
            isFocus = true;
        }
    } else {
        var rCount = rate_sch.length;
        for (var j = 0; j < rCount; j++) {
            if (rate_sch[j].min == '') {
                $('#rate-error').html('Minimum must be greter than 0');
                if (!isFocus) {
                    $("#min_" + rate_sch[j].plan).focus();
                    isFocus = true;
                }
                isValid = false;

            } else if (rate_sch[j].max != '') {
                if (parseInt(rate_sch[j].min) > parseInt(rate_sch[j].max)) {
                    $('#rate-error').html('Maximum must be greter than Minimum');
                    $("#min_" + rate_sch[j].plan).focus();
                    isFocus = true;
                    isValid = false;
                }
            } else {
                $('#rate-error').html('');
            }
        }

    }

    var design_file = new Array();
    var design_file1 = '';
    var i = 0;
    var userData = new FormData();
	var images_error= false;
    $('.design_file').each(function () {
        design_file = $(this).prop('files')[0];
        var flag_val = $(this).attr('flag');
		var filename=$(this).val();                
                if(filename.length > 1){
                    var Extension = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
                    if (Extension == "pdf" || Extension == "png" | Extension == "jpeg" || Extension == "jpg")
                    {
                        if (typeof $(this).attr("id") !== "undefined") {
                                id = $(this).attr("id");
                                if (design_file)
                                        userData.append('file[' + id + ']', design_file);
                                        userData.append('file[' + id + ']', flag_val);
                        } else {
                                userData.append('file[' + flag_val + ']', design_file);
                        }
                    }else{
                        images_error= true;
                    }
                }else{
                    id = $(this).attr("id");
                    userData.append('file[' + id + ']', flag_val);
                }
        i++;
    });
	if(images_error){
		jAlert('Please Upload JPEG, PNG, PDF, JPG format only', 'File upload type Error');
		return false;
	}
    var delete_id = '';
    $('.delete_attachment').each(function () {
        if ($(this).val() != '') {
            delete_id += $(this).val() + ',';
        }
    });

    var croom_to_list = [];
    $('#croom_to_list :selected').each(function (i, selected) {
        croom_to_list[i] = $(selected).val();
    });
    if (croom_to_list.length <= 1) {
        $('#avroom-error').html('There must be 2 Combined Rooms Selected');
        $("#croom_to_list").focus();
        isFocus = true;
        isValid = false;
    } 
    userData.append('room_name', room_name);
    userData.append('location', location);
    userData.append('tenant_admin', tenant_admin);
    userData.append('tenant_user', tenant_user);
    userData.append('auto_billing', auto_billing);
    userData.append('recurrence_building_user', rec_building_users);
    userData.append('recurrence_tenant_admin', rec_tenant_admin);
    userData.append('recurrence_tenant_user', rec_tenant_users);
    userData.append('status', status);
    userData.append('schedule_id', schedule_name);
    userData.append('delete_id', delete_id);
    userData.append('cid', cid);
    var rate_sch = JSON.stringify(rate_sch);
    userData.append('rate_sch', rate_sch);
    var croom_to_list = JSON.stringify(croom_to_list);
    userData.append('croom_to_list', croom_to_list);
    if (isValid) {
        $.ajax({
            url: baseUrl + "conference/updatecmultiroom",
            type: "post",
            datatype: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: userData,
            success: function (data) {                
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    setInterval(function () {
                        parent.location.reload();
                    }, 1100);
                } else {
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }

            },
            error: function () {
                $('.loader').hide();
                alert('There was an error');
            }
        });
    }
}



function checkTAdmin()
{
    if ($('#tenant_admin').is(':checked')) {

    } else {
        $('#tenant_admin').prop('checked', true);
    }
}

function checkTUser()
{
    if ($('#tenant_user').is(':checked')) {
        if ($('#tenant_admin').is(':checked')) {

        } else {
            jAlert('Please first unselect Tenant User ', 'Vision Work Orders');
        }
        $('#tenant_admin').prop('checked', true);
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

function isDeciNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode > 31
            && (charCode < 48 || charCode > 57)) {
        return false;
    } else
        return true;
}

function editConRoom(url)
{
    CheckForSessionpop(baseUrl);
    $('a[href="#editConRoom"]').fancybox({
        type: 'iframe',
        href: url,
        width: 680,
        height: 600,
        'beforeClose': function () {           
        },
        'afterLoad': function () {
            $.fancybox.hideLoading();            
			setInterval(function () {                
            }, 100);
        }
    });
}

function deleteCRoom(cid)
{
    var check_delete = 'YES';
    jPrompt('For Deleting Conference Room, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {
            if (check_delete === return_value) {
                $('.loader').show();
                $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'conference/deletecroom',
                    data: {cid: cid},
                    success: function (data) {

                        $('.loader').hide();
                        var content = $.parseJSON(data);
                        if (content.status == 'success') {
                            $('.success_message').html(content.msg);
                            setTimeout(function () {
                                parent.location.reload();
                            }, 1000);
                        } else {
                            $('#success_message').html(content.msg);

                        }
                    }
                });
            } else {
                jAlert('You have entered wrong word.', 'Vision Work Orders');
            }
        }
    });
}

function deleteCrRoom(crid)
{
    var check_delete = 'YES';
    jPrompt('For Deleting Conference Room, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {
            //alert("sanjay");
            //return false;
            if (check_delete === return_value) {
                $('.loader').show();
                $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'conference/deletecrroom',
                    data: {crid: crid},
                    success: function (data) {
                        console.log(data);
                        $('.loader').hide();
                        var content = $.parseJSON(data);
                        if (content.status == 'success') {
                            $('.success_message').html(content.msg);
                            //parent.$.fancybox.close();
                            parent.$( "a#curr_mo" ).trigger( "click" );
                            //setTimeout(function () {
                            //    parent.location.reload();
                            //}, 1000);
                        } else {
                            $('#success_message').html(content.msg);

                        }
                    }
                });
            } else {
                jAlert('You have entered wrong word.', 'Vision Work Orders');
            }
        }
    });
}

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

function validateMultiConRoom()
{
    
    var room_name = $.trim($('#room_name').val());
    var schedule_name = $.trim($('#schedule_name').val());
    var location = $.trim($('#location').val());
    var building_id = $.trim($('#building_id').val());
    
       /* Recurrence */
    
    if($('#rec_building_users').is(':checked')){
        var rec_building_users = 1;
    }else{
        var rec_building_users = 0;
    }
    
    if($('#rec_tenant_admin').is(':checked')){
        var rec_tenant_admin = 1;
    }else{
        var rec_tenant_admin = 0;
    }
    
    if($('#rec_tenant_users').is(':checked')){
        var rec_tenant_users = 1;
    }else{
        var rec_tenant_users = 0; 
    }
    /* End Recurrence*/
    
    var tenant_admin = 1;
    if ($('#tenant_admin').is(':checked')) {
        tenant_admin = 1;
    } else {
        tenant_admin = 0;
    }
    var tenant_user = 0;
    if ($('#tenant_user').is(':checked')) {
        tenant_user = 1;
    } else {
        tenant_user = 0;
    }
    var auto_billing = 0;
    if ($('#auto_billing').is(':checked')) {
        auto_billing = 1;
    } else {
        auto_billing = 0;
    }
    var status = $.trim($('#status').val());
    var isValid = true;
    var isFocus = false;

    if (room_name == '') {
        $('#room-name-error').html('Room Name Required');
        $('#room_name').focus();
        isValid = false;
        isFocus = true;
    } else {
        $('#room-name-error').html('');
    }
    if (location == '') {
        $('#location-error').html('Location Required');
        isValid = false;
        if (!isFocus) {
            $('#location').focus();
            isFocus = true;
        }
    } else {
        $('#location-error').html('');
    }

    if (schedule_name == '') {
        $('#schedule-error').html('Availability Schedule Required');
        isValid = false;
        if (!isFocus) {
            $('#schedule_name').focus();
            isFocus = true;
        }
    } else {
        $('#schedule-error').html('');
    }

    var rate_sch = new Array();
    var i = 0;
    $(".plan_status").each(function () {
        if ($(this).is(':checked')) {
            var plan_id = $(this).val();
            var cost_id = $('#cost_' + plan_id).val();
            var min_val = $('#min_' + plan_id).val();
            var max_val = $('#max_' + plan_id).val();
            rate_sch[i] = {plan: plan_id, cost: cost_id, min: min_val, max: max_val}
            i++;
        }
    });
    if (i == 0) {
        $('#rate-error').html('Rate Schedule Required');
        isValid = false;
        if (!isFocus) {
            $(".cost_max:eq(0)").focus();
            isFocus = true;
        }
    } else {
        var rCount = rate_sch.length;
        for (var j = 0; j < rCount; j++) {
            if (rate_sch[j].min == '') {
                $('#rate-error').html('Minimum must be greter than 0');
                if (!isFocus) {
                    $("#min_" + rate_sch[j].plan).focus();
                    isFocus = true;
                }
                isValid = false;

            } else if (rate_sch[j].max != '') {
                if (parseInt(rate_sch[j].min) > parseInt(rate_sch[j].max)) {
                    $('#rate-error').html('Maximum must be greter than Minimum');
                    $("#min_" + rate_sch[j].plan).focus();
                    isValid = false;
                    isFocus = true;
                }
            } else {
                $('#rate-error').html('');
            }
        }

    }

    var design_file = new Array();
    var design_file1 = '';
    var i = 0;
	var images_error= false;
    var userData = new FormData();
    $('.design_file').each(function () {
        design_file = $(this).prop('files')[0];
        var flag_val = $(this).attr('flag');
		var filename=$(this).val();
		var Extension = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
		if (Extension == "pdf" || Extension == "png" | Extension == "jpeg" || Extension == "jpg")
		{
			userData.append('file[' + flag_val + ']', design_file);
		}else{
			images_error= true;
		}
        i++;
    });
	
	if(images_error){
		jAlert('Please Upload JPEG, PNG, PDF, JPG format only', 'File upload type Error');
		return false;
	}	
	
    var croom_to_list = [];
    $('#croom_to_list :selected').each(function (i, selected) {
        croom_to_list[i] = $(selected).val();
    });

    if (croom_to_list.length <= 1) {
        $('#avroom-error').html('There must be 2 Combined Rooms Selected');
        $("#croom_to_list").focus();
        isFocus = true;
        isValid = false;
    }

    userData.append('room_name', room_name);
    userData.append('tenant_admin', tenant_admin);
    userData.append('tenant_user', tenant_user);
    userData.append('auto_billing', auto_billing);
    userData.append('recurrence_building_user', rec_building_users);
    userData.append('recurrence_tenant_admin', rec_tenant_admin);
    userData.append('recurrence_tenant_user', rec_tenant_users);
    userData.append('status', status);
    userData.append('location', location);
    userData.append('building_id', building_id);
    userData.append('schedule_id', schedule_name);
    var rate_sch = JSON.stringify(rate_sch);
    userData.append('rate_sch', rate_sch);
    var croom_to_list = JSON.stringify(croom_to_list);
    userData.append('croom_to_list', croom_to_list);
    if (isValid) {
		$('.loader').show();
		$('#emailsave').attr('disabled','disabled');
        $.ajax({
            url: baseUrl + "conference/savemulticroom",
            type: "post",
            datatype: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: userData,
            success: function (data) {
               // alert(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    setInterval(function () {
                        parent.location.reload();
                    }, 1500);
                } else {
					$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }

            },
            error: function () {
                $('.loader').hide();
                alert('There was an error');
            }
        });
    }else{
		$('.loader').hide();
	}
	
}

function croomPagination(page) {
    $('#croom_page').val(page);
    var building_id = $('#building_id').val();
    var search_by = $.trim($('#search_by').val());
    var search_value = $.trim($('#search_value').val());
    showCRoomList(building_id, search_by, search_value);
}

function showCRoomList(buildingId, search_by, search_value) {
    var search_by = (typeof search_by === "undefined") ? "" : search_by;
    var search_value = (typeof search_value === "undefined") ? "" : search_value;
    if (buildingId != '') {
        var page = $('#croom_page').val();
        $('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + "conference/showcroomlist",
            dataType: 'json',
            data: {
                buildingId: buildingId,
                page: page,
                search_by: search_by,
                search_value: search_value
            },
            success: function (response) {
                $('.loader').hide();
                // $("#priority_popup").hide();
                $('#conference_info').html('');
                $('#conference_info').html(response.content);

            }

        });
    } else {
        jAlert('No Building selected', 'Vision Work Orders');
    }
}

function cschedulePagination(page) {
    $('#croom_page').val(page);
    var building_id = $('#building_id').val();
    var search_by = $.trim($('#search_by_sch').val());
    var search_value = $.trim($('#search_value_sch').val());
    showCScheduleList(building_id, search_by, search_value);
}

function showCScheduleList(buildingId, search_by, search_value) {
    var search_by = (typeof search_by === "undefined") ? "" : search_by;
    var search_value = (typeof search_value === "undefined") ? "" : search_value;
   //console.log(search_value);
    if (buildingId != '') {
        var page = $('#croom_page').val();
        $('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + "conference/showcschedulelist",
            dataType: 'json',
            data: {
                buildingId: buildingId,
                page: page,
                search_by: search_by,
                search_value: search_value
            },
            success: function (response) {
                $('.loader').hide();
                // $("#priority_popup").hide();
                $('#schedule_info').html('');
                $('#schedule_info').html(response.content);

            }

        });
    } else {
        jAlert('No Building selected', 'Vision Work Orders');
    }
}

function changeMonth(mon, year,page) {

    if (mon != '' && year != '', page!='') {
        $('.loader').show();
        var type=$("#t_type").val();
        //alert(type);
        $.ajax({
            type: "POST",
            url: baseUrl + "conference/changemonth",
            data: {
                month: mon,
                year: year,
                page: page,
                type:type
            },
            success: function (content) {
                $('.loader').hide();
                $('#calender_change').html('');
                $('#calender_change').html(content);

            }

        });
    }
}




function changeweekly(yr,mon, ldate, page) {

    if (mon != '' && ldate != '') {
        $('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + "conference/changeweekly",
            data: {
                month: mon,
                lastdate: ldate,
                page:page,
				year:yr
            },
            success: function (content) {
                $('.loader').hide();
                $('#calender_change').html('');
                $('#calender_change').html(content);

            }

        });
    }
}

function changedaily(yr, mon, days, page) {

    if (mon != '' && days != '') {
        $('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + "conference/changedaily",
            data: {
                month: mon,
                day: days,
                page: page,
                year: yr
            },
            success: function (content) {
                $('.loader').hide();
                $('#calender_change').html('');
                $('#calender_change').html(content);

            }

        });
    }
}

function schedule(dt,mon,year,page){
	if (mon != '' && year != '') {
        $('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + "conference/schedule",
            data: {
                month: mon,
                year: year,
                date: dt,
                page:page,
                status:''
            },
            success: function (content) {
                $('.loader').hide();
                $('#calender_change').html('');
                $('#calender_change').html(content);

            }

        });
    }
}

function schedulefirst(dt,mon,year,page,status){
	if (mon != '' && year != '') {
        $('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + "conference/schedule",
            data: {
                month: mon,
                year: year,
                date: dt,
                page:page,
                status:status
            },
            success: function (content) {
                $('.loader').hide();
                $('#calender_change').html('');
                $('#calender_change').html(content);

            }

        });
    }
}

function createBooking(url)
{
	$('.loader').hide();
    //CheckForSessionpop(baseUrl);
    
    
    $('a[href="#createBooking"]').fancybox({
        type: 'iframe',
        href: url,
        width: 680,
        height: 900,
        'beforeClose': function () {
            $('.loader').hide();            
        },
        'afterLoad': function () {
            $.fancybox.hideLoading();
            
           
            setInterval(function () {
                $('.loader').hide();
            }, 5000);
        },
        afterShow: function()
        {
          $(".fancybox-inner").addClass("createbooking");  
          $("body").attr("style","background-color: white !important;");
        }
        
      
    });
    //alert($("#hrday input:checked").val());
    //$("").o(":cheecked"){        
    //}

}
function CheckForSessionpop(baseUrl) {
var ua = window.navigator.userAgent;
var msie = ua.indexOf("MSIE ");
        var str="chksession=true";
        jQuery.ajax({
                type: "POST",
                url         : baseUrl+"user/checksession",
                data: str,
                cache: false,
                success: function(res){
                    if(parseInt(res) != 1) {
					$('#popup_container').show();
					$(".fancybox-wrap ").empty();
					$('#fancybox-overlay').remove();
					
					
					
						if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) { 
							document.execCommand("Stop");
						} else {
						
							document.location.href = baseUrl+"/index/logout";							
							window.stop();
						}
						
						document.location.href = baseUrl+"/index/logout";
						return false;
                    }
                }
        });
}


function makerecurrenceclone(){
    
    var html = "";

    var how = $("#howafter").val();
    var dateR = $('input[name=reqBy]:checked').val();
    if(how.trim()==""){
        $('#howaften_error').html("Please Select On List");
        $('#howaften_error').addClass("ugroupErr");
        return false;
    }else{
        html += '<input type="hidden" id="how_aften" value="'+how+'">';
        $('#howaften_error').html("");
        $('#howaften_error').removeClass("ugroupErr");
    }
    
    if(dateR == 1){
       html += '<input type="hidden" id="date_rang" value="'+dateR+'">'; 
    }else if(dateR == 2){
        var no = $("#number").val();
        if(no.trim()==''){
            $('#number_error').html("Please Enter Number");
            $('#number_error').addClass("ugroupErr");
            return false;
        }else{
            html += '<input type="hidden" id="date_rang" value="'+dateR+'">';
            html += '<input type="hidden" id="date_input" value="'+no+'">';
            $('#number_error').html("");
            $('#number_error').removeClass("ugroupErr");
        }
    }if(dateR == 3){
        var no = $("#daterec").val();
        //console.log(no);
        if(no.trim()=='' || no.trim()=="mm/dd/yyyy"){            
            $('#number_error').html("");
            $('#number_error').removeClass("ugroupErr");
            $('#date_error').html("Please Select Date");
            $('#date_error').addClass("ugroupErr");
            return false;
        }else{
            html += '<input type="hidden" id="date_rang" value="'+dateR+'">';
            html += '<input type="hidden" id="date_input" value="'+no+'">';
            $('#date_error').html("");
            $('#date_error').removeClass("ugroupErr");
        }
    }
    $(".recurrence").html("");
    $(html).clone().appendTo(".recurrence");
        //jQuery.fancybox.close();    
        $('.loader').show();
        validateCRoomRequest();
        
        
        
}


function updatemakerecurrenceclone(){
    
    var html = "";

    var how = $("#howafter").val();
    var dateR = $('input[name=reqBy]:checked').val();
    if(how.trim()==""){
        $('#howaften_error').html("Please Select On List");
        $('#howaften_error').addClass("ugroupErr");
        return false;
    }else{
        html += '<input type="hidden" id="how_aften" value="'+how+'">';
        $('#howaften_error').html("");
        $('#howaften_error').removeClass("ugroupErr");
    }
    
    if(dateR == 1){
       html += '<input type="hidden" id="date_rang" value="'+dateR+'">'; 
    }else if(dateR == 2){
        var no = $("#number").val();
        if(no.trim()==''){
            $('#number_error').html("Please Enter Number");
            $('#number_error').addClass("ugroupErr");
            return false;
        }else{
            html += '<input type="hidden" id="date_rang" value="'+dateR+'">';
            html += '<input type="hidden" id="date_input" value="'+no+'">';
            $('#number_error').html("");
            $('#number_error').removeClass("ugroupErr");
        }
    }if(dateR == 3){
        var no = $("#daterec").val();
        //console.log(no);
        if(no.trim()=='' || no.trim()=="mm/dd/yyyy"){            
            $('#number_error').html("");
            $('#number_error').removeClass("ugroupErr");
            $('#date_error').html("Please Select Date");
            $('#date_error').addClass("ugroupErr");
            return false;
        }else{
            html += '<input type="hidden" id="date_rang" value="'+dateR+'">';
            html += '<input type="hidden" id="date_input" value="'+no+'">';
            $('#date_error').html("");
            $('#date_error').removeClass("ugroupErr");
        }
    }
    $(".recurrence").html("");
    $(html).clone().appendTo(".recurrence");
        //jQuery.fancybox.close();
        
        updatevalidateCRoomRequest();
}



function cancelrecurrence(){
    jQuery.fancybox.close();
}

// Open add recurrence popup on Recurren

function saveRecurrence() {
    //$('.loader').show();
    var rid = $("#croom").val();
    var tenant = $("#tenant").val();
    var created_user = $("#created_user").val();
    var meeting_title = $("#meeting_title").val();
    var phone_number = $("#phone_number").val();
    var email = $("#email").val();
    var croom = $("#croom").val();
    var start_time = $("#start_time").val();
    var end_time = $("#end_time").val();
    var building_id = $("#building_id").val();
    var requested_date = $("#requested_date").val();
    var design_id = $('input[name="design_id"]:checked').val();
    var schedule_id = $('input[type="radio"]:checked').val();
    var isFocus = false;
    var isValid = true;
    //console.log(start_time + requested_date );
    //return false;
    if (tenant == ''){
        $('#tenant-error').html('Tenant Required');
        $('#tenant').focus();
        isValid = false;
        isFocus = true;
    } else {
        $('#tenant-error').html('');
    }

    if (created_user == '') {
        $('#requestedby-error').html('Requested By Required');
        if (!isFocus) {
            $('#created_user').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#requestedby-error').html('');
    }

    if (meeting_title == '') {
        $('#meeting-title-error').html('Meeting Title Required');
        if (!isFocus) {
            $('#meeting_title').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#meeting-title-error').html('');
    }

    if (phone_number == '') {
        $('#phone-error').html('Phone Number Required');
        if (!isFocus) {
            $('#phone_number').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#phone-error').html('');
    }

    if (email == '') {
        $('#email-error').html('Email Required');
        if (!isFocus) {
            $('#email').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#email-error').html('');
    }

    if (croom == '') {
        $('#room-error').html('Conference Room Required');
        if (!isFocus) {
            $('#croom').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#room-error').html('');
    }
    if ($("input[name=design_id]").length) {

        if ($('input[name=design_id]:checked').val() != undefined && $('input[name=design_id]:checked').val() != '') {
            design_id = $('input[name=design_id]:checked').val();
            $('#design-error').html('');
        } else {
            $('#design-error').html('Design Required');
            if (!isFocus) {
                $('#design-error').focus();
                isFocus = true;
            }
            isValid = false;
        }

    }
    if ($('input[name=schedule_id]:checked').val() != undefined && $('input[name=schedule_id]:checked').val() != '') {
        schedule_id = $('input[name=schedule_id]:checked').val();
        $('#schedule-error').html('');
    } else {
        $('#schedule-error').html('Schedule Required');
        if (!isFocus) {
            $('#schedule-error').focus();
            isFocus = true;
        }
        isValid = false;
    }
    var dt = new Date("November 1, 2015 " + start_time);
    dt = dt.getTime();
    var dt2 = new Date("November 1, 2015 " + end_time);
    dt2 = dt2.getTime();
    if (dt > dt2 ) {
        jAlert('End Time must be greater then start time', 'Vision Work Orders');
        return false;
    }
    
    
    
    if (!isValid) {
        return false;
    } else {
                
            $.ajax({
                    url: baseUrl + "conference/recurrencesetup",
                    type: "post",
                    datatype: 'json',
                    data: {roomId: rid,requested_date:requested_date},
                    success: function (data) {
                        //console.log(data);
                        $('.loader').hide();
                        //$('#edit_wo_form').show();
                        $('#file_form').html(data);
                        $('#file_form_href').trigger('click');
                        //$('#file_form').show();
                        //$('.fade_default_opt').show();
                    },
                    error: function () {
                        $('.loader').hide();
                        alert('There was an error');
                    }

                });
        
    }
}


// edit recurrence popup on Recurren

function editRecurrence() {
    //$('.loader').show();
    var rid = $("#croom").val();
    var tenant = $("#tenant").val();
    var created_user = $("#created_user").val();
    var meeting_title = $("#meeting_title").val();
    var phone_number = $("#phone_number").val();
    var email = $("#email").val();
    var croom = $("#croom").val();
    var start_time = $("#start_time").val();
    var end_time = $("#end_time").val();
    var building_id = $("#building_id").val();
    var requested_date = $("#requested_date").val();
    var design_id = $('input[name="design_id"]:checked').val();
    var schedule_id = $('input[type="radio"]:checked').val();
    
    var howaften = $('#howaften').val();
    var daterang = $('#daterang').val();
    var inputdata = $('#inputdata').val();
    var isFocus = false;
    var isValid = true;
    //console.log(start_time + requested_date );
    //return false;
    if (tenant == ''){
        $('#tenant-error').html('Tenant Required');
        $('#tenant').focus();
        isValid = false;
        isFocus = true;
    } else {
        $('#tenant-error').html('');
    }

    if (created_user == '') {
        $('#requestedby-error').html('Requested By Required');
        if (!isFocus) {
            $('#created_user').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#requestedby-error').html('');
    }

    if (meeting_title == '') {
        $('#meeting-title-error').html('Meeting Title Required');
        if (!isFocus) {
            $('#meeting_title').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#meeting-title-error').html('');
    }

    if (phone_number == '') {
        $('#phone-error').html('Phone Number Required');
        if (!isFocus) {
            $('#phone_number').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#phone-error').html('');
    }

    if (email == '') {
        $('#email-error').html('Email Required');
        if (!isFocus) {
            $('#email').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#email-error').html('');
    }

    if (croom == '') {
        $('#room-error').html('Conference Room Required');
        if (!isFocus) {
            $('#croom').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#room-error').html('');
    }
    if ($("input[name=design_id]").length) {

        if ($('input[name=design_id]:checked').val() != undefined && $('input[name=design_id]:checked').val() != '') {
            design_id = $('input[name=design_id]:checked').val();
            $('#design-error').html('');
        } else {
            $('#design-error').html('Design Required');
            if (!isFocus) {
                $('#design-error').focus();
                isFocus = true;
            }
            isValid = false;
        }

    }
    if ($('input[name=schedule_id]:checked').val() != undefined && $('input[name=schedule_id]:checked').val() != '') {
        schedule_id = $('input[name=schedule_id]:checked').val();
        $('#schedule-error').html('');
    } else {
        $('#schedule-error').html('Schedule Required');
        if (!isFocus) {
            $('#schedule-error').focus();
            isFocus = true;
        }
        isValid = false;
    }
    var dt = new Date("November 1, 2015 " + start_time);
    dt = dt.getTime();
    var dt2 = new Date("November 1, 2015 " + end_time);
    dt2 = dt2.getTime();
    if (dt > dt2 ) {
        jAlert('End Time must be greater then start time', 'Vision Work Orders');
        return false;
    }
    
    if (!isValid) {
        return false;
    } else {   
    
        $.ajax({
            url: baseUrl + "conference/editrecurrencesetup",
            type: "post",
            datatype: 'json',
            data: {roomId: rid,howaften:howaften,daterang:daterang,inputdata:inputdata},
            success: function (data) {
                $('.loader').hide();
                $('#file_form').html(data);
                $('#file_form_href').trigger('click');
            },
            error: function () {
                $('.loader').hide();
                alert('There was an error');
            }

        });
    }
}






function showTenantUser(tId) {
    $.ajax({
        url: baseUrl + "conference/selecttnuser/tId/" + tId,
        success: function (content) {
            $('#show_tenant_user').html(content);
             //alert($("#created_user option:selected").val());
             addEmailPhone($("#created_user option:selected").val());
            //$('#workrequest').show();
        }

    });
}

function showTenantUserEdit(tId,sId) {
    $.ajax({
        url: baseUrl + "conference/selecttnuser/tId/" + tId+"/sId/"+sId,
        success: function (content) {
            $('#show_tenant_user').html(content);
             //alert($("#created_user option:selected").val());
             addEmailPhone($("#created_user option:selected").val());
            //$('#workrequest').show();
        }

    });
}

function addEmailPhone(uid) {

    if (uid != '') {
        //$('.loader').show();
        $.ajax({
            url: baseUrl + "conference/addemailphone/uid/" + uid,
            datatype: 'json',
            success: function (data) {
                var content = $.parseJSON(data);
                //console.log(content.email);
                //console.log(content.phoneNumber);
                $('#phone_number').val(content.phoneNumber);
                $('#email').val(content.email);
                $('.loader').hide();
                // $('#show_tenant_user').html(content);
                //$('#workrequest').show();
            }

        });
    } else {
        $('#phone_number').val('');
        $('#email').val('');
    }
}

function showLayout(cid)
{
    //$('.loader').show();
    $.ajax({
        url: baseUrl + "conference/showdesign/cid/" + cid,
        success: function (content) {
            $('.loader').hide();
            $('#table_attachment').html(content);
            //alert(12);
            showtype_scheduler(cid,"");
            showdate_scheduler(cid);     
            //console.log("inner");
           // $(".fancybox-inner").attr("style","height: 697px !important");
           
            //$('#workrequest').show();
            //if("created_user").is("selected",)
           
        }

    });
}

function showdate_scheduler(cid)
{
    $.ajax({
        url: baseUrl + "conference/datescheduler/cid/" + cid,
        success: function (content) {
            //alert(content)
            //console.log(contant);
            $('.loader').hide();                 
            $('.sch_view').html(content);           
            //showdate_scheduler(cid);
            //$('#workrequest').show();
            //if("created_user").is("selected",)
           
        }

    });
}

function showtype_scheduler(cid,plan)
{
    $.ajax({
        url: baseUrl + "conference/typescheduler/cid/" + cid+'/plan/'+plan,
        success: function (content) {
            //alert(content)
            //console.log(contant);
            $('.loader').hide();
            $('.view_type').html(content);
            //showdate_scheduler(cid);
            //$('#workrequest').show();
            //if("created_user").is("selected",)
           
        }

    });
}

function showeditLayout(cid,did)
{
    $('.loader').show();
    $.ajax({
        url: baseUrl + "conference/showdesign/cid/" + cid+"/did/"+did,
        success: function (content) {
            $('.loader').hide();
            $('#table_attachment').html(content);
            
            //showtype_scheduler(cid,)
            //showdate_scheduler(cid,start,end);
            //$('#workrequest').show();
            //if("created_user").is("selected",)
           
        }

    });
}



function showedittime(cid,startTime,endTime)
{
    $.ajax({
        url: baseUrl + "conference/datescheduler/cid/" + cid + "/start_time/"+startTime+"/end_time/"+endTime,
        success: function (content) {
            
            //alert(content);
            //console.log(contant);
            $('.loader').hide();
            $('.sch_view').html(content);
            //showdate_scheduler(cid);
            //$('#workrequest').show();
            //if("created_user").is("selected",)
           
        }

    });
}



function validateCRoomRequest() {
    var tenant = $("#tenant").val();
    var created_user = $("#created_user").val();
    var meeting_title = $("#meeting_title").val();
    var phone_number = $("#phone_number").val();
    var email = $("#email").val();
    var croom = $("#croom").val();
    var start_time = $("#start_time").val();
    var end_time = $("#end_time").val();
    var building_id = $("#building_id").val();
    var requested_date = $("#requested_date").val();
    var design_id = $('input[name="design_id"]:checked').val();
    var schedule_id = $('input[type="radio"]:checked').val();
    var isFocus = false;
    var isValid = true;
    
    // Recerrence  data wiull recive in this section 
    var how_aften = $('#how_aften').val();
    var date_rang = $('#date_rang').val();
    var date_input = $('#date_input').val();

    //console.log(start_time + requested_date );
    //return false;
    if (tenant == ''){
        $('#tenant-error').html('Tenant Required');
        $('#tenant').focus();
        isValid = false;
        isFocus = true;
    } else {
        $('#tenant-error').html('');
    }

    if (created_user == '') {
        $('#requestedby-error').html('Requested By Required');
        if (!isFocus) {
            $('#created_user').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#requestedby-error').html('');
    }

    if (meeting_title == '') {
        $('#meeting-title-error').html('Meeting Title Required');
        if (!isFocus) {
            $('#meeting_title').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#meeting-title-error').html('');
    }

    if (phone_number == '') {
        $('#phone-error').html('Phone Number Required');
        if (!isFocus) {
            $('#phone_number').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#phone-error').html('');
    }

    if (email == '') {
        $('#email-error').html('Email Required');
        if (!isFocus) {
            $('#email').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#email-error').html('');
    }

    if (croom == '') {
        $('#room-error').html('Conference Room Required');
        if (!isFocus) {
            $('#croom').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#room-error').html('');
    }
    if ($("input[name=design_id]").length) {

        if ($('input[name=design_id]:checked').val() != undefined && $('input[name=design_id]:checked').val() != '') {
            design_id = $('input[name=design_id]:checked').val();
            $('#design-error').html('');
        } else {
            $('#design-error').html('Design Required');
            if (!isFocus) {
                $('#design-error').focus();
                isFocus = true;
            }
            isValid = false;
        }

    }
    if ($('input[name=schedule_id]:checked').val() != undefined && $('input[name=schedule_id]:checked').val() != '') {
        schedule_id = $('input[name=schedule_id]:checked').val();
        $('#schedule-error').html('');
    } else {
        $('#schedule-error').html('Schedule Required');
        if (!isFocus) {
            $('#schedule-error').focus();
            isFocus = true;
        }
        isValid = false;
    }
    var dt = new Date("November 1, 2015 " + start_time);
    dt = dt.getTime();
    var dt2 = new Date("November 1, 2015 " + end_time);
    dt2 = dt2.getTime();
    if (dt > dt2 ) {
        jAlert('End Time must be greater then start time', 'Vision Work Orders');
        return false;
    }
    if (!isValid) {
        return false;
    } else {
        $.ajax({
            type: "POST",
            datatype: 'json',
            url: baseUrl + 'conference/checkavaliablecr',
            data: {start_time: start_time, end_time: end_time, plan:schedule_id,requested_date: requested_date,
                croom_id: croom,booking_type:how_aften,Rang_type:date_rang,rang_value:date_input},
            success: function (data) {                
                var content = $.parseJSON(data);
                //console.log(content);
                //return false;
                if (content.status == 'success') {
                    $("#enddate").val(content.enddate);
                    //console.log(content.enddate);
                    //return false;
                   
                    validateCRoomRequest1();
                } else {
                    $('.loader').hide();
                   jAlert(content.msg, 'Vision Work Orders');
                    return false;
                }
            }
        });
    }


   
}


function updatevalidateCRoomRequest() {
    var tenant = $("#tenant").val();
    var created_user = $("#created_user").val();
    var meeting_title = $("#meeting_title").val();
    var phone_number = $("#phone_number").val();
    var email = $("#email").val();
    var croom = $("#croom").val();
    var start_time = $("#start_time").val();
    var end_time = $("#end_time").val();
    var building_id = $("#building_id").val();
    var requested_date = $("#requested_date").val();
    var design_id = $('input[name="design_id"]:checked').val();
    var schedule_id = $('input[type="radio"]:checked').val();
    
   var enddate = $("#enddate").val();
    // Recerrence  data wiull recive in this section 
    var how_aften = $('#how_aften').val();
    var date_rang = $('#date_rang').val();
    var date_input = $('#date_input').val();
    
    var crid = $('#crid').val();
    var isFocus = false;
    var isValid = true;
    if (tenant == '') {
        $('#tenant-error').html('Tenant Required');
        $('#tenant').focus();
        isValid = false;
        isFocus = true;
    } else {
        $('#tenant-error').html('');
    }

    if (created_user == '') {
        $('#requestedby-error').html('Requested By Required');
        if (!isFocus) {
            $('#created_user').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#requestedby-error').html('');
    }

    if (meeting_title == '') {
        $('#meeting-title-error').html('Meeting Title Required');
        if (!isFocus) {
            $('#meeting_title').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#meeting-title-error').html('');
    }

    if (phone_number == '') {
        $('#phone-error').html('Phone Number Required');
        if (!isFocus) {
            $('#phone_number').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#phone-error').html('');
    }

    if (email == '') {
        $('#email-error').html('Email Required');
        if (!isFocus) {
            $('#email').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#email-error').html('');
    }

    if (croom == '') {
        $('#room-error').html('Conference Room Required');
        if (!isFocus) {
            $('#croom').focus();
            isFocus = true;
        }
        isValid = false;

    } else {
        $('#room-error').html('');
    }
    if ($("input[name=design_id]").length) {

        if ($('input[name=design_id]:checked').val() != undefined && $('input[name=design_id]:checked').val() != '') {
            design_id = $('input[name=design_id]:checked').val();
            $('#design-error').html('');
        } else {
            $('#design-error').html('Design Required');
            if (!isFocus) {
                $('#design-error').focus();
                isFocus = true;
            }
            isValid = false;
        }

    }
    if ($('input[name=schedule_id]:checked').val() != undefined && $('input[name=schedule_id]:checked').val() != '') {
        schedule_id = $('input[name=schedule_id]:checked').val();
        $('#schedule-error').html('');
    } else {
        $('#schedule-error').html('Schedule Required');
        if (!isFocus) {
            $('#schedule-error').focus();
            isFocus = true;
        }
        isValid = false;
    }
    var dt = new Date("November 1, 2015 " + start_time);
    dt = dt.getTime();
    var dt2 = new Date("November 1, 2015 " + end_time);
    dt2 = dt2.getTime();
    if (dt > dt2 ) {
        jAlert('End Time must be greater then start time', 'Vision Work Orders');
        return false;
    }
    if (!isValid) {
        return false;
    } else {
        $.ajax({
            type: "POST",
            datatype: 'json',
            url: baseUrl + 'conference/checkavaliablecr',
            data: {start_time: start_time, end_time: end_time, plan:schedule_id,requested_date: requested_date, croom_id: croom,crid:crid,
            booking_type:how_aften,Rang_type:date_rang,rang_value:date_input,end_date:enddate},
            success: function (data) { 
               // console.log(data);                
                var content = $.parseJSON(data); 
                if (content.status == 'success') {
                    $('.loader').show();                    
                     updatevalidateCRoomRequest1();
                } else {
                   jAlert(content.msg, 'Vision Work Orders');
                    return false;
                }
            }
        });
    }


   
}



        

function updatevalidateCRoomRequest1()
{
    var tenant = $("#tenant").val();
    var created_user = $("#created_user").val();
    var meeting_title = $("#meeting_title").val();
    var phone_number = $("#phone_number").val();
    var email = $("#email").val();
    var croom = $("#croom").val();
    var start_time = $("#start_time").val();
    var end_time = $("#end_time").val();
    var building_id = $("#building_id").val();
    var requested_date = $("#requested_date").val();
    var month = $("#month").val().trim();
    var design_id = $('input[name="design_id"]:checked').val();
    var schedule_id = $('input[name="schedule_id"]:checked').val();
    var crid = $('#crid').val();
    
    
    
    // Recerrence  data wiull recive in this section 
        var how_aften = $('#how_aften').val();
        var date_rang = $('#date_rang').val();
        var date_input = $('#date_input').val();

	$('#emailsave').attr('disabled','disabled');
	$.ajax({
		type: "POST",
		datatype: 'json',
		url: baseUrl + 'conference/updatecroomrequest',
		data: {tenant: tenant, created_user: created_user, meeting_title: meeting_title,
			start_time: start_time, end_time: end_time,
			building_id: building_id, requested_date: requested_date,
			croom_id: croom, design_id: design_id, schedule_id: schedule_id,crid:crid,email:email,phone:phone_number,booking_type:how_aften,Rang_type:date_rang,rang_value:date_input},
		success: function (data) {
			//console.log(data);
			$('.loader').hide();
			var content = $.parseJSON(data);
			if (content.status == 'success') {
				$('.success_message').html(content.msg);
                                jQuery.fancybox.close();
                                parent.$.fancybox.close();
                                parent.$( "a#curr_mo" ).trigger( "click" );
                                //changeMonth(month, '2016','auser');                                
				//setTimeout(function () {
				//	parent.location.reload();
				//}, 1000);
                               

			} else {
				$('#emailsave').removeAttr('disabled');                        
                               // $( "button:first" ).trigger( "click" );
				 //$('.loader').show();
				$('#success_message').html(content.msg);

			}
		}
	});
}


function validateCRoomRequest1()
{
    var tenant = $("#tenant").val();
    var created_user = $("#created_user").val();
    var meeting_title = $("#meeting_title").val();
    var phone_number = $("#phone_number").val();
    var email = $("#email").val();
    var croom = $("#croom").val();
    var start_time = $("#start_time").val();
    var end_time = $("#end_time").val();
    var building_id = $("#building_id").val();
    var requested_date = $("#requested_date").val();
    var month = $("#month").val();
    var design_id = $('input[name="design_id"]:checked').val();
    var schedule_id = $('input[name="schedule_id"]:checked').val();
    var enddate = $('#enddate').val();
    // Recerrence  data wiull recive in this section 
    var how_aften = $('#how_aften').val();
    var date_rang = $('#date_rang').val();
    var date_input = $('#date_input').val();
   
	$('#emailsave').attr('disabled','disabled');
	$.ajax({
		type: "POST",
		datatype: 'json',
		url: baseUrl + 'conference/savecroomrequest',
		data: 
                    { 
                        tenant: tenant, created_user: created_user, meeting_title: meeting_title,
			start_time: start_time, end_time: end_time,
			building_id: building_id, requested_date: requested_date,
			croom_id: croom, design_id: design_id, schedule_id: schedule_id,email:email,
                        phone:phone_number,booking_type:how_aften,Rang_type:date_rang,rang_value:date_input,end_date:enddate
                    },
		success: function (data) {
                        // alert(data);
			//console.log(data);
			$('.loader').hide();
			var content = $.parseJSON(data);
			if (content.status == 'success') {
				$('.success_message').html(content.msg);
                                jQuery.fancybox.close(); 
                                parent.$.fancybox.close();
                                parent.$( "a#curr_mo" ).trigger( "click" );
                                } else {
				$('#emailsave').removeAttr('disabled');
				 //$('.loader').show();
				$('#success_message').html(content.msg);

			}
		}
	});
}

function halfday(){
	var startdate=$("#start_time").val();
	//$("#end_time option").removeAttr('disabled').
	//filter( "[value='12:00 AM'],[value='12:30 AM'],[value='01:00 AM'],[value='01:30 AM'],[value='02:00 AM'],[value='02:30 AM'],[value='03:00 AM'],[value='03:30 AM']" )
	//.attr( 'disabled', 'disabled' );
	$('.loader').show();
	$.ajax({
		type: "POST",
		datatype: 'json',
		url: baseUrl + 'conference/showdateinterval',
		data: {start:startdate,action:"add"},
		success: function (data) {
			//$("#end_time option[value='"+ data.trim() +"']").attr("selected","selected");
			$('.loader').hide();
                        endtimedata();
			/* var content = $.parseJSON(data);
			if (content.status == 'success') {
				$('.success_message').html(content.msg);				
			} else {
				$('#success_message').html(content.msg);

			} */
		}
	});
}

function hrday(min,max){
	var startdate=$("#start_time").val();
	///$("#end_time option").removeAttr('disabled').
	//filter( "[value='12:00 AM'],[value='12:30 AM'],[value='01:00 AM'],[value='01:30 AM'],[value='02:00 AM'],[value='02:30 AM'],[value='03:00 AM'],[value='03:30 AM']" )
	//.attr( 'disabled', 'disabled' );
	$('.loader').show();
	$.ajax({
		type: "POST",
		datatype: 'json',
		url: baseUrl + 'conference/showdateinterval',
		data: {start:startdate,changehr:"hr"},
		success: function (data) {
			$("#end_time option[value='"+ data.trim() +"']").attr("selected","selected");
			$('.loader').hide();
                        endtimedata();
                       /* var content = $.parseJSON(data);
                        if (content.status == 'success') {
				$('.success_message').html(content.msg);				
			} else {
				$('#success_message').html(content.msg);

			} */
		}
	});
}

function starttimechange(){
	var ch="";
	var hr="";
	if($('#halfday').is(':checked')){
		hr= "";
		ch="yes";
	}else if($('#allday').is(':checked')){
		hr="8";
		ch="yes";
	}
	if(ch.length <= 0){
		return false;
	}
	var startdate=$("#start_time").val();
	$('.loader').show();
	$.ajax({
		type: "POST",
		datatype: 'json',
		url: baseUrl + 'conference/showdateinterval',
		data: {start:startdate,action:"add",interval:hr},
		success: function (data) {
	
			$("#end_time option[value='"+ data.trim() +"']").attr("selected","selected");
			$('.loader').hide();
			var content = $.parseJSON(data);
			if (content.status == 'success') {
				$('.success_message').html(content.msg);				
			} else {
				$('#success_message').html(content.msg);

			}
		}
	});
}

function endtimechange(){
	var ch="";
	var hr="";
	if($('#halfday').is(':checked')){
		hr= "";
		ch="yes";
	}else if($('#allday').is(':checked')){
		hr="8";
		ch="yes";
	}
	//alert(hr);
	if(ch.length <= 0){
		return false;
	}
	var startdate=$("#end_time").val();
	var enddate=$("#start_time").val();
	$('.loader').show();
	$.ajax({
		type: "POST",
		datatype: 'json',
		url: baseUrl + 'conference/showdateinterval',
		data: {start:startdate,action:"sub",end:enddate,interval:hr},
		success: function (data) {
			if(data.trim()=='Error'){
				jAlert('End Time must be '+ hr +' hr interval to start time', 'Vision Work Orders');
			}else{
				$("#start_time option[value='"+ data.trim() +"']").attr("selected","selected");
			}
			$('.loader').hide();
			var content = $.parseJSON(data);
			if (content.status == 'success') {
				$('.success_message').html(content.msg);				
			} else {
				$('#success_message').html(content.msg);

			}
		}
	});
}

function allday(){
	var startdate=$("#start_time").val();
	//$("#end_time option[value='12:00 AM'],option[value='1:00 AM']").attr("disabled","disabled")
	$("#end_time option").removeAttr('disabled').
	filter( "[value='12:00 AM'],[value='12:30 AM'],[value='01:00 AM'],[value='01:30 AM'],[value='02:00 AM'],[value='02:30 AM'],[value='03:00 AM'],[value='03:30 AM'],[value='04:00 AM'],[value='04:30 AM'],[value='05:00 AM'],[value='05:30 AM'],[value='06:00 AM'],[value='06:30 AM'],[value='07:00 AM'],[value='07:30 AM']" )
	.attr( 'disabled', 'disabled' );
	$('.loader').show();
	$.ajax({
		type: "POST",
		datatype: 'json',
		url: baseUrl + 'conference/showdateinterval',
		data: {start:startdate,action:"add",interval:"data"},
		success: function (data) {

			if(data.trim()=='Error'){
				jAlert('End Time must be 8 hr interval to start time', 'Vision Work Orders');
			}else{
				$("#end_time option[value='"+ data.trim() +"']").attr("selected","selected");
			}
			$('.loader').hide();
                        endtimedata();
			/* var content = $.parseJSON(data);
			if (content.status == 'success') {
				$('.success_message').html(content.msg);				
			} else {
				$('#success_message').html(content.msg);

			} */
		}
	});
}


function numberOnly(evt){
    var charCode=(evt.which)?evt.which:event.keyCode
    if(charCode>31&&(charCode<46||charCode>57))
        return FALSE;
 return TRUE;
}

function editbookroom(url){
    	$('.loader').hide();
    CheckForSessionpop(baseUrl);
    $('a[href="#editbookroom"]').fancybox({
        type: 'iframe',
        href: url,
        width: 680,
        height: 800,
        'beforeClose': function () {
            $('.loader').hide();
        },
        'afterLoad': function () {
            $.fancybox.hideLoading();
            
            setInterval(function () {
                $('.loader').hide();
            }, 5000);
        }
      
    });
    
}

function endtimedata(){
    var start_time=$("#start_time").val();
    var end_time=$("#endlimittime").val();
    var plan=$("input[name=schedule_id]:checked").val();
    var room_id=$('#croom').find(":selected").val();
    $.ajax({
		type: "POST",
		datatype: 'json',
		url: baseUrl + 'conference/showendtimelimit',
		data: {start:start_time,end:end_time,plan:plan,room:room_id},
		success: function (data) {
                    $("#end_time").html(data);
		}
	});
    
}

function get_rooms(){
    
   scheduler_id=$("#schedule_name").val();
   $.ajax({
		type: "POST",
		datatype: 'json',
		url: baseUrl + 'conference/multiroomselected',
		data: {sid:scheduler_id,action:"add"},
		success: function (data) {
                    $(".avaliable_room").html(data);                    
                    scheduleRat();
		}
	});
}

function edit_multi_rooms(cid){
  var scheduler_id=$("#schedule_name").val();
  if(scheduler_id.length < 1 )
      scheduler_id=cid;
        $.ajax({
		type: "POST",
		datatype: 'json',
		url: baseUrl + 'conference/multiroomselected',
		data: {sid:scheduler_id,action:"edit"},
		success: function (data) {
                    $(".avaliable_room").html(data);
		}
	});  
    
}

/* Schedule Rate*/

function scheduleRat(){    
     var secheduler_id = $('#schedule_name :selected').val();
    $.ajax({
            type: "POST",
            datatype: 'json',
            url: baseUrl + 'conference/getscheduler',
            data: {sid:secheduler_id},
            success: function (data) {
                console.log(data);
                var to = JSON.parse(data);
                $("#total").val(to.total);
                $(".plan_status").each(function () {
                    if ($(this).is(':checked')) {                        
                        makechanges($(this).val());
                    }
                });

            }
    });
}

function showminmax(obj){
    //alert($(obj).attr('checked'));
    if($(obj).attr('checked')){
        var type = $(obj).val();
        //alert(type);
        makechanges(type);
    }else{
        var type = $(obj).val();
        $("#cost_"+type).val('');
        $("#min_"+type).val('');
        $("#max_"+type).val('');
    }
}

function makechanges(id){
    var total= ($("#total").val()).trim();
    
    if(id.trim()==1){
        $("#cost_1").val('0');
        $("#min_1").val('1');
        $("#max_1").val(total);
        return true;
    }
    if(id.trim()==2){
        $("#cost_2").val('0');
        
        if(total <= 4 ){
            $("#min_2").val(total);
        }else{
            $("#min_2").val('4');
        }
        
        $("#max_2").val(total);
        
        return true;
    }
    if(id.trim()==3){
        $("#cost_3").val('0');        
        if(total <= 8 ){
            $("#min_3").val(total);
        }else{
            $("#min_3").val('8');
        }
        
        $("#max_3").val(total);
        
        return true;
    }
    
}

function edit_scheduleRat(){
    var secheduler_id = $('#schedule_name :selected').val();
    $.ajax({
            type: "POST",
            datatype: 'json',
            url: baseUrl + 'conference/getscheduler',
            data: {sid:secheduler_id},
            success: function (data) {
                var to = JSON.parse(data);
                $("#total").val(to.total);
            }
    });
}


    function rec_tenant_users(){
       // console.log(obj);
        //$("#rec_tenant_users").attr("checked","checked");
        $("#rec_tenant_admin").attr("checked","checked");
        $("#rec_building_users").attr("checked","checked");
    }
    function rec_tenant_admin(){
        //console.log(obj);
        $("#rec_tenant_users").attr("checked",false);
        //$("#rec_tenant_admin").attr("checked","checked");
        $("#rec_building_users").attr("checked","checked");
    }
//// Recurrence
//$("#rec_tenant_users").click(function(){
//    alert(12);
//    //$("#rec_tenant_users").attr("checked","checked");
//    //$("#rec_tenant_admin").attr("checked","checked");
//    //$("#rec_building_users").attr("checked","checked");
//});




