<?php

/**
 * Mageplace Freshdesk extension
 *
 * @category    Mageplace_Freshdesk
 * @package     Mageplace_Freshdesk
 * @copyright   Copyright (c) 2014 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */
class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Grid_Column_Renderer_Orders
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {


        if ($index = (int)$row->getData($this->getColumn()->getIndex())) {
            $order = Mage::getModel('sales/order')->load($index);

            if ($orderId = $order->getId()) {
                return '<a href="' . Mage::getUrl('*/sales_order/view/', array('order_id' => $orderId)) . '">#' . $order->getIncrementId() . '</strong>';
            }
        }

        return '---';
    }
}
