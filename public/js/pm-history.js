// PM Work Order Options
function pmHistoryNotesPopup(url)
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
    $(".date-picker")
            .datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                changeYear: true,
                //minDate: 0,
                showButtonPanel: true,
                numberOfMonths: 1,
                dateFormat: 'mm/dd/yy'
            });
    });
    // Close popup cancel button event //
function closepopup() {
    parent.$("body").removeClass("dynamicclass");
    parent.jQuery.fancybox.close();
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
function getFloor() {
    var eqname = $("#equipmentname").val();            
    $.ajax({
        //type: "POST",
        dataType: 'JSON',
        url: baseUrl + 'pmhistory/searchfor',
        data: {eqname: eqname},
        success: function (response) {
            var len = response.length;
            //console.log(response);
            $("#floor").empty();
            $("#floor").append("<option value='0'>Select Floor</option>");
            for( var i = 0; i<len; i++){
                var name = response[i]['Floor'];
                $("#floor").append("<option value='"+name+"'>"+name+"</option>");
            }

        }
    });
}

function getUnit() {
    $('#historySearch').prop('selectedIndex',0);
    var eqname = $("#equipmentname").val();
    var floor = $("#floor").val();
    $.ajax({
        //type: "POST",
        dataType: 'JSON',
        url: baseUrl + 'pmhistory/searchfor',
        data: {eqname: eqname, floor:floor},
        success: function (response) {
            var len = response.length;
            //console.log(response);
            $("#unit").empty();
            $("#unit").append("<option value='0'>Select Unit</option>");
            for( var i = 0; i<len; i++){
                var name = response[i]['Unit'];
                var AU_Equipment_Detail_ID = response[i]['AU_Equipment_Detail_ID'];
                $("#unit").append("<option value='"+AU_Equipment_Detail_ID+"'>"+name+"</option>");
            }

        }
    });
}



function HideShow(){
    var historySearch = $("#historySearch").val();    
    if(historySearch=='All'){
        $("#wonumber").hide();
        $("#daterange").hide();        
    } else if(historySearch=='woNumber'){
        $("#wonumber").show();
        $("#daterange").hide();
        var equipmentDetailId = $("#unit").val(); 
        $.ajax({
        //type: "POST",
        dataType: 'JSON',
        url: baseUrl + 'pmhistory/searchforunit',
        data: {equipmentDetailId: equipmentDetailId},
        success: function (response) {
            var len = response.length;
            console.log(response);
            $("#wonumberfrom").empty();            
            $("#wonumberto").empty();            
            for( var i = 0; i<len; i++){
                //var pmHistoryId = response[i]['PM_History_ID'];
                var pmWoNumber = response[i]['PM_WO_Number'];
                $("#wonumberfrom").append("<option value='"+pmWoNumber+"'>"+pmWoNumber+"</option>");
                $("#wonumberto").append("<option value='"+pmWoNumber+"'>"+pmWoNumber+"</option>");
            }

        }
    });
    } else if(historySearch=='dateRnage'){
        $("#wonumber").hide();
        $("#daterange").show();
        
    }   
    
}

function getfrom(){
    var wofrom = $("#wonumberfrom").val();
    //$("#wonumberto option:selected").val();
    $("#wonumberto option[value='"+wofrom+"']").remove();
    $("#wonumberto").append("<option selected='selected' value='"+wofrom+"'>"+wofrom+"</option>");
    
}

function searchDataForHistoryDetails(){
    
    var alldata = $("#searchform" ).serializeArray();
       
    $.ajax({
        type: "POST",
        //dataType: 'JSON',
        url: baseUrl + 'pmhistory/filterpmhistorydetails',
        //data: {equipmentNameId: equipmentNameId,floor:floor,equipmentDetailId:equipmentDetailId, historySearch:historySearch},
        data: {alldata:alldata},
        success: function (data) {
            $('.loader').hide();
            $('#filterhistorydetails').html(data);
        }
            
    });
    
}

function getDataforreading() {
    //var reading = reading;
    var alldata = $("#searchform" ).serializeArray();
    $.ajax({
        type: "POST",
        url: baseUrl + 'pmhistory/pmhistoryreadingdetails',
        data: {alldata:alldata},
        success: function (data) {
            $('.loader').hide();
            $('#readingdatecompleted').html(data);
        }
    });
}

function getDatafortask(task) {
    //var task = task; 
    var alldata = $("#searchform" ).serializeArray();
    $.ajax({
        type: "POST",
        url: baseUrl + 'pmhistory/pmhistorytaskdetails',
        data: {alldata:alldata},
        success: function (data) {
            $('.loader').hide();
            $('#readingdatecompleted').html(data);
        }
    });
}


function printPMHistoryNotes(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;
     document.body.innerHTML = printContents;
     window.print();
     document.body.innerHTML = originalContents;
}

function sortingNotesByDate() {
    var id = $("#sortingvalue").val(); 
    
    $.ajax({
        type: "POST",
        url: baseUrl + 'pmhistory/sortpmhistorynotesbydate',
        data: {id: id},
        success: function (data) {
           // console.log(data);
            $('#printableArea').html(data);
        }
    });
}

function sortingPMHistoryByWorkOrder() {
    var x = $("#sortingvalue").val();
    var alldata = $("#searchform" ).serializeArray();
    if (x == 1) {
        $("#sortingid").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvalue" id="sortingvalue">');
        id = $("#sortingvalue").val();
    } else {
        $("#sortingid").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvalue" id="sortingvalue">');
        id = $("#sortingvalue").val();
    }
    $.ajax({
        type: "POST",
        url: baseUrl + 'pmhistory/sortpmhistorybyworkorder',
        data: {id: id,alldata:alldata},
        success: function (data) {
           // console.log(data);
            $('#sortphhistorybyworkorder').html(data);
        }
    });
}

function sortingPMHistoryReadingByWorkOrder() {
    var x = $("#sortingvalue").val();
    var alldata = $("#searchform" ).serializeArray();
    if (x == 1) {
        $("#sortingid").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes-alt arrow-shrting"></span><input type="hidden" value="0" name="sortingvalue" id="sortingvalue">');
        id = $("#sortingvalue").val();
    } else {
        $("#sortingid").html('<span class="shorting-icon-white glyphicon glyphicon-sort-by-attributes arrow-shrting"></span><input type="hidden" value="1" name="sortingvalue" id="sortingvalue">');
        id = $("#sortingvalue").val();
    }
    $.ajax({
        type: "POST",
        url: baseUrl + 'pmhistory/sortpmhistoryreadingbyworkorder',
        data: {id: id,alldata:alldata},
        success: function (data) {
           // console.log(data);
            $('#sortphhistorybyworkorder').html(data);
        }
    });
}
