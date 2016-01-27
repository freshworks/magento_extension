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
 * Class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Grid_Column_Renderer_Ordersincrements
 */
class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Grid_Column_Renderer_Orderincrements
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
        if ($index = $row->getData($this->getColumn()->getIndex())) {
            $orderId = $row->getData(Mageplace_Freshdesk_Model_Ticket::ORDER_ID);
            if ($orderId) {
                return '<a href="' . Mage::getUrl('*/sales_order/view/', array('order_id' => $orderId)) . '">#' . $index . '</strong>';
            } else {
                return $index;
            }
        }

        return '---';
    }
}
