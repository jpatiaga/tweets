<?php

class PostController extends Zend_Controller_Action
{
		public $twitter;

    public function init()
    {
        $config = array(
						'callbackUrl' => 'http://tweets.morph5.com/index.php/post',
						'siteUrl' => 'http://twitter.com/oauth',
						'consumerKey' => 'ervfx4KNBPfH7Zo4H96HA',
						'consumerSecret' => 'OiDQZqJMsMcO1Y4R798eqSDXgq46KRjO9oP47NNkZU0'
				);
				$consumer = new Zend_Oauth_Consumer($config);
				
				print var_dump($_COOKIE);
				
				if (!empty($_GET) && isset($_COOKIE['TWITTER_REQUEST_TOKEN']) && !empty($_COOKIE['TWITTER_REQUEST_TOKEN'])) {
						$token = $consumer->getAccessToken(
                 $_GET,
                 unserialize($_COOKIE['TWITTER_REQUEST_TOKEN'])
             );
						 setcookie('TWITTER_ACCESS_TOKEN', serialize($token), time()+7200);
						 // Now that we have an Access Token, we can discard the Request Token
						 setcookie('TWITTER_REQUEST_TOKEN', '', time()-7200);
				} elseif (!isset($_COOKIE['TWITTER_ACCESS_TOKEN'])) {
						$token = $consumer->getRequestToken();
						setcookie('TWITTER_REQUEST_TOKEN', serialize($token), time()+7200);
						$consumer->redirect();
				}
				
				$token = unserialize($_COOKIE['TWITTER_ACCESS_TOKEN']);
				print '[pasoooooooooooooo]';
				$this->twitter = new Zend_Service_Twitter(array(
						'username' => 'jpatiaga',
						'accessToken' => $token
				));
    }

    public function indexAction()
    {
        $post = new Application_Model_PostMapper();
        $this->view->entries = $post->fetchAll();
				
				$response = $this->twitter->status->userTimeline();
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





