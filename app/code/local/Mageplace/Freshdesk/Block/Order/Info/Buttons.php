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
 * Class Mageplace_Freshdesk_Block_Order_Info_Buttons
 */
class Mageplace_Freshdesk_Block_Order_Info_Buttons extends Mageplace_Freshdesk_Block_Customer_Ticket_Abstract
{
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Get url for printing order
     *
     * @param Mage_Sales_Model_Order|null $order
     *
     * @return string
     */
    public function getCreateTicketUrl($order = null)
    {
        if (null === $order) {
            $order = $this->getOrder();
        }

        return Mage::helper('freshdesk')->getCreateOrderTicketUrl($order);
    }

    protected function _toHtml()
    {
        if (Mage::helper('freshdesk')->isTicketTabEnabled()) {
            $orderField = Mage::getModel('freshdesk/field')->getOrderField();
            if (is_object($orderField) && $orderField->isEditable()) {
                return parent::_toHtml();
            }
        }

        return '';
    }
}
