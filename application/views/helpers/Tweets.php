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
	
        return $html;
    }
}