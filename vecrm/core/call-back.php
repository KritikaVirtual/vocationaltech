<?php 
	$pg_prm = array(
		'title' => 'Call Back'
	);

	if(isset($_POST['cmd_dld']))
		$pg_prm['skip_html'] = true;

	$arr_err = array();
	$arr_ifo = array();

	require_once 'inc/head.php'; 

	if(isset($_POST['cmd_del'])){
		if(!count($_POST['chk_sel']))
			$arr_err[] = 'Nothing to delete';
		if(!count($arr_err)){
			foreach($_POST['chk_sel'] as $sel){
				$sql = 'delete from admin_cal_bck where id = '.intval($sel);
				$conn->execute($sql);
			}
			$arr_ifo[] = 'Selected data have been deleted';
		}
	}
	if(isset($_POST['cmd_dld'])){
		$arr_lst = array();
		$sql = '
			select *
			from admin_cal_bck
			order by dte_add desc
		';				
		$rs = $conn->select($sql);
		while($row = $conn->read($rs)){
			$arr_lst[] = array(
				$row['nme'],
				$row['phn'],
				$row['ip'],
				$row['aff'],
				$row['dte_add']
			);
		}

		$data = array(
			1 => array ('Name', 'Phone', 'IP', 'Affiliate ID', 'Submit Date / Time')
		);
		$data = array_merge($data, $arr_lst);

		$xls = new Excel_XML('UTF-8', false, 'Call Back List');
		$xls->addArray($data);
		$xls->generateXML('Call Back List');
		exit;
	}
			
?>
<div id="sct_main">
	<div class="mgr_act"><a href="?act=lst">List</a></div>
	<?php 
		if(count($arr_err))
			echo '<div class="msg err">'.implode('<br/>', $arr_err).'</div>';
		if(count($arr_ifo))
			echo '<div class="msg ifo">'.implode('<br/>', $arr_ifo).'</div>';
	?>
	<form action="" method="post" enctype="multipart/form-data">
		<?php if($_GET['act'] == 'add'){ ?>		
		<?php }else{ ?>
			<table cellpadding="4" cellspacing="1" style="width:100%">
				<tr bgcolor="#efefef" style="font-weight:bold">
					<td style="width:20px"></td>
					<td>Name</td>
					<td>Phone</td>
					<td>IP</td>
					<td>Affiliate ID</td>
					<td>Submit Date / Time</td>
				</tr>
			<?php 



  define('MAX_REC_PER_PAGE', 20);
  $coun = 'select count(*) from admin_cal_bck';
  $count = $conn->select($coun);
  list($total) = mysql_fetch_row($count);
  $total_pages = ceil($total / MAX_REC_PER_PAGE);
  $page = intval(@$_GET["page"]); 
  if (0 == $page){ $page = 1; }  
  $start = MAX_REC_PER_PAGE * ($page - 1);
  $max = MAX_REC_PER_PAGE;	


				$sql = '
					select *
					from admin_cal_bck
					order by dte_add desc limit '.$start.",". $max.'';

				$rs = $conn->select($sql);
				if($conn->has_data($rs)){
  					while($row = $conn->read($rs)){
			?>			
					<tr valign="top">
						<td><input type="checkbox" name="chk_sel[]" value="<?php echo intval($row['id']); ?>"/></td>
						<td><?php echo str_esc($row['nme']); ?></td>
						<td><?php echo str_esc($row['phn']); ?></td>
						<td><?php echo str_esc($row['ip']); ?></td>
						<td><?php echo str_esc($row['aff']); ?></td>
						<td><?php echo str_esc($row['dte_add']); ?></td>
					</tr>
			<?php
					}
				}else{
					echo '<tr><td colspan="6">Nothing yet</td></tr>';
				}
			?>
			</table>
			<br/>
			<input type="submit" value="Delete" name="cmd_del" onclick="return confirm('Are you sure to delete selected data?')"/>	
			<input type="submit" value="Download All" name="cmd_dld"/>			
		<?php } ?>
	</form>
</div>

<table border="0" cellpadding="5" align="center">
  <tr><td>Goto Page:</td>
  <?php
  for ($i = 1; $i <= $total_pages; $i++) {
  $txt = $i;
  if ($page != $i) 
  $txt = "<a href=\"" . $_SERVER["PHP_SELF"] . "?page=$i\">$txt</a>";  ?>  
  <td align="center"><?= $txt ?></td>
  <?php } ?>
  </tr>
</table>
<hr>

<?php require_once 'inc/footer.php'; ?>