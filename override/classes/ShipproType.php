<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class ShippingproType extends ObjectModel
{
	public $name;

	public static $definition = array(
		'table' => 'shippingpro_type',
		'primary' => 'id_shippingpro_type',
		'multilang' => true,
		'fields' => array(
			'name' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
		),
	);

	public static function getShippingproType()
	{
		return Db::getInstance()->executeS('
			SELECT DISTINCT s.id_shippingpro_type, l.name
			FROM `'._DB_PREFIX_.'shippingpro_type` s '
			.'INNER JOIN `'._DB_PREFIX_.'shippingpro_type_lang` l ON (s.`id_shippingpro_type` = l.`id_shippingpro_type` AND l.`id_lang` = '.(int)Context::getContext()->language->id.')'
			.'ORDER BY name ASC');
	}

	public static function insertDefaultData()
	{
		return true;
	}
}
