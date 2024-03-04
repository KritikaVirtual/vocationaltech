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
	var date_requested = $('#date_requested').val();
	var hour = $('#hour').val();
	var minute = $('#minute').val();
	var priority = $('#priority').val();
	var category = $('#category').val();
	var work_order_request = $('#work_order_request').val();
	
	
	if(date_requested==''){
		$('#message').html('Requested Date Required');
		$('#date_requested').addClass("inputErr");
		return false;
	}else{
		$('#message').html('');
		$('#date_requested').removeClass("inputErr");
	}
	
	if(hour=='' || minute==''){
		$('#message').html('Requested Time Required');
		$('#hour').addClass("inputErr");		
		return false;
	}else{
		$('#message').html('');
		$('#hour').removeClass("inputErr");		
	}
	
	if(priority==''){
		$('#message').html('Select Priority');
		$('#priority').addClass("inputErr");		
		return false;
	}else{
		$('#message').html('');
		$('#priority').removeClass("inputErr");		
	}
	
	if(category==''){
		$('#message').html('Select Category');
		$('#category').addClass("inputErr");		
		return false;
	}else{
		$('#message').html('');
		$('#category').removeClass("inputErr");		
	}
	
	if(work_order_request==''){
		$('#message').html('Fill Work Order Request');
		$('#work_order_request').addClass("inputErr");
		return false;
	}else{
		$('#message').html('');
		$('#work_order_request').removeClass("inputErr");
		return true;
	}
}

function cancelUser(){	
    window.location.href = baseUrl+'workorder/index';
}
