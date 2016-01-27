<?php
/**
 * Mageplace Freshdesk extension
 *
 * @category    Mageplace_Freshdesk
 * @package     Mageplace_Freshdesk
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Freshdesk_Exception extends Mage_Core_Exception
{
    const ERROR_FIELDS = 1;

    public function isWrongConfig()
    {
        switch($this->getCode()) {
            case self::ERROR_FIELDS:
                return true;
        }

        return false;
    }
}