<?php
if (!isset($_SESSION)) {
  session_start();
}

$hostname_webserver = "localhost";
$database_webserver = "voctech_mailconnector";
$username_webserver = "voctech_mailconn";
$password_webserver = "VT159263@mail";
$webserver = mysql_pconnect($hostname_webserver, $username_webserver, $password_webserver) or trigger_error(mysql_error(),E_USER_ERROR); 


if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

function fix_text($str)
 {
     $subject = '';
     $subject_array = imap_mime_header_decode($str);
 
    foreach ($subject_array AS $obj)
         $subject .= rtrim($obj->text, "\t");
 
    return $subject;
 } 
$mc_cc = 051;

$colname_rsMailConnector = "-1";
if (isset($_GET['mc_cc'])) {
  $colname_rsMailConnector = $_GET['mc_cc'];
}
mysql_select_db($database_webserver, $webserver);
$query_rsMailConnector = sprintf("SELECT * FROM mailconnector WHERE mc_cc = %s and mc_active = 'Y' ORDER BY key_connector_id ASC", GetSQLValueString($colname_rsMailConnector, "text"));
$rsMailConnector = mysql_query($query_rsMailConnector, $webserver) or die(mysql_error());
$row_rsMailConnector = mysql_fetch_assoc($rsMailConnector);
$totalRows_rsMailConnector = mysql_num_rows($rsMailConnector);

// echo "Mail Connector : " . $query_rsMailConnector . "</br>";
?>
<? 
// $mailbox = imap_open("{localhost:143/notls}INBOX", "fidelity@wkorder.net", "cbre@dm1n");  //connects to mailbox on your server   

for($z = 1; $z <= $totalRows_rsMailConnector; $z++){
	$mailbox = imap_open("{localhost:143/notls}INBOX", $row_rsMailConnector['mc_email'], $row_rsMailConnector['mc_password']);  //connects to mailbox on your server   
	
	
	if ($mailbox == false) { 
		echo "<p>Error: Can't open mailbox!</p>";  
		echo imap_last_error(); } 
		
		else {    //Check number of messages  
			$num = imap_num_msg($mailbox);   
			
			echo "The number of messages ID: " . $num . "<br>";
			
			//if there is a message in your inbox  
			if( $num > 0 ){ //this just reads the most recent email. In order to go through all the emails, you'll have to loop through the number of messages  
				$num = imap_num_msg($mailbox);
				// Loop throught each e-mail message
				// for($x=1; $x <= $num; $x++){
				while ($num >0){
					//echo "mes number" . $x . "<br>";
					$email = imap_fetchheader($mailbox, $num ); //get email header    
					$lines = explode("\n", $email);    
					// data we are looking for  
					$from = "";  
					$subject = ""; 
					$to = "";  
					$wo_number = ""; 
					$z = -1;
					$splittingheaders = true;    
						for ($i=0; $i < count($lines); $i++) {
							if ($splittingheaders) { 
							
							//====================================================================================
								 //this is a header  
								//echo $headers .= $lines[$i]."\n";    
								// look out for special headers  
							   
								 echo "[" .$i ."] " . $lines[$i] . "<br>";
							//====================================================================================							
							   if (preg_match("/^Subject: (.*)/", $lines[$i], $matches)) {  
									$subject = $matches[1];  
								}
								
								if (preg_match("/^From: (.*)/", $lines[$i], $matches)) {  
									$from = $matches[1];  
									//echo "</br> From : " . $from . "</br>";
									
									if (strrpos($from,"<")== 0){
										// If = 0 then email address is not on this line
										// Set Z equil to the next line to ge the e-mail addrerss
										
										$z= $i +1;
									}
									else {
								
										$pos1 = strrpos($from,"<");
										$pos2 = strrpos($from,">");
										//echo "</br>Pos 1 " . $pos1 . "</br>";
										//echo "Pos 2 " . $pos2 . "</br>";
										$pos3 = $pos2 - $pos1;
										//echo "Pos 3 " . $pos3 . "</br>";
										
										$from = substr($from ,$pos1, $pos3);
										
										//echo "From 1 - Sub po1 from pos2 " . $from . " wow </br> </br>";
										
										//echo "The Position is " . $pos . "</br>";
										$from = str_replace("<","",$from);
										$from = str_replace(">","",$from);
										$from = trim($from);
										$email = $from;
										//exit();
									}
									
								}
								
								// Check for line Z
								if ($i == $z){
									//Check for From Field
									//echo "Found E-mail from " .  $lines[$i] . "</br>";
										$from = $lines[$i];
										$pos1 = strrpos($from,"<");
										$pos2 = strrpos($from,">");
										//echo "</br>Pos 1 '" . $pos1 . "'</br>";
										//echo "Pos 2 '" . $pos2 . "'</br>";
										$pos3 = $pos2 - $pos1;
										//echo "Pos 3 " . $pos3 . "</br>";
										
										$from = substr($from ,$pos1, $pos3);
										
										//echo "From 1 - Sub po1 from pos2 " . $from . " wow </br> </br>";
										
										//echo "The Position is " . $pos . "</br>";
										$from = str_replace("<","",$from);
										$from = str_replace(">","",$from);
										$from = trim($from);
										$email = $from;
										
										//echo "From " . $email  . "</br>";
										$z=-1;
										//exit();
									
								}
								
								if (preg_match("/^Reply-To: (.*)/", $lines[$i], $matches)) {  
								//	$email = $matches[1]; 
								//	$email = str_replace("<","",$email);
								//	$email = str_replace(">","",$email);
								//	$email = trim($email);
								}
								
								if ($email == NULL){ $email = $from; }
								if (preg_match("/^To: (.*)/", $lines[$i], $matches)) {
									$to = $matches[1];  
								}
								if (preg_match("/^Date: (.*)/", $lines[$i], $matches)) {
									$datest = $matches[1];  
								}
								if (preg_match("/Request ID:(.*)/", $lines[$i], $matches)) {
									//$to1 = $matches[1];
									
									echo "The wo number is " . $wo_number . "<br";
									$wo_number = $matches[1]; 
									$wo_number = trim($wo_number);
									
									
									echo "The wo number is " . $wo_number . "<br";
									
									$wo_number = html_entity_decode($wo_number);   
								}
								if (preg_match("/Citizens Request ID:(.*)/", $lines[$i], $matches)) {
									//$to1 = $matches[1];
									$wo_number = $matches[1]; 
									$wo_number = trim($wo_number);
									$wo_number = html_entity_decode($wo_number);   
								}								
							}
						}
					
					
					
					
					$bdy1 =(imap_body($mailbox, $num));
					
					//echo "Body 1 " . $bdy1 . "</br>";
					
					
					$bdy1 = base64_decode($bdy1);
					
					// echo "Body 1 " . $bdy1 . "</br>";
				
					
					$bdy = strip_tags($bdy1);
							//====================================================================================					
					//We can just display the relevant information in our browser, like below or write some method, that will put that information in a database 
					echo "FROM: ". strval($from)."<br>";  
					echo "TO: ".$to."<br>";  
					echo "SUBJECT: ".$subject."<br>";  
					echo "WO :" . $wo_number . "<br>";
					
					// echo "BODY: ". $bdy1 . "<br><br>"; //imap_qprint(imap_body($mailbox, $num));   
					// echo "BODY One: ". $bdy;
					
					
					//delete message  
					//imap_delete($mailbox,$num);  
					//imap_expunge($mailbox);  
					//}  
					//else {  echo "No more messages";  }  
					
					//$lines = explode("\n", $bdy );  
					//$lines = preg_split( '/\r\n|\r|\n/', $bdy );  

					
								//====================================================================================				
					
					$lines = explode("\r\n", $bdy );  
	
					 
					$Problem_code = ""; 
					$Problocation = ""; 
					$Caller = ""; 
					$contact = ""; 
					$phone = "" ;
					$Description = "" ;
			//		for ($i=0; $i < count($lines); $i++) {
			//			echo "line number - ". $i . " --- " . strip_tags($lines[$i]) .  "<br>";
			//		}
					
				
					// Check the e-mail address
					// Needs to be from a defiend e-mail domain
					// example : @wkorder.com or @gmail.com
					// Must conating the @domainname.com
					// If Fales then remove the e-mail and do not process
					
//====================================================================================		
					// Temp - Set From to : RobPalermo@vision-networks.com for debug
				//	$from = "jllrbscitizens@ilrs.360facility.net";

//====================================================================================							
					
					if (stristr($row_rsMailConnector['mc_form_email'], $from) != false){
			
						for ($i=0; $i < count($lines); $i++) {
					 
					 //	 echo "line number - ". $i . " --- " . strip_tags($lines[$i]) .  "<br>";
						   if (preg_match("/Request ID:(.*)/", $lines[$i], $matches)) {  
						//	   echo "have a match -" . $lines[$i] . "<br>";
						//   echo "Matched - " . $matches[1] . "<br/><br/><br/>";
							$wo_number = $matches[1]; 
							$wo_number = trim($wo_number);
							$wo_number = html_entity_decode($wo_number);
						//	echo "The wo number is : " .  $wo_number . "<br>";
							}
							
				//	exit();
						if (preg_match("/^Statement of Work:(.*)/", $lines[$i], $matches)) {  
						//		echo "have a match -" . $lines[$i] . "<br>";
						//		echo "Matched - " . $matches[1] . "<br><br><br>";
							$Description = $matches[1];
							$Description = trim($Description);
							$Description = html_entity_decode($Description);
							
							}
					//echo "line number - ". $i . " --- " . strip_tags($lines[$i]) .  "<br>";					
						if (preg_match("/^Type \| Subtype:(.*)/", $lines[$i], $matches)) {  
						//		print_r($matches);
						//		echo "<br><br>have a match -" . $lines[$i] . "<br>";
						//		echo "Matched - " . $matches[1] . "<br><br><br>";
							$Problem_code = $matches[1];
							$Problem_code = trim($Problem_code);
							$Problem_code = ltrim($Problem_code, "&nbsp;");  
							
						//	echo "Problem Code : " . $Problem_code . "<br>";
							}
					
						if (preg_match("/^Requested By:(.*)/", $lines[$i], $matches)) {
						//		echo "have a match -" . $lines[$i] . "<br>";
						//		echo "Matched - " . $matches[1] . "<br><br><br>";
							$Caller = $matches[1]; 
							$Caller = trim($Caller);
						//	echo " the caller is " . $Caller . "<br>";
							$pos = strpos($Caller, ", RBS Citizens");
							 if($pos >0){
								$Caller = substr($Caller,0,$pos);
						//		echo "The New Caller is " . $Caller . "<br><br><br><br><br>"; 
							 }
							
							}
							
							if (preg_match("/, RBS Citizens -(.*)/", $lines[$i], $matches)) {
						//		echo "have a match -" . $lines[$i] . "<br>";
						//		echo "Matched - " . $matches[1] . "<br><br><br>";
							$phone = $matches[1]; 
							$phone = trim($phone);
							$pos = strpos($phone, "Cost Center:");
							 if($pos >0){
								$phone = substr($phone,0,$pos);
						//		echo "The New Caller is " . $phone . "<br><br><br><br><br>"; 
							 }
							}		
							if ($phone == NULL){
									$phone='Non Given';
							}
						}
				

						// Enter Costcenter number and Tenant Name from Database 
						$cc = $row_rsMailConnector['mc_cc'];
						$tn = $row_rsMailConnector['mc_tenant_name'];
		
		
						if ($contact == NULL){
							$contact = $Caller;
						}
		
	
						echo "</br>";
						echo "E-Mail Number : '" .  $num . "' </br> "; 
						echo "FROM : ".$from."<br>"; 
						echo "SUBJECT: ".$subject."<br>"; 
						echo "Work Order Number : ". $wo_number ."<br>";  
						echo "Problem_code : ". $Problem_code ."<br>";  				
						echo "Caller " . $Caller . "<br>";
						echo "Contact " . $contact. "<br>";
						echo "Phone " . $phone. "<br>";
						echo "Description : " . $Description. "<br>";
						echo "CostCenter : " . $cc . "<br>";
						echo "Tenant Name : " . $tn . "<br>";
						echo "Reply to: " . $email . "<br>";
						echo "From : " . $from . "<br>";
						
						
						
						
						echo "================================================ </br>";
						
						
						
						//*************************************************************************************************************************
						// Map to Description of work orders 
						//*************************************************************************************************************************
						
						 $description = "WO# " . $wo_number . " | Problem Code: " . $Problem_code . " | " ; 
						// $description .="Problem Location: " . $Problocation . " - " ; 
						// $description .="Caller: " . $Caller . ", Phone: " . $Phone . " - ";
						 $description .= $Description ;
						 
						 echo "Description into database : " .  $description . "</br></br></br>";
						 $datest = strtotime($datest);
						 $timest = date('g:i:s A', $datest);
						 $datest = date('m/d/Y', $datest);
						 
						 
						 
						 
						//*************************************************************************************************************************
						// Map to Suite
						//*************************************************************************************************************************
						 if(($Problocation == '') OR ($Problocation == NULL)){
						  $suite ='Not Specified';
						 }
						 else{
							 $suite = $Problocation;
						 }
						 
						 
						//*************************************************************************************************************************
						// Map to Category, Will Always be Miscellanous<br>
						//*************************************************************************************************************************
						$category = "Miscellaneous";

						
					 	// Add Code to enter in New Vision Work Order System and send out e-mails
						
						
						
						//  $cc - Search database for costcenter to find the correct building | Costcenter is 1454344632
						//        	Company Name is CBRE/New England
						//			Buisling Name is Citizens Plaza
						//			Compnay Name is  Citizens Bank<br>
						// $tn - is the Tenant Name, in this case it Citizens Bank
						// $contact - Requested By
						// $category - Category - Set to default to Miscellaneous
						// $description - Work Order Request
						// $suite - Suite / Location
						// $timest - Time of Request is entered into the system - Should be current time
						

						// $datest - Date of the work order is inserted into the database
						

						
						
						
						
						 
						 // delete message  
						imap_delete($mailbox,$num);  
						imap_expunge($mailbox);  
				
				
					// $num = $num -1;
					$num = imap_num_msg($mailbox);
					}
				else{
					echo "<br>***** Not A Valid Message - Sender is not reconized *****<br>";
					if (stristr($row_rsMailConnector['mc_form_email'], "'" . $from . "'") === FALSE){
						//echo "This is false ";
						//echo "<br> From : " . $from;
						$findr =  trim($from);
						echo "E-Mail is from - " . $findr . " and should be from " . $row_rsMailConnector['mc_form_email'] ."</br>";
						//echo "<br>" . stristr($row_rsMailConnector['mc_form_email'], $findr );
						echo "<br>***** This email will be deleted! ***** <br>";
					}
				
					imap_delete($mailbox,$num);  
					imap_expunge($mailbox); 
			
					$num = imap_num_msg($mailbox); 
					
					exit();
				}
			}
		}
		imap_close($mailbox); 
	} 
}

?>
<?php
mysql_free_result($rsMailConnector);


?>
