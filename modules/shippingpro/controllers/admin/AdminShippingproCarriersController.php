<?php

class AdminShippingproCarriersController extends ModuleAdminController
{
	protected $position_identifier = 'id_carrier';

	public function __construct()
	{

		// if ($id_carrier = Tools::getValue('id_carrier') && !Tools::isSubmit('deletecarrier') && !Tools::isSubmit('statuscarrier') && !Tools::isSubmit('isFreecarrier') && !Tools::isSubmit('onboarding_carrier'))
		// 	Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminShippingproCarrierWizard').'&id_carrier='.(int)$id_carrier);

		$this->bootstrap = true;
		$this->table = 'carrier';
		$this->className = 'Carrier';
		$this->lang = false;
		$this->deleted = true;

		$this->addRowAction('edit');

		$this->context = Context::getContext();

		$this->fieldImageSettings = array(
			'name' => 'logo',
			'dir' => 's'
		);

		$this->fields_list = array(
			'id_carrier' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'name' => array(
				'title' => $this->l('Name')
			),
			'image' => array(
				'title' => $this->l('Logo'),
				'align' => 'center',
				'image' => 's',
				'class' => 'fixed-width-xs',
				'orderby' => false,
				'search' => false
			),
			'active' => array(
				'title' => $this->l('Status'),
				'align' => 'center',
				'active' => 'status',
				'type' => 'bool',
				'class' => 'fixed-width-sm',
				'orderby' => false,
			)
		);
		parent::__construct();
		
		if (Tools::isSubmit('onboarding_carrier'))
			$this->display = 'view';
	}

	public function initToolbar()
	{
		parent::initToolbar();
		
		if (isset($this->toolbar_btn['new']) && $this->display != 'view')
			$this->toolbar_btn['new']['href'] = $this->context->link->getAdminLink('AdminCarriers').'&onboarding_carrier';
	}

	public function initPageHeaderToolbar()
	{
		$this->page_header_toolbar_title = $this->l('Carriers');
		if ($this->display != 'view')
			$this->page_header_toolbar_btn['new_carrier'] = array(
				'href' => $this->context->link->getAdminLink('AdminCarriers').'&onboarding_carrier',
				'desc' => $this->l('Add new carrier', null, null, false),
				'icon' => 'process-icon-new'
			);

		parent::initPageHeaderToolbar();
	}
	
	// public function renderView()
	// {
	// 	$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
	// 	$this->tpl_view_vars = array(
	// 		'currency_sign' => $currency->sign,
	// 		'PS_WEIGHT_UNIT' => Configuration::get('PS_WEIGHT_UNIT'),
	// 		'item_unit' => 'item',
	// 		'validate_url' => $this->context->link->getAdminLink('AdminShippingproCarrier'),
	// 		'multistore_enable' => Shop::isFeatureActive(),
	// 	);
	// 	return parent::renderView();
	// }
	
	public function renderList()
	{
		$this->_select = 'b.*';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'carrier_lang` b ON a.id_carrier = b.id_carrier'.Shop::addSqlRestrictionOnLang('b').' AND b.id_lang = '.$this->context->language->id.' LEFT JOIN `'._DB_PREFIX_.'carrier_tax_rules_group_shop` ctrgs ON (a.`id_carrier` = ctrgs.`id_carrier` AND ctrgs.id_shop='.(int)$this->context->shop->id.')';
		return parent::renderList();
	}

	public function renderForm()
	{
		if (Tools::getValue('id_carrier') && $this->tabAccess['edit'])
			$carrier = $this->loadObject();

		if ((!$this->tabAccess['edit'] && Tools::getValue('id_carrier')) ||  (!$this->tabAccess['add'] && !Tools::getValue('id_carrier')))
		{
			$this->errors[] = Tools::displayError('You do not have permission to use this wizard.');
			return ;
		}

		$this->addJqueryPlugin('typewatch');
		$this->addJs(_PS_MODULE_DIR_.'shippingpro/js/admin_carrier_wizard.js');

		// $helper = new HelperView();
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$this->tpl_view_vars = array(
			'currency_sign' => $currency->sign,
			'PS_WEIGHT_UNIT' => Configuration::get('PS_WEIGHT_UNIT'),
			'item_unit' => 'item',
			'validate_url' => $this->context->link->getAdminLink('AdminShippingproCarriers'),
			'multistore_enable' => Shop::isFeatureActive(),
			'content' => $this->renderCarrier($carrier),
			'id_carrier' => Tools::getValue('id_carrier')
		);

		return $this->renderView();
	}

	public function renderCarrier($carrier)
	{
		$rules = $carrier->getZoneRules();

		$context = '';
		if (empty($rules)) {
			$context .= $this->renderRuleView($this->getDefaultRule(), 0);
		}
		else {
			foreach ($rules as $key => $rule) {
				$context .= $this->renderRuleView($rule, $key);
			}
		}

		return $context;
	}
	public function getDefaultRule()
	{
		return array(
			'is_free' => false,
			'shipping_handling' => false,
			'shipping_method' => ShippingproRule::SHIPPING_METHOD_QUANTITY,
			'id_zone' => 0,
			'id_country' => 0,
			'zip_code_min' => '',
			'zip_code_max' => '',
			'id_tax_rules_group' => 0,
			'range_behavior' => 0,
			'max_width' => 0,
			'max_height' => 0,
			'max_depth' => 0,
			'max_weight' => 0,
			'ranges' => array(array('lower'=>'', 'upper'=> '', 'price'=>''))
		);
	}

	public function renderRuleView($rule, $index)
	{
		$this->fields_form = array(
			'form' => array(
				'id_form' => 'step_carrier_ranges',
				'legend' => array(       
					'title' => $this->l('Rule'),
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Zone'),
						'name' => 'id_zone',
						'options' => array(
							'query' => Zone::getZones(true),
							'id' => 'id_zone',
							'name' => 'name',
							'default' => array(
								'label' => $this->l('Select Zone'),
								'value' => 0
							)
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Country'),
						'name' => 'id_country',
						'options' => array(
							'query' => Country::getCountries($this->context->language->id, true),
							'id' => 'id_country',
							'name' => 'name',
							'default' => array(
								'label' => $this->l('Select Country'),
								'value' => 0
							)
						)
					),
					array(
						'type' => 'text',
						'label' => $this->l('Min zip code'),
						'name' => 'zip_code_min',
						'required' => false,
						'hint' => $this->l('Min zip code')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Max zip code'),
						'name' => 'zip_code_max',
						'required' => false,
						'hint' => $this->l('Max zip code')
					),
					array(
						'type' => 'text',
						'label' => sprintf($this->l('Maximum package height (%s)'), Configuration::get('PS_DIMENSION_UNIT')),
						'name' => 'max_height',
						'required' => false,
						'hint' => $this->l('Maximum height managed by this carrier. Set the value to "0", or leave this field blank to ignore.').' '.$this->l('The value must be an integer.')
					),
					array(
						'type' => 'text',
						'label' => sprintf($this->l('Maximum package width (%s)'), Configuration::get('PS_DIMENSION_UNIT')),
						'name' => 'max_width',
						'required' => false,
						'hint' => $this->l('Maximum width managed by this carrier. Set the value to "0", or leave this field blank to ignore.').' '.$this->l('The value must be an integer.')
					),
					array(
						'type' => 'text',
						'label' => sprintf($this->l('Maximum package depth (%s)'), Configuration::get('PS_DIMENSION_UNIT')),
						'name' => 'max_depth',
						'required' => false,
						'hint' => $this->l('Maximum depth managed by this carrier. Set the value to "0", or leave this field blank to ignore.').' '.$this->l('The value must be an integer.')
					),
					array(
						'type' => 'text',
						'label' => sprintf($this->l('Maximum package weight (%s)'), Configuration::get('PS_WEIGHT_UNIT')),
						'name' => 'max_weight',
						'required' => false,
						'hint' => $this->l('Maximum weight managed by this carrier. Set the value to "0", or leave this field blank to ignore.')
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Add handling costs'),
						'name' => 'shipping_handling',
						'required' => false,
						'class' => 't',
						'values' => array(
							array(
								'id' => 'shipping_handling_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'shipping_handling_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
						'hint' => $this->l('Include the handling costs (as set in Shipping > Preferences) in the final carrier price.')
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Free shipping'),
						'name' => 'is_free',
						'required' => false,
						'class' => 't',
						'values' => array(
							array(
								'id' => 'is_free_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'is_free_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						)
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Billing'),
						'name' => 'shipping_method',
						'required' => false,
						'class' => 't',
						'br' => true,
						'values' => array(
							array(
								'id' => 'billing_price',
								'value' => ShippingproRule::SHIPPING_METHOD_PRICE,
								'label' => $this->l('According to total price.')
							),
							array(
								'id' => 'billing_weight',
								'value' => ShippingproRule::SHIPPING_METHOD_WEIGHT,
								'label' => $this->l('According to total weight.')
							),
							array(
								'id' => 'billing_quantity',
								'value' => ShippingproRule::SHIPPING_METHOD_QUANTITY,
								'label' => $this->l('According to total quantity.')
							)
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Tax'),
						'name' => 'id_tax_rules_group',
						'options' => array(
							'query' => TaxRulesGroup::getTaxRulesGroups(true),
							'id' => 'id_tax_rules_group',
							'name' => 'name',
							'default' => array(
								'label' => $this->l('No tax'),
								'value' => 0
							)
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Out-of-range behavior'),
						'name' => 'range_behavior',
						'options' => array(
							'query' => array(
								array(
									'id' => 0,
									'name' => $this->l('Apply the cost of the highest defined range')
								),
								array(
									'id' => 1,
									'name' => $this->l('Disable carrier')
								)
							),
							'id' => 'id',
							'name' => 'name'
						),
						'hint' => $this->l('Out-of-range behavior occurs when no defined range matches the customer\'s cart (e.g. when the weight of the cart is greater than the highest weight limit defined by the weight ranges).')
					),
					array(
						'type' => 'zone',
						'name' => 'zones'
					),
				),
				'submit' => array(
					'title' => $this->l('Delete'),
					'class' => 'button'   
				),

			));

		$tpl_vars = array();
		$tpl_vars['PS_WEIGHT_UNIT'] = Configuration::get('PS_WEIGHT_UNIT');
		$tpl_vars['item_unit'] = 'item';
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$tpl_vars['currency_sign'] = $currency->sign;

		$fields_value = $rule;

		$tpl_vars['ranges'] = $rule['ranges'];

		return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value, $tpl_vars);
	}

	public function getRuleFieldsValues($rule)
	{
		$id_tax_rules_group = (is_object($this->object) && !$this->object->id) ? Carrier::getIdTaxRulesGroupMostUsed() : $this->getFieldValue($rule, 'id_tax_rules_group');

		$shipping_handling = (is_object($this->object) && !$this->object->id) ? 0 : $this->getFieldValue($rule, 'shipping_handling');

		return array(
			// 'is_free' => $this->getFieldValue($rule, 'is_free'),
			'id_tax_rules_group' => (int)$id_tax_rules_group,
			'shipping_handling' => $shipping_handling,
			'shipping_method' => $this->getFieldValue($rule, 'shipping_method'),
			'id_zone' =>  $this->getFieldValue($rule, 'id_zone'),
			'id_country' =>  $this->getFieldValue($rule, 'id_country'),
			'min_zip_code' =>  $this->getFieldValue($rule, 'min_zip_code'),
			'max_zip_code' =>  $this->getFieldValue($rule, 'max_zip_code'),
			'max_height' =>  $this->getFieldValue($rule, 'max_height'),
			'max_width' =>  $this->getFieldValue($rule, 'max_width'),
			'max_depth' =>  $this->getFieldValue($rule, 'max_depth'),
			'max_weight' =>  $this->getFieldValue($rule, 'max_weight'),
			'range_behavior' =>  $this->getFieldValue($rule, 'range_behavior'),
		);
	}

	public function ajaxProcessNewRule()
	{
		die($this->renderRuleView($this->getDefaultRule(), 0));
	}

	public function renderGenericForm($fields_form, $fields_value, $tpl_vars = array())
	{
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->tpl_vars = array_merge(array(
				'fields_value' => $fields_value,
				'languages' => $this->getLanguages(),
				'id_language' => $this->context->language->id
			), $tpl_vars);
		$helper->override_folder = 'shippingpro_carriers/';

		return $helper->generateForm($fields_form);
	}

	public function ajaxProcessFinish()
	{
		$return = array('has_error' => false);
		if (!$this->tabAccess['edit'])
			$return = array(
				'has_error' =>  true,
				$return['errors'][] = Tools::displayError('You do not have permission to use this wizard.')
			);
		else
		{	
			if ($id_carrier = Tools::getValue('id_carrier'))
			{
				$carrier = new Carrier((int)$id_carrier);
			}
			else
			{
				$return['has_error'] = true;
				$return['errors'][] = $this->l('No carrier');
			}

			if (Validate::isLoadedObject($carrier))
			{
				if (!$this->processRanges((int)$carrier->id))
				{
					$return['has_error'] = true;
					$return['errors'][] = $this->l('An error occurred while saving carrier ranges.');
				}
				$return['id_carrier'] = $carrier->id;
			}
		}
		die(Tools::jsonEncode($return));
	}

	public function processRanges($id_carrier)
	{
		if (!$this->tabAccess['edit'] || !$this->tabAccess['add'])
		{
			$this->errors[] = Tools::displayError('You do not have permission to use this wizard.');
			return;
		}

		$carrier = new Carrier((int)$id_carrier);
		if (!Validate::isLoadedObject($carrier))
			return false;

		$carrier->deleteRules();

		if (Tools::getValue('is_free')) {
			return true;
		}

		foreach (Tools::getValue('rule_form') as $key => $rule) {
			$new_rule = new ShippingproRule();
			if (array_key_exists('range_inf', $rule)) {
				$ranges = array();
				$count = count($rule['range_inf']);
				for ($i=0; $i < $count; $i++) { 
					$ranges[] = array(
						'lower' => $rule['range_inf'][$i],
						'upper' => $rule['range_sup'][$i],
						'price' => $rule['fees'][$i]
					);
				}
				$rule['ranges'] = serialize($ranges);
			}
			$new_rule->copyFromData($rule);
			$new_rule->id_carrier = $id_carrier;
			$new_rule->add();
		}

		return true;
	}

	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		foreach ($this->_list as $key => $list)
			if ($list['name'] == '0')
				$this->_list[$key]['name'] = Configuration::get('PS_SHOP_NAME');
	}

	
	protected function initTabModuleList()
	{
		if (Tools::isSubmit('onboarding_carrier'))
		{
			parent::initTabModuleList();
			$this->filter_modules_list = $this->tab_modules_list['default_list'] = $this->tab_modules_list['slider_list'];
		}
	}
}
