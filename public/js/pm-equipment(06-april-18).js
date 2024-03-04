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
function validatequipment(){
    
    var  equipmentnamedata = $('#browsers option[value="' + $('#equipmentname').val() + '"]').val();
    if(equipmentnamedata!="undefined"){
       equipmentnamedata = $('#equipmentname').val();
    }
    console.log(equipmentnamedata);
    var  attach_template_name = $("#attach_template option:selected").text();
    
    equipmentname = $('#browsers option[value="' + $('#equipmentname').val() + '"]').attr('id');
    if(equipmentname!="undefined"){
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
    
    var userData = new FormData();
    var manual = $("#equipmentmenual").prop('files')[0];
    var flag_val = escape($("#equipmentmenual").attr('name'));
    userData.append('file[' + flag_val + ']', manual);
    
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
    
    $valid = true;
    if($valid === false){
        return false;
    }else{
        
        $("#first").hide();
        $("#second").show();
        $("#viewEquipment").html(equipmentnamedata);
        $("#viewAttach").html(attach_template_name);
        $("#viewStatus").html(status);
        $("#viewUnit").html(unit);
        $("#viewFlor").html(floor);
    }
    
}

// Close popup cancel button event //
function closepopup() {
    parent.jQuery.fancybox.close();
}
function backequipment(){
        $("#first").show();
        $("#second").hide();
}

  $( ".date-picker" )
        .datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          changeYear: true,
          minDate: 0,
          showButtonPanel: true,
          numberOfMonths: 1
        });
        
        // upload task and reading data 
        attach_template();
     
        function attach_template(){
            console.log("loaddata");
            $('.loader').show();
            var type = "AU";
            var design_id = $("#attach_template option:selected").val();
            console.log(design_id);
            $.ajax({
                        type: "POST",
                        url: baseUrl + 'pm/importdesiganitiontemplate',
                        data: {design_id: design_id,type:type},
                        success: function (data) {
                                $.ajax({
                                    type: "POST",
                                    url: baseUrl + 'pm/importdesiganitiontemplatechange',
                                    data: {design_id: design_id,type:type},
                                    success: function (data) {
                                        console.log(data);
                                        $('.loader').hide();
                                        $("#tempalte-detail-view-change").html(data);

                                    }
                                });
                                //console.log(data);
                                $('.loader').hide();
                                $("#tempalte-detail-view").html(data);


                        }
                    });
        }
     

         
        $("#file").change(function(){
               console.log("Change event");
               $(".submit").trigger("click");
        });


        $("#image1").on('submit',(function(e) {
            e.preventDefault();    

            var file = document.getElementById('file').files[0];
            //console.log(file);
            $("#imagename").val(file.name);
            $.ajax({
                url: baseUrl + 'pm/uploadimage', // Url to which the request is send
                type: "POST",             // Type of request to be send, called as method
                data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                contentType: false,       // The content type used when sending data to the server.
                cache: false,             // To unable request pages to be cached
                processData:false,        // To send DOMDocument or non processed data file it is set to false
                success: function(data)   // A function to be called if request succeeds
                        {
                            if(data.trim()=='true'){
                               var html ='<img style="width: 178px;height: 247px;margin-bottom: 6px;" src="'+baseUrl+'public/pm/'+file.name+'">';
                               $(".viewimage").html(html);
                            }else{
                                
                            }
                            console.log(data);
                            $('#loading').hide();
                        }
            });
        }));
        
        
    $('#browsers option').each(function() {
    if($(this).is(':selected')){
        var equipmentname = $(this).val().trim();
        // Your code here with the selected value
        var opt = $('option[value="'+$(this).val()+'"]');
        alert(opt.length ? opt.attr('value') : 'NO OPTION');
       }
    });
    
    /* function to get vender details */
    
    function getvenderdetails(){
        vid = $("#venderid").val();
        $.ajax({
                type: "POST",
                url: baseUrl + 'pm/getvenderdetails',
                data: {vid: vid},
                success: function (data) {
                    var vdata= $.parseJSON(data);
                    console.log(vdata);
                    //console.log(vdata.first_name);
                    //console.log(vdata[0].first_name);
                   // $.each(data,function(i,vdata){
                        $("#vname").html(vdata[0].first_name+' '+vdata[0].last_name);
                        $("#vemail").html(vdata[0].email);
                        $("#vphone").html(vdata[0].phone_number);
                        $("#vcell").html(vdata[0].cell_number);
                        $("#vaddress").html(vdata[0].address1);

                    //});
                    $('.loader').hide();
                    //$("#tempalte-detail-view-change").html(data);

                }
            });
    }
