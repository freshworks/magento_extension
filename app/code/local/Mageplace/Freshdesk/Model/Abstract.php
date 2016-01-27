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
 * Class Mageplace_Freshdesk_Model_Abstract
 */
class Mageplace_Freshdesk_Model_Abstract extends Mage_Core_Model_Abstract
{
    public function getFreshdeskModel()
    {
        return $this->getResource()->getFreshdeskModel();
    }

    public function save()
    {
        try {
            $this->_beforeSave();
            if ($this->_dataSaveAllowed) {
                $this->_getResource()->save($this);
                $this->_afterSave();
            }
            $this->_hasDataChanges = false;
        } catch (Exception $e) {
            $this->_hasDataChanges = true;
            Mage::logException($e);
            throw $e;
        }

        return $this;
    }
}