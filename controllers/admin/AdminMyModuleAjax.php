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
        // $url = $this->context->link->getAdminLink('AdminMyModuleAjax').'&ajax=1&action=get_items';

        $response = [
            'items' => [],
        ];

        header('Content-Type: application/json');
        die(Tools::jsonEncode($response));
    }
}
