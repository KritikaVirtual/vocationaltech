<?php

   class DBFunctions1 {
    private $db;

    function __Construct() {
        require_once '../include/DBConnect.php';
        $this->db = new DBConnect();
        $this->db->connect();
		//echo $query = "TRUNCATE TABLE `auto_refersh";
		$query = "DELETE FROM auto_refersh where created_at < NOW() - INTERVAL 2 MINUTE";
		$result = mysql_query($query) or die(mysql_error());
    }

    function __Destruct() {

    }
	
	}
	
	$new = new DBFunctions1();

?>