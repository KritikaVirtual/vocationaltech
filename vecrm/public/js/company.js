$(document).ready(function(){
	
	$.fn.editable.defaults.mode = 'popup';
	$('#username').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	$('.buiding').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.address').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.address2').editable();
	
	$('.city').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.state').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.postal').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
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
	
	$('.fax').editable({
		validate: function(value) {
           if($.trim(value) != ''){
			   var isValid = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/.test(value);
				if (!isValid){
					return 'The \'Fax Number\' is not a vaild, please try again!';
				}
			}
        }
	});
	
	$('.billCompany').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.billAddress').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.billAddress2').editable();
	
	$('.billSuite').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.billCity').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.billState').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.billPostal').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.billPhone').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
           var isValid = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/.test(value);
			if (!isValid){
				return 'The \'Phone Number\' is not a vaild, please try again!';
			}
        }
	});
	
	$('.billPhoneExt').editable({
		validate: function(value) {
           if($.trim(value) != ''){
			   var isValid = /^[0-9-+]+$/.test(value);
				if (!isValid){
					return 'The \'Phone Extension\' is not a vaild, please try again!';
				}
			}

        }
		});
	
	$('.billFax').editable({
		validate: function(value) {
            if($.trim(value) != ''){
			   var isValid = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/.test(value);
				if (!isValid){
					return 'The \'Fax Number\' is not a vaild, please try again!';
				}
			}
        }
	});
	
	$('.attention').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.remit_address').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	   
 });
