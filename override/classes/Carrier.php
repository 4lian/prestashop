<?php

class Carrier extends CarrierCore
{
	public function deleteRules()
	{
		return (Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'shippingpro_rule WHERE id_carrier = '.(int)$this->id));
	}
	public function getZoneRules()
	{
		$cache_id = 'Carrier::getZoneDetail_'.(int)$this->id;
		// if (!Cache::isStored($cache_id))
		if (true)
		{
			$sql = 'SELECT *
					FROM `'._DB_PREFIX_.'shippingpro_rule` r
					WHERE r.`id_carrier` = '.(int)$this->id;
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
			foreach ($result as &$row) {
				$row['ranges'] = unserialize($row['ranges']);
			}
			return $result;
			Cache::store($cache_id, $result);
		}
		return Cache::retrieve($cache_id);
	}
}