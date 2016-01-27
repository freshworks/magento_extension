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
 * Class Mageplace_Freshdesk_Model_Resource_Field
 */
class Mageplace_Freshdesk_Model_Resource_Field extends Varien_Object
{
    public function getFreshdeskModel()
    {
        return Mage::getSingleton('freshdesk/freshdesk_fields');
    }
}