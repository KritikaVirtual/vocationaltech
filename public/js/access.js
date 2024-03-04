$(function(){
        
        $(".no_access").click(function(){
            if(this.checked){  //$(".read").removeAttr('checked');
                $(this).prev(".write").prev(".read").removeAttr('checked');
                $(this).prev(".write").removeAttr('checked');

                $('input[type=checkbox]').attr('disabled',true);

                var noAccess;
                var dataSet = new Object;

                noAccess = this.value.split('_');

                dataSet.location_id = noAccess[3];
                dataSet.role = noAccess[1];
                dataSet.is_access = 1;
                dataSet.is_read = 0;
                dataSet.is_write = 0;
                
               
                $.ajax({
                    url: "setaccess",
                    dataType: "json",
                    type: "post",
                    data: dataSet,
                    beforeSend: function( xhr ) {
                        $('.loader').show(0);
                    },
                    success: function( data ) {

                       $('.loader').delay(1000).hide(0);

                       setTimeout(function(){
                          $('input[type=checkbox]').removeAttr('disabled');
                        }, 1100);

                    }
                });
            }
            else{
                $(this).prev(".write").prev(".read").prop('checked',true);
                $('input[type=checkbox]').attr('disabled',true);

                var noAccess;
                var dataSet = new Object;

                noAccess = this.value.split('_');

                dataSet.location_id = noAccess[3];
                dataSet.role = noAccess[1];
                dataSet.is_access = 0;
                dataSet.is_read = 1;
                dataSet.is_write = 0;
                
               
                $.ajax({
                    url: "setaccess",
                    dataType: "json",
                    type: "post",
                    data: dataSet,
                    beforeSend: function( xhr ) {
                        $('.loader').show(0);
                    },
                    success: function( data ) {

                       $('.loader').delay(1000).hide(0);

                       setTimeout(function(){
                          $('input[type=checkbox]').removeAttr('disabled');
                        }, 1100);

                       
                        //alert( data );
                    }
                });
            }
        });

        $(".read").on('click',function(){
            $('input[type=checkbox]').attr('disabled',true);

            if(this.checked){  //$(".read").removeAttr('checked');
                $(this).next(".write").next(".no_access").removeAttr('checked');

                var noAccess;
                var dataSet = new Object;

                noAccess = this.value.split('_');
                dataSet.location_id = noAccess[3];
                dataSet.role = noAccess[1];
                dataSet.is_access = 0;
                dataSet.is_read = 1;

                if($(this).next(".write").checked){
                    dataSet.is_write = 1;
                }
                else{
                    dataSet.is_write = 0;
                }
            }
            else{
                var noAccess;
                var dataSet = new Object;

                noAccess = this.value.split('_');
                dataSet.location_id = noAccess[3];
                dataSet.role = noAccess[1];
                
                dataSet.is_read = 0;

                if($(this).next(".write").checked){
                    dataSet.is_write = 1;
                    dataSet.is_access = 0;
                }
                else{
                    dataSet.is_write = 0;
                    dataSet.is_access = 1;
                    $(this).next(".write").next(".no_access").prop('checked',true);
                }
            }   

                $.ajax({
                    url: "setaccess",
                    dataType: "json",
                    type: "post",
                    data: dataSet,
                    beforeSend: function( xhr ) {
                        $('.loader').show(0);
                    },
                    success: function( data ) {
                       $('.loader').delay(1000).hide(0);
                       setTimeout(function(){
                          $('input[type=checkbox]').removeAttr('disabled');
                        }, 1100);
                        //alert( data );
                    }
                });
            
        });

        $(".write").click(function(){

            $('input[type=checkbox]').attr('disabled',true);
            if(this.checked){  //$(".read").removeAttr('checked');
                $(this).next(".no_access").removeAttr('checked');
                $(this).prev(".read").prop('checked',true);

                var noAccess;
                var dataSet = new Object;

                noAccess = this.value.split('_');
                dataSet.location_id = noAccess[3];
                dataSet.role = noAccess[1];
                dataSet.is_access = 0;
                dataSet.is_write = 1;
                dataSet.is_read = 1;

            }
            else{
                var noAccess;
                var dataSet = new Object;

                noAccess = this.value.split('_');
                dataSet.location_id = noAccess[3];
                dataSet.role = noAccess[1];
                dataSet.is_access = 0;
                dataSet.is_write = 0;
                dataSet.is_read = 1;
            }     
                $.ajax({
					type: "POST",
                    url: "setaccess",
                    dataType: "json",
                    data: dataSet,
                    beforeSend: function( xhr ) {
                        $('.loader').show(0);
                    },
                    success: function( data ) {
                       $('.loader').delay(1000).hide(0);
                       setTimeout(function(){
                          $('input[type=checkbox]').removeAttr('disabled');
                        }, 1100);
                    }
                });
           
        });         
         
    });
