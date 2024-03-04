<?php 
	$pg_prm = array(
		'title' => 'Ban IP',
		'css' => 'my-account',
		'js' => 'my-account'
	); 
	require_once 'inc/head.php';
	

	
if (isset($_POST['action']) && $_POST['action'] == 'ban-ip') {

       function writeBan($i){
                $ban="Deny from ".$i."\n";
                $file="../.htaccess";
                $lines = count($file);
                $open = @fopen($file, "a");
                $write = @fputs($open, $ban);
                @fclose($open);
        }

        $txa_ip    = addslashes($_POST['txa_ip']);
	$sql =  'insert into admin_banip (ip,dte_add) values ('.sql_quote($txa_ip).',now())';
	$conn->execute($sql);
	$sqlip = 'SELECT ip FROM admin_banip WHERE id = (SELECT max(id) FROM admin_banip )';
	$ban_ip = $conn->read($conn->select($sqlip));
        $arr_ip = explode("\n", str_replace("\r", '', $ban_ip['ip']));	
	foreach($arr_ip as $bip){
	   if(trim($bip) != '')
	      writeBan($bip);
	}
	
	$error=2;
	echo "	<script>";
	echo "	location.href='ban-ip.php?msg=".$error."';";
	echo "	</script>";
}
?>
<form action="" method="post" name="fwd" enctype="multipart/form-data">
<input type="hidden" name="action" value="ban-ip">

<div id="sct_main">
    <div id="sct_my_acc" class="frm">
        <div class="row">
            <strong>Update Ban IP Address</strong>
        </div>
        <div class="row">
            <label>IP Address</label>
            <textarea id="txa_ip" name="txa_ip" class="txa"></textarea>
        </div>
    </div>
</div>

<div id="sct_main">
    <div id="sct_my_acc" class="frm">
        <div class="row">
            <strong>Update Ban IP Address</strong>
        </div>
		
		<div class="row"></div>
                <div class="row"><input type="submit" value="Update" name="submit" /></div>
		     <?php $error = $_GET['msg'];
               if($error == 2 ){?>
                <div id="msg_my_acc" class="msg">Record Update Successfully...</div>     
              <?php } ?>
	
    </div>
</div>
</form>
<?php require_once 'inc/footer.php'; ?>