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
 * Class Mageplace_Freshdesk_Model_Resource_Ticket
 */
class Mageplace_Freshdesk_Model_Resource_Ticket extends Varien_Object
{
    protected $_requester;

    public function getFreshdeskModel()
    {
        return Mage::getSingleton('freshdesk/freshdesk_tickets');
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

    public function getIdFieldName()
    {
        return Mageplace_Freshdesk_Model_Freshdesk_Tickets::FIELD_DISPLAY_ID;
    }

    /**
     * @param Mageplace_Freshdesk_Model_Ticket $ticket
     * @param int                              $id
     * @param null                             $field
     *
     * @return array|null
     */
    public function load($ticket, $id, $field = null)
    {
        $requester = $this->getRequester() ? $this->getRequester() : $ticket->getRequester();
        return $ticket->addData(
            $this->getFreshdeskModel()
                ->getTicket($id, $requester)
        );
    }

    public function save(Mageplace_Freshdesk_Model_Ticket $ticket)
    {
        $this->getFreshdeskModel()
            ->setTicketFromArray($ticket->getData())
            ->createTicket();

        return $this;
    }
}