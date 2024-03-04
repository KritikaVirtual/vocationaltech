function cancelForm(id){
	location.hash = '';
	$('#buiildingform_tr').hide();
	$("#combuilding_popup").hide();
	showBuildingList(id,'open');
	return false;
}
function editBuilding(cust_id,build_id){
    var height = $(".container-right").height();	
	var tot_height = parseInt(parseInt(height)+560);
	
	$.ajax({        
		type: "POST",
		url: baseUrl+"building/editbuilding",
		data: {
			buildingID:build_id,
		},
		beforeSend: function () {
			 //$('.loader').show();
		},
		success: function (msg) {
			$("#combuilding_popup").css('height',tot_height+'px');
	        $("#combuilding_popup").show();	
			$('.buiildingform_tr').show();			
			$('#showForm-'+cust_id).html(msg);			
			location.hash = '#addNewBuilding';
			 return false;
		},
		error: function(){
			alert('error');
		}
	});

}
function deleteBuilding(id)
{
var cid =id;
if(confirm('Are you sure you want to delete this Building?'))
	{
		$.ajax({        
			type: "POST",
			url: baseUrl + "building/deletebuilding",
			dataType:'json',
			data: {
				buildID: cid
			},
			beforeSend: function () {
				 //$('.loader').show();
			},
			success: function (msg) {

				if(msg.status=='error')
				{
					alert(msg.message);
					return false;
				}else if(msg.status=='success')
				{
					$('#build-'+id).remove();
					$('#msg').html('Selected building has been deleted successfully.');
				}
			}

		});
	}
}
function showBuildingList(id,open){
	
	//location.hash = '';
	var className = $('#open_div_'+id).attr('class');
	//alert('hello'+className);
	$('.buildingContainer').each(function(){
		var eId= $(this).attr('id');
		var sId = eId.split('-');
		//alert(sId[1]);
		var runId = sId[1];
		if(runId!=id){						
			$('#open_div_'+runId).addClass("open_plus");
			$('#open_div_'+runId).removeClass("open_close");
			$(this).hide();
		}
	});	
	if(className == 'open_plus' || open=='open'){
		$('#open_div_'+id).addClass("open_close");
		$('#open_div_'+id).removeClass("open_plus");		
		$('.loader').show();
		$.ajax({        
			type: "POST",
			url: baseUrl+"building/showbuildinglist",
			data: {
				companyID: id,
			},			
			success: function (msg) {			
				$('.loader').hide();
				$("#combuilding_popup").hide();
				$('#buildingContainer-'+id).show();
				$('#building_list_'+id).html(msg);
				$('.buiildingform_tr').hide();	        
				$('#showForm-'+id).html('');			
			}

		});
	}else{
		$('#open_div_'+id).addClass("open_plus");
		$('#open_div_'+id).removeClass("open_close");
		$('#buildingContainer-'+id).hide();
		$('#building_list_'+id).html('');
		return false;
	}
	//$('#'+id).addClass("open");
   //$('#'+id).removeClass("close");
	
}

function showBuildingForm(o){
	var id = $(o).attr('id');
		//	alert(id);
	var height = $(".container-right").height();	
	var tot_height = parseInt(parseInt(height)+560);	
	$.ajax({        
		type: "POST",
		url: baseUrl+"building/showbuildingform",
		data: {
			companyID: id,
		},
		beforeSend: function () {
			 $('.loader').show();
		},
		success: function (msg) {
			$('.loader').hide();
	//		$('.divOverlay').css("display","block");	        	       
	        $("#combuilding_popup").css('height',tot_height+'px');
	        $("#combuilding_popup").show();	
	        $('.buiildingform_tr').show();	        
			$('#showForm-'+id).html(msg);
			var url=document.URL.split("#");
			if(url[1]!='')
			 location.hash = '#addNewBuilding';
		}

	});
}
function addNewBuilding(){
	$('.loader').show();
	$("#confirm").attr('disabled',true);
	var checkComp = false;
	var buildingName = $('#buildingName').val();
	//alert('cname'+cname);
	if(buildingName!=''){		
		$.ajax({
				type: "POST",
				url: baseUrl+'building/checkbuilding',
				data: {buildingName: buildingName },				
				success: function (msg) {					
					$('.loader').hide();					
						if(msg == 'true'){
							$("#confirm").attr('disabled',false);
							$('.buildingNameErr').html("Building Name already in use."); 
							$('#buildingName').addClass('inputErr');
							$('#buildingName').focus();						
							return false;
												
						}else{
							$('.buildingNameErr').html(""); 
							$('#buildingName').removeClass('inputErr');							
							saveNewBuilding();	
						} 			
				}
			  });
          
    }else{
		$('.loader').hide();
		$("#confirm").attr('disabled',false);
		$('.buildingNameErr').html("Building Name Required."); 
		$('#buildingName').addClass('inputErr');
		$('#buildingName').focus();
		return false;
	}
         
}
function saveNewBuilding(){
//console.log('this is test');
   $("#confirm").attr('disabled',true);
	var companyID = $.trim($('#companyID').val());
	var accountNumber = $.trim($('#accountNumber').val());
	var costCenter = $.trim($('#costCenter').val());
	var buildingName = $.trim($('#buildingName').val());
	var address = $.trim($('#address').val());
	var address2 = $.trim($('#address2').val());
	var city = $.trim($('#city').val());
	var state = $.trim($('#state').val());
	var postalCode = $.trim($('#postalCode').val());
	var phoneNumber = $.trim($('#phoneNumber').val());
	var phoneExt = $.trim($('#ext').val());
	var faxNumber = $.trim($('#faxNumber').val());
	var status = $('#status').val();
	var temp = 0;
	if(accountNumber == ''){
		$('#companyID').focus();		
	}
	if(costCenter == ''){
		$('#costCenter').focus();
		$('#costCenter').addClass("inputErr");
		$('.costCenterErr').html('Cost Center Required');		
		temp = 1;
	}else{
		$('#costCenter').removeClass("inputErr");
		$('.costCenterErr').html('');		
	}
	 if(buildingName == ''){
		$('#buildingName').focus();
		$('#buildingName').addClass("inputErr");
		$('.buildingNameErr').html('Building Name Required');
		temp = 1;		
	}else{
		$('#buildingName').removeClass("inputErr");
		$('.buildingNameErr').html('');		
	}
	
	 if(address == ''){
		$('#address').focus();
		$('#address').addClass("inputErr");
		$('.addressErr').html('Address Required');
		temp = 1;		
	}else{
		$('#address').removeClass("inputErr");
		$('.addressErr').html('');		
	}
	 if(city == ''){
		$('#city').focus();
		$('#city').addClass("inputErr");
		$('.cityErr').html('City Required');
		temp = 1;
		
	}else{
		$('#city').removeClass("inputErr");
		$('.cityErr').html('');		
	}
	 if(state == ''){
		$('#state').focus();
		$('#state').addClass("inputErr");
		$('.stateErr').html('State Required');
		temp = 1;		
	}else{
		$('#state').removeClass("inputErr");
		$('.stateErr').html('');		
	}
	 if(postalCode == '' ){
		$('#postalCode').focus();
		$('#postalCode').addClass("inputErr");
		$('.postalCodeErr').html('Postal Code Required');
		temp = 1;		
	}else{
		$('#postalCode').removeClass("inputErr");
		$('.postalCodeErr').html('');		
	}
	 if(phoneNumber == ''){
		$('#phoneNumber').focus();
		$('#phoneNumber').addClass("inputErr");
		$('.phoneNumberErr').html('Phone Number Required');
		temp = 1;
		
	}else{
		$('#phoneNumber').removeClass("inputErr");
		$('.phoneNumberErr').html('');		
	}
	
	 if(faxNumber == ''){
		$('#faxNumber').focus();
		$('#faxNumber').addClass("inputErr");
		$('.faxNumberErr').html('Fax Number Required');
		temp = 1;		
	}else{
		$('#faxNumber').removeClass("inputErr");
		$('.faxNumberErr').html('');		
	}
	
	if(temp == 1){
		$("#confirm").attr('disabled',false);
		return false;
		
	}else{		
		$('.loader').show();
		$.ajax({        
			type: "POST",
			url: baseUrl+"building/addbuilding",
			data: {
				companyID:companyID,
				accountNumber: accountNumber,
				costCenter:costCenter,
				buildingName:buildingName,
				address:address,
				address2:address2,
				city:city,
				state:state,
				postalCode:postalCode,
				phoneNumber:phoneNumber,
				phoneExt:phoneExt,
				faxNumber:faxNumber,
				status:status
			},
			beforeSend: function () {
				 //$('.loader').show();
			},
			success: function (msg) {
				//alert(msg);
				$('#msg').html(msg);
			//	window.location.reload();
			showBuildingList(companyID,'open');
			},
			error: function(){
				alert('error');
			}
		});
		return false;
	}
}
function updateBuilding(){
//console.log('this is test');
	var buildingID = $.trim($('#buildingID').val());
	var companyID = $.trim($('#companyID').val());
	var accountNumber = $.trim($('#accountNumber').val());
	var costCenter = $.trim($('#costCenter').val());
	var buildingName = $.trim($('#buildingName').val());
	var address = $.trim($('#address').val());
	var address2 = $.trim($('#address2').val());
	var city = $.trim($('#city').val());
	var state = $.trim($('#state').val());
	var postalCode = $.trim($('#postalCode').val());
	var phoneNumber = $.trim($('#phoneNumber').val());
	var phoneExt = $.trim($('#ext').val());	
	var faxNumber = $.trim($('#faxNumber').val());
	var timezone = $.trim($('#timezone').val());
	var status = $('#status').val();
	var temp = 0;
	if(accountNumber == ''){
	//	$('#companyID').focus();
	//	return false;
	}
	
    if(costCenter == ''){
		$('#costCenter').focus();
		$('#costCenter').addClass("inputErr");
		$('.costCenterErr').html('Cost Center Required');
		return false;
		temp = 1;
	}else{
		$('#costCenter').removeClass("inputErr");
		$('.costCenterErr').html('');		
	}
	 if(buildingName == ''){
		$('#buildingName').focus();
		$('#buildingName').addClass("inputErr");
		$('.buildingNameErr').html('Building Name Required');
		temp = 1;
		return false;
	}else{
		$('#buildingName').removeClass("inputErr");
		$('.buildingNameErr').html('');		
	}
	
	 if(address == ''){
		$('#address').focus();
		$('#address').addClass("inputErr");
		$('.addressErr').html('Address Required');
		temp = 1;
		return false;
	}else{
		$('#address').removeClass("inputErr");
		$('.addressErr').html('');		
	}
	 if(city == ''){
		$('#city').focus();
		$('#city').addClass("inputErr");
		$('.cityErr').html('City Required');
		temp = 1;
		return false;
	}else{
		$('#city').removeClass("inputErr");
		$('.cityErr').html('');		
	}
	 if(state == ''){
		$('#state').focus();
		$('#state').addClass("inputErr");
		$('.stateErr').html('State Required');
		temp = 1;
		return false;
	}else{
		$('#state').removeClass("inputErr");
		$('.stateErr').html('');		
	}
	 if(postalCode == '' ){
		$('#postalCode').focus();
		$('#postalCode').addClass("inputErr");
		$('.postalCodeErr').html('Postal Code Required');
		temp = 1;
		return false;
	}else{
		$('#postalCode').removeClass("inputErr");
		$('.postalCodeErr').html('');		
	}
	 if(phoneNumber == ''){
		$('#phoneNumber').focus();
		$('#phoneNumber').addClass("inputErr");
		$('.phoneNumberErr').html('Phone Number Required');
		temp = 1;
		return false;
	}else{
		$('#phoneNumber').removeClass("inputErr");
		$('.phoneNumberErr').html('');		
	}
	
	/* if(faxNumber == ''){
		$('#faxNumber').focus();
		$('#faxNumber').addClass("inputErr");
		$('.faxNumberErr').html('Fax Number Required');
		temp = 1;
		return false;
	}else{
		$('#faxNumber').removeClass("inputErr");
		$('.faxNumberErr').html('');		
	}*/
	
	if(temp == 1){
		return false;
	}else{
		$.ajax({        
			type: "POST",
			url: baseUrl+"building/updatebuilding",
			dataType:'json',
			data: {
				buildingID:buildingID,
				companyID:companyID,
				accountNumber: accountNumber,
				costCenter:costCenter,
				buildingName:buildingName,
				address:address,
				address2:address2,
				city:city,
				state:state,
				postalCode:postalCode,
				phoneNumber:phoneNumber,
				phoneExt:phoneExt,
				faxNumber:faxNumber,
				timezone:timezone,
				status:status
			},
			beforeSend: function () {
				 $('.loader').show();
			},
			success: function (msg) {
				$('.loader').hide();
				if(msg.status=='success'){
				  $('#msg').html(msg.message);
				   showBuildingList(companyID,'open');
			     }else if(msg.status=='build_error'){
					 $('#buildingName').focus();
					 $('#buildingName').addClass("inputErr");
					 $('.buildingNameErr').html(msg.message);
				 }else{
					 $('#msg').html(msg.message);
				 }
			     
				//alert(msg);
			//	window.location.reload();
			
			},
			error: function(){
				alert('error');
			}
		});
		return false;
	}
}
function isNumberKey(evt)
{
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 46 && charCode > 31
	&& (charCode < 48 || charCode > 57))
	return false;
	 
	return true;
}
function numValidate(obj){
	
//	var id =$(obj).attr('id');
	var inputVal = $(obj).val();
    var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
    if(!numericReg.test(inputVal)) {
		$(obj).focus();
		$(obj).val(
			function(index,value){
				return value.substr(0,value.length-1);
			}
		);
		alert("Enter Only Digits");
	}
		   return false;
}
