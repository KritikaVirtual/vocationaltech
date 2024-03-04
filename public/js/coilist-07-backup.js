function showAddCoiTenant(tId){
	if(tId!=''){
		$('.loader').show();
		$.ajax({
				url         : baseUrl+"coilist/addservice",
				type        : "post",
				datatype    : 'json',
				data        : {tId:tId},
				success     : function( data ) {
					$('.loader').hide(); 
					//$('#edit_wo_form').show();
					$('#add_service_form').html(data);
					//$('#add_service_form').show();
					//$('.service_popup_form').show();
					$('#add_service_form_href').trigger('click');
				},
				error       : function(){
					$('.loader').hide();
					alert('There was an error');
				}  
				
			 });
	 }
}

function cancelAddService(){
	$("div.fancybox-close").trigger("click");
	$('#add_service_form').html('');
	$('#add_service_form').hide();
	$('.service_popup_form').hide();
}


function saveCoiTenant(){
	parent.CheckForSessionpop(baseUrl);
	//if(bId!=''){
		//alert('hello');
		var submit_flag=true;
		$('#save_coi').attr('disabled','disabled');
		var userData = new FormData();
		
		var company_name = $('#company_name').val();
		var expiration_date = $('#expiration_date').val();
        var tenant_id = $('#tenant_id').val();
        var tenant_number = $('#tenant_number').val();
        var buliding_id = $('#buliding_id').val();		
		var equipmentmenual = $("#equipmentmenual").val().trim();
		
		if(company_name==''){			
			$('#cerror_name').html('Please enter Company Name.');
			submit_flag = false;
		}else{
			$('#cerror_name').html('');
		}
        
        if(expiration_date ==''){			
			$('#cerror_expiration').html('Please enter Expiration Date.');
			submit_flag = false;
		}else{
			$('#cerror_expiration').html('');
		}
		var manual = $("#equipmentmenual").prop('files')[0];
		var flag_val = escape($("#equipmentmenual").attr('name'));
		
        if (typeof manual !== 'undefined') {
        var ext = manual.name.split(".");
        var type_ext = ext[ext.length - 1];
        //console.log(type_ext);
        if (type_ext !== 'pdf' && type_ext !== 'PDF') {
            $("#equipmentmenual").focus();
            $("#equipmentmenual").addClass('error-border');
            $("#equipmentmenual_error").html("Please upload Only a PDF File");
            submit_flag = false;
        } else {
            $("#equipmentmenual_error").html("");
            $("#equipmentmenual").removeClass('error-border');
        }
    }
         if(submit_flag==false){			 
			$('#save_coi').attr('disabled',false);
			return false; 
		 }else{
			 
			  if (typeof manual !== 'undefined') {
			    userData.append('file[' + flag_val + ']', manual);
			  }
                userData.append('coi_au_date_to', expiration_date);
				userData.append('tenant_Id', tenant_id);
				userData.append('tenant_number', tenant_number);
				userData.append('building_id', buliding_id);

			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"coilist/saveservice",
						type        : "post",
						datatype    : 'json',
						cache: false,
                        contentType: false,
                        processData: false,
                        data: userData,
						success     : function( data ) {
							$('.loader').hide(); 
							var content = $.parseJSON(data);
							if(content.status=='success'){
								$('#error_msg').html('');
								$('#success_msg').html(content.msg);
								location.reload();
							}else{
								//alert('Error occurred');
								$('#success_msg').html('');
								$('#error_msg').html(content.msg);
								$('#save_coi').attr('disabled',false);
							}
						},
						error       : function(){
							$('.loader').hide();
							alert('There was an error');
						}  
						
					 });
		 }
        
        
	/* }else{
		alert('Error Occurred');
		return false;
	} */
}

function deleteCoiList(cid){
	var check_delete='YES';
	//var con = confirm("Do you really want to delete category?");
	jPrompt('For Deleting Coi List, Enter Yes in Capital letters.', '', 'Alert', function(return_value) {
	    if(return_value!=null){	
				if(check_delete === return_value){
				//var building_id = $('#building_id').val();
				$('.loader').show();
				$.ajax({        
					type: "POST",
					url: baseUrl + "coilist/deletecoi",
					dataType:'json',
					data: {
						cid: cid		
					},					
					success: function (msg) {
						$('.loader').hide();						
						if(msg==true){
							$('#service_success').html('Coi List has been deleted successfully.');								
							location.reload();							
						}else{
							$('#service_error').html('Error Occurred during deletion of coi list.');
						}
					}

				});
			}
			else{
				jAlert('You have entered wrong word.', 'Vision Work Orders');
			}
		}
	});	
}
function showEditCoi(cid){
	if(cid!=''){
		$('.loader').show();
		$.ajax({
				url         : baseUrl+"coilist/editservice",
				type        : "post",
				datatype    : 'json',
				data        : {cid:cid},
				success     : function( data ) {
					$('.loader').hide(); 
					//$('#edit_wo_form').show();
					$('#edit_service_form').html(data);
					//$('#edit_service_form').show();
					//$('.service_popup_form').show();
					$('#edit_service_form_href').trigger('click');
				},
				error       : function(){
					$('.loader').hide();
					alert('There was an error');
				}  
				
			 });
	 }
}     


function cancelEditService(){
	$("div.fancybox-close").trigger("click");
	$('#edit_service_form').html('');
	$('#edit_service_form').hide();
	$('.service_popup_form').hide();
}


function updateCoiList(cid){
	parent.CheckForSessionpop(baseUrl);
	if(cid!=''){
		//alert('hello');
		var submit_flag=true;
		$('#save_service').attr('disabled','disabled');
		var userData = new FormData();
		
		var buliding_id = $('#buliding_id').val();
		var unique_cost = $('#unique_cost').val();				
		var tenant_number = $('#tenant_number').val();
		var tenant_id = $('#tenant_id').val();		
		var coi_date_from = $('#coi_date_from').val();
		var coi_type = $('#coi_type').val();
		var edit_expiration_date = $('#edit_expiration_date').val();
		var equipmentmenual = $("#equipmentmenual").val().trim();
		
        if(edit_expiration_date==''){			
			$('#cerror_expiration').html('Please enter Expiration Date.');
			submit_flag = false;
		}else{
			$('#cerror_expiration').html('');
		}
        
        var manual = $("#equipmentmenual").prop('files')[0];
		var flag_val = escape($("#equipmentmenual").attr('name'));
		
        if (typeof manual !== 'undefined') {
        var ext = manual.name.split(".");
        var type_ext = ext[ext.length - 1];
        //console.log(type_ext);
        if (type_ext !== 'pdf' && type_ext !== 'PDF') {
            $("#equipmentmenual").focus();
            $("#equipmentmenual").addClass('error-border');
            $("#equipmentmenual_error").html("Please upload Only a PDF File");
            submit_flag = false;
        } else {
            $("#equipmentmenual_error").html("");
            $("#equipmentmenual").removeClass('error-border');
        }
    }
         
         if(submit_flag==false){			 
			$('#save_service').attr('disabled',false);
			return false; 
		 }else{			 
			   
			   if (typeof manual !== 'undefined') {
			    userData.append('file[' + flag_val + ']', manual);  
			   }
                userData.append('building_id', buliding_id);
				userData.append('uniquecostcenter', unique_cost);
				userData.append('tenant_number', tenant_number);
				userData.append('tenant_Id', tenant_id);
				userData.append('coi_au_date_from', coi_date_from);
				userData.append('coi_au_date_to', edit_expiration_date);
				userData.append('coi_au_Ten_or_Vendor', coi_type);
				userData.append('coi_au_tenant_id', cid);
			 
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"coilist/updateservice",
						type        : "post",
						datatype    : 'json',
						cache       : false,
                        contentType : false,
                        processData : false,
                        data        : userData,
						success     : function( data ) {
							$('.loader').hide(); 
							var content = $.parseJSON(data);
							if(content.status=='success'){
								$('#error_msg').html('');
								$('#success_msg').html(content.msg);
								location.reload();
							}else{
								//alert('Error occurred');
								$('#success_msg').html('');
								$('#error_msg').html(content.msg);
								$('#save_service').attr('disabled',false);
							}
						},
						error       : function(){
							$('.loader').hide();
							setInterval(function(){ jAlert('There was an error', 'Vision Work Orders'); }, 1000);
						}  
						
					 });
		 }
        
        
	}else{
		alert('Error Occurred');
		return false;
	}
}

function importServiceTemplate(bId){
	$('.loader').show();
	$.ajax({
			url         : baseUrl+"buildservice/servicetemplate",
			type        : "post",
			datatype    : 'json',
			data        : {bId:bId},			
			success     : function( data ) {
				$('.loader').hide(); 
				//$('#edit_wo_form').show();
				$('#service_template_form').html(data);
				//$('#service_template_form').show();
				//$('.service_popup_form').show();
				$('#service_template_form_href').trigger('click');
			},
			error       : function(){
				$('.loader').hide();
				alert('There was an error');
			}  
			
		 });
}

function cancelServiceTemplate(){
	$("div.fancybox-close").trigger("click");
	$('#service_template_form').html('');
	$('#service_template_form').hide();
	$('.service_popup_form').hide();
} 


function setServiceTemplate(bsid){
	$('.loader').show();
		$.ajax({
				url         : baseUrl+"buildservice/loadservicetemplate",
				type        : "post",
				datatype    : 'json',
				data        : {bsid:bsid},
				success     : function( data ) {
					$('.loader').hide(); 
					//$('#edit_wo_form').show();
					$('#load_global_tmp_div').html(data);					
				},
				error       : function(){
					$('.loader').hide();
					alert('There was an error');
				}  
				
			 });
}

function isNumber(evt) {

        var charCode = (evt.which) ? evt.which : evt.keyCode;        

        if (
            (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode != 8 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK backspace, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    }

$(function() {
$(".modalbox").fancybox({'openEffect': 'none',fitToView: true});
$( "#expiration_date" ).datepicker({changeMonth: true,
            changeYear: true,minDate: 0});
$( "#edit_expiration_date" ).datepicker({changeMonth: true,
            changeYear: true,minDate: 0});			

});