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
 * Class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Edit_Form_Fields
 */
class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Edit_Form_Fields extends Mage_Core_Block_Template
{
    protected $_helper;

    public function __construct()
    {
        parent::__construct();

        $this->_helper = $this->helper('freshdesk');

        $this->setTemplate('freshdesk/ticket/edit/form/fields.phtml');
    }

    /**
     * @return Mageplace_Freshdesk_Model_Resource_Field_Collection
     */
    public function getFields()
    {
        if ($this->_getData('fields') === null) {
            $this->setData('fields', Mage::getResourceModel('freshdesk/field_collection'));

        }

        return $this->_getData('fields');
    }

    /**
     * @param Mageplace_Freshdesk_Model_Field $field
     *
     * @return string
     */
    public function getFieldHtml($field)
    {
        $block = $this->getLayout()->createBlock(
            'freshdesk/field_row',
            'freshdesk_field_row',
            array('field' => $field)
        );
        if ($block instanceof Mageplace_Freshdesk_Block_Field_Row) {
            return $block->toHtml();
        } else {
            return '';
        }
    }

    public function _helper()
    {
        return $this->_helper;
    }

    protected function _toHtml()
    {
        return parent::_toHtml();
    }
}
