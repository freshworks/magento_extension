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
 * Class Mageplace_Freshdesk_Block_Customer_Ticket_Recent
 *
 * @method Mageplace_Freshdesk_Block_Customer_Ticket_Recent setTickets
 * @method array getTickets
 */
class Mageplace_Freshdesk_Block_Customer_Ticket_Recent extends Mageplace_Freshdesk_Block_Customer_Ticket_Grid
{
    public function __construct()
    {
        parent::__construct();

        if (Mage::helper('freshdesk')->isRecentTicketsGridEnabled()
            && Zend_Validate::is($this->getCustomerEmail(), 'EmailAddress')
        ) {
            $tickets = Mage::getResourceModel('freshdesk/ticket_collection')
                ->setRequester($this->getCustomerEmail())
                ->setOrder('created_at', 'DESC')
                ->setPageSize('5')
                ->setCurPage(1)
                ->load();

            $this->setTickets($tickets);
        }
    }

    public function getViewAllUrl()
    {
        return $this->getUrl('freshdesk/ticket/list');
    }

    protected function _toHtml()
    {
        if (Mage::helper('freshdesk')->isRecentTicketsGridEnabled() && is_object($this->getTickets()) && $this->getTickets()->getSize() > 0) {
            return parent::_toHtml();
        }

        return '';
    }
}
