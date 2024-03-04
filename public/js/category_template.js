function getCategory(cid) {
		$.ajax({        
			type: "POST",
			url: baseUrl + "category/getcategoryfortemplate",
			dataType:'json',
			data: {
				cid: cid
			},			
			success: function (data) {   
				
				
					$('#status_temp').val(data.status);
					$('#priority_temp option[value="0"]').remove(); 
					$("#priority_temp").append('<option value="'+data.prioritySchedule+'" selected>'+data.priorityName+'</option>');
					$('#user_to_list_temp').find('option').remove();
					if(data.userDetails!=undefined) {
					var count = Object.keys(data.userDetails).length;
						for(var i = 0; i <= count-1; i++) {
							$('#user_to_list_temp').append('<option value="'+data.userDetails[i].uid+'">'+data.userDetails[i].lastName+','+data.userDetails[i].firstName+'</option>');
						}
					}
					
					$('#send_to_list_temp').find('option').remove();
					if(data.groupDetails!=undefined) {
					var count = Object.keys(data.groupDetails).length;
						for(var i = 0; i <= count-1; i++) {
							$('#send_to_list_temp').append('<option value="'+data.groupDetails[i].id+'">'+data.groupDetails[i].group_name+'</option>');
						}
					}
					
					$('#tenant_to_list_temp').find('option').remove();
					if(data.tenantDetails!=undefined) {
					var count = Object.keys(data.tenantDetails).length;
						for(var i = 0; i <= count-1; i++) {
							$('#tenant_to_list_temp').append('<option value="'+data.tenantDetails[i].id+'">'+data.tenantDetails[i].tenantName+'</option>');
						}
					}
					if(data.visible_status == '1') { 
						$('#visible_div_temp').prop('checked', true);
					} else {
						$('#non_visible_div_temp').prop('checked', true);
					}
					//visible-div
				
				//$("#priority_temp").append('<option value="0" selected>'+data.priorityName+'</option>');
				//$('#priority_temp').val(data.priorityName);
				
			}

		});
}

function move_list_items(sourceid, destinationid)
{	
	var select_item = $("#"+sourceid+"  option:selected").val();	
	if(select_item == '' || select_item == null || select_item == undefined){
		jAlert('Please select item', 'Vision Work Orders');
		return false;
	}else{
       $("#"+sourceid+"  option:selected").appendTo("#"+destinationid);
    }
}




function importPriorityTemplate(){
	$('#saveCatImport').attr('disabled',true);
	parent.CheckForSessionpop(baseUrl);
	 var catName = $('#category_name_temp');
	 
        var prioritySchedule = $('#priority_temp');
        var isActive = $('#status_temp');
        var building_id = $('#building_id_temp').val();
		var global_template = $('#category_global_template_temp').prop('value'); 
        /*********Select all options ************/
        $('#tenant_to_list_temp option').prop('selected', true);
	    $('#send_to_list_temp option').prop('selected', true);
	    $('#user_to_list_temp option').prop('selected', true);
        var dataSet = new Object;
        dataSet.categoryName = catName.val();
		dataSet.categoryNameText = $('#category_name_temp option:selected').text(); 
		
        dataSet.prioritySchedule = prioritySchedule.val();
		dataSet.priorityScheduletext = $('#priority_temp option:selected').text(); 
        dataSet.status = isActive.val();
        dataSet.building_id = building_id;
		dataSet.global_template = global_template;
        var include_exclude = '';
        var send_email ='';
         var account_user ='';         
        if($('#tenant_to_list_temp').val()!='' && $('#tenant_to_list_temp').val()!= null)			
         include_exclude = ($('#tenant_to_list_temp').val()).join();	    
        dataSet.include_exclude = include_exclude;
        if($('#send_to_list_temp').val()!='' && $('#send_to_list_temp').val()!= null)
         send_email = ($('#send_to_list_temp').val()).join();
        dataSet.send_email = send_email;
        
         if($('#user_to_list_temp').val()!='' && $('#user_to_list_temp').val()!= null)
         account_user = ($('#user_to_list_temp').val()).join();
        dataSet.account_user = account_user;
        dataSet.visible_status = $('input[name="visible_status_temp"]:checked').val();

        if(!catName.val()){          
		  $('#category_name').addClass('inputErr');		 
		 $('#name_error').html('Please enter name');  
		 $('#category_name').focus();
		 $('#saveCatImport').attr('disabled',false);        
          return false;
        }
        else{
          $('#name_error').html('');
          $('#category_name').removeClass('inputErr');
        }
        
        $('.loader').show();
        $.ajax({
              url: baseUrl + "category/importcategory",
              dataType: "json",
              type: "post",
              data: dataSet,
              
              success: function( data ) {
               
			   setInterval(function(){ parent.location.reload();}, 1100);
              },
			  error: function(){
				jAlert('error', 'Vision Work Orders');
				setInterval(function(){ parent.location.reload(); }, 1500);
				
			}
          });
    }

