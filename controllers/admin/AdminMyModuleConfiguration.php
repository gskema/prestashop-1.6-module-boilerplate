<?php

/**
 * Class AdminMyModuleController
 *
 * @property MyModule $module
 */
class AdminMyModuleConfigurationController extends ModuleAdminController
{
    /**
     * AdminMyModuleConfigurationController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;

        // Options definition must be defined in constructor
        $this->fields_options = $this->getOptionsFormDefinition();
    }

    /**
     * Returns options form definition
     *
     * @return array
     */
    public function getOptionsFormDefinition()
    {
        /** @see http://doc.prestashop.com/display/PS16/Using+the+HelperOptions+class */
        /** @see /admin/themes/default/template/helpers/options/options.tpl */
        return [
            'general' => [
                'title' => $this->l('My Module Configuration'),
                'description' => $this->l('Some description'),
                'submit' => ['title' => $this->l('Save'),],
                'fields' => [
                    MyModule::OPT_TEXT_1 => [
                        'title' => $this->l('Text 1'),
                        'desc' =>  $this->l('Text 1 Description'),
                        'cast' => 'strval',
                        'type' => 'text'
                    ],
                    MyModule::OPT_TEXT_2 => [
                        'title' => $this->l('Text 2'),
                        'desc' => $this->l('Text 2 Description'),
                        'cast' => 'strval',
                        'type' => 'textareaLang',
                        'validation' => 'isCleanHtml',
                        'rte'  => true,
                        'cols' => 9,
                        'rows' => 9,
                    ],
                ],
            ],
        ];
    }

    /**
     * Applies hax to textareas
     *
     * @return string
     */
    public function renderOptions()
    {
        $this->addJS([
            _PS_JS_DIR_.'tiny_mce/tiny_mce.js',
            _PS_JS_DIR_.'admin/tinymce.inc.js',
        ]);

        $iso = $this->context->language->iso_code;
        Media::addJsDef([
            'iso'      => file_exists(_PS_CORE_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en',
            'path_css' => _THEME_CSS_DIR_,
            'ad'       => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_),
        ]);

        // @TODO Custom selector for textareas with RTE option
        $hax = '';
        // $hax = '<script>$(function () { tinySetup({ editor_selector: "textarea-autosize" }); })</script>';

        return parent::renderOptions().$hax;
    }
}
