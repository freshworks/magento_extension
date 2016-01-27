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
 * Class Mageplace_Freshdesk_IndexController
 */
class Mageplace_Freshdesk_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if (!Mage::helper('freshdesk')->isSSOEnabled()) {
            return $this->_redirect('*/ticket/list');
        }

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $name     = $customer->getName();
            $email    = $customer->getEmail();
            $ssoUrl   = Mage::getSingleton('freshdesk/freshdesk')->getSSOUrl($name, $email);
            Mage::log($ssoUrl);
            $this->_redirectUrl($ssoUrl);
            $this->getResponse()->sendResponse();
            exit;
        } else {
            $this->_redirectUrl(Mage::helper('freshdesk/customer')->getLoginUrl());
        }

        return;
    }
}