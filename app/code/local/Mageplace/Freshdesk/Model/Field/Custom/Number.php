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
 * Class Mageplace_Freshdesk_Model_Field_Custom_Number
 */
class Mageplace_Freshdesk_Model_Field_Custom_Number extends Mageplace_Freshdesk_Model_Field
{
    public function checkFieldValue($value, &$allValues, &$skipValues)
    {
        parent::checkFieldValue($value, $allValues, $skipValues);

        if ($value && !is_numeric($value)) {
            throw new Mageplace_Freshdesk_Exception(Mage::helper('freshdesk')->__('Field "%s" is not valid, please re-enter', $this->getLabel()));
        }

        return true;
    }
}