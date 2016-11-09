<?php

/**
 * Class AdminMyModuleAjaxController
 *
 * @property MyModule $module
 */
class AdminMyModuleAjaxController extends ModuleAdminController
{
    /**
     * @request ajax=1
     *          &action=get_items
     */
    public function ajaxProcessGetItems()
    {
        $response = array(
            'items' => array(),
        );

        header('Content-Type: application/json');
        die(Tools::jsonEncode($response));
    }
}
