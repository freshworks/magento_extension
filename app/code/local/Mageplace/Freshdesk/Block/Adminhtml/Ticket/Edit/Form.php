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
 * Class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Edit_Form
 */
class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('create-ticket-fieldset',
            array(
                'legend' => $this->__('Create Ticket'),
                'class'  => 'fieldset-wide'
            )
        );

        $fieldset->addField('ticket_fields_area',
            'note',
            array(
                'name'  => 'ticket_fields_area',
                'text'  => '<div style="width:50%;">' . $this->getLayout()->createBlock('freshdesk/adminhtml_ticket_edit_form_fields')->setForm($this)->toHtml() . '</div>',
            )
        );

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setMethod('post');

        $form->setAction($this->getSaveUrl());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
