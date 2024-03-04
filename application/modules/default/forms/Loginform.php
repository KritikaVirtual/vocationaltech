<?php
class Form_Loginform extends Zend_Form{

 	 
    // Creating forms
    public function init()
    {
		// remove errors, label and html tags
		$this->setElementDecorators(array(
		    'ViewHelper',		   
		));  
    	
    	$this->addElement('text', 'email', array(
 
 			'required'   => true,
            'filters'    => array('StringTrim'),'class'=>'inp_box',
          	'validators' => array(
						'NotEmpty' 
						),
			'placeholder'=>'Username',
			'autofocus'=>true,			        
        ));
        
        $this->addElement('password', 'password', array(
      	 
 			'required'   => true,
            'filters'    => array('StringTrim'),'class'=>'inp_box',
          	'validators' => array(
						'NotEmpty'
						),
			'placeholder'=>'Password'			        
        ));
         
 
		 
        
        
    }    
    
    function setPlaceholder(){
		$this->email->setAttrib('placeholder','Email');
		$this->password->setAttrib('placeholder','Password');
	}


}

?>
