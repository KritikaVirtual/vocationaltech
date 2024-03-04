function showAddService(bId){
	if(bId!=''){
		$('.loader').show();
		$.ajax({
				url         : baseUrl+"buildservice/addservice",
				type        : "post",
				datatype    : 'json',
				data        : {bId:bId},
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


function saveBuildService(bId){
	parent.CheckForSessionpop(baseUrl);
	if(bId!=''){
		//alert('hello');
		var submit_flag=true;
		$('#save_service').attr('disabled','disabled');
		
		var service_name = $('#service_name').val();
		var unit_measure = $('#unit_measure').val();				
		var cost = $('#cost').val();
		var minimum = $('#minimum').val();
		var global_template = $('#global_template').val();
		var import_template = $('#import_template').val();
		var status = $('#status').val();
		
		
		if(service_name==''){			
			$('#serror_name').html('Please enter Service Name.');
			submit_flag = false;
		}else{
			$('#serror_name').html('');
		}
        
        if(unit_measure==''){			
			$('#serror_unit').html('Please enter Unit of Measure.');
			submit_flag = false;
		}else{
			$('#serror_unit').html('');
		}
        
        if(cost==''){			
			$('#serror_cost').html('Please enter Cost.');
			submit_flag = false;
		}else{
			$('#serror_cost').html('');
		}
		
		if(minimum==''){			
			$('#serror_minimum').html('Please enter Minimum.');
			submit_flag = false;
		}else{
			$('#serror_minimum').html('');
		}
         
         if(submit_flag==false){			 
			$('#save_service').attr('disabled',false);
			return false; 
		 }else{
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"buildservice/saveservice",
						type        : "post",
						datatype    : 'json',
						data        : {
							            service_name:service_name,unit_measure:unit_measure,status:status,
							            building:bId,cost:cost,global_template:global_template,
							            minimum:minimum,import_template:import_template
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
								$('#save_service').attr('disabled',false);
							}
						},
						error       : function(){
							$('.loader').hide();
							alert('There was an error');
						}  
						
					 });
		 }
        
        
	}else{
		alert('Error Occurred');
		return false;
	}
}

function deleteService(bsid){
	var check_delete='YES';
	//var con = confirm("Do you really want to delete category?");
	jPrompt('For Deleting Building Service, Enter Yes in Capital letters.', '', 'Vision Work Orders', function(return_value) {
	    if(return_value!=null){	
				if(check_delete === return_value){
				//var building_id = $('#building_id').val();
				$('.loader').show();
				$.ajax({        
					type: "POST",
					url: baseUrl + "buildservice/deletebservice",
					dataType:'json',
					data: {
						bsid: bsid		
					},					
					success: function (msg) {
						$('.loader').hide();						
						if(msg==true){
							$('#service_success').html('Building Service has been deleted successfully.');								
							location.reload();							
						}else{
							$('#service_error').html('Error Occurred during deletion of building service.');
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
function showEditService(bsid,bId){
	if(bId!=''&& bsid!=''){
		$('.loader').show();
		$.ajax({
				url         : baseUrl+"buildservice/editservice",
				type        : "post",
				datatype    : 'json',
				data        : {bId:bId,bsid:bsid},
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


function updateBuildService(bsid,bId){
	parent.CheckForSessionpop(baseUrl);
	if(bsid!='' && bId!=''){
		//alert('hello');
		var submit_flag=true;
		$('#save_service').attr('disabled','disabled');
		
		var service_name = $('#service_name').val();
		var unit_measure = $('#unit_measure').val();				
		var cost = $('#cost').val();
		var minimum = $('#minimum').val();		
		var status = $('#status').val();
		var global_template = $('#global_template').val();
		
		
		if(service_name==''){			
			$('#serror_name').html('Please enter Service Name.');
			submit_flag = false;
		}else{
			$('#serror_name').html('');
		}
        
        if(unit_measure==''){			
			$('#serror_unit').html('Please enter Unit of Measure.');
			submit_flag = false;
		}else{
			$('#serror_unit').html('');
		}
        
        if(cost==''){			
			$('#serror_cost').html('Please enter Cost.');
			submit_flag = false;
		}else{
			$('#serror_cost').html('');
		}
		
		if(minimum==''){			
			$('#serror_minimum').html('Please enter Minimum.');
			submit_flag = false;
		}else{
			$('#serror_minimum').html('');
		}
         
         if(submit_flag==false){			 
			$('#save_service').attr('disabled',false);
			return false; 
		 }else{
			    $('.loader').show();
				$.ajax({
						url         : baseUrl+"buildservice/updateservice",
						type        : "post",
						datatype    : 'json',
						data        : {
							            service_name:service_name,unit_measure:unit_measure,status:status,
							            cost:cost,minimum:minimum,bsid:bsid,building:bId,global_template:global_template
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
});