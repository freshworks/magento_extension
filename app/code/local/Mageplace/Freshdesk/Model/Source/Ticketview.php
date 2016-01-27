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
 * Class Mageplace_Freshdesk_Model_Source_Ticketview
 */
class Mageplace_Freshdesk_Model_Source_Ticketview extends Mageplace_Freshdesk_Model_Source_Abstract
{
    const NO  = 0;
    const YES = 1;

    public function toOptionArray()
    {
        $return   = array();
        $return[] = array('value' => self::NO, 'label' => $this->_getHelper()->__('No, they will have to use your Freshdesk portal'));
        $return[] = array('value' => self::YES, 'label' => $this->_getHelper()->__('Yes, My Account will have My Tickets to view, reply and close tickets'));

        return $return;
    }
}
