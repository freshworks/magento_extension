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
 * Class Mageplace_Freshdesk_Block_Adminhtml_Config_Feedbackwidget
 */
class Mageplace_Freshdesk_Block_Adminhtml_Config_Feedbackwidget extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '';
        $html .= '<td class="" colspan="4">';
        $html .= '<div class="comment"><textarea>' . $element->getComment() . '</textarea></div>';
        $html .= '</td>';

        return $this->_decorateRowHtml($element, $html);
    }
}
