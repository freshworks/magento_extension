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
 * Class Mageplace_Freshdesk_Model_Resource_Ticket_Collection
 */
class Mageplace_Freshdesk_Model_Resource_Ticket_Collection extends Mageplace_Freshdesk_Model_Resource_Collection_Abstract
{
    protected $_priority = array();
    protected $_status = array();
    protected $_requester;

    public function __construct()
    {
        parent::__construct();

        $this->setItemObjectClass('freshdesk/ticket');
    }

    public function getPriorities()
    {
        $this->loadTickets();

        return $this->_priority;
    }

    public function getStatuses()
    {
        $this->loadTickets();

        return $this->_status;
    }

    public function setRequester($requester)
    {
        $this->_requester = $requester;

        return $this;
    }

    public function getRequester()
    {
        return $this->_requester;
    }

    protected function loadFreshdeskData()
    {
        return $this->loadTickets();
    }

    protected function loadTickets()
    {
        if (!is_array($this->_freshdeskData)) {
            $requster             = $this->getRequester();
            $email                = strpos($requster, '@') > 0 ? $requster : null;
            $this->_freshdeskData = Mage::getSingleton('freshdesk/freshdesk_tickets')
                ->getTickets($requster);
            if (!is_array($this->_freshdeskData)) {
                $this->_freshdeskData = array();

                return array();
            }

            $orderField = Mage::getModel('freshdesk/field')->getOrderField();
            foreach ($this->_freshdeskData as &$ticket) {
                $orderId = $orderIncrementId = null;
                if (is_object($orderField) && ($orderFieldName = $orderField->getName())) {
                    if (!array_key_exists($orderFieldName, $ticket)) {
                        if (!empty($ticket[Mageplace_Freshdesk_Model_Ticket::CUSTOM_FIELDS])
                            && array_key_exists($orderFieldName, $ticket[Mageplace_Freshdesk_Model_Ticket::CUSTOM_FIELDS])
                        ) {
                            $orderId = $ticket[Mageplace_Freshdesk_Model_Ticket::CUSTOM_FIELDS][$orderFieldName];
                        }
                    } else {
                        $orderId = $ticket[$orderFieldName];
                    }

                    if ($orderId) {
                        if (strpos($orderId, '#') === 0) {
                            $orderId = substr($orderId, 1);
                        }

                        $order = Mage::getModel('sales/order')->load($orderId);
                        if (!$order->getId()) {
                            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
                        }

                        if (is_object($order) && $order->getId() && (!isset($email) || $email == $order->getCustomerEmail())) {
                            $orderId          = $order->getId();
                            $orderIncrementId = $order->getIncrementId();
                        } else {
                            $orderId = $orderIncrementId = null;
                        }
                    }
                }
                $ticket[Mageplace_Freshdesk_Model_Ticket::ORDER_ID]           = $orderId;
                $ticket[Mageplace_Freshdesk_Model_Ticket::ORDER_INCREMENT_ID] = $orderIncrementId;

                $this->_addPriority(
                    $ticket[Mageplace_Freshdesk_Model_Freshdesk_Tickets::FIELD_PRIORITY],
                    $ticket[Mageplace_Freshdesk_Model_Freshdesk_Tickets::FIELD_PRIORITY_NAME]
                );
                $this->_addStatus(
                    $ticket[Mageplace_Freshdesk_Model_Freshdesk_Tickets::FIELD_STATUS],
                    $ticket[Mageplace_Freshdesk_Model_Freshdesk_Tickets::FIELD_STATUS_NAME]
                );
            }
            unset($ticket);
        }

        return $this->_freshdeskData;
    }

    protected function _addPriority($value, $label)
    {
        if (empty($label) || array_key_exists($value, $this->_priority)) {
            return $this;
        }

        $this->_priority[$value] = $label;

        return $this;
    }

    protected function _addStatus($value, $label)
    {
        if (empty($label) || array_key_exists($value, $this->_status)) {
            return $this;
        }

        $this->_status[$value] = $label;

        return $this;
    }
}