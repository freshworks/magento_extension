<?php
/**
 * Mageplace Freshdesk extension
 *
 * @category    Mageplace_Freshdesk
 * @package     Mageplace_Freshdesk
 * @copyright   Copyright (c) 2014 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

/**
 * Class Mageplace_Freshdesk_Model_Source_Abstract
 */
abstract class Mageplace_Freshdesk_Model_Source_Abstract
{
	abstract public function toOptionArray();

	protected function _getHelper()
	{
		return Mage::helper('freshdesk');
	}

	public function toOptionHash()
	{
		$hash = array();
		foreach($this->toOptionArray() as $item) {
			$hash[$item['value']] = $item['label'];
		}
		
		return $hash;
	}
}