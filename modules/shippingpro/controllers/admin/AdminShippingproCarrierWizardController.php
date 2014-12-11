<?php

class AdminShippingproCarrierWizardController extends ModuleAdminController
{
	protected $wizard_access;

	public function __construct()
	{
		$this->bootstrap = true;
		$this->display = 'view';
		$this->table = 'carrier';
		$this->identifier = 'id_shippingpro_carrier';
		$this->className = 'ShippingproCarrier';
		$this->lang = false;
		$this->deleted = true;
		$this->step_number = 0;
		
		$this->multishop_context = Shop::CONTEXT_ALL;
		$this->context = Context::getContext();
		
		$this->fieldImageSettings = array(
			'name' => 'logo',
			'dir' => 's'
		);

		parent::__construct();

		$this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminShippingproCarriers'));
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryPlugin('smartWizard');
		$this->addJqueryPlugin('typewatch');
		$this->addJs(_PS_MODULE_DIR_.'shippingpro/js/admin_carrier_wizard.js');
	}

	public function initWizard()
	{
		$this->wizard_steps = array(
			'name' => 'carrier_wizard',
			'steps' => array(
				array(
					'title' => $this->l('General settings'),
				),
				array(
					'title' => $this->l('Shipping locations and costs'),
				),
				array(
					'title' => $this->l('Size, weight, and group access'),
				),
				array(
					'title' => $this->l('Summary'),
				),

			));

		if (Shop::isFeatureActive())
		{
			$multistore_step = array(
				array(
					'title' => $this->l('MultiStore'),
				)
			);
			array_splice($this->wizard_steps['steps'], 1, 0, $multistore_step);
		}
	}

	public function renderView()
	{
		$this->initWizard();

		if (Tools::getValue('id_shippingpro_carrier') && $this->tabAccess['edit'])
			$carrier = $this->loadObject();
		elseif ($this->tabAccess['add'])
			$carrier = new ShippingproCarrier();

		if ((!$this->tabAccess['edit'] && Tools::getValue('id_shippingpro_carrier')) ||  (!$this->tabAccess['add'] && !Tools::getValue('id_shippingpro_carrier')))
		{
			$this->errors[] = Tools::displayError('You do not have permission to use this wizard.');
			return ;
		}
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$this->tpl_view_vars = array(
			'currency_sign' => $currency->sign,
			'PS_WEIGHT_UNIT' => Configuration::get('PS_WEIGHT_UNIT'),
			'item_unit' => 'item',
			'enableAllSteps' => Validate::isLoadedObject($carrier),
			'wizard_steps' => $this->wizard_steps,
			'validate_url' => $this->context->link->getAdminLink('AdminShippingproCarrierWizard'),
			'carrierlist_url' => $this->context->link->getAdminLink('AdminShippingproCarriers').'&conf='.((int)Validate::isLoadedObject($carrier) ? 4 : 3),
			'multistore_enable' => Shop::isFeatureActive(),
			'wizard_contents' => array(
				'contents' => array(
					0 => $this->renderStepOne($carrier),
					1 => $this->renderStepThree($carrier),
					2 => $this->renderStepFour($carrier),
					3 => $this->renderStepFive($carrier),
				)),
			'labels' => array('next' => $this->l('Next'), 'previous' => $this->l('Previous'), 'finish' => $this->l('Finish'))
		);


		if (Shop::isFeatureActive())
			array_splice($this->tpl_view_vars['wizard_contents']['contents'], 1, 0, array(0 => $this->renderStepTwo($carrier)));

		$this->context->smarty->assign(array(
				'carrier_logo' => (Validate::isLoadedObject($carrier) && file_exists(_PS_SHIP_IMG_DIR_.$carrier->id.'.jpg') ? _THEME_SHIP_DIR_.$carrier->id.'.jpg' : false),
 			));
 			
 		$this->context->smarty->assign(array(
 			'logo_content' => $this->createTemplate('logo.tpl')->fetch()
 		));
 		
		$this->addjQueryPlugin(array('ajaxfileupload'));

		return parent::renderView();
	}
	
	public function initBreadcrumbs($tab_id = null,$tabs = null)
	{
		if (Tools::getValue('id_shippingpro_carrier'))
			$this->display = 'edit';
		else
			$this->display = 'add';

		parent::initBreadcrumbs((int)Tab::getIdFromClassName('AdminShippingproCarriers'));
		
		$this->display = 'view';
	}

	public function initPageHeaderToolbar()
	{
		parent::initPageHeaderToolbar();

		$this->page_header_toolbar_btn['cancel'] = array(
			'href' => $this->context->link->getAdminLink('AdminShippingproCarriers'),
			'desc' => $this->l('Cancel', null, null, false)
		);
	}

	public function renderStepOne($carrier)
	{
		$this->fields_form = array(
			'form' => array(
				'id_form' => 'step_carrier_general',
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Carrier name'),
						'name' => 'name',
						'required' => true,
						'hint' => array(
							sprintf($this->l('Allowed characters: letters, spaces and "%s".'), '().-'),
							$this->l('The carrier\'s name will be displayed during checkout.'),
							$this->l('For in-store pickup, enter 0 to replace the carrier name with your shop name.')
						)
					),
					array(
						'type' => 'text',
						'label' => $this->l('Speed grade'),
						'name' => 'grade',
						'required' => false,
						'size' => 1,
						'hint' => $this->l('Enter "0" for a longest shipping delay, or "9" for the shortest shipping delay.')
					),
					array(
						'type' => 'logo',
						'label' => $this->l('Logo'),
						'name' => 'logo'
					),
					array(
						'type' => 'text',
						'label' => $this->l('Tracking URL'),
						'name' => 'url',
						'hint' => $this->l('Delivery tracking URL: Type \'@\' where the tracking number should appear. It will be automatically replaced by the tracking number.')
					),
				)),
		);

		$tpl_vars = array('max_image_size' => (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE') / 1024 / 1024);
		$fields_value = $this->getStepOneFieldsValues($carrier);
		return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value, $tpl_vars);
	}

	public function renderStepTwo($carrier)
	{
		$this->fields_form = array(
			'form' => array(
				'id_form' => 'step_carrier_shops',
				'input' => array(
					array(
						'type' => 'shop',
						'label' => $this->l('Shop association'),
						'name' => 'checkBoxShopAsso',
					),
				))
		);
		$fields_value = $this->getStepTwoFieldsValues($carrier);
		return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value);
	}

	public function renderStepThree($carrier)
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
		$context .= '<div class="new_rule"><a href="#" onclick="add_new_rule(this);return false;" class="btn btn-default" id="add_new_rule">'.$this->l('Add new rule').'</a></div>';

		return $context;
	}
	public function getDefaultRule()
	{
		return array(
			'is_free' => false,
			'shipping_handling' => false,
			'shipping_method' => ShippingproCarrierZone::SHIPPING_METHOD_QUANTITY,
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
								'value' => ShippingproCarrierZone::SHIPPING_METHOD_PRICE,
								'label' => $this->l('According to total price.')
							),
							array(
								'id' => 'billing_weight',
								'value' => ShippingproCarrierZone::SHIPPING_METHOD_WEIGHT,
								'label' => $this->l('According to total weight.')
							),
							array(
								'id' => 'billing_quantity',
								'value' => ShippingproCarrierZone::SHIPPING_METHOD_QUANTITY,
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

		$tpl_vars['ranges'] = unserialize($rule['ranges']);

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

	public function renderStepFour($carrier)
	{
		$this->fields_form = array(
			'form' => array(
				'id_form' => 'step_carrier_conf',
				'input' => array(
					array(
						'type' => 'group',
						'label' => $this->l('Group access'),
						'name' => 'groupBox',
						'values' => Group::getGroups(Context::getContext()->language->id),
						'hint' => $this->l('Mark the groups that are allowed access to this carrier.')
					)
				)
			));

		$fields_value = $this->getStepFourFieldsValues($carrier);

		// Added values of object Group
		$carrier_groups = $carrier->getGroups();
		$carrier_groups_ids = array();
		if (is_array($carrier_groups))
			foreach ($carrier_groups as $carrier_group)
				$carrier_groups_ids[] = $carrier_group['id_group'];

			$groups = Group::getGroups($this->context->language->id);

		foreach ($groups as $group)
			$fields_value['groupBox_'.$group['id_group']] = Tools::getValue('groupBox_'.$group['id_group'], (in_array($group['id_group'], $carrier_groups_ids) || empty($carrier_groups_ids) && !$carrier->id));

		return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value);
	}

	public function renderStepFive($carrier)
	{
		$this->fields_form = array(
			'form' => array(
				'id_form' => 'step_carrier_summary',
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Enabled'),
						'name' => 'active',
						'required' => false,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1
							),
							array(
								'id' => 'active_off',
								'value' => 0
							)
						),
						'hint' => $this->l('Enable the carrier in the Front Office.')
					)
				)
			));

		$template = $this->createTemplate('shippingpro_carrier_wizard/summary.tpl');

		$fields_value = $this->getStepFiveFieldsValues($carrier);

		$active_form = $this->renderGenericForm(array('form' => $this->fields_form), $fields_value);

		$active_form =  str_replace(array('<fieldset id="fieldset_form">', '</fieldset>'), '', $active_form);

		$template->assign('active_form', $active_form);

		return $template->fetch(_PS_MODULE_DIR_.'shippingpro/views/templates/admin/shippingpro_carrier_wizard/summary.tpl');
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

		if ($shipping_method == ShippingproCarrierZone::SHIPPING_METHOD_FREE)
		{
			$range_obj = $carrier->getRangeObject($carrier->shipping_method);
			$price_by_range = array();
		}
		else
		{
			$range_obj = $carrier->getRangeObject();
			$price_by_range = ShippingproCarrier::getDeliveryPriceByRanges($range_table, (int)$carrier->id);
		}

		foreach ($price_by_range as $price)
			$tpl_vars['price_by_range'][$price['id_'.$range_table]][$price['id_zone']] = $price['price'];

		$tmp_range = $range_obj->getRanges((int)$carrier->id);
		$tpl_vars['ranges'] = array();
		if ($shipping_method != ShippingproCarrierZone::SHIPPING_METHOD_FREE)
			foreach ($tmp_range as $id => $range)
			{
				$tpl_vars['ranges'][$range['id_'.$range_table]] = $range;
				$tpl_vars['ranges'][$range['id_'.$range_table]]['id_range'] = $range['id_'.$range_table];
			}

		// init blank range
		if (!count($tpl_vars['ranges']))
			$tpl_vars['ranges'][] = array('id_range' => 0, 'delimiter1' => 0, 'delimiter2' => 0);
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
		$helper->id = (int)Tools::getValue('id_shippingpro_carrier');
		$helper->identifier = $this->identifier;
		$helper->tpl_vars = array_merge(array(
				'fields_value' => $fields_value,
				'languages' => $this->getLanguages(),
				'id_language' => $this->context->language->id
			), $tpl_vars);
		$helper->override_folder = 'shippingpro_carrier_wizard/';

		return $helper->generateForm($fields_form);
	}

	public function getStepOneFieldsValues($carrier)
	{
		return array(
			'id_shippingpro_carrier' => $this->getFieldValue($carrier, 'id_shippingpro_carrier'),
			'name' => $this->getFieldValue($carrier, 'name'),
			'grade' => $this->getFieldValue($carrier, 'grade'),
			'url' => $this->getFieldValue($carrier, 'url'),
		);
	}

	public function getStepTwoFieldsValues($carrier)
	{
		return array('shop' => $this->getFieldValue($carrier, 'shop'));

	}

	public function getStepFourFieldsValues($carrier)
	{
		return array(
			'range_behavior' => $this->getFieldValue($carrier, 'range_behavior'),
			'group' => $this->getFieldValue($carrier, 'group'),
		);
	}

	public function getStepFiveFieldsValues($carrier)
	{
		return array('active' => $this->getFieldValue($carrier, 'active'));
	}

	protected function validateForm($die = true)
	{
		$step_number = (int)Tools::getValue('step_number');
		$return = array('has_error' => false);

		if (!$this->tabAccess['edit'])
			$this->errors[] = Tools::displayError('You do not have permission to use this wizard.');
		else
		{
			if (Shop::isFeatureActive() && $step_number == 2)
			{
				if (!Tools::getValue('checkBoxShopAsso_carrier'))
				{
					$return['has_error'] = true;
					$return['errors'][] = $this->l('You must choose at least one shop or group shop.');
				}
			}
			else
				$this->validateRules();
		}

		if (count($this->errors))
		{
			$return['has_error'] = true;
			$return['errors'] = $this->errors;
		}
		if (count($this->errors) || $die)
			die(Tools::jsonEncode($return));

	}


	public function ajaxProcessValidateStep()
	{
			$this->validateForm(true);
	}

	public function processRanges($id_carrier)
	{
		if (!$this->tabAccess['edit'] || !$this->tabAccess['add'])
		{
			$this->errors[] = Tools::displayError('You do not have permission to use this wizard.');
			return;
		}

		$carrier = new ShippingproCarrier((int)$id_carrier);
		if (!Validate::isLoadedObject($carrier))
			return false;

		if (Tools::getValue('is_free')) {
			return true;
		}

		foreach (Tools::getValue('rule_form') as $key => $rule) {
			$carrierZone = new ShippingproCarrierZone();
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
			$carrierZone->copyFromData($rule);
			$carrierZone->id_shippingpro_carrier = $id_carrier;
			$carrierZone->add();
		}

		return true;
	}

	public function ajaxProcessUploadLogo()
	{
		if (!$this->tabAccess['edit'])
			die('<return result="error" message="'.Tools::displayError('You do not have permission to use this wizard.').'" />');

		$allowedExtensions = array('jpeg', 'gif', 'png', 'jpg');

		$logo = (isset($_FILES['carrier_logo_input']) ? $_FILES['carrier_logo_input'] : false);
		if ($logo && !empty($logo['tmp_name']) && $logo['tmp_name'] != 'none'
			&& (!isset($logo['error']) || !$logo['error'])
			&& preg_match('/\.(jpe?g|gif|png)$/', $logo['name'])
			&& is_uploaded_file($logo['tmp_name'])
			&& ImageManager::isRealImage($logo['tmp_name'], $logo['type']))
		{
			$file = $logo['tmp_name'];
			do $tmp_name = uniqid().'.jpg';
			while (file_exists(_PS_TMP_IMG_DIR_.$tmp_name));
			if (!ImageManager::resize($file, _PS_TMP_IMG_DIR_.$tmp_name))
				die('<return result="error" message="Impossible to resize the image into '.Tools::safeOutput(_PS_TMP_IMG_DIR_).'" />');
			@unlink($file);
			die('<return result="success" message="'.Tools::safeOutput(_PS_TMP_IMG_.$tmp_name).'" />');
		}
		else
			die('<return result="error" message="Cannot upload file" />');
	}

	public function ajaxProcessFinishStep()
	{
		$return = array('has_error' => false);
		if (!$this->tabAccess['edit'])
			$return = array(
				'has_error' =>  true,
				$return['errors'][] = Tools::displayError('You do not have permission to use this wizard.')
			);
		else
		{	
			$this->validateForm(false);
			if ($id_carrier = Tools::getValue('id_shippingpro_carrier'))
			{
				$current_carrier = new ShippingproCarrier((int)$id_carrier);

				// if update we duplicate current Carrier
				$new_carrier = $current_carrier->duplicateObject();
				if (Validate::isLoadedObject($new_carrier))
				{
					// Set flag deteled to true for historization
					$current_carrier->deleted = true;
					$current_carrier->update();

					// Fill the new carrier object
					$this->copyFromPost($new_carrier, $this->table);
					$new_carrier->position = $current_carrier->position;
					$new_carrier->update();

					$this->updateAssoShop((int)$new_carrier->id);
					$this->duplicateLogo((int)$new_carrier->id, (int)$current_carrier->id);
					$this->changeGroups((int)$new_carrier->id);
					
					//Copy default carrier
					if (Configuration::get('PS_CARRIER_DEFAULT') == $current_carrier->id)
						Configuration::updateValue('PS_CARRIER_DEFAULT', (int)$new_carrier->id);
					
					$this->postImage($new_carrier->id);
					// $this->changeZones($new_carrier->id);
					// $new_carrier->setTaxRulesGroup((int)Tools::getValue('id_tax_rules_group'));
					$carrier = $new_carrier;
				}
			}
			else
			{
				$carrier = new ShippingproCarrier();
				$this->copyFromPost($carrier, $this->table);
				if (!$carrier->add())
				{
					$return['has_error'] = true;
					$return['errors'][] = $this->l('An error occurred while saving this carrier.');
				}
			}

			if (Validate::isLoadedObject($carrier))
			{
				if (!$this->changeGroups((int)$carrier->id))
				{
					$return['has_error'] = true;
					$return['errors'][] = $this->l('An error occurred while saving carrier groups.');
				}

				// if (!$this->changeZones((int)$carrier->id))
				// {
				// 	$return['has_error'] = true;
				// 	$return['errors'][] = $this->l('An error occurred while saving carrier zones.');
				// }

				if (!$this->processRanges((int)$carrier->id))
				{
					$return['has_error'] = true;
					$return['errors'][] = $this->l('An error occurred while saving carrier ranges.');
				}

				if (Shop::isFeatureActive() && !$this->updateAssoShop((int)$carrier->id))
				{
					$return['has_error'] = true;
					$return['errors'][] = $this->l('An error occurred while saving associations of shops.');
				}

				// if (!$carrier->setTaxRulesGroup((int)Tools::getValue('id_tax_rules_group')))
				// {
				// 	$return['has_error'] = true;
				// 	$return['errors'][] = $this->l('An error occurred while saving the tax rules group.');
				// }

				if (Tools::getValue('logo'))
				{
					if (Tools::getValue('logo') == 'null' && file_exists(_PS_SHIP_IMG_DIR_.$carrier->id.'.jpg'))
						unlink(_PS_SHIP_IMG_DIR_.$carrier->id.'.jpg');
					else
					{
						$logo = basename(Tools::getValue('logo'));
						if (!file_exists(_PS_TMP_IMG_DIR_.$logo) || !copy(_PS_TMP_IMG_DIR_.$logo, _PS_SHIP_IMG_DIR_.$carrier->id.'.jpg'))
						{
							$return['has_error'] = true;
							$return['errors'][] = $this->l('An error occurred while saving carrier logo.');
						}
					}
				}
				$return['id_shippingpro_carrier'] = $carrier->id;
			}
		}
		die(Tools::jsonEncode($return));
	}

	protected function changeGroups($id_carrier, $delete = true)
	{
		$carrier = new ShippingproCarrier((int)$id_carrier);
		if (!Validate::isLoadedObject($carrier))
			return false;

		return $carrier->setGroups(Tools::getValue('groupBox'));
	}

	public function changeZones($id)
	{
		$return = true;
		$carrier = new ShippingproCarrier($id);
		if (!Validate::isLoadedObject($carrier))
			die (Tools::displayError('The object cannot be loaded.'));
		$zones = Zone::getZones(false);
		foreach ($zones as $zone)
			if (count($carrier->getZone($zone['id_zone'])))
			{
				if (!isset($_POST['zone_'.$zone['id_zone']]) || !$_POST['zone_'.$zone['id_zone']])
					$return &= $carrier->deleteZone((int)$zone['id_zone']);
			}
		else
			if (isset($_POST['zone_'.$zone['id_zone']]) && $_POST['zone_'.$zone['id_zone']])
				$return &= $carrier->addZone((int)$zone['id_zone']);

			return $return;
	}

	public function getValidationRules()
	{
		$step_number = (int)Tools::getValue('step_number');
		if (!$step_number)
			return;

		if ($step_number == 4 && !Shop::isFeatureActive() || $step_number == 5 && Shop::isFeatureActive())
			return array('fields' => array());

		$step_fields = array(
			1 => array('name', 'grade', 'url'),
			2 => array('is_free', 'id_tax_rules_group', 'shipping_handling', 'shipping_method', 'range_behavior'),
			3 => array('range_behavior', 'max_height', 'max_width', 'max_depth', 'max_weight'),
			4 => array(),
		);
		if (Shop::isFeatureActive())
		{
			$tmp = $step_fields;
			$step_fields = array_slice($tmp, 0, 1, true) + array(2 => array('shop'));
			$step_fields[3] = $tmp[2];
			$step_fields[4] = $tmp[3];
		}

		$definition = ObjectModel::getDefinition('ShippingproCarrier');
		foreach ($definition['fields'] as $field => $def)
			if (is_array($step_fields[$step_number]) && !in_array($field, $step_fields[$step_number]))
				unset($definition['fields'][$field]);
		return $definition;
	}

	public static function displayFieldName($field)
	{
		return $field;
	}

	public function duplicateLogo($new_id, $old_id)
	{
		$old_logo = _PS_SHIP_IMG_DIR_.'/'.(int)$old_id.'.jpg';
		if (file_exists($old_logo))
			copy($old_logo, _PS_SHIP_IMG_DIR_.'/'.(int)$new_id.'.jpg');

		$old_tmp_logo = _PS_TMP_IMG_DIR_.'/carrier_mini_'.(int)$old_id.'.jpg';
		if (file_exists($old_tmp_logo))
		{
			if (!isset($_FILES['logo']))
				copy($old_tmp_logo, _PS_TMP_IMG_DIR_.'/carrier_mini_'.$new_id.'.jpg');
			unlink($old_tmp_logo);
		}
	}
}
