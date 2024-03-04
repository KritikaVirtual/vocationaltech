function openpopup(url)
{
    CheckForSessionpop(baseUrl);
    $('a[href="#CreateNewMultiCon"]').fancybox({
        type: 'iframe',
        href: url,
        width: 700,
        height: 600,
        helpers : { 
            overlay : {closeClick: false}
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

function CheckForSessionpop(baseUrl) {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
        var str="chksession=true";
        jQuery.ajax({
                type: "POST",
                url : baseUrl+"user/checksession",
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

function ValidationReading(type){
    
    var frequencydata = "";
    var typeaction = type;
    var subset_level  = $("#subset_level").val();
    var reading_instruction  = $("#reading_instruction").val();    

    var startDate  = $("#startDate").val();
    var startdateofmonth  = $("#sdom").val();
    var startdateadjustment  = $("#startdateadjustment").val();
    var taskjobtime  = $("#taskjobtime").val();
    var overtime  = $("#overtime").val();
    var assignedto  = $("#assignedto").val();
    var $valid = true;
    var action  = $("#action").val();
    var reading_id = $("#reading_id").val();
    var desig_id = $('#desig_id').val();    
    var reading_val = $('#reading_val').val();
    var Interval_Value = 1;
    var seasonal = "";
    var begDate = "";
    var stopDate = "";
    var taskjobtime = "";
    var Frequency_ID = "";
    if($("#freq:checked").length===1){
        Frequency_ID = $("#frequency").val();
    }else{
        Interval_Value = $("#freq_num").val();
        Frequency_ID = $("#frequency1").val();
    }
      
    var startDate  = $("#startDate").val();
    var endDate  = $("#endDate").val();
    
    if($("#seasonal:checked").length===1){
        seasonal = 'Y';
        begDate = $("#begDate").val();
        stopDate = $("#stopDate").val();      
    }else{
        seasonal = 'N';
        begDate = "-";//$("#begDate").val("");
        stopDate = "-";//$("#stopDate").val("");
    }
    
 
    var startdateofmonth  = $("#sdom").val();
    var startdateadjustment  = $("#startdateadjustment").val();
    if($("#domlast:checked").length === 1){
        var startdateofmonth  = $("#domlast").val();
    }else{
        var startdateofmonth  = $("#sdom").val();
    }
    
    if($("#jobtime1:checked").length===1){
        taskjobtime = $("#taskjobtime").val();
    }else{
        custome_hours = $("#custome_hours").val();
        if(custome_hours > 59){
            $("#task_instruction").focus();
           $(".job_time_error").html("Please inter Below 59 ");
           $valid =  false;
       }else{
           $(".job_time_error").html("");
       }
        taskjobtime1 = $("#taskjobtime1").val();
        taskjobtime = parseFloat(custome_hours) + parseFloat(taskjobtime1);
    }
    
    
    if(reading_val==""){
        $("#reading_val").focus();
        $(".task_reading_val_error").html("Please Enter Reading Value");
        $valid =  false;
    }else{
        $(".task_reading_val_error").html("");
    }
    var tolerance = $('#tolerance').val();
    
    if(tolerance == ""){
        $("#task_instruction").focus();
        $(".tolerance_error").html("Please Enter Tolerance value");
        $valid =  false;
    }else{
        $(".tolerance_error").html("");
    }
    var unitofmeasure = $("#unitofmeasure").val();
    if(reading_instruction == ""){
        $("#task_instruction").focus();
        $(".task_inst_error").html("Please Enter reading Instruction ");
        $valid =  false;
    }else{
        $(".task_inst_error").html("");
    }
    if(startDate ==""){
        $("#startDate").focus();
        $(".startDate_error").html("Please Enter Start date ");
        $valid = false;
    }else{
        $(".startDate_error").html("");
    }
    
    if($valid === false){
        return true;
    }else{
        $('.loader').show();
        if(action=='update'){
             $.ajax({    
                    type: "POST",
                    url: baseUrl + 'pm/updatereading',
                    data: {
                        parent_id: subset_level,
                        Reading_Instruction:reading_instruction,
                        AU_Frequency_ID:Frequency_ID,
                        Interval_Value:Interval_Value,
                        AU_uom_ID:unitofmeasure,
                        Reading_Value:reading_val,
                        Tolerance:tolerance,
                        Start_date:startDate,
                        End_date:endDate,
                        Seasonal_Task:seasonal,
                        Seasonal_Start_Date:begDate,
                        Seasonal_End_Date:stopDate,
                        Startdate_month:startdateofmonth,
                        AU_sda_ID:startdateadjustment,
                        Task_jobtime:taskjobtime,
                        Assigned_to:assignedto,
                        Overtime:overtime,                    
                        reading_id:reading_id
                    },
                    success: function (data) {
                        
                        var content = $.parseJSON(data);
                        if (content.status == 'success') {
                            $('.success_message').html(content.msg);

                            $('.loader').hide();
                             if(typeaction === 'new'){
                                 parent.$("#reading_instruction").removeAttr('value');
                             }else{
                                  parent.jQuery.fancybox.close();
                                  parent.view_all_Reading(desig_id);
                             }
                        } else {
                            $('.success_message').html(content.msg);
                            alert('There was an error');
                        }
                    }
                });
        }else{
            $.ajax({    
                type: "POST",
                url: baseUrl + 'pm/savereading',
                data: {
                    parent_id: subset_level,
                    Reading_Instruction:reading_instruction,
                    AU_Frequency_ID:Frequency_ID,
                    Interval_Value:Interval_Value,
                    AU_uom_ID:unitofmeasure,
                    Reading_Value:reading_val,
                    Tolerance:tolerance,
                    Start_date:startDate,
                    End_date:endDate,
                    Seasonal_Task:seasonal,
                    Seasonal_Start_Date:begDate,
                    Seasonal_End_Date:stopDate,
                    Startdate_month:startdateofmonth,
                    AU_sda_ID:startdateadjustment,
                    Task_jobtime:taskjobtime,
                    Assigned_to:assignedto,
                    Overtime:overtime,                    
                    VT_Template_Designation_ID:desig_id
                    
                },
                success: function (data) {
                    //  console.log(data);
                    $('.loader').hide();
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        //$("#reading_instruction").removeAttr('value'); 
                        //view_Add_Reading(desig_id);
                        //view_all_Reading(desig_id);
                        $('.loader').hide();
                             if(typeaction === 'new'){
                                 $("#reading_instruction").removeAttr('value');
                             }else{
                                  parent.jQuery.fancybox.close();
                                  parent.view_all_Reading(desig_id);
                             }
                    } else {
                        //  $('#emailsave').removeAttr('disabled');
                        $('.success_message').html(content.msg);
                        alert('There was an error');
                    }
                }
            }); 
        }
 
       
    }   
    
}

// Edit Reading section 

function editreading(id,desig_id){
    var task_id = id;
    
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/editreading',
            data: {task_id: task_id,desig_id:desig_id},
            success: function (data) {
                //alert(data);
                //$(".add-task").html("");
                $(".add-reading").html(data);
               
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    //parent.jQuery.fancybox.close();
                    //  setInterval(function(){	
                    //      parent.location.reload();
                    //  }, 1500);
                } else {
                    //  $('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
}

// delete Reading section 

function deletereading(reading_id,desig_id)
{

                $('.loader').show();
                $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'pm/deletereading',
                    data: {reading_id: reading_id},
                    success: function (data) {
                        console.log(data);                        
                        var content = $.parseJSON(data);
                        if (content.status == 'success') {
                            $('.success_message').html(content.msg);
                            $('.loader').hide();
                            //view_Add_Reading(desig_id);
                            view_all_Reading(desig_id);
                        } else {
                            $('#success_message').html(content.msg);

                        }
                    }
                });
            
    
}

function addReadingSubset(desig_id) {

    $('.loader').show();
    $.ajax({
        url: baseUrl + "pm/createreadingsubset",
        type: "post",
        data: {desig_id: desig_id},
        success: function (data) {
            $('.loader').hide();
            //$('#edit_wo_form').show();
            $('#file_form').html(data);
            $('#file_form_href').trigger('click');
            $('#file_form').show();
            $('.fade_default_opt').show();
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }

    });
}

// valdate reading section

function validatesubsetreading()
   {
    var subsetname = $("#subsetname").val();
    var subsetname_id = $("#subsetname_id").val();
    $valid = true;
    if(subsetname === ""){
        $("#subsetname").focus();
        $("#tempate_name_error").html("Please Enter Subset Name");
        $valid = false;
    }else{        
        $("#tempate_name_error").html("");
    }
    
    if($valid === false){
        return false;
    }else{
        $('.loader').show();
        $.ajax({
                type: "POST",
                url: baseUrl + 'pm/validatesubsetreading',
                data: {subsetname: subsetname,subsetname_id:subsetname_id},
                success: function (msg) {
                    console.log(msg);
                    $('.loader').hide();
                    //if(msg == 'true'){
                    if(msg != ''){
                        $('#tempate_name_error').html("");
                        if(subsetname_id!=""){
                             update_subset_reading();
                        }else{
                            create_subset_reading();
                        }
                       
                    }else{
                        $('.loader').hide();
                        console.log("already exit");
                        $('#tempate_name_error').html("Template Name already in use.");
                        $('#templateName').focus();
                        return false;
                    }
                }
            });
    } 

    }
    
    function create_subset_reading(){
        var subsetname  = $("#subsetname").val();
        var desig_id = $("#desig_id").val();

        $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/savereadingsubset',
            data: {Reading_Instruction:subsetname,desig_id:desig_id},
            success: function (data) {
                console.log(data);
                console.log("insert Id");
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                        //console.log(content);
                        showsubsetreading(desig_id,content.InsertId);
                        $('.success_message').html(content.msg);
                        $("div.fancybox-close").trigger("click");
                        $('#file_form').html('');
                        $('#file_form').hide();
                        $('.fade_default_opt').hide();
                        
                        //parent.view_Add_Reading(desig_id);
                       // parent.view_all_Reading(desig_id);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
    }
    
    function update_subset_reading(){
        var subsetname  = $("#subsetname").val();
        var subsetname_id = $("#subsetname_id").val();
        var desig_id = $("#desig_id").val();
        $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/updatereadingsubset',
            data: {reading_instruction: subsetname,subsetname_id:subsetname_id},
            success: function (data) {
                console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                        parent.jQuery.fancybox.close();
                        //parent.view_Add_Reading(desig_id);
                        parent.view_all_Reading(desig_id);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
    }

/*  Group modication sction start */

/* 
 *  Update frequency group modiaction root
 */
function update_readingroot_frequency(id){
    
    Interval_Value ="";
    Frequency_ID = "";
    
    if($("#freq:checked").length===1){
        Frequency_ID = $("#frequency").val();
    }else{
        Interval_Value = $("#freq_num").val();
        Frequency_ID = $("#frequency1").val();
    }
    
    //var Interval_Value = $("#noofday").val();
    //var Frequency_ID = $("#type").val();
    //var frequency = noofday + ' ' + type;
    var includesubset = "";
    if($('#includesubset:checked').length==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingrootfrequeancy',
                data: {desig_id: desig_id,Interval_Value:Interval_Value,Frequency_ID:Frequency_ID,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                        parent.jQuery.fancybox.close();
                        //parent.view_Add_Reading(desig_id);
                        parent.view_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
        });
}

/* 
 *  Update frequency group modiaction Subset
 */

function update_readingsubset_frequency(id){
     Interval_Value ="";
     Frequency_ID = "";
    
    if($("#freq:checked").length===1){
        Frequency_ID = $("#frequency").val();
    }else{
        Interval_Value = $("#freq_num").val();
        Frequency_ID = $("#frequency1").val();
    }  
    var parent_id = $('#parent_id').val();
    
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingfrequeancysubset',
                data: {desig_id: desig_id,Interval_Value:Interval_Value,Frequency_ID:Frequency_ID,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_all_Reading(desig_id);
                           // parent.location.reload();
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/*
 * Start date update on root 
 */  
function update_readingroot_startdate(id){
    var startdate = $("#startdate").val();
    var includesubset = "";
    if($('#includesubset:checked').val()==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    if(startdate==""){
        $("#startdate").focus();
        $("#error_startdate").html("Please Select The Start Date");
        return false;
    } 
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingrootstartdate',
                data: {desig_id: desig_id,startdate:startdate,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_all_Reading(desig_id);

                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/*
 * Start date update on Subset 
 */
function update_readingsubset_startdate(id){
    var startdate = $("#startdate").val();
    var parent_id = $('#parent_id').val();
    if(startdate==""){
        $("#startdate").focus();
        $("#error_startdate").html("Please Select Start Date");
        return false;
    } 
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingsubsetstartdate',
                data: {desig_id: desig_id,startdate:startdate,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/*
 * Start date of Month update on root 
 */ 
function update_readingroot_startdateofmonth(id){
    var startdateofmonth = $("#startdateofmonth").val();
    var includesubset = "";
    if($('#includesubset:checked').val()==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    if(startdateofmonth==""){
        $("#startdateofmonth").focus();
        $("#error_startdateofmonth").html("Please Select The Start Date");
        return false;
    } 
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingrootstartdateofmonth',
                data: {desig_id: desig_id,startdateofmonth:startdateofmonth,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);
                    }
                }
            });
}

/*
 * Start date of Month update on Subset 
 */ 
function update_readingsubset_startdateofmonth(id){
    var startdateofmonth = $("#startdateofmonth").val();
    var parent_id = $('#parent_id').val();
    if(startdateofmonth==""){
        $("#startdateofmonth").focus();
        $("#error_startdateofmonth").html("Please Select Start Date of Month");
        return false;
    } 
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingsubsetstartdateofmonth',
                data: {desig_id: desig_id,startdateofmonth:startdateofmonth,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/*
 * Start date Adjustment update on Root 
 */

function update_readingroot_startdateadjustment(id){
    var startdateadjustment = $("#startdateadjustment").val();
    var includesubset = "";
    if($('#includesubset:checked').val()==1){
       includesubset = $('#includesubset:checked').val(); 
    }

    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingrootstartdateadjustment',
                data: {desig_id: desig_id,startdateadjustment:startdateadjustment,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
            });
}

/*
 * Start date Adjustment update on Subset
 */
function update_readingsubset_startdateadjustment(id){
    var startdateadjustment = $("#startdateadjustment").val();
    var parent_id = $('#parent_id').val();
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingsubsetstartdateadjustment',
                data: {desig_id: desig_id,startdateadjustment:startdateadjustment,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/*
 *  Reading value update and root
 */

function update_readingroot_readingvalue(id){
    var readingvalue = $("#readingvalue").val();
    var includesubset = "";
    if($('#includesubset:checked').val()==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    if(readingvalue==""){
        $("#error_readingvalue").html("Please Enter Reading Value!");
        $("#readingvalue").focus();
        return false;
    }else{
        $("#error_readingvalue").html("");
    }

    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingrootreadingvalue',
                data: {desig_id: desig_id,reading_value:readingvalue,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
            });
}

/*
 * Reading value update and subset
 */
function update_readingsubset_readingvalue(id){
    var readingvalue = $("#readingvalue").val();
    var parent_id = $('#parent_id').val();
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingsubsetreadingvalue',
                data: {desig_id: desig_id,readingvalue:readingvalue,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}


/*
 *  Reading Unit Of Measure update and root
 */

function update_readingroot_unitofmeasure(id){
    var unitofmeasure = $("#unitofmeasure").val();
    var includesubset = "";
    if($('#includesubset:checked').val()==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    if(unitofmeasure==""){
        $("#error_unitofmeasure").html("Please Enter Reading Value!");
        $("#unitofmeasure").focus();
        return false;
    }else{
        $("#error_readingvalue").html("");
    }

    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingrootunitofmeasure',
                data: {desig_id: desig_id,unitofmeasure:unitofmeasure,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);
                    }
                }
            });
}

/*
 * Reading Unit Of Measure update and subset
 */
function update_readingsubset_unitofmeasure(id){
    var unitofmeasure = $("#unitofmeasure").val();
    var parent_id = $('#parent_id').val();
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingsubsetunitofmeasure',
                data: {desig_id: desig_id,unitofmeasure:unitofmeasure,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}



/*
 *  Tolerance update and root
 */

function update_readingroot_tolerance(id){
    var tolerance = $("#tolerance").val();
    var includesubset = "";
    if($('#includesubset:checked').val()==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    if(tolerance==""){
        $("#error_unitofmeasure").html("Please Enter tolerance!");
        $("#tolerance").focus();
        return false;
    }else{
        $("#error_readingvalue").html("");
    }

    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatereadingroottolerance',
                data: {desig_id: desig_id,tolerance:tolerance,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);
                    }
                }
            });
}

/*
 * Tolerance update and subset
 */
function update_readingsubset_tolerance(id){
    var tolerance = $("#tolerance").val();
    var parent_id = $('#parent_id').val();
    var desig_id = id;
    $.ajax({
             type: "POST",
             datatype: 'json',
             url: baseUrl + 'pm/updatereadingsubsettolerance',
             data: {desig_id: desig_id,tolerance:tolerance,parent_id:parent_id},
             success: function (data) {
                 console.log(data);                        
                 var content = $.parseJSON(data);
                 if (content.status == 'success') {
                     $('.success_message').html(content.msg);
                     $('.loader').hide();
                     parent.jQuery.fancybox.close();
                     //parent.view_Add_Reading(desig_id);
                     parent.view_all_Reading(desig_id);
                 } else {
                     $('#success_message').html(content.msg);
                 }
             }
         });
}

/* End group modification section */

function importreading(desig_id) {

    $('.loader').show();
    $.ajax({
        url: baseUrl + "pm/importreading",
        type: "post",
        data: {desig_id: desig_id},
        success: function (data) {
            $('.loader').hide();
            //$('#edit_wo_form').show();
            $('#file_form').html(data);
            $('#file_form_href').trigger('click');
            $('#file_form').show();
            $('.fade_default_opt').show();
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }

    });
}

/* Import the data */
function importApplyReading(id){
    var import_id = $("#designation").val();
    
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/importapply',
            data: {desig_id: id,import_id:import_id},
            success: function (response) {
                //alert(response);
                var content = $.parseJSON(response);
                $('.success_message').html(content.msg);
                $('.loader').hide();
                     $('#file_form').hide();
                    $('.fade_default_opt').hide();
                    //parent.jQuery.fancybox.close(); 
                    //window.location = window.location.href;
                    setInterval(function(){	
                         parent.location.reload();                          
                      }, 1500);
                
                parent.view_Add_Reading(id);
                
            }
     });
}


    
/// common function  view all task and add new task

function view_Add_Reading(desig_id){
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/viewaddreading/desig_id/'+desig_id,
            data: {task: "empty"},
            success: function (response) {
                //alert(response);
                $(".add-reading").html(response);
                $('.success_message').slideUp(500);
            }
     });
}
function view_all_Reading(desig_id){
    //alert(desig_id);
    //////////////////////
    var list  = "";
    $("input[type='checkbox']:checked").each(function(){
        console.log($(this).val());  
        list += parseInt($(this).val())+","; 
    });
    list =list.substring(0,list.length - 1);
    var all = list.split(',');
    var ctotal = all.length;
    console.log("total "+ctotal);
    cssdata = 93 / (ctotal + 1) ;
    csscolapsdata = 91.25 / (ctotal + 1) ;
    var html = ".dd3-content .id{";
        html += "width : " + cssdata+ "%";
        html +="}";
        html += ".collapse-table .dd3-content .id{";
        html += "width : " + csscolapsdata+ "%";
        html +="}";
    //////////////////////////////
    console.log("Call the View function");
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/viewreading/desig_id/'+desig_id,
            data: {viewlist: list},
            success: function (response) {
                //alert(response);
                $('.loader').hide();                
                $("#readingview").html(response);
                $("#readingclass").html('<style>'+html+'</style>');
                $('.success_message').slideUp(500);
            }
     });
}

// Close popup cancel button event //
function closepopup() {
    parent.jQuery.fancybox.close();
}

function view_update(desig_id){
    var  list = "";
    $(".showlistoption[type='checkbox']:checked").each(function(){
        console.log($(this).val());  
        list += parseInt($(this).val())+","; 
    });
    //alert(list);
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/updateviewtask/',
            data: {viewlist: list,type:'Reading'},
            success: function (response) {
                //alert(response);
               // $('.loader').hide();                
                //$("#viewtask").html(response);
                //$('.success_message').slideUp(500);
                //$( "#addcss" ).html( '<style>'+html+'</style>' );
            }
     });
    view_all_Reading(desig_id);
    //return false;
}


/// validate number of decimal place 

var xTriggered = 0;
$( "#reading_val" ).keydown(function( event ) {
    //console.log(event.which);
    var value = event.which;    //parseFloat(obj.value).toFixed(2);
    var data = value.split('.');
    count = data[1].length;
    if ( count > 2 ){
     //event.preventDefault();
     return false;
    }
//  xTriggered++;
//  var msg = "Handler for .keydown() called " + xTriggered + " time(s).";
//  $.print( msg, "html" );
//  $.print( event );
});



function setTwoNumberDecimal(evt){    
    var charCode = (evt.which) ? evt.which : window.event.keyCode;
    if(charCode == 8){
        return true;
    }    
    var value = $("#reading_val").val();//evt.key;  
    var data = value.split('.');
    count = data[1].length;
    if(count > 2) {
        return false;
    }
    //  console.log("count"+data[1].length);
    //  $('#e').val(value);
    //  console.log(value);
    //  console.log(obj.value);
    //  aparseFloat(obj.value).toFixed(2);
}

function setthreeNumberDecimal(evt){
    var charCode = (evt.which) ? evt.which : window.event.keyCode;
    if(charCode == 8){
        return true;
    }    
    var value = $("#reading_val").val();//evt.key;  
    var data = value.split('.');
    count = data[1].length;
    if(count > 2) {
        return false;
    }
}

function check_freq(){
    //console.log();
    if($("#freq:checked").length==1){
        $("#frequency1").attr("disabled",true);
        $("#frequency1").prop('selectedIndex',0);
        $("#freq_num").attr("disabled",true);
        $("#freq_num").attr('value',"");
        $("#frequency").attr("disabled",false);
        
    }else{
        $("#frequency").attr("disabled",true);
        $("#frequency").prop('selectedIndex',0);
        $("#frequency1").attr("disabled",false);
        $("#freq_num").attr("disabled",false);
    }       
}

function jobtimeact(){
    if($("#jobtime1:checked").length==1){
        $("#taskjobtime1").attr("disabled",true);
        $("#custome_hours").attr("disabled",true);
        $("#custome_hours").prop('selectedIndex',0);
        $("#taskjobtime").attr("disabled",false);
        $("#taskjobtime1").prop('selectedIndex',0);
    }else{
        $("#taskjobtime").attr("disabled",true);
        $("#taskjobtime").prop('selectedIndex',0);
        $("#taskjobtime1").attr("disabled",false);
        $("#custome_hours").attr("disabled",false);
    }   
}

function lastday(){
    if($("#domlast:checked").length==1){
        $("#sdom").attr("disabled",true);
    }else{
        $("#sdom").attr("disabled",false);
    }
    
}

function seasonal(){
    if($("#seasonal:checked").length == 1){
        $("#begDate").removeAttr("disabled");
        $("#stopDate").removeAttr("disabled");
    }else{
        $("#begDate").attr('disabled','disabled');
        $("#stopDate").attr('disabled','disabled');
    }
}
function showsubsetreading(desig_id,IncId){
    console.log(desig_id);
    //$('.loader').show();
    var i;
    $.ajax({
        url: baseUrl + "pm/getsubsetreading",
        type: "post",
        data: {desig_id: desig_id},
        success: function (data) {
            var html = '<option value="0">Root</option>';
            var content = $.parseJSON(data);
            console.log(content);
            $(content).each(function(i,obj){
                console.log(obj.VT_Template_Reading_ID);
                html +='<option value="'+obj.VT_Template_Reading_ID+'" '+(obj.VT_Template_Reading_ID==IncId?"Selected ":"")+'>'+obj.Reading_Instruction+'</option>';
            });
            console.log(html);
            $("#subset_level").html(html);
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }

    });
}


function numbersonly(e,control)
{
    //alert('numbersonly')
    var unicode=e.charCode? e.charCode : e.keyCode;
    if (unicode!=8 && unicode!=46) //if the key isn't the backspace key (which we should allow)
    {
           if (unicode<48||unicode>57) //if not a number
           return false; //disable key press
           var character = String.fromCharCode(unicode);
           var val = control.value+character;
 
           if(val>100)
           {
               return false;
           }
 
           if(String(val).indexOf(".")!=-1)
           {
               if (String(val).indexOf(".")< (String(val).length - 3))
               {
                   return false;
               }
           }
    }
}
function float_validation(event, value){
    if(event.which <= 45 || event.which > 58 || event.which == 47 ) {
          return false;
            event.preventDefault();
        } // prevent if not number/dot

        if(event.which == 46 && value.indexOf('.') != -1) {
            return false;
           // event.preventDefault();
        } // prevent if already dot
        var number = value.split('.');
        if (number[1].length == 2 )
            return false;
//        console.log(value);
//        console.log(number);
//        console.log(number[1]);
//        console.log(number[1].length);
        //console.log(value.match(/^\d*(.\d{0,2})?$/));
//          console.log(event.which);
//            if(event.which == 45 && value.indexOf('-') != -1) {
//                return false;
//            event.preventDefault();
//        } // prevent if already dot

        if(event.which == 45 && value.length>0) {
            event.preventDefault();
        } // prevent if already -

    return true;

}
function float_validation_three(event, value){
    if(event.which <= 45 || event.which > 58 || event.which == 47 ) {
          return false;
            event.preventDefault();
        } // prevent if not number/dot

        if(event.which == 46 && value.indexOf('.') != -1) {
            return false;
           // event.preventDefault();
        } // prevent if already dot
        var number = value.split('.');
        if (number[1].length == 3 )
            return false;
//        console.log(value);
//        console.log(number);
//        console.log(number[1]);
//        console.log(number[1].length);
        //console.log(value.match(/^\d*(.\d{0,2})?$/));
//          console.log(event.which);
//            if(event.which == 45 && value.indexOf('-') != -1) {
//                return false;
//            event.preventDefault();
//        } // prevent if already dot

        if(event.which == 45 && value.length>0) {
            event.preventDefault();
        } // prevent if already -

    return true;

}
function cancelsubsetpopup() {
    $("div.fancybox-close").trigger("click");
    $('#file_form').html('');
    $('#file_form').hide();
    $('.fade_default_opt').hide();
}

function begDate(){
  
      //stopdate();
      var data = $("#begDate").val();
      //alert(data);
        $('#stopDate option').show();
        $('#stopDate option[value="'+data+'"]').hide();
        var next = data;
        //alert(next);
        $('#stopDate option[value="'+data+'"]').next().attr("selected",true);
  
}





/* ********************* Equipment Section ********************************************** */
//validate sub set 



function addequipmenttemplateReadingSubset(desig_id) {

    $('.loader').show();
    $.ajax({
        url: baseUrl + "pm/createequipmenttemplatereadingsubset",
        type: "post",
        data: {desig_id: desig_id},
        success: function (data) {
            $('.loader').hide();
            //$('#edit_wo_form').show();
            $('#file_form').html(data);
            $('#file_form_href').trigger('click');
            $('#file_form').show();
            $('.fade_default_opt').show();
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }

    });
}


function validateequipmenttemplatereadingsubset()
   {
    var subsetname = $("#subsetname").val();
    var subsetname_id = $("#subsetname_id").val();
    var desig_id = $("#desig_id").val();
    $valid = true;
    if(subsetname === ""){
        $("#subsetname").focus();
        $("#tempate_name_error").html("Please Enter Subset Name");
        $valid = false;
    }else{        
        $("#tempate_name_error").html("");
    }
    
    if($valid === false){
        return false;
    }else{
        $('.loader').show();
         $.ajax({
                type: "POST",
                url: baseUrl + 'pm/validateequipmenttemplatesubsetreading',
                data: {subsetname: subsetname,subsetname_id:subsetname_id,desig_id:desig_id},
                success: function (msg) {
                    //console.log(msg);
                    //return false;
                    $('.loader').hide();
                   // if(msg == 'true'){
                   if(msg != ''){
                        $('#tempate_name_error').html("");
                        if(subsetname_id!=""){
                             updateequipmenttemplate_subset();
                        }else{
                            createequipmenttemplate_subset();
                        }
                       
                    }else{
                        $('.loader').hide();
                        console.log("already exit");
                        $('#tempate_name_error').html("Subset Name already in use.");
                        $('#templateName').focus();
                        return false;
                    }
                }
            });
    } 
    
}

function createequipmenttemplate_subset(){
    var subsetname  = $("#subsetname").val();
    var desig_id = $("#desig_id").val();
        $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/saveequipmenttemplatereadingsubset',
            data: {Reading_Instruction: subsetname,AU_Template_Designation_ID:desig_id},
            success: function (data) {
                console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    showequipmenttemplatereadingsubset(desig_id,content.InsertId);
                    $('.success_message').html(content.msg);
                    $("div.fancybox-close").trigger("click");
                    $('#file_form').html('');
                    $('#file_form').hide();
                    $('.fade_default_opt').hide();
                } else {
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
}

function showequipmenttemplatereadingsubset(desig_id,IncId){
    //console.log("test"+IncId);
    
    //$('.loader').show();
    var i;
    $.ajax({
        url: baseUrl + "pm/getequipmenttemplatesubsetreading",
        type: "post",
        data: {desig_id: desig_id},
        success: function (data) {
            var html = '<option value="0">Root</option>';
            var content = $.parseJSON(data);
            console.log(content);
            $(content).each(function(i,obj){
                console.log(obj);
                html +='<option value="'+obj.AU_Template_Reading_ID+'" '+(obj.AU_Template_Reading_ID==IncId?"Selected":"")+'>'+obj.Reading_Instruction+'</option>';
            });
            //console.log(html);
            $("#subset_level").html(html);
            cancelsubsetpopup();
            return true;
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }

    });
}
// popup Section Active

function updateequipmenttemplate_subset(){
    var subsetname  = $("#subsetname").val();
    var subsetname_id = $("#subsetname_id").val();
     var desig_id = $("#desig_id").val();
        $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/updateequipmenttemplatereadingsubset',
            data: {Task_Instruction: subsetname,subsetname_id:subsetname_id,AU_Template_Designation_ID: desig_id},
            success: function (data) {
                console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                        parent.jQuery.fancybox.close();
                        //parent.view_Add_Task(temp_id);
                        parent.viewequipmenttemplate_all_Reading(desig_id);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
}


    
function ValidationEquipmenttemplateReading(type){
    
    var frequencydata = "";
    var typeaction = type;
    var subset_level  = $("#subset_level").val();
    var reading_instruction  = $("#reading_instruction").val();    

    var startDate  = $("#startDate").val();
    var startdateofmonth  = $("#sdom").val();
    var startdateadjustment  = $("#startdateadjustment").val();
    var taskjobtime  = $("#taskjobtime").val();
    var overtime  = $("#overtime").val();
    var assignedto  = $("#assignedto").val();
    var $valid = true;
    var action  = $("#action").val();
    var reading_id = $("#reading_id").val();
    var desig_id = $('#desig_id').val();    
    var reading_val = $('#reading_val').val();
    var Interval_Value = 1;
    var seasonal = "";
    var begDate = "";
    var stopDate = "";
    var taskjobtime = "";
    var Frequency_ID = "";
    if($("#freq:checked").length===1){
        Frequency_ID = $("#frequency").val();
    }else{
        Interval_Value = $("#freq_num").val();
        Frequency_ID = $("#frequency1").val();
    }
      
    var startDate  = $("#startDate").val();
    var endDate  = $("#endDate").val();
    
    if($("#seasonal:checked").length===1){
        seasonal = 'Y';
        begDate = $("#begDate").val();
        stopDate = $("#stopDate").val();      
    }else{
        seasonal = 'N';
        begDate = "-";//$("#begDate").val("");
        stopDate = "-";//$("#stopDate").val("");
    }
    
 
    var startdateofmonth  = $("#sdom").val();
    var startdateadjustment  = $("#startdateadjustment").val();
    if($("#domlast:checked").length === 1){
        var startdateofmonth  = $("#domlast").val();
    }else{
        var startdateofmonth  = $("#sdom").val();
    }
    
    if($("#jobtime1:checked").length===1){
        taskjobtime = $("#taskjobtime").val();
    }else{
        custome_hours = $("#custome_hours").val();
        if(custome_hours > 59){
            $("#task_instruction").focus();
           $(".job_time_error").html("Please inter Below 59 ");
           $valid =  false;
       }else{
           $(".job_time_error").html("");
       }
        taskjobtime1 = $("#taskjobtime1").val();
        taskjobtime = parseFloat(custome_hours) + parseFloat(taskjobtime1);
    }
    
    
    if(reading_val==""){
        $("#reading_val").focus();
        $(".task_reading_val_error").html("Please Enter Reading Value");
        $valid =  false;
    }else{
        $(".task_reading_val_error").html("");
    }
    var tolerance = $('#tolerance').val();
    
    if(tolerance == ""){
        $("#task_instruction").focus();
        $(".tolerance_error").html("Please Enter Tolerance value");
        $valid =  false;
    }else{
        $(".tolerance_error").html("");
    }
    var unitofmeasure = $("#unitofmeasure").val();
    if(reading_instruction == ""){
        $("#task_instruction").focus();
        $(".task_inst_error").html("Please Enter reading Instruction ");
        $valid =  false;
    }else{
        $(".task_inst_error").html("");
    }
    if(startDate ==""){
        $("#startDate").focus();
        $(".startDate_error").html("Please Enter Start date ");
        $valid = false;
    }else{
        $(".startDate_error").html("");
    }
    
    if($valid === false){
        return true;
    }else{
        $('.loader').show();
        if(action=='update'){
             $.ajax({    
                    type: "POST",
                    url: baseUrl + 'pm/updateequipmenttemplatereading',
                    data: {
                        parent_id: subset_level,
                        Reading_Instruction:reading_instruction,
                        AU_Frequency_ID:Frequency_ID,
                        Interval_Value:Interval_Value,
                        AU_uom_ID:unitofmeasure,
                        Reading_Value:reading_val,
                        Tolerance:tolerance,
                        Start_date:startDate,
                        End_date:endDate,
                        Seasonal_Task:seasonal,
                        Seasonal_Start_Date:begDate,
                        Seasonal_End_Date:stopDate,
                        Startdate_month:startdateofmonth,
                        AU_sda_ID:startdateadjustment,
                        Task_jobtime:taskjobtime,
                        Assigned_to:assignedto,
                        Overtime:overtime,                    
                        reading_id:reading_id
                    },
                    success: function (data) {
                        
                        var content = $.parseJSON(data);
                        if (content.status == 'success') {
                            $('.success_message').html(content.msg);

                            $('.loader').hide();
                             if(typeaction === 'new'){
                                 parent.$("#reading_instruction").removeAttr('value');
                             }else{
                                  parent.jQuery.fancybox.close();
                                  parent.view_equipmenttemplate_all_Reading(desig_id);
                             }
                        } else {
                            $('.success_message').html(content.msg);
                            alert('There was an error');
                        }
                    }
                });
        }else{
            $.ajax({    
                type: "POST",
                url: baseUrl + 'pm/saveequipmenttemplatereading',
                data: {
                    parent_id: subset_level,
                    Reading_Instruction:reading_instruction,
                    AU_Frequency_ID:Frequency_ID,
                    Interval_Value:Interval_Value,
                    AU_uom_ID:unitofmeasure,
                    Reading_Value:reading_val,
                    Tolerance:tolerance,
                    Start_date:startDate,
                    End_date:endDate,
                    Seasonal_Task:seasonal,
                    Seasonal_Start_Date:begDate,
                    Seasonal_End_Date:stopDate,
                    Startdate_month:startdateofmonth,
                    AU_sda_ID:startdateadjustment,
                    Task_jobtime:taskjobtime,
                    Assigned_to:assignedto,
                    Overtime:overtime,                    
                    AU_Template_Designation_ID:desig_id
                    
                },
                success: function (data) {
                    //  console.log(data);
                    $('.loader').hide();
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        //$("#reading_instruction").removeAttr('value'); 
                        //view_Add_Reading(desig_id);
                        //view_equipmenttemplate_all_Reading(desig_id);
                        $('.loader').hide();
                             if(typeaction === 'new'){
                                 $("#reading_instruction").removeAttr('value');
                             }else{
                                  parent.jQuery.fancybox.close();
                                  parent.view_equipmenttemplate_all_Reading(desig_id);
                             }
                    } else {
                        //  $('#emailsave').removeAttr('disabled');
                        $('.success_message').html(content.msg);
                        alert('There was an error');
                    }
                }
            }); 
        }
 
       
    }   
    
}

// Edit Reading section 

function editreading(id,desig_id){
    var task_id = id;
    
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/editequipmenttemplatereading',
            data: {task_id: task_id,desig_id:desig_id},
            success: function (data) {
                //alert(data);
                //$(".add-task").html("");
                $(".add-reading").html(data);
               
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    //parent.jQuery.fancybox.close();
                    //  setInterval(function(){	
                    //      parent.location.reload();
                    //  }, 1500);
                } else {
                    //  $('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
}

// delete Reading section 

function deleteequipmenttemplatereading(reading_id,desig_id)
{

                $('.loader').show();
                $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'pm/deleteequipmenttemplatereading',
                    data: {reading_id: reading_id},
                    success: function (data) {
                        console.log(data);                        
                        var content = $.parseJSON(data);
                        if (content.status == 'success') {
                            $('.success_message').html(content.msg);
                            $('.loader').hide();
                            //view_Add_Reading(desig_id);
                            view_equipmenttemplate_all_Reading(desig_id);
                        } else {
                            $('#success_message').html(content.msg);

                        }
                    }
                });
            
    
}

// valdate reading section

function validateequipmenttemplatesubsetreading()
   {
    var subsetname = $("#subsetname").val();
    var subsetname_id = $("#subsetname_id").val();
    $valid = true;
    if(subsetname === ""){
        $("#subsetname").focus();
        $("#tempate_name_error").html("Please Enter Subset Name");
        $valid = false;
    }else{        
        $("#tempate_name_error").html("");
    }
    var desig_id = $("#desig_id").val();
    if($valid === false){
        return false;
    }else{
        $('.loader').show();
        $.ajax({
                type: "POST",
                url: baseUrl + 'pm/validateequipmenttemplatesubsetreading',
                data: {subsetname: subsetname,subsetname_id:subsetname_id,desig_id:desig_id},
                success: function (msg) {
                    console.log("sanjay");
                    console.log(msg);
                    $('.loader').hide();
                    //if(msg == 'true'){
                    if(msg=='true'){
                        $('#tempate_name_error').html("");
                        if(subsetname_id!=""){
                             update_equipmenttemplate_subset_reading();
                        }else{
                            create_equipmenttemplate_subset_reading();
                        }
                       
                    }else{
                        $('.loader').hide();
                        console.log("already exit");
                        $('#tempate_name_error').html("Template Name already in use.");
                        $('#templateName').focus();
                        return false;
                    }
                }
            });
    } 

    }
    
    function create_equipmenttemplate_subset_reading(){
        var subsetname  = $("#subsetname").val();
        var desig_id = $("#desig_id").val();

        $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/saveequipmenttemplatereadingsubset',
            data: {Reading_Instruction:subsetname,desig_id:desig_id},
            success: function (data) {
                console.log(data);
                console.log("insert Id");
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                        parent.showequipmenttemplatesubsetreading(desig_id,content.InsertId);
                        parent.jQuery.fancybox.close();
                        
                        //parent.view_Add_Reading(desig_id);
                       // parent.view_equipmenttemplate_all_Reading(desig_id);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
    }
    
    function update_equipmenttemplate_subset_reading(){
        var subsetname  = $("#subsetname").val();
        var subsetname_id = $("#subsetname_id").val();
        var desig_id = $("#desig_id").val();
        $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/updateequipmenttemplatereadingsubset',
            data: {reading_instruction: subsetname,subsetname_id:subsetname_id},
            success: function (data) {
                console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                        parent.jQuery.fancybox.close();
                        //parent.view_Add_Reading(desig_id);
                        parent.view_equipmenttemplate_all_Reading(desig_id);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
    }

/*  Group modication sction start */

/* 
 *  Update frequency group modiaction root
 */
function update_equipmenttemplate_readingroot_frequency(id){
    
    Interval_Value ="";
    Frequency_ID = "";
    
    if($("#freq:checked").length===1){
        Frequency_ID = $("#frequency").val();
    }else{
        Interval_Value = $("#freq_num").val();
        Frequency_ID = $("#frequency1").val();
    }
    
    //var Interval_Value = $("#noofday").val();
    //var Frequency_ID = $("#type").val();
    //var frequency = noofday + ' ' + type;
    var includesubset = "";
    if($('#includesubset:checked').length==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingrootfrequeancy',
                data: {desig_id: desig_id,Interval_Value:Interval_Value,Frequency_ID:Frequency_ID,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                        parent.jQuery.fancybox.close();
                        //parent.view_Add_Reading(desig_id);
                        parent.view_equipmenttemplate_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
        });
}

/* 
 *  Update frequency group modiaction Subset
 */

function update_equipmenttemplate_readingsubset_frequency(id){
     Interval_Value ="";
     Frequency_ID = "";
    
    if($("#freq:checked").length===1){
        Frequency_ID = $("#frequency").val();
    }else{
        Interval_Value = $("#freq_num").val();
        Frequency_ID = $("#frequency1").val();
    }  
    var parent_id = $('#parent_id').val();
    
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingfrequeancysubset',
                data: {desig_id: desig_id,Interval_Value:Interval_Value,Frequency_ID:Frequency_ID,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_equipmenttemplate_all_Reading(desig_id);
                           // parent.location.reload();
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/*
 * Start date update on root 
 */  
function update_equipmenttemplate_readingroot_startdate(id){
    var startdate = $("#startdate").val();
    var includesubset = "";
    if($('#includesubset:checked').val()==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    if(startdate==""){
        $("#startdate").focus();
        $("#error_startdate").html("Please Select The Start Date");
        return false;
    } 
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingrootstartdate',
                data: {desig_id: desig_id,startdate:startdate,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_equipmenttemplate_all_Reading(desig_id);

                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/*
 * Start date update on Subset 
 */
function update_equipmenttemplate_readingsubset_startdate(id){
    var startdate = $("#startdate").val();
    var parent_id = $('#parent_id').val();
    if(startdate==""){
        $("#startdate").focus();
        $("#error_startdate").html("Please Select Start Date");
        return false;
    } 
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingsubsetstartdate',
                data: {desig_id: desig_id,startdate:startdate,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_equipmenttemplate_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/*
 * Start date of Month update on root 
 */ 
function update_equipmenttemplate_readingroot_startdateofmonth(id){
    var startdateofmonth = $("#startdateofmonth").val();
    var includesubset = "";
    if($('#includesubset:checked').val()==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    if(startdateofmonth==""){
        $("#startdateofmonth").focus();
        $("#error_startdateofmonth").html("Please Select The Start Date");
        return false;
    } 
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingrootstartdateofmonth',
                data: {desig_id: desig_id,startdateofmonth:startdateofmonth,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_equipmenttemplate_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);
                    }
                }
            });
}

/*
 * Start date of Month update on Subset 
 */ 
function update_equipmenttemplate_readingsubset_startdateofmonth(id){
    var startdateofmonth = $("#startdateofmonth").val();
    var parent_id = $('#parent_id').val();
    if(startdateofmonth==""){
        $("#startdateofmonth").focus();
        $("#error_startdateofmonth").html("Please Select Start Date of Month");
        return false;
    } 
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingsubsetstartdateofmonth',
                data: {desig_id: desig_id,startdateofmonth:startdateofmonth,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_equipmenttemplate_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/*
 * Start date Adjustment update on Root 
 */

function update_equipmenttemplate_readingroot_startdateadjustment(id){
    var startdateadjustment = $("#startdateadjustment").val();
    var includesubset = "";
    if($('#includesubset:checked').val()==1){
       includesubset = $('#includesubset:checked').val(); 
    }

    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingrootstartdateadjustment',
                data: {desig_id: desig_id,startdateadjustment:startdateadjustment,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_equipmenttemplate_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
            });
}

/*
 * Start date Adjustment update on Subset
 */
function update_equipmenttemplate_readingsubset_startdateadjustment(id){
    var startdateadjustment = $("#startdateadjustment").val();
    var parent_id = $('#parent_id').val();
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingsubsetstartdateadjustment',
                data: {desig_id: desig_id,startdateadjustment:startdateadjustment,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_equipmenttemplate_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/*
 *  Reading value update and root
 */

function update_equipmenttemplate_readingroot_readingvalue(id){
    var readingvalue = $("#readingvalue").val();
    var includesubset = "";
    if($('#includesubset:checked').val()==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    if(readingvalue==""){
        $("#error_readingvalue").html("Please Enter Reading Value!");
        $("#readingvalue").focus();
        return false;
    }else{
        $("#error_readingvalue").html("");
    }

    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingrootreadingvalue',
                data: {desig_id: desig_id,reading_value:readingvalue,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_equipmenttemplate_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
            });
}

/*
 * Reading value update and subset
 */
function update_equipmenttemplate_readingsubset_readingvalue(id){
    var readingvalue = $("#readingvalue").val();
    var parent_id = $('#parent_id').val();
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingsubsetreadingvalue',
                data: {desig_id: desig_id,readingvalue:readingvalue,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_equipmenttemplate_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}


/*
 *  Reading Unit Of Measure update and root
 */

function update_equipmenttemplate_readingroot_unitofmeasure(id){
    var unitofmeasure = $("#unitofmeasure").val();
    var includesubset = "";
    if($('#includesubset:checked').val()==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    if(unitofmeasure==""){
        $("#error_unitofmeasure").html("Please Enter Reading Value!");
        $("#unitofmeasure").focus();
        return false;
    }else{
        $("#error_readingvalue").html("");
    }

    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingrootunitofmeasure',
                data: {desig_id: desig_id,unitofmeasure:unitofmeasure,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_equipmenttemplate_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);
                    }
                }
            });
}

/*
 * Reading Unit Of Measure update and subset
 */
function update_equipmenttemplate_readingsubset_unitofmeasure(id){
    var unitofmeasure = $("#unitofmeasure").val();
    var parent_id = $('#parent_id').val();
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingsubsetunitofmeasure',
                data: {desig_id: desig_id,unitofmeasure:unitofmeasure,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_equipmenttemplate_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}



/*
 *  Tolerance update and root
 */

function update_equipmenttemplate_readingroot_tolerance(id){
    var tolerance = $("#tolerance").val();
    var includesubset = "";
    if($('#includesubset:checked').val()==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    if(tolerance==""){
        $("#error_unitofmeasure").html("Please Enter tolerance!");
        $("#tolerance").focus();
        return false;
    }else{
        $("#error_readingvalue").html("");
    }

    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatereadingroottolerance',
                data: {desig_id: desig_id,tolerance:tolerance,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                        $('.loader').hide();
                            parent.jQuery.fancybox.close();
                            //parent.view_Add_Reading(desig_id);
                            parent.view_equipmenttemplate_all_Reading(desig_id);
                    } else {
                        $('#success_message').html(content.msg);
                    }
                }
            });
}

/*
 * Tolerance update and subset
 */
function update_equipmenttemplate_readingsubset_tolerance(id){
    var tolerance = $("#tolerance").val();
    var parent_id = $('#parent_id').val();
    var desig_id = id;
    $.ajax({
             type: "POST",
             datatype: 'json',
             url: baseUrl + 'pm/updateequipmenttemplatereadingsubsettolerance',
             data: {desig_id: desig_id,tolerance:tolerance,parent_id:parent_id},
             success: function (data) {
                 console.log(data);                        
                 var content = $.parseJSON(data);
                 if (content.status == 'success') {
                     $('.success_message').html(content.msg);
                     $('.loader').hide();
                     parent.jQuery.fancybox.close();
                     //parent.view_Add_Reading(desig_id);
                     parent.view_equipmenttemplate_all_Reading(desig_id);
                 } else {
                     $('#success_message').html(content.msg);
                 }
             }
         });
}

/* End group modification section */

function importequipmenttemplatereading(desig_id) {

    $('.loader').show();
    $.ajax({
        url: baseUrl + "pm/importequipmenttemplatereading",
        type: "post",
        data: {desig_id: desig_id},
        success: function (data) {
            $('.loader').hide();
            //$('#edit_wo_form').show();
            $('#file_form').html(data);
            $('#file_form_href').trigger('click');
            $('#file_form').show();
            $('.fade_default_opt').show();
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }

    });
}

/* Import the data */
function importequipmenttemplateApplyReading(id){
    var import_id = $("#designation").val();
    
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/importequipmenttemplateapply',
            data: {desig_id: id,import_id:import_id},
            success: function (response) {
                //alert(response);
                var content = $.parseJSON(response);
                $('.success_message').html(content.msg);
                $('.loader').hide();
                     $('#file_form').hide();
                    $('.fade_default_opt').hide();
                    //parent.jQuery.fancybox.close(); 
                    //window.location = window.location.href;
                    setInterval(function(){	
                         parent.location.reload();                          
                      }, 1500);
                
                parent.view_Add_Reading(id);
                
            }
     });
}


    
/// common function  view all task and add new task

function view_equipmenttemplate_Add_Reading(desig_id){
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/viewequipmenttemplateaddreading/desig_id/'+desig_id,
            data: {task: "empty"},
            success: function (response) {
                //alert(response);
                $(".add-reading").html(response);
                $('.success_message').slideUp(500);
            }
     });
}
function view_equipmenttemplate_all_Reading(desig_id){
    //alert(desig_id);
    //////////////////////
    var list  = "";
    $("input[type='checkbox']:checked").each(function(){
        console.log($(this).val());  
        list += parseInt($(this).val())+","; 
    });
    list =list.substring(0,list.length - 1);
    var all = list.split(',');
    var ctotal = all.length;
    console.log("total "+ctotal);
    cssdata = 93 / (ctotal + 1) ;
    csscolapsdata = 91.25 / (ctotal + 1) ;
    var html = ".dd3-content .id{";
        html += "width : " + cssdata+ "%";
        html +="}";
        html += ".collapse-table .dd3-content .id{";
        html += "width : " + csscolapsdata+ "%";
        html +="}";
    //////////////////////////////
    console.log("Call the View function");
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/viewequipmenttemplatereading/desig_id/'+desig_id,
            data: {viewlist: list},
            success: function (response) {
                //alert(response);
                $('.loader').hide();                
                $("#readingview").html(response);
                $("#readingclass").html('<style>'+html+'</style>');
                $('.success_message').slideUp(500);
            }
     });
}

// Close popup cancel button event //
function closepopup() {
    parent.jQuery.fancybox.close();
}

function view_equipmenttemplate_update(desig_id){
    var  list = "";
    $(".showlistoption[type='checkbox']:checked").each(function(){
        console.log($(this).val());  
        list += parseInt($(this).val())+","; 
    });
    //alert(list);
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/updateviewtask/',
            data: {viewlist: list,type:'Reading'},
            success: function (response) {
                //alert(response);
               // $('.loader').hide();                
                //$("#viewtask").html(response);
                //$('.success_message').slideUp(500);
                //$( "#addcss" ).html( '<style>'+html+'</style>' );
            }
     });
    view_equipmenttemplate_all_Reading(desig_id);
    //return false;
}


/// validate number of decimal place 

var xTriggered = 0;
$( "#reading_val" ).keydown(function( event ) {
    //console.log(event.which);
    var value = event.which;    //parseFloat(obj.value).toFixed(2);
    var data = value.split('.');
    count = data[1].length;
    if ( count > 2 ){
     //event.preventDefault();
     return false;
    }
//  xTriggered++;
//  var msg = "Handler for .keydown() called " + xTriggered + " time(s).";
//  $.print( msg, "html" );
//  $.print( event );
});



function setTwoNumberDecimal(evt){    
    var charCode = (evt.which) ? evt.which : window.event.keyCode;
    if(charCode == 8){
        return true;
    }    
    var value = $("#reading_val").val();//evt.key;  
    var data = value.split('.');
    count = data[1].length;
    if(count > 2) {
        return false;
    }
    //  console.log("count"+data[1].length);
    //  $('#e').val(value);
    //  console.log(value);
    //  console.log(obj.value);
    //  aparseFloat(obj.value).toFixed(2);
}

function setthreeNumberDecimal(evt){
    var charCode = (evt.which) ? evt.which : window.event.keyCode;
    if(charCode == 8){
        return true;
    }    
    var value = $("#reading_val").val();//evt.key;  
    var data = value.split('.');
    count = data[1].length;
    if(count > 2) {
        return false;
    }
}

function check_freq(){
    //console.log();
    if($("#freq:checked").length==1){
        $("#frequency1").attr("disabled",true);
        $("#frequency1").prop('selectedIndex',0);
        $("#freq_num").attr("disabled",true);
        $("#freq_num").attr('value',"");
        $("#frequency").attr("disabled",false);
        
    }else{
        $("#frequency").attr("disabled",true);
        $("#frequency").prop('selectedIndex',0);
        $("#frequency1").attr("disabled",false);
        $("#freq_num").attr("disabled",false);
    }       
}

function jobtimeact(){
    if($("#jobtime1:checked").length==1){
        $("#taskjobtime1").attr("disabled",true);
        $("#custome_hours").attr("disabled",true);
        $("#custome_hours").prop('selectedIndex',0);
        $("#taskjobtime").attr("disabled",false);
        $("#taskjobtime1").prop('selectedIndex',0);
    }else{
        $("#taskjobtime").attr("disabled",true);
        $("#taskjobtime").prop('selectedIndex',0);
        $("#taskjobtime1").attr("disabled",false);
        $("#custome_hours").attr("disabled",false);
    }   
}

function lastday(){
    if($("#domlast:checked").length==1){
        $("#sdom").attr("disabled",true);
    }else{
        $("#sdom").attr("disabled",false);
    }
    
}

function seasonal(){
    if($("#seasonal:checked").length == 1){
        $("#begDate").removeAttr("disabled");
        $("#stopDate").removeAttr("disabled");
    }else{
        $("#begDate").attr('disabled','disabled');
        $("#stopDate").attr('disabled','disabled');
    }
}
function showequipmenttemplatesubsetreading(desig_id,IncId){
    console.log(desig_id);
    //$('.loader').show();
    var i;
    $.ajax({
        url: baseUrl + "pm/getsubsetreading",
        type: "post",
        data: {desig_id: desig_id},
        success: function (data) {
            var html = '<option value="0">Root</option>';
            var content = $.parseJSON(data);
            console.log(content);
            $(content).each(function(i,obj){
                console.log(obj.AU_Template_Reading_ID);
                html +='<option value="'+obj.AU_Template_Reading_ID+'" '+(obj.AU_Template_Reading_ID==IncId?"Selected":"")+'>'+obj.Reading_Instruction+'</option>';
            });
            console.log(html);
            $("#subset_level").html(html);
        },
        error: function () {
            $('.loader').hide();
            alert('There was an error');
        }

    });
}