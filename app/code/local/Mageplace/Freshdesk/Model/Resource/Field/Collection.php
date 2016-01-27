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
 * Class Mageplace_Freshdesk_Model_Resource_Field_Collection
 *
 * @method Mageplace_Freshdesk_Model_Field getNewEmptyItem
 */
class Mageplace_Freshdesk_Model_Resource_Field_Collection extends Mageplace_Freshdesk_Model_Resource_Collection_Abstract
{
    protected $_filedsByNames;

    public function __construct()
    {
        parent::__construct();

        $this->setItemObjectClass('freshdesk/field');
    }

    public function createItem($data)
    {
        $item = $this->getNewEmptyItem()->getFieldModel($data);
        $this->addItem($item);
    }

    public function getItemsByNames()
    {
        if(!is_array($this->_filedsByNames)) {
            $fields = $this->getItems();
            foreach($fields as $field) {
                /** @var Mageplace_Freshdesk_Model_Field $field */
                $this->_filedsByNames[$field->getName()] = $field;
            }
        }

        return $this->_filedsByNames;
    }

    protected function loadFreshdeskData()
    {
        return $this->loadFields();
    }

    protected function loadFields()
    {
        if (!is_array($this->_freshdeskData)) {
            $this->_freshdeskData = $this->getNewEmptyItem()
                ->getFreshdeskModel()
                ->getFields();
            if (!is_array($this->_freshdeskData)) {
                $this->_freshdeskData = array();
            }
        }

        return $this->_freshdeskData;
    }
}