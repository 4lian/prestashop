<?php

/**
 * Logging in with username
 *
 * @author    PrestashopExtensions.com
 * @copyright PrestashopExtensions.com
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Manufacturer extends ManufacturerCore
{
	public $location;
	public $crop;
	public $subject;
	public $brief;
	public $certification;

	public function __construct($id = null)
	{
		self::$definition['fields']['location'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 50);
		self::$definition['fields']['crop'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255);
		self::$definition['fields']['subject'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255);
		self::$definition['fields']['brief'] = array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 500);
		self::$definition['fields']['certification'] = array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml');
		parent::__construct($id);
	}
}


