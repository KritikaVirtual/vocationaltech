 
 function checkBuildingUser(obj){	 
 var build_id	=	obj.value;
 var userid	=	$("form#addNewBuilding input#uid").val();
 $('.loader').show();
 $.ajax({
			url         : baseUrl+"user/validatebuildinguser",
			type        : "post",
			datatype    : 'json',                
			data        : { build_id:build_id },
			success : function( result ) {
				var data = $.parseJSON(result);
				if(parseInt(data.total)==1 && parseInt(data.user_id)==parseInt(userid)){					
	                $('.module').attr('checked',true);
					document.getElementById("building_"+build_id).checked = true;
					$("form#addNewBuilding input#building_"+build_id).attr("checked",true);
					$("form#addNewBuilding input#building_"+build_id).prop("checked",true);
					$('#error_msg').html('Building should have more than one user before uncheck.');
				}else{
					 $("#email-error").html("");
					var checkedIds = $(".buildings:checkbox:checked").map(function() {
						return this.id;
					}).get();
					if(checkedIds.length==0){
						$('.module').attr('checked',false);
						$('#nextSecond').attr('disabled',true);
						$('#error_msg').html('At least One module & One building must be selected.');
					}
					else{				
						$('.module').prop('checked',true);
						$('#nextSecond').attr('disabled',false);
						$('#error_msg').html('');
					}

				}
				$('.loader').hide();
			}
		});	
 }


