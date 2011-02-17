<?php

class PostController extends Zend_Controller_Action
{

    public $twitter = null;
		public $cache 	= null;

    public function init()
    {
        $auth = Zend_Auth::getInstance();
				if (!$auth->hasIdentity()) {
						$this->_helper->redirector('index', 'auth');
				}
				
				// Pages under /post/ show twitter information, so we initialize a twitter session here
		
				$config = array(
						'callbackUrl' => 'http://tweets.morph5.com/index.php/post',
						'siteUrl' => 'http://twitter.com/oauth',
						'consumerKey' => 'ervfx4KNBPfH7Zo4H96HA',
						'consumerSecret' => 'OiDQZqJMsMcO1Y4R798eqSDXgq46KRjO9oP47NNkZU0'
				);
				$consumer = new Zend_Oauth_Consumer($config);
				
				if (!isset($_COOKIE['TWITTER_ACCESS_TOKEN']) && empty($this->twitter) && !empty($_GET) && isset($_COOKIE['TWITTER_REQUEST_TOKEN'])) {
						$token = $consumer->getAccessToken(
								 $_GET,
								 unserialize($_COOKIE['TWITTER_REQUEST_TOKEN'])
						 );
						 // We store the access token for 2 hours, enough for a demonstration application
						 setcookie('TWITTER_ACCESS_TOKEN', serialize($token), time()+7200);
						 // Now that we have an Access Token, we can discard the Request Token
						 setcookie('TWITTER_REQUEST_TOKEN', '', time()-7200);
				} elseif (!isset($_COOKIE['TWITTER_ACCESS_TOKEN'])) {
						$token = $consumer->getRequestToken();
						setcookie('TWITTER_REQUEST_TOKEN', serialize($token), time()+7200);
						$consumer->redirect();
				}
				
				$token = unserialize($_COOKIE['TWITTER_ACCESS_TOKEN']);

				$twitteruser = $auth->getIdentity()->twitteruser;
				$this->twitter = new Zend_Service_Twitter(array(
						'username' => $twitteruser,
						'accessToken' => $token
				));

				// Initialize AJAX
				$ajaxContext = $this->_helper->getHelper('AjaxContext');
				$ajaxContext->addActionContext('list', 'html')
										->initContext();
				
				// Initialize cache
				$frontendOptions = array(
					 'lifetime' => 30, // cache lifetime of 30 seconds (testing)
					 'automatic_serialization' => true
				);
				 
				$backendOptions = array(
						'cache_dir' => './tmp/' // Directory where to put the cache files
				);
				 
				// getting a Zend_Cache_Core object
				$this->cache = Zend_Cache::factory('Core',
																					 'File',
																					 $frontendOptions,
																					 $backendOptions);			
		}

    public function indexAction()
    {
        $post = new Application_Model_PostMapper();
                $this->view->entries = $post->fetchAll();
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
		$postMapper = new Application_Model_PostMapper();
		$this->view->entries = $postMapper->searchByUser($userid);
    }

    public function listAction()
    {
				$userid = $this->getRequest()->getParam('userid');
				if (empty($userid)) {
					$auth = Zend_Auth::getInstance();
					$twitteruser = $auth->getIdentity()->twitteruser;
				}
				else {
					$userMapper = new Application_Model_UserMapper();
					$user = $userMapper->getUser($userid);
					$twitteruser = $user->twitteruser;
				}
				if ( ($tweets = $this->cache->load($twitteruser)) === false ) {
						$tweets = $this->twitter->status->userTimeline( array('id'=>$twitteruser) );
						$this->cache->save($tweets, $twitteruser);
				} else {
						print '[[[del cache!!!!!!]]]';
				}
				$this->view->twitteruser = $twitteruser;
				$this->view->tweets = $tweets;
    }


}
