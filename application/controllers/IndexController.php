<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
        $post = new Application_Model_PostMapper();
        $this->view->entries = $post->fetchAll();
    }


}

