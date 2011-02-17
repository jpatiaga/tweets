<?php
class Zend_View_Helper_Tweets extends Zend_View_Helper_Abstract 
{
    public function tweets($userid)
    {
				$html = '<div id="tweets"></div>
						<script type="text/javascript">
						function loadTweets() {
								$("#tweets").load("/index.php/post/list/format/html?userid=' . $userid . '");
								setTimeout("loadTweets()", 60000);
						}
						$(document).ready(function() {
								loadTweets();
						});
						</script>';
	
        return $html;
    }
}