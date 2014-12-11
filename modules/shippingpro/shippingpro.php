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
	public $carrier_list;

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

	public function hookDisplayAdminProductsExtra($params)
	{
		$id_product = Tools::getValue('id_product');
		$product = new Product((int)$id_product);

		$this->loadCarriers();

		$product_item = array(
			'id_product_attribute' => 0,
			'name' => 'Main',
			'carriers' => $this->getProductCarriers($product)
		);

		$this->getProductAttributeCarriers($product);

		$product_and_attribute = array_merge(array($product_item), $this->getProductAttributeCarriers($product));


		if (Validate::isLoadedObject($product)) {
			$this->context->smarty->assign(array(
				'js_link' => $this->_path.'js/shippingpro_product.js',
				'attribute_list' => $product_and_attribute,
				'languages' => $this->context->controller->_languages,
				'default_language' => (int)Configuration::get('PS_LANG_DEFAULT')
			));
			return $this->display(__FILE__, 'views/sample.tpl');
		}
		return '';
	}
	public function hookActionAdminControllerSetMedia($params)
	{
	}

	protected function loadCarriers()
	{
		$carrier_list = Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS);
		$collect_carrier_list = array();

		foreach ($carrier_list as $value) {
			$collect_carrier_list[] = array(
				'id_carrier' => $value['id_carrier'],
				'id_reference' => $value['id_reference'],
				'name' => $value['name'],
			);
		}
		$this->carrier_list = $collect_carrier_list;
	}

	protected function getProductCarriers($product)
	{
		$carrier_list = $this->carrier_list;
		$carrier_selected_list = $this->queryProductCarriers($product);
		foreach ($carrier_list as &$carrier)
			foreach ($carrier_selected_list as $carrier_selected)
				if ($carrier_selected['id_reference'] == $carrier['id_reference'])
				{
					$carrier['selected'] = true;
					continue;
				}
		return $carrier_list;
	}

	public function queryProductCarriers($product)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.*
			FROM `'._DB_PREFIX_.'shippingpro_carrier_combination` cc
			INNER JOIN `'._DB_PREFIX_.'carrier` c
				ON (c.`id_reference` = cc.`id_carrier_reference` AND c.`deleted` = 0)
			WHERE cc.`id_product` = '.(int)$product->id);
	}

	public function getProductAttributeCarriers($product)
	{
		$combinations = $product->getAttributeCombinations($this->context->language->id);
		$groups = array();
		$comb_array = array();
		if (is_array($combinations))
		{
			foreach ($combinations as $k => $combination)
			{
				$price_to_convert = Tools::convertPrice($combination['price'], $currency);
				$price = Tools::displayPrice($price_to_convert, $currency);

				$comb_array[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
				$comb_array[$combination['id_product_attribute']]['attributes'][] = array($combination['group_name'], $combination['attribute_name'], $combination['id_attribute']);
				$comb_array[$combination['id_product_attribute']]['default_on'] = $combination['default_on'];
				if ($combination['is_color_group'])
					$groups[$combination['id_attribute_group']] = $combination['group_name'];
			}
		}

		$irow = 0;
		if (isset($comb_array))
		{
			foreach ($comb_array as $id_product_attribute => $product_attribute)
			{
				$list = '';
				asort($product_attribute['attributes']);

				foreach ($product_attribute['attributes'] as $attribute)
					$list .= $attribute[0].' - '.$attribute[1].', ';

				$list = rtrim($list, ', ');
				$comb_array[$id_product_attribute]['attributes'] = $list;
				$comb_array[$id_product_attribute]['name'] = $list;

				if ($product_attribute['default_on'])
					$comb_array[$id_product_attribute]['class'] = $default_class;
			}
		}

		$collect_id_attribute = array();
		foreach ($comb_array as $value) {
			$collect_id_attribute[] = $value['id_product_attribute'];
		}

		$carrier_selected_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.id_reference, cc.id_product_attribute 
			FROM `'._DB_PREFIX_.'shippingpro_carrier_combination` cc
			INNER JOIN `'._DB_PREFIX_.'carrier` c
				ON (c.`id_reference` = cc.`id_carrier_reference` AND c.`deleted` = 0)
			WHERE cc.`id_product_attribute` IN ('.implode(',', $collect_id_attribute).')');

		foreach ($comb_array as &$row) {
			$carrier_list = $this->carrier_list;
			foreach ($carrier_list as &$carrier)
				foreach ($carrier_selected_list as $carrier_selected)
					if (($carrier_selected['id_product_attribute'] == $row['id_product_attribute']) && ($carrier_selected['id_reference'] == $carrier['id_reference']))
					{
						$carrier['selected'] = true;
						continue;
					}
			$row['carriers'] = $carrier_list;
		}

		return $comb_array;
	}

	public function hookActionProductUpdate($params)
	{

	}
}