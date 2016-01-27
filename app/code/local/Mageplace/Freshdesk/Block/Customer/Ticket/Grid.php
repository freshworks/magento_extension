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
 * Class Mageplace_Freshdesk_Block_Customer_Ticket_Grid
 */
class Mageplace_Freshdesk_Block_Customer_Ticket_Grid extends Mageplace_Freshdesk_Block_Customer_Ticket_Abstract
{
    public function isSubjectColumnVisible() {
        return $this->isSubjectVisible();
    }

    public function isIdColumnVisible() {
        return $this->isFieldVisible(Mageplace_Freshdesk_Model_Field::FIELD_DISPLAY_ID);
    }

    public function isDateCreatedColumnVisible() {
        return $this->isFieldVisible(Mageplace_Freshdesk_Model_Field::FIELD_CREATED_AT);
    }

    public function isAgentColumnVisible() {
        return $this->isFieldVisible(Mageplace_Freshdesk_Model_Field::FIELD_AGENT);
    }

    public function isStatusColumnVisible() {
        return $this->isStatusVisible();
    }

    public function isPriorityColumnVisible() {
        return $this->isFieldVisible(Mageplace_Freshdesk_Model_Field::FIELD_PRIORITY);
    }

    public function getOrderUrl($orderId)
    {
        return $this->getUrl('sales/order/view', array('order_id' => $orderId));
    }
}
