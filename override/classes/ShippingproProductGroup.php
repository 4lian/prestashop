<?php

class ShippingproProductGroup extends ObjectModel
{
	public $name;

	public $active;

	public static $definition = array(
		'table' => 'shippingpro_product_group',
		'primary' => 'id_shippingpro_product_group',
		'multilang' => true,
		'fields' => array(
			'name' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCarrierName', 'required' => true, 'size' => 64),
			'active' => 	array('type' => self::TYPE_BOOL),
		),
	);
}