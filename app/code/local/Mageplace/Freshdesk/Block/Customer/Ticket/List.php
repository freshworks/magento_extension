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
 * Class Mageplace_Freshdesk_Block_Customer_Ticket_List
 *
 * @method Mageplace_Freshdesk_Block_Customer_Ticket_List setTickets
 * @method Mageplace_Freshdesk_Model_Resource_Ticket_Collection getTickets
 */
class Mageplace_Freshdesk_Block_Customer_Ticket_List extends Mageplace_Freshdesk_Block_Customer_Ticket_Grid
{
    public function __construct()
    {
        parent::__construct();

        if (Mage::helper('freshdesk')->isTicketTabEnabled()
            && Zend_Validate::is($this->getCustomerEmail(), 'EmailAddress')
        ) {
            $tickets = Mage::getResourceModel('freshdesk/ticket_collection')
                ->setRequester($this->getCustomerEmail())
                ->setOrder('created_at', 'DESC');

            $this->setTickets($tickets);
        }
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()
            ->createBlock('page/html_pager', 'freshdesk_tickets_pager')
            ->setCollection($this->getTickets());
        $this->setChild('pager', $pager);

        $this->getTickets()->load();

        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function isRefreshButtonVisible()
    {
        return
            Mage::app()->useCache(Mageplace_Freshdesk_Model_Cache::CACHE_TYPE)
            && is_object($this->getTickets())
            && $this->getTickets()->getSize() > 0;
    }

    public function getRefreshUrl()
    {
        return $this->getUrl('freshdesk/ticket/refresh');
    }

    protected function _toHtml()
    {
        if (Mage::helper('freshdesk')->isTicketTabEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }
}
