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
 * Class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Edit
 */
class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_objectId = 'ticket_id';
    protected $_blockGroup = 'freshdesk';
    protected $_controller = 'adminhtml_ticket';


    /**
     * Constructor for the category edit form
     */
    public function __construct()
    {
        parent::__construct();

        $this->_removeButton('back');
        $this->_removeButton('reset');
        $this->_removeButton('delete');

        $this->_addButton('save_and_new',
            array(
                'label'   => $this->__('Save and New'),
                'onclick' => 'editForm.submit(\'' . $this->getSaveAndNewUrl() . '\')',
                'class'   => 'save'
            ),
            -110
        );

        /*$this->_addButton('save_and_close',
            array(
                'label'   => $this->__('Save and Close'),
                'onclick' => 'editForm.submit(\'' . $this->getSaveAndCloseUrl() . '\')',
                'class'   => 'save'
            ),
            -100
        );*/

        $this->_addButton('cancel',
            array(
                'label'   => $this->__('Cancel'),
                'onclick' => 'setLocation(\'' . $this->getCancelUrl() . '\')',
                'class'   => 'cancel'
            ),
            -90
        );
    }

    public function getCancelUrl()
    {
        return $this->getUrl('*/freshdesk/ticket');
    }

    public function getSaveAndNewUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current' => true,
            'back'     => 'new',
        ));
    }

    public function getSaveAndCloseUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current' => true,
            'back'     => 'no',
        ));
    }

    public function addFormScripts($js)
    {
        $this->_formScripts[] = $js;
    }

    public function getHeaderText()
    {
        return $this->__('Create Ticket');
    }

    public function getHeaderCssClass()
    {
        return '';
    }
}