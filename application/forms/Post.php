<?php

class Application_Form_Post extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
 
		$auth = Zend_Auth::getInstance();
		$user = $auth->getIdentity();
        $this->addElement('hidden', 'userid', array(
            'value'      => $user->id,
        ));
 
        $this->addElement('textarea', 'post', array(
            'label'      => 'Your post:',
            'required'   => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )
        ));
 
        // Add a captcha
        $this->addElement('captcha', 'captcha', array(
            'label'      => 'Please enter the 5 letters displayed below:',
            'required'   => true,
            'captcha'    => array(
                'captcha' => 'Figlet',
                'wordLen' => 5,
                'timeout' => 300
            )
        ));
 
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Post',
        ));
 
        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
    }


}

