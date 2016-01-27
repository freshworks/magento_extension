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
 * Class Mageplace_Freshdesk_Model_Source_Priority
 */
class Mageplace_Freshdesk_Model_Source_Priority extends Mageplace_Freshdesk_Model_Source_Abstract
{
    public function toOptionArray()
    {
        $priorities = Mage::getModel('freshdesk/ticket')
            ->getCollection()
            ->getPriorities();

        $return = array();
        foreach ($priorities as $value => $label) {
            $return[] = array('value' => $value, 'label' => $label);
        }

        return $return;
    }
}