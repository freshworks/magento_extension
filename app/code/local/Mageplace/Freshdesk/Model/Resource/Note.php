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
 * Class Mageplace_Freshdesk_Model_Resource_Note
 */
class Mageplace_Freshdesk_Model_Resource_Note extends Varien_Object
{
    public function getFreshdeskModel()
    {
        return Mage::getSingleton('freshdesk/freshdesk_notes');
    }

    /**
     * @param Mageplace_Freshdesk_Model_Note $note
     * @param int                            $id
     * @param null                           $field
     *
     * @return array|null
     */
    public function load($note, $id, $field = null)
    {
        return $note->addData(
            $this->getFreshdeskModel()
                ->getNote($id)
        );
    }

    public function save(Mageplace_Freshdesk_Model_Note $note)
    {
        $this->getFreshdeskModel()
            ->setDataFromArray($note->getData())
            ->saveNote();

        return $this;
    }
}