<?php
class Ve_General_Dynamicforms
{	
public function genrateForm($array,$idPrefix="",$class="",$select="")
{		
	$return="";		 		
	foreach($array as $key){
	trim($key[5]);				
	if($key[0]=='select'){					
	$return .= '<select  alt="'.  $key[2] .'"  name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '" class="' . $select . '">';										    $i=0;							
	foreach($key[3] as $opt=>$val){		
	
	if(empty($key[5]) ){									
	$return .= '<option value="' . (($i++==0) ? '' : $val) . '" label="' . $val . '">' . $val . '</option>';								
	}else{													
	$return .= '<option value="' . (($i++==0) ? '' : $val) . '" ';									
	if($key[5]==$val) $return .=' selected ';									
	$return .= ' label="' . $val . '">' . $val . '</option>';								}																												   		}	
	$return .= '</select>';								
	}else if($key[0]=='meter'){
				  	$txtfldval = explode('-',$key[5]);
					$return .= '<div class="elemtrno"><div class="mtrlable">Enter Your Meter Number (MPAN):</div><div class="mrtclumn">
					<div class="mrtclleft">S</div><div class="mrtclright">
					<div class="mrtcltop">
					<input class="mtxtone" type="' . $key[0] . '"   value="'.$txtfldval[0].'" name="' . $key[1] . '[]" id="' .$idPrefix . $key[1] . '" onkeyup="stringvalidate(this.id)">
					<input class="mtxttwo" type="' . $key[0] . '"   value="'.$txtfldval[1].'" name="' . $key[1] . '[]" id="' .$idPrefix . $key[1] . '" onkeyup="stringvalidate(this.id)">
					<input class="mtxtone" type="' . $key[0] . '"   value="'.$txtfldval[2].'" name="' . $key[1] . '[]" id="' .$idPrefix . $key[1] . '" onkeyup="stringvalidate(this.id)">
					</div>
					<div class="mrtclbottom">
					<input class="mtxtthree" type="' .$key[0] . '" value="'.$txtfldval[3].'" name="' . $key[1] . '[]" id="' .$idPrefix . $key[1] . '" onkeyup="stringvalidate(this.id)">
					<input class="mtxtone" type="' . $key[0] . '"   value="'.$txtfldval[4].'" name="' . $key[1] . '[]" id="' .$idPrefix . $key[1] . '" onkeyup="stringvalidate(this.id)">
					<input class="mtxtone" type="' . $key[0] . '"   value="'.$txtfldval[5].'" name="' . $key[1] . '[]" id="' .$idPrefix . $key[1] . '" onkeyup="stringvalidate(this.id)">
					<input class="mtxtthree" type="' . $key[0] . '" value="'.$txtfldval[6].'" name="' . $key[1] . '[]" id="' .$idPrefix . $key[1] . '" onkeyup="stringvalidate(this.id)">
					</div>
					</div></div></div>';
					
				}else{									
	if(empty($key[5]) ){
	
	$return .= '<input type="' . $key[0] . '" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '" value="' . $key[2] . '"';					
	}else{						
	$return .= '<input type="' . $key[0] . '" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '" value="' . $key[5] . '"';					
	}																
	$return .= ' onfocus="if(this.value == \'' . $key[2] . '\') this.value = \'\'" onblur="if(!this.value) this.value = \'' . $key[2] . '\';" alt="'.  $key[2] .'" class="' . $class . '" />';			
	}			
}						
return $return;	
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
		}}}
		}else if($key[0]=='text'){
	
		if(count($key[4])>0){					
			foreach($key[4] as $val){
				if($val=="trim"){
				
				$return .= 'if($(\'#' .$idPrefix . $key[1] . '\').val()=="" || $(\'#' .$idPrefix . $key[1] . '\').val()=="' . $key[2] . '"){';										$return .= 'alert("Please select ' . $key[2] . ' ");$(\'#' .$idPrefix . $key[1] . '\').focus();return false;';									
				
				$return .= '}';						
					}else if($val=="numeric"){									 									
				$return .= 'if(!CheckUnit($(\'#' .$idPrefix . $key[1] . '\').val())) {';										
				$return .= 'alert("Please eneter ' . $key[2] . ' in numeric ! ");$(\'#' .$idPrefix . $key[1] . '\').focus();return false;';									$return .= '}';						
				}else if($val=="dob" || $val=="date" ){									 									
			$return .= 'if(!isDate($(\'#' .$idPrefix . $key[1] . '\').val())) {';										
			$return .= 'alert("Please eneter ' . $key[2] . ' in dd-mm-yyyy format ");$(\'#' .$idPrefix . $key[1] . '\').focus();return false;';									$return .= '}';						
			}}}}}				
return $return;	
}	
	 
 
	
}

?>
