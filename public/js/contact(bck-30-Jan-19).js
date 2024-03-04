/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 
function validation(e){
    e.preventDefault();   
    var isValid = false;
    var name = $("#name").val();
    var company = $("#company").val();
    var email = $("#email").val();
    var telephone = $("#telephone").val();
    var form_name = $("#form_name").val();
    var baseurl = $("#baseurl").val();
    var question = $("#question").val();
    
    if(name.trim() == ""){
       // $(".error_name").html("Please Enter Name");
       $(".error_name").html('<strong>Error!</strong>  Please Enter Name');
       $(".error_name").addClass('alert alert-danger');
        isValid = true;
    }else{
        $(".error_name").html("");
        $(".error_name").removeClass('alert alert-danger');
    }
    if(company.trim() == ""){
        $(".error_company").html('<strong>Error!</strong>  Please Enter Company Name');
        $(".error_company").addClass('alert alert-danger');
        isValid = true;
    }else{
        $(".error_company").html("");
        $(".error_company").removeClass('alert alert-danger');
    }
    if(email.trim() == ""){
        $(".error_email").html('<strong>Error!</strong>  Please Enter email');
        $(".error_email").addClass('alert alert-danger'); 
        
    }else{
        x = email.trim();
        var atpos = x.indexOf("@");
        var dotpos = x.lastIndexOf(".");
        if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {
            $(".error_email").html('<strong>Error!</strong>  Please Enter Valid Email');
            $(".error_email").addClass('alert alert-danger');
            isValid = true;
        }else{
            $(".error_email").html("");
            $(".error_email").removeClass('alert alert-danger');
        }
    }
    if(telephone.trim() == ""){
        $(".error_telephone").html('<strong>Error!</strong>  Please Enter Telephone Number');
        $(".error_telephone").addClass('alert alert-danger');
        isValid = true;
    }else{
        $(".error_telephone").html("");
        $(".error_telephone").removeClass('alert alert-danger');
    }
    if(isValid){
        return false;
    }else{
        $('.loader').addClass("show");        
        $.ajax({
                type: "POST",
                url: baseurl + 'index/contactus',
                data: {name: name,form_name:form_name,company:company,email:email,telephone:telephone,question:question},
                success: function (response) {
                    console.log(response);
                    $('.loader').removeClass("show");
                    $('#msg').attr("style","display:block");
                    //alert("Thanks for contacting us");
                    document.getElementById("form").reset();
                }
            });
             
    }
 return false;  
    
}

  function isNumberKey(evt)
	{
		var charCode = (evt.which) ? evt.which : event.keyCode;
		if (charCode != 46 && charCode > 31
		&& (charCode < 48 || charCode > 57))
		return false;
		 
		return true;
	}
        
        
        