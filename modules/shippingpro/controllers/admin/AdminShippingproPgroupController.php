<?php

class AdminShippingproPgroupController extends ModuleAdminController
{

	public function __construct()
	{
		$this->bootstrap = true;
		$this->table = 'shippingpro_pgroup';
		$this->className = 'ShippingproPgroup';
		$this->lang = true;
		$this->deleted = false;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->context = Context::getContext();

		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'confirm' => $this->l('Delete selected items?'),
				'icon' => 'icon-trash'
			)
		);

		$this->fields_list = array(
			'id_shippingpro_product_group' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'name' => array(
				'title' => $this->l('Name'),
				'lang' => true
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'active' => 'status',
				'type' => 'bool',
				'align' => 'center',
				'class' => 'fixed-width-xs',
				'orderby' => false
			)
		);
		parent::__construct();
	}

	public function renderForm()
	{
		if (!($product_group = $this->loadObject(true)))
			return;

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Product Group'),
				'icon' => 'icon-certificate'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name'),
					'name' => 'name',
					'col' => 4,
					'lang' => true,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Enable'),
					'name' => 'active',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				)
			),
			'submit' => array(
				'title' => $this->l('Save')
			),
		);

		return parent::renderForm();
	}

	protected function beforeDelete($object)
	{
		return true;
	}

	public function processSave()
	{
		parent::processSave();
	}
}