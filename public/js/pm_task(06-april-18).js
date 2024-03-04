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

//validate sub set 

function validatesubset()
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
                url: baseUrl + 'pm/validatesubset',
                data: {subsetname: subsetname,subsetname_id:subsetname_id,desig_id:desig_id},
                success: function (msg) {
                    //console.log(msg);
                    //return false;
                    $('.loader').hide();
                   // if(msg == 'true'){
                   if(msg != ''){
                        $('#tempate_name_error').html("");
                        if(subsetname_id!=""){
                             update_subset();
                        }else{
                            create_subset();
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

function create_subset(){
    var subsetname  = $("#subsetname").val();
    var desig_id = $("#desig_id").val();
        $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/savesubset',
            data: {Task_Instruction: subsetname,VT_Template_Designation_ID:desig_id},
            success: function (data) {
                console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    showsubset(desig_id,content.InsertId);
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

function update_subset(){
    var subsetname  = $("#subsetname").val();
    var subsetname_id = $("#subsetname_id").val();
     var desig_id = $("#desig_id").val();
        $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/updatesubset',
            data: {Task_Instruction: subsetname,subsetname_id:subsetname_id,VT_Template_Designation_ID: desig_id},
            success: function (data) {
                console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                        parent.jQuery.fancybox.close();
                        //parent.view_Add_Task(temp_id);
                        parent.view_all_Task(desig_id);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
}

    /// frequency change Action 
    
//    function changefrequency(obj){
//        var freq = $('#frequency').val();
//        //console.log(freq);
//        if(parseInt(freq) == 0 ){
//            $("#freqcustome").show();
//        }else{
//            $("#freqcustome").hide();
//            console.log("data will send ");
//            document.getElementById("custome_id").value = "";
//            document.getElementById("custome_freq").value = "";
//            document.getElementById("custome_numberofday").value = "";
//        }
//        
//    }
    

function validationtask(obj){
    
    var typeaction = obj;
    var frequencydata = "";    
    var subset_level  = $("#subset_level").val();
    //console.log(subset_level);
    //return false;
    var task_instruction  = $("#task_instruction").val(); 
      
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
        begDate = "";//$("#begDate").val("");
        stopDate = "";//$("#stopDate").val("");
    }
    var $valid = true;
    
    if($("#domlast:checked").length === 1){
        var startdateofmonth  = $("#domlast").val();
    }else{
        var startdateofmonth  = $("#sdom").val();
    }
    
    //return false;
    var startdateadjustment  = $("#startdateadjustment").val();    
    
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
    //console.log(taskjobtime);
    //return false;
    
    var overtime  = $("#overtime").val();
    var assignedto  = $("#assignedto").val();
    
    var action  = $("#action").val();
    var task_id = $("#task_id").val();
    var desig_id = $('#desig_id').val();
    
    if(task_instruction == ""){
        $("#task_instruction").focus();
        $(".task_inst_error").html("Please Enter Task Instruction ");
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
    
    if(parseInt(frequencydata)==0){
        $("#frequency").focus();
        $(".task_freq_error").html("Please Enter Frequency ");
        $valid =  false;
    }else{
        $(".task_freq_error").html("");
    } 
    if(seasonal == 'Y'){
        if(begDate ==""){
            $("#endDate").focus();
            $(".endDate_error").html("Please Enter Begin Date ");
            $valid = false;
        }else{
            $(".startDate_error").html("");
        }
        
        if(stopDate ==""){
            $("#endDate").focus();
            $(".endDate_error").html("Please Enter Stop Date ");
            $valid = false;
        }else{
            $(".startDate_error").html("");
        }
    }
    
    if($valid === false){
        return true;
    }else{
        if(action=='update'){
             $.ajax({    
                    type: "POST",
                    url: baseUrl + 'pm/updatetask',
                    data: {
                        task_id :task_id,
                        Parent_ID: subset_level,
                        Task_Instruction:task_instruction,
                        AU_Frequency_ID:Frequency_ID,
                        Interval_Value :Interval_Value,
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
                        //View_order:0
                    },
                    success: function (data) {
                        $('.loader').show();                       
                        var content = $.parseJSON(data);
                        if (content.status == 'success') {
                            $('.success_message').html(content.msg);
                             $('.loader').hide();
                             if(typeaction === 'new'){
                                 parent.$("#task_instruction").removeAttr('value');
                             }else{
                                  parent.jQuery.fancybox.close();
                                  parent.view_all_Task(desig_id);
                             }
                            
                             //console.log("close");
                             //view_Add_Task(desig_id);
                             
                        } else {
                            $('.success_message').html(content.msg);
                            alert('There was an error');
                        }
                    }
                });
        }else{
            $('.loader').show();
            $.ajax({    
                type: "POST",
                url: baseUrl + 'pm/savetask',
                data: {
                    Parent_ID: subset_level,
                    Task_Instruction:task_instruction,
                    AU_Frequency_ID:Frequency_ID,
                    Interval_Value :Interval_Value,
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
                    VT_Template_Designation_ID:desig_id,
                    View_order:0
                },
                success: function (data) {
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);                        
                        //$('#task_instruction').removeAttr('value');
                        if(typeaction === 'new'){
                                $('.loader').hide();
                                $("#task_instruction").removeAttr('value');
                                parent.view_all_Task(desig_id);
                        }else{
                             parent.jQuery.fancybox.close();
                             parent.view_all_Task(desig_id);
                        }
                            //parent.jQuery.fancybox.close();
                            //parent.view_all_Task(desig_id);
                    } else {
                        $('.success_message').html(content.msg);
                        alert('There was an error');
                    }
                }
            }); 
        }
    }
}




function addcustomefrequency(url)
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
        },
        onClosed:function() {
            alert('Closed!');
        }
    }); 
}

function clonecustomefrq(){
    var numberofday = $("#numberofday").val();
    var nameofday = $("#nameofday").val();
    var html = "";
    var numbers = /^[0-9]+$/;  
    if(!numberofday.match(numbers)){
          $("#numberofday").focus();
          $("#error_noofday").html("Please Enter numeric value only");
          return false;
      }
    if(parseInt(numberofday) < 0){
        $("#numberofday").focus();
          $("#error_noofday").html("Please Do not enter nagitave Value");
          return false;
    } 
    if(numberofday==""){
        $("#numberofday").focus();
        $("#error_noofday").html("Please Enter Number of days");
        return false;
    }  
    
    //console.log(parent.$("div").hasClass("custome_freq_section"));
    //id = parent.
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/insertfrequency',
            data: {name: nameofday,value:numberofday,type:"custome"},
            success: function (data) {
                var content = $.parseJSON(data);
                html += '<input type="hidden" id="custome_id" name="custome_id" value="'+content.id+'" >';
                parent.$(".custome_freq_section").html("");
                $(html).clone().appendTo(parent.$(".custome_freq_section"));
                parent.jQuery.fancybox.close();
            }
        });
    
}


// clone for devices

function clonecustometimejob(){
    var hours = $("#hours").val();
    var minutes = $("#minutes").val();
    var html = "";
    var numbers = /^[0-9]+$/;  
    if(!hours.match(numbers)){
          $("#numberofday").focus();
          $("#error_noofday").html("Please Enter numeric value only");
          return false;
      }
    if(parseInt(hours) < 0){
        $("#numberofday").focus();
          $("#error_noofday").html("Please Do not enter nagitave Value");
          return false;
    } 
    if(hours==""){
        $("#numberofday").focus();
        $("#error_noofday").html("Please Enter Number of days");
        return false;
    }  
    html += '<input type="hidden" id="custome_hours" name="custome_hours" value="'+hours+'" >';
    html += '<input type="hidden" id="custome_minutes" name="custome_minutes" value="'+minutes+'" >';
    //console.log(parent.$("div").hasClass("custome_freq_section"));
    //id = parent.
    parent.$(".custome_timejob_section").html("");
    $(html).clone().appendTo(parent.$(".custome_timejob_section"));
    parent.$('select option[value=""]').attr("selected",true);
    parent.jQuery.fancybox.close();
}

//  chnange job time  //
function taskjobcheck(){
    var freq = $('#taskjobtime').val();
        //console.log(freq);
        if(parseInt(freq) == 0 ){
            $("#custometjob").show();
        }else{
            console.log("data will send ");
            document.getElementById("custome_hours").value = "";
            document.getElementById("custome_minutes").value = "";
            $("#custometjob").hide();
        }
    
    
}


// Edit task section 

function edittask(id,temp_id){
    var task_id = id;
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/edittask',
            data: {task_id: task_id,temp_id:temp_id},
            success: function (data) {
                $(".add-task").html(data);               
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    parent.jQuery.fancybox.close();
                } else {
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
}


// delete Task section 

function deletetask(task_id,temp_id)
{
    $('.loader').show();
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/deletetask',
        data: {Task_id: task_id},
        success: function (data) {
            console.log(data);                        
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                $('.success_message').html(content.msg);
                //view all task listing 
                 view_all_Task(temp_id);
            } else {
                $('#success_message').html(content.msg);
            }
        }
    });
}


/// Task root frequency 
function update_taskroot_frequency(id){
    Interval_Value ="";
    Frequency_ID = "";
    
    if($("#freq:checked").length===1){
        Frequency_ID = $("#frequency").val();
    }else{
        Interval_Value = $("#freq_num").val();
        Frequency_ID = $("#frequency1").val();
    }
    //var frequency = noofday + ' ' + type;
    var includesubset = "";
    if($('#includesubset:checked').length==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    
    var desig_id = id;
    $('.loader').show();
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatetaskrootfrequeancy',
                data: {desig_id: desig_id,Interval_Value:Interval_Value,Frequency_ID:Frequency_ID,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.loader').hide();
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.view_all_Task(desig_id);
                    } else {
                        $('.loader').hide();
                        $('#success_message').html(content.msg);

                    }
                }
        });
}

/// task subset frequency
function update_tasksubset_frequency(id){
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
                url: baseUrl + 'pm/updatetaskfrequeancysubset',
                data: {desig_id: desig_id,Interval_Value:Interval_Value,Frequency_ID:Frequency_ID,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.view_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/// Task root Startdate 
function update_taskroot_startdate(id){
    var startdate = $("#startdate").val();
    var includesubset = "";
    if($('#includesubset:checked').length==1){
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
                url: baseUrl + 'pm/updatetaskrootstartdate',
                data: {desig_id: desig_id,startdate:startdate,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
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
function update_tasksubset_startdate(id){
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
                url: baseUrl + 'pm/updatetasksubsetstartdate',
                data: {desig_id: desig_id,startdate:startdate,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.view_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}


// Task group modification on root start date of month
function update_taskroot_startdateofmonth(id){
    var startdateofmonth = $("#startdateofmonth").val();
    var includesubset = "";
    if($('#includesubset:checked').length==1){
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
                url: baseUrl + 'pm/updatetaskrootstartdateofmonth',
                data: {desig_id: desig_id,startdateofmonth:startdateofmonth,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.view_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);
                    }
                }
            });
}


/* Task group modification on Subset start date of month */
function update_tasksubset_startdateofmonth(id){
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
                url: baseUrl + 'pm/updatetasksubsetstartdateofmonth',
                data: {desig_id: desig_id,startdateofmonth:startdateofmonth,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.view_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}


// Task group modification on root start date of month
function update_taskroot_startdateadjustment(id){
    var startdateadjustment = $("#startdateadjustment").val();
    var $includesubset = "";
    if($('#includesubset:checked').val()==1){
       $includesubset = $('#includesubset:checked').val(); 
    }

    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatetaskrootstartdateadjustment',
                data: {desig_id: desig_id,startdateadjustment:startdateadjustment,includesubset:$includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.view_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
            });
}

/* Task group modification on Subset start date of month */
function update_tasksubset_startdateadjustment(id){
    var startdateadjustment = $("#startdateadjustment").val();
    var parent_id = $('#parent_id').val();
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updatetasksubsetstartdateadjustment',
                data: {desig_id: desig_id,startdateadjustment:startdateadjustment,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.view_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}



function importtask(desig_id) {

    $('.loader').show();
    $.ajax({
        url: baseUrl + "pm/import",
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
function importApply(id){
    var import_id = $("#designation").val();
    $('.loader').show();
    desig_id = id;
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/importapply',
            data: {desig_id: desig_id,import_id:import_id},
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
                    
                    parent.view_all_Task(desig_id);
                    
            }
     });
}


/// common function  view all task and add new task

function view_Add_Task(temp_id){
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/viewaddtask',
            data: {temp_id: temp_id},
            success: function (response) {
                //alert(response);
                $(".add-task").html(response);
                $('.success_message').slideUp(500);
            }
     });
}
function view_all_Task(desig_id){
    //alert('sdaf,smdgf');
    //////////////////////
    var list  = "";
    
    $(".showlistoption[type='checkbox']:checked").each(function(){
        console.log($(this).val());  
        list += parseInt($(this).val())+","; 
    });
    
    list =list.substring(0,list.length - 1);
    var all = list.split(',');
    console.log("total "+list);
    var ctotal = all.length;
    console.log("total "+ctotal);
    cssdata = 94 / (ctotal + 1) ;
    csscolapsdata = 92.25 / (ctotal + 1) ;
    var html = ".dd3-content .id{";
        html += "width : " + cssdata+ "%";
        html +="}";
        html += ".collapse-table .dd3-content .id{";
        html += "width : " + csscolapsdata+ "%";
        html +="}";
        console.log("new csss generate");  
    
    //////////////////////////////
    
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/viewtask/desig_id/'+desig_id,
            data: {viewlist: list,desig_id:desig_id},
            success: function (response) {
                //alert(response);
                $('.loader').hide();                
                $("#viewtask").html(response);
                //$('.success_message').slideUp(500);
                $( "#addcss" ).html( '<style>'+html+'</style>' );
            }
     });
     
}

//Seasonal task

function actionendtime(obj){
    console.log(obj.checked);
    console.log($('#seasonaltask::checked').length);
    if(obj.checked){
        $('#enddateview').show();
    }else{
        $('#enddateview').hide();
    }
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
            data: {viewlist: list,type:'task'},
            success: function (response) {
                //alert(response);
               // $('.loader').hide();                
                //$("#viewtask").html(response);
                //$('.success_message').slideUp(500);
                //$( "#addcss" ).html( '<style>'+html+'</style>' );
            }
     });
    
    view_all_Task(desig_id);
    //return false;
}

function canceltemplate(){
    parent.jQuery.fancybox.close();
}

function cancelsubsetpopup() {
    $("div.fancybox-close").trigger("click");
    $('#file_form').html('');
    $('#file_form').hide();
    $('.fade_default_opt').hide();
}

function addTaskSubset(desig_id) {

    $('.loader').show();
    $.ajax({
        url: baseUrl + "pm/createsubset",
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

function lastday(){
    if($("#domlast:checked").length==1){
        $("#sdom").attr("disabled",true);
    }else{
        $("#sdom").attr("disabled",false);
    }
    
}

function jobtimeact(){
    if($("#jobtime1:checked").length==1){
        $("#taskjobtime1").attr("disabled",true);
        $("#taskjobtime1").prop('selectedIndex',0);
        $("#custome_hours").attr("disabled",true);
        $("#custome_hours").prop('selectedIndex',0);
        $("#taskjobtime").attr("disabled",false);
        
    }else{
        $("#taskjobtime").attr("disabled",true);
        $("#taskjobtime").prop('selectedIndex',0);
        $("#taskjobtime1").attr("disabled",false);
        $("#custome_hours").attr("disabled",false);
    }   
}





function seasonal(){
    if($("#seasonal:checked").length == 1){
        //startdate();
        $("#begDate").removeAttr("disabled");
        $("#stopDate").removeAttr("disabled");
    }else{
        $("#begDate").attr('disabled','disabled');
        $("#stopDate").attr('disabled','disabled');
    }
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
//});


function showsubset(desig_id,IncId){
    console.log(desig_id);
    //$('.loader').show();
    var i;
    $.ajax({
        url: baseUrl + "pm/getsubset",
        type: "post",
        data: {desig_id: desig_id},
        success: function (data) {
            var html = '<option value="0">Root</option>';
            var content = $.parseJSON(data);
            console.log(content);
            $(content).each(function(i,obj){
                console.log(obj);
                html +='<option value="'+obj.VT_Template_Task_ID+'" '+(obj.VT_Template_Task_ID==IncId?"Selected":"")+'>'+obj.Task_Instruction+'</option>';
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


function showequipmenttemplatesubset(desig_id,IncId){
    console.log("test"+IncId);
    
    //$('.loader').show();
    var i;
    $.ajax({
        url: baseUrl + "pm/getequipmenttemplatesubset",
        type: "post",
        data: {desig_id: desig_id},
        success: function (data) {
            var html = '<option value="0">Root</option>';
            var content = $.parseJSON(data);
            console.log(content);
            $(content).each(function(i,obj){
                console.log(obj);
                html +='<option value="'+obj.AU_Template_Task_ID+'" '+(obj.AU_Template_Task_ID==IncId?"Selected":"")+'>'+obj.Task_Instruction+'</option>';
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
// popup Section Active

function ActiveSction(SecName){
    var desig_id = $("#desig_id").val();
   if(SecName==='Task'){
       
        $('.task-section').show();
        $('.reading-section').hide();
       
       
   }else{
       $('.task-section').hide();
        $('.reading-section').show();
       
   } 
}


function number_validation(event, value){
    if(event.which < 45 || event.which > 58 || event.which == 47 ) {
          return false;
            event.preventDefault();
        } // prevent if not number/dot

        if(event.which == 46 && value.indexOf('.') != -1) {
            return false;
           // event.preventDefault();
        } // prevent if already dot
        //if()
        if(parseInt(value) > 59){
            return false;
        }

    return true;

}



/* ********************* Equipment Section ********************************************** */
//validate sub set 

function validateequipmenttemplatesubset()
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
                url: baseUrl + 'pm/validateequipmenttemplatesubset',
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
            url: baseUrl + 'pm/saveequipmenttemplatesubset',
            data: {Task_Instruction: subsetname,AU_Template_Designation_ID:desig_id},
            success: function (data) {
                console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    showequipmenttemplatesubset(desig_id,content.InsertId);
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

function updateequipmenttemplate_subset(){
    var subsetname  = $("#subsetname").val();
    var subsetname_id = $("#subsetname_id").val();
     var desig_id = $("#desig_id").val();
        $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/updateequipmenttemplatesubset',
            data: {Task_Instruction: subsetname,subsetname_id:subsetname_id,AU_Template_Designation_ID: desig_id},
            success: function (data) {
                console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                        parent.jQuery.fancybox.close();
                        //parent.view_Add_Task(temp_id);
                        parent.viewequipmenttemplate_all_Task(desig_id);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
}

    /// frequency change Action 
    
//    function changefrequency(obj){
//        var freq = $('#frequency').val();
//        //console.log(freq);
//        if(parseInt(freq) == 0 ){
//            $("#freqcustome").show();
//        }else{
//            $("#freqcustome").hide();
//            console.log("data will send ");
//            document.getElementById("custome_id").value = "";
//            document.getElementById("custome_freq").value = "";
//            document.getElementById("custome_numberofday").value = "";
//        }
//        
//    }
    

function validationequipmenttemplatetask(obj){
    
    var typeaction = obj;
    var frequencydata = "";    
    var subset_level  = $("#subset_level").val();
    //console.log(subset_level);
    //return false;
    var task_instruction  = $("#task_instruction").val(); 
      
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
        begDate = "";//$("#begDate").val("");
        stopDate = "";//$("#stopDate").val("");
    }
    var $valid = true;
    
    if($("#domlast:checked").length === 1){
        var startdateofmonth  = $("#domlast").val();
    }else{
        var startdateofmonth  = $("#sdom").val();
    }
    
    //return false;
    var startdateadjustment  = $("#startdateadjustment").val();    
    
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
    //console.log(taskjobtime);
    //return false;
    
    var overtime  = $("#overtime").val();
    var assignedto  = $("#assignedto").val();
    
    var action  = $("#action").val();
    var task_id = $("#task_id").val();
    var desig_id = $('#desig_id').val();
    
    if(task_instruction == ""){
        $("#task_instruction").focus();
        $(".task_inst_error").html("Please Enter Task Instruction ");
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
    
    if(parseInt(frequencydata)==0){
        $("#frequency").focus();
        $(".task_freq_error").html("Please Enter Frequency ");
        $valid =  false;
    }else{
        $(".task_freq_error").html("");
    } 
    if(seasonal == 'Y'){
        if(begDate ==""){
            $("#endDate").focus();
            $(".endDate_error").html("Please Enter Begin Date ");
            $valid = false;
        }else{
            $(".startDate_error").html("");
        }
        
        if(stopDate ==""){
            $("#endDate").focus();
            $(".endDate_error").html("Please Enter Stop Date ");
            $valid = false;
        }else{
            $(".startDate_error").html("");
        }
    }
    
    if($valid === false){
        return true;
    }else{
        if(action=='update'){
             $.ajax({    
                    type: "POST",
                    url: baseUrl + 'pm/updateequipmenttemplatetask',
                    data: {
                        task_id :task_id,
                        Parent_ID: subset_level,
                        Task_Instruction:task_instruction,
                        AU_Frequency_ID:Frequency_ID,
                        Interval_Value :Interval_Value,
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
                        //View_order:0
                    },
                    success: function (data) {
                        $('.loader').show();                       
                        var content = $.parseJSON(data);
                        if (content.status == 'success') {
                            $('.success_message').html(content.msg);
                             $('.loader').hide();
                             if(typeaction === 'new'){
                                 parent.$("#task_instruction").removeAttr('value');
                             }else{
                                  parent.jQuery.fancybox.close();
                                  parent.viewequipmenttemplate_all_Task(desig_id);
                             }
                            
                             //console.log("close");
                             //view_Add_Task(desig_id);
                             
                        } else {
                            $('.success_message').html(content.msg);
                            alert('There was an error');
                        }
                    }
                });
        }else{
            $('.loader').show();
            $.ajax({    
                type: "POST",
                url: baseUrl + 'pm/saveequipmenttemplatetask',
                data: {
                    Parent_ID: subset_level,
                    Task_Instruction:task_instruction,
                    AU_Frequency_ID:Frequency_ID,
                    Interval_Value :Interval_Value,
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
                    AU_Template_Designation_ID:desig_id,
                    View_order:0
                },
                success: function (data) {
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);                        
                        //$('#task_instruction').removeAttr('value');
                        if(typeaction === 'new'){
                                $('.loader').hide();
                                $("#task_instruction").removeAttr('value');
                                parent.viewequipmenttemplate_all_Task(desig_id);
                        }else{
                             parent.jQuery.fancybox.close();
                             parent.viewequipmenttemplate_all_Task(desig_id);
                        }
                            //parent.jQuery.fancybox.close();
                            //parent.view_all_Task(desig_id);
                    } else {
                        $('.success_message').html(content.msg);
                        alert('There was an error');
                    }
                }
            }); 
        }
    }
}




function addcustomefrequency(url)
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
        },
        onClosed:function() {
            alert('Closed!');
        }
    }); 
}

function clonecustomefrq(){
    var numberofday = $("#numberofday").val();
    var nameofday = $("#nameofday").val();
    var html = "";
    var numbers = /^[0-9]+$/;  
    if(!numberofday.match(numbers)){
          $("#numberofday").focus();
          $("#error_noofday").html("Please Enter numeric value only");
          return false;
      }
    if(parseInt(numberofday) < 0){
        $("#numberofday").focus();
          $("#error_noofday").html("Please Do not enter nagitave Value");
          return false;
    } 
    if(numberofday==""){
        $("#numberofday").focus();
        $("#error_noofday").html("Please Enter Number of days");
        return false;
    }  
    
    //console.log(parent.$("div").hasClass("custome_freq_section"));
    //id = parent.
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/insertfrequency',
            data: {name: nameofday,value:numberofday,type:"custome"},
            success: function (data) {
                var content = $.parseJSON(data);
                html += '<input type="hidden" id="custome_id" name="custome_id" value="'+content.id+'" >';
                parent.$(".custome_freq_section").html("");
                $(html).clone().appendTo(parent.$(".custome_freq_section"));
                parent.jQuery.fancybox.close();
            }
        });
    
}


// clone for devices

function clonecustometimejob(){
    var hours = $("#hours").val();
    var minutes = $("#minutes").val();
    var html = "";
    var numbers = /^[0-9]+$/;  
    if(!hours.match(numbers)){
          $("#numberofday").focus();
          $("#error_noofday").html("Please Enter numeric value only");
          return false;
      }
    if(parseInt(hours) < 0){
        $("#numberofday").focus();
          $("#error_noofday").html("Please Do not enter nagitave Value");
          return false;
    } 
    if(hours==""){
        $("#numberofday").focus();
        $("#error_noofday").html("Please Enter Number of days");
        return false;
    }  
    html += '<input type="hidden" id="custome_hours" name="custome_hours" value="'+hours+'" >';
    html += '<input type="hidden" id="custome_minutes" name="custome_minutes" value="'+minutes+'" >';
    //console.log(parent.$("div").hasClass("custome_freq_section"));
    //id = parent.
    parent.$(".custome_timejob_section").html("");
    $(html).clone().appendTo(parent.$(".custome_timejob_section"));
    parent.$('select option[value=""]').attr("selected",true);
    parent.jQuery.fancybox.close();
}

//  chnange job time  //
function taskequipmenttemplatejobcheck(){
    var freq = $('#taskjobtime').val();
        //console.log(freq);
        if(parseInt(freq) == 0 ){
            $("#custometjob").show();
        }else{
            console.log("data will send ");
            document.getElementById("custome_hours").value = "";
            document.getElementById("custome_minutes").value = "";
            $("#custometjob").hide();
        }
    
    
}


// Edit task section 

function editequipmenttemplatetask(id,temp_id){
    var task_id = id;
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/editequipmenttemplatetask',
            data: {task_id: task_id,temp_id:temp_id},
            success: function (data) {
                $(".add-task").html(data);               
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    parent.jQuery.fancybox.close();
                } else {
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
}


// delete Task section 

function deleteequipmenttemplatetask(task_id,desig_id)
{
    $('.loader').show();
    $.ajax({
        type: "POST",
        datatype: 'json',
        url: baseUrl + 'pm/deleteequipmenttemplatetask',
        data: {Task_id: task_id},
        success: function (data) {
            console.log(data);                        
            var content = $.parseJSON(data);
            if (content.status == 'success') {
                $('.success_message').html(content.msg);
                //view all task listing 
                 viewequipmenttemplate_all_Task(desig_id);
            } else {
                $('#success_message').html(content.msg);
            }
        }
    });
}


/// Task root frequency 
function update_equipmenttemplate_taskroot_frequency(id){
    Interval_Value ="";
    Frequency_ID = "";
    
    if($("#freq:checked").length===1){
        Frequency_ID = $("#frequency").val();
    }else{
        Interval_Value = $("#freq_num").val();
        Frequency_ID = $("#frequency1").val();
    }
    //var frequency = noofday + ' ' + type;
    var includesubset = "";
    if($('#includesubset:checked').length==1){
       includesubset = $('#includesubset:checked').val(); 
    }
    
    var desig_id = id;
    $('.loader').show();
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatetaskrootfrequeancy',
                data: {desig_id: desig_id,Interval_Value:Interval_Value,Frequency_ID:Frequency_ID,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.loader').hide();
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.viewequipmenttemplate_all_Task(desig_id);
                    } else {
                        $('.loader').hide();
                        $('#success_message').html(content.msg);

                    }
                }
        });
}

/// task subset frequency
function update_equipmenttemplate_tasksubset_frequency(id){
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
                url: baseUrl + 'pm/updateequipmenttemplatetaskfrequeancysubset',
                data: {desig_id: desig_id,Interval_Value:Interval_Value,Frequency_ID:Frequency_ID,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.viewequipmenttemplate_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/// Task root Startdate 
function update_equipmenttemplate_taskroot_startdate(id){
    var startdate = $("#startdate").val();
    var includesubset = "";
    if($('#includesubset:checked').length==1){
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
                url: baseUrl + 'pm/updateequipmenttemplatetaskrootstartdate',
                data: {desig_id: desig_id,startdate:startdate,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.viewequipmenttemplate_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}

/// task subset Startdate
function update_equipmenttemplate_tasksubset_startdate(id){
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
                url: baseUrl + 'pm/updateequipmenttemplatetasksubsetstartdate',
                data: {desig_id: desig_id,startdate:startdate,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.viewequipmenttemplate_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}


// Task group modification on root start date of month
function update_equipmenttemplate_taskroot_startdateofmonth(id){
    var startdateofmonth = $("#startdateofmonth").val();
    var includesubset = "";
    if($('#includesubset:checked').length==1){
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
                url: baseUrl + 'pm/updateequipmenttemplatetaskrootstartdateofmonth',
                data: {desig_id: desig_id,startdateofmonth:startdateofmonth,includesubset:includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.viewequipmenttemplate_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);
                    }
                }
            });
}


/* Task group modification on Subset start date of month */
function update_equipmenttemplate_tasksubset_startdateofmonth(id){
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
                url: baseUrl + 'pm/updateequipmenttemplatetasksubsetstartdateofmonth',
                data: {desig_id: desig_id,startdateofmonth:startdateofmonth,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.viewequipmenttemplate_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}


// Task group modification on root start date of month
function update_equipmenttemplate_taskroot_startdateadjustment(id){
    var startdateadjustment = $("#startdateadjustment").val();
    var $includesubset = "";
    if($('#includesubset:checked').val()==1){
       $includesubset = $('#includesubset:checked').val(); 
    }

    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatetaskrootstartdateadjustment',
                data: {desig_id: desig_id,startdateadjustment:startdateadjustment,includesubset:$includesubset},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.viewequipmenttemplate_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
            });
}

/* Task group modification on Subset start date of month */
function update_equipmenttemplate_tasksubset_startdateadjustment(id){
    var startdateadjustment = $("#startdateadjustment").val();
    var parent_id = $('#parent_id').val();
    var desig_id = id;
       $.ajax({
                type: "POST",
                datatype: 'json',
                url: baseUrl + 'pm/updateequipmenttemplatetasksubsetstartdateadjustment',
                data: {desig_id: desig_id,startdateadjustment:startdateadjustment,parent_id:parent_id},
                success: function (data) {
                    console.log(data);                        
                    var content = $.parseJSON(data);
                    if (content.status == 'success') {
                        $('.success_message').html(content.msg);
                            parent.jQuery.fancybox.close();
                            parent.viewequipmenttemplate_all_Task(desig_id);
                    } else {
                        $('#success_message').html(content.msg);

                    }
                }
                });
}



function importequipmenttemplatetask(desig_id) {

    $('.loader').show();
    $.ajax({
        url: baseUrl + "pm/importequipmenttemplate",
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
function importequipmenttemplateApply(id){
    var import_id = $("#designation").val();
    $('.loader').show();
    desig_id = id;
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/importequipmenttemplateapply',
            data: {desig_id: desig_id,import_id:import_id},
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
                    
                    parent.viewequipmenttemplate_all_Task(desig_id);
                    
            }
     });
}


/// common function  view all task and add new task

function viewequipmenttemplate_Add_Task(temp_id){
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/viewequipmenttemplateaddtask',
            data: {temp_id: temp_id},
            success: function (response) {
                //alert(response);
                $(".add-task").html(response);
                $('.success_message').slideUp(500);
            }
     });
}
function viewequipmenttemplate_all_Task(desig_id){
    //alert('sdaf,smdgf');
    //////////////////////
    var list  = "";
    
    $(".showlistoption[type='checkbox']:checked").each(function(){
        console.log($(this).val());  
        list += parseInt($(this).val())+","; 
    });
    
    list =list.substring(0,list.length - 1);
    var all = list.split(',');
    console.log("total "+list);
    var ctotal = all.length;
    console.log("total "+ctotal);
    cssdata = 94 / (ctotal + 1) ;
    csscolapsdata = 92.25 / (ctotal + 1) ;
    var html = ".dd3-content .id{";
        html += "width : " + cssdata+ "%";
        html +="}";
        html += ".collapse-table .dd3-content .id{";
        html += "width : " + csscolapsdata+ "%";
        html +="}";
        console.log("new csss generate");  
    
    //////////////////////////////
    
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/viewequipmenttemplatetask/desig_id/'+desig_id,
            data: {viewlist: list,desig_id:desig_id},
            success: function (response) {
                //alert(response);
                $('.loader').hide();                
                $("#viewtask").html(response);
                //$('.success_message').slideUp(500);
                $( "#addcss" ).html( '<style>'+html+'</style>' );
            }
     });
     
}

//Seasonal task

function actionendtime(obj){
    console.log(obj.checked);
    console.log($('#seasonaltask::checked').length);
    if(obj.checked){
        $('#enddateview').show();
    }else{
        $('#enddateview').hide();
    }
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
            url: baseUrl + 'pm/updateequipmenttemplateviewtask/',
            data: {viewlist: list,type:'task'},
            success: function (response) {
                //alert(response);
               // $('.loader').hide();                
                //$("#viewtask").html(response);
                //$('.success_message').slideUp(500);
                //$( "#addcss" ).html( '<style>'+html+'</style>' );
            }
     });
    
    viewequipmenttemplate_all_Task(desig_id);
    //return false;
}

function canceltemplate(){
    parent.jQuery.fancybox.close();
}

function cancelequipmenttemplatesubsetpopup() {
    $("div.fancybox-close").trigger("click");
    $('#file_form').html('');
    $('#file_form').hide();
    $('.fade_default_opt').hide();
}

function addequipmenttemplateTaskSubset(desig_id) {

    $('.loader').show();
    $.ajax({
        url: baseUrl + "pm/createequipmenttemplatesubset",
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



function AllEquipmentTemplate(action,build_id){
    console.log(desig_id);
    //$('.loader').show();
    var Action = action;
    var build_id = build_id;
    var i;
    $.ajax({
        url: baseUrl + "pm/getequipmenttemplatesubset",
        type: "post",
        data: {desig_id: desig_id},
        success: function (data) {
            var html = '<option value="0">Root</option>';
            var content = $.parseJSON(data);
            console.log(content);
            $(content).each(function(i,obj){
                console.log(obj);
                html +='<option value="'+obj.VT_Template_Task_ID+'" '+(obj.VT_Template_Task_ID==IncId?"Selected":"")+'>'+obj.Task_Instruction+'</option>';
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

