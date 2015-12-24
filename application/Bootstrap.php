<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initAutoLoad()
	{

	}

	protected function _initDbRegistry()
	{
		if ($this->hasPluginResource("db")) {
			$dbResource = $this->getPluginResource("db");
			$db = $dbResource->getDbAdapter();
			Zend_Registry::set("db", $db);
		}
		$config = new Zend_Config($this->getOptions());
		Zend_Registry::set('config', $config);
	}
}

