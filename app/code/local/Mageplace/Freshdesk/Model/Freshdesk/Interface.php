<?php
/**
 * Mageplace Freshdesk extension
 *
 * @category    Mageplace_Freshdesk
 * @package     Mageplace_Freshdesk
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

interface Mageplace_Freshdesk_Model_Freshdesk_Interface
{
	/**
	 * @return Mageplace_Freshdesk_Model_Freshdesk
	 */
	public function getFreshdesk();

	public function request($method = null);
}