<?php

/**
 * Logging in with username
 *
 * @author    PrestashopExtensions.com
 * @copyright PrestashopExtensions.com
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_'))
	exit;


class ShippingPro extends Module
{
	public function __construct()
	{
		$this->name						 = 'shippingpro';
		$this->version					 = '1.0.1';
		$this->tab						 = 'front_office_features';
		$this->displayName				 = $this->l('Shipping Pro');
		$this->author					 = 'Howard Yang';

		if (version_compare(_PS_VERSION_, '1.5.0.0 ', '>='))
			$this->multishop_context = Shop::CONTEXT_ALL;

		if (!defined('_PS_ADMIN_DIR_'))
		{
			if (defined('PS_ADMIN_DIR'))
				define('_PS_ADMIN_DIR_', PS_ADMIN_DIR);
			else
				$this->_errors[] = $this->l('This version of PrestaShop cannot be upgraded: the PS_ADMIN_DIR constant is missing.');
		}
		parent::__construct();
		$this->description				 = $this->l('Shipping Pro');
		$this->confirmUninstall			 = $this->l('Do you want to uninstall').' '.$this->displayName.'?';
	}

	public function install()
	{
		if ($id_tab = Tab::getIdFromClassName('AdminShippingPro'))
		{
			$tab = new Tab((int)$id_tab);
			if (!$tab->delete())
				$this->_errors[] = sprintf($this->l('Unable to delete outdated "AdminShippingPro" tab (tab ID: %d).'), (int)$id_tab);
		}

		/* If the "AdminSelfUpgrade" tab does not exist yet, create it */
		if (!$id_tab = Tab::getIdFromClassName('AdminShippingPro'))
		{
			$tab = new Tab();
			$tab->class_name = 'AdminShippingPro';
			$tab->module = 'shippingpro';
			$tab->id_parent = (int)Tab::getIdFromClassName('AdminParentShipping');
			foreach (Language::getLanguages(false) as $lang)
				$tab->name[(int)$lang['id_lang']] = 'Shipping Pro';
			if (!$tab->save())
				return $this->_abortInstall($this->l('Unable to create the "AdminShippingPro" tab'));
		}
		else
			$tab = new Tab((int)$id_tab);

		/* Update the "AdminSelfUpgrade" tab id in database or exit */
		if (Validate::isLoadedObject($tab))
			Configuration::updateValue('PS_SHIPPINGPRO_MODULE_IDTAB', (int)$tab->id);
		else
			return $this->_abortInstall($this->l('Unable to load the "AdminShippingPro" tab'));

		return parent::install() &&
				$this->installTables() &&
				$this->registerHook('displayBeforeCarrier')
				;
	}

	protected function installTables()
	{
		// Db::getInstance()->execute('
		// CREATE TABLE `'._DB_PREFIX_.'shippro_rule_lang` (
		// 	`id_shippro_rule` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		// 	`id_lang` INT UNSIGNED NOT NULL,
		// 	`name` varchar(64) NOT NULL,
		// 	UNIQUE KEY `index_unique_shippro_rule_lang` (`id_shippro_rule`,`id_lang`)
		// ) DEFAULT CHARSET=utf8 ;');


// CREATE TABLE `ps_shippro_type` (
//   `id_shippro_type` INT UNSIGNED NOT NULL AUTO_INCREMENT,
//   PRIMARY KEY (`id_shippro_type`)
// ) DEFAULT CHARSET=utf8;

// CREATE TABLE `ps_shippro_type_lang` (
//   `id_shippro_type` INT UNSIGNED NOT NULL AUTO_INCREMENT,
//   `id_lang` INT UNSIGNED NOT NULL,
//   `name` varchar(64) NOT NULL,
//   UNIQUE KEY `index_unique_shippro_type_lang` (`id_shippro_type`,`id_lang`)
// ) DEFAULT CHARSET=utf8;


// CREATE TABLE `ps_shippro_rule` (
//   `id_shippro_rule` INT UNSIGNED NOT NULL AUTO_INCREMENT,
//   `id_shippro_type` INT UNSIGNED NOT NULL DEFAULT 1,
//   `detail` text,
//   `id_tax_rules_group` INT(10) UNSIGNED DEFAULT 0,
//   `shipping_method` INT(2) UNSIGNED DEFAULT 0,
//   `date_add` DATETIME NOT NULL,
//   `date_upd` DATETIME NOT NULL,
//   PRIMARY KEY (`id_shippro_rule`),
//   INDEX `index_id_shippro_type` (`id_shippro_type`),
//   KEY `id_tax_rules_group` (`id_tax_rules_group`)
// ) DEFAULT CHARSET=utf8;

// CREATE TABLE `ps_shippro_rule_lang` (
//   `id_shippro_rule` INT UNSIGNED NOT NULL AUTO_INCREMENT,
//   `id_lang` INT UNSIGNED NOT NULL,
//   `name` varchar(64) NOT NULL,
//   `description` text,
//   UNIQUE KEY `index_unique_shippro_rule_lang` (`id_shippro_rule`,`id_lang`)
// ) DEFAULT CHARSET=utf8;

// CREATE TABLE `ps_shippro_group` (
//   `id_shippro_group` INT UNSIGNED NOT NULL AUTO_INCREMENT,
//   `id_shippro_rule` INT UNSIGNED NOT NULL DEFAULT 1,
//   `date_add` DATETIME NOT NULL,
//   `date_upd` DATETIME NOT NULL,
//   PRIMARY KEY (`id_shippro_group`),
//   INDEX `index_id_shippro_rule` (`id_shippro_rule`)
// ) DEFAULT CHARSET=utf8;

// CREATE TABLE `ps_shippro_group_lang` (
//   `id_shippro_group` INT UNSIGNED NOT NULL AUTO_INCREMENT,
//   `id_lang` INT UNSIGNED NOT NULL,
//   `name` varchar(64) NOT NULL,
//   UNIQUE KEY `index_unique_shippro_group_lang` (`id_shippro_group`,`id_lang`)
// ) DEFAULT CHARSET=utf8;

// CREATE TABLE `ps_shippro_delivery` (
//   `id_shippro_delivery` int(10) unsigned NOT NULL AUTO_INCREMENT,
//   `id_shop` int(10) unsigned DEFAULT NULL,
//   `id_shop_group` int(10) unsigned DEFAULT NULL,
//   `id_carrier` int(10) unsigned NOT NULL,
//   `id_range_price` int(10) unsigned DEFAULT NULL,
//   `id_range_weight` int(10) unsigned DEFAULT NULL,
//   `id_zone` int(10) unsigned NOT NULL,
//   `price` decimal(20,6) NOT NULL,
//   PRIMARY KEY (`id_delivery`),
//   KEY `id_zone` (`id_zone`),
//   KEY `id_carrier` (`id_carrier`,`id_zone`),
//   KEY `id_range_price` (`id_range_price`),
//   KEY `id_range_weight` (`id_range_weight`)
// ) ENGINE=InnoDB AUTO_INCREMENT=531 DEFAULT CHARSET=utf8;


		
		return true;
	}

	public function uninstall()
	{
		if ($id_tab = Tab::getIdFromClassName('AdminShippingPro'))
		{
			$tab = new Tab((int)$id_tab);
			$tab->delete();
		}

		return parent::uninstall();
	}

	protected function _abortInstall($error)
	{
		if (version_compare(_PS_VERSION_, '1.5.0.0 ', '>='))
			$this->_errors[] = $error;
		else
			echo '<div class="error">'.strip_tags($error).'</div>';

		return false;
	}

	public function hookDisplayBeforeCarrier($params)
	{
		return "yoowerjeiwr";
	}

}