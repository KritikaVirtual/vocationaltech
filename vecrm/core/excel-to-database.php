<?php 
	$pg_prm = array(
		'title' => 'Excel to Database'
	);
	if(isset($_POST['cmd_dld']))
		$pg_prm['skip_html'] = true;
	$arr_err = array();
	$arr_ifo = array();
	require_once 'inc/head.php';

if (isset($_POST['action']) && $_POST['action'] == 'excel-data') {
require_once 'excelclass/reader.php';
$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('CP1251');


$txa_tb    = $_POST['txt_tb'];
$sendemail    = $_POST['sendemail'];

$filename = basename($_FILES['exlfile']['name']);
$ext  = substr($filename, strrpos($filename, '.') + 1);
$path = "excelfile/".$filename;

if (($ext == "xls") && ($_FILES["exlfile"]["size"] < 2000000)) {
  $test = copy($_FILES['exlfile']['tmp_name'],$path);
  $temp = $_FILES['exlfile']['tmp_name'];
  if (file_exists($temp)) {unlink($temp);} 
 }
   
$data->read($path);
error_reporting(E_ALL ^ E_NOTICE);

if ($txa_tb == "gaselectrcity-request"){
  $sql = "INSERT INTO admin_gas (";
} 

for ($j = 2; $j <= $data->sheets[0]['numCols']; $j++){
	$sql .= "" . mysql_escape_string($data->sheets[0]['cells'][1][$j]) . ",";
}
$sql = substr($sql, 0, -1) . ") VALUES\r\n";
//cells
for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++){
	$sql .= "(";
	for ($j = 2; $j <= $data->sheets[0]['numCols']; $j++)
	{
		$sql .= "'" . mysql_escape_string($data->sheets[0]['cells'][$i][$j]) . "',";
	}
	$sql = substr($sql, 0, -1) . "),\r\n";
}
$sql =  substr($sql, 0, -3) . ";";
$conn->execute($sql);
echo "Record Updated Successfully...";


if (!empty($sendemail)){

for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++){
ob_start();
?>
<p style="text-transform:capitalize;">Hi,</p><br />
Here is a new lead form business gas electric for you:<br />
<?php
for ($j = 2; $j <= $data->sheets[0]['numCols']; $j++){ ?>
<?php if($j==3){ ?>Contact Name  : <?php echo $data->sheets[0]['cells'][$i][$j]; ?><br /><?php } ?>
<?php if($j==4){ ?>Phone         : <?php echo $data->sheets[0]['cells'][$i][$j]; ?><br /><?php } ?>
<?php if($j==5){ ?>Email         : <?php echo $data->sheets[0]['cells'][$i][$j]; ?><br /><?php } ?>
<?php }?>
<br />
Thank you <br />
<?php
     $sqle = "select email from admin_setting where name = 'email'";
     $dat_usr = $conn->read($conn->select($sqle));
     $arr_eml = explode("\n", str_replace("\r", '', $dat_usr['eml']));

     $from = "sales@businessgaselectric.org.uk";
     //$to = "chandra.suman9@gmail.com";
     $headers = "MIME-Version: 1.0" . "\r\n";
     $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
     $headers .= "From: $from" . "\r\n";
     $message = ob_get_clean();
     foreach($arr_eml as $eml){
	if(trim($eml) != '')
	   mail($eml,'Business Gas Electric | businessgaselectric.org.uk', $message, $headers);
     }
    
}

}

}?>

<div id="sct_main">
	<form name="frm" action="" method="post" enctype="multipart/form-data">
         <input type="hidden" value="excel-data" name="action" />
                          <table border="0" cellspacing="0" cellpadding="0" style="width:100%">
				<tr style="font-weight:bold"><td colspan="4" style="border: 0 none;">&nbsp;</td></tr>
				<tr style="font-weight:bold"><td colspan="4" style="border: 0 none;">&nbsp;</td></tr>
				<tr style="font-weight:bold"><td colspan="4" style="border: 0 none;">&nbsp;</td></tr>
				<tr style="font-weight:bold">
					<td colspan="2" style="border: 0 none;">Select Section:</td>
					<td colspan="2" style="border: 0 none;"><select id="txt_tb" name="txt_tb">
                                                        <option value="" selected="selected">Please Select</option>
                                                        <option value="gaselectrcity-request">Gas Electricity Request</option>
                                                        </select></td>
				</tr>
				<tr style="font-weight:bold"><td colspan="4" style="border: 0 none;">&nbsp;</td></tr>
				<tr style="font-weight:bold">
					<td colspan="2" style="border: 0 none;">Select File: (XLS)</td>
					<td colspan="2" style="border: 0 none;"><input name="exlfile" type="file" size="30" /></td>
				</tr>
				<tr style="font-weight:bold"><td colspan="4" style="border: 0 none;">&nbsp;</td></tr>
				<tr style="font-weight:bold">
					<td colspan="2" style="border: 0 none;">Send Email:</td>
					<td colspan="2" style="border: 0 none;"><input id="sendemail" name="sendemail" type="checkbox"></td>
				</tr>
				<tr style="font-weight:bold"><td colspan="4" style="border: 0 none;">&nbsp;</td></tr>
				<tr style="font-weight:bold"><td colspan="4" style="border: 0 none;">&nbsp;</td></tr>
				<tr style="font-weight:bold"><td colspan="4" style="border: 0 none;">&nbsp;</td></tr>
				<tr style="font-weight:bold">
					<td colspan="2" style="border: 0 none;">&nbsp;</td>
					<td colspan="2" style="border: 0 none;"><input type="submit" name="submit" value="Upload File"  /></td>
				</tr>

				<tr style="font-weight:bold"><td colspan="4" style="border: 0 none; height:150px;">&nbsp;</td></tr>
                          </table>
	</form>

       </div>
       <?php require_once 'inc/footer.php'; ?>