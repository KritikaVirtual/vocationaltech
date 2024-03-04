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
    $('.colorblue').addClass('blue-bg');
    $(".show_hide").html('<i class="fa fa fa-plus-circle" aria-hidden="true"></i>');
    
    $("#expandall").removeClass("group-btn-custom");  
    $("#collapseall").addClass("group-btn-custom");
    
}
function sortingbyfloor(type, equipment_name_id) {
    var id = $("#sortingvaluebyfloor-" + equipment_name_id).val();
    
   /* if (x == 1) {
        $("#sortingidbyfloor").html('<span class="glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvaluebyfloor" id="sortingvaluebyfloor">');
        id = $("#sortingvaluebyfloor").val();
    } else {
        $("#sortingidbyfloor").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvaluebyfloor" id="sortingvaluebyfloor">');
        id = $("#sortingvaluebyfloor").val();
    }*/
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortworkorderequipment',
        data: {id: id, type: type, equipment_name_id: equipment_name_id},
        success: function (data) {
            $('#mno' + equipment_name_id).html(data);
        }
    });
}

function sortingbyunit(type, equipment_name_id) {
    var id = $("#sortingvaluebyunit-" + equipment_name_id).val();
        
    /*if (x == 1) {
        $("#sortingidbyunit").html('<span class="glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvaluebyunit" id="sortingvaluebyunit">');
        id = $("#sortingvaluebyunit").val();
    } else {
        $("#sortingidbyunit").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvaluebyunit" id="sortingvaluebyunit">');
        id = $("#sortingvaluebyunit").val();
    }*/
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortworkorderequipment',
        data: {id: id, type: type, equipment_name_id: equipment_name_id},
        success: function (data) {
            $('#mno' + equipment_name_id).html(data);
        }
    });
}

function sortingbywo(type, equipment_name_id) {
    var id = $("#sortingvaluebywo-" + equipment_name_id).val();
    /*if (x == 1) {
        $("#sortingidbywo").html('<span class="glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvaluebywo" id="sortingvaluebywo">');
        id = $("#sortingvaluebywo").val();
    } else {
        $("#sortingidbywo").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvaluebywo" id="sortingvaluebywo">');
        id = $("#sortingvaluebywo").val();
    }*/
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortworkorderequipment',
        data: {id: id, type: type, equipment_name_id: equipment_name_id},
        success: function (data) {
            $('#mno' + equipment_name_id).html(data);
        }
    });
}

function searchfor() {    
    $('#eqparts').val('');         
    var eqname = $("#eqname").val();
    $.ajax({
        //type: "POST",
        dataType: 'JSON',
        url: baseUrl + 'pm/wosearchfor',
        data: {eqname: eqname},
        success: function (data) {
            //console.log(data);
            $('#typehintvalue').html("");
            var uniqueNames = [];
            for (i = 0; i < data.length; i++) {

                if (data[i].hasOwnProperty('Equipment_Floor')) {
                    if (uniqueNames.indexOf(data[i].Equipment_Floor) === -1) {
                        uniqueNames.push(data[i].Equipment_Floor);
                    }
                }
                if (data[i].hasOwnProperty('WO_Number')) {
                    if (uniqueNames.indexOf(data[i].WO_Number) === -1) {
                        uniqueNames.push(data[i].WO_Number);
                    }
                }
                if (data[i].hasOwnProperty('AU_Equipment_Name')) {
                    if (uniqueNames.indexOf(data[i].AU_Equipment_Name) === -1) {
                        uniqueNames.push(data[i].AU_Equipment_Name);
                    }
                }
                if (data[i].hasOwnProperty('Equipment_Location')) {
                    if (uniqueNames.indexOf(data[i].Equipment_Location) === -1) {
                        uniqueNames.push(data[i].Equipment_Location);
                    }
                }
                if (data[i].hasOwnProperty('Equipment_Unit')) {
                    if (uniqueNames.indexOf(data[i].Equipment_Unit) === -1) {
                        uniqueNames.push(data[i].Equipment_Unit);
                    }
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
        url: baseUrl + 'pm/wosortequipmentname',
        data: {id: id},
        success: function (data) {
            //console.log(data);
            $('#pm-work-cta').html(data);
        }
    });

}

function searchData() {
    var eqparts = $("#eqparts").val().trim();
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/wosearchequipment',
        data: {eqparts: eqparts},
        success: function (data) {
            $('#pm-work-cta').html(data);
        }
    });
}

function sortingbyfloorofwo(type) {
    var id = $("#sortingvaluebyfloor").val();
   /* if (x == 1) {
        $("#sortingidbyfloor").html('<span class="glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"><input type="hidden" value="0" name="sortingvaluebyfloor" id="sortingvaluebyfloor"></span>');
        id = $("#sortingvaluebyfloor").val();
    } else {
        $("#sortingidbyfloor").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvaluebyfloor" id="sortingvaluebyfloor">');
        id = $("#sortingvaluebyfloor").val();
    }*/
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortworkorderequipmentbywo',
        data: {id: id, type: type},
        success: function (data) {
            $('#by-work-order-number').html(data);
        }
    });
}

function sortingbyunitofwo(type) {
    var id = $("#sortingvaluebyunit").val();
    /*if (x == 1) {
        $("#sortingidbyunit").html('<span class="glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvaluebyunit" id="sortingvaluebyunit">');
        id = $("#sortingvaluebyunit").val();
    } else {
        $("#sortingidbyunit").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvaluebyunit" id="sortingvaluebyunit">');
        id = $("#sortingvaluebyunit").val();
    }*/
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortworkorderequipmentbywo',
        data: {id: id, type: type},
        success: function (data) {
            $('#by-work-order-number').html(data);
        }
    });
}

function sortingbywoofwo(type) {
    var id = $("#sortingvaluebywo").val();
    /*if (x == 1) {
        $("#sortingidbywo").html('<span class="glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvaluebywo" id="sortingvaluebywo">');
        id = $("#sortingvaluebywo").val();
    } else {
        $("#sortingidbywo").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvaluebywo" id="sortingvaluebywo">');
        id = $("#sortingvaluebywo").val();
    }*/
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortworkorderequipmentbywo',
        data: {id: id, type: type},
        success: function (data) {
            $('#by-work-order-number').html(data);
        }
    });
}

function sortingbylocationofwo(type) {
    var id = $("#sortingvaluebylocation").val();
   /* if (x == 1) {
        $("#sortingidbylocation").html('<span class="glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvaluebylocation" id="sortingvaluebylocation">');
        id = $("#sortingvaluebylocation").val();
    } else {
        $("#sortingidbylocation").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvaluebylocation" id="sortingvaluebylocation">');
        id = $("#sortingvaluebylocation").val();
    }*/
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortworkorderequipmentbywo',
        data: {id: id, type: type},
        success: function (data) {
            $('#by-work-order-number').html(data);
        }
    });
}

function sortingbyequipmentofwo(type) {
    var id = $("#sortingvaluebyequipment").val();
   /* if (x == 1) {
        $("#sortingidbyequipment").html('<span class="glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvaluebyequipment" id="sortingvaluebyequipment">');
        id = $("#sortingvaluebyequipment").val();
    } else {
        $("#sortingidbyequipment").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvaluebyequipment" id="sortingvaluebyequipment">');
        id = $("#sortingvaluebyequipment").val();
    }*/
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortworkorderequipmentbywo',
        data: {id: id, type: type},
        success: function (data) {
            $('#by-work-order-number').html(data);
        }
    });
}

function searchDatawo() {
    var eqparts = $("#eqparts").val().trim();
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/sortworkorderequipmentbywo',
        data: {eqparts: eqparts},
        success: function (data) {
            $('#by-work-order-number').html(data);
        }
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
            x = x + 70;
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
    $(".date-picker")
            .datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                changeYear: true,
                //minDate: 0,
                showButtonPanel: true,
                numberOfMonths: 1,
                dateFormat: 'MM dd, yy'
            });

    /* This is for PM Setup and Options */

    $('input:radio[name=pmwojobs]').change(function () {
        if ($("input[name='pmwojobs']:checked").val() == 'Y') {
        } else {
            //$("#generatepmjobsauto").prop("checked", false);
            $("input:radio[name='generatepmjobsauto']").attr("checked", false);
            $("input:radio[name='generatepmjobsautomonth']").attr("checked", false);
            $("input:radio[name='dailyweeklypmjobs']").attr("checked", false);
            $('#weeklyon').val('');
            $('#monthlynoofday').val('');
            $('#monthlyday').val('');
            $('input[name=excdaily]').attr('checked', false);
        }

    });

    $('input:radio[name=generatepmjobsauto]').change(function () {
        // $("input:radio[name='pmwojobs']").attr("checked", true);
        $('input:radio[name=pmwojobs]:nth(0)').attr('checked', true);
        if ($("input[name='generatepmjobsauto']:checked").val() == '4') {
            $("input:radio[name='generatepmjobsautomonth']").attr("checked", true);
            $('input:radio[name=dailyweeklypmjobs]:nth(0)').attr('checked', true);
            $('#weeklyon').val('');
            $('input[name=excdaily]').attr('checked', false);
            $('#monthlynoofday').val('');
            $('#monthlyday').val('');
        } else if ($("input[name='generatepmjobsauto']:checked").val() == '1') {
            $("input:radio[name='generatepmjobsautomonth']").attr("checked", false);
            $('input:radio[name=dailyweeklypmjobs]:nth(0)').attr('checked', false);
            $('#monthlynoofday').val('');
            $('#monthlyday').val('');
            $('input[name=excdaily]').attr('checked', false);
            
        } else if ($("input[name='generatepmjobsauto']:checked").val() == '2') {
            $("input:radio[name='generatepmjobsautomonth']").attr("checked", false);
            $('input:radio[name=dailyweeklypmjobs]:nth(0)').attr('checked', false);
            $('#monthlynoofday').val('');
            $('#monthlyday').val('');
            $('#weeklyon').val('');
            $('input[name=excdaily]').attr('checked', false);

        } else if ($("input[name='generatepmjobsauto']:checked").val() == '3') {
            $("input:radio[name='generatepmjobsautomonth']").attr("checked", false);
            $('input:radio[name=dailyweeklypmjobs]:nth(0)').attr('checked', false);
            $('#monthlynoofday').val('');
            $('#monthlyday').val('');

        }
    });

    $('input:radio[name=pmworeports]').change(function () {
        if ($("input[name='pmworeports']:checked").val() == 'Y') {
            $("input:radio[name='dailypmjobs']").attr("checked", false);
            $("input:radio[name='weeklypmjobs']").attr("checked", false);
        } else if ($("input[name='pmworeports']:checked").val() == 'N') {
            $("input:radio[name='dailypmjobs']:nth(0)").attr("checked", true);
            $("input:radio[name='weeklypmjobs']:nth(0)").attr("checked", true);
        }
    });

    $('input:radio[name=dailypmjobs]').change(function () {
        $("input:radio[name='pmworeports']:nth(1)").attr("checked", true);
    });

    $('input:radio[name=weeklypmjobs]').change(function () {
        $("input:radio[name='pmworeports']:nth(1)").attr("checked", true);
    });
    
    /*$('#byequipment').click(function(e) {
        //$("#byequipment").removeClass("group-btn-custom");  
        //$("#bywonumber").addClass("group-btn-custom");
    });
    
    $('#bywonumber').click(function(e) {
        alert(1);
        $("#byequipment").removeClass("group-btn-custom");  
        $("#bywonumber").addClass("group-btn-custom");
    });*/
    

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
// Close popup cancel button event //
function closepopup() {
    parent.$("body").removeClass("dynamicclass");
    parent.jQuery.fancybox.close();
}

//validate and save pm -setup and options

function vlidate_pmsetupoptions() {
    var pmsetupoptionsData = new FormData();
    var pmwojobs = $("input[name='pmwojobs']:checked").val();
    if (pmwojobs == 'Y') {
        var generatepmjobsauto = $("input[name='generatepmjobsauto']:checked").val();
        if (generatepmjobsauto == 1) {
            pmsetupoptionsData.append('PM_Auto_Create_Jobs', 'Y');
            pmsetupoptionsData.append('PM_Auto_Schedule', 'E');
            pmsetupoptionsData.append('PM_Auto_Month_Day_Of_Week', '1');
            pmsetupoptionsData.append('PM_Auto_Month_Generate', 'DAY');
            pmsetupoptionsData.append('PM_Auto_Exclude', 'N');

        } else if (generatepmjobsauto == 2) {
            pmsetupoptionsData.append('PM_Auto_Create_Jobs', 'Y');
            pmsetupoptionsData.append('PM_Auto_Schedule', 'F');
            pmsetupoptionsData.append('PM_Auto_Month_Day_Of_Week', '1');
            pmsetupoptionsData.append('PM_Auto_Month_Generate', 'DAY');
            pmsetupoptionsData.append('PM_Auto_Exclude', 'N');

        } else if (generatepmjobsauto == 3) {
            pmsetupoptionsData.append('PM_Auto_Create_Jobs', 'Y');
            pmsetupoptionsData.append('PM_Auto_Schedule', 'W');
            pmsetupoptionsData.append('PM_Auto_Month_Day_Of_Week', '1');
            var weeklyon = $("#weeklyon").val();
            pmsetupoptionsData.append('PM_Auto_Month_Generate', weeklyon);
            if ($("#excdaily").prop('checked') == true) {
                var yesno = 'Y';
            } else {
                var yesno = 'N';
            }
            pmsetupoptionsData.append('PM_Auto_Exclude', yesno);

        } else if (generatepmjobsauto == 4) {
            pmsetupoptionsData.append('PM_Auto_Create_Jobs', 'Y');
            pmsetupoptionsData.append('PM_Auto_Schedule', 'M');
            var monthlynoofday = $("#monthlynoofday").val();
            pmsetupoptionsData.append('PM_Auto_Month_Day_Of_Week', monthlynoofday);
            var monthlyday = $("#monthlyday").val();
            pmsetupoptionsData.append('PM_Auto_Month_Generate', monthlyday);
            var dailyweeklypmjobs = $("input[name='dailyweeklypmjobs']:checked").val();
            pmsetupoptionsData.append('PM_Auto_Exclude', dailyweeklypmjobs);
        }

    } else if (pmwojobs == 'N') {
        pmsetupoptionsData.append('PM_Auto_Create_Jobs', 'N');
        pmsetupoptionsData.append('PM_Auto_Schedule', 'M');
        pmsetupoptionsData.append('PM_Auto_Month_Day_Of_Week', '1');
        pmsetupoptionsData.append('PM_Auto_Month_Generate', 'DAY');
        pmsetupoptionsData.append('PM_Auto_Exclude', 'N');
    }

    var pmworeports = $("input[name='pmworeports']:checked").val();
    if (pmworeports == 'Y') {
        pmsetupoptionsData.append('PM_Reports_Separate', pmworeports);
    } else {
        var dailypmjobs = $("input[name='dailypmjobs']:checked").val();
        pmsetupoptionsData.append('PM_Reports_Exclude_Daily', dailypmjobs);
        var weeklypmjobs = $("input[name='weeklypmjobs']:checked").val();
        pmsetupoptionsData.append('PM_Reports_Exclude_Weekly', weeklypmjobs);
        pmsetupoptionsData.append('PM_Reports_Separate', pmworeports);
    }
    var pmworeportsjobtime = $("input[name='pmworeportsjobtime']:checked").val();
    pmsetupoptionsData.append('PM_Complete_Job_Time', pmworeportsjobtime);

    $.ajax({
        url: baseUrl + 'pm/savepmsetupoptions',
        type: "post",
        datatype: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: pmsetupoptionsData,
        success: function (data) {
            //console.log(data);
            console.log(data.trim());
            if(data.trim()=='updated'){
                $('.loader').hide();
                $("#building_error").html("PM setup and options updated!");
                parent.jQuery.fancybox.close(); 
                parent.window.location.reload();
            } else if(data.trim()=='added') {
                $('.loader').hide();
                $("#building_error").html("PM setup and options added!");
                parent.jQuery.fancybox.close(); 
                parent.window.location.reload();
            }else {
                $("#building_error").html("Some error occurs!");
                return false;                
            }           

        }
    });

}

/**
 * Validate Generate wo's and save data
 * 
 */
function validate_generatewo(){
    var wogenerateddate = $("#wogenerateddate").val();
    if(wogenerateddate==''){
        $("#wogenerateddate").addClass('error-border');
            return false;        
    } else {
        $("#wogenerateddate").removeClass('error-border');
    }
    
    $.ajax({
        url: baseUrl + 'pm/savepmgeneratewo',
        type: "post",
        datatype: 'json',
        data: {wogenerateddate: wogenerateddate},
        success: function (data) {
            var response = JSON.parse(data);
            if(response.message){                             
                $.ajax({
                    url: baseUrl + 'cron/pm_cron_jobs/manual_pm_jobs.php',
                    type: "post",
                    datatype: 'json',
                    data: {wogenerateddate: wogenerateddate,PM_MAN_DATE:response.PM_MAN_DATE, Key_Building_Number:response.Key_Building_Number,User_Id:response.User_Id,Cost_Center_Number:response.Cost_Center_Number},
                    success: function (data) {
                        parent.jQuery.fancybox.close();
                        parent.$("#xtrigManualCreateWO").trigger("click");                           
                    }
                }); 
                //End here this is for Internal work purpose
                
            } else {
                $("#building_error").html("Some error occurs!");
                return false;                
            }           

        }
    });
}
    
    /**
 * Validate Notes / Comments wo's and save data
 * 
 */
function validate_noteswo(PM_WO_Notes_ID){
    var pm_wo_notes_id = PM_WO_Notes_ID;
    var pm_wo_no = $("#pm_wo_no").val();
    var notes = $("#notes").val();    
    if(notes==''){
        $("#notes").addClass('error-border');
            return false;        
    } else {
        $("#notes").removeClass('error-border');
    }
    
    $.ajax({
        url: baseUrl + 'pm/savenotespmcompletewo',
        type: "post",
        datatype: 'json',
        data: {notes: notes,pm_wo_no:pm_wo_no,pm_wo_notes_id:pm_wo_notes_id},
        success: function (data) {
            var response = JSON.parse(data);
            console.log(response);           
            if(response.message=='success'){
                $('.loader').hide();
                $("#building_error").html("Notes / Comments WO's added!");
                parent.jQuery.fancybox.close(); 
                //parent.window.location.reload()            
            } else {
                $("#building_error").html("Some error occurs!");
                return false;                
            }           

        }
    });
    
}
 /**
 * Validate Photos / Documents wo's and save data
 * 
 */
function validate_photoswo(){
    // 1MB = 1024*1024 = 1048576
    
    var isValid = true;
    var woPhotoData = new FormData();    
   
    var docs = $("#photos").prop('files')[0];
    var flag_val = escape($("#photos").attr('name'));    
    var pm_wo_no = $("#pm_wo_no").val();
    if (typeof docs !== 'undefined') {
        var fsize = $("#photos").prop('files')[0].size;   
        var ext = docs.name.split(".");
        var type_ext = ext[ext.length - 1]; 
        
        if (type_ext !== 'pdf' && type_ext !== 'PDF' && type_ext !== 'PNG' && type_ext !== 'png' && type_ext !== 'JPG' && type_ext !== 'jpg' && type_ext!=='BMP' && type_ext!=='bmp') {
            $("#photos").focus();
            $("#photos").addClass('error-border');
            $("#photos_error").html("Please upload Only a PDF, PNG, JPG, and BMP File!");
            isValid = false;
        } else if(fsize > 1048576){
            $("#photos").focus();
            $("#photos").addClass('error-border');
            $("#photos_error").html("File size should be Less than 1MB!");
            isValid = false;
        }
    } else {
        $("#photos").focus();
        $("#photos").addClass('error-border');
        $("#photos_error").html("Please upload Only a PDF, PNG, JPG, and BMP File and file size 1mb!");
        isValid = false;
    }    
    
    if (isValid === false) {
        return false;
    } else {
       $('.loader').show(); 
    woPhotoData.append('file[' + flag_val + ']', docs);
    woPhotoData.append('pm_wo_no', pm_wo_no);
    
    $.ajax({
        url: baseUrl + 'pm/savedocumentspmcompletewo',
        type: "post",
        //datatype: 'json',
        dataType: 'html',
        cache: false,
        contentType: false,
        processData: false,
        data: woPhotoData,
        success: function (data) {
            $('.loader').hide();
            $('#listPhotosDocuments').html(data);
            //parent.window.location.reload();
            parent.jQuery.fancybox.close(); 
            parent.$("#xtrigdocs").trigger("click"); 
        }
    });
    }
} 
    
    function deletephotoscompletewo(PM_WO_Photo_ID){         
        var pm_wo_photo_id = PM_WO_Photo_ID;
        $('.loader').show();
        $.ajax({
            type: "POST",
            datatype: 'json',
            url: baseUrl + 'pm/deleteedocumenthistorywo',
            data: {pm_wo_photo_id: pm_wo_photo_id},
            success: function (data) {
                //console.log(data);
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    parent.jQuery.fancybox.close(); 
                    parent.$("#xtrigdocs").trigger("click");                    
                } else {
                    $('#success_message').html(content.msg);
                }
            }
        });            
        
}
function editNewDisGroup(url) {
    alert('heelo');
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



