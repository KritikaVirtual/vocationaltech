/***** move single item *****/
function move_list_items(sourceid, destinationid)
{	
	var select_item = $("#"+sourceid+"  option:selected").val();	
	if(select_item == '' || select_item == null || select_item == undefined){
		jAlert('Please select item.', 'Vision Work Orders');
		return false;
	}else if(select_item == 'assigned'){
		jAlert('This user is already assigned.', 'Vision Work Orders');
		return false;
	}
	else{
       $("#"+sourceid+"  option:selected").appendTo("#"+destinationid);
    }
}

function showAddLabor(bId){
	if(bId!=''){
		$('.loader').show();
		$.ajax({
				url         : baseUrl+"bill/addlabor",
				type        : "post",
				datatype    : 'json',
				data        : {bId:bId},
				success     : function( data ) {
					$('.loader').hide(); 
					//$('#edit_wo_form').show();
					$('#add_labor_form').html(data);
					//$('#add_labor_form').show();
					//$('.labor_popup_form').show();
					$('#add_labor_form_href').trigger('click')
				},
				error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');
				}  
				
			 });
	 }
}

function saveLaborCharge(bId){
	$('#save_labor').attr('disabled',true);
	parent.CheckForSessionpop(baseUrl);
	if(bId!=''){
		//alert('hello');
		var submit_flag=true;
		$('#save_labor').attr('disabled','disabled');
		var description = $('#description').val();
		//alert('HELLO pop');
		var status = $('#status').val();
		var charge_hour = $('#charge_hour').val();
		var global_template = $('#global_template').val();
		var import_template = $('#import_template').val();
		var set_default = $('#set_default').val();
		var account_user ='';
		$('#user_to_list option').prop('selected', true);
		
		if($('#user_to_list').val()!='' && $('#user_to_list').val()!= null)
        account_user = ($('#user_to_list').val()).join();
        
        if(description==''){			
			$('#lerror_desc').html('Please enter description.');
			submit_flag = false;
		}else{
			$('#lerror_desc').html('');
		}
        
        if(charge_hour==''){			
			$('#lerror_charge').html('Please enter Charge/Hour.');
			submit_flag = false;
		}else{
			$('#lerror_charge').html('');
		}
         
        if(set_default=='1' && account_user==''){
			$('#lerror_default').html('Please assign to user from list of Account Users to make it Default.');
			submit_flag = false;			
		}else{
			$('#lerror_default').html('');
		} 
         if(submit_flag==false){			 
			$('#save_labor').attr('disabled',false);
			return false; 
		 }else{
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"bill/savelaborcharge",
						type        : "post",
						datatype    : 'json',
						data        : {
							            description:description,status:status,building:bId,
							            charge_hour:charge_hour,global_template:global_template,
							            set_default:set_default,assign_to:account_user,import_template:import_template
							          },
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
								$('#save_labor').attr('disabled',false);
							}
						},
						error       : function(){
							$('.loader').hide();
							setInterval(function(){ jAlert('There was an error', 'Vision Work Orders'); }, 1100);
														
						}  
						
					 });
		 }
        
        
	}else{
		jAlert('Error Occurred', 'Vision Work Orders');
		return false;
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
    
    
 function showEditLabor(blid,bId){
	if(bId!=''&& blid!=''){
		$('.loader').show();
		$.ajax({
				url         : baseUrl+"bill/editlabor",
				type        : "post",
				datatype    : 'json',
				data        : {bId:bId,blid:blid},
				success     : function( data ) {
					$('.loader').hide(); 
					//$('#edit_wo_form').show();
					$('#edit_labor_form').html(data);
					//$('#edit_labor_form').show();
					//$('.labor_popup_form').show();
					$('#edit_labor_form_href').trigger('click');
				},
				error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');							
				}  
				
			 });
	 }
}       

function updateLaborCharge(blid,bId){
	parent.CheckForSessionpop(baseUrl);
	if(bId!='' && blid!=''){
		//alert('hello');
		var submit_flag=true;
		$('#save_labor').attr('disabled','disabled');
		var description = $('#description').val();
		//alert('HELLO pop');
		var status = $('#status').val();
		var charge_hour = $('#charge_hour').val();		
		var set_default = $('#set_default').val();
		var global_template = $('#global_template').val();
		var account_user ='';
		$('#user_to_list option').prop('selected', true);
		
		if($('#user_to_list').val()!='' && $('#user_to_list').val()!= null)
        account_user = ($('#user_to_list').val()).join();
        
        if(description==''){			
			$('#lerror_desc').html('Please enter description.');
			submit_flag = false;
		}else{
			$('#lerror_desc').html('');
		}
        
        if(charge_hour==''){			
			$('#lerror_charge').html('Please enter Charge/Hour.');
			submit_flag = false;
		}else{
			$('#lerror_charge').html('');
		}
        
        if(set_default=='1' && account_user==''){
			$('#lerror_default').html('Please assign to user from list of Account Users to make it Default.');
			submit_flag = false;			
		}else{
			$('#lerror_default').html('');
		}  
         if(submit_flag==false){			 
			$('#save_labor').attr('disabled',false);
			return false; 
		 }else{
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"bill/updatelaborcharge",
						type        : "post",
						datatype    : 'json',
						data        : {
							            description:description,status:status,building:bId,
							            charge_hour:charge_hour,blid:blid,
							            set_default:set_default,assign_to:account_user,global_template:global_template
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
							setInterval(function(){ jAlert('There was an error', 'Vision Work Orders'); }, 1000);
														
						}  
						
					 });
		 }
        
        
	}else{
		jAlert('Error Occurred', 'Vision Work Orders');							
		return false;
	}
}


function deleteLaborCharge(blid){
	var check_delete='YES';	
		jPrompt('For Deleting Labor Charge, Enter Yes in Capital letters.', '', 'Vision Work Orders', function(return_value) {
	    if(return_value!=null){	
				if(check_delete === return_value){				
				$('.loader').show();
				$.ajax({        
					type: "POST",
					url: baseUrl + "bill/deletelcharge",
					dataType:'json',
					data: {
						blid: blid		
					},					
					success: function (msg) {
						$('.loader').hide();						
						if(msg==true){
							$('#bill_success').html('Labor Charge has been deleted successfully.');								
							location.reload();							
						}else{
							$('#bill_error').html('Error Occurred during deletion of labor charge.');
						}
					}

				});
			}else{
				jAlert('You have entered wrong word.', 'Vision Work Orders');
			}
		}
		});	
}

function cancelAddLabor(){
	$("div.fancybox-close").trigger("click");
	$('#add_labor_form').html('');
	$('#add_labor_form').hide();
	$('.labor_popup_form').hide();
}

function cancelEditModule(){
	$("div.fancybox-close").trigger("click");
	$('#edit_labor_form').html('');
	$('#edit_labor_form').hide();
	$('.labor_popup_form').hide();
}

function importLaborTemplate(bId){
	$('.loader').show();
	$.ajax({
			url         : baseUrl+"bill/labortemplate",
			type        : "post",
			datatype    : 'json',
			data        : {bId:bId},			
			success     : function( data ) {
				$('.loader').hide(); 
				//$('#edit_wo_form').show();
				$('#labor_template_form').html(data);
				//$('#labor_template_form').show();
				//$('.labor_popup_form').show();
				$('#labor_template_form_href').trigger('click');
			},
			error       : function(){
				$('.loader').hide();
				jAlert('There was an error', 'Vision Work Orders');
			}  
			
		 });
}

function cancelLaborTemplate(){
	$("div.fancybox-close").trigger("click");
	$('#labor_template_form').html('');
	$('#labor_template_form').hide();
	$('.labor_popup_form').hide();
} 


function setTemplateData(blid){
	$('.loader').show();
		$.ajax({
				url         : baseUrl+"bill/loadlabortemplate",
				type        : "post",
				datatype    : 'json',
				data        : {blid:blid},
				success     : function( data ) {
					$('.loader').hide(); 
					//$('#edit_wo_form').show();
					$('#load_global_tmp_div').html(data);					
				},
				error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');							
				}  
				
			 });
}

function showAddRate(bId){
	if(bId!=''){
		$('.loader').show();
		$.ajax({
				url         : baseUrl+"bill/addrate",
				type        : "post",
				datatype    : 'json',
				data        : {bId:bId},
				success     : function( data ) {
					$('.loader').hide(); 
					//$('#edit_wo_form').show();
					$('#add_rate_form').html(data);
					//$('#add_rate_form').show();
					//$('.labor_popup_form').show();
					$('#add_rate_form_href').trigger('click');
				},
				error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');							
				}  
				
			 });
	 }
}

function cancelAddRate(){
	$("div.fancybox-close").trigger("click");
	$('#add_rate_form').html('');
	$('#add_rate_form').hide();
	$('.labor_popup_form').hide();
}


function saveRateCharge(bId){
$('#save_rate').attr('disabled',true);
parent.CheckForSessionpop(baseUrl);
	if(bId!=''){
		//alert('hello');
		var submit_flag=true;
		$('#save_rate').attr('disabled','disabled');
		var rate_name = $('#rate_name').val();
		var description = $('#description').val();
		//alert('HELLO pop');
		var status = $('#status').val();
		var multiplier = $('#multiplier').val();
		var global_template = $('#global_template').val();
		var import_template = $('#import_template').val();
		var set_default = $('#set_default').val();
		
		
		if(rate_name==''){			
			$('#rerror_name').html('Please enter Rate Name.');
			submit_flag = false;
		}else{
			$('#rerror_name').html('');
		}
        
        if(description==''){			
			$('#rerror_desc').html('Please enter description.');
			submit_flag = false;
		}else{
			$('#rerror_desc').html('');
		}
        
        if(multiplier==''){			
			$('#rerror_multiplier').html('Please enter Multiplier.');
			submit_flag = false;
		}else{
			$('#rerror_multiplier').html('');
		}
         
         if(submit_flag==false){			 
			$('#save_rate').attr('disabled',false);
			return false; 
		 }else{
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"bill/saveratecharge",
						type        : "post",
						datatype    : 'json',
						data        : {
							            rate_name:rate_name,description:description,status:status,
							            building:bId,multiplier:multiplier,global_template:global_template,
							            set_default:set_default,import_template:import_template
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
								$('#save_rate').attr('disabled',false);
							}
						},
						error       : function(){
							$('.loader').hide();
							jAlert('There was an error', 'Vision Work Orders');
						}  
						
					 });
		 }
        
        
	}else{
		jAlert('Error Occurred', 'Vision Work Orders');
		return false;
	}
}

function deleteRateCharge(brid){
	var check_delete='YES';	
		jPrompt('For Deleting Rate Charge, Enter Yes in Capital letters.', '', 'Vision Work Orders', function(return_value) {
	    if(return_value!=null){	
				if(check_delete === return_value){				
				$('.loader').show();
				$.ajax({        
					type: "POST",
					url: baseUrl + "bill/deleteratecharge",
					dataType:'json',
					data: {
						brid: brid		
					},					
					success: function (msg) {
						$('.loader').hide();						
						if(msg==true){
							$('#bill_success').html('Rate Charge has been deleted successfully.');								
							location.reload();							
						}else{
							$('#bill_error').html('Error Occurred during deletion of rate charge.');
						}
					}

				});
			}else{
				jAlert('You have entered wrong word.', 'Vision Work Orders');
			}
		}
	});	
}
function showEditRate(brid,bId){
	if(bId!=''&& brid!=''){
		$('.loader').show();
		$.ajax({
				url         : baseUrl+"bill/editrate",
				type        : "post",
				datatype    : 'json',
				data        : {bId:bId,brid:brid},
				success     : function( data ) {
					$('.loader').hide(); 
					//$('#edit_wo_form').show();
					$('#edit_rate_form').html(data);
					//$('#edit_rate_form').show();
					//$('.labor_popup_form').show();
					$('#edit_rate_form_href').trigger('click');

				},
				error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');
				}  
				
			 });
	 }
}     


function cancelEditRate(){
	$("div.fancybox-close").trigger("click");
	$('#edit_rate_form').html('');
	$('#edit_rate_form').hide();
	$('.labor_popup_form').hide();
}


function updateRateCharge(brid,bId){
	parent.CheckForSessionpop(baseUrl);
	if(brid!='' && bId!=''){
		//alert('hello');
		var submit_flag=true;
		$('#save_rate').attr('disabled','disabled');
		var rate_name = $('#rate_name').val();
		var description = $('#description').val();
		//alert('HELLO pop');
		var status = $('#status').val();
		var multiplier = $('#multiplier').val();		
		var set_default = $('#set_default').val();
		var global_template = $('#global_template').val();
		
		
		if(rate_name==''){			
			$('#rerror_name').html('Please enter Rate Name.');
			submit_flag = false;
		}else{
			$('#rerror_name').html('');
		}
        
        if(description==''){			
			$('#rerror_desc').html('Please enter description.');
			submit_flag = false;
		}else{
			$('#rerror_desc').html('');
		}
        
        if(multiplier==''){			
			$('#rerror_multiplier').html('Please enter Multiplier.');
			submit_flag = false;
		}else{
			$('#rerror_multiplier').html('');
		}
         
         if(submit_flag==false){			 
			$('#save_rate').attr('disabled',false);
			return false; 
		 }else{
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"bill/updateratecharge",
						type        : "post",
						datatype    : 'json',
						data        : {
							            rate_name:rate_name,description:description,status:status,
							            multiplier:multiplier,set_default:set_default,brid:brid,building:bId,global_template:global_template
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
								$('#save_rate').attr('disabled',false);
							}
						},
						error       : function(){
							$('.loader').hide();
							setInterval(function(){ jAlert('There was an error', 'Vision Work Orders'); }, 1000);
							
						}  
						
					 });
		 }
        
        
	}else{
		jAlert('Error Occurred', 'Vision Work Orders');
		return false;
	}
}

function importRateTemplate(bId){
	$('.loader').show();
	$.ajax({
			url         : baseUrl+"bill/ratetemplate",
			type        : "post",
			datatype    : 'json',
			data        : {bId:bId},			
			success     : function( data ) {
				$('.loader').hide(); 
				//$('#edit_wo_form').show();
				$('#rate_template_form').html(data);
				//$('#rate_template_form').show();
				//$('.labor_popup_form').show();
				$('#rate_template_form_href').trigger('click');
			},
			error       : function(){
				$('.loader').hide();
				jAlert('There was an error', 'Vision Work Orders');
			}  
			
		 });
}

function cancelRateTemplate(){
	$("div.fancybox-close").trigger("click");
	$('#rate_template_form').html('');
	$('#rate_template_form').hide();
	$('.labor_popup_form').hide();
} 


function setRateTemplate(brid){
	$('.loader').show();
		$.ajax({
				url         : baseUrl+"bill/loadratetemplate",
				type        : "post",
				datatype    : 'json',
				data        : {brid:brid},
				success     : function( data ) {
					$('.loader').hide(); 
					//$('#edit_wo_form').show();
					$('#load_global_tmp_div').html(data);					
				},
				error       : function(){
					$('.loader').hide();
					jAlert('There was an error', 'Vision Work Orders');
				}  
				
			 });
}
$(function() {
	$(".modalbox").fancybox({'openEffect': 'none',fitToView: true});
});