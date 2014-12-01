<?php

class Category extends CategoryCore
{
	public function getProductsLite($id_lang)
	{
		$context = Context::getContext();
		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront')))
			$front = false;
			
		return Db::getInstance()->executeS('
		SELECT p.`id_product`,  pl.`name`
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
			p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = '.(int)$id_lang.$context->shop->addSqlRestrictionOnLang('pl').'
		)
		WHERE p.`id_category_default` = '.(int)$this->id.
		($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : ''));
	}
}
