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
 * Class Mageplace_Freshdesk_Block_Adminhtml_Customer_Edit_Tab_View_Tickets
 */
class Mageplace_Freshdesk_Block_Adminhtml_Customer_Edit_Tab_View_Tickets extends Mage_Core_Block_Abstract
{
    protected function _prepareLayout()
    {
        /** @var Mage_Adminhtml_Block_Customer_Edit_Tab_View_Accordion $accordion */
        $accordion = $this->getLayout()->getBlock('accordion');
        if ($accordion instanceof Mage_Adminhtml_Block_Customer_Edit_Tab_View_Accordion) {
            $accordion->addItem('freshdesk_tickets', array(
                'title'       => Mage::helper('freshdesk')->__('Recent Tickets'),
                'ajax'        => true,
                'content_url' => $this->getUrl('*/freshdesk/customerView', array('_current' => true)),
            ));
        } else {
            Mage::getSingleton('adminhtml/session')->addWarning("Can't get parent block for Recent Tickets");
            Mage::logException(new Mageplace_Freshdesk_Exception("Can't get parent block for Recent Tickets"));
        }
    }
}
