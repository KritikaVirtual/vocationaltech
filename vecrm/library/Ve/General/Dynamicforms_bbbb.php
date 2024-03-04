<?php

class Ve_General_Dynamicforms
{

	
public function genrateForm($array,$idPrefix="",$class="",$select=""){	 
	//echo '<pre>';print_r($array);
		foreach($array as $key){
				if($key[0]=='select'){
					echo '<select name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '" class="' . $select . '">';
							$i=0;
							foreach($key[3] as $opt=>$val){
								echo '<option value="' . (($i++==0) ? '' : $val) . '" label="' . $val . '">' . $val . '</option>';
						   }
				   echo '</select>';
				
				}else if($key[0]=='meter')
				{
					echo '<input type="' . $key[0] . '" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '" value="' . (($key[5]!='') ? $key[5] : $key[2]) . '"';
					echo ' onfocus="if(this.value == \'' . $key[2] . '\') this.value = \'\'" onblur="if(!this.value) this.value = \'' . $key[2] . '\';"  class="' . $class . '" />';
					
				}else{
				
					echo '<input type="' . $key[0] . '" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '" value="' . (($key[5]!='') ? $key[5] : $key[2]) . '"';
					echo ' onfocus="if(this.value == \'' . $key[2] . '\') this.value = \'\'" onblur="if(!this.value) this.value = \'' . $key[2] . '\';"  class="' . $class . '" />';
				}
			}
	}
	
	public function editGenrateForm($array,$idPrefix="",$class="",$select=""){	 
		
		foreach($array as $key){
				if($key[0]=='select'){
					echo '<select name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '" class="' . $select . '">';
							$i=0;
							foreach($key[3] as $opt=>$val){
								echo '<option value="' . (($i++==0) ? '' : $val) . '" label="' . $val . '"  '.(($key[5]==$val) ?"selected=selected" : '').'>' . $val . '</option>';
						   }
				   echo '</select>';
				
				}else if($key[0]=='meter')
				{
					echo '<input type="' . $key[0] . '" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '" value="' . (($key[5]!='') ? $key[5] : $key[2]) . '"';
					echo ' onfocus="if(this.value == \'' . $key[2] . '\') this.value = \'\'" onblur="if(!this.value) this.value = \'' . $key[2] . '\';"  class="' . $class . '" />';
					
				}else{
				
					echo '<input type="' . $key[0] . '" name="' . $key[1] . '" id="' .$idPrefix . $key[1] . '" value="' . (($key[5]!='') ? $key[5] : $key[2]) . '"';
					echo ' onfocus="if(this.value == \'' . $key[2] . '\') this.value = \'\'" onblur="if(!this.value) this.value = \'' . $key[2] . '\';"  class="' . $class . '" />';
				}
			}
	}
	
	public function genrateJqueryValidation($array,$idPrefix=""){
		foreach($array as $key){
			
			if($key[0]=='select'){
				if(count($key[4])>0){
					foreach($key[4] as $val){
						if($val=="trim"){
									echo 'if($(\'#' .$idPrefix . $key[1] . '\').val()=="" ){';
										echo 'alert("Please select ' . $key[2] . ' ");$(\'#' .$idPrefix . $key[1] . '\').focus();return false;';
									echo '}';
						}
					}
				}
			}else if($key[0]=='text'){
				
				if(count($key[4])>0){
					foreach($key[4] as $val){
						if($val=="trim"){
									echo 'if($(\'#' .$idPrefix . $key[1] . '\').val()=="" || $(\'#' .$idPrefix . $key[1] . '\').val()=="' . $key[2] . '"){';
										echo 'alert("Please select ' . $key[2] . ' ");$(\'#' .$idPrefix . $key[1] . '\').focus();return false;';
									echo '}';
						}else if($val=="numeric"){
									 

									echo 'if(!$(\'#' .$idPrefix . $key[1] . '\').val().match(/^\d+$/)) {';
										echo 'alert("Please eneter ' . $key[2] . ' in numeric ! ");$(\'#' .$idPrefix . $key[1] . '\').focus();return false;';
									echo '}';
						}else if($val=="dob"){
									 
/*
									echo 'if(!$(\'#' .$idPrefix . $key[1] . '\').val().match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)) {';
										echo 'alert("Please eneter ' . $key[2] . ' in dd-mm-yyyy format ! ");$(\'#' .$idPrefix . $key[1] . '\').focus();return false;';
									echo '}';*/
						}
					}				
				
				}

			}

		}

	}	


	

}



?>

