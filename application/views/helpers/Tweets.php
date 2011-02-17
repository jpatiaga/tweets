<?php
class Zend_View_Helper_Tweets extends Zend_View_Helper_Abstract 
{
    public function tweets($userid)
    {
				$html = '<div id="tweets"></div>
						<script type="text/javascript">
						$(document).ready(function() {
								$("#tweets").load("/index.php/post/list/format/html?userid=' . $userid . '");
						});
						</script>';
	
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $username = $auth->getIdentity()->username;
            $logoutUrl = $this->view->url(array('controller'=>'auth',
                'action'=>'logout'), null, true);
            return 'Welcome ' . $username .  '. <a href="'.$logoutUrl.'">Logout</a>';
        } 

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if($controller == 'auth' && $action == 'index') {
            return '';
        }
        $loginUrl = $this->view->url(array('controller'=>'auth', 'action'=>'index'));
        return '<a href="'.$loginUrl.'">Login</a>';
    }
}