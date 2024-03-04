(function ($) {

		$.fn.toggleClick = function(){
			var functions = arguments ;
			return this.click(function(){
					var iteration = $(this).data('iteration') || 0;
					functions[iteration].apply(this, arguments);
					iteration = (iteration + 1) % functions.length ;
					$(this).data('iteration', iteration);
			});
		};
		
    })(jQuery);   
    function showWorkOrder(woId){
		
		 if(document.getElementById('workrequest_'+woId).style.display!="none"){
			   document.getElementById('workrequest_'+woId).style.display="none";
				$('#plus_'+woId).addClass("fa-plus");
				$('#plus_'+woId).removeClass("fa-minus");
		 }else{
			 $('.loader').show();
			 $('.plus_min_icon').removeClass("fa-minus");
			 $('.plus_min_icon').addClass("fa-plus");	
			 $('.tr-order').hide();
             $('.order-detail').html('');
			 $('#plus_'+woId).removeClass("fa-plus");
			 $('#plus_'+woId).addClass("fa-minus");			 
				$.ajax({        			
				url: baseUrl + "dashboard/orderdetail/woId/"+woId,
				success: function (content) {
					//$('.loader').hide();
					$('#order_content_'+woId).html(content);
					$('#workrequest_'+woId).show();
					$('.loader').hide();
				}

			});
		}
	}
	
	function showByStatus(status){
		document.search_form.submit();
	}
	
	function showStatus(){
		document.search_mini_form.submit();
	}
	
	$(function() {
		$( "#from_date" ).datepicker({
			 dateFormat:'mm/dd/yy',
			 changeMonth: true,
			 changeYear: true
			 });
		$( "#to_date" ).datepicker({
		dateFormat:'mm/dd/yy',
		changeMonth: true,
		changeYear: true
		});	
		$search_show_form = $('#search_show_form');		
		$( "#show_hide" ).toggleClick(function() {
			$search_show_form.animate({'right':'-2px'},1000);
			
			}, function() {
			  $search_show_form.animate({'right':'-'+$search_show_form.outerWidth()},1000);
			});	 
			 
	});
	
	function updateWorkorder(woId){
		var order_status = $('#order_status_'+woId).val();
		var internal_note = $('#internal_note_'+woId).val();
		var exist_schedule = $('#exist_schedule_'+woId).val();
		var priority = $('#priority_'+woId).val();
		var update_flag='false';
		var time = '';
		var slength='';
		var insert_schedule='0';
		var eschedule = exist_schedule.split(',');
		var current_status = $('#current_wstatus_'+woId).val();
		if(current_status > 1 && order_status==1){
			$('#wstatus_error_'+woId).html("Work order cann't be assign as new.");
			return false;
		}else if(current_status == order_status){
			$('#wstatus_error_'+woId).html("Work order cann't be assign as same status again.");
			return false;
	    }else{
			$('#wstatus_error_'+woId).html('');
		}
		
		if(eschedule.indexOf(order_status)=='-1'){
			$('#time_length_div_'+woId).show();
			 time = $('#Time_'+woId).val();
			 slength = $('#length_'+woId).val();
			if(time==''){
				//alert('Please enter Time');
				$('#schedule_error').html('Please enter Time');
			}else{
				insert_schedule=1;
			  update_flag='true';
		    }
		}else
		{
			update_flag='true';
		}		
		if(order_status!='' && update_flag=='true'){
			$('.loader').show();
			$.ajax({
					type: "POST",
					url: baseUrl+'dashboard/updateajaxorder',
					data: {order_status: order_status, current_status:current_status, internal_note: internal_note,woId:woId, time:time, slength:slength, insert_schedule:insert_schedule, priority:priority  },				
					success: function (msg) {
						  $('.loader').hide();
							if(msg == 'true'){
								$('.workorder_success').html("Work order update successfully.");													
								location.reload();					
							}else{							
								$('.workorder_error').html("Error occurred during work order update.");
							} 			
					}
				  });
			  
		   }
	}
	
	function ValidateForm(){
		
		var order_status = $('#order_status').val();
		var exist_schedule = $('#exist_schedule').val();
		var priority = $('#priority').val();
		
		var update_flag='false';
		var time = '';
		var slength='';
		var current_status = $('#current_wstatus').val();
		if(current_status > 1 && order_status==1){
			$('#wstatus_error').html("Work order cann't be assign as new.");
			return false;
		}else if(current_status == order_status){
			$('#wstatus_error').html("Work order cann't be assign as same status again.");
			return false;
	    }else{
			$('#wstatus_error').html('');
		}
		var insert_schedule='0';
		var eschedule = exist_schedule.split(',');		
		if(eschedule.indexOf(order_status)=='-1'){
			$('#time_length_div').show();
			 time = $('#Time').val();
			 slength = $('#length').val();
			if(time==''){
				//alert('Please enter Time');
				$('#schedule_error').html('Please enter Time');
			}else{
				//insert_schedule=1;
				$('#insert_schedule').val('1');
				$('#schedule_error').html('');
			  update_flag='true';
		    }
		}else
		{
			update_flag='true';
		}		
		if(order_status!='' && update_flag=='true'){			
			document.worequestform.submit();
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
$(function(){
	// building select menu
	$('<select />').appendTo('.tabmenu');

	

	$('.tabmenu ul li a').each(function(){
		var target = $(this);
        if(target.attr('class')=='resp-tab-active') {
		$('<option />', {
			'selected': 'selected',
			'value' : target.attr('href'),
			'text': target.text()
		}).appendTo('.tabmenu select');
        }else {
		   $('<option />', {			   
				'value' : target.attr('href'),
				'text': target.text()
			}).appendTo('.tabmenu select');
		 }   
	});

	// on clicking on link
	$('.tabmenu select').on('change',function(){
		window.location = $(this).find('option:selected').val();
	});
});

// show and hide sub menu
$(function(){
	$('.tabmenu ul li').hover(
		function () {
			//show its submenu
			$('ul', this).slideDown(150);
		}, 
		function () {
			//hide its submenu
			$('ul', this).slideUp(150);			
		}
	);
});


function reassignCategory(woId){
	if(woId!=''){
		var curr_cat = $('#curr_cat_'+woId).val();
		var change_cat = $('#assign_category_'+woId).val();
		if(change_cat!=''){
			 if(change_cat!=curr_cat){
				 $('.loader').show();
					$.ajax({
							type: "POST",
							url: baseUrl+'dashboard/changecat',
							data: {woId: woId, curr_cat: curr_cat, change_cat:change_cat },				
							success: function (msg) {
								  $('.loader').hide();
									if(msg == 'true'){
										$('.workorder_success').html("Category reassign successfully.");													
										location.reload();					
									}else{							
										$('#assign_error_'+woId).html("Error occurred during reassign category.");
									} 			
							}
						  });
			 }else{
				$('#assign_error_'+woId).html('Reassign category should be different from current category.');
			}			 
		}else{
			$('#assign_error_'+woId).html('Select Category for Reassign');
		}
	}else{
		$('#assign_error_'+woId).html('Error Occurred');
	}
}

function showCatLog(woId){
	$('#hide_hist_'+woId).show();
	$('#show_hist_'+woId).hide();
	$('#catlog_'+woId).show(100);
}

function hideCatLog(woId){
	$('#hide_hist_'+woId).hide();
	$('#show_hist_'+woId).show();
	$('#catlog_'+woId).hide();
}
