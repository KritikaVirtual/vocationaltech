
// TEMPATE SECTION 



function addnewtemplete(url)
{
    CheckForSessionpop(baseUrl);
    $('a[href="#CreateNewMultiCon"]').fancybox({
        type: 'iframe',
        href: url,
        width: 770,
        height: 724,
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


function edittemplete(url)
{
    //CheckForSessionpop(baseUrl);
    $('a[href="#CreateNewMultiCon"]').fancybox({
        type: 'iframe',
        href: url,
        width: 770,
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



// Close popup cancel button event //
function canceltemplate() {
    parent.jQuery.fancybox.close();
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

// Create template Validation
function validatetemplate(){
    var template_name = $("#templateName").val().trim();
    var template_id = $("#template_id").val().trim();
    $valid = true;
    if(template_name === ""){
        $("#templateName").focus();
        $("#tempate_name_error").html("Please Enter Template Name");
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
                url: baseUrl + 'pm/validatetemplate',
                data: {TemplateName: template_name,Template_id:template_id},
                success: function (msg) {
                    console.log(msg.trim());
                    console.log("sanjay");
                    $('.loader').hide();
                    if(msg.trim() == 'true'){
                        $('#tempate_name_error').html("");
                        if(template_id!=""){
                            update_template();
                        }else{
                            create_template();
                        }
                    return false;  
                    }else{
                        $('.loader').hide();
                        console.log("already exit 12");
                        $('#tempate_name_error').html("Template Name already in use.");
                        $('#templateName').focus();
                        return false;
                    }
                }
            });
    }
    
}



// crate template function

function create_template(){
    var template_name = $("#templateName").val().trim();
    
    $.ajax({    type: "POST",
            url: baseUrl + 'pm/savetemplate',
            data: {TemplateName: template_name},
            success: function (data) {
                //console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    setInterval(function () {	
                        parent.location.reload();
                    }, 1500);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
    
}

// update template function

function update_template(){
    var template_name = $("#templateName").val().trim();
    var template_id = $("#template_id").val();
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/updatetemplate',
            data: {TemplateName: template_name,Template_id:template_id},
            success: function (data) {
                //console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    setInterval(function () {	
                        parent.location.reload();
                    }, 1500);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
    
}


// delete templale 

function deletetemplate(template_id)
{
    var check_delete = 'YES';
    jPrompt('For Deleting Template, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {
            //alert("sanjay");
            //return false;
            if (check_delete === return_value) {
                $('.loader').show();
                $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'pm/deletetemplate',
                    data: {Template_id: template_id},
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


function tasksection(){
    
}

////  END TEMPLATE SECTION 


// TYPE DESIGNATION START

//// validation Type Designation Pm template 

function validate_type_designation(){
    var template_id = $("#templateName").val();
    var typedesignation = $("#typedesignation").val().trim();
    var typedesination_id = $("#typedesination_id").val();
    var isValid = true;
    
    if(template_id == ""){
        $("#templateName").focus();
        $("#tempate_name_error").html("Please Select Template Name");
        isValid = false;
    }else{
        $("#tempate_name_error").html("");
    }
    
    if(typedesignation == ""){
        $("#typedesignation").focus();
        $("#typedesignation_error").html("Please Enter Type Designation");
        isValid = false;
    }else{
        $("#typedesignation_error").html("");
    }
    
    if(isValid == false){
        return false;
    }else{
        $('.loader').show();
         $.ajax({
                type: "POST",
                url: baseUrl + 'pm/validatetypedesignation',
                data: {templateID: template_id,typedesignation:typedesignation,typedesination_id:typedesination_id},
                success: function (msg) {
                    console.log(msg);
                    $('.loader').hide();
                    if(msg.trim() == 'true'){
                        $('#typedesignation_error').html("");
                        if(typedesination_id == ""){
                             create_typedesignation();
                        }else{
                             update_typedesignation();
                        }
                       
                    }else{
                        $('.loader').hide();
                        $('#typedesignation_error').html("Type Designation Name already in use.");
                        $('#templateName').focus();
                        return false;
                    }
                }
            });
        
    }
}

function create_typedesignation(){
    var template_id     = $("#templateName").val();
    var typedesignation = $("#typedesignation").val();
    var typedescription = $("#typedescription").val();
        $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/savetypedesignation',
            data: {VT_Template_Name_ID: template_id,VT_TypeDesignation:typedesignation,VT_TypeDescritpion:typedescription},
            success: function (data) {
                console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    //parent.jQuery.fancybox.close();
                    //alert(11);
                    parent.window.location  = "createtask/desig_id/"+content.id;
                    setInterval(function(){	
                        //parent.location.reload();
                        //alert(11);
                    }, 1500);
//                    setInterval(function () {	
//                        parent.location.reload();
//                    }, 1500);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
}

function update_typedesignation(){
    var template_id = $("#templateName").val();
    var typedesignation = $("#typedesignation").val();
    var typedesination_id = $("#typedesination_id").val();
    var typedescription = $("#typedescription").val();
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/updatetypedesignation',
            data: {template_id: template_id,typedesignation:typedesignation,typedesination_id:typedesination_id,typedescription:typedescription},
            success: function (data) {
                //console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    setInterval(function () {	
                        parent.location.reload();
                    }, 1500);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
}

function editdesignation(url){
    //CheckForSessionpop(baseUrl);
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


// delete templale 

function deletedesignation(type_id)
{
    var check_delete = 'YES';
    jPrompt('For Deleting Type Designation, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {
            //alert("sanjay");
            //return false;
            if (check_delete === return_value) {
                $('.loader').show();
                $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'pm/deletetypedescription',
                    data: {type_id: type_id},
                    success: function (data) {
                        console.log(data);                        
                        var content = $.parseJSON(data);
                        console.log(content);
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
// TYPE DESIGNATION END


// Equipment Template section 

// Create template Validation
function validateequipmenttemplate(){
    var template_name = $("#templateName").val().trim();
    var template_id = $("#template_id").val().trim();
    $valid = true;
    if(template_name === ""){
        $("#templateName").focus();
        $("#tempate_name_error").html("Please Enter Template Name");
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
                url: baseUrl + 'pm/validateequipmenttemplate',
                data: {TemplateName: template_name,Template_id:template_id},
                success: function (msg) {
                    //console.log(msg);
                    $('.loader').hide();
                    if(msg.trim() == 'true'){
                        $('#tempate_name_error').html("");
                        if(template_id!=""){
                             update_equipment_template();
                        }else{
                            create_equipment_template();
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



// crate template function

function create_equipment_template(){
    var template_name = $("#templateName").val().trim();
    
    $.ajax({    type: "POST",
            url: baseUrl + 'pm/saveequipmenttemplate',
            data: {TemplateName: template_name},
            success: function (data) {
                //console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                console.log(content);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    setInterval(function () {	
                        parent.location.reload();
                    }, 1500);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
    
}

// update template function

function update_equipment_template(){
    var template_name = $("#templateName").val().trim();
    var template_id = $("#template_id").val();
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/updateequipmenttemplate',
            data: {TemplateName: template_name,Template_id:template_id},
            success: function (data) {
                //console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    setInterval(function () {	
                        parent.location.reload();
                    }, 1500);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
    
}


// delete templale 

function deleteequipmenttemplate(template_id)
{
    var check_delete = 'YES';
    jPrompt('For Deleting Template, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {
            //alert("sanjay");
            //return false;
            if (check_delete === return_value) {
                $('.loader').show();
                $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'pm/deleteequipmenttemplate',
                    data: {Template_id: template_id},
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



// TYPE DESIGNATION START

//// validation Type Designation Pm template 

function validateequipmenttemplate_type_designation(){
    var template_id = $("#templateName").val();
    var typedesignation = $("#typedesignation").val().trim();
    var typedesination_id = $("#typedesination_id").val();
    var isValid = true;
    
    if(template_id == ""){
        $("#templateName").focus();
        $("#tempate_name_error").html("Please Select Template Name");
        isValid = false;
    }else{
        $("#tempate_name_error").html("");
    }
    
    if(typedesignation == ""){
        $("#typedesignation").focus();
        $("#typedesignation_error").html("Please Enter Type Designation");
        isValid = false;
    }else{
        $("#typedesignation_error").html("");
    }
    
    if(isValid == false){
        return false;
    }else{
        $('.loader').show();
         $.ajax({
                type: "POST",
                url: baseUrl + 'pm/validateequipmenttemplatetypedesignation',
                data: {templateID: template_id,typedesignation:typedesignation,typedesination_id:typedesination_id},
                success: function (msg) {
                    console.log(msg);
                    $('.loader').hide();
                    if(msg.trim() == 'true'){
                        $('#typedesignation_error').html("");
                        if(typedesination_id == ""){
                             createequipmenttemplate_typedesignation();
                        }else{
                             updateequipmenttemplate_typedesignation();
                        }
                       
                    }else{
                        $('.loader').hide();
                        $('#typedesignation_error').html("Type Designation Name already in use.");
                        $('#templateName').focus();
                        return false;
                    }
                }
            });
        
    }
}

function createequipmenttemplate_typedesignation(){
    var template_id     = $("#templateName").val();
    var typedesignation = $("#typedesignation").val();
    var typedescription = $("#typedescription").val();
        $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/saveequipmenttemplatetypedesignation',
            data: {AU_Template_Name_ID: template_id,AU_TypeDesignation:typedesignation,AU_TypeDescritpion:typedescription},
            success: function (data) {
                console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    //parent.jQuery.fancybox.close();
                    //alert(11);
                    parent.window.location  = "createequipmenttemplatetask/desig_id/"+content.id;
                    setInterval(function(){	
                        //parent.location.reload();
                        //alert(11);
                    }, 1500);
//                    setInterval(function () {	
//                        parent.location.reload();
//                    }, 1500);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
}

function updateequipmenttemplate_typedesignation(){
    var template_id = $("#templateName").val();
    var typedesignation = $("#typedesignation").val();
    var typedesination_id = $("#typedesination_id").val();
    var typedescription = $("#typedescription").val();
    $.ajax({    
            type: "POST",
            url: baseUrl + 'pm/updateequipmenttemplatetypedesignation',
            data: {template_id: template_id,typedesignation:typedesignation,typedesination_id:typedesination_id,typedescription:typedescription},
            success: function (data) {
                //console.log(data);
                $('.loader').hide();
                var content = $.parseJSON(data);
                if (content.status == 'success') {
                    $('.success_message').html(content.msg);
                    setInterval(function () {	
                        parent.location.reload();
                    }, 1500);
                } else {
                    //$('#emailsave').removeAttr('disabled');
                    $('.success_message').html(content.msg);
                    alert('There was an error');
                }
            }
        });
}


// delete templale 

function deleteequipmenttemplatedesignation(type_id)
{
    var check_delete = 'YES';
    jPrompt('For Deleting Type Designation, Enter Yes in Capital letters.', '', 'Vision Work Orders', function (return_value) {
        if (return_value != null) {
            //alert("sanjay");
            //return false;
            if (check_delete === return_value) {
                $('.loader').show();
                $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'pm/deleteequipmenttemplatetypedescription',
                    data: {type_id: type_id},
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

function import_equpment_template(){
    var isvalid = true;
    var design_id = $("#design_id").val();
    var type = $("#temp_type").val();
    var template_name = "";
    var typedesignation = $("#designname").val();
    if(!$("#view-all-temp tr").hasClass("active")){
            $(".view-all-temp-error").html("Please select any template Name !");
            isvalid =  false;
    }else{
        $(".view-all-temp-error").html("");
    }
    
    if($("#temptypedata:checked").val()==3){
        if($("#temptypedatanew").val()==""){
            $(".view-temptypedatanew-error").html("Please Enter Templete Name !");
            isvalid =  false;
            //console.log
        }else{
             template_name = $("#temptypedatanew").val();
             $(".view-temptypedatanew-error").html("");
        }
    }
    
    if($("#temptypedata:checked").val()==1){
        template_name = $("#tempname").val();
    }
  if(isvalid){
        if($("#temptypedata:checked").val()!=2){

                  $.ajax({
                        type: "POST",
                        datatype: 'json',
                        url: baseUrl + 'pm/validateequipmenttemplate',
                        data: {TemplateName:template_name,design_id:design_id},
                        success: function (data) {
                            //console.log(data);
                            if(data.trim()=='true'){
                                    $.ajax({
                                            type: "POST",
                                            datatype: 'json',
                                            url: baseUrl + 'pm/validateequipmenttemplatetypedesignation',
                                            data: {typedesignation:typedesignation},
                                            success: function (data1) { 
                                                console.log(data1);
                                                //return false;
                                                if(data1.trim() === 'true'){
                                                 
                                                    import_equipment_templete_insert(1);
                                                }else{
                                                   
                                                    import_equipment_templete_insert(0);
                                                    
                                                }
                                                
                                            }
                                        });
                                
                                    
                                    //$(".view-temptypedatanew-error").html("");
                            }else{
                                    $(".view-temptypedatanew-error").html("Template Name Already Exist!");
                            }
                            return false;
                            console.log(data);
                 
                        }
                    });
        }else{
            //import_equipment_templete_insert(1);
            $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'pm/validateequipmenttemplatetypedesignation',
                    data: {typedesignation:typedesignation},
                    success: function (data1) { 
                        console.log(data1);
                        if(data1.trim() === 'true'){

                            import_equipment_templete_insert(1);
                        }else{

                            import_equipment_templete_insert(0);

                        }

                    }
                });
        }
    }else{
        return false;
    }
}
        //var viewall = $("#view-all-temp tr").hasClass("active").attr("id");
        //var viewall = $("tr").findClass("active").attr("id");
        //console.log(viewall);
        //var vdata = viewall.split("=");
 function import_equipment_templete_insert(act){
        //console.log(act);
        var imp_design_id = "";
        var design_id = $("#design_id").val();
        var type = $("#temp_type").val();
        var tempoption = $("#temptypedata:checked").val();
        var template_name = $("#temptypedatanew").val();
        if(tempoption==2){
            imp_design_id = $("#assignedto option:selected").val();
        }
        $('.loader').show();
                $.ajax({
                    type: "POST",
                    datatype: 'json',
                    url: baseUrl + 'pm/importtemplatedatainuser',
                    data: {template_name:template_name,type: type,design_id:design_id,tempoption:tempoption,imp_design_id:imp_design_id,action:act},
                    success: function (data) {
                        console.log(data);                        
                        var content = $.parseJSON(data);
                        if (content.status == 'success') {
                            $('.success_message').html(content.msg);
                            $('.loader').hide();
                            setTimeout(function () {                                
                                parent.location.reload();
                            }, 1000);
                        } else {
                            $('.loader').hide();
                            $('#success_message').html(content.msg);
                            setTimeout(function(){
                                $('.loader').hide();
                                parent.location.reload();
                            }, 1000);

                        }
                    }
                });
}



// Close popup cancel button event //
function closepopup() {
    parent.jQuery.fancybox.close();
}
