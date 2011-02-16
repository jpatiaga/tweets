<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initDoctype()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->doctype('XHTML1_STRICT');
		$view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
	}

	protected function _initAutoload()
	{
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Application',
			'basePath' => APPLICATION_PATH
		));
		return $loader;
	}

}

