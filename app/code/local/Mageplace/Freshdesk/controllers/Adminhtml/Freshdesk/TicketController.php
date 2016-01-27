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
 * Class Mageplace_Freshdesk_Adminhtml_Freshdesk_TicketController
 */
class Mageplace_Freshdesk_Adminhtml_Freshdesk_TicketController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::helper('freshdesk')->isConfigSet()) {
            $this->_getSession()->addError($this->__('Please fill in Freshdesk configuration options first'));
            $this->_redirect('*/system_config/edit/section/freshdesk');
            $this->getResponse()->sendResponse();
            exit;
        }

        return $this;
    }

    /**
     * Displays the tickets overview grid.
     */
    public function indexAction()
    {
        return $this->_redirect('*/freshdesk/ticket');
    }

    /**
     * Displays the tickets overview grid.
     */
    public function refreshAction()
    {
        $this->_refreshCache();

        return $this->_redirect('*/freshdesk/ticket');
    }

    /**
     * Displays the tickets overview grid.
     */
    public function createAction()
    {
        try {
            return $this->loadLayout()
                ->_setActiveMenu('freshdesk/ticket')
                ->_title($this->__('Freshdesk'))
                ->_title($this->__('Create Ticket'))
                ->_addContent($this->getLayout()->createBlock('freshdesk/adminhtml_ticket_edit'))
                ->renderLayout();

        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect('*/freshdesk/ticket');
    }

    public function saveAction()
    {
        try {
            $post = $this->getRequest()->getPost();
            error_log($post,0);
            if (empty($post)) {
                throw new Mageplace_Freshdesk_Exception($this->__('Wrong request data'));
            }

            Mage::getModel('freshdesk/ticket')
                ->setData($post)
                ->save();

            $this->_getSession()->addSuccess($this->__('Ticket was successfully created'));

            $this->_refreshCache(false);

            if (!$this->getRequest()->getParam('back')) {
                return $this->_redirect('*/freshdesk/ticket');
            }

        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect('*/freshdesk_ticket/create');
    }

    public function editAction()
    {
        $ticketId = (int)$this->getRequest()->getParam('ticket_id');
        $this->_redirectUrl(Mage::getSingleton('freshdesk/freshdesk')->getTicketEditUrl($ticketId));
        Mage::app()->getResponse()->sendResponse();
        exit;
    }

    public function viewAction()
    {
        $ticketId = (int)$this->getRequest()->getParam('ticket_id');
        $this->_redirectUrl(Mage::getSingleton('freshdesk/freshdesk')->getTicketViewUrl($ticketId));
        Mage::app()->getResponse()->sendResponse();
        exit;
    }


    public function closeAction()
    {
        $ticketId   = (int)$this->getRequest()->getParam('ticket_id');
        $customerId = (int)$this->getRequest()->getParam('customer_id');

        try {
            if (Mage::getModel('freshdesk/ticket')->close($ticketId)) {
                $this->_getSession()->addSuccess($this->__('Ticket was successfully closed'));
                $this->_refreshCache(false);
            } else {
                $this->_getSession()->addError($this->__('Ticket wasn\'t closed'));
            }

        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        if ($customerId) {
            return $this->_redirect('*/customer/edit', array('id' => $customerId));
        } else {
            return $this->_redirect('*/freshdesk/ticket');
        }
    }

    protected function _refreshCache($message = true)
    {
        Mage::getModel('freshdesk/cache')->clean();
        Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => Mageplace_Freshdesk_Model_Cache::CACHE_TYPE));

        if ($message) {
            $this->_getSession()->addSuccess($this->__('Cache was successfully refreshed'));
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/freshdesk/tickets');
    }
}