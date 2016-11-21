<?php

/**
 * Class AdminMyModule_Model4Controller
 *
 * @property MyModule                  $module
 * @property MyModule_Model4 $object
 */
class AdminMyModule_Model4Controller extends ModuleAdminController
{
    /**
     * AdminMyModule_Model4Controller constructor.
     */
    public function __construct()
    {
        Shop::addTableAssociation('mm_model4', ['type' => 'shop']);

        // @TODO Add this if you want to have translations per-shop basis
        // Shop::addTableAssociation('mm_model4_lang', ['type' => 'fk_shop']);

        $this->className   = 'MyModule_Model4';
        $this->table       = 'mm_model4';

        // If not specified, will be constructed like this: 'id_'.$this->table
        $this->identifier = 'id_mm_model4';

        // Used when table has position field
        $this->position_identifier = 'id_mm_model4';

        $this->_defaultOrderBy  = 'position';
        $this->_defaultOrderWay = 'asc';
        $this->bootstrap = true;

        parent::__construct();
    }

    /**
     * Renders options panel
     *
     * @return string
     */
    public function renderOptions()
    {
        // @TODO this must be declared in the constructor
        $this->fields_options = [
            'general' => [
                'title' =>    $this->l('Object options'),
                'fields' =>    [
                    'MYMODULE_PARAM_1' => [
                        'title' => $this->l('MyModule parameter 1'),
                        'desc' => $this->l('An option associated with the object.'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ],
                ],
                'submit' => ['title' => $this->l('Save')]
            ],
        ];

        return parent::renderOptions();
    }

    /**
     * Clears cache on updating item options
     *
     * @throws PrestaShopException
     */
    protected function processUpdateOptions()
    {
        parent::processUpdateOptions();
        // @TODO Trigger cache clear on options change, when templates are affected by these options
        // Hook::exec('actionMyModule_Model4OptionsChanged');
    }

    /**
     * Adds no_follow toggle action to process routing
     */
    public function initProcess()
    {
        parent::initProcess();

        // Property 'active' gets processed automatically

        // @TODO Use if you have additional boolean columns in the list, set correct process function
        if (Tools::getValue($this->identifier)) {
            if (Tools::getIsset('toggleable'.$this->table) || Tools::getIsset('toggleable')) {
                if ($this->tabAccess['edit'] === '1') {
                    $this->action = 'toggleable';
                } else {
                    $this->errors[] = Tools::displayError('You do not have permission to edit this.');
                }
            }
        }
    }

    /**
     * Processes toggling toggleable values through admin list
     *
     * @throws PrestaShopException
     */
    public function processToggleable()
    {
        /** @var MyModule_Model4 $object */
        $object = $this->loadObject();
        if (!Validate::isLoadedObject($object)) {
            $this->errors[] = Tools::displayError('An error occurred while updating item information.');
        }
        $object->toggleable = !$object->toggleable;
        if (!$object->update()) {
            $this->errors[] = Tools::displayError('An error occurred while updating item information.');
        }
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function getList(
        $id_lang,
        $order_by = null,
        $order_way = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = false
    ) {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
        // @TODO Format list values in $this->_list to be more user friendly
    }

    /**
     * Renders a list of item objects.
     * Position column is only visible in the shop context
     *
     * @return false|string
     */
    public function renderList()
    {
        // $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = [
            'delete' => [
                'text'    => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
            ],
        ];
        $this->fields_list = [
            'id_mm_model4' => ['title' => $this->l('ID'),],
            'property1'          => ['title' => $this->l('Property 1'),],
            'toggleable' => [
                'title'   => $this->l('Toggleable Property'),
                'align'   => 'center',
                'active'  => 'bool_prop',
                'type'    => 'bool',
                'class'   => 'fixed-width-sm',
                'orderby' => false,
            ],
            'text' => [
                'title'   => $this->l('Text'),
                'width'   => 'auto',
            ],
            'content' => [
                'title'   => $this->l('Content'),
                'width'   => 'auto',
            ],
            'active' => [
                'title'   => $this->l('Active'),
                'align'   => 'center',
                'active'  => 'status',
                'type'    => 'bool',
                'class'   => 'fixed-width-sm',
                'orderby' => false,
            ],
            'position' => [
                'title'      => $this->l('Position'),
                'filter_key' => 'a!position', // 'a' corresponds to table alias in SELECT query
                'align'      => 'center',
                'class'      => 'fixed-width-sm',
                'position'   => 'position'
            ],
        ];

        // $this->informations[] = $this->l('Here is an informative message helping the administrator.');

        return parent::renderList();
    }

    /**
     * Renders item add/edit form
     *
     * @return string
     */
    public function renderForm()
    {
        // @TODO Add extra .js and .css here, for example: autocomplete script
        // $this->addCSS($this->module->getLocalPath().'views/css/bo.css');
        // $this->addJS($this->module->getLocalPath().'views/js/bo.js');

        // @TODO Pass module variables to script, e.g. autocomplete value list
        Media::addJsDef([
            'mymodule' => [
                'values' => [],
            ],
        ]);

        // @TODO Define form fields for add/edit form
        $this->fields_form = [
            'tinymce' => true, // Use only if you have HTML fields
            'legend' => [
                'title' => $this->l('Edit/Add item'),
            ],
            'input' => [
                [
                    'name'  => 'property1',
                    'type'  => 'text',
                    'label' => $this->l('Property 1'),
                    'desc'  => $this->l('Here I property 1 to the user.'),
                    'hint'  => $this->l('Some extra formatting tip: forbidden characters, allowed values, etc.'),
                ],
                [
                    'name'  => 'text',
                    'type'  => 'text',
                    'lang'  => true,
                    'label' => $this->l('Text'),
                    'desc'  => $this->l('This text will be used or displayed somewhere.'),
                    'hint'  => $this->l('Some extra formatting tip: forbidden characters, allowed values, etc.'),
                ],
                [
                    'name'  => 'content',
                    'type'  => 'text',
                    'autoload_rte' => true, // Enables RTE editor for this field
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'hint' => $this->l('Invalid characters:').' <>;=#{}',
                    'label' => $this->l('Content'),
                    'desc'  => $this->l('This HTML will be output somewhere.'),
                ],
                [
                    'type'    => 'switch',
                    'label'   => $this->l('Toggleable'),
                    'name'    => 'toggleable',
                    'class'   => 't',
                    'is_bool' => true,
                    'values'  => [
                        ['id' => 'toggleable_on',  'value' => 1, 'label' => $this->l('Yes')],
                        ['id' => 'toggleable_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                    'desc' => $this->l('If enabled, this will be true.')
                ],
                [
                    'type'    => 'switch',
                    'label'   => $this->l('Active'),
                    'name'    => 'active',
                    'class'   => 't',
                    'is_bool' => true,
                    'values'  => [
                        ['id' => 'active_on',  'value' => 1, 'label' => $this->l('Yes')],
                        ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                    'desc' => $this->l('If disabled, this will be false.')
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        return parent::renderForm();
    }

    /**
     * Updates positions submitted via Ajax
     */
    public function ajaxProcessUpdatePositions()
    {
        $id_shop = Shop::getContextShopID();
        // Build an array of order IDs (could be a page!)
        $submittedIds = [];
        $rows = (array)Tools::getValue($this->table);
        foreach ($rows as $row) {
            $ids = explode('_', $row);
            $submittedIds[] = (int)$ids[2];
        }

        // Get all IDs from database
        $sql = new DbQuery();
        $sql->select('id_mm_model4');
        $sql->from('mm_model4_shop');
        $sql->where('id_shop = '.(int)$id_shop);
        $sql->orderBy('position ASC');
        $rows = (array)Db::getInstance()->executeS($sql);

        $allIds = [];
        foreach ($rows as $row) {
            $allIds[] = (int)$row['id_mm_model4'];
        }

        // Go through all IDs, if the ID exists in the sorted ID list (could be fragment (page) of sorted IDs)
        // then pick an ID from sorted ID list and overwrite the value.
        $i = 0;
        foreach ($allIds as $key1 => $id) {
            $key2 = array_search($id, $submittedIds);
            if ($key2 !== false) {
                $allIds[$key1] = $submittedIds[$i++];
            }
        }

        // Update positions of all values the way the are ordered in the array
        $position  = 0;
        $isSuccess = true;
        $shopIDs   = Shop::getContextListShopID();
        foreach ($allIds as $id_mm_model4) {
            $isSuccess &= Db::getInstance()->update(
                $this->table,
                ['position' => $position++,],
                'id_mm_model4 = '.(int)$id_mm_model4
                .' AND id_shop IN ('.implode(', ', $shopIDs).')'
            );
            if (!$isSuccess) {
                break;
            }
        }

        // @TODO Trigger cache clear since we are inserting in DB directly
        Hook::exec('actionMyModule_Model4PositionsChanged');

        if ($isSuccess) {
            die(true);
        } else {
            header('Content-Type: application/json');
            die(Tools::jsonEncode([
                'hasError' => true,
                'errors'   => $this->l('Could not update positions in the database table.')
            ]));
        }
    }
}
