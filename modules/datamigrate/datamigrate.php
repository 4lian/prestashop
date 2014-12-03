<?php
if (!defined('_PS_VERSION_'))
	exit;

class DataMigrate extends Module
{	
	public $unit_array = array(
		"包" => "40",
		"斤" => "34",
		"杯" => "38",
		"瓶" => "44",
		"盒" => "35",
		"箱" => "36",
		"粒" => "37",
		"組" => "43",
		"罐" => "42",
		"袋" => "41",
		"隻" => "39",
	);
	public $ship_array = array(
		"normal" => "50",
		"cool" => "51",
		"ship001" => "52",
		"ship002" => "53",
		"ship003" => "54",
		"ship004" => "55",
		"overseaship02" => "57",
		"overseaship03" => "56",
		"ship005" => "58",
	);
	public $status_array = array(
		"finish" => "5",
		"cancel" => "6",
		"deliver" => "4",
		"new" => "10",
		"paysucc" => "2",
		"packing" => "3",
		"payfail" => "8",
	);

	public function unitSwitch($unit)
	{
		return (int)$this->unit_array[$unit];
	}

	public function shipSwitch($ship)
	{
		return (int)$this->ship_array[$ship];
	}

	public function statusSwitch($status)
	{
		return (int)$this->status_array[$status];
	}

	public function ReSQLString($InputStr)
	{
		if (trim($InputStr)=="") 
		{			
			return "";
		}
				 
		$InputStr = str_replace( "&lt;" , "<" , $InputStr);
		$InputStr = str_replace( "&gt;" ,">" , $InputStr);
		$InputStr = str_replace( "’" ,"'", $InputStr);
		$InputStr = str_replace( "｜" ,"|" , $InputStr );
		$InputStr = str_replace( "—" ,"-" , $InputStr );	
	
		$InputStr = stripslashes($InputStr);
		
		return $InputStr;
   }

	public function __construct()
	{
		$this->name = 'datamigrate';
		$this->tab = 'administration';
		$this->version = '1.0.0';
		$this->author = 'One Yang';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Data Migrate');
		$this->description = $this->l('Migrate data');

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

	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitDataMigrate'))
		{
			$migration_option = (int)(Tools::getValue('DATA_MIGRATE'));
			switch ($migration_option) {
				case 0:
					$output .= $this->migrateMainCategory();
					break;
				case 1:
					$output .= $this->migrateSubCategory();
					break;
				case 2:
					$output .= $this->migrateApproximateCategory();
					break;
				case 3:
					$output .= $this->migrateProduct();
					break;
				case 4:
					$output .= $this->migrateManufacturer();
					break;
				case 5:
					$output .= $this->migrateCustomer();
					break;
				case 6:
					$output .= $this->migrateAddress();
					break;
				case 7:
					$output .= $this->updateCustomer();
					break;
				case 8:
					$output .= $this->addProductFeature();
					break;
				case 9:
					$output .= $this->updateManufacturerDetail();
					break;
				case 10:
					$output .= $this->migrateOrder();
					break;
				default:
					break;
			}
		}
		return $this->renderForm().$output;
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'radio',
						'label' => $this->l('Migrate What'),
						'name' => 'DATA_MIGRATE',
						'values' => array(
							array(
								'id' => 'home',
								'value' => 0,
								'label' => $this->l('Main Category')
							),
							array(
								'id' => 'home',
								'value' => 1,
								'label' => $this->l('Sub Category')
							),
							array(
								'id' => 'home',
								'value' => 2,
								'label' => $this->l('Approximate Category')
							),
							array(
								'id' => 'current',
								'value' => 3,
								'label' => $this->l('Product')
							),
							array(
								'id' => 'current',
								'value' => 4,
								'label' => $this->l('Manufacturer')
							),
							array(
								'id' => 'current',
								'value' => 5,
								'label' => $this->l('Customer')
							),
							array(
								'id' => 'current',
								'value' => 6,
								'label' => $this->l('Address')
							),
							array(
								'id' => 'current',
								'value' => 7,
								'label' => $this->l('Update Customer')
							),
							array(
								'id' => 'current',
								'value' => 8,
								'label' => $this->l('Add Product Feature')
							),
							array(
								'id' => 'current',
								'value' => 9,
								'label' => $this->l('Update Manufacturer Detail')
							),
							array(
								'id' => 'current',
								'value' => 10,
								'label' => $this->l('Order')
							),
						)
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitDataMigrate';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'DATA_MIGRATE' => 0,
		);
	}

	public function migrateMainCategory()
	{
		die();
		$output = '';
		$db = new PDO("mysql:host=localhost;dbname=buynearb_webdb;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$r = $db->query("SELECT * FROM prodtype WHERE SubId = 0 ORDER BY MainId;");
		foreach ($r as $row) {

			$output .= $row['MainName'].'<br><br><br><br>';
			$object = new Category();
			foreach (Language::getLanguages(false) as $lang){
				$object->name[$lang['id_lang']] = $row['MainName'];
				$object->link_rewrite[$lang['id_lang']] = "category-".$row['MainId'];
			}
			$object->id_parent = Configuration::get('PS_HOME_CATEGORY');
			$object->add();
			$db->query("UPDATE prodtype SET category_id = '".$object->id."' WHERE MainId = '".$row['MainId']."' AND SubId = 0;");
			$output .= print_r($db->errorInfo(),true).'<br><br><br><br>';
		}
		return $output;
	}

	public function migrateSubCategory()
	{
		die();
		$output = '';
		$db = new PDO("mysql:host=localhost;dbname=buynearb_webdb;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$r = $db->query("SELECT * FROM prodtype WHERE SubId != 0 ORDER BY MainId, SubRank;");
		foreach ($r as $row) {
			$output .= $row['SubName'].'<br><br><br><br>';
			$object = new Category();
			foreach (Language::getLanguages(false) as $lang){
				$object->name[$lang['id_lang']] = $row['SubName'];
				$object->link_rewrite[$lang['id_lang']] = "category-".$row['MainId']."-".$row['SubId'];
			}
			$object->id_parent = $row['category_id'];
			$object->add();
			$db->query("UPDATE prodtype SET category_id = '".$object->id."' WHERE MainId = '".$row['MainId']."' AND SubId = '".$row['SubId']."';");
			$output .= print_r($db->errorInfo(),true).'<br><br><br><br>';
		}
		return $output;
	}

	public function migrateApproximateCategory()
	{
		die();
		$output = '';
		$db = new PDO("mysql:host=localhost;dbname=buynearb_webdb;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$r = $db->query("SELECT p.*, t.category_id FROM product p, prodtype t WHERE p.MainType = t.MainId AND p.SubType = t.SubId ORDER BY ProdId;");
		foreach ($r as $row) {
			$output .= $row['ProdName'].'<br><br><br><br>';
			$object = new Category();
			foreach (Language::getLanguages(false) as $lang){
				$object->name[$lang['id_lang']] = $row['ProdName'];
				$object->link_rewrite[$lang['id_lang']] = "product-".$row['ProdId'];
			}
			$object->id_parent = $row['category_id'];
			$object->add();
			$db->query("UPDATE product SET cp_id = '".$object->id."' WHERE ProdId = '".$row['ProdId']."';");
			$output .= print_r($db->errorInfo(),true).'<br><br><br><br>';
		}
		return $output;
	}

	public function migrateProduct()
	{
		die();
		$output = '';
		$db = new PDO("mysql:host=localhost;dbname=buynearb_webdb;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$r = $db->query("SELECT h.*, p.cp_id, f.ma_id FROM farmerharvest h, product p, farmers f WHERE h.FarmerId = f.FarmerId AND h.ProdId = p.ProdId ORDER BY h.FhId;");
		foreach ($r as $row) {
			$output .= $row['ProdName'].'<br><br><br><br>';
			// $object = new Product($row['pr_id']);

			// foreach (Language::getLanguages(false) as $lang){
			// 	$object->name[$lang['id_lang']] = !empty($row['ProdName']) ? $row['ProdName']: 'not set';
			// 	$object->description[$lang['id_lang']] = $row['SubTitle'];
			// 	$object->link_rewrite[$lang['id_lang']] = 'harvest-'.$row['FhId'];
			// }
			// $object->id_manufacturer = $row['ma_id'];
			// $object->id_category_default = $row['cp_id'];
			// $object->price = $row['SellPrice'];

			$stock = $row['Stock'] >= 0 ? $row['Stock'] : 0;
			// $object->quantity = $stock;
			// $object->update();


			// $object->updateCategories(array($row['cp_id']));

			// $db2 = new PDO("mysql:host=localhost;dbname=prestashop;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
			// $db2->query("UPDATE ps_stock_available SET quantity = '".$stock."' WHERE id_product = '".$row['pr_id']."';");

			// $db->query("UPDATE farmerharvest SET pr_id = '".$object->id."' WHERE FhId = '".$row['FhId']."';");
			$output .= print_r($db->errorInfo(),true).'<br><br><br><br>';
		}
		return $output;
	}

	public function migrateManufacturer()
	{
		die();
		$output = '';
		$db = new PDO("mysql:host=localhost;dbname=buynearb_webdb;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$r = $db->query("SELECT * FROM farmers ORDER BY FarmerId;");
		foreach ($r as $row) {
			$output .= $row['FarmerName'].'<br><br><br><br>';
			$object = new Manufacturer();
			$object->name = $row['FarmerName'];
			$object->active = true;
			$object->add();
			$db->query("UPDATE farmers SET ma_id = '".$object->id."' WHERE FarmerId = '".$row['FarmerId']."';");
			$output .= print_r($db->errorInfo(),true).'<br><br><br><br>';
		}
		return $output;
	}

	public function migrateCustomer()
	{
		die();
		return '';
		$output = '';
		$db = new PDO("mysql:host=localhost;dbname=buynearb_webdb;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$r = $db->query("SELECT * FROM member WHERE MemId > 1822 ORDER BY MemId;");
		foreach ($r as $row) {
			$output .= $row['MemName'].'<br>';
			$object = new Customer();
			$object->firstname = !empty($row['MemName']) ? $row['MemName'] : ' ';
			$object->lastname = ' ';
			$object->email = $row['MemAcc'];
			$object->setWsPasswd($row['MemPwd']);
			if ($row['Gender']) {
				$object->id_gender = $row['Gender'] == 'M' ? 1 : 2;
			}
			if (!empty($row['Birthday']) && strlen($row['Birthday']) == 10) {
				$object->birthday = $row['Birthday'];
			}
			$object->id_default_group = (int)Configuration::get('PS_CUSTOMER_GROUP');
			$object->groupBox = array((int)Configuration::get('PS_CUSTOMER_GROUP'));
			$object->update();
			$db->query("UPDATE member SET cu_id = '".$object->id."' WHERE MemId = '".$row['MemId']."';");
			$output .= print_r($db->errorInfo(),true).'<br>';
		}
		return $output;
	}

	public function migrateAddress()
	{
		die();
		$output = '';
		$db = new PDO("mysql:host=localhost;dbname=buynearb_webdb;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$r = $db->query("SELECT * FROM member WHERE MemId > 456 ORDER BY MemId;");
		foreach ($r as $row) {
			if (empty($row['County'])) {
				continue;
			}
			$output .= $row['County'].'<br>';
			$object = new Address();
			$object->id_customer = $row['cu_id'];
			$object->id_country = 203;
			$firstname = !empty($row['MemName']) ? $row['MemName'] : ' ';
			$object->alias = $firstname.'的地址';
			$object->firstname = $firstname;
			$object->lastname = ' ';
			$object->city = $row['County'];
			$object->address1 = !empty($row['City']) ? $row['City'] : ' ';
			$object->address2 = $row['Address'];
			$object->postcode = $row['AreaCode'];
			$object->phone = $row['TelArea'].'-'.$row['Tel'].'-'.$row['Extension'];
			$object->phone_mobile = $row['Mobile'];

			$object->add();
			$db->query("UPDATE member SET ad_id = '".$object->id."' WHERE MemId = '".$row['MemId']."';");
			$output .= print_r($db->errorInfo(),true).'<br>';
		}
		return $output;
	}

	public function updateCustomer()
	{
		die();
		$output = '';
		$db = new PDO("mysql:host=localhost;dbname=buynearb_webdb;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$r = $db->query("SELECT * FROM member WHERE MemId > 1822 ORDER BY MemId;");
		foreach ($r as $row) {
			$customer = new Customer($row['cu_id']);

			$object->update();
			$output .= print_r($db->errorInfo(),true).'<br>';
		}
		return $output;
	}

	public function addProductFeature()
	{
		die();
		$output = '';
		$db = new PDO("mysql:host=localhost;dbname=buynearb_webdb;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

		$r = $db->query("SELECT * FROM farmerharvest WHERE pr_id IS NOT NULL ORDER BY FhId DESC;");
		foreach ($r as $row) {
			$output .= $row['Quantity'].'<br>';

			$object = new Product($row['pr_id']);

			// $unit = $this->unitSwitch($row['Unit']);
			// $object->addFeaturesToDB(8, $unit);

			// if (!is_null($row['ShipType']) && $row['ShipType'] != '') {
			// 	$ship = $this->shipSwitch($row['ShipType']);
			// 	$object->addFeaturesToDB(9, $ship);
			// }

			// if (!is_null($row['Quality']) && $row['Quality'] != '') {
			// 	$fv_id = $object->addFeaturesToDB(10, 0, 1);
			// 	$object->addFeaturesCustomToDB($fv_id, 1, $row['Quality']);
			// }

			// if (!is_null($row['UnitMemo']) && $row['UnitMemo'] != '') {
			// 	$fv_id = $object->addFeaturesToDB(11, 0, 1);
			// 	$object->addFeaturesCustomToDB($fv_id, 1, $row['UnitMemo']);
			// }

			$fv_id = $object->addFeaturesToDB(12, 0, 1);
			$object->addFeaturesCustomToDB($fv_id, 1, $row['Quantity']);

			$db->query("UPDATE farmerharvest SET unit_done = '1' WHERE FhId = '".$row['FhId']."';");
			$output .= print_r($db->errorInfo(),true).'<br>';
		}
		return $output;
	}

	public function updateManufacturerDetail()
	{
		die();
		$output = '';
		$db = new PDO("mysql:host=localhost;dbname=buynearb_webdb;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

		$r = $db->query("SELECT * FROM farmers ORDER BY FarmerId DESC;");
		foreach ($r as $row) {
			$db2 = new PDO("mysql:host=localhost;dbname=prestashop;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
			$db2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			$sth = $db2->prepare("UPDATE ps_manufacturer_lang SET location = ?, crop = ?, subject = ?, brief = ?, certification = ?, description = ?, short_description = ? WHERE id_manufacturer = ?;");
			$sth->execute(array($row['Location'], $row['MainCrop'], $row['Subject'], $row['Brief'], $this->ReSQLString($row['Certification']), $this->ReSQLString($row['Story']), $this->ReSQLString($row['FarmIntro']), $row['ma_id']));

			$output .= print_r($db2->errorInfo(),true).'<br>';
		}
		return $output;
	}

	public function migrateOrder()
	{
		die();
		$output = '';
		$db = new PDO("mysql:host=localhost;dbname=buynearb_webdb;charset=utf8", "root", "root", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

		$r = $db->query("SELECT o.Status, o.PayPrice, o.TotalPrice, o.Freight, o.OrderNo, m.cu_id FROM mainorder o, member m WHERE o.Email = m.MemAcc AND or_id IS NULL ORDER BY OrderNo;");
		foreach ($r as $row) {
			if (is_null($row['cu_id'])) {
				continue;
			}

			$customer = new Customer($row['cu_id']);
			$cart = new Cart();
			$cart->secure_key = $customer->secure_key;
			$cart->id_shop_group = 1;
			$cart->id_shop = 1;
			// $cart->id_address_delivery = ;
			// $cart->id_address_invoice = ;
			$cart->id_currency = 1;
			$cart->id_customer = $row['cu_id'];
			$cart->id_guest = 0;
			$cart->id_lang = 1;
			$cart->add();

			$object = new Order();

			$object->id_address_delivery = 4;
			$object->id_address_invoice = 4;
			$object->id_shop_group = 1;
			$object->id_shop = 1;
			$object->id_cart = $cart->id;
			$object->id_currency = 1;
			$object->id_lang = 1;
			$object->id_customer = $row['cu_id'];
			$object->id_carrier = 10;
			$object->current_state = $this->statusSwitch($row['Status']);
			$object->payment = '銀行匯款';
			$object->module = 'bankwire';
			$object->conversion_rate = 1;
			$object->secure_key = $customer->secure_key;
			$object->total_paid = $row['PayPrice'];
			$object->total_shipping_tax_incl = $row['PayPrice'];
			$object->total_shipping_tax_excl = $row['PayPrice'];
			$object->total_paid_real = $row['PayPrice'];
			$object->total_products_wt = $row['TotalPrice'];
			$object->total_products = $row['TotalPrice'];
			$object->total_shipping = $row['Freight'];
			$object->add();

			$db->query("UPDATE mainorder SET or_id = '".$object->id."' WHERE OrderNo = '".$row['OrderNo']."';");
			$output .= print_r($db->errorInfo(),true).'<br>';
		}
		return $output;
	}
}
