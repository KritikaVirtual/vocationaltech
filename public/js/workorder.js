function showWorkOrder(buildId){
	$('.loader').show();
	if(buildId!=''){
		$.ajax({        
			type: "POST",
			url: baseUrl+"workorder/ajaxworkorder",
			data: {
				buildId: buildId,
			},
			success: function (content) {			
				$('.loader').hide();			        
				$('#work_order_field').html(content);			
			}

		});
	}else{
		$('.loader').hide();			        
		$('#work_order_field').html('');	
	}
}

function checkFormValidation(){
	var tenant_name = $('#tenant_name').val();
	var suite_location = $('#suite_location').val();
	var email_id = $('#email_id').val();
	var date_requested = $('#date_requested').val();
	var hour = $('#hour').val();
	var minute = $('#minute').val();
	var priority = $('#priority').val();
	var category = $('#category').val();
	var work_order_request = $('#work_order_request').val();
	var create_user = $('#create_user').val();
	var file_val = $('#wo_file').val();
	var file_val_type = $('#wo_file').val().toLowerCase();
    var regex = new RegExp("(.*?)\.(pdf|png|jpg|jpeg|gif)$");
	if(tenant_name==''){
		$('#message').html('Tenant Name Required');
		$('#tenant_name').addClass("inputErr");
		return false;
	}else{
		$('#message').html('');
		$('#tenant_name').removeClass("inputErr");
	}
	
	if(suite_location==''){
		$('#message').html('Suite Required');
		$('#suite_location').addClass("inputErr");
		return false;
	}else{
		$('#message').html('');
		$('#suite_location').removeClass("inputErr");
	}
	
	if(email_id==''){
		$('#message').html('Email Required');
		$('#email_id').addClass("inputErr");
		return false;
	}else{
		$('#message').html('');
		$('#email_id').removeClass("inputErr");
	}
	
	if(date_requested==''){
		$('#date_error').html('Requested Date Required');
		$('#date_requested').addClass("inputErr");
		return false;
	}else{
		$('#date_error').html('');
		$('#date_requested').removeClass("inputErr");
	}
	
	if(hour=='' || minute==''){
		$('#time_error').html('Requested Time Required');
		$('#hour').addClass("inputErr");		
		return false;
	}else{
		$('#time_error').html('');
		$('#hour').removeClass("inputErr");		
	}
	
	if(create_user==''){
		$('#requested_error').html('Select Requested by');
		$('#create_user').addClass("inputErr");		
		return false;
	}else{
		$('#requested_error').html('');
		$('#create_user').removeClass("inputErr");		
	}
	if(category==''){
		$('#category_error').html('Select Category');
		$('#category').addClass("inputErr");		
		return false;
	}else{
		$('#category_error').html('');
		$('#category').removeClass("inputErr");		
	}
	
	if(work_order_request==''){
		$('#work_order_error').html('Fill Work Order Request');
		$('#work_order_request').addClass("inputErr");
		return false;
	}else{
		$('#work_order_error').html('');
		$('#work_order_request').removeClass("inputErr");
		//return true;
	}
	
	if(file_val!=''){
		//var logo_file = $('#wo_file')[0].files[0];
		var logo_file=document.querySelector("input[type='file']"); 
		var file_size = logo_file.files[0].size;
		/*var file_name = logo_file.name;
		var dotIndex = file_name.lastIndexOf('.');
        var ext = $('#company_logo').val().split('.').pop().toLowerCase();
        var validFileExtensions = ["jpg", "jpeg", "gif", "png"];
		if( $.inArray( ext, validFileExtensions ) == -1){			
			$('#logoErr').html('Upload logo only in jpg, png or gif format.');
			file_action2=0;
			return false;
		}*/
		
		if(file_size >(1024*1024*5)){			
			$('#file_error').html('File size must be less than 5MB.');
			file_action2 =1;
			return false;
		}
		
		if(!(regex.exec(file_val_type))) {
            $('#file_error').html('Please select correct file format.');
		    return false;
        }
		else
		 return true;
	}else{
		return true;
	}
}

function cancelUser(){	
    window.location.href = baseUrl+'workorder/index';
}
