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





