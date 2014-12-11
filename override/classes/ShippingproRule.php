<?php

class ShippingproRule extends ObjectModel
{
	const SHIPPING_METHOD_DEFAULT = 0;
	const SHIPPING_METHOD_WEIGHT = 1;
	const SHIPPING_METHOD_PRICE = 2;
	const SHIPPING_METHOD_FREE = 3;
	const SHIPPING_METHOD_QUANTITY = 4;

	const SORT_BY_PRICE = 0;
	const SORT_BY_POSITION = 1;

	const SORT_BY_ASC = 0;
	const SORT_BY_DESC = 1;

	public $id_carrier;

	public $id_zone;

	public $id_country;

	public $zip_code_min;

	public $zip_code_max;

	public $id_tax_rules_group;

	public $shipping_handling;

	public $handling_fee;

	public $range_behavior;

	public $is_free;

	public $shipping_method;

	public $max_width;

	public $max_height;

	public $max_depth;

	public $max_weight;

	public $ranges;

	public $description;


	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'shippingpro_rule',
		'primary' => 'id_shippingpro_rule',
		'multilang' => false,
		'multilang_shop' => false,
		'fields' => array(
			'id_shippingpro_rule' => 	array('type' => self::TYPE_INT),
			'id_carrier' =>				array('type' => self::TYPE_INT),
			'id_zone' => 				array('type' => self::TYPE_INT),
			'id_country' => 			array('type' => self::TYPE_INT),
			'zip_code_min' => 			array('type' => self::TYPE_INT),
			'zip_code_max' => 			array('type' => self::TYPE_INT),
			'id_tax_rules_group' => 	array('type' => self::TYPE_INT),
			'shipping_handling' => 		array('type' => self::TYPE_INT),
			'handling_fee' => 			array('type' => self::TYPE_INT),
			'range_behavior' => 		array('type' => self::TYPE_INT),
			'is_free' => 				array('type' => self::TYPE_INT),
			'shipping_method' => 		array('type' => self::TYPE_INT),
			'max_width' => 				array('type' => self::TYPE_INT),
			'max_height' => 			array('type' => self::TYPE_INT),
			'max_depth' => 				array('type' => self::TYPE_INT),
			'max_weight' => 			array('type' => self::TYPE_FLOAT),
			'ranges' => 				array('type' => self::TYPE_STRING),
			'description' => 			array('type' => self::TYPE_STRING),
		),
	);

	public function __construct($id = null, $id_lang = null)
	{
		parent::__construct($id, $id_lang);

		if ($this->shipping_method == ShippingproRule::SHIPPING_METHOD_DEFAULT)
			$this->shipping_method = ((int)Configuration::get('PS_SHIPPING_METHOD') ? ShippingproRule::SHIPPING_METHOD_WEIGHT : ShippingproRule::SHIPPING_METHOD_PRICE);

		if ($this->id)
			$this->id_tax_rules_group = $this->getIdTaxRulesGroup(Context::getContext());
	}

	public function copyFromData($data)
	{
		$table = $this->definition['table'];
		foreach ($data as $key => $value)
			if (array_key_exists($key, $this) && $key != 'id_'.$table)
			{
				$this->{$key} = $value;
			}

		/* Multilingual fields */
		// $languages = Language::getLanguages(false);
		// $fields = $this->definition['fields'];

		// foreach ($fields as $field => $params) {
		// 	if (array_key_exists('lang', $params) && $params['lang']) {
		// 		foreach ($languages as $language) {
		// 			if (isset($data[$field.'_'.(int)$language['id_lang']])) {
		// 				$this->{$field}[(int)$language['id_lang']] = $data[$field.'_'.(int)$language['id_lang']];
		// 			}
		// 		}
		// 	}
		// }
	}

	public function add($autodate = true, $null_values = false)
	{
		if (!parent::add($autodate, $null_values) || !Validate::isLoadedObject($this))
			return false;

		return true;
	}

	/**
	 * @since 1.5.0
	 * @see ObjectModel::delete()
	 */
	public function delete()
	{
		if (!parent::delete())
			return false;
		
	}
}
