<?php
//print_r($_FILES);
$output['file_arr'] =  $_FILES;
$new_image_name = 'WO-'.time().'-'.str_replace('%','',$_FILES["file"]["name"]);
$status = move_uploaded_file($_FILES["file"]["tmp_name"], "/home3/voctech/public_html/visionworkorders.com/public/work_order/".$new_image_name);

$output['file_name'] =  $new_image_name;
$output['status'] =  $status;
echo json_encode($output);

?>