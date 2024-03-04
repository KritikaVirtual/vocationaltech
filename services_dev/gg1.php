<?php
phpinfo();
/*
chkServer('gateway.sandbox.push.apple.com',2196);
//chkServer('gateway.push.apple.com',2195);

function chkServer($host, $port)
{
$hostip = @gethostbyname($host);

if ($hostip == $host)
{
echo "Server is down or does not exist";
}
else
{
if (!$x = @fsockopen($hostip, $port, $errno, $errstr, 5))
{
echo "Port $port is closed.";
}
else
{
echo "Port $port is open.";
if ($x)
{
@fclose($x);
}
}
}
} */
?> 



<?php
/*
    $gateway = 'ssl://gateway.sandbox.push.apple.com:2196';


// Create a Stream
$ctx = stream_context_create();
// Define the certificate to use 
stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck2.pem');
// Passphrase to the certificate
stream_context_set_option($ctx, 'ssl', 'passphrase', '1choc3747*1');

// Open a connection to the APNS server
$fp = stream_socket_client(
    $gateway, $err,
    $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx); 

// Check that we've connected
if (!$fp) {
    die("Failed to connect: $err $errstr");
}

// Ensure that blocking is disabled
stream_set_blocking($fp, 0);

// Send it to the server
$result = fwrite($fp, $notification, strlen($notification));

// Close the connection to the server
fclose($fp);

*/
?>




<?php 
 // Provide the Host Information.
//$tHost = 'gateway.sandbox.push.apple.com';
$tHost = 'ssl://gateway.push.apple.com';
$tPort = 2195;
// Provide the Certificate and Key Data.
	
$tCert = 'include/ck2.pem';
// Provide the Private Key Passphrase (alternatively you can keep this secrete
// and enter the key manually on the terminal -> remove relevant line from code).
// Replace XXXXX with your Passphrase
//$tPassphrase = '1choc3747*1'; 
$tPassphrase = '1choc3747*1'; 
$tToken =array();
// Provide the Device Identifier (Ensure that the Identifier does not have spaces in it).
// Replace this token with the token of the iOS device that is to receive the notification.
//$tToken = 'b3d7a96d5bfc73f96d5bfc73f96d5bfc73f7a06c3b0101296d5bfc73f38311b4';
//$tToken[] = '1f495c4b44301bf79d413a5c1ba1d5a9587a6b66ba18357917d1160ead56e9f6';
//$tToken[] = '1f495c4b44301bf79d413a5c1ba1d5a9587a6b66ba18357917d1160ead56e9f6';
$tToken[] =   '04bd473e1f738de4ba2f571170ab839130d3a200fb6a5fd3badf12476b0a938a';
$tToken[] =   'a06d942dc4f1881b84c20f9d3caf6873e83b87f8 d73071ace4b65d14b5a18f69';
//0a32cbcc8464ec05ac3389429813119b6febca1cd567939b2f54892cd1dcb134
// The message that is to appear on the dialog.
$tAlert = 'You have a LiveCode APNS Message';
// The Badge Number for the Application Icon (integer >=0).
$tBadge = 8;
// Audible Notification Option.
$tSound = 'default';
// The content that is returned by the LiveCode "pushNotificationReceived" message.
$tPayload = 'APNS Message Handled by LiveCode';
// Create the message content that is to be sent to the device.
$tBody['aps'] = array (
'alert' => $tAlert,
'badge' => $tBadge,
'sound' => $tSound,
);
$tBody ['payload'] = $tPayload;
// Encode the body to JSON.
$tBody = json_encode ($tBody);
// Create the Socket Stream.
$tContext = stream_context_create ();
stream_context_set_option ($tContext, 'ssl', 'local_cert', $tCert);
// Remove this line if you would like to enter the Private Key Passphrase manually.
stream_context_set_option ($tContext, 'ssl', 'passphrase', $tPassphrase);
// Open the Connection to the APNS Server.
$tSocket = stream_socket_client ($tHost.':'.$tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $tContext);
// Check if we were able to open a socket.
if (!$tSocket)
exit ("APNS Connection Failed: $error $errstr" . PHP_EOL);
// Build the Binary Notification.
for($i=0; $i<=1; $i++) {
	$tMsg = chr (0) . chr (0) . chr (32) . pack ('H*', str_replace(' ', '',$tToken[$i])) . pack ('n', strlen ($tBody)) . $tBody;

// Send the Notification to the Server.
$tResult = fwrite ($tSocket, $tMsg, strlen ($tMsg)); }
if ($tResult)
echo 'Delivered Message to APNS' . PHP_EOL;
else
echo 'Could not Deliver Message to APNS' . PHP_EOL;
// Close the Connection to the Server.
fclose ($tSocket);
    die("End push notification ");
?>








<?php  
// API access key from Google API's Console
define( 'API_ACCESS_KEY', 'AIzaSyAk8wM98zDQPj4ePYRtN8iNsS5DmTXT-rg' );
$registrationIds = array(  'APA91bEgzZ21XSwhS3pvyHJrhibHHAHjfqZl3xhQ0j4suc30uMRGToY1B9XQbv70IJsNXiLHugDrrwWqLwBuYsfRZFva6XDbMo5wfkqomJ8bp9Enzev4XjT61jnhLtbSCmqrdVFvxc3o');
// prep the bundle
$iphontoken = '1f495c4b44301bf79d413a5c1ba1d5a9587a6b66ba18357917d1160ead56e9f6';
$msg = array
(
	'message' 	=> 'here is a message. message',
	'title'		=> 'This is a title. title',
	'subtitle'	=> 'This is a subtitle. subtitle',
	'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
	'vibrate'	=> 1,
	'sound'		=> 1,
	'largeIcon'	=> 'large_icon',
	'smallIcon'	=> 'small_icon'
);
$fields = array
(
	'registration_ids' 	=> $registrationIds,
	'data'			=> $msg
);
 
$headers = array
(
	'Authorization: key=' . API_ACCESS_KEY,
	'Content-Type: application/json'
);


$ch = curl_init();
curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
curl_setopt( $ch,CURLOPT_POST, true );
curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
$result = curl_exec($ch );
curl_close( $ch );
echo $result;

?>

