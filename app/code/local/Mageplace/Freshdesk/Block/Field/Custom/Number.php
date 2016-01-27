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
 * Class Mageplace_Freshdesk_Block_Field_Custom_Text
 */
class Mageplace_Freshdesk_Block_Field_Custom_Number extends Mageplace_Freshdesk_Block_Field_Text
{
    const CLASS_NAME_VALIDATE_NUMBER = 'validate-number';


    protected function _beforeToHtml()
    {
        $this->addFieldClass(self::CLASS_NAME_VALIDATE_NUMBER);

        return Mageplace_Freshdesk_Block_Field_Abstract::_beforeToHtml();
    }
}