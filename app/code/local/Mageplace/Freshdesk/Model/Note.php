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
 * Class Mageplace_Freshdesk_Model_Note
 *
 * @method Mageplace_Freshdesk_Model_Note setTicketId
 * @method Mageplace_Freshdesk_Model_Note setBody
 * @method Mageplace_Freshdesk_Model_Note setUserName
 * @method int|null getTicketId
 * @method int|null getUserId
 * @method string getUserName
 * @method Mageplace_Freshdesk_Model_User getUser
 * @method Mageplace_Freshdesk_Model_Note setUser
 */
class Mageplace_Freshdesk_Model_Note extends Mageplace_Freshdesk_Model_Abstract
{
    const NOTE = Mageplace_Freshdesk_Model_Freshdesk_Notes::NOTE;

    protected function _construct()
    {
        parent::_construct();

        $this->_init('freshdesk/note');
    }

    /**
     * @param array                            $notes
     * @param Mageplace_Freshdesk_Model_Ticket $ticket
     *
     * @return array
     */
    public function parseTicketNotes($ticket, $notes = null)
    {
        $this->setTicketId($ticket->getId());

        $notesParsed = array();

        if (null === $notes) {
            $notes = $ticket->getNotes();
        }

        foreach ($notes as $note) {
            $noteModel = clone $this;

            if (!empty($note[self::NOTE])) {
                $noteModel->addData($note[self::NOTE]);
            } else {
                $noteModel->addData($note);
            }

            if ($noteModel->getUserId() > 0) {
                $user = Mage::getModel('freshdesk/user')->load($noteModel->getUserId());
                $noteModel->setUser($user);
                $noteModel->setUserName($user->getName());
                unset($user);
            }

            $notesParsed[] = $noteModel;
        }

        return $notesParsed;
    }
}