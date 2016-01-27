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
 * Class Mageplace_Freshdesk_Block_Customer_Ticket_Abstract
 */
class Mageplace_Freshdesk_Block_Customer_Ticket_Abstract extends Mage_Core_Block_Template
{
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getCustomerEmail()
    {
        if ($this->_getData('customer_email') === null) {
            $this->setData('customer_email', $this->getCustomer()->getEmail());
        }

        return $this->_getData('customer_email');
    }

    public function isFieldVisible($fieldName)
    {
        if ($this->_getData('field_visible_' . $fieldName) === null) {
            $field = Mage::getModel('freshdesk/field')->loadByName($fieldName);

            $this->setData('field_visible_' . $fieldName, is_object($field) && $field->isVisible());
        }

        return $this->_getData('field_visible_' . $fieldName);
    }

    public function isSubjectVisible()
    {
        return $this->isFieldVisible(Mageplace_Freshdesk_Model_Field::FIELD_SUBJECT);
    }

    public function isStatusVisible()
    {
        return $this->isFieldVisible(Mageplace_Freshdesk_Model_Field::FIELD_STATUS);
    }

    public function isOrderColumnVisible()
    {
        $orderField = Mage::getModel('freshdesk/field')->getOrderField();

        return is_object($orderField) && $orderField->isVisible() && $orderField->isEditable();
    }

    public function canAction()
    {
        if ($this->_getData('can_action') === null) {
            $this->setData('can_action', Mage::helper('freshdesk')->isTicketTabEnabled());
        }

        return $this->_getData('can_action');
    }

    /**
     * @param Mageplace_Freshdesk_Model_Ticket $ticket
     *
     * @return bool
     */
    public function canClose($ticket)
    {
        if (!$this->canAction()) {
            return false;
        }

        $statusField = Mage::getModel('freshdesk/field')->loadStatusField();

        return is_object($statusField) && $statusField->isEditable() && $ticket->getStatus() != Mage::helper('freshdesk')->getStatusClose();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('freshdesk/ticket/create');
    }

    public function getViewUrl($ticket)
    {
        return $this->getUrl('freshdesk/ticket/view', array('ticket_id' => $ticket->getDisplayId()));
    }

    public function getCloseUrl($ticket)
    {
        return $this->getUrl('freshdesk/ticket/close', array('ticket_id' => $ticket->getDisplayId()));
    }
}
