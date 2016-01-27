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
 * Class Mageplace_Freshdesk_Model_Observer
 */
class Mageplace_Freshdesk_Model_Observer
{
    public function processCoreBlockAbstractToHtmlBefore($observer)
    {
        /*if ($this->isTicketTabEnabled() && ($observer->getBlock() instanceof Mage_Customer_Block_Account_Navigation)) {
            $observer->getBlock()->addLink(
                'freshdesk_tickets',
                'freshdesk/ticket/list',
                Mage::helper('freshdesk')->__('My Tickets')
            );
        }*/
    }

    public function processControllerActionPostdispatchContacts($event)
    {
        if (!Mage::helper('freshdesk')->isContactUsFormEnabled()) {
            return;
        }

        try {
            $post = $event->getControllerAction()->getRequest()->getPost();
            if ($post) {
                $messages = $this->_getCustomerSession()->getMessages();
                if ($messages->getLastAddedMessage()->getType() == Mage_Core_Model_Message::SUCCESS) {
                    $check = Mage::getModel('freshdesk/ticket')
                        ->setSubject(Mage::helper('freshdesk')->__('Contact Us form ticket from %s', $post['name']))
                        ->setEmail($post['email'])
                        ->setDescription($post['comment'] . "\n" . Mage::helper('contacts')->__('Telephone') . ': ' . $post['telephone'])
                        ->save();

                    if ($check) {
                        $this->_getCustomerSession()->addSuccess(Mage::helper('freshdesk')->__('Ticket was created'));
                    } else {
                        $this->_getCustomerSession()->addError(Mage::helper('freshdesk')->__('Ticket wasn\'t created'));
                    }
                }
            }
        } catch (Mageplace_Freshdesk_Exception $mfe) {
            $this->_getCustomerSession()->addError(Mage::helper('freshdesk')->__('Ticket wasn\'t created'));
            Mage::logException($mfe);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function processSaveCustomer($observer)
    {
        if(!$this->isTicketTabEnabled()) {
            return;
        }

        try {
            $customer = $observer->getEvent()->getCustomer();
            if ($customer instanceof Mage_Customer_Model_Customer) {
                Mage::getModel('freshdesk/user')->syncCustomer($customer);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    protected function isTicketTabEnabled()
    {
        static $enabled;

        if (null === $enabled) {
            $enabled = Mage::helper('freshdesk')->isTicketTabEnabled();
        }

        return $enabled;
    }

    protected function isFeedbackWidgetEnabled()
    {
        static $enabled;

        if (null === $enabled) {
            $enabled = Mage::helper('freshdesk')->isFeedbackWidgetEnabled();
        }

        return $enabled;
    }

    protected function isSupportLinkEnabled()
    {
        static $enabled;

        if (null === $enabled) {
            $enabled = Mage::helper('freshdesk')->isSupportLinkEnabled();
        }

        return $enabled;
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }
}