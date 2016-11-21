<?php

/**
 * Class MyModuleActionOneModuleFrontController
 *
 * @property MyModule $module
 *
 * @TODO List lifecycle methods here and describe where to put what
 */
class MyModuleActionOneModuleFrontController extends ModuleFrontController
{
    /**
     * __construct                       // Construct basic controller object with dependencies
     * run                               // Runs the execution logic below
     *   init                            // SSL redirects, auth redirects, geolocation, logout, canonical URL, contextual variables, maintenance page
     *   !checkAccess                    // Checks if the customer can access this functionality (these pages) at all.
     *     ? initCursedPage()            // Outputs ACCESS DENIED page
     *     :
     *       !ajax
     *         ? setMedia                // Queues media assets (.js, .css, etc.)
     *       postProcess                 // Place to process POST or GET variables
     *       redirect_after
     *         ? redirect                // Redirects to $this->redirect_after URL.
     *       !ajax
     *         ? initHeader              // Prepares variables for header.tpl (no hooks)
     *       viewAccess
     *         ? process,                // Allows adding Smarty variables without overwriting initContent
     *           initContent             // Prepares page content variables and executes hooks (header, top, left_column, right_column)
     *         : +error                  // Adds error 'Access denied.' to $this->errors. You must output {$errors} yourself.
     *       !ajax
     *         ? initFooter              // Prepares variables for footer.tpl and executes hooks (footer)
     *       ajax
     *         ? displayAjax{ACTION}     // Where AJAX response is generated
     *         : display                 // Compiles and outputs header, content and footer.
     */

    /**
     * Controller initialization
     */
    public function init()
    {
        parent::init();

        // @TODO Set page template for this controller
        $this->template = $this->getTemplatePath('action_one.tpl');
    }

    /**
     * Checks for CSRF token when controller is accessed via Ajax
     *
     * @return bool
     */
    public function checkAccess()
    {
        if ($this->ajax && !$this->isTokenValid()) {
            $response = [
                'status'  => 0,
                'message' => Tools::displayError('Invalid token.'),
            ];

            header('Content-Type: application/json');
            die(Tools::jsonEncode($response));
        }

        return true;
    }

    /**
     * Add CSS and JS files here
     */
    public function setMedia()
    {
        parent::setMedia();

        // @TODO Queue .js scripts and .css files here
        // $this->context->controller->addJS($this->module->getLocalPath().'views/js/fo.js');
        // $this->context->controller->addCSS($this->module->getLocalPath().'views/css/fo.css');
    }

    /**
     * Initializes page content
     *
     * @throws PrestaShopException
     */
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign([
            // @TODO Assign template variables
        ]);

        Media::addJsDef([
            'mymodule' => [
                'var1' => true,
                'var2' => false,
            ],
        ]);
    }

    /**
     * Processes submitted input
     */
    public function postProcess()
    {
        parent::postProcess();

        // @TODO Process submitted user input here
    }
}
