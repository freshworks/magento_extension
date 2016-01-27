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
 * Class Mageplace_Freshdesk_Block_Customer_Ticket_View
 *
 * @method Mageplace_Freshdesk_Model_Ticket getTicket
 * @method array getNotes
 * @method Mageplace_Freshdesk_Model_User getUser
 * @method Mageplace_Freshdesk_Block_Customer_Ticket_View setTicket
 * @method Mageplace_Freshdesk_Block_Customer_Ticket_View setNotes
 * @method Mageplace_Freshdesk_Block_Customer_Ticket_View setUser
 */
class Mageplace_Freshdesk_Block_Customer_Ticket_View extends Mageplace_Freshdesk_Block_Customer_Ticket_Abstract
{
    const TAG_MESSAGE = '{{MESSAGE}}';

    public function __construct()
    {
        parent::__construct();

        if (Mage::helper('freshdesk')->isTicketTabEnabled()) {
            $this->setTicket(Mage::helper('freshdesk')->getCurrentTicket());
            $this->setNotes(Mage::helper('freshdesk')->getCurrentTicket()->getNoteItems());

            $user = Mage::helper('freshdesk')->getCurrentUser();
            if (!$user->getId()) {
                $user = Mage::getModel('freshdesk/user')->load(Mage::helper('freshdesk')->getCurrentTicket()->getRequesterId());
            }
            $this->setUser($user);
        }
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getTitle());
        }

        return $this;
    }

    public function getTitle()
    {
        if (null === $this->_getData('title')) {
            if (($ticket = $this->getTicket()) && is_object($ticket)) {
                if ($this->isStatusVisible()) {
                    $this->setData('title', $this->__('Ticket #%1$d - %2$s', $ticket->getDisplayId(), $ticket->getStatusName()));
                } else {
                    $this->setData('title', $this->__('Ticket #%d', $ticket->getDisplayId()));
                }
            } else {
                $this->setData('title', '');
            }
        }

        return $this->_getData('title');
    }

    public function getSubject()
    {
        if (null === $this->_getData('subject')) {
            if (($ticket = $this->getTicket()) && is_object($ticket) && $this->isSubjectVisible()) {
                $this->setData('subject', $ticket->getSubject());
            } else {
                $this->setData('subject', '');
            }
        }

        return $this->_getData('subject');
    }

    public function getDateDiff($date, $word)
    {
        $curTime    = gmdate('U');
        $neededTime = strtotime($date);

        $diff = $curTime - $neededTime;
        if (($years = floor($diff / (365 * 24 * 60 * 60))) > 0) {
            return $this->__('%1$s about %2$d year(s) ago', $word, $years);
        } elseif (($month = floor($diff / (30 * 24 * 60 * 60))) > 0) {
            return $this->__('%1$s about %2$d month(s) ago', $word, $month);
        } elseif (($day = floor($diff / (24 * 60 * 60))) > 0) {
            return $this->__('%1$s about %2$d day(s) ago', $word, $day);
        } elseif (($hour = floor($diff / (60 * 60))) > 0) {
            return $this->__('%1$s about %2$d hour(s) ago', $word, $hour);
        } elseif (($minute = floor($diff / (60))) > 0) {
            return $this->__('%1$s about %2$d minute(s) ago', $word, $minute);
        } else {
            return $this->__('%1$s about %2$d second(s) ago', $word, $diff);
        }
    }

    public function isRefreshButtonVisible()
    {
        return Mage::app()->useCache(Mageplace_Freshdesk_Model_Cache::CACHE_TYPE);
    }

    public function getRefreshUrl()
    {
        return $this->getUrl('freshdesk/ticket/refresh', array('ticket_id' => $this->getTicket()->getId()));
    }

    public function getMessageTag()
    {
        return self::TAG_MESSAGE;
    }

    public function getSendReplyUrl()
    {
        return $this->getUrl('freshdesk/ticket/reply', array('ticket_id' => $this->getTicket()->getId(), 'message' => $this->getMessageTag()));
    }

    public function getBackUrl()
    {
        return Mage::getUrl('*/*/list');
    }

    public function getCloseUrl($ticket = null)
    {
        if (null === $ticket) {
            $ticket = $this->getTicket();
        }

        return parent::getCloseUrl($ticket);
    }

    public function getBackTitle()
    {
        return Mage::helper('sales')->__('Back to My Tickets');
    }

    protected function _toHtml()
    {
        if (Mage::helper('freshdesk')->isTicketTabEnabled() && is_object($this->getTicket()) && $this->getTicket()->getId()) {
            return parent::_toHtml();
        }

        return '';
    }
}
