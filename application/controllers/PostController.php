<?php

class PostController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $post = new Application_Model_PostMapper();
        $this->view->entries = $post->fetchAll();
				
				$config = array(
						'callbackUrl' => 'http://tweets.morph5.com/index.php/post',
						'siteUrl' => 'http://twitter.com/oauth',
						'consumerKey' => 'ervfx4KNBPfH7Zo4H96HA',
						'consumerSecret' => 'OiDQZqJMsMcO1Y4R798eqSDXgq46KRjO9oP47NNkZU0'
				);
				$consumer = new Zend_Oauth_Consumer($config);
				
				if (!empty($_GET) && isset($_SESSION['TWITTER_REQUEST_TOKEN'])) {
						$token = $consumer->getAccessToken(
                 $_GET,
                 unserialize($_SESSION['TWITTER_REQUEST_TOKEN'])
             );
						 $_SESSION['TWITTER_ACCESS_TOKEN'] = serialize($token);
						 
						 // Now that we have an Access Token, we can discard the Request Token
						 $_SESSION['TWITTER_REQUEST_TOKEN'] = null;
				} elseif (!isset($_SESSION['TWITTER_ACCESS_TOKEN'])) {
						$token = $consumer->getRequestToken();
						$consumer->redirect();
				} else {
						// Mistaken request? Some malfeasant trying something?
						exit('Invalid callback request. Oops. Sorry.');
				}
				
				$token = unserialize($_SESSION['TWITTER_ACCESS_TOKEN']);
				
				$twitter = new Zend_Service_Twitter(array(
						'username' => 'jpatiaga',
						'accessToken' => $token
				));
				
				$response = $twitter->account->verifyCredentials();
				print var_dump($response);
    }

    public function signAction()
    {
        $request = $this->getRequest();
                $form    = new Application_Form_Post();
         
                if ($this->getRequest()->isPost()) {
                    if ($form->isValid($request->getPost())) {
                        $post = new Application_Model_Post($form->getValues());
                        $mapper  = new Application_Model_PostMapper();
                        $mapper->save($post);
                        return $this->_helper->redirector('index');
                    }
                }
         
                $this->view->form = $form;
    }

    public function userpostsAction()
    {
        $userid = $this->getRequest()->getParam('userid');
				$selecteduser = new Application_Model_UserMapper();
				$this->view->username = $selecteduser->usernameById($userid);
				$post = new Application_Model_PostMapper();
        $this->view->entries = $post->searchByUser($userid);
    }


}





