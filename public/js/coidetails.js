$(function () {
	$(".modalbox").fancybox({ 'openEffect': 'none', fitToView: true });

	$("#tabs").tabs();
	$("#tabs").show();
	$("#date_cp_in").datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true
	});
	$("#date_cp_out").datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true
	});
});


function checkNewcoidetails() {
	parent.CheckForSessionpop(baseUrl);
	var certificate_holder = CKEDITOR.instances.certificate_holder.getData();
	var certificate_description = CKEDITOR.instances.certificate_description.getData();
	var send_certificate_to = CKEDITOR.instances.send_certificate_to.getData();
	var isError = false;
	if (certificate_holder.length == 0) {
		$("#holder_name_error").html("Please Enter Certificate Holder Information");
		isError = true;
	} else {
		$("#holder_name_error").html("");
	}
	if (certificate_description.length == 0) {
		$("#description_error").html("Please Enter Description of Special Terms");
		isError = true;
	} else {
		$("#description_error").html("");
	}
	if (send_certificate_to.length == 0) {
		$("#send_to_error").html("Please Enter Send Certificate To");
		isError = true;
	} else {
		$("#send_to_error").html("");
	}
	if (!isError) {
		$('.loader').show();
		$.ajax({
			url: baseUrl + "coi/createnewcoidetails",
			type: "post",
			datatype: 'json',
			data: { coi_au_details_holder: certificate_holder, coi_au_details_specialterms: certificate_description, coi_au_details_send_certificate_to: send_certificate_to },
			success: function (data) {
				var content = $.parseJSON(data);
				if (content.status == 'success') {
					$('.loader').hide();
					parent.jQuery.fancybox.close();
					parent.location.reload();					
					//$('#success_msg').html('COI-Detail added successfully!');
					//$("#success_msg").focus();
				} else {
					alert('Error occurred');
					location.reload();
				}
			},
			error: function () {
				alert('There was an error');
			}

		});

	} else {
		return false;
	}
}

function updateCoidetails() {
	var cid = $("#cid").val();
	var bid = $("#bid").val();
	var uniquecc = $("#uniquecc").val();
	var certificate_holder = CKEDITOR.instances.certificate_holder.getData(); //$("#email_content").val(); //tinymce.get('content').getContent();
	var certificate_description = CKEDITOR.instances.certificate_description.getData(); //$("#email_content").val(); //tinymce.get('content').getContent();
	var send_certificate_to = CKEDITOR.instances.send_certificate_to.getData(); //$("#email_content").val(); //tinymce.get('content').getContent();
	var isError = false;
	if (certificate_holder.length == 0) {
		$("#holder_name_error").html("Please enter certificate holder");
		isError = true;
	} else {
		$("#holder_name_error").html("");
	}
	if (certificate_description.length == 0) {
		$("#description_name_error").html("Please enter description of special term");
		isError = true;
	} else {
		$("#holder_nadescription_errorme_error").html("");
	}
	if (send_certificate_to.length == 0) {
		$("#send_to_error").html("Please enter send certificate to");
		isError = true;
	} else {
		$("#send_to_error").html("");
	}
	if (!isError) {
		$('.loader').show();
		$.ajax({
			url: baseUrl + "coi/updatecoidetailstest",
			type: "post",
			datatype: 'json',
			data: { coi_au_details_ID: cid, Building_ID: bid, uniqueCostCenter: uniquecc, coi_au_details_holder: certificate_holder, coi_au_details_specialterms: certificate_description, coi_au_details_send_certificate_to: send_certificate_to },
			success: function (data) {
				var content = $.parseJSON(data);
				if (content.status == 'success') {
					$('.loader').hide();
					$('#success_msg').html('COI-Detail edited successfully!');
					parent.location.reload();
				} else {
					alert('Error occurred');
					location.reload();
				}
			},
			error: function () {
				alert('There was an error');
			}

		});

	} else {
		return false;
	}


}

function cancelReport() {
	parent.jQuery.fancybox.close();
}

function showEditCoidetails(id) {

	$('.loader').show();
	if (id != '') {
		$.ajax({
			url: baseUrl + "coi/editcoidetails",
			type: "post",
			data: { id: id },
			success: function (data) {
				$('.loader').hide();
				$('#show_Edit_Report_div').html(data);
				$('#show_Edit_Report_div_href').trigger('click');

				//reloadPage();
			},
			error: function () {
				alert('There was an error');
			}

		});
	}
}

function deleteCoiDetails(cid) {
	var check_delete = 'YES';
	var return_value = jPrompt("For Deleting Coi Details, Enter Yes in Capital letters.", '', 'Vision Work Orders', function (return_value) {
		if (return_value != null) {
			if (check_delete === return_value) {
				$('.loader').show();
				if (cid != '') {
					$.ajax({
						url: baseUrl + "coi/deletecoidetails",
						type: "post",
						datatype: 'json',
						data: { cid: cid },
						success: function (data) {
							var content = $.parseJSON(data);
							if (content.status == 'success') {
								$('.loader').hide();
								location.reload();
							} else {
								alert('Error occurred');
								location.reload();
							}
						},
						error: function () {
							alert('There was an error');
						}

					});
				}
			} else {
				$('#cw_error').html('You have entered wrong word.');
			}
		}
	});
}

