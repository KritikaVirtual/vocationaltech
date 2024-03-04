<?php 
	$pg_prm = array(
		'title' => 'My Account',
		'css' => 'my-account',
		'js' => 'my-account'
	); 
	require_once 'inc/head.php';
	
	/*$sql = '
		select *
		from admin_usr
		where id = '.sql_quote(get_uid()).'
	';
	$dat_usr = $conn->read($conn->select($sql));*/

	$ins ="select email FROM admin_setting where name = 'email'";
	$dat_usr1 = $conn->read($conn->select($ins));
 
	

if (isset($_POST['action']) && $_POST['action'] == 'admin-password') {

        $txa_beml    = addslashes($_POST['txa_beml']);
       // $txa_mbeml   = addslashes($_POST['txa_mbeml']);
        //$txa_cbeml   = addslashes($_POST['txa_cbeml']);
		
        $pwd_usr_pwd   = $_POST['user_password'];
	
	if($txa_beml){
		$sql =  "update admin_setting set email='".$txa_beml."' where name='email'";
		$conn->execute($sql);
		 
	}
	if($pwd_usr_pwd ){
		$pwd_usr_pwd   = md5($_POST['user_password']);
		$sql2 = "update admin_usr set usr_pwd='".$pwd_usr_pwd."' where id='". $_SESSION['uid'] . "'";
		$conn->execute($sql2);
	}

	$error=2;
	echo "	<script>";
	echo "	location.href='my-account.php?msg=".$error."';";
	echo "	</script>";


}
?>
<form action="" method="post" name="fwd" enctype="multipart/form-data">
<input type="hidden" name="action" value="admin-password">

<div id="sct_mainacc" style="height:135px">
    <div id="sct_my_acc" class="frm">
        <div class="row">
            <strong>Update PPI eMail Details</strong>
        </div>
        <div class="row">
            <label>Email Address</label>
            <textarea  style="width: 365px; height: 23px;" id="txa_beml" name="txa_beml" class="txa"><?php echo str_esc($dat_usr1['email']); ?></textarea>
        </div>
    </div>
</div>




<div id="sct_mainacc" style="height:135px">
    <div id="sct_my_acc" class="frm" >
        <div class="row">
            <strong>Update Administrator Password</strong>
        </div>
		<div class="row"> <label>New Password</label><br></div>
		<div class="row">
           
            <input type="password" id="user_password" name="user_password" value="" class="txt" />
        </div>
		 
        <div class="row"></div>
		     <?php $error = $_GET['msg'];
               if($error == 2 ){?>
                <div id="msg_my_acc" class="msg">Record Update Successfully...</div>     
              <?php } ?>
		
		
		
    </div>
</div>
<div id="sct_mainacc" style="width: 687px; text-align: center;">
	<input type="submit" value="Update" name="submit" />
</div>
</form>


<div id="sct_mainacc" style="display:none">
    <div id="sct_my_acc" class="frm">
        <div class="row">
            <strong>Update Mobile PPI eMail Details</strong>
        </div>
        <div class="row">
            <label>Email Address</label>
            <textarea id="txa_mbeml" name="txa_mbeml" class="txa"><?php echo str_esc($dat_usr2['eml']); ?></textarea>
        </div>
    </div>
</div>

<div id="sct_mainacc" style="display:none">
    <div id="sct_my_acc" class="frm">
        <div class="row">
            <strong>Update Call Back eMail Details</strong>
        </div>
        <div class="row">
            <label>Email Address</label>
            <textarea id="txa_cbeml" name="txa_cbeml" class="txa"><?php echo str_esc($dat_usr3['eml']); ?></textarea>
        </div>
        <div id="msg_my_acc" class="msg"></div>
    </div>
</div>
<?php require_once 'inc/footer.php'; ?>