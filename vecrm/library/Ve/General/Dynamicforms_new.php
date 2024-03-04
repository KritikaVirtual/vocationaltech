<?php

class Ve_General_Dynamicforms
{	
public function genrateForm($array,$idPrefix="",$class="",$select=""){	
	$returnvar=''; 
	//echo '<pre>';print_r($array);die;
		foreach($array as $key){
				if($key[0]=='select'){
					$returnvar .= '<select name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '" class="' . $select . '">';
							$i=0;
							foreach($key[3] as $opt=>$val){
								if(empty($key[5]) ){
										$returnvar .= '<option value="' . (($i++==0) ? '' : $val) . '" label="' . $val . '">' . $val . '</option>';
										}else{
										$returnvar .= '<option value="' . (($i++==0) ? '' : $val) . '" ';
										if($key[5]==$val) $returnvar .=' selected ';
										$returnvar .= ' label="' . $val . '">' . $val . '</option>';
								}
						   }
				  $returnvar .= '</select>';
				
				}else if($key[0]=='meter'){
				  	$txtfldval = explode('-',$key[5]);
					$returnvar .= '<div class="elemtrno"><div class="mtrlable">Enter Your Meter Number (MPAN):</div><div class="mrtclumn">
					<div class="mrtclleft">S</div><div class="mrtclright">
					<div class="mrtcltop">
					<input class="mtxtone" type="' . $key[0] . '"   value="'.$txtfldval[0].'" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '">
					<input class="mtxttwo" type="' . $key[0] . '"   value="'.$txtfldval[1].'" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '">
					<input class="mtxtone" type="' . $key[0] . '"   value="'.$txtfldval[2].'" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '">
					</div>
					<div class="mrtclbottom">
					<input class="mtxtthree" type="' . $key[0] . '" value="'.$txtfldval[3].'" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '">
					<input class="mtxtone" type="' . $key[0] . '"   value="'.$txtfldval[4].'" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '">
					<input class="mtxtone" type="' . $key[0] . '"   value="'.$txtfldval[5].'" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '">
					<input class="mtxtthree" type="' . $key[0] . '" value="'.$txtfldval[6].'" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '">
					</div>
					</div></div></div>';
					
				}else{
				
					if(empty($key[5]) ){
						$returnvar .= '<input type="' . $key[0] . '" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '" value="' . $key[2] . '"';
						}else{
						$returnvar .= '<input type="' . $key[0] . '" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '" value="' . $key[5] . '"';
					}
				}
			}
			//echo '<pre>';print_r($returnvar);exit;
			return $returnvar;
			
	}
	
	 
	
 public function genrateJqueryValidation($array,$idPrefix=""){
  $return="";
  foreach($array as $key){
   
   if($key[0]=='select'){
    if(count($key[4])>0){
     foreach($key[4] as $val){
      if($val=="trim"){
         $return .= 'if($(\'#' .$idPrefix . $key[1] . '\').val()=="" ){';
          $return .= 'alert("Please select ' . $key[2] . ' ");$(\'#' .$idPrefix . $key[1] . '\').focus();return false;';
         $return .= '}';
      }
     }
    }
   }else if($key[0]=='text'){
    
    if(count($key[4])>0){
     foreach($key[4] as $val){
      if($val=="trim"){
         $return .= 'if($(\'#' .$idPrefix . $key[1] . '\').val()=="" || $(\'#' .$idPrefix . $key[1] . '\').val()=="' . $key[2] . '"){';
          $return .= 'alert("Please select ' . $key[2] . ' ");$(\'#' .$idPrefix . $key[1] . '\').focus();return false;';
         $return .= '}';
      }else if($val=="numeric"){
          

         $return .= 'if(!CheckUnit($(\'#' .$idPrefix . $key[1] . '\').val())) {';
          $return .= 'alert("Please eneter ' . $key[2] . ' in numeric ! ");$(\'#' .$idPrefix . $key[1] . '\').focus();return false;';
         $return .= '}';
      }else if($val=="dob" || $val=="date" ){
          
         $return .= 'if(!isDate($(\'#' .$idPrefix . $key[1] . '\').val())) {';
          $return .= 'alert("Please eneter ' . $key[2] . ' in dd-mm-yyyy format ");$(\'#' .$idPrefix . $key[1] . '\').focus();return false;';
         $return .= '}';
      }
     }    
    
    }

   }

  }
  
  return $return;

 }


	

}



?>

