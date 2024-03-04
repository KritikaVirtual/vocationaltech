<?php
header('Access-Control-Allow-Origin: *');
include_once('../constant.php');

if (isset($_POST['woId']) && $_POST['woId'] != "") {
    require_once '../include/APIFunctions.php';
    $db = new APIFunctions();
 		$woId = $_POST['woId'];
 		$type = $_POST['type'];
 		
 		
//         $file_name = $_FILES["file_name"]["name"];
//         $targetDir = "../../public/work_order/";
//         $fileName = basename($_FILES["file_name"]["name"]);
//         $targetFilePath = $targetDir . $fileName;
//         $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
        
//         if(!empty($_FILES["file_name"]["name"])){
//             $allowTypes = array('jpg','png','jpeg','gif','pdf');
//             if(in_array($fileType, $allowTypes)){
//                 if(move_uploaded_file($_FILES["file_name"]["tmp_name"], $targetFilePath)){
//                     $uploadA = $db->uploadAttachment($fileName, $woId);
//                     if($uploadA){
//                         $response['status'] = 200;
//                         $response['error'] = FALSE;
//                         $response['error_msg'] = "The file ".$fileName. " has been uploaded successfully.";
//                         echo json_encode($response);
//                     }else{
//                         $response['status'] = 400;
//                     	$response['error'] = TRUE;
//                     	$response['error_msg'] = "File upload failed, please try again.";
//                     	echo json_encode($response);
//                     } 
//                 }else{
//                     $response['status'] = 400;
//                 	$response['error'] = TRUE;
//                 	$response['error_msg'] = "Sorry, there was an error uploading your file.";
//                 	echo json_encode($response);
//                 }
//             }else{
//                 $response['status'] = 400;
//             	$response['error'] = TRUE;
//             	$response['error_msg'] = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
//             	echo json_encode($response);
//             }
//         }


        if($type === "doc"){
            $file_name = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '', $_POST['file_name']));
            $ext = 'pdf';
            if (strpos($file_name, '%PDF') !== 0) {
            $response['status'] = 202;
            $response['error'] = TRUE;
            $response['error_msg'] = "Missing the PDF file signature";
            echo json_encode($response);
            exit();
              //throw new Exception('');
            }
        }elseif($type === "image"){
            $file_name = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['file_name']));
            $size = getImageSizeFromString($file_name);
            if (empty($size['mime']) || strpos($size['mime'], 'image/') !== 0) {
              die('Base64 value is not a valid image');
            }
            $ext = substr($size['mime'], 6);
            if (!in_array($ext, ['png', 'gif', 'jpeg'])) {
              die('Unsupported image type');
            }
        }
        
        $name = "WO-".rand(100000,999999);
        $img_file = "../../public/work_order/$name.{$ext}";
        file_put_contents($img_file, $file_name);
        $finalname = $name.".".$ext;
        $uploadA = $db->uploadAttachment($name, $finalname, $woId);

        if ($uploadA == true) {
            $response['status'] = 200;
            $response['error'] = FALSE;
            $response['error_msg'] = "Attachment uploaded successfully.";
            echo json_encode($response);

        } else {
            $response['status'] = 202;
            $response['error'] = TRUE;
            $response['error_msg'] = "No status found .";
            echo json_encode($response);
        }
} else {
    $response['status'] = 400;
	$response['error'] = TRUE;
	$response['error_msg'] = "Required parameter is missing";
	echo json_encode($response);
}
?>