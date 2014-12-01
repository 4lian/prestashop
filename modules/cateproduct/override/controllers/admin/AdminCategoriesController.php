<?php

class AdminCategoriesController extends AdminCategoriesControllerCore
{
	public function renderView()
	{
		if (!($category = $this->loadObject()))
			return;

		$products = $category->getProductsLite($this->context->language->id);
		$total_product = count($products);
		for ($i = 0; $i < $total_product; $i++)
		{
			$products[$i] = new Product($products[$i]['id_product'], false, $this->context->language->id);
			$products[$i]->loadStockData();
			/* Build attributes combinations */
			$combinations = $products[$i]->getAttributeCombinations($this->context->language->id);
			foreach ($combinations as $k => $combination)
			{
				$comb_array[$combination['id_product_attribute']]['reference'] = $combination['reference'];
				$comb_array[$combination['id_product_attribute']]['ean13'] = $combination['ean13'];
				$comb_array[$combination['id_product_attribute']]['upc'] = $combination['upc'];
				$comb_array[$combination['id_product_attribute']]['quantity'] = $combination['quantity'];
				$comb_array[$combination['id_product_attribute']]['attributes'][] = array(
					$combination['group_name'],
					$combination['attribute_name'],
					$combination['id_attribute']
				);
			}

			if (isset($comb_array))
			{
				foreach ($comb_array as $key => $product_attribute)
				{
					$list = '';
					foreach ($product_attribute['attributes'] as $attribute)
						$list .= $attribute[0].' - '.$attribute[1].', ';
					$comb_array[$key]['attributes'] = rtrim($list, ', ');
				}
				isset($comb_array) ? $products[$i]->combination = $comb_array : '';
				unset($comb_array);
			}
		}

		$this->tpl_view_vars = array(
			'category' => $category,
			'products' => $products,
			'stock_management' => Configuration::get('PS_STOCK_MANAGEMENT'),
			'shopContext' => Shop::getContext(),
		);

		return parent::renderView().AdminController::renderView();
	}

	public function renderKpis()
	{
		$helper = new HelperView($this);

		$helper->base_folder = 'helpers/view/';
		$helper->base_tpl = 'all.tpl';

		$this->setHelperDisplay($helper);
		$helper->tpl_vars = array(
			'manufacturer' => "kk",
		);

		$view = $helper->generateView();

		return parent::renderKpis().$view;
	}
}