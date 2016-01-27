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
 * Class Mageplace_Freshdesk_Model_Ticket
 *
 * @method string getSubject
 * @method int getDisplayId
 * @method int getRequesterId
 * @method int getPriority
 * @method int getStatus
 * @method datetime getCreatedAt
 * @method string getPriorityName
 * @method string getStatusName
 * @method array getNotes
 * @method Mageplace_Freshdesk_Model_Ticket setStatus
 * @method Mageplace_Freshdesk_Model_Ticket setDisplayId
 */
class Mageplace_Freshdesk_Model_Ticket extends Mageplace_Freshdesk_Model_Abstract
{
    const ORDER_ID           = 'order_id';
    const ORDER_INCREMENT_ID = 'order_increment_id';
    const CUSTOM_FIELDS      = Mageplace_Freshdesk_Model_Freshdesk_Tickets::FIELD_CUSTOM_FIELD;
    const FIELD_DISPLAY_ID   = Mageplace_Freshdesk_Model_Freshdesk_Tickets::FIELD_DISPLAY_ID;

    protected $_requester;

    protected $_notes = array();

    protected function _construct()
    {
        parent::_construct();

        $this->_init('freshdesk/ticket');
    }

    public function loadFromCollection($id, $field = self::FIELD_DISPLAY_ID)
    {
        /** @var Mageplace_Freshdesk_Model_Resource_Ticket_Collection $tickets */
        $tickets = $this->getCollection();
        if ($this->getRequester()) {
            $tickets->setRequester($this->getRequester());
        }

        $model = null;
        if (null === $field) {
            $tickets = $tickets->getItems();
            $model   = $tickets[$id];
        } else {
            $model = $tickets->addFilter($field, $id)
                ->load()
                ->getFirstItem();
        }

        return $model;
    }

    public function getNoteItems()
    {
        if(empty($this->_notes)) {
            $this->_notes = Mage::getModel('freshdesk/note')->parseTicketNotes($this);
        }

        return $this->_notes;
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

    public function close($ticketId)
    {
        return $this->setDisplayId($ticketId)
            ->setStatus(Mage::helper('freshdesk')->getStatusClose())
            ->save();
    }
}