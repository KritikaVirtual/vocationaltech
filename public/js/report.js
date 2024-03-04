$(function() {
            $(".modalbox").fancybox({'openEffect': 'none',fitToView: true});
			
            $( "#tabs" ).tabs();
			$( "#tabs" ).show();
            $( "#date_cp_in" ).datepicker({
             dateFormat:'mm/dd/yy',
             changeMonth: true,
             changeYear: true
             });
             $( "#date_cp_out" ).datepicker({
             dateFormat:'mm/dd/yy',
             changeMonth: true,
             changeYear: true
             });
          });


function checkNewReport() 
{   parent.CheckForSessionpop(baseUrl);
    $('#addnewreport').attr('disabled',true);
	var report_name = $("#report_name").val();
	var dashboard_menu = $("#dashboard_menu").val();
	var report_mrt = $("#report_mrt").val();
	var report_target = $("#report_target").val();
	var accounts = $("#accounts").val();
	var report_option = [];
        var report_type = $("#report_type:checked").val();        
        $('.multicheckdd:checked').each(function(i){
          report_option[i] = $(this).val();
        });
		console.log(report_option.length);
	var submit_flag = true;
	if(report_name==''){
			$('#report_name_error').html("Report Name can't be blank");
			submit_flag = false;
	}
	if(dashboard_menu==''){
			$('#dashboard_menu_error').html("Dashboard Menu can't be blank");
			submit_flag = false;
	}
	if(report_mrt==''){
			$('#report_mrt_error').html("Please select Report");
			submit_flag = false;
	}
	if(report_option.length==0){
			$('#report_option_error').html("Option can't be blank");
			submit_flag = false;
	}
	if(report_target==''){
			$('#report_target_error').html("Please select Target");
			submit_flag = false;
	}
	if(accounts==''){
			$('#accounts_error').html("Please select Account");
			submit_flag = false;
	}
	if(!submit_flag) {
		$('#addnewreport').attr('disabled',false);
	}
	if(submit_flag) {
		$('.loader').show();
		$.ajax({
				url         : baseUrl+"report/createnewreport",
				type        : "post",
				datatype    : 'json',
				data        : {report_name:report_name,dashboard_menu:dashboard_menu,report_mrt:report_mrt,
							   report_option:report_option, report_target:report_target, accounts:accounts,report_type:report_type },
				success     : function( data ) {
				var content = $.parseJSON(data);
                    if(content.status=='success'){
						$('.loader').hide();
						//parent.jQuery.fancybox.close();
						parent.location.reload();
						
					}else{
						alert('Error occurred');
						location.reload();
					}
				},
				error       : function(){
					alert('There was an error');
				}

			 });
	
	} else {
	    return false;
	}
}


function updateReport() 
{
 $('#save_bdservice').attr('disabled','disabled');
	var report_name = $("#report_name").val();
	var dashboard_menu = $("#dashboard_menu").val();
	var report_mrt = $("#report_mrt").val();
	var report_target = $("#report_target").val();
	var accounts = $("#accounts").val();
	var rid= $('#rid').val();
	var report_option = [];
        var report_type = $("#report_type:checked").val();
        $('.multicheckdd:checked').each(function(i){
          report_option[i] = $(this).val();
        });
	var submit_flag = true;
	if(report_name==''){
			$('#report_name_error').html("Report Name can't be blank");
			submit_flag = false;
	}
	if(dashboard_menu==''){
			$('#dashboard_menu_error').html("Dashboard Menu can't be blank");
			submit_flag = false;
	}
	if(report_mrt==''){
			$('#report_mrt_error').html("Please select Report");
			submit_flag = false;
	}
	if(report_option.length==0){
			$('#report_option_error').html("Option can't be blank");
			submit_flag = false;
	}
	if(report_target==''){
			$('#report_target_error').html("Please select Target");
			submit_flag = false;
	}
	if(accounts==''){
			$('#accounts_error').html("Please select Account");
			submit_flag = false;
	}
	if(submit_flag) {
		if(rid!=''){
			$('.loader').show();
			$.ajax({
					url         : baseUrl+"report/createnewreport",
					type        : "post",
					datatype    : 'json',
					data        : {report_name:report_name,dashboard_menu:dashboard_menu,report_mrt:report_mrt,
								   report_option:report_option, report_target:report_target, accounts:accounts,rid:rid, report_type:report_type },
					success     : function( data ) {
					var content = $.parseJSON(data);
						if(content.status=='success'){
							$('.loader').hide();
							parent.jQuery.fancybox.close();
							 location.reload();
						}else{
							alert('Error occurred');
							location.reload();
						}
					},
					error       : function(){
						alert('There was an error');
					}

				 });
		
		} else {
			return false;
		} 
	}
}

function cancelReport() 
{
	parent.jQuery.fancybox.close();
}

function showEditReport(rid)
{
	$('.loader').show();
	if(rid!='') {
		$.ajax({
					url         : baseUrl+"report/editreport",
					type        : "post",
					data        : {rid:rid},
					success     : function( data ) {
							$('.loader').hide();
							$('#show_Edit_Report_div').html(data);
							$('#show_Edit_Report_div_href').trigger('click');
							
							//reloadPage();
					},
					error       : function(){
						alert('There was an error');
					}

		});
	}
}

function deleteReport(rid) 
{
	var check_delete='YES';
    var return_value = jPrompt("For Deleting Report, Enter Yes in Capital letters.", '', 'Vision Work Orders', function(return_value) { 
        if(return_value!=null){
                if(check_delete === return_value){
                $('.loader').show();
	if(rid!='') {
		$.ajax({
					url         : baseUrl+"report/deletereport",
					type        : "post",
					datatype    : 'json',
					data        : {rid:rid},
					success     : function( data ) {
					var content = $.parseJSON(data);
						if(content.status=='success'){
							$('.loader').hide();
							location.reload();
						}else{
							alert('Error occurred');
							location.reload();
						}
					},
					error       : function(){
						alert('There was an error');
					}

		});
	}
	 } else{
                $('#cw_error').html('You have entered wrong word.');
            }
        } } );
}

