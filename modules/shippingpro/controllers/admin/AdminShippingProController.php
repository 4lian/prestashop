<?php

class AdminShippingProController extends ModuleAdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->table = 'shippingpro_carrier';
		$this->className = 'ShippingProCarrier';
		$this->multishop_context = Shop::CONTEXT_ALL;
		$this->lang = true;


		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->_defaultOrderBy = 'id_shippingpro_carrier';

		$this->context = Context::getContext();

		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'confirm' => $this->l('Delete selected items?'),
				'icon' => 'icon-trash'
			)
		);

		$this->fieldImageSettings = array(
			'name' => 'logo',
			'dir' => 's'
		);
		
		$this->fields_list = array(
			'id_shippingpro_carrier' => array(
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
			'delay' => array(
				'title' => $this->l('Delay'),
				'orderby' => false
			),
			'active' => array(
				'title' => $this->l('Status'),
				'align' => 'center',
				'active' => 'status',
				'type' => 'bool',
				'class' => 'fixed-width-sm',
				'orderby' => false,
			),
			'is_free' => array(
				'title' => $this->l('Free Shipping'),
				'align' => 'center',
				'active' => 'isFree',
				'type' => 'bool',
				'class' => 'fixed-width-sm',
				'orderby' => false,
			),
			'position' => array(
				'title' => $this->l('Position'),
				'filter_key' => 'a!position',
				'align' => 'center',
				'class' => 'fixed-width-sm',
				'position' => 'position'
			)
		);
		parent::__construct();
	}

	public function initContent()
	{
		parent::initContent();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryPlugin('typewatch');
		$this->addJs(_PS_MODULE_DIR_.'shippingpro/js/update.js');
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'form' => array(
				'id_form' => 'step_carrier_ranges',
				'input' => array(
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
						'label' => $this->l('Shipping Type'),
						'name' => 'id_shippro_type',
						'options' => array(
							'query' => ShipproType::getShipproType(),
							'id' => 'id_shippro_type',
							'name' => 'name',
							'default' => array(
								'label' => $this->l('請選擇'),
								'value' => 0
							)
						)
					),
					array(
						'type' => 'zone',
						'name' => 'zones'
					)
				),

			));

		$shippro_rule = new ShipproRule();

		$tpl_vars = array();
		$tpl_vars['PS_WEIGHT_UNIT'] = Configuration::get('PS_WEIGHT_UNIT');
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$tpl_vars['currency_sign'] = $currency->sign;

		$fields_value = $this->getStepThreeFieldsValues($shippro_rule);

		$this->getTplRangesVarsAndValues($shippro_rule, $tpl_vars, $fields_value);
		return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value, $tpl_vars);
	}

	public function renderGenericForm($fields_form, $fields_value, $tpl_vars = array())
	{
		$helper = new HelperForm();
		$helper->module = $this;
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
				'id_language' => $this->context->language->id,
				'form_ranges' => _PS_MODULE_DIR_.'shippingpro/views/templates/admin/_configure/helpers/form/form_ranges.tpl',
			), $tpl_vars);
		$helper->override_folder = '_configure/';

		return $helper->generateForm($fields_form);
	}

	public function getStepThreeFieldsValues($shippro_rule)
	{
		$id_tax_rules_group = (is_object($this->object) && !$this->object->id) ? ShipproRule::getIdTaxRulesGroupMostUsed() : $this->getFieldValue($shippro_rule, 'id_tax_rules_group');

		return array(
			'id_tax_rules_group' => (int)$id_tax_rules_group,
			'shipping_method' => $this->getFieldValue($shippro_rule, 'shipping_method'),
			// 'range_behavior' =>  $this->getFieldValue($shippro_rule, 'range_behavior'),
			'zones' =>  $this->getFieldValue($shippro_rule, 'zones'),
		);
	}

	protected function getTplRangesVarsAndValues($carrier, &$tpl_vars, &$fields_value)
	{
		$tpl_vars['zones'] = Zone::getZones(false);
		$carrier_zones = $carrier->getZones();
		$carrier_zones_ids = array();
		if (is_array($carrier_zones))
			foreach ($carrier_zones as $carrier_zone)
				$carrier_zones_ids[] = $carrier_zone['id_zone'];

			$range_table = $carrier->getRangeTable();
		$shipping_method = $carrier->getShippingMethod();

		$zones = Zone::getZones(false);
		foreach ($zones as $zone)
			$fields_value['zones'][$zone['id_zone']] = Tools::getValue('zone_'.$zone['id_zone'], (in_array($zone['id_zone'], $carrier_zones_ids)));

		if ($shipping_method == Carrier::SHIPPING_METHOD_FREE)
		{
			$range_obj = $carrier->getRangeObject($carrier->shipping_method);
			$price_by_range = array();
		}
		else
		{
			$range_obj = $carrier->getRangeObject();
			$price_by_range = Carrier::getDeliveryPriceByRanges($range_table, (int)$carrier->id);
		}

		foreach ($price_by_range as $price)
			$tpl_vars['price_by_range'][$price['id_'.$range_table]][$price['id_zone']] = $price['price'];

		$tmp_range = $range_obj->getRanges((int)$carrier->id);
		$tpl_vars['ranges'] = array();
		if ($shipping_method != Carrier::SHIPPING_METHOD_FREE)
			foreach ($tmp_range as $id => $range)
			{
				$tpl_vars['ranges'][$range['id_'.$range_table]] = $range;
				$tpl_vars['ranges'][$range['id_'.$range_table]]['id_range'] = $range['id_'.$range_table];
			}

		// init blank range
		if (!count($tpl_vars['ranges']))
			$tpl_vars['ranges'][] = array('id_range' => 0, 'delimiter1' => 0, 'delimiter2' => 0);
	}
}
