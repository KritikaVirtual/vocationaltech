/***********Show Edit Note*********/

function showEditNote(id)
{
	clearMessage();
	$('.show_note_tr').show();
	$('.edit_note_tr').hide();	
	$('#edit_note_'+id).show();
	$('#show_note_'+id).hide();
	
}
function hideEditNote(id)
{
	clearMessage();
	$('#edit_note_'+id).hide();
	$('.edit_note_tr').hide();
	$('#show_note_'+id).show();
	
}

function clearMessage(){
	$('.error_msg').html('');
	$('.success_msg').html('');
}

function addNotes(){
	clearMessage();
	var notes = $('#notes').val();
	if(notes==''){
		$('#notes_error').html('Please Enter Notes.');
		return false;
	}else{
		$('#notes_error').html('');
		document.form_add_note.submit();
	}
}

function cancelAddNote(){
	clearMessage();
	$('#add_note').hide();
}


function showAddNote(){
	clearMessage();
	$('#notes_error').html('');
	$('#notes').val('');
	$('#add_note').show();
}

function editNote(nid){
	
	var notes = $('#notes_'+nid).val();
	var status = $('#status_'+nid).val();
	if(notes==''){
		alert('Notes can not be empty.');		
		return false;
		}else{
			$('.loader').show();
			$.ajax({
						url         : baseUrl+"notes/editnotes",
						type        : "post",
						datatype    : 'json',                
						data        : {
									   nid:nid,notes:notes,status:status
									   },
						success : function( result ) {
							$('.loader').hide();												
							if(result=='true'){
								hideEditNote(nid);
								$('.success_msg').html('Predefined  Notes has been updated successfully.');
								location.reload();
							}else{
								$('.error_msg').html('Some error occurred.');
							}					
						}
					});
	}
	
	
	
}

function deleteNote(nid){
	if(nid!=''){
		var check_delete='YES';		
		var return_value = prompt("For Deleting Notes, Enter Yes in Capital letter.");
		if(return_value!=null){
			if(check_delete === return_value){
				$('.loader').show();
				$.ajax({
						url         : baseUrl+"notes/deletenotes",
						type        : "post",
						datatype    : 'json',                
						data        : {
									   nid:nid
									   },
						success : function( result ) {
							$('.loader').hide();												
							if(result=='true'){
								$('.success_msg').html('Predefined  Notes has been deleted successfully.');
								location.reload();
							}else{
								$('.error_msg').html('Some error occurred.');
							}					
						}
					});	
			}else{
				//alert('You have entered wrong word.');
				$('.error_msg').html('You have entered wrong word.');
			}	
		}
	}
}
