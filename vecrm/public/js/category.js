function showEditPriority(pid){
	$('.edit-priority').hide();
	$('.show-priority').show();
	$('#show_priority_id_'+pid).hide();
	$('#edit_priority_id_'+pid).show();
}

function hideEditPriority(pid){
	$('#edit_priority_id_'+pid).hide();
	$('#show_priority_id_'+pid).show();
}


function deletePriority(pid){
	//var priorityId =pid;
if(confirm('Are you sure, you want to delete the priority?'))
	{
		$('.loader').show();
		building_id= $('#building_id').val();
		$.ajax({        
			type: "POST",
			url: baseUrl + "category/deletepriority",
			dataType:'json',
			data: {
				pid: pid
			},			
			success: function (msg) {
                $('.loader').hide();
				if(msg.status=='error')
				{
					alert(msg.message);
					return false;
				}else if(msg.status=='success')
				{
					$('#show_priority_id_'+pid).remove();
					$('#edit_priority_id_'+pid).remove();
					showPriorityList(building_id);
					showCategoryList(building_id);
					//var msgId = 3;
					//filtercompany(msgId);
				}
			}

		});
	}
}

function  addPriority(){
	$('.loader').show();
	$('#save_priority').attr('disabled',true);
	var checkComp = false;
	var priorityName= $('#priority_name').val();
	var building_id= $('#building_id').val();
	//alert('cname'+cname);
	if(priorityName!=''){		
		$.ajax({
				type: "POST",
				url: baseUrl+'category/checkpriority',
				data: {priorityName: priorityName,
					building_id:building_id  },				
				success: function (msg) {										
					$('.loader').hide();										
						if(msg == 'true'){
							$("#save_priority").attr('disabled',false);
							$("#priority-name-error").html("Priority Name already in use.");
		                    $('#priority_name').addClass('inputErr');
							$('#priority_name').focus();						
							return false;
												
						}else{
							$('#priority-name-error').html(""); 
							$('#priority_name').removeClass('inputErr');							
							savePriority();	
						} 			
				}
			  });
          
    }else{
		$('.loader').hide();
		$("#save_priority").attr('disabled',false);
		$("#priority-name-error").html("Please enter priority name");
		$('#priority_name').addClass('inputErr');
		$('#priority_name').focus();
		return false;
	}
}

function showPriorityForm(){
	var height = $("#priority_info").height();	
	var tot_height = parseInt(parseInt(height)+215)
	$("#priority_popup").css('height',tot_height+'px');	 
	$("#priority_popup").show();
	$("#priority-name-error").html("");
	$('#priority_name').removeClass('inputErr');
	$("#priority-description-error").html("");
	$('#priority_description').removeClass('inputErr');
	$('#priority_name').val('');
	$('#priority_description').val('');
	$('#add-priority-td').show();
}
function hidePriorityForm(){
	$('#add-priority-td').hide();
	$("#priority_popup").hide();
}


function savePriority(){
	$('#save_priority').attr('disabled',true);
	var priorityName= $('#priority_name').val();
	var priorityDescription= $('#priority_description').val();
	var status = $('#status').val();	
	var valid_true = true;
	if(priorityName==''){
		$("#priority-name-error").html("Please enter priority name");
		$('#priority_name').addClass('inputErr');
		valid_true = false;
	}else{
		$("#priority-name-error").html("");
		$('#priority_name').removeClass('inputErr');
	}
	if(priorityDescription==''){
		$("#priority-description-error").html("Please enter priority description");
		$('#priority_description').addClass('inputErr');
		valid_true = false;
	}else{
		$("#priority-description-error").html("");
		$('#priority_description').removeClass('inputErr');
	}
	
	if(valid_true==false){
		$('#save_priority').attr('disabled',false);
		return false;
	}else{
		$('.loader').show();
		building_id= $('#building_id').val();
		$.ajax({        
			type: "POST",
			url: baseUrl + "category/savepriority",
			dataType:'json',
			data: {
				priorityName: priorityName,
				priorityDescription: priorityDescription,
				status: status,
				building_id: building_id
			},			
			success: function (msg) {                 
				if(msg.status=='error')
				{
				$('.message').html(msg.message);
				$('.loader').hide();	
					
				}else if(msg.status=='success')
				{
					$('.message').html(msg.message);
					showPriorityList(building_id);					
				}
			}

		});
    }
}

function editPriority(pid){
	var priority_name = $('#priority_name_'+pid).val();
	var priority_description = $('#priority_description_'+pid).val();
	var priority_status = $('#status_'+pid).val();
	var buildingId= $('#building_id').val();	
	if(priority_name==''){
		alert('Please enter the priority name');
		return false;
	}
	
	if(priority_description==''){
		alert('Please enter the priority description.');
		return false;
	}else{
		$('.loader').show();
		$.ajax({        
			type: "POST",
			url: baseUrl + "category/editpriority",
			dataType:'json',
			data: {
				priorityName: priority_name,
				priorityDescription: priority_description,
				status: priority_status,
				pid: pid,
				building_id:buildingId				
			},
			beforeSend: function () {
				 //$('.loader').show();
			},
			success: function (msg) {
                $('.loader').hide();
				if(msg.status=='error')
				{
					
					
				}else if(msg.status=='success')
				{
					$('.message').html(msg.message);
					showPriorityList(buildingId);									
					
				}else if(msg.status=='priority_error'){
					alert(msg.message);
				}
			}

		});
	}
}

function showPriorityList(buildingId,order){
	var order = (typeof order === "undefined") ? "default" : order;
	if(buildingId!=''){
		var page = $('#priority_page').val();
		$('.loader').show();
		$.ajax({        
			type: "POST",
			url: baseUrl + "category/showprioritylist",
			dataType:'json',
			data: {
				buildingId: buildingId,
				page:page,
				order:order												
			},			
			success: function (response) {						
			   $('.loader').hide();
			   $("#priority_popup").hide();
               $('#priority_info').html(response.content); 
				
			}

		});
	}else{
		alert('No Building selected');
	}
}

function priorityPagination(page){
	$('#priority_page').val(page);
	var building_id= $('#building_id').val();	
	showPriorityList(building_id);
}

function categoryPagination(page){
	$('#category_page').val(page);
	var building_id= $('#building_id').val();	
	showCategoryList(building_id);
}

function showPriorityScheduleList(pid,open,order){	
	var className = $('#open_div_'+pid).attr('class');
	var order = (typeof order === "undefined") ? "default" : order;
	$('.show-tr-schlist').each(function(){
		var eId= $(this).attr('id');
		var sId = eId.split('-');
		//alert(sId[1]);
		var runId = sId[1];
		if(runId!=pid){						
			$('#open_div_'+runId).addClass("open_plus");
			$('#open_div_'+runId).removeClass("open_close");
			$('#show_priority_schedule_'+runId).html('');
			$(this).hide();
		}
	});	
	if(className == 'open_plus' || open=='open'){
		$('#open_div_'+pid).addClass("open_close");
		$('#open_div_'+pid).removeClass("open_plus");
		
	}else{
		$('#open_div_'+pid).addClass("open_plus");
		$('#open_div_'+pid).removeClass("open_close");
		$('#show_priority_schedule_'+pid).html('');
		$('#tr_schecdule-'+pid).hide();
		return false;
	}
	if(pid!=''){
		$('.loader').show();
		$.ajax({        
			type: "POST",
			url: baseUrl + "category/showpriorityschedulelist",			
			data: {
				pid: pid,
				order:order								
			},			
			success: function (response) {						
			   $('.loader').hide();
			   $("#priority_popup").hide();
               $('#show_priority_schedule_'+pid).html(response);
               $('#tr_schecdule-'+pid).show(); 
				
			}

		});
	}else{
		alert('No Building selected');
	}
}


function showEditSchedule(sid){
	$('.edit-schedule').hide();
	$('.show-schedule').show();
	$('#show_schedule_id_'+sid).hide();
	$('#edit_schedule_id_'+sid).show();
}

function hideEditSchedule(sid){
	$('#edit_schedule_id_'+sid).hide();
	$('#show_schedule_id_'+sid).show();
}

function showScheduleForm(){
	var height = $("#priority_info").height();	
	var tot_height = parseInt(parseInt(height)+350)
	$("#priority_popup").css('height',tot_height+'px');	
	$("#end-status-error").html("");
	$("#time-error").html("");
	$('#Time').removeClass('inputErr');
	$('#Time').val('');
	$("#priority_popup").show();
	$('#add-schedule-td').show();
}


function hideScheduleForm(){
	$("#priority_popup").hide();
	$('#add-schedule-td').hide();
}

function saveSchedule(){
	$('#save_schedule').attr('disabled',true);
	var start_status= $('#start_status').val();
	var end_status= $('#end_status').val();
	var Time= $('#Time').val();
	var length= $('#length').val();
	var access_days= $('#access_days').val();
	var status = $('#sch_status').val();	
	var valid_true = true;
	if(start_status==end_status){
		$("#end-status-error").html("Status can not be same.");
		valid_true = false;
	}else{
		$("#end-status-error").html("");		
	}
	if(Time==''|| Time == 0){
		$("#time-error").html("Please enter the time.");
		$('#Time').addClass('inputErr');
		valid_true = false;
	}else{
		$("#time-error").html("");
		$('#Time').removeClass('inputErr');
	}
	
	if(valid_true==false){
		$('#save_schedule').attr('disabled',false);
		return false;
	}else{
		$('.loader').show();
		priority_id= $('#priority_id').val();
		$.ajax({        
			type: "POST",
			url: baseUrl + "category/saveschedule",
			dataType:'json',
			data: {
				start_status: start_status,
				end_status: end_status,
				Time: Time,
				length: length,
				access_days: access_days,
				status: status,
				priority_id: priority_id
			},			
			success: function (msg) {                 
				if(msg.status=='error')
				{
				$('.message').html(msg.message);
				$('.loader').hide();	
					
				}else if(msg.status=='success')
				{
					$('.message').html(msg.message);
					showPriorityScheduleList(priority_id,'open');					
				}
			}

		});
	}
	
}


function editSchedule(sId){	
	var start_status= $('#start_status_'+sId).val();
	var end_status= $('#end_status_'+sId).val();
	var Time= $('#Time_'+sId).val();
	var length= $('#length_'+sId).val();
	var access_days= $('#access_days_'+sId).val();
	var status = $('#sch_status_'+sId).val();	
	var valid_true = true;
	if(start_status==end_status){		
		alert('Start Status and End Status can not be same.');
		valid_true = false;
	}
	
	if(Time=='' || Time == 0){
		alert('Please enter the time.');
		$('#Time').addClass('inputErr');
		valid_true = false;
	}else{		
		$('#Time').removeClass('inputErr');
	}
	
	if(valid_true==false){		
		return false;
	}else{
		$('.loader').show();
		priority_id= $('#priority_id').val();
		$.ajax({        
			type: "POST",
			url: baseUrl + "category/editschedule",
			dataType:'json',
			data: {
				start_status: start_status,
				end_status: end_status,
				Time: Time,
				length: length,
				access_days: access_days,
				status: status,
				id: sId
			},			
			success: function (msg) {                 
				if(msg.status=='error')
				{
				$('.message').html(msg.message);
				$('.loader').hide();	
					
				}else if(msg.status=='success')
				{
					$('.message').html(msg.message);
					showPriorityScheduleList(priority_id,'open');					
				}
			}

		});
	}
	
}

function isNumberKey(evt)
{
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 46 && charCode > 31
	&& (charCode < 48 || charCode > 57)){		
	  return false;
  }else
	return true;
}

function deleteSchedule(sid){
	if(confirm('Are you sure, you want to delete the schedule?'))
	{
		$('.loader').show();
		priority_id= $('#priority_id').val();
		$.ajax({        
			type: "POST",
			url: baseUrl + "category/deleteschedule",
			dataType:'json',
			data: {
				id: sid
			},
			beforeSend: function () {
				 //$('.loader').show();
			},
			success: function (msg) {
                $('.loader').hide();
				if(msg.status=='error')
				{
					alert(msg.message);
					return false;
				}else if(msg.status=='success')
				{
					showPriorityScheduleList(priority_id,'open');
					
				}
			}

		});
	}
}


function showEditcategory(cid){
	/*$('.edit-category').hide();
	$('.show-category').show();
	$('#show_category_id_'+cid).hide();*/
	
	$('.loader').show();	
	var building_id = $('#building_id').val();
			$.ajax({        
			type: "POST",
			url: baseUrl + "category/editcategory",
			data: {			
			building_id : building_id,
			cid : cid,
		   },
			success: function (msg) {
				var height = $("#category_info").height();	
				var tot_height = parseInt(parseInt(height)+700)
				$("#category_popup").css('height',tot_height+'px');	 
				$("#category_popup").show();
				$('.loader').hide();
				$('#edit_category_id_'+cid).show();
			   $("#edit_cat_form_"+cid).html(msg);	
			   $("#open_cat_"+cid).hide();
			   $("#close_cat_"+cid).show();	
		},
			error: function(){
				alert('error');
			}
		});
}

function hideEditcategory(cid){
	$('#edit_category_id_'+cid).hide();
	$('#show_category_id_'+cid).show();
}

function showCategroyFrom(){
	$('.loader').show();	
	var building_id = $('#building_id').val();
			$.ajax({        
			type: "POST",
			url: baseUrl + "category/addcategory",
			data: {
			actionType : "addNew",
			building_id : building_id,
		} ,
			beforeSend: function () {                        
		},
			success: function (msg) {
				var height = $("#category_info").height();	
				var tot_height = parseInt(parseInt(height)+700)
				$("#category_popup").css('height',tot_height+'px');	 
				$("#category_popup").show();
				$('.loader').hide();
				$("#show_cat_tr").show();
			$("#add_cat_form").html(msg);
		//alert('sss');
		},
			error: function(){
				alert('error');
			}
		});
}

function cancelCategroyFrom(){
	$("#show_cat_tr").hide();
	$("#category_popup").hide();
	$("#add_cat_form").html('');
	$(".edit-category").hide();
	$(".open_plus").show();
	$(".open_close").hide();
	$(".edit-cat-td").html('');
}
function saveCategory(){
	 var catName = $('#category_name');
	 $('#saveCat').attr('disabled',true);
	 
        var prioritySchedule = $('#priority');
        var isActive = $('#status');
        var building_id = $('#building_id').val();
        /*********Select all options ************/
        $('#tenant_to_list option').prop('selected', true);
	    $('#send_to_list option').prop('selected', true);
	    $('#user_to_list option').prop('selected', true);
        var dataSet = new Object;
        dataSet.categoryName = catName.val();
        dataSet.prioritySchedule = prioritySchedule.val();
        dataSet.status = isActive.val();
        dataSet.building_id = building_id;
        var include_exclude = '';
        var send_email ='';
         var account_user ='';         
        if($('#tenant_to_list').val()!='' && $('#tenant_to_list').val()!= null)			
         include_exclude = ($('#tenant_to_list').val()).join();	    
        dataSet.include_exclude = include_exclude;
        if($('#send_to_list').val()!='' && $('#send_to_list').val()!= null)
         send_email = ($('#send_to_list').val()).join();
        dataSet.send_email = send_email;
        
         if($('#user_to_list').val()!='' && $('#user_to_list').val()!= null)
         account_user = ($('#user_to_list').val()).join();
        dataSet.account_user = account_user;
        dataSet.visible_status = $('input[name="visible_status"]:checked').val();

        if(!catName.val()){          
		  $('#category_name').addClass('inputErr');		 
		 $('#name_error').html('Please enter name');  
		 $('#saveCat').attr('disabled',false);        
          return false;
        }
        else{
          $('#name_error').html('');
          $('#category_name').removeClass('inputErr');
        }
        
        $('.loader').show();
        $.ajax({
              url: baseUrl + "category/createcat",
              dataType: "json",
              method: "post",
              data: dataSet,
              beforeSend: function( xhr ) {
              },
              success: function( data ) {
                $('.loader').hide();
                
                if(data == 3){
                  $('.loader').hide();
                  $('#name_error').html('Name is already exist.');
				  $('#saveCat').attr('disabled',false);
                    return false;
                }
                else if(data == true){
                  $('.loader').hide();
                  $('#name_error').text('');
                  $('#saveCat').attr('disabled',false);
                  showCategoryList(building_id);

                }
                else{
                  $('.loader').hide();
                  alert("We are unable to process you request this time. Please try later!");
                }
                $('#add_cat_form').html('');
              }
          });
}


function editCategory(cid){
	$('#tenant_to_list option').prop('selected', true);
	$('#send_to_list option').prop('selected', true);
	$('#user_to_list option').prop('selected', true);
	var include_exclude = '';
    var send_email ='';
    var account_user ='';
    if($('#tenant_to_list').val()!='' && $('#tenant_to_list').val()!= null)			
         include_exclude = ($('#tenant_to_list').val()).join(); 
        
   if($('#send_to_list').val()!='' && $('#send_to_list').val()!= null)
        send_email = ($('#send_to_list').val()).join();
        
   if($('#user_to_list').val()!='' && $('#user_to_list').val()!= null)
        account_user = ($('#user_to_list').val()).join();     
          
    var  visible_status = $('input[name="visible_status"]:checked').val();
	var building_id = $('#building_id').val();	 
		$('.loader').show();
		$.ajax({        
			type: "POST",
			url: baseUrl + "category/editcat",
			dataType:'json',
			data: {
				include_exclude: include_exclude,
				send_email: send_email,
				visible_status:visible_status,
				account_user:account_user,				
				cat_id: cid,								
			},			
			success: function (msg) {
				 if(msg == 3){
                  $('.loader').hide();
                  //$('#name-error').text('Name is already exist.');

                  alert('Name is already exist.');
                  
                }
                else if(msg == true){
                  $('.loader').hide();
                  $('#name-error').text('');
                  $('.message').html('Category has been updated successfully.');
                  showCategoryList(building_id);

                }
                else{
                  $('.loader').hide();
                  alert("We are unable to process you request this time. Please try later!");
                }
                $('#add_cat_form').html('');
			}

		});
	
}


function deleteCategory(cid){
	var check_delete='YES';
	//var con = confirm("Do you really want to delete category?");
	var return_value = prompt("For Deleting Category, Enter Yes in Capital letter.");
	    if(return_value!=null){	
				if(check_delete === return_value){
				var building_id = $('#building_id').val();
				$('.loader').show();
				$.ajax({        
					type: "POST",
					url: baseUrl + "category/deletecat",
					dataType:'json',
					data: {
						cat_id: cid		
					},
					beforeSend: function () {
					},
					success: function (msg) {
						if(msg){
							$('.message').html('Category has been deleted successfully.');
							showCategoryList(building_id);
								
							$('.loader').hide();
							$('#add_cat_form').html('');
						}
					}

				});
			}
			else{
				$('.error-txt').html('You have entered wrong word.');
			}
		}
}

function recoverCategory(cId){
	var con = confirm("Do you really want to recover this category?");
	if(con && cId!='' && cId!=null){
		window.location.href= baseUrl + "category/activatecategory/cId/"+cId;
	}
}

function showCategoryList(buildingId){
	var page = $('#category_page').val();	
  if(buildingId!=''){
    $('.loader').show();
    $.ajax({        
      type: "POST",
      url: baseUrl + "category/showcategorylist",
      dataType:'json',
      data: {
        buildingId: buildingId,
        page:page                
      },      
      success: function (response) {            
         $('.loader').hide();
         $("#category_popup").hide();
               $('#category_info').html(response.content); 
        
      }

    });
  }else{
    alert('No Building selected');
  }
}

/***** move single item *****/
function move_list_items(sourceid, destinationid)
{	
	var select_item = $("#"+sourceid+"  option:selected").val();	
	if(select_item == '' || select_item == null || select_item == undefined){
		alert('Please select item.');
		return false;
	}else{
       $("#"+sourceid+"  option:selected").appendTo("#"+destinationid);
    }
}

/***** move all item *****/
function move_list_items_all(sourceid, destinationid)
{
    $("#"+sourceid+" option").appendTo("#"+destinationid);
}
