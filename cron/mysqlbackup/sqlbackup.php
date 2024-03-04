<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
 echo "IFO-100 - The Document root is " .  dirname(__FILE__)  . "<br> \r";

echo "IFO-105 - Current working Directory " . getcwd() . "<br> \r"; 
if ($_SERVER['SERVER_NAME']){
	echo "IFO-106 - SERVER_NAME <br> \r"; 
	$CaseVar = $_SERVER['SERVER_NAME'];
	$CurrentWorkingDir = getcwd() ;
	// echo "IFO CWD - " .  getcwd() . "<br> \n\r";
	require '../PHPMailer/src/PHPMailer.php';
	require '../PHPMailer/src/SMTP.php';
	require '../PHPMailer/src/Exception.php';


}else{
	echo "IFO-107 - PHP_SELF <br> \r"; 
	$CaseVar = $_SERVER['PHP_SELF'];
	$CurrentWorkingDir =  dirname(__FILE__);
	require dirname(dirname(__FILE__)).'/PHPMailer/src/PHPMailer.php';
	require dirname(dirname(__FILE__)).'/PHPMailer/src/SMTP.php';
	require dirname(dirname(__FILE__)).'/PHPMailer/src/Exception.php';
}
echo "IFO-110 - PHP Self - " . $_SERVER['PHP_SELF'] .  "<br> \r";
echo "IFO-120 - Server Name - " . $_SERVER['SERVER_NAME'] . "<br> \r"; 
echo "IFO-130 - Current working Directory " . getcwd() . "<br> \r"; 
echo "IFO-135 - Set Driectory " . $CurrentWorkingDir . "<br> \r"; 
echo "IFO-140 - Case Version - " . $CaseVar . "<br> \r";

				switch ($CaseVar){
			
						case "qaworkorder.com";
						case "www.qaworkorder.com";
							require "../../../croninclude/cron.php";
							break;
						
						case "/home/ve/public_html/cron/mysqlbackup/sqlbackup.php";
							require  getcwd() ."/croninclude/cron.php";
							break;
					
						case "visionworkorders.com";
						case "www.visionworkorders.com";
							require "../../../../croninclude/cron.php";
							break;						
						
						case "/home/voctech/public_html/visionworkorders.com/cron/mysqlbackup/sqlbackup.php";
							require  getcwd() ."/croninclude/cron.php";
							break;
			
			
						case "qaworkorder.com";
						case "www.qaworkorder.com";
							require "../../../croninclude/cron.php";
							break;
						
						case "/home/qaworkorder/public_html/cron/mysqlbackup/sqlbackup.php";
							require  getcwd() ."/croninclude/cron.php";						
							break;

					} 
//exit();					
// $servername = "localhost";
// $tables = '*';

//echo "INo-999 - UserName " . $username . "<br> \r";
//echo "Passsword " . $password . "<br> \r";
//echo "Database " . $dbname . "<br> \r";


echo "IFO-150 - Connecting to Server.</br> \r"; 

	
// exit();	

   
   $backup_file = $CurrentWorkingDir ."/".$dbname . date("Y-m-d-H-i-s") . '.sql';
   
   //echo "IFO-155 - mysqldump --no-tablespaces -u $username -p$password $dbname> $backup_file <br> \r";
   $command = "mysqldump --no-tablespaces -u $username -p$password $dbname> $backup_file";
   
   system($command);
   
   
   

$zip = new ZipArchive;

$BackupFileZIP = $CurrentWorkingDir ."/VisionBackup.zip";


echo "ZIP-100 - Check if Backup File Exist  '$BackupFileZIP' <br> \r";
	if (file_exists($BackupFileZIP )){
		echo "ZIP-101 - Backup does exist <br> \r";
		if (!unlink($BackupFileZIP)) {
			echo ("ZIP-102 - " .$BackupFileZIP . " cannot be deleted due to an error \r");
		}
		else {
			echo ("ZIP-103 - " .$BackupFileZIP . " has been deleted \r");
		}
	}
	else{
		echo "ZIP-104 -  Backup File Not found, will continue <br> \r";
	}

	
echo "ZIP-105 - Creating Backup.zip File ....<br> \r";

$zip = new ZipArchive;
if ($zip->open($BackupFileZIP, ZipArchive::CREATE) === TRUE)
{
    // Add files to the zip file<br>
	echo "ZIP-106 - Adding file '" . $backup_file . "' <br> \r";
	
	if (file_exists($backup_file )){
    $zip->addFile($backup_file);
	}else{
	echo "ZIP-107 - Cannot find the file "  .$backup_file. " <br> \r";
}
 
    // All files are added, so close the zip file.
    $zip->close();
}
echo "ZIP-108 - '$backup_file' has been Created <br> \r";	

echo "CWD-100 - Current Working Driectory : '" . getcwd()."' <br> \r";
echo "IFO-160 - Mail Host ".  $mailHost ."<br> \r";
		
$mail =  new PHPMailer;
//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                //Enable verbose debug output
$mail->isSMTP();                                        // Set mailer to use SMTP
$mail->Host = $mailHost;                                // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                                 // Enable SMTP authentication
$mail->Username = $mailUsername;                        // SMTP username
$mail->Password = $EMailPassword ;                      // SMTP password
//$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted


$mail->From =  $FromEmail;
$mail->isHTML(true); 
$mail->addAddress('support@voc-tech.com');

$mail->Subject = "Vision DB Backup - " . $backup_file;
$mail->Body    = "Please see attache file - ".$BackupFileZIP ;

$file_to_attach ="/";

$mail->FromName = 'Vision Database Backup';

$mail->AddAttachment($BackupFileZIP);
echo "EML-100 - Sending Email </br> \r";
if(!$mail->send()) {
    echo "EML-105 - Message could not be sent. <br> \r";
    echo "EML-106 - Mailer Error: " . $mail->ErrorInfo. " <br> \r";
} else {
    echo "EML-110 - Message has been sent to : 'support@voc-tech.com' <br> \r";
}          
          
echo "RMD-100 - Remove Backip File " . $backup_file . "<br> \r";

if (file_exists($backup_file )){
		echo "RMD-101 - Backup does exist, Proceed to delete file <br> \r";
		if (!unlink($backup_file)) {
			echo ("RMD-102 - " .$backup_file . " cannot be deleted due to an error <br> \r");
		}
		else {
			echo ("RMD-103 - " .$backup_file . " has been deleted <br>\r");
		}
	}
	else{
		echo "RMD-104 -  Backup File Not found, will continue <br> \r";
	}

?>