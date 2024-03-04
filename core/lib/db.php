<?php
	class cls_connection{
		var $db_server = '';
		var $db_name = '';
		var $db_user = '';
		var $db_pass = '';
		
		var $conn;
		
		function connect(){
			$this->conn = @mysql_connect(
				$this->db_server,
				$this->db_user,
				$this->db_pass				
			);
						
			if(!$this->conn){
				die('Cannot connect to database server!');				
			}else{
				$sql = "set names 'utf8'";
				$this->execute($sql);
				if(!mysql_select_db($this->db_name, $this->conn))
					die('Cannot connect to database server!');
				return $this->conn;
			}
		}
		
		function disconnect(){
			mysql_close($this->conn);
		}
		
		function error(){
			throw new Exception(mysql_error($this->conn));
		}
		
		function execute($sql){
			$result = '';
			$result = mysql_query($sql, $this->conn);
			
			if(!$result)
				$this->error();
			else
				return '';
		}
		
		function get_id(){
			return mysql_insert_id($this->conn);		
		}
		
		function select($sql){
			$result = '';
			$result = mysql_query($sql, $this->conn);
			
			if(!$result)
				$this->error();
			else
				return $result;
		}
		
		function quick_select($sql, $col){
			$res = '';
			$rs = $this->select($sql);
			if($row = $this->read($rs))
				$res = $row[$col];			
			$this->dump($rs);
			return $res;
		}
		
		function read($rs){
			$result = '';
			$result = mysql_fetch_array($rs);
			return $result;
		}
		
		function read_assoc($rs){
			$result = '';
			$result = mysql_fetch_assoc($rs);
			return $result;
		}
		
		function read_row($rs){
			$result = '';
			$result = mysql_fetch_row($rs);
			return $result;
		}
		
		function dump($rs){
			mysql_free_result($rs);
		}
		
		function begin_trans(){
			$this->execute('set autocommit=0');
			$this->execute('start transaction');
		}
				
		function commit(){
			$this->execute('commit');
			$this->execute('set autocommit=1');
		}
		
		function roll_back(){
			$this->execute('rollback');
			$this->execute('set autocommit=1');
		}
		
		function record_count($rs){
			return mysql_num_rows($rs);
		}
		
		function affected_rows(){
			return mysql_affected_rows($this->conn);
		}
		
		function has_data($rs){
			return (($this->record_count($rs) > 0)?true:false);
		}
		
		function get_date_time(){
			try{
				$res = '';
				
				$sql = 'select now()';				
				return $this->quick_select($sql, 0);					
			}catch(Exception $e){
				handle_error($e->getMessage());
			}
		}
	}
?>