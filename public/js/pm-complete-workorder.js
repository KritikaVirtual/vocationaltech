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
// Close popup cancel button event //
function closepopup() {
    parent.$("body").removeClass("dynamicclass");
    parent.jQuery.fancybox.close();
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

$(document).ready(function () {
    $(".notes-comments-btn").on("click", function () {
        $("body").addClass('notes-cmt-modal');
        $("body").removeClass('dynamicclass');
    });
    $(".confirm").on("click", function () {
        $("body").removeClass('notes-cmt-modal');
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
                numberOfMonths: 1
            });
            
    $('#hiddenDate').datepicker({
        defaultDate: "+1w",
                changeMonth: true,
                changeYear: true,
                //minDate: 0,
                showButtonPanel: true,
                numberOfMonths: 1
    });
    $('#pickDate').click(function (e) {
        $('#hiddenDate').datepicker("show");
        e.preventDefault();
    });
    
    $('#hiddenDateReading').datepicker({
        defaultDate: "+1w",
                changeMonth: true,
                changeYear: true,
                //minDate: 0,
                showButtonPanel: true,
                numberOfMonths: 1
    });
    $('#pickDateReading').click(function (e) {
        $('#hiddenDateReading').datepicker("show");
        e.preventDefault();
    });
    

});

function searchData() {
    
    var pmwonumber = $("#pmwonumber").val().trim();
    //var pmreadingtask = $("#pmreadingtask").val().trim();
    if (pmwonumber=="") {                
                $("#pmwonumber").focus();
                $("#pmwonumber").addClass('error-border');
                return false;
            } else {                
                $("#pmwonumber").removeClass('error-border');
                $('.loader').show();
            }
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/searchofpmcompletewo',
        data: {pmwonumber: pmwonumber},
        success: function (data) {
            $('.loader').hide();
            $('#pmcompletewo').html(data);
        }
    });
}

function getDataforreading(PM_WO_Number,reading) {
    var pmwonumber = PM_WO_Number;
    var reading = reading;
    
    /*if (pmwonumber=="") {                
                $("#pmwonumber").focus();
                $("#pmwonumber").addClass('error-border');
                return false;
            } else {                
                $("#pmwonumber").removeClass('error-border');
                $('.loader').show();
            }*/
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/readingofpmcompletewo',
        data: {pmwonumber: pmwonumber,reading:reading},
        success: function (data) {
            $('.loader').hide();
            $('#readingdatecompleted').html(data);
        }
    });
}

function getDatafortask(PM_WO_Number,task) {
    var pmwonumber = PM_WO_Number;
    var task = task;    
    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/taskofpmcompletewo',
        data: {pmwonumber: pmwonumber,task:task},
        success: function (data) {
            $('.loader').hide();
            $('#readingdatecompleted').html(data);
        }
    });
}

function updateCompletedDate(instruction) {
         
    if(instruction=='T')
    {
        var completedDate = $("#hiddenDate").val();  
       //parent.jQuery.fancybox.close();
       $('input[name="completedDate"]').val(completedDate);         
    } else if(instruction=='R'){
        var completedDate = $("#hiddenDateReading").val();  
        //parent.jQuery.fancybox.close();
       $('input[name="r_completedDate"]').val(completedDate);    
    }
        
}

function updateCompletedBy(instruction){
    //Instruction means Task/Reading 
    if(instruction=='T')
    {
        var completedBy = $("#completedby").val(); 
       //parent.jQuery.fancybox.close();
       $('select[name="selectedby"]').val(completedBy);        
    } else if(instruction=='R'){
       var completedBy = $("#completedbyReading").val(); 
        //parent.jQuery.fancybox.close();
       $('select[name="r_selectedby"]').val(completedBy);
    }   
    
}

function selectAll() {
    $(".checkbox").prop("checked", true);
    //check "select all" if all checkbox items are checked
    if ($('.checkbox:checked').length == $('.checkbox').length) {
        $("#select_all").prop('checked', false);
    }
}

function valid_complete() {
    var reading_task = $('#instruction').val();
    var pmwooption = $("#pmwooption").val();
    if(reading_task=='T'){
    var padding = "6px 255px";
    var favorite = [];
    var ids = [];
    $.each($("input[name='checkornot']:checked"), function () {
        favorite.push($(this).val());
        ids.push($(this).attr("id"));
    });
    if (ids.length < 1) {
        alert('Sorry you have not checked any Task !');
        return false;
    }
    var list = [];
    for (var i = 0; i < ids.length; i++) {
        //var list = [];
        var numbers = ids[i].split('_');
        var actualTime = $("#actualTime_" + numbers[1]).val();
        var completedDate = $("#completedDate_" + numbers[1]).val();
        var notes = $("#notes_" + numbers[1]).val();
        var selectedby = $("#selectedby_" + numbers[1]).val();
        
        if(pmwooption !== null && pmwooption !== '') {
            if (actualTime == "") {
                $("#actualTime_" + numbers[1]).focus();
                $("#actualTime_" + numbers[1]).addClass('error-border');
                return false;
            } else {
                var entered_value = $("#actualTime_" + numbers[1]).val();
                var regexPattern = /^\d{0,8}(\.\d{1,2})?$/;
                //Allow only Number as well 0nly 2 digit after dot(.)
                if (regexPattern.test(entered_value)) {
                    //$(this).css('background-color', 'white');
                    $("#actualTime_" + numbers[1]).removeClass('error-border');
                    //return true;
                } else {
                    $("#actualTime_" + numbers[1]).focus();
                    //$("#actualTime_" + numbers[1]).css('background-color', 'red');
                    $("#actualTime_" + numbers[1]).addClass('error-border');
                    return false;
                    //$(this).css('background-color', 'red');
                    // $('.err-msg').html('Enter a valid Decimal Number');
                }
            }
                        
        } else {
            if (actualTime == "") {
                
            } else {
                var entered_value = $("#actualTime_" + numbers[1]).val();
                var regexPattern = /^\d{0,8}(\.\d{1,2})?$/;
                //Allow only Number as well 0nly 2 digit after dot(.)
                if (regexPattern.test(entered_value)) {
                    //$(this).css('background-color', 'white');
                    $("#actualTime_" + numbers[1]).removeClass('error-border');
                    //return true;
                } else {
                    $("#actualTime_" + numbers[1]).focus();
                    //$("#actualTime_" + numbers[1]).css('background-color', 'red');
                    $("#actualTime_" + numbers[1]).addClass('error-border');
                    return false;
                    //$(this).css('background-color', 'red');
                    // $('.err-msg').html('Enter a valid Decimal Number');
                }
            }
            
        }        

        if (completedDate == "") {
            $("#completedDate_" + numbers[1]).addClass('error-border');
            return false;
        } else {
            $("#completedDate_" + numbers[1]).removeClass('error-border');

        }
        if (selectedby == "") {
            $("#selectedby_" + numbers[1]).addClass('error-border');
            return false;
        } else {
            $("#selectedby_" + numbers[1]).removeClass('error-border');
        }

        list.push([numbers[1], actualTime, completedDate, notes, selectedby]);
    }

    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/savepmcompletehistory',
        data: {list: list},
        success: function (msg) {
            //console.log(msg);
            $("#success_msg").html(msg + "<span style='padding:" + padding + ";'>");
            window.location.reload();
        }
    });
    }else {
        valid_reading_complete(pmwooption);        
    }   

}

function valid_reading_complete(pmwooption){
    var pmwooption = pmwooption;
    var padding = "6px 255px";
    var favorite = [];
    var ids = [];
    $.each($("input[name='checkornot']:checked"), function () {
        favorite.push($(this).val());
        ids.push($(this).attr("id"));
    });
    if (ids.length < 1) {
        alert('Sorry you have not checked any Task !');
        return false;
    }
    var list = [];
    for (var i = 0; i < ids.length; i++) {
        //var list = [];
        var numbers = ids[i].split('_');
        var actualTime = $("#r_actualTime_" + numbers[1]).val();
        var completedDate = $("#r_completedDate_" + numbers[1]).val();
        var notes = $("#r_notes_" + numbers[1]).val();
        var selectedby = $("#r_selectedby_" + numbers[1]).val();
        var reading_value = $("#readingValue_" + numbers[1]).val();

        if(pmwooption !== null && pmwooption !== '') {
            if (actualTime == "") {
                $("#r_actualTime_" + numbers[1]).focus();
                $("#r_actualTime_" + numbers[1]).addClass('error-border');
                return false;
            } else {
                var entered_value = $("#r_actualTime_" + numbers[1]).val();
                var regexPattern = /^\d{0,8}(\.\d{1,2})?$/;
                //Allow only Number as well 0nly 2 digit after dot(.)
                if (regexPattern.test(entered_value)) {
                    //$(this).css('background-color', 'white');
                    $("#r_actualTime_" + numbers[1]).removeClass('error-border');
                    //return true;
                } else {
                    $("#r_actualTime_" + numbers[1]).focus();
                    //$("#actualTime_" + numbers[1]).css('background-color', 'red');
                    $("#r_actualTime_" + numbers[1]).addClass('error-border');
                    return false;
                    //$(this).css('background-color', 'red');
                    // $('.err-msg').html('Enter a valid Decimal Number');
                }
            }
                        
        } else {
            if (actualTime == "") {
                
            } else {
                var entered_value = $("#r_actualTime_" + numbers[1]).val();
                var regexPattern = /^\d{0,8}(\.\d{1,2})?$/;
                //Allow only Number as well 0nly 2 digit after dot(.)
                if (regexPattern.test(entered_value)) {
                    //$(this).css('background-color', 'white');
                    $("#r_actualTime_" + numbers[1]).removeClass('error-border');
                    //return true;
                } else {
                    $("#r_actualTime_" + numbers[1]).focus();
                    //$("#actualTime_" + numbers[1]).css('background-color', 'red');
                    $("#r_actualTime_" + numbers[1]).addClass('error-border');
                    return false;
                    //$(this).css('background-color', 'red');
                    // $('.err-msg').html('Enter a valid Decimal Number');
                }
            }
            
        } 

        if (reading_value == "") {
            $("#readingValue_" + numbers[1]).addClass('error-border');
            return false;            
        } else {
            $("#readingValue_" + numbers[1]).removeClass('error-border');
        }
        
        
        if (completedDate == "") {
            $("#r_completedDate_" + numbers[1]).addClass('error-border');
            return false;
        } else {
            $("#r_completedDate_" + numbers[1]).removeClass('error-border');

        }
        if (selectedby == "") {
            $("#r_selectedby_" + numbers[1]).addClass('error-border');
            return false;
        } else {
            $("#r_selectedby_" + numbers[1]).removeClass('error-border');
        }

        list.push([numbers[1], actualTime, completedDate, notes, selectedby, reading_value]);
    }

    $.ajax({
        type: "POST",
        url: baseUrl + 'pm/savepmcompletehistory',
        data: {list: list},
        success: function (msg) {
            //console.log(msg);
            $("#success_msg").html(msg + "<span style='padding:" + padding + ";'>");
            window.location.reload();
        }
    });
}

function confirm(highTolerance, lowTolerance, tolerance, wo_id ) {    
    //alert('Range Between' + highTolerance + '-' + lowTolerance);        
    var readingVal = $("#readingValue_" + wo_id).val();
            var regexPattern = /^\d{0,8}(\.\d{1,2})?$/;
            //Allow only Number as well 0nly 2 digit after dot(.)
            if (regexPattern.test(readingVal)) {
                //$(this).css('background-color', 'white');
                $("#readingValue_" + wo_id).removeClass('error-border');
                //return true;
            } else {
                $("#readingValue_" + wo_id).focus();
                //$("#actualTime_" + numbers[1]).css('background-color', 'red');
                $("#readingValue_" + wo_id).addClass('error-border');
                return false;
                //$(this).css('background-color', 'red');
                // $('.err-msg').html('Enter a valid Decimal Number');
            }
    var readingFValue = parseFloat(readingVal);
    if (highTolerance >= readingFValue && lowTolerance <= readingFValue) {
        //alert('Acceptable value');
        $("#readingValue_" + wo_id).css("color", "#000000");
        $("#r_notes_" + wo_id).css("color", "#000000");
        $("#r_notes_" + wo_id).val('');
        $("#r_notes_" + wo_id).attr('readonly', false);
        return true;
    } else {
        //alert('warning - Value is outside acceptable % Tolerance');
        functionConfirm("Warning - Value is outside acceptable " + tolerance + "% tolerance!", function yes() {
            //alert("Yes")
            $("#readingValue_" + wo_id).val('');
            $("#readingValue_" + wo_id).focus();
        }, function no() {
           // alert("no")
            $("#r_notes_" + wo_id).val('**** Out of Tolerance Value Entered ****');
            $("#r_notes_" + wo_id ).css("color", "red");
            $("#readingValue_" + wo_id).css("color", "red");
            //$("#r_notes_" + wo_id).attr('readonly', true);
            $("#readingValue_" + wo_id).attr('readonly', true);
        }
        )
    }
}

function functionConfirm(msg, myYes, myNo, cancel) {
    var confirmBox = $("#confirm");
    confirmBox.find(".message").text(msg);
    confirmBox.find(".yes,.no").unbind().click(function () {
        confirmBox.hide();
    });
    confirmBox.find(".yes").click(myYes);
    confirmBox.find(".no").click(myNo);
    confirmBox.show();
}