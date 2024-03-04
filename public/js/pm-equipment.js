function openpopup(url)
{
    CheckForSessionpop(baseUrl);
    $('a[href="#CreateNewMultiCon"]').fancybox({
        type: 'iframe',
        href: url,
        width: 700,
        height: 600,
        helpers: {
            overlay: {closeClick: false}
        },
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

// Close popup cancel button event //
function canceltemplate() {
    parent.jQuery.fancybox.close();
}
// add equipment
function addequipment(url)
{
    CheckForSessionpop(baseUrl);
    $('a[href="#CreateNewMultiCon"]').fancybox({
        type: 'iframe',
        href: url,
        width: 770,
        height: 724,
        overlayShow: false,
        beforeShow: function () {
            $.fancybox.hideLoading();
            $('.loader').show();
            var obj = this;
            $('.loader').hide();
            var x = obj.height = parent.$(".fancybox-iframe").contents().find("body").height();
            var y = obj.width = parent.$(".fancybox-iframe").contents().find("body").width();
            $(".fancybox-iframe").css('height', x + 'px');
            $(".fancybox-iframe").css('width', y + 'px');
            $(".fancybox-wrap").addClass('fancybox-parent');
        },
        helpers: {
            overlay: {closeClick: false}
        },
        'beforeClose': function () {
            $('.loader').hide();
        },
        
    });
}
// PM Work Order Options
function pmSetupAndOption(url)
{
    CheckForSessionpop(baseUrl);
    $('a[href="#CreateNewMultiCon"]').fancybox({
        type: 'iframe',
        href: url,
        width: 900,
        height: 724,
        overlayShow: false,
        beforeShow: function () {
            $.fancybox.hideLoading();
            $('.loader').show();
            var obj = this;
            $('.loader').hide();
            var x = obj.height = parent.$(".fancybox-iframe").contents().find("body").height();
            x = x + 110;
            var y = obj.width = parent.$(".fancybox-iframe").contents().find("body").width();
            $(".fancybox-iframe").css('height', x + 'px');
            $(".fancybox-iframe").css('width', y + 'px');
            $(".fancybox-wrap").addClass('fancybox-parent');
        },
        helpers: {
            overlay: {closeClick: false}
        },
        'beforeClose': function () {
            $('.loader').hide();
        }

    });
}

$(document).ready(function () {
    $(".confirm").on("click", function () {
        $("body").addClass('dynamicclass');
    });
    
    $(".dyn").on("click", function () {
        $("body").addClass('dynamicclass');
    });
    
    $(".slidingDiv").hide();
    $("#eqname").change();
});


function CheckForSessionpop(baseUrl) {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
    var str = "chksession=true";
    jQuery.ajax({
        type: "POST",
        url: baseUrl + "user/checksession",
        data: str,
        cache: false,
        success: function (res) {
            if (parseInt(res) != 1) {
                $('#popup_container').show();
                $(".fancybox-wrap ").empty();
                $('#fancybox-overlay').remove();

                if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
                    document.execCommand("Stop");
                } else {

                    document.location.href = baseUrl + "/index/logout";
                    window.stop();
                }

                document.location.href = baseUrl + "/index/logout";
                return false;
            }
        }
    });
}

// Create template Validation
function validatequipment(action) {
    var letters = /^[0-9a-zA-Z]+$/; //This is for Alphanumeric

    var equipmentnamedata = $('#browsers option[value="' + $('#equipmentname').val() + '"]').val();
    if (equipmentnamedata != "undefined") {
        equipmentnamedata = $('#equipmentname').val();
    }
    //console.log(equipmentnamedata);
    var attach_template_name = $("#attach_template option:selected").text();

    var equipment_id = equipmentname = $('#browsers option[value="' + $('#equipmentname').val() + '"]').attr('id');
    if (equipmentname != "undefined") {
        equipmentname = $('#equipmentname').val();
    }
    var unit = $("#unit").val().trim();
    var floor = $("#floor").val().trim();
    var location = $("#location").val().trim();
    var serialnumber = $("#serialnumber").val().trim();
    var makemodel = $("#makemodel").val().trim();
    var inservicedate = $("#inservicedate").val().trim();
    var note = $("#note").val().trim();
    var equipmentmenual = $("#equipmentmenual").val().trim();
    var imagename = $("#imagename").val().trim();
    var status = $("#status").val().trim();
    var attach_template = $("#attach_template").val();

    // var userData = new FormData();
    var isValid = true;
    var manual = $("#equipmentmenual").prop('files')[0];
    //console.log(manual);
    //var flag_val = escape($("#equipmentmenual").attr('name'));
    // validation section start
    //console.log(attach_template);
    if (attach_template == '-1') {
        $("#attach_template").focus();
        $("#attach_template").addClass('error-border');
        isValid = false;
    } else {
        $("#attachtemplate_error").html("");
        $("#attach_template").removeClass('error-border');

    }
    if (equipmentname == "") {
        $("#templateName").focus();
        $("#equipmentname").addClass('error-border');
        //$("#equipmentname_error").html("Please Select Equipment Name");
        isValid = false;
    } else {
        $("#equipmentname_error").html("");
        $("#equipmentname").removeClass('error-border');
    }
    if (unit == "") {
        $("#unit").focus();
        $("#unit").addClass('error-border');
        //$("#unit_error").html("Please Enter unit Name");
        isValid = false;
    } else if (!unit.match(letters)) {
        $("#unit").focus();
        $("#unit").addClass('error-border');
        $("#unit_error").html("Please Enter unit in alphanumeric only");
        return false;

    } else {
        $("#unit").removeClass('error-border');
        $("#unit_error").html("");
    }
    if (floor == "") {
        $("#floor").focus();
        $("#floor").addClass('error-border');
        //$("#floor_error").html("Please Enter Floor Name");
        isValid = false;
    } else if (!floor.match(letters)) {
        $("#floor").focus();
        $("#floor").addClass('error-border');
        $("#floor_error").html("Please Enter floor in alphanumeric only");
        return false;

    } else {
        $("#floor").removeClass('error-border');
        $("#floor_error").html("");
    }
    if (location == "") {
        $("#location").focus();
        $("#location").addClass('error-border');
        //$("#location_error").html("Please Enter Location Name");
        isValid = false;
    } else {
        $("#location").removeClass('error-border');
        $("#location_error").html("");
    }

    if (typeof manual !== 'undefined') {
        var ext = manual.name.split(".");
        var type_ext = ext[ext.length - 1];
        //console.log(type_ext);
        if (type_ext !== 'pdf' && type_ext !== 'PDF') {
            $("#equipmentmenual").focus();
            $("#equipmentmenual").addClass('error-border');
            $("#equipmentmenual_error").html("Please upload Only a PDF File");
            isValid = false;
        } else {
            $("#equipmentmenual_error").html("");
            $("#equipmentmenual").removeClass('error-border');
        }
    }

    if (isValid === false) {
        return false;
    } else {

//$('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + 'pm/validatsfloorandunit',
            data: {equipment_id: equipment_id, unit: unit, floor: floor},
            success: function (msg) {
                //console.log(msg);
                $('.loader').hide();
                if (msg.trim() == 'true') {

                    $("#first").hide();
                    $("#second").show();
                    $("#viewEquipment").html(equipmentnamedata);
                    $("#viewAttach").html(attach_template_name);
                    if (status == 1) {
                        showstatus = "Active";
                    } else {
                        showstatus = "Deactive";
                    }
                    $("#viewStatus").html(showstatus);
                    $("#viewUnit").html(unit);
                    $("#viewFlor").html(floor);
                    return true;

                } else {
                    //$('.loader').hide();
                    //console.log("already exit");
                    $('#unit').focus();
                    $("#unit").addClass('error-border');
                    $("#unit_error").html("The unit number has already been assigned.");
                    return false;
                }
            }
        });
    }
}
// Create template Validation
function validatemultipleequipment(action) {
    var letters = /^[1-9][0-9]*$/; //This is for Numeric only
        
    //console.log(equipmentnamedata);
    var attach_template_name = $("#attach_template option:selected").text();

    /*var equipment_id = equipmentname = $('#browsers option[value="' + $('#equipmentname').val() + '"]').attr('id');
     if (equipmentname != "undefined") {
     equipmentname = $('#equipmentname').val();
     }*/
    var equipmentname = $("#equipmentname").val().trim();
    var unit = $("#unit").val().trim();
    var floor = $("#floor").val().trim();
    var location = $("#location").val().trim();
    var serialnumber = $("#serialnumber").val().trim();
    var makemodel = $("#makemodel").val().trim();
    var inservicedate = $("#inservicedate").val().trim();
    var note = $("#note").val().trim();
    var equipmentmenual = $("#equipmentmenual").val().trim();
    var imagename = $("#imagename").val().trim();
    var status = $("#status").val().trim();
    var attach_template = $("#attach_template").val();

    // var userData = new FormData();
    var isValid = true;
    var manual = $("#equipmentmenual").prop('files')[0];
    //console.log(manual);
    //var flag_val = escape($("#equipmentmenual").attr('name'));
    // validation section start
    //console.log(attach_template);
    if (attach_template == '-1') {
        $("#attach_template").focus();
        $("#attach_template").addClass('error-border');
        isValid = false;
    } else {
        $("#attachtemplate_error").html("");
        $("#attach_template").removeClass('error-border');

    }
    if (equipmentname == "") {
        $("#templateName").focus();
        $("#equipmentname").addClass('error-border');
        //$("#equipmentname_error").html("Please Select Equipment Name");
        isValid = false;
    } else {
        $("#equipmentname_error").html("");
        $("#equipmentname").removeClass('error-border');
    }
    if (unit == "") {
        $("#unit").focus();
        $("#unit").addClass('error-border');
        //$("#unit_error").html("Please Enter unit Name");
        isValid = false;
    } else if (!unit.match(letters)) {
        $("#unit").focus();
        $("#unit").addClass('error-border');
        $("#unit_error").html("Please Enter unit in numeric only");
        return false;

    } else {
        $("#unit").removeClass('error-border');
        $("#unit_error").html("");
    }
    if (floor == "") {
        $("#floor").focus();
        $("#floor").addClass('error-border');
        //$("#floor_error").html("Please Enter Floor Name");
        isValid = false;
    } else if (!floor.match(letters)) {
        $("#floor").focus();
        $("#floor").addClass('error-border');
        $("#floor_error").html("Please Enter floor in numeric only");
        return false;

    } else {
        $("#floor").removeClass('error-border');
        $("#floor_error").html("");
    }
    if (location == "") {
        $("#location").focus();
        $("#location").addClass('error-border');
        //$("#location_error").html("Please Enter Location Name");
        isValid = false;
    } else {
        $("#location").removeClass('error-border');
        $("#location_error").html("");
    }

    if (typeof manual !== 'undefined') {
        var ext = manual.name.split(".");
        var type_ext = ext[ext.length - 1];
        //console.log(type_ext);
        if (type_ext !== 'pdf' && type_ext !== 'PDF') {
            $("#equipmentmenual").focus();
            $("#equipmentmenual").addClass('error-border');
            $("#equipmentmenual_error").html("Please upload Only a PDF File");
            isValid = false;
        } else {
            $("#equipmentmenual_error").html("");
            $("#equipmentmenual").removeClass('error-border');
        }
    }

    if (isValid === false) {
        return false;
    } else {

        $.ajax({
            type: "POST",
            url: baseUrl + 'pm/validateequipmentname',
            data: {EquipmentName: equipmentname},
            success: function (msg) {
                //console.log(msg);
                $('.loader').hide();
                if (msg.trim() == 'true') {
                    $("#first").hide();
                    $("#second").show();
                    $("#viewEquipment").html(equipmentname);
                    $("#viewAttach").html(attach_template_name);
                    if (status == 1) {
                        showstatus = "Active";
                    } else {
                        showstatus = "Deactive";
                    }
                    $("#viewStatus").html(showstatus);
                    $("#viewUnit").html(unit);
                    $("#viewFlor").html(floor);
                    return true;

                } else {
                    //$('.loader').hide();
                    //console.log("already exit");
                    $('#equipmentname').focus();
                    $("#equipmentname").addClass('error-border');
                    $("#equipmentname_error").html("Already exist.");
                    return false;
                }
            }
        });
    }
}
// Create Second stepe template Validation
function validatequipment_second(action) {

    var userData = new FormData();
    var equipmentnamedata = $('#browsers option[value="' + $('#equipmentname').val() + '"]').val();
    if (equipmentnamedata != "undefined") {
        equipmentnamedata = $('#equipmentname').val();
    }
    //console.log(equipmentnamedata);
    var attach_template_name = $("#attach_template option:selected").text();

    equipmentname = $('#browsers option[value="' + $('#equipmentname').val() + '"]').attr('id');
    if (equipmentname != "undefined") {
        equipmentname = $('#equipmentname').val();
    }
    var unit = $("#unit").val().trim();
    var floor = $("#floor").val().trim();
    var location = $("#location").val().trim();
    var serialnumber = $("#serialnumber").val().trim();
    var makemodel = $("#makemodel").val().trim();
    var inservicedate = $("#inservicedate").val().trim();
    var note = $("#note").val().trim();
    var equipmentmenual = $("#equipmentmenual").val().trim();
    var imagename = $("#imagename").val().trim();
    var status = $("#status").val().trim();
    var attach_template = $("#attach_template").val();
    var build_id = $("#build_id").val();
    var startdate = $("#startdate").val();
    var venderid = "";
    var groupid = "";
    var assignto = "";

    var contract = $("#Contract").prop("files")[0];
    var flag_contract = escape($("#Contract").attr('name'));
    var manual = $("#equipmentmenual").prop('files')[0];
    var flag_val = escape($("#equipmentmenual").attr('name'));
    
    if ($("#assignto1:checked").length > 0)
    {
        assignto = $("#assignto").val();
        var outsidevendor = 'No';
        //console.log("first checked");

    } else {
        //console.log("else section");
        var outsidevendor = 'Yes';
        var venderid = $("#venderid").val();
        var assignto = $("#groupid").val();
        if (location == "") {
            $("#venderid").focus();
            $("#venderid").addClass('error-border');
            return false;
        } else {
            $("#venderid").removeClass('error-border');
        }
        if (venderid == "") {
            $("#venderid").focus();
            $("#venderid").addClass('error-border');
            return false;
        } else {
            $("#venderid").removeClass('error-border');
        }

        if (typeof contract !== 'undefined') {
            var ext = contract.name.split(".");
            var type_ext = ext[ext.length - 1];
            if (type_ext !== 'pdf' && type_ext !== 'PDF') {
                $("#Contract").focus();
                $("#Contract").addClass('error-border');
                $("#Contract_error").html("Please upload Only a PDF File");
                return false;
            } else {
                $("#Contract_error").html("");
                $("#Contract").removeClass('error-border');
            }
        }
    }

    userData.append('file[' + flag_val + ']', manual);
    userData.append('file[' + flag_contract + ']', contract);
    userData.append('AU_Equipment_Name', equipmentname);
    userData.append('Equipment_Floor', floor);
    userData.append('Equipment_Unit', unit);
    userData.append('Equipment_Make_Model', makemodel);
    userData.append('Equipment_Location', location);
    userData.append('Equipment_Serial_Number', serialnumber);
    userData.append('Equipment_Inservice_Date', inservicedate);
    userData.append('Equipment_Notes', note);
    userData.append('Equipment_Status', status);
    userData.append('Equipment_Image', imagename);
    userData.append('designation_id', attach_template);
    userData.append('build_id', build_id);
    userData.append('startdate', startdate);
    userData.append('assignto', assignto);
    userData.append('venderid', venderid);
    userData.append('outsidevendor', outsidevendor);

    $.ajax({
        url: baseUrl + 'pm/saveequipment',
        type: "post",
        datatype: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: userData,
        success: function (data) {
            //console.log(data);
            $('.loader').hide();
            //var content = $.parseJSON(data);
            $("#tempalte-detail-view-change").html('Data Save successfully!');
            if (action == 'new') {
                parent.jQuery.fancybox.close();
                parent.$("#add_equp").trigger("click");
            } else {
                parent.jQuery.fancybox.close();
                parent.window.location.reload();
            }
        }
    });
}

// Create Second stepe multiple equipment Validation
function validatemultipleequipment_second(action) {

    var userData = new FormData();
    var attach_template_name = $("#attach_template option:selected").text();
    var equipmentname = $("#equipmentname").val().trim();
    var unit = $("#unit").val().trim();
    var floor = $("#floor").val().trim();
    var location = $("#location").val().trim();
    var serialnumber = $("#serialnumber").val().trim();
    var makemodel = $("#makemodel").val().trim();
    var inservicedate = $("#inservicedate").val().trim();
        var note = $("#note").val().trim();
    var equipmentmenual = $("#equipmentmenual").val().trim();
    var imagename = $("#imagename").val().trim();
    var status = $("#status").val().trim();
    var attach_template = $("#attach_template").val();
    var build_id = $("#build_id").val();
    var startdate = $("#startdate").val();
    var venderid = "";
    var groupid = "";
    var assignto = "";
    var contract = $("#Contract").prop("files")[0];
    var flag_contract = escape($("#Contract").attr('name'));
    var manual = $("#equipmentmenual").prop('files')[0];
    var flag_val = escape($("#equipmentmenual").attr('name'));

    if ($("#assignto1:checked").length > 0)
    {
        assignto = $("#assignto").val();
        var outsidevendor = 'No';

    } else {
        var outsidevendor = 'Yes';
        var venderid = $("#venderid").val();
        var assignto = $("#groupid").val();
        if (location == "") {
            $("#venderid").focus();
            $("#venderid").addClass('error-border');
            return false;
        } else {
            $("#venderid").removeClass('error-border');
        }
        if (venderid == "") {
            $("#venderid").focus();
            $("#venderid").addClass('error-border');
            return false;
        } else {
            $("#venderid").removeClass('error-border');
        }

        if (typeof contract !== 'undefined') {
            var ext = contract.name.split(".");
            var type_ext = ext[ext.length - 1];
            if (type_ext !== 'pdf' && type_ext !== 'PDF') {
                $("#Contract").focus();
                $("#Contract").addClass('error-border');
                $("#Contract_error").html("Please upload Only a PDF File");
                return false;
            } else {
                $("#Contract_error").html("");
                $("#Contract").removeClass('error-border');
            }
        }
    }

    userData.append('file[' + flag_val + ']', manual);
    userData.append('file[' + flag_contract + ']', contract);
    userData.append('AU_Equipment_Name', equipmentname);
    userData.append('Equipment_Floor', floor);
    userData.append('Equipment_Unit', unit);
    userData.append('Equipment_Make_Model', makemodel);
    userData.append('Equipment_Location', location);
    userData.append('Equipment_Serial_Number', serialnumber);
    userData.append('Equipment_Inservice_Date', inservicedate);
    userData.append('Equipment_Notes', note);
    userData.append('Equipment_Status', status);
    userData.append('Equipment_Image', imagename);
    userData.append('designation_id', attach_template);
    userData.append('build_id', build_id);
    userData.append('startdate', startdate);
    userData.append('assignto', assignto);
    userData.append('venderid', venderid);
    userData.append('outsidevendor', outsidevendor);

    $.ajax({
        url: baseUrl + 'pm/savemultipleequipment',
        type: "post",
        datatype: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: userData,
        success: function (data) {
            //console.log(data);
            $('.loader').hide();
            //var content = $.parseJSON(data);
            $("#tempalte-detail-view-change").html('Data Save successfully!');
            if (action == 'new') {
                parent.jQuery.fancybox.close();
                parent.$("#add_multipleequp").trigger("click");
            } else {
                parent.jQuery.fancybox.close();
                parent.window.location.reload();
            }
        }
    });
}

// Close popup cancel button event //
function closepopup() {
    parent.$("body").removeClass("dynamicclass");
    parent.jQuery.fancybox.close();
}
function backequipment() {
    $("#first").show();
    $("#second").hide();
}

$(".date-picker")
        .datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            changeYear: true,
            minDate: 0,
            showButtonPanel: true,
            numberOfMonths: 1
        });

$(".date-picker-with-old")
        .datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            changeYear: true,
            //minDate: 0,
            showButtonPanel: true,
            numberOfMonths: 1
        });


// upload task and reading data

//attach_template();

function attach_template() {
    $('.loader').show();
    var type = "AU";
    var design_id = $("#attach_template option:selected").val();
    console.log(design_id);
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/importdesiganitiontemplate',
        //async: true,
        data: {design_id: design_id, type: type},
        /*beforeSend: function () {
            $('.loader').show();
        },
        complete: function () {
            $('.loader').hide();
        },*/
        success: function (data) {
          
            //$("#tempalte-detail-view-change").html(data);
             $.ajax({
                 type: "POST",
                 url: baseUrl + 'pm/importdesiganitiontemplatechange',
                 async: true, //Now perform a synchronous request 
                 data: {design_id: design_id, type: type},
                 beforeSend: function () {
                     $('.loader').show();
                 },
                 complete: function () {
                     $('.loader').hide();
                 },
                 success: function (data) {
                     //console.log(data);
                     //$('.loader').hide();
                     $("#tempalte-detail-view-change").html(data);
                     

                 }
             });
            if (data.length > 6460) {
                //var html = "<button class='confirm active' id='task-btn' onclick='view_task_section();'> Task </button><button class='confirm' id='reading-btn' onclick='view_reading_section();'> Reading </button>";
                var html = "";
                $("#data-available").html(html);
                $("#data-available").css("display", "block");
            } else {
                var html = "<div style='text-align: center;'>Data are not available!</div>";
                $("#data-available").html(html);
            }
            $('.loader').hide();
            $("#tempalte-detail-view").html(data);
            $("script[src='jquery.maskedinput.js']").remove();

        }
    });
}

function attach_template_reload() {
    $('.loader').show();
    var type = "AU";
    var design_id = $("#attach_template option:selected").val();
    //console.log(design_id);
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/importdesiganitiontemplatechange',
        data: {design_id: design_id, type: type},
        success: function (data) {
            //console.log(data);
            $('.loader').hide();
            //closepopup();
            jQuery.fancybox.close();
            $("#tempalte-detail-view-change").html(data);

        }
    });
    //console.log(data);
    $('.loader').hide();
    $("#tempalte-detail-view").html(data);

}

$("#file").change(function () {
    console.log("Change event");
    $(".submit").trigger("click");
});


$("#image1").on('submit', (function (e) {
    e.preventDefault();

    var file = document.getElementById('file').files[0];
    //console.log(file);
    if (typeof file !== 'undefined') {
        var ext = file.name.split(".");
        var type_ext = ext[ext.length - 1];
        //console.log(type_ext);
        if (type_ext !== 'jpg' && type_ext !== 'jpeg' && type_ext !== 'bmp' && type_ext !== 'png') {
            $("#file").focus();
            $("#file").addClass('error-border');
            $("#file_error").html("Please upload Only a jpg, jpeg, bmp, png formats only");
            return false;
        } else {
            $("#file_error").html("");
            $("#file").removeClass('error-border');
        }
    }

    $("#imagename").val(file.name);
    $.ajax({
        url: baseUrl + 'pm/uploadimage', // Url to which the request is send
        type: "POST", // Type of request to be send, called as method
        data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
        contentType: false, // The content type used when sending data to the server.
        cache: false, // To unable request pages to be cached
        processData: false, // To send DOMDocument or non processed data file it is set to false
        success: function (data)   // A function to be called if request succeeds
        {
            if (data.trim() == 'true') {
                var html = '<img style="width: 175px;height: 175px;margin-bottom: 6px;" src="' + baseUrl + 'public/pm/' + file.name + '">';
                $(".viewimage").html(html);
            } else {

            }
            console.log(data);
            $('#loading').hide();
        }
    });
}));

$('#browsers option').each(function () {
    if ($(this).is(':selected')) {
        var equipmentname = $(this).val().trim();
        // Your code here with the selected value
        var opt = $('option[value="' + $(this).val() + '"]');
        alert(opt.length ? opt.attr('value') : 'NO OPTION');
    }
});

/* function to get vender details */

function getvenderdetails() {
    vid = $("#venderid").val();
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/getvenderdetails',
        data: {vid: vid},
        success: function (data) {
            var vdata = $.parseJSON(data);
            console.log(vdata);
            $("#vname").html(vdata[0].first_name + ' ' + vdata[0].last_name);
            $("#vemail").html(vdata[0].email);
            $("#vphone").html(vdata[0].phone_number);
            $("#vcell").html(vdata[0].cell_number);
            $("#vaddress").html(vdata[0].address1);
            $('.loader').hide();
        }
    });
}
function getDateUpdate() {
    $('.loader').show();
    var dateToUpdate = $("#startdate").val();

    if (dateToUpdate == '') {
        $("#startdate").focus();
        $("#startdate").addClass('error-border');
        $("#startdate_error").html("Please select Date");
        return false;
    } else {
        $("#startdate_error").html("");
        $("#startdate").removeClass('error-border');
    }

    var templateDesignId = $("#attach_template").val();
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/equipmentdateupdate',
        data: {dateToUpdate: dateToUpdate, templateDesignId: templateDesignId},
        success: function (data) {
            $('.loader').hide();
            attach_template();

        }
    });
}

// Cancel add Equipment 

function addequipmentcan()
{
    var check_delete = 'YES';
    jPrompt('For cancel Add New Equipment, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {
            if (check_delete === return_value) {
                closepopup();
            } else {
                jAlert('You have entered wrong word.', 'Vision Work Orders');
            }
        }
    });
}

/// Task root Startdate 
function update_equipmenttaskroot_startdate(id) {
    $('.loader').show();
    var startdate = $("#startdate").val();
    var includesubset = "";
    if ($('#includesubset:checked').length == 1) {
        includesubset = $('#includesubset:checked').val();
    }
    if (startdate == "") {
        $("#startdate").focus();
        $("#error_startdate").html("Please Select The Start Date");
        $('.loader').hide();
        return false;
    }
    var desig_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updatetaskrootstartdate',
        data: {desig_id: desig_id, startdate: startdate, includesubset: includesubset},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                parent.attach_template_reload();
                $('.success_message').html(content.msg);
                parent.jQuery.fancybox.close();
                parent.view_all_Task(desig_id);

            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}

/// task subset Startdate
function update_equipmenttasksubset_startdate(id) {
    $('.loader').show();
    var startdate = $("#startdate").val();
    var parent_id = $('#parent_id').val();
    if (startdate == "") {
        $("#startdate").focus();
        $("#error_startdate").html("Please Select Start Date");
        $('.loader').hide();
        return false;
    }
    var desig_id = id;

    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updatetasksubsetstartdate',
        data: {desig_id: desig_id, startdate: startdate, parent_id: parent_id},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                parent.attach_template_reload();
                $('.success_message').html(content.msg);
                parent.jQuery.fancybox.close();
                parent.view_all_Task(desig_id);


            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}

/*
 * Start date update on root 
 */
function update_equipmentreadingroot_startdate(id) {
    $('.loader').show();
    var startdate = $("#startdate").val();
    var includesubset = "";
    if ($('#includesubset:checked').val() == 1) {
        includesubset = $('#includesubset:checked').val();
    }
    if (startdate == "") {
        $("#startdate").focus();
        $("#error_startdate").html("Please Select The Start Date");
        $('.loader').hide();
        return false;
    }
    var desig_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updatereadingrootstartdate',
        data: {desig_id: desig_id, startdate: startdate, includesubset: includesubset},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                parent.attach_template_reload();
                $('.success_message').html(content.msg);
                parent.jQuery.fancybox.close();
                parent.view_all_Reading(desig_id);
                parent.$("#reading-btn-change").trigger("click");

            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}

/*
 * Start date update on Subset 
 */
function update_equipmentreadingsubset_startdate(id) {
    $('.loader').show();
    var startdate = $("#startdate").val();
    var parent_id = $('#parent_id').val();
    if (startdate == "") {
        $("#startdate").focus();
        $("#error_startdate").html("Please Select Start Date");
        $('.loader').hide();
        return false;
    }
    var desig_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updatereadingsubsetstartdate',
        data: {desig_id: desig_id, startdate: startdate, parent_id: parent_id},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                parent.attach_template_reload();
                $('.success_message').html(content.msg);
                parent.jQuery.fancybox.close();
                parent.view_all_Reading(desig_id);
                parent.$("#reading-btn-change").trigger("click");

            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}
// Task Assign root 
function update_equipmenttaskroot_assign(id) {
    $('.loader').show();
    var assignto = $("#assignto").val();
    var includesubset = "";
    if ($('#includesubset:checked').length == 1) {
        includesubset = $('#includesubset:checked').val();
    }
    var desig_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updatetaskrootassign',
        data: {desig_id: desig_id, assignto: assignto, includesubset: includesubset},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                parent.attach_template_reload();
                $('.success_message').html(content.msg);
                parent.jQuery.fancybox.close();
                parent.view_all_Task(desig_id);


            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}

// Task Assign subset 
function update_equipmenttasksubset_assign(id) {
    $('.loader').show();
    var assignto = $("#assignto").val();
    var parent_id = $("#parent_id").val();
    var desig_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updatetasksubsetassign',
        data: {desig_id: desig_id, assignto: assignto, parent_id: parent_id},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                parent.attach_template_reload();
                $('.success_message').html(content.msg);
                parent.jQuery.fancybox.close();
                parent.view_all_Task(desig_id);


            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}
/*
 * Assignto update on root 
 */
function update_equipmentreadingroot_assignto(id) {
    $('.loader').show();
    var assignto = $("#assignto").val();
    var includesubset = "";
    if ($('#includesubset:checked').val() == 1) {
        includesubset = $('#includesubset:checked').val();
    }
    var desig_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updatereadingrootassignto',
        data: {desig_id: desig_id, assignto: assignto, includesubset: includesubset},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                parent.attach_template_reload();
                $('.success_message').html(content.msg);
                parent.jQuery.fancybox.close();
                parent.view_all_Reading(desig_id);
                parent.$("#reading-btn-change").trigger("click");

            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}

/*
 * Assignto update on subset 
 */
function update_equipmentreadingsubset_assignto(id) {
    $('.loader').show();
    var assignto = $("#assignto").val();
    var parent_id = $("#parent_id").val();
    var desig_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updatereadingsubsetassignto',
        data: {desig_id: desig_id, assignto: assignto, parent_id: parent_id},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                parent.attach_template_reload();
                $('.success_message').html(content.msg);
                parent.jQuery.fancybox.close();
                parent.view_all_Reading(desig_id);
                parent.$("#reading-btn-change").trigger("click");

            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}

function assignTo() {
    var templateDesignId = $("#attach_template option:selected").val();
    var assignto = $("#assignto").val();
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/equipmentupdateassign',
        data: {templateDesignId: templateDesignId, assignto: assignto},
        success: function (data) {
            attach_template();
        }
    });
}

function sortingequipmentname() {
    var x = $("#sortingvalue").val();
    if (x == 1) {
        $("#sortingid").html('<span class="glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvalue" id="sortingvalue">');
        id = $("#sortingvalue").val();
    } else {
        $("#sortingid").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvalue" id="sortingvalue">');
        id = $("#sortingvalue").val();
    }

    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortequipmentname',
        data: {id: id},
        success: function (data) {
            console.log(data);
            $('#sortequipmentname').html(data);
        }
    });

}

function sortingbyfloor(type, equipment_name_id) {
    var x = $("#sortingvaluebyfloor").val();
    if (x == 1) {
        $("#sortingidbyfloor").html('<span class="glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvaluebyfloor" id="sortingvaluebyfloor">');
        id = $("#sortingvaluebyfloor").val();
    } else {
        $("#sortingidbyfloor").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvaluebyfloor" id="sortingvaluebyfloor">');
        id = $("#sortingvaluebyfloor").val();
    }
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortequipmentdetail',
        data: {id: id, type: type, equipment_name_id: equipment_name_id},
        success: function (data) {
            $('#mno' + equipment_name_id).html(data);
        }
    });
}

function sortingbyunit(type, equipment_name_id) {
    var x = $("#sortingvaluebyunit").val();
    if (x == 1) {
        $("#sortingidbyunit").html('<span class="glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvaluebyunit" id="sortingvaluebyunit">');
        id = $("#sortingvaluebyunit").val();
    } else {
        $("#sortingidbyunit").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvaluebyunit" id="sortingvaluebyunit">');
        id = $("#sortingvaluebyunit").val();
    }
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortequipmentdetail',
        data: {id: id, type: type, equipment_name_id: equipment_name_id},
        success: function (data) {
            $('#mno' + equipment_name_id).html(data);
        }
    });
}
function sortingbylocation(type, equipment_name_id) {
    var x = $("#sortingvaluebylocation").val();
    if (x == 1) {
        $("#sortingidbylocation").html('<span class="glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvaluebylocation" id="sortingvaluebylocation">');
        id = $("#sortingvaluebylocation").val();
    } else {
        $("#sortingidbylocation").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvaluebylocation" id="sortingvaluebylocation">');
        id = $("#sortingvaluebylocation").val();
    }
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortequipmentdetail',
        data: {id: id, type: type, equipment_name_id: equipment_name_id},
        success: function (data) {
            $('#mno' + equipment_name_id).html(data);
        }
    });
}
function plusminus(id) {
    var div = document.getElementById("xyz" + id);
    if (div.style.display !== "none") {
        div.style.display = "none";
        $("#" + id).html('<i class="fa fa fa-plus-circle" aria-hidden="true"></i>');
    } else {
        //div.style.display = "block";
        div.style.display = "table-row";
        $("#" + id).html('<i class="fa fa fa-minus-circle" aria-hidden="true"></i>');
    }
}

function expandAll() {
    $(".collapseall").show();
    $(".slidingDiv").show();
    $(".show_hide").html('<i class="fa fa fa-minus-circle" aria-hidden="true"></i>');
}

function collapseall() {
    $(".collapseall").hide();
    $('.heading-tr').addClass('blue-bg');
}

function expandEquipmentDeail(id) {
    var div = document.getElementById("eqid" + id);
    if (div.style.display !== "none") {
        div.style.display = "none";
    } else {
        div.style.display = "table-row";

    }
}
function resetForm() {
    document.getElementById("fsearch").reset();
}

function searchData() {
    var eqname = $("#eqname").val().trim();
    var eqparts = $("#eqparts").val().trim();
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/searchequipment',
        data: {eqname: eqname, eqparts: eqparts},
        success: function (data) {
            $('#sortequipmentname').html(data);
        }
    });
}

function searchfor() {
    var eqname = $("#eqname").val();
    //$('#eqparts').removeAttr('value');
    $("#eqparts").val('');        
    $.ajax({
        //type: "POST",
        dataType: 'JSON',
        url: baseUrl + 'pm/searchfor',
        data: {eqname: eqname},
        success: function (data) {
            console.log(data);
            $('#typehintvalue').html("");            
            var uniqueNames = [];
               
            for (i = 0; i < data.length; i++) {
                if (uniqueNames.indexOf(data[i].Floor) === -1) {
                    uniqueNames.push(data[i].Floor);
                }
                if (uniqueNames.indexOf(data[i].Unit) === -1) {
                    uniqueNames.push(data[i].Unit);
                }
                if (uniqueNames.indexOf(data[i].MakeModel) === -1) {
                    uniqueNames.push(data[i].MakeModel);
                }

                if (uniqueNames.indexOf(data[i].Location) === -1) {
                    uniqueNames.push(data[i].Location);
                }
                if (uniqueNames.indexOf(data[i].Template) === -1) {
                    uniqueNames.push(data[i].Template);
                }
            }
            for (i = 0; i < uniqueNames.length; i++) {
                if (uniqueNames[i] != 'null') {
                    $('#typehintvalue').append('<option id="' + uniqueNames[i] + '" value="' + uniqueNames[i] + '" >');
                    
                }
            }

        }
    });
}

function changeGroupEmailId() {
    $('.loader').show();
    var desig_id = $("#attach_template option:selected").val();
    var emailGroupId = $("#assignto").val();
    //alert(emailGroupId);
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/emailgroupidupdate()',
        data: {emailGroupId: emailGroupId, desig_id: desig_id},
        success: function (data) {
            $('.loader').hide();
            attach_template();

        }
    });
}

function outsiderchangeGroupEmailId() {
    $('.loader').show();
    var desig_id = $("#attach_template option:selected").val();
    var emailGroupId = $("#groupid").val();
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/emailgroupidupdate()',
        data: {emailGroupId: emailGroupId, desig_id: desig_id},
        success: function (data) {
            $('.loader').hide();
            attach_template();

        }
    });
}

/// Task root Startdate 
function update_equipmentdetailtaskroot_startdate(id) {
    $('.loader').show();
    var startdate = $("#startdate").val();
    var includesubset = "";
    if ($('#includesubset:checked').length == 1) {
        includesubset = $('#includesubset:checked').val();
    }
    if (startdate == "") {
        $("#startdate").focus();
        $("#error_startdate").html("Please Select The Start Date");
        $('.loader').hide();
        return false;
    }
    var eqp_detail_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updateonlystartdateofequipmentdetailtaskroot',
        data: {eqp_detail_id: eqp_detail_id, startdate: startdate, includesubset: includesubset},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                //$('.success_message').html(content.msg);
                parent.location.reload();
                parent.jQuery.fancybox.close();

            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}

function update_equipmentdetailtasksubset_startdate(id) {
    $('.loader').show();
    var startdate = $("#startdate").val();
    var parent_id = $('#parent_id').val();
    if (startdate == "") {
        $("#startdate").focus();
        $("#error_startdate").html("Please Select Start Date");
        $('.loader').hide();
        return false;
    }
    var eqp_detail_id = id;

    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updateonlystartdateofequipmentdetailtasksubset',
        data: {eqp_detail_id: eqp_detail_id, startdate: startdate, parent_id: parent_id},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                parent.location.reload();
                parent.jQuery.fancybox.close();
            } else {
                $('#success_message').html(content.msg);
            }
        }
    });
}

function update_equipmentdetailtask_startdate(id) {
    $('.loader').show();
    var startdate = $("#startdate").val();
    var assingedTo = $("#assignedto").val();
    var day_of_month = $("#sdom").val();
    
    if (startdate == "") {
        $("#startdate").focus();
        $("#error_startdate").html("Please Select The Start Date");
        $('.loader').hide();
        return false;
    }
    var eqp_task_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updateonlystartdateofequipmentdetailtask',
        data: {eqp_task_id: eqp_task_id, startdate: startdate, assingedTo: assingedTo, day_of_month: day_of_month},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                //$('.success_message').html(content.msg);
                parent.location.reload();
                parent.jQuery.fancybox.close();

            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}

/*
 * equipment detail reading Start date only update on root 
 */
function update_equipmentdetail_readingroot_startdate(id) {
    $('.loader').show();
    var startdate = $("#startdate").val();
    var includesubset = "";
    if ($('#includesubset:checked').val() == 1) {
        includesubset = $('#includesubset:checked').val();
    }
    if (startdate == "") {
        $("#startdate").focus();
        $("#error_startdate").html("Please Select The Start Date");
        $('.loader').hide();
        return false;
    }
    var eqp_detail_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updateonlystartdateofequipmentdetailreadingroot',
        data: {eqp_detail_id: eqp_detail_id, startdate: startdate, includesubset: includesubset},
        success: function (data) {
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                //$('.success_message').html(content.msg);
                $('.loader').hide();
                parent.location.reload();
                parent.jQuery.fancybox.close();

            } else {
                $('#success_message').html(content.msg);
            }
        }
    });
}

/*
 * equipment detail reading update Start date only on Subset 
 */
function update_equipmentdetail_readingsubset_startdate(id) {
    $('.loader').show();
    var startdate = $("#startdate").val();
    var parent_id = $('#parent_id').val();
    if (startdate == "") {
        $("#startdate").focus();
        $("#error_startdate").html("Please Select Start Date");
        $('.loader').hide();
        return false;
    }
    var eqp_detail_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updateonlystartdateofequipmentreadingsubset',
        data: {eqp_detail_id: eqp_detail_id, startdate: startdate, parent_id: parent_id},
        success: function (data) {
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                //$('.success_message').html(content.msg);
                $('.loader').hide();
                parent.location.reload();
                parent.jQuery.fancybox.close();
            } else {
                $('#success_message').html(content.msg);
            }
        }
    });
}

/*
 * Equipment detail reading start date update only
 */

function update_equipmentdetailreading_startdate(id) {
    $('.loader').show();
    var startdate = $("#startdate").val();
    var assignedTo = $("#assignedto").val();
    var startdate_month = $("#sdom").val();
    if (startdate == "") {
        $("#startdate").focus();
        $("#error_startdate").html("Please Select The Start Date");
        $('.loader').hide();
        return false;
    }
    var eqp_task_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updateonlystartdateofequipmentdetailreading',
        data: {eqp_task_id: eqp_task_id, startdate: startdate, assignedTo: assignedTo, startdate_month: startdate_month},
        success: function (data) {
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                //$('.success_message').html(content.msg);
                parent.location.reload();
                parent.jQuery.fancybox.close();

            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}

function eqpdetailplusminus(id) {
    var div = document.getElementById("mno" + id);
    if (div.style.display !== "none") {
        div.style.display = "none";
        $("#" + id).html('<i class="fa fa fa-plus-circle pull-right" aria-hidden="true"></i>');
    } else {
        div.style.display = "table-row-group";
        //div.style.display = "table-row";
        $("#" + id).html('<i class="fa fa fa-minus-circle pull-right" aria-hidden="true"></i>');
    }
}

function view_equipmentdetail_update(id) {
    var eqp_detail_id = id;
    var list = "";
    $(".showlistoption[type='checkbox']:checked").each(function () {
        console.log($(this).val());
        list += parseInt($(this).val()) + ",";
    });
    //alert(list);
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/updateequipmentdetailviewtask',
        data: {viewlist: list, type: 'task', eqp_detail_id: eqp_detail_id},
        success: function (response) {
            $('.loader').hide();
            $("#viewtask").html(response);

        }
    });
}

function view_equipmentdetail_reading_update(id) {
    var eqp_detail_id = id;
    var list = "";
    $(".showlistoption[type='checkbox']:checked").each(function () {
        console.log($(this).val());
        list += parseInt($(this).val()) + ",";
    });
    //alert(list);
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/updateequipmentdetailviewreading',
        data: {viewlist: list, type: 'task', eqp_detail_id: eqp_detail_id},
        success: function (response) {
            $('.loader').hide();
            $("#viewreading").html(response);

        }
    });

}

/**
 * 
 * This is for equipment name validate
 */
function validateequipmentname() {

    var equipment_name = $("#equipmentName").val().trim();
    var equipment_id = $("#equipment_id").val().trim();
    $valid = true;
    if (equipment_name === "") {
        $("#equipmentName").focus();
        $("#equipment_name_error").html("Please Enter Equipment Name");
        $valid = false;
    } else {
        $("#equipment_name_error").html("");
    }

    if ($valid === false) {
        return false;
    } else {
        $('.loader').show();
        $.ajax({
            type: "POST",
            url: baseUrl + 'pm/validateequipmentname',
            data: {EquipmentName: equipment_name, Equipment_id: equipment_id},
            success: function (msg) {
                //console.log(msg);
                $('.loader').hide();
                if (msg.trim() == 'true') {
                    $('#equipment_name_error').html("");
                    if (equipment_id != "") {
                        update_equipment_name();
                    } else {
                        //create_equipment_template();
                    }

                } else {
                    $('.loader').hide();
                    console.log("already exit");
                    $('#equipment_name_error').html("Equipment Name already in use.");
                    $('#equipmentName').focus();
                    return false;
                }
            }
        });
    }

}

/*
 * This functionality for update Equipment Name
 */
function update_equipment_name() {
    var equipment_name = $("#equipmentName").val().trim();
    var equipment_id = $("#equipment_id").val();
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/editequipmentname',
        data: {EquipmentName: equipment_name, Equipment_id: equipment_id},
        //data: {AU_Equipment_Name: equipment_name},
        success: function (data) {
            console.log(data);
            $('.loader').hide();
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                $('.success_message').html(content.msg);
                parent.jQuery.fancybox.close();
                parent.window.location.reload();
            } else {
                //$('#emailsave').removeAttr('disabled');
                $('.success_message').html(content.msg);
                alert('There was an error');
            }
        }
    });

}

// Edit Equipment Validation
function validateeditequipment(action) {

    var letters = /^[0-9a-zA-Z]+$/; //This is for Alphanumeric 

    var equipment_id = $("#equipmentnameid").val().trim();
    var equipment_detail_id = $("#equipmentdetailid").val().trim();
    var old_unit = $("#old_unit").val().trim();
    var old_floor = $("#old_floor").val().trim();
    var unit = $("#unit").val().trim();
    var floor = $("#floor").val().trim();
    var location = $("#location").val().trim();
    var serialnumber = $("#serialnumber").val().trim();
    var makemodel = $("#makemodel").val().trim();
    var inservicedate = $("#inservicedate").val().trim();
    var note = $("#note").val().trim();
    var equipmentmenual = $("#equipmentmenual").val().trim();
    var startdate = $("#startdate").val().trim();
	var taskId = $("#taskId").val().trim();
    var imagename = $("#imagename").val().trim();
    var status = $("#status").val().trim();

    var isValid = true;
    var manual = $("#equipmentmenual").prop('files')[0];
    var flag_val = escape($("#equipmentmenual").attr('name'));
    
    if (unit == "") {
        $("#unit").focus();
        $("#unit").addClass('error-border');
        isValid = false;
    } else if (!unit.match(letters)) {
        $("#unit").focus();
        $("#unit").addClass('error-border');
        $("#unit_error").html("Please Enter unit in alphanumeric only");
        return false;

    } else {
        $("#unit").removeClass('error-border');
        $("#unit_error").html("");
    }
    if (floor == "") {
        $("#floor").focus();
        $("#floor").addClass('error-border');
        //$("#floor_error").html("Please Enter Floor Name");
        isValid = false;
    } else if (!floor.match(letters)) {
        $("#floor").focus();
        $("#floor").addClass('error-border');
        $("#floor_error").html("Please Enter floor in alphanumeric only");
        return false;

    } else {
        $("#floor").removeClass('error-border');
        $("#floor_error").html("");
    }
    if (location == "") {
        $("#location").focus();
        $("#location").addClass('error-border');
        //$("#location_error").html("Please Enter Location Name");
        isValid = false;
    } else {
        $("#location").removeClass('error-border');
        $("#location_error").html("");
    }

    if (typeof manual !== 'undefined') {
        var ext = manual.name.split(".");
        var type_ext = ext[ext.length - 1];
        //console.log(type_ext);
        if (type_ext !== 'pdf' && type_ext !== 'PDF') {
            $("#equipmentmenual").focus();
            $("#equipmentmenual").addClass('error-border');
            $("#equipmentmenual_error").html("Please upload Only a PDF File");
            isValid = false;
        } else {
            $("#equipmentmenual_error").html("");
            $("#equipmentmenual").removeClass('error-border');
        }
    }

    if (isValid === false) {
        return false;
    } else {

        $.ajax({
            type: "POST",
            url: baseUrl + 'pm/validatsfloorandunit',
            data: {equipment_id: equipment_id, unit: unit, floor: floor, old_unit: old_unit, old_floor: old_floor},
            success: function (msg) {
                //console.log(msg);
                $('.loader').hide();
                if (msg.trim() == 'true') {
                    // For saving the updated data
                    var editData = new FormData();
                    editData.append('file[' + flag_val + ']', manual);
                    editData.append('AU_Equipment_Detail_ID', equipment_detail_id);
                    editData.append('Equipment_Floor', floor);
                    editData.append('Equipment_Unit', unit);
                    editData.append('Equipment_Make_Model', makemodel);
                    editData.append('Equipment_Location', location);
                    editData.append('Equipment_Serial_Number', serialnumber);
                    editData.append('Equipment_Inservice_Date', inservicedate);
                    editData.append('Equipment_Notes', note);
                    editData.append('Equipment_Status', status);
                    editData.append('Equipment_Image', imagename);
                    editData.append('startdate', startdate);
					editData.append('taskId', taskId);
                    
                    $.ajax({
                        url: baseUrl + 'pm/saveeditequipment',
                        type: "post",
                        datatype: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: editData,
                        success: function (data) {
                            //console.log(data);
                            $('.loader').hide();
                            //var content = $.parseJSON(data);
                            $("#tempalte-detail-view-change").html('Data Save successfully!');
                            parent.jQuery.fancybox.close();
                            parent.window.location.reload();
                        }
                    });
                    //return true;
                } else {
                    $('#unit').focus();
                    $("#unit").addClass('error-border');
                    $("#unit_error").html("The unit number has already been assigned.");
                    return false;
                }
            }
        });
    }
}

// delete equipment name 

function deleteequipmentname(au_equipment_name_id)
{
    var check_delete = 'YES';
    jPrompt('For Deleting Equipment Name, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {

            if (check_delete === return_value) {
                $('.loader').show();
                $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'pm/deleteequipmentname',
                    data: {au_equipment_name_id: au_equipment_name_id},
                    success: function (data) {
                        console.log(data);
                        var content = $.parseJSON(data);
                        if (content.status == 'success') {
                            $('.success_message').html(content.msg);
                            setTimeout(function () {
                                $('.loader').hide();
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

// Delete the manual pdf file during the edit equipment detail
function deletemanualpdf(au_equipment_manual_id, au_equipment_detail_id )
{
    var check_delete = 'YES';
    jPrompt('For Deleting Equipment Manual, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {

            if (check_delete === return_value) {
                $('.loader').show();
                $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'pm/deleteequipmentmanualpdf',
                    data: {au_equipment_manual_id: au_equipment_manual_id, au_equipment_detail_id:au_equipment_detail_id },
                    success: function (data) {
                        console.log(data);
                        var content = $.parseJSON(data);
                        if (content.status == 'success') {
                            manualpdf(au_equipment_detail_id);
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

//This is for listing the manual pdf list

function manualpdf(au_equipment_detail_id){
    var au_equipment_detail_id = au_equipment_detail_id;
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/manualpdflist',
        data: {au_equipment_detail_id: au_equipment_detail_id},
        success: function (data) {
            //console.log(data);
            $('.manual-pdf').html(data);
        }
    });    
}

// This is for Update group start date of month of equipment task detail
// Task group modification on root start date of month
function update_equipmentdetail_taskroot_startdateofmonth(id) {
    var startdateofmonth = $("#startdateofmonth").val();
    var includesubset = "";
    if ($('#includesubset:checked').length == 1) {
        includesubset = $('#includesubset:checked').val();
    }
    if (startdateofmonth == "") {
        $("#startdateofmonth").focus();
        $("#error_startdateofmonth").html("Please Select The Start Date");
        return false;
    }
    var eqp_detail_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updateequipmentdetailtaskrootstartdateofmonth',
        data: {eqp_detail_id: eqp_detail_id, startdateofmonth: startdateofmonth, includesubset: includesubset},
        success: function (data) {
            console.log(data);
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                $('.success_message').html(content.msg);
                parent.location.reload();
                parent.jQuery.fancybox.close();
                
            } else {
                $('#success_message').html(content.msg);
            }
        }
    });
}

/* Task group modification on Subset start date of month */
function update_equipmentdetail_tasksubset_startdateofmonth(id) {
    var startdateofmonth = $("#startdateofmonth").val();
    var parent_id = $('#parent_id').val();
    if (startdateofmonth == "") {
        $("#startdateofmonth").focus();
        $("#error_startdateofmonth").html("Please Select Start Date of Month");
        return false;
    }
    var eqp_detail_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updateequipmentdetailtasksubsetstartdateofmonth',
        data: {eqp_detail_id: eqp_detail_id, startdateofmonth: startdateofmonth, parent_id: parent_id},
        success: function (data) {
            console.log(data);
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                $('.success_message').html(content.msg);
                parent.jQuery.fancybox.close();
                parent.location.reload();
               
            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}

function update_equipmentdetail_readingroot_startdateofmonth(id) {
    var startdateofmonth = $("#startdateofmonth").val();
    var includesubset = "";
    if ($('#includesubset:checked').length == 1) {
        includesubset = $('#includesubset:checked').val();
    }
    if (startdateofmonth == "") {
        $("#startdateofmonth").focus();
        $("#error_startdateofmonth").html("Please Select The Start Date");
        return false;
    }
    var eqp_detail_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updateequipmentdetailreadingrootstartdateofmonth',
        data: {eqp_detail_id: eqp_detail_id, startdateofmonth: startdateofmonth, includesubset: includesubset},
        success: function (data) {
            console.log(data);
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                $('.success_message').html(content.msg);
                parent.location.reload();
                parent.jQuery.fancybox.close();
                
            } else {
                $('#success_message').html(content.msg);
            }
        }
    });
}

function update_equipmentdetail_readingsubset_startdateofmonth(id) {
    var startdateofmonth = $("#startdateofmonth").val();
    var parent_id = $('#parent_id').val();
    if (startdateofmonth == "") {
        $("#startdateofmonth").focus();
        $("#error_startdateofmonth").html("Please Select Start Date of Month");
        return false;
    }
    var eqp_detail_id = id;
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/updateequipmentdetailreadingsubsetstartdateofmonth',
        data: {eqp_detail_id: eqp_detail_id, startdateofmonth: startdateofmonth, parent_id: parent_id},
        success: function (data) {
            console.log(data);
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                $('.success_message').html(content.msg);
                parent.jQuery.fancybox.close();
                parent.location.reload();
               
            } else {
                $('#success_message').html(content.msg);

            }
        }
    });
}
function getTemplate() {
    var eqid = $("#browsers option[value='" + $('#equipmentname').val() + "']").attr('id');  
    if(eqid != ''){
     $.ajax({
        type: "POST",
        dataType: 'JSON',
        url: baseUrl + 'pm/searchtemplate',
        data: {eqid: eqid},		
        success: function (response) {
		console.log(response);
		 var len = response.length; 
              $("#attach_template").empty();
            //$("#attach_template").append("<option value='0'>Select Floor</option>");
            for( var i = 0; i<len; i++){
                var id = response[i]['AU_Template_Designation_ID'];
				var tname = response[i]['AU_Template_Name'];
				var td = response[i]['AU_TypeDesignation'];
				var ttd = response[i]['AU_TypeDescritpion'];
				var tetd = response[i]['AU_Template_Name_IDD'];
				if(id == tetd)
					var sel = 'selected';
				else
					var sel = 'select';
                $("#attach_template").append("<option value='"+id+"' "+sel+">"+tname+' '+td+' '+ttd+"</option>"); 
            } 
             attach_template();
        }
    }); 
	}
}