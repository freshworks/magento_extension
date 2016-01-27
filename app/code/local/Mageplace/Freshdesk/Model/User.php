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
 * Class Mageplace_Freshdesk_Model_User
 *
 * @method string|null getName
 * @method string|null getEmail
 * @method Mageplace_Freshdesk_Model_User setName
 * @method Mageplace_Freshdesk_Model_User setEmail
 */
class Mageplace_Freshdesk_Model_User extends Mageplace_Freshdesk_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();

        $this->_init('freshdesk/user');
    }

    public function syncCustomer(Mage_Customer_Model_Customer $customer)
    {
        if (!$email = $customer->getEmail()) {
            return false;
        }

        $this->load($email);
        if (!$this->getId()) {
            $this->setEmail($email);
        }
        $this->setName($customer->getName());

        $this->save();

        $this->cleanCache();

        return true;
    }

    public function cleanCache()
    {
        Mageplace_Freshdesk_Model_Freshdesk_Users::cleanCache();
    }
}