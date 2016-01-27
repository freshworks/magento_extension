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
 * Class Mageplace_Freshdesk_TicketController
 */
class Mageplace_Freshdesk_TicketController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::helper('freshdesk')->isTicketTabEnabled() || !Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    public function indexAction()
    {
        return $this->_redirect('*/*/list');
    }

    public function listAction()
    {
        try {
            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');

            $this->getLayout()->getBlock('head')->setTitle($this->__('My Tickets'));

            if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
                $block->setRefererUrl($this->_getRefererUrl());
            }

            return $this->renderLayout();

        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
    }

    public function viewAction()
    {
        try {
            if ($this->getRequest()->getParam('after_reply')) {
                /*sleep(3); //Needed for status was changed on freshdesk server*/
                $this->_refreshCache();
            }

            if (!$this->_loadValidTicket()) {
                return;
            }

            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');

            if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
                $navigationBlock->setActive('freshdesk/ticket/list');
            }

            return $this->renderLayout();

        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirectUrl('*/*/list');
    }

    public function editAction()
    {
        return $this->_redirect('*/*/create');
    }

    public function createAction()
    {
        try {
            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');

            if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
                $navigationBlock->setActive('freshdesk/ticket/list');
            }

            if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
                $block->setRefererUrl($this->_getRefererUrl());
            }

            return $this->renderLayout();

        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirectUrl('*/*/list');
    }

    public function saveAction()
    {
        try {
            $post = $this->getRequest()->getPost();
            if (empty($post)) {
                throw new Mageplace_Freshdesk_Exception($this->__('Wrong request data'));
            }

            if (array_key_exists(Mageplace_Freshdesk_Model_Field::FIELD_DISPLAY_ID, $post)) {
                unset($post[Mageplace_Freshdesk_Model_Field::FIELD_DISPLAY_ID]);
            }

            $fields = Mage::getModel('freshdesk/field')->getCollection();
            foreach ($fields as $field) {
                if (!$field->isVisible() || !$field->isEditable()) {
                    if (array_key_exists($field->getName(), $post)) {
                        unset($post[$field->getName()]);
                    } elseif ($field->getFirstLevelFieldName() && array_key_exists($field->getFirstLevelFieldName(), $post)) {
                        unset($post[$field->getFirstLevelFieldName()]);
                    } elseif ($field->getSecondLevelFieldName() && array_key_exists($field->getSecondLevelFieldName(), $post)) {
                        unset($post[$field->getSecondLevelFieldName()]);
                    }
                }
            }

            /*if($this->_getFreshdeskUser()->getId()) {
                $post[Mageplace_Freshdesk_Model_Field::FIELD_REQUESTER_ID] = $this->_getFreshdeskUser()->getId();
            }*/
            $post[Mageplace_Freshdesk_Model_Field::FIELD_EMAIL]          = $this->_getCustomer()->getEmail();
            $post[Mageplace_Freshdesk_Model_Field::FIELD_REQUESTER_NAME] = $this->_getCustomer()->getName();
            $post[Mageplace_Freshdesk_Model_Field::FIELD_STATUS]         = Mage::helper('freshdesk')->getStatusDefault();

            $orderId = (int)$this->getRequest()->getPost('order_id');
            if (!$orderId) {
                $orderId = (int)$this->getRequest()->getParam('order_id');
            }
            if ($orderId && $this->_canViewOrder($orderId)) {
                $orderField = Mage::getModel('freshdesk/field')->getOrderField();
                if (is_object($orderField) && $orderField->isEditable()) {
                    $post[$orderField->getName()] = $orderId;
                }
            }

            Mage::getModel('freshdesk/ticket')
                ->setData($post)
                ->save();

            $this->_getSession()->addSuccess($this->__('Ticket was successfully created'));

            $this->_refreshCache();

            $back = $this->getRequest()->getParam('back');
            if (!$back) {
                $lastTicket = Mage::getModel('freshdesk/ticket')->getCollection()
                    ->setRequester($this->_getCustomer()->getEmail())
                    ->setOrder(Mageplace_Freshdesk_Model_Field::FIELD_DISPLAY_ID, Varien_Data_Collection::SORT_ORDER_DESC)
                    ->load()
                    ->getFirstItem();

                if ($lastTicket->getId()) {
                    return $this->_redirect('*/*/view', array('ticket_id' => $lastTicket->getId()));
                } else {
                    return $this->_redirect('*/*/list');
                }
            } elseif ($back == 'new') {
                return $this->_redirect('*/*/create');
            }

            return $this->_redirect('*/*/list');

        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect('*/*/create');
    }

    public function replyAction()
    {
        $ticketId = (int)$this->getRequest()->getParam('ticket_id');
        if (!$this->_loadValidTicket($ticketId)) {
            return;
        }

        $message = trim($this->getRequest()->getParam('message'));
        if (!$message) {
            return $this->_redirect('*/*/view', array('ticket_id' => $ticketId));
        }

        try {
            $userId = $this->_getFreshdeskUser()->getId();
            if (!$userId) {
                if (is_object(Mage::helper('freshdesk')->getCurrentTicket())) {
                    $userId = Mage::helper('freshdesk')
                        ->getCurrentTicket()
                        ->getRequesterId();
                }
            }

            if (!$userId) {
                $this->_getSession()->addError($this->__('Wrong freshdesk user information'));

                return $this->_redirect('*/*/view', array('ticket_id' => $ticketId));
            }

            Mage::getModel('freshdesk/note')
                ->setTicketId($ticketId)
                ->setUserId($userId)
                ->setBody($message)
                ->save();

            $this->_refreshCache();

            $this->_loadValidTicket();

            return $this->_redirect('*/*/view', array('ticket_id' => $ticketId, 'after_reply' => 1));

        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect('*/*/list');
    }

    public function closeAction()
    {
        $ticketId = (int)$this->getRequest()->getParam('ticket_id');
        if (!$this->_loadValidTicket($ticketId)) {
            return;
        }

        try {
            $statusField = Mage::getModel('freshdesk/field')->loadStatusField();
            if (!$statusField->isEditable()) {
                $this->_getSession()->addError($this->__('Permissions denied'));
                $this->_redirect('*/*/list');

                return;
            }

            if (Mage::getModel('freshdesk/ticket')->close($ticketId)) {
                $this->_getSession()->addSuccess($this->__('Ticket was successfully closed'));
                $this->_refreshCache();
            } else {
                $this->_getSession()->addError($this->__('Ticket wasn\'t closed'));
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/list');
    }

    public function refreshAction()
    {
        try {
            $this->_refreshCache();
            $this->_getSession()->addSuccess($this->__('Cache was successfully refreshed'));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        if ($ticketId = $this->getRequest()->getParam('ticket_id')) {
            $this->_redirect('*/*/view', array('ticket_id' => $ticketId));
        } else {
            $this->_redirect('*/*/list');
        }
    }

    protected function _loadValidTicket($ticketId = null)
    {
        if (null === $ticketId) {
            $ticketId = (int)$this->getRequest()->getParam('ticket_id');
        }

        if (!$ticketId) {
            $this->_forward('noRoute');

            return false;
        }

        try {
            /** @var Mageplace_Freshdesk_Model_Ticket $ticket */
            $ticket = Mage::getModel('freshdesk/ticket')
                ->setRequester($this->_getCustomer()->getEmail())
                ->load($ticketId);
            if ($ticketId = $ticket->getId()) {
                $id = $this->_getFreshdeskUser()->getId();
                if (!$id || $ticket->getRequesterId() != $id) {
                    $collTicket = Mage::getModel('freshdesk/ticket')
                        ->getCollection()
                        ->setRequester($this->_getCustomer()->getEmail())
                        ->addFilter('display_id', $ticketId)
                        ->load()
                        ->getFirstItem();

                    if ($collTicket->getId() != $ticketId) {
                        $this->_forward('noRoute');

                        return false;
                    }
                }

                Mage::helper('freshdesk')->setCurrentTicket($ticket);

                return true;
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/list');

        return false;
    }

    protected function _refreshCache()
    {
        Mageplace_Freshdesk_Model_Freshdesk_Tickets::cleanCache($this->_getCustomer()->getEmail());
        Mageplace_Freshdesk_Model_Freshdesk_Users::cleanCache();
    }

    /**
     * @return Mage_Catalog_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('catalog/session');
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    protected function _getFreshdeskUser()
    {
        return Mage::helper('freshdesk')->getCurrentUser();
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool
     */
    protected function _canViewOrder($orderId)
    {
        $order           = Mage::getModel('sales/order')->load($orderId);
        $customerId      = Mage::getSingleton('customer/session')->getCustomerId();
        $availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, $strict = true)
        ) {
            return true;
        }

        return false;
    }
}