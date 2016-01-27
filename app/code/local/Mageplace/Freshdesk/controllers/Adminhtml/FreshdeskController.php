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
 * Class Mageplace_Freshdesk_Adminhtml_FreshdeskController
 */
class Mageplace_Freshdesk_Adminhtml_FreshdeskController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::helper('freshdesk')->isConfigSet()) {
            $message = $this->__('Please fill in Freshdesk configuration options first');
            if ($this->getRequest()->getActionName() == 'customerView') {
                echo $message;
            } else {
                $this->_getSession()->addError($message);
                $this->_redirect('*/system_config/edit/section/freshdesk');
                $this->getResponse()->sendResponse();
            }
            exit;
        }

        return $this;
    }

    /**
     * Displays the tickets overview grid.
     */
    public function ticketAction()
    {
        try {
            return $this->loadLayout()
                ->_setActiveMenu('freshdesk/ticket')
                ->_title($this->__('Freshdesk'))
                ->_title($this->__('Manage Tickets'))
                ->_addContent($this->getLayout()->createBlock('freshdesk/adminhtml_ticket'))
                ->renderLayout();
        } catch (Mageplace_Freshdesk_Exception $mfe) {
            $this->_getSession()->addError($mfe->getMessage());
            $this->_getSession()->addError($this->__('Please check Freshdesk configuration options'));
            if ($mfe->isWrongConfig()) {
                $this->_redirect('*/system_config/edit/section/freshdesk');
            } else {
                $this->_redirect('*');
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*');
        }
    }

    public function portalAction()
    {
        $this->_redirectUrl(Mage::getSingleton('freshdesk/freshdesk')->getDashboardUrl());
        Mage::app()->getResponse()->sendResponse();
        exit;
    }

    public function customerViewAction()
    {
        if (!is_object(Mage::registry('current_customer'))) {
            $customerId = $this->getRequest()->getParam('id');
            $customer   = Mage::getModel('customer/customer')->load($customerId);
            Mage::unregister('current_customer');
            Mage::register('current_customer', $customer);
        }

        $content = '';
        try {
            $content = $this->getLayout()
                ->createBlock('freshdesk/adminhtml_ticket_grid')
                ->toHtml();
        } catch (Mageplace_Freshdesk_Exception $mfe) {
            $content .= $mfe->getMessage();
            $content .= $this->__('Please check Freshdesk configuration options');
        } catch (Exception $e) {
            $content .= $e->getMessage();
        }

        $this->getResponse()->setBody($content);
    }

    protected function _isAllowed()
    {
        $action = $this->getRequest()->getActionName();

        if (in_array($action, array('portal'))) {
            return Mage::getSingleton('admin/session')->isAllowed('admin/freshdesk/portal');
        }

        return Mage::getSingleton('admin/session')->isAllowed('admin/freshdesk/tickets');
    }
}