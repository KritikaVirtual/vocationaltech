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
	
	$('.phoneNumber').editable({
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
	
	$('.suite_location').editable({
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
		
	$('.cc_enable').editable({
		source: [		
        {value: '0', text: 'No'},       
        {value: '1', text: 'Yes'},
       ],
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});
	
	$('.complete_notification').editable({
		source: [		
        {value: '0', text: 'No'},       
        {value: '1', text: 'Yes'},
       ],
		validate: function(value) {
           if($.trim(value) == '') return 'This field is required';
        }
	});	

	   
 });
