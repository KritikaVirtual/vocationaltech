$(function () {
    $(".modalbox").fancybox({'openEffect': 'none', fitToView: true});
});

function checkSearch()
{
    var search_key = $('#search_by').val();
    var search_value = $('#search_value').val();
    var service_value = $('#service_value').val();
    var submit_flag = false;
    if (search_key != 'services') {
        if (search_value == '') {
            $('#search_message').html('Enter serach value.');
        } else
            submit_flag = true;
    } else {
        if (service_value == '') {
            $('#search_message').html('Select Service Option.');
        } else
            submit_flag = true;
    }

    if (submit_flag) {
        return true;
    } else {
        return false;
    }
}

function showEditModule(mid)
{
    CheckForSessionpop(baseUrl);
    $('.loader').show();
    $.ajax({
        type: "POST",
        url: baseUrl + "module/editmodule",
        data: {
            mid: mid,
        },
        success: function (msg) {
            $("#show_module").html(msg);
            $('#show_module_href').trigger('click');
            $('.loader').hide();
        },
        error: function () {
            setInterval(function () {
                jAlert('error', 'Vision Work Orders');
            }, 1100);

        }
    });
}

function cancelEditModule()
{
    $("div.fancybox-close").trigger("click");
    $('#show_module').html('');
    $('#show_module').hide();
}

function updateModule(module_id)
{
    CheckForSessionpop(baseUrl);
    $('.loader').show();
    var status = $('#status').val();

    $.ajax({
        type: "POST",
        url: baseUrl + "module/updatemodule",
        datatype: 'json',
        data: {module_id: module_id, status: status},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                $('#success_msg').html(content.msg);
                setTimeout(function () {
                    location.reload();
                }, 1000);
            } else {
                $('#error_msg').html(content.msg);
                $('#save_rate').attr('disabled', false);
            }
        },
        error: function () {
            setInterval(function () {
                jAlert('error', 'Vision Work Orders');
            }, 1100);

        }
    });
}

function deleteModule(module_id)
{
    var check_delete = 'YES';
    jPrompt('For Deleting Module, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {
            if (check_delete === return_value) {
                $('.loader').show();
                $.ajax({
                    type: "POST",
                    url: baseUrl + "module/deletemodule",
                    dataType: 'json',
                    data: {
                        module_id: module_id
                    },
                    success: function (msg) {
                        $('.loader').hide();
                        if (msg == true) {
                            $('#module_success').html('Module has been deleted successfully.');
                            location.refresh(true);
                            location.reload();
                        } else {
                            $('#module_error').html('Error Occurred during deletion of Module.');
                        }
                    }

                });
            } else {
                jAlert('You have entered wrong word.', 'Vision Work Orders');
            }
        }
    });
}

function showModuleDetails(module_id) {
    $('.loader').show();
    hideAllCompany();
    $('#open_div_' + module_id).hide();
    $('#close_div_' + module_id).show();
    $.ajax({
        type: "POST",
        url: baseUrl + "module/loadmodulecomp",
        datatype: 'json',
        data: {module_id: module_id},
        success: function (data) {
            $('.loader').hide();
            if (data) {
                $('#loadcompany_' + module_id).html(data);
                $('#trId_' + module_id).show();
            } else {
                jAlert('There was an error');
            }
        },
        error: function () {
            setTimeout(function () {
                jAlert('error', 'Vision Work Orders');
            }, 1100);

        }
    });

}
function showModulebuildings(module_id, cust_id) {
    $('.loader').show();
    hideAllBuilding();
    $('#open_div_' + module_id + '_' + cust_id).hide();
    $('#close_div_' + module_id + '_' + cust_id).show();
    $.ajax({
        type: "POST",
        url: baseUrl + "module/loadmodulebuildings",
        datatype: 'json',
        data: {module_id: module_id, cust_id: cust_id},
        success: function (data) {
            $('.loader').hide();
            if (data) {
                $('#loadbuilding_' + module_id + '_' + cust_id).html(data);
                $('#building_trId_' + module_id + '_' + cust_id).show();
            } else {
                jAlert('There was an error');
            }
        },
        error: function () {
            setTimeout(function () {
                jAlert('error', 'Vision Work Orders');
            }, 1100);

        }
    });

}


function editBuilding(cust_id, build_id) {
    var height = $(".container-right").height();
    var tot_height = parseInt(parseInt(height) + 560);

    $.ajax({
        type: "POST",
        url: baseUrl + "building/editbuilding",
        data: {
            buildingID: build_id,
        },
        beforeSend: function () {
            //$('.loader').show();
        },
        success: function (msg) {
            $('.loader').hide();
            $("#combuilding_popup").css('height', tot_height + 'px');
            $("#combuilding_popup").show();
            //$('#show_edit_form').show();			
            $('#show_edit_form').html(msg);
            location.hash = '#companyListData';
            $('#show_edit_form_href').trigger('click');
            return false;
        },
        error: function () {
            jAlert('error', 'Alert Dialog');
        }
    });

}


function updateBuilding() {
//console.log('this is test');
    var buildingID = $.trim($('#buildingID').val());
    var companyID = $.trim($('#companyID').val());
    var accountNumber = $.trim($('#accountNumber').val());
    var costCenter = $.trim($('#costCenter').val());
    var buildingName = $.trim($('#buildingName').val());
    var address = $.trim($('#address').val());
    var address2 = $.trim($('#address2').val());
    var city = $.trim($('#city').val());
    var state = $.trim($('#state').val());
    var postalCode = $.trim($('#postalCode').val());
    var phoneNumber = $.trim($('#phoneNumber').val());
    var phoneExt = $.trim($('#ext').val());
    var faxNumber = $.trim($('#faxNumber').val());
    var timezone = $.trim($('#timezone').val());
    var status = $('#status').val();
    var temp = 0;
    var module_id = '';
    $(".multicheckddmodule_id").each(function () {
        if ($(this).is(':checked')) {
            module_id = module_id + $(this).val() + ',';
        }

    });
    if (accountNumber == '') {
        //	$('#companyID').focus();
        //	return false;
    }

    if (costCenter == '') {
        $('#costCenter').focus();
        $('#costCenter').addClass("inputErr");
        $('.costCenterErr').html('Cost Center Required');
        return false;
        temp = 1;
    } else {
        $('#costCenter').removeClass("inputErr");
        $('.costCenterErr').html('');
    }
    if (buildingName == '') {
        $('#buildingName').focus();
        $('#buildingName').addClass("inputErr");
        $('.buildingNameErr').html('Building Name Required');
        temp = 1;
        return false;
    } else {
        $('#buildingName').removeClass("inputErr");
        $('.buildingNameErr').html('');
    }

    if (address == '') {
        $('#address').focus();
        $('#address').addClass("inputErr");
        $('.addressErr').html('Address Required');
        temp = 1;
        return false;
    } else {
        $('#address').removeClass("inputErr");
        $('.addressErr').html('');
    }
    if (city == '') {
        $('#city').focus();
        $('#city').addClass("inputErr");
        $('.cityErr').html('City Required');
        temp = 1;
        return false;
    } else {
        $('#city').removeClass("inputErr");
        $('.cityErr').html('');
    }
    if (state == '') {
        $('#state').focus();
        $('#state').addClass("inputErr");
        $('.stateErr').html('State Required');
        temp = 1;
        return false;
    } else {
        $('#state').removeClass("inputErr");
        $('.stateErr').html('');
    }
    if (postalCode == '') {
        $('#postalCode').focus();
        $('#postalCode').addClass("inputErr");
        $('.postalCodeErr').html('Postal Code Required');
        temp = 1;
        return false;
    } else if (postalCode.length < 5) {
        $('#postalCode').focus();
        $('#postalCode').addClass("inputErr");
        $('.postalCodeErr').html('Please enter 5 digits Postal Code');
        temp = 1;
        return false;
    } else {
        $('#postalCode').removeClass("inputErr");
        $('.postalCodeErr').html('');
    }
    if (phoneNumber == '') {
        $('#phoneNumber').focus();
        $('#phoneNumber').addClass("inputErr");
        $('.phoneNumberErr').html('Phone Number Required');
        temp = 1;
        return false;
    } else if (phoneNumber.length < 12) {
        $('#phoneNumber').focus();
        $('#phoneNumber').addClass("inputErr");
        $('.phoneNumberErr').html('Please enter 10 digits Phone Number');
        temp = 1;
        return false;
    } else {
        $('#phoneNumber').removeClass("inputErr");
        $('.phoneNumberErr').html('');
    }

    if (faxNumber.length > 0 && faxNumber.length < 12) {
        $('#faxNumber').focus();
        $('#faxNumber').addClass("inputErr");
        $('.faxNumberErr').html('Please enter 10 digits Fax Number Required');
        temp = 1;
        return false;
    } else {
        $('#faxNumber').removeClass("inputErr");
        $('.faxNumberErr').html('');
    }

    if (temp == 1) {
        return false;
    } else {

        $.ajax({
            type: "POST",
            url: baseUrl + "building/updatebuilding",
            dataType: 'json',
            data: {
                buildingID: buildingID,
                companyID: companyID,
                accountNumber: accountNumber,
                costCenter: costCenter,
                buildingName: buildingName,
                address: address,
                address2: address2,
                city: city,
                state: state,
                postalCode: postalCode,
                phoneNumber: phoneNumber,
                phoneExt: phoneExt,
                faxNumber: faxNumber,
                timezone: timezone,
                status: status,
                module_id: module_id
            },
            beforeSend: function () {
                $('.loader').show();
            },
            success: function (msg) {
                $('.loader').hide();
                if (msg.status == 'success') {
                    $('#msg').html(msg.message);
                    //showBuildingList(companyID, 'open');
                    $("div.fancybox-close").trigger("click");
                } else if (msg.status == 'build_error') {
                    $('#buildingName').focus();
                    $('#buildingName').addClass("inputErr");
                    $('.buildingNameErr').html(msg.message);
                } else {
                    $('#msg').html(msg.message);
                }

                //alert(msg);
                //	window.location.reload();

            },
            error: function () {
                jAlert('error', 'Alert Dialog');
            }
        });
        return false;
    }
}

function hideAllBuilding() {
    $('.open_plus2').show();
    $('.open_close2').hide();
    $('.trbuilding-class').hide();
    $('.tdbuilding-class').html('');
}

function hideAllCompany() {
    $('.open_plus').show();
    $('.open_close').hide();
    $('.trtenant-class').hide();
    $('.tdtenant-class').html('');
}

function hideModuleBuild(module_id) {
    $('#open_div_' + module_id).show();
    $('#close_div_' + module_id).hide();
    $(".message").html('');
    $(".error-msg").html('');
    $('#loadtenant_' + module_id).html('');
    $('#trId_' + module_id).hide();

}

function cancelForm(id) {
    $("div.fancybox-close").trigger("click");
    location.hash = '';
    location.hash = 'compID-' + id;
    $('#show_edit_form').hide();
    $('#show_edit_form').html('');
    $("#combuilding_popup").hide();
    //showBuildingList(id, 'open');
    return false;
}

function deletebuilding(module_id, building_id) {
    var check_delete = 'YES';
    jPrompt('For Deleting Building From Module, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {
            if (check_delete === return_value) {
                $('.loader').show();
                $.ajax({
                    type: "POST",
                    url: baseUrl + "module/deletemodulebuilding",
                    dataType: 'json',
                    data: {
                        module_id: module_id,
                        building_id: building_id
                    },
                    success: function (msg) {
                        $('.loader').hide();
                        if (msg == true) {
                            $('.loader').hide();
                            $('#module_success').html('Building has been deleted successfully from module.');
                            //location.refresh(true);
                            location.reload();
                        } else {
                            $('.loader').hide();
                            $('#module_error').html('Error Occurred during deletion of Module.');
                        }
                    }

                });
            } else {
                jAlert('You have entered wrong word.', 'Vision Work Orders');
            }
        }
    });

}