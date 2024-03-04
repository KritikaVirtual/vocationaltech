$(function() {
			$( "#tabs" ).tabs();
		  });

function showTenantUser(tId){
	resetUserInfo();
	if(tId!=''){
		    $.ajax({
					url         : baseUrl+"complete/showtuser",
					type        : "post",
					datatype    : 'json',
					data        : {tId:tId},
					success     : function( data ) {
						$('.loader').hide(); 
					   $('#create_user_dropdown').html(data);
					},
					error       : function(){
						$('.loader').hide();
						alert('There was an error');
					}  
					
				 });
         }    
}


function showUserInfo(tuid){
	if(tuid!=''){
		    $.ajax({
					url         : baseUrl+"complete/tuserinfo",
					type        : "post",
					datatype    : 'json',
					data        : {tuid:tuid},
					success     : function( data ) {
						$('.loader').hide(); 
					    rspData = $.parseJSON(data);
					    if(rspData.message=='success'){
							userData = rspData.userData;							
							$('#suite').val(userData.suite);
							$('#email').val(userData.email);
							$('#phone_number').val(userData.phone);
						}
					},
					error       : function(){
						$('.loader').hide();
						alert('There was an error');
					}  
					
				 });
         }    
}

function resetUserInfo(){
	$('select[name^="create_user"] option[value=""]').attr("selected","selected");
	$('#suite').val('');
	$('#email').val('');
	$('#phone_number').val('');
}

/***************Work order option Js code ****************/
function showEditForm(woId){
	if(woId!=''){
		$('.loader').show();
		    $.ajax({
					url         : baseUrl+"complete/showeditwo",
					type        : "post",
					datatype    : 'json',
					data        : {woId:woId},
					success     : function( data ) {
						$('.loader').hide(); 
					    //$('#edit_wo_form').show();
					    $('#edit_wo_form').html(data);
					    $('.fade_edit_wo').show();
					},
					error       : function(){
						$('.loader').hide();
						alert('There was an error');
					}  
					
				 });
         } 
}

function cancelWo(){
	//$('#edit_wo_form').hide();
	$('#edit_wo_form').html('');
	$('.fade_edit_wo').hide();
}

function updateWO(woId){
	var tenant = $('#tenant').val();
	var date_received = $('#date_received').val();
	var time_received = $('#time_received').val();
	var create_user = $('#create_user').val();
	var status = $('#wo_status').val();
	var category = $('#category').val();
	var wo_request = $('#work_order_request').val();
	var submit_flag = true;
	
	/**********Check form validation *******/
	if(tenant==''){
		$('#tenant_error').html("Select Tenant");
		submit_flag = false;
	}else{
		$('#tenant_error').html("");
	}
	
	if(date_received==''){
		$('#date_error').html("Select Date");
		submit_flag = false;
	}else{
		$('#date_error').html("");
	}
	
	if(time_received==''){
		$('#time_error').html("Select Time");
		submit_flag = false;
	}else{
		$('#time_error').html("");
	}
	
	if(create_user==''){
		$('#time_error').html("Select Request By");
		submit_flag = false;
	}else{
		$('#time_error').html("");
	}
	
	if(status==1 && category==''){
	    $('#category_error').html("Select Category");
		submit_flag = false;
	}else{
		$('#category_error').html("");
	}
	
	if(wo_request==''){
		$('#worequest_error').html("Work order request can't be blank");
		submit_flag = false;
	}else{
		$('#worequest_error').html("");
	}
	
	if(submit_flag){
		if(woId!=''){
		$('.loader').show();
		    $.ajax({
					url         : baseUrl+"complete/updatewoinfo",
					type        : "post",
					datatype    : 'json',
					data        : {woId:woId,tenant:tenant,date_received:date_received,
						           time_received:time_received, create_user:create_user, status:status,
						           category:category, wo_request:wo_request },
					success     : function( data ) {
						$('.loader').hide();						 
					    //$('#edit_wo_form').show();
					    //$('#edit_wo_form').html(data);
					    $('#fdw').addClass('fade_edit_wo');
					    if(data=='success'){
							location.reload();
						}else{
							alert('Error occurred');
						}
					},
					error       : function(){
						$('.loader').hide();
						alert('There was an error');
					}  
					
				 });
         } 
		
	}else
	return false;
}

function showParameter(bId){
	$('.loader').show();
		    $.ajax({
					url         : baseUrl+"complete/woparameter",
					type        : "post",
					datatype    : 'json',
					data        : {bId:bId},
					success     : function( data ) {
						$('.loader').hide(); 
					    //$('#edit_wo_form').show();
					    $('#bd_dft_fm').html(data);
					    $('#bd_dft_fm').show();
					    $('.fade_default_opt').show();
					},
					error       : function(){
						$('.loader').hide();
						alert('There was an error');
					}  
					
				 });
}

function cancelWoParam(){
	//$('#edit_wo_form').hide();
	$('#bd_dft_fm').html('');
	$('#bd_dft_fm').hide();
	$('.fade_default_opt').hide();
}

function updateBuildOpt(bId){
	if(bId!=''){
		var status_closed = ($('#status_closed').prop('checked'))?'1':'0';
		var billable = ($('#billable').prop('checked'))?'1':'0';
		var inc_tnt_rqt = ($('#inc_tnt_rqt').prop('checked'))?'1':'0';
		var email_tenant = ($('#email_tenant').prop('checked'))?'1':'0';
		var sale_tax = $('#sale_tax').val();
		var auto_charge = $('#auto_charge').val();
		var dft_markup = $('#dft_markup').val();
		var override_markup = $('#override_markup').val();
		var time_in_start = $('#time_in_start').val();
		var time_in_incmt = $('#time_in_incmt').val();
		var time_min_charge = $('#time_min_charge').val();
		$.ajax({
					url         : baseUrl+"complete/updateparameter",
					type        : "post",
					datatype    : 'json',
					data        : {status_closed:status_closed,billable:billable,inc_tnt_rqt:inc_tnt_rqt,
						           email_tenant:email_tenant, sale_tax:sale_tax, auto_charge:auto_charge,
						           dft_markup:dft_markup, override_markup:override_markup,time_in_start:time_in_start,
						            time_in_incmt:time_in_incmt,time_min_charge:time_min_charge,building:bId},
					success     : function( data ) {
						$('.loader').hide();
						//alert(data); 
					    //$('#edit_wo_form').show();
					    //$('#edit_wo_form').html(data);
					    //$('#fdw').addClass('fade_edit_wo');
					    if(data=='success'){
							location.reload();
						}else{
							alert('Error occurred');
						}
					},
					error       : function(){
						$('.loader').hide();
						alert('There was an error');
					}  
					
				 });
		
	}
}

/*************** Description Js code *****************/
function showDescription(woId){
	$('.loader').show();
	$.ajax({
			url         : baseUrl+"complete/showdesc",
			type        : "post",
			datatype    : 'json',
			data        : {woId:woId},
			success     : function( data ) {
				$('.loader').hide(); 
				//$('#edit_wo_form').show();
				$('#wo_desc').html(data);
				$('#wo_desc').show();
				$('.fade_default_opt').show();
			},
			error       : function(){
				$('.loader').hide();
				alert('There was an error');
			}  
			
		 });
}

function cancelWoDesc(){
	//$('#edit_wo_form').hide();
	$('#wo_desc').html('');
	$('#wo_desc').hide();
	$('.fade_default_opt').hide();
}

function setNote(obj){
	$('#work_description').val(obj.value);
}


function saveDescription(woId){
	var desc = $('#work_description').val();
	if(desc==''){
		$('#desc_error').html("Please enter work description");
		return false;
	}else{
	   $('.loader').show();
		    $.ajax({
					url         : baseUrl+"complete/savedescription",
					type        : "post",
					datatype    : 'json',
					data        : {woId:woId,description:desc},
					success     : function( data ) {
						$('.loader').hide(); 
					    if(data=='success'){
							location.reload();
						}else{
							alert('Error occurred');
						}
					},
					error       : function(){
						$('.loader').hide();
						alert('There was an error');
					}  
					
				 });
		}		 
}

/*************** Labor charge Js Code *****************/
function showLaborForm(bId,woId){
	$('.loader').show();
	$.ajax({
			url         : baseUrl+"complete/laborform",
			type        : "post",
			datatype    : 'json',
			data        : {bId:bId,woId:woId},
			success     : function( data ) {
				$('.loader').hide(); 
				//$('#edit_wo_form').show();
				$('#labor_form').html(data);
				$('#labor_form').show();
				$('.fade_default_opt').show();
			},
			error       : function(){
				$('.loader').hide();
				alert('There was an error');
			}  
			
		 });
}

function cancelLaborForm(){
	$('#labor_form').html('');
	$('#labor_form').hide();
	$('.fade_default_opt').hide();
}

function setCharge(uId){
	var labor_id = labor_user[uId];
	if(labor_id!='' && labor_id != undefined){
		setLaborSelected(labor_id);		
	}else{
		var dft_lab_id = default_lcharge['dft_labor'];
		setLaborSelected(dft_lab_id);
	}
}

function setLaborSelected(labor_id){
	$('select[name^="labor_charge"] option:selected').attr("selected",null);
	$('select[name^="labor_charge"] option[value="'+labor_id+'"]').attr("selected","selected");
	setLaborCharge(labor_id);
}

function setLaborCharge(blid){
	//alert(blid);
	var charge = labor_charge[blid];
	$('#charge_hour').val(charge);
}


function saveLaborData(woId){
	$('#save_labor').attr('disabled','disabled');
	var emp_id = $('#emp_id').val();
	var charge_hour = $('#charge_hour').val();
	var labor_charge = $('#labor_charge').val();
	var rate_charge = $('#rate_charge').val();
	var job_time = $('#job_time').val();
	var submit_flag = true;
	
	if(emp_id==''){			
		$('#cwerr_emp').html('Please select the employee.');
		submit_flag = false;
	}else{
		$('#cwerr_emp').html('');
	}
	
	if(labor_charge==''){			
		$('#cwerr_charge').html('Please select the labor charge.');
		submit_flag = false;
	}else{
		$('#cwerr_charge').html('');
	}
	
	if(charge_hour==''){			
		$('#cwerr_charge').html('Charge can not be blank.');
		submit_flag = false;
	}else{
		$('#cwerr_charge').html('');
	}
	
	if(rate_charge==''){			
		$('#cwerr_rate').html('Please select the rate charge.');
		submit_flag = false;
	}else{
		$('#cwerr_rate').html('');
	}
	
	if(job_time==''){			
		$('#cwerr_job').html('Please select the rate charge.');
		submit_flag = false;
	}else{
		$('#cwerr_job').html('');
	}
	
	if(submit_flag==false){			 
			$('#save_labor').attr('disabled',false);
			return false; 
		 }else{
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"complete/savelabor",
						type        : "post",
						datatype    : 'json',
						data        : {
							            emp_id:emp_id,charge_hour:charge_hour,bl_id:labor_charge,
							            rate_charge:rate_charge,job_time:job_time,woId:woId							            
							          },
						success     : function( data ) {
							$('.loader').hide(); 
							var content = $.parseJSON(data);
							if(content.status=='success'){
								$('#success_msg').html(content.msg);
								location.reload();
							}else{
								//alert('Error occurred');
								$('#error_msg').html(content.msg);
								$('#save_labor').attr('disabled',false);
							}
						},
						error       : function(){
							$('.loader').hide();
							alert('There was an error');
						}  
						
					 });
		 }
	
}

function showEditLabor(lid,bId){
	$('.loader').show();
	$.ajax({
			url         : baseUrl+"complete/editlabor",
			type        : "post",
			datatype    : 'json',
			data        : {bId:bId,lid:lid},
			success     : function( data ) {
				$('.loader').hide(); 
				//$('#edit_wo_form').show();
				$('#labor_form').html(data);
				$('#labor_form').show();
				$('.fade_default_opt').show();
			},
			error       : function(){
				$('.loader').hide();
				alert('There was an error');
			}  
			
		 });	
}

function updateLaborData(lid){
	$('#save_labor').attr('disabled','disabled');
	var emp_id = $('#emp_id').val();
	var charge_hour = $('#charge_hour').val();
	var labor_charge = $('#labor_charge').val();
	var rate_charge = $('#rate_charge').val();
	var job_time = $('#job_time').val();
	var submit_flag = true;
	
	if(emp_id==''){			
		$('#cwerr_emp').html('Please select the employee.');
		submit_flag = false;
	}else{
		$('#cwerr_emp').html('');
	}
	
	if(labor_charge==''){			
		$('#cwerr_charge').html('Please select the labor charge.');
		submit_flag = false;
	}else{
		$('#cwerr_charge').html('');
	}
	
	if(charge_hour==''){			
		$('#cwerr_charge').html('Charge can not be blank.');
		submit_flag = false;
	}else{
		$('#cwerr_charge').html('');
	}
	
	if(rate_charge==''){			
		$('#cwerr_rate').html('Please select the rate charge.');
		submit_flag = false;
	}else{
		$('#cwerr_rate').html('');
	}
	
	if(job_time==''){			
		$('#cwerr_job').html('Please select the rate charge.');
		submit_flag = false;
	}else{
		$('#cwerr_job').html('');
	}
	
	if(submit_flag==false){			 
			$('#save_labor').attr('disabled',false);
			return false; 
		 }else{
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"complete/savelabor",
						type        : "post",
						datatype    : 'json',
						data        : {
							            emp_id:emp_id,charge_hour:charge_hour,bl_id:labor_charge,
							            rate_charge:rate_charge,job_time:job_time,lid:lid							            
							          },
						success     : function( data ) {
							$('.loader').hide(); 
							var content = $.parseJSON(data);
							if(content.status=='success'){
								$('#success_msg').html(content.msg);
								location.reload();
							}else{
								//alert('Error occurred');
								$('#error_msg').html(content.msg);
								$('#save_labor').attr('disabled',false);
							}
						},
						error       : function(){
							$('.loader').hide();
							alert('There was an error');
						}  
						
					 });
		 }
	
}

function deleteLaborCharge(lid){
	var check_delete='YES';	
	var return_value = prompt("For Deleting Labor Charge, Enter Yes in Capital letter.");
	    if(return_value!=null){	
				if(check_delete === return_value){				
				$('.loader').show();
				$.ajax({        
					type: "POST",
					url: baseUrl+"complete/deletelabor",
					dataType:'json',
					data: {
						lid: lid		
					},					
					success: function (msg) {
						$('.loader').hide();						
						if(msg==true){
							$('#cw_success').html('Labor Charge has been deleted successfully.');								
							location.reload();							
						}else{
							$('#cw_error').html('Error Occurred during deletion of labor charge.');
						}
					}

				});
			}
			else{
				$('#cw_error').html('You have entered wrong word.');
			}
		}
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

function isTime(evt) {

        var charCode = (evt.which) ? evt.which : evt.keyCode;               

        if (
            (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 58 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode != 8 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK backspace, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    }

/*************Building Service Charge Js Code ***************/
function showBServiceForm(bId,woId){
	$('.loader').show();
	$.ajax({
			url         : baseUrl+"complete/addbservice",
			type        : "post",
			datatype    : 'json',
			data        : {bId:bId,woId:woId},
			success     : function( data ) {
				$('.loader').hide(); 
				//$('#edit_wo_form').show();
				$('#bservice_form').html(data);
				$('#bservice_form').show();
				$('.fade_default_opt').show();
			},
			error       : function(){
				$('.loader').hide();
				alert('There was an error');
			}  
			
		 });
}

function cancelBService(){
	$('#bservice_form').html('');
	$('#bservice_form').hide();
	$('.fade_default_opt').hide();
}

function setBServCharge(bsid){
	var charge = bs_charge[bsid];
	var minimum = bs_minimum[bsid];
	$('#charge').val(charge);
	$('#amount_requested').val(minimum);
}


function saveBServiceData(woId){
	$('#save_bdservice').attr('disabled','disabled');
	var service = $('#service').val();
	var charge = $('#charge').val();
	var amount_requested = $('#amount_requested').val();
	var comment = $('#comment').val();	
	var submit_flag = true;
	
	if(service==''){			
		$('#cwerr_service').html('Please select the service.');
		submit_flag = false;
	}else{
		$('#cwerr_service').html('');
	}
	
	if(charge==''){			
		$('#cwerr_charge').html('Charge can not be blank.');
		submit_flag = false;
	}else{
		$('#cwerr_charge').html('');
	}
	
	if(amount_requested==''){			
		$('#cwerr_amount').html('Please enter the requested amount.');
		submit_flag = false;
	}else{
		$('#cwerr_amount').html('');
	}	
	
	
	if(submit_flag==false){			 
			$('#save_bdservice').attr('disabled',false);
			return false; 
		 }else{
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"complete/savebuildingservice",
						type        : "post",
						datatype    : 'json',
						data        : {
							            service:service,charge:charge,amount_requested:amount_requested,
							            comment:comment,woId:woId							            
							          },
						success     : function( data ) {
							$('.loader').hide(); 
							var content = $.parseJSON(data);
							if(content.status=='success'){
								$('#success_msg').html(content.msg);
								location.reload();
							}else{
								//alert('Error occurred');
								$('#error_msg').html(content.msg);
								$('#save_bdservice').attr('disabled',false);
							}
						},
						error       : function(){
							$('.loader').hide();
							alert('There was an error');
						}  
						
					 });
		 }
	
}

function showEditBService(bsId,bId){
	$('.loader').show();
	$.ajax({
			url         : baseUrl+"complete/editbservice",
			type        : "post",
			datatype    : 'json',
			data        : {bId:bId,bsId:bsId},
			success     : function( data ) {
				$('.loader').hide(); 
				//$('#edit_wo_form').show();
				$('#bservice_form').html(data);
				$('#bservice_form').show();
				$('.fade_default_opt').show();
			},
			error       : function(){
				$('.loader').hide();
				alert('There was an error');
			}  
			
		 });	
}

function updateBServiceData(bsId){
	$('#save_bdservice').attr('disabled','disabled');
	var service = $('#service').val();
	var charge = $('#charge').val();
	var amount_requested = $('#amount_requested').val();
	var comment = $('#comment').val();	
	var submit_flag = true;
	
	if(service==''){			
		$('#cwerr_service').html('Please select the service.');
		submit_flag = false;
	}else{
		$('#cwerr_service').html('');
	}
	
	if(charge==''){			
		$('#cwerr_charge').html('Charge can not be blank.');
		submit_flag = false;
	}else{
		$('#cwerr_charge').html('');
	}
	
	if(amount_requested==''){			
		$('#cwerr_amount').html('Please enter the requested amount.');
		submit_flag = false;
	}else{
		$('#cwerr_amount').html('');
	}	
	
	
	if(submit_flag==false){			 
			$('#save_bdservice').attr('disabled',false);
			return false; 
		 }else{
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"complete/savebuildingservice",
						type        : "post",
						datatype    : 'json',
						data        : {
							            service:service,charge:charge,amount_requested:amount_requested,
							            comment:comment,bsId:bsId							            
							          },
						success     : function( data ) {
							$('.loader').hide(); 
							var content = $.parseJSON(data);
							if(content.status=='success'){
								$('#success_msg').html(content.msg);
								location.reload();
							}else{
								//alert('Error occurred');
								$('#error_msg').html(content.msg);
								$('#save_bdservice').attr('disabled',false);
							}
						},
						error       : function(){
							$('.loader').hide();
							alert('There was an error');
						}  
						
					 });
		 }
	
}

function deleteBservice(bsId){
	var check_delete='YES';	
	var return_value = prompt("For Deleting Building Service Charge, Enter Yes in Capital letter.");
	    if(return_value!=null){	
				if(check_delete === return_value){				
				$('.loader').show();
				$.ajax({        
					type: "POST",
					url: baseUrl+"complete/deletebuildingservice",
					dataType:'json',
					data: {
						bsId: bsId		
					},					
					success: function (msg) {
						$('.loader').hide();						
						if(msg==true){
							$('#cw_success').html('Building Service Charge has been deleted successfully.');								
							location.reload();							
						}else{
							$('#cw_error').html('Error Occurred during deletion of Building Service Charge.');
						}
					}

				});
			}
			else{
				$('#cw_error').html('You have entered wrong word.');
			}
		}
}

/**************** Material Charge Js Code ******************/
function showMaterialForm(bId,woId){
	$('.loader').show();
	$.ajax({
			url         : baseUrl+"complete/addmaterial",
			type        : "post",
			datatype    : 'json',
			data        : {bId:bId,woId:woId},
			success     : function( data ) {
				$('.loader').hide(); 
				//$('#edit_wo_form').show();
				$('#material_form').html(data);
				$('#material_form').show();
				$('.fade_default_opt').show();
			},
			error       : function(){
				$('.loader').hide();
				alert('There was an error');
			}  
			
		 });
}

function cancelMaterial(){
	$('#material_form').html('');
	$('#material_form').hide();
	$('.fade_default_opt').hide();
}

function setMaterial(mid){
	var cost = material_cost[mid];
	var markup = material_markup[mid];
	$('#cost').val(cost);
	$('#markup').val(markup);
}


function saveMaterialData(woId){
	$('#save_material').attr('disabled','disabled');
	var material_id = $('#material_id').val();
	var charge = $('#charge').val();
	var amount_requested = $('#amount_requested').val();
	var comment = $('#comment').val();	
	var submit_flag = true;
	
	if(material_id==''){			
		$('#cwerr_service').html('Please select the service.');
		submit_flag = false;
	}else{
		$('#cwerr_service').html('');
	}
	
	if(charge==''){			
		$('#cwerr_charge').html('Charge can not be blank.');
		submit_flag = false;
	}else{
		$('#cwerr_charge').html('');
	}
	
	if(amount_requested==''){			
		$('#cwerr_amount').html('Please enter the requested amount.');
		submit_flag = false;
	}else{
		$('#cwerr_amount').html('');
	}	
	
	
	if(submit_flag==false){			 
			$('#save_material').attr('disabled',false);
			return false; 
		 }else{
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"complete/savematerial",
						type        : "post",
						datatype    : 'json',
						data        : {
							            service:service,charge:charge,amount_requested:amount_requested,
							            comment:comment,woId:woId							            
							          },
						success     : function( data ) {
							$('.loader').hide(); 
							var content = $.parseJSON(data);
							if(content.status=='success'){
								$('#success_msg').html(content.msg);
								location.reload();
							}else{
								//alert('Error occurred');
								$('#error_msg').html(content.msg);
								$('#save_material').attr('disabled',false);
							}
						},
						error       : function(){
							$('.loader').hide();
							alert('There was an error');
						}  
						
					 });
		 }
	
}
