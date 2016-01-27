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
 * Class Mageplace_Freshdesk_Model_Resource_User
 */
class Mageplace_Freshdesk_Model_Resource_User extends Varien_Object
{
    public function getFreshdeskModel()
    {
        return Mage::getSingleton('freshdesk/freshdesk_users');
    }

    public function getIdFieldName()
    {
        return Mageplace_Freshdesk_Model_Freshdesk_Users::PARAM_ID;
    }

    /**
     * @param Mageplace_Freshdesk_Model_User $user
     * @param int                            $id
     * @param null                           $field
     *
     * @return array|null
     */
    public function load($user, $id, $field = null)
    {
        return $user->addData(
            $this->getFreshdeskModel()
                ->getUser($id)
        );
    }

    public function save(Mageplace_Freshdesk_Model_User $user)
    {
        $this->getFreshdeskModel()
            ->setDataFromArray($user->getData())
            ->saveUser();

        return $this;
    }
}