<?php
class DBConnect{
    
    function __construct(){
        //define("DB_HOST", "localhost");
		//define("DB_USER", "root");
		//define("DB_DATABASE", "vecrm");
		//define("DB_PASSWORD", "");
		
		//define("DB_HOST", "192.185.68.232");
		//define("DB_USER", "voctech_user");
		//define("DB_DATABASE", "voctech_vecrm");
		//define("DB_PASSWORD", "vision@123");
		
		/* define("DB_HOST", "192.185.68.232");
		define("DB_USER", "voctech_user");
		define("DB_DATABASE", "voctech_devcrm");
		define("DB_PASSWORD", "vision@123"); */
		define( 'API_ACCESS_KEY', 'AIzaSyAk8wM98zDQPj4ePYRtN8iNsS5DmTXT-rg' );
		define( 'API_SERVER_KEY', 'AAAA-U2-qNo:APA91bGWaVXme5PrAufzTCfnq6EAXZDI18RZUFXwCbFQ-htDhcpNHb5oGkadynIoyCzA9zqxedTmMapAByhDdub4aWhJtIFtoBzFAU9sanuVTBwBv0JlgJRQIoo6KhRmM7aJPDW9ElD2');
		define("BASE_URL", "http://qaworkorder.com/"); 
		define("DB_HOST", "localhost");
		define("DB_USER", "ve_crm_new");
		define("DB_DATABASE", "ve_crm_new");
		define("DB_PASSWORD", ")atYF#@vd+w%"); 
		
		
    }
    
    
    public function connect(){

       // $con = @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
        
       // mysql_select_db(DB_DATABASE)or die(mysql_error());
        $con = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        return $con;                
    }
    
    public function close(){
        mysqli_close();
    }
    
}
?>