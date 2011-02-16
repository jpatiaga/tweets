<?php

class Application_Form_Registration extends Zend_Form
{

    public function init()
    {
        $username = $this->createElement('text','username');
        $username->setLabel('Username: *')
                ->setRequired(true);
								
        $twitteruser = $this->createElement('text','twitteruser');
        $twitteruser->setLabel('Twitter username: *')
                ->setRequired(true);
                
        $password = $this->createElement('password','password');
        $password->setLabel('Password: *')
                ->setRequired(true);
                
        $confirmPassword = $this->createElement('password','confirmPassword');
        $confirmPassword->setLabel('Confirm Password: *')
                ->setRequired(true);
                
        $register = $this->createElement('submit','register');
        $register->setLabel('Sign up')
                ->setIgnore(true);
                
        $this->addElements(array(
                        $username,
												$twitteruser,
                        $password,
                        $confirmPassword,
                        $register
        ));
    }


}

