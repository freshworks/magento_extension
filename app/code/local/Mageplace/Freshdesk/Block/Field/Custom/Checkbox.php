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
 * Class Mageplace_Freshdesk_Block_Field_Custom_Checkbox
 */
class Mageplace_Freshdesk_Block_Field_Custom_Checkbox extends Mageplace_Freshdesk_Block_Field_Abstract
{
    const CLASS_NAME_CHECKBOX = 'checkbox';

    protected $_fieldRowTemplate = 'freshdesk/field/row_inline.phtml';
    protected $_fieldRowAdminTemplate = 'freshdesk/field/row_1_column.phtml';

    public function _construct()
    {
        $this->setTemplate('freshdesk/field/checkbox.phtml');

        parent::_construct();
    }

    protected function _beforeToHtml()
    {
        $this->addFieldClass(self::CLASS_NAME_CHECKBOX);

        return parent::_beforeToHtml();
    }
}