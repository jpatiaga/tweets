<?php

class PostController extends Zend_Controller_Action
{

    public $twitter = null;

    public $cache = null;

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
        					 'lifetime' => 20, // cache lifetime of 20 seconds (testing)
        					 'automatic_serialization' => true
        				);
        				 
        				$backendOptions = array(
        						'cache_dir' => '/tmp/' // Directory where to put the cache files
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
        // get twitter username based on userid
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
        
        				// restore tweets from cache if they exist
        				if ( ($tweets = $this->cache->load($twitteruser)) === false ) {
        						$tweets_object = $this->twitter->status->userTimeline( array('id'=>$twitteruser) );
        						$tweets = array();
        						foreach ($tweets_object as $tweet_object) {
        							$tweet = new stdClass();
        							$tweet->id = (string)$tweet_object->id;
        							$tweet->text = (string)$tweet_object->text;
        							$tweet->created_at = (string)$tweet_object->created_at;
        							$tweet->profile_image_url = (string)$tweet_object->user->profile_image_url;
        							$tweets[$tweet->id] = $tweet;
        						}
        						$this->cache->save(serialize($tweets), $twitteruser);
        				} else {
        						$tweets = unserialize($tweets);
        				} $tweets = $this->twitter->status->userTimeline( array('id'=>$twitteruser) );
        				
        				$tweets = $this->_filterTweets($tweets);
        				$this->view->twitteruser = $twitteruser;
        				$this->view->tweets = $tweets;
    }

	// hideAction() - this is called with a get id parameter to hide that twitter status id
    public function hideAction()
    {
		$status_id = $this->getRequest()->getParam('id');

		$data = array('id' => $status_id,);
        $hiddentweet = new Application_Model_Hiddentweets($data);
		$mapper = new Application_Model_HiddentweetsMapper();

		$mapper->save($hiddentweet);
    }

	// _filterTweets($tweets) - removes hidden tweets from $tweets array
    private function _filterTweets($tweets)
    {
		$mapper = new Application_Model_HiddentweetsMapper();
        foreach ($tweets as $tweet) {
			
		}
		return $tweets;
    }

}


