<?php
if (!defined('_PS_VERSION_'))
	exit;

class CateProduct extends Module
{	
	public function __construct()
	{
		$this->name = 'cateproduct';
		$this->tab = 'others';
		$this->version = '1.0.0';
		$this->author = 'One Yang';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Category Procut');
		$this->description = $this->l('Show Procuts on Category');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
		
		if (!Configuration::get('MYMODULE_NAME'))  
			$this->warning = $this->l('No name provided.');
	}

	public function install()
	{
		if (!parent::install())
			return false;
		return true;
	}
}
