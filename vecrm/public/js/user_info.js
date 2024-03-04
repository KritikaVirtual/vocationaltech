$(document).ready(function(){
	
	$.fn.editable.defaults.mode = 'popup';
	$('.firstName').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	$('.lastName').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.title').editable();
	
	$('.phone').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
           var isValid = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/.test(value);
			if (!isValid){
				return 'The \'Phone Number\' is not a vaild, please try again!';
			}

        }
	});
	
	$('.phoneExt').editable({
		validate: function(value) {
           if($.trim(value) != ''){
			   var isValid = /^[0-9-+]+$/.test(value);
				if (!isValid){
					return 'The \'Phone Extension\' is not a vaild, please try again!';
				}
			}

        }
		});

	   
 });
 
 function removeUser(userId,buildId,totalUser,deleteEgroup){	 
	var check_delete='YES';
	var role_id =  $('#role_id').val();
	if(totalUser > 1){
		if(deleteEgroup=='Yes'){
			var return_value = prompt("For Deleting Building User, Enter Yes in Capital letter.");
			//alert(return_value);
			if(return_value!=null){	
			if(check_delete === return_value){
				var group_Ids = $('#group_Ids_'+userId).val();
				$.ajax({
						url         : baseUrl+"company/deletebuildinguser",
						type        : "post",
						datatype    : 'json',                
						data        : {
									   userId:userId,buildId:buildId,group_Ids:group_Ids
									   },
						success : function( result ) {
							var data = $.parseJSON(result);
							//alert(data.msg);
							if(data.msg=='true'){
								//$('.message').html('User has been deleted successfully from building.');
								alert('User has been deleted successfully from building');
							}else{
								//$('.message').html('Some error occurred.');
								alert('Some error occurred.');
							}
							location.reload();						
						}
					});	
			}else{
				//alert('You have entered wrong word.');
				$('.message').html('You have entered wrong word.');
			}
		}
		}else{
			var distribution_group = $('#distribution_group_'+userId).val();			
			//alert(distribution_group);
			alert('Account User cannot be removed from the system because they are the only one listed under the '+distribution_group);
		}
	}
	else{
		alert('There must be more than one user. Please add one more user to delete the user from building.');
	}
 }
