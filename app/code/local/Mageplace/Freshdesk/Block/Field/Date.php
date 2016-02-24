<?php
/**
 * Class Mageplace_Freshdesk_Block_Field_Abstract
 */
class Mageplace_Freshdesk_Block_Field_Date extends Mageplace_Freshdesk_Block_Field_Abstract
{
    public function _construct()
    {
        $this->setTemplate('freshdesk/field/date.phtml');
        parent::_construct();
    }
}