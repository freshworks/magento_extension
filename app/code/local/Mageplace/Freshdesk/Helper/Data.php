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
 * Class Mageplace_Freshdesk_Helper_Data
 */
class Mageplace_Freshdesk_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isConfigSet()
    {
        return $this->getDomain() !== '' && $this->getAdminEmail() !== '' && ($this->getPassword() !== '' || $this->getAdminApiKey() !== '');
    }

    public function getDomain()
    {
        return trim(Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_ACCOUNT_DOMAIN));
    }

    public function getAdminEmail()
    {
        return trim(Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_ACCOUNT_EMAIL));
    }

    public function getAdminApiKey()
    {
        return trim(Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_ACCOUNT_API_KEY));
    }

    public function getPassword()
    {
        return Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_ACCOUNT_PASSWORD);
    }

    public function isSSOEnabled()
    {
        return Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_SSO_ENABLED);
    }

    public function getSSOSecret()
    {
        return Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_SSO_SECRET);
    }

    public function getSSOLoginUrl()
    {
        return Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_SSO_LOGIN_URL);
    }

    public function getSSOLogoutUrl()
    {
        return Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_SSO_LOGOUT_URL);
    }

    public function getOrderIdField()
    {
        return Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_ORDERS_ORDER_ID);
    }

    public function isContactUsFormEnabled()
    {
        return Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_CHANNELS_CONTACT_US_ENABLED) == 1;
    }

    public function isFeedbackWidgetEnabled()
    {
        return Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_CHANNELS_FEEDBACK_WIDGET_ENABLED) == 1;
    }

    public function getFeedbackWidgetCode()
    {
        return Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_CHANNELS_FEEDBACK_WIDGET_CODE);
    }

    public function isSupportLinkEnabled()
    {
        return Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_CHANNELS_ENABLE_SUPPORT_LINK) == 1;
    }

    public function getSupportLink()
    {
        try {
            return Mage::getSingleton('freshdesk/freshdesk')->getUrl();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return 'http://www.freshdesk.com';
    }

    public function isCustomerViewEnabled()
    {
        return Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_CUSTOMER_VIEW_ENABLE_CUSTOMER_VIEW) == 1;
    }

    public function isTicketTabEnabled()
    {
        return $this->isCustomerViewEnabled();
        /*&& Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_CUSTOMER_VIEW_ENABLE_TICKET_TAB) == 1;*/
    }

    public function isRecentTicketsGridEnabled()
    {
        return $this->isCustomerViewEnabled()
        && Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_CUSTOMER_VIEW_ENABLE_RECENT_TICKET) == 1;
    }

    public function getPriorityDefault()
    {
        return (int)Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_TICKETS_PRIORITY);
    }

    public function getStatusDefault()
    {
        return (int)Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_TICKETS_STATUS);
    }

    public function getStatusClose()
    {
        return (int)Mage::getStoreConfig(Mageplace_Freshdesk_Helper_Const::XML_TICKETS_STATUS_CLOSE);
    }

    public function getCreateOrderTicketUrl(Mage_Sales_Model_Order $order)
    {
        return $this->_getUrl('freshdesk/ticket/create', array('order_id' => $order->getId()));
    }

    public function setCurrentTicket($ticket)
    {
        Mage::unregister(Mageplace_Freshdesk_Helper_Const::REGISTER_CURRENT_FRESHDESK_TICKET);
        Mage::register(Mageplace_Freshdesk_Helper_Const::REGISTER_CURRENT_FRESHDESK_TICKET, $ticket);
    }

    public function getCurrentTicket()
    {
        return Mage::registry(Mageplace_Freshdesk_Helper_Const::REGISTER_CURRENT_FRESHDESK_TICKET);
    }

    public function getCurrentUser()
    {
        if (null === ($user = Mage::registry(Mageplace_Freshdesk_Helper_Const::REGISTER_CURRENT_FRESHDESK_USER))) {
            Mage::unregister(Mageplace_Freshdesk_Helper_Const::REGISTER_CURRENT_FRESHDESK_USER);

            if (Mage::getSingleton('customer/session')->getCustomer()) {
                $user = Mage::getModel('freshdesk/user')->load(Mage::getSingleton('customer/session')->getCustomer()->getEmail());
            } else {
                $user = false;
            }

            Mage::register(Mageplace_Freshdesk_Helper_Const::REGISTER_CURRENT_FRESHDESK_USER, $user);
        }

        return $user;
    }
}