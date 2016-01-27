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
 * Class Mageplace_Freshdesk_Block_Page_Html_Head_Widget
 */
class Mageplace_Freshdesk_Block_Page_Html_Head_Widget extends Mageplace_Freshdesk_Block_Customer_Ticket_Abstract
{
    protected function _construct()
    {
        parent::_construct();
    }

    public function getFeedbackWidgetCode()
    {
        return Mage::helper('freshdesk')->getFeedbackWidgetCode();
    }

    protected function _toHtml()
    {
        if (Mage::helper('freshdesk')->isFeedbackWidgetEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }
}
