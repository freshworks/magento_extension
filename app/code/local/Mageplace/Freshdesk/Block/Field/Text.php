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
 * Class Mageplace_Freshdesk_Block_Field_Abstract
 */
class Mageplace_Freshdesk_Block_Field_Text extends Mageplace_Freshdesk_Block_Field_Abstract
{
    const CLASS_NAME_INPUT_TEXT = 'input-text';

    public function _construct()
    {
        $this->setTemplate('freshdesk/field/text.phtml');

        parent::_construct();
    }

    protected function _beforeToHtml()
    {
        $this->addFieldClass(self::CLASS_NAME_INPUT_TEXT);

        return parent::_beforeToHtml();
    }
}