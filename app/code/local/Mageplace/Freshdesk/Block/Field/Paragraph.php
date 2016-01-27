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
class Mageplace_Freshdesk_Block_Field_Paragraph extends Mageplace_Freshdesk_Block_Field_Abstract
{
    const CLASS_NAME_TEXTAREA = 'textarea';

    public function _construct()
    {
        $this->setTemplate('freshdesk/field/paragraph.phtml');

        parent::_construct();
    }

    public function getEditor()
    {
        $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array(
            'enabled'       => $this->isAdminArea(),
            'add_variables' => 0,
            'add_widgets'   => false,
            'add_images'    => false,
        ));

        $editor = new Varien_Data_Form_Element_Editor(array(
            'config'      => $config,
            'html_id'     => $this->getFieldId(),
            'name'        => $this->getFieldName(),
            'title'       => $this->getFieldLabel(),
            'required'    => $this->isRequired(),
            'add_widgets' => 0,
            'add_images'  => 0,
            'plugins'     => 0,

        ));

        $editor->setForm(new Varien_Data_Form());

        return $editor;
    }

    protected function _beforeToHtml()
    {
        $this->addFieldClass(self::CLASS_NAME_TEXTAREA);

        return parent::_beforeToHtml();
    }
}