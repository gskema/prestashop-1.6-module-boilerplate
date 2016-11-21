<?php

require_once(__DIR__.'/vendor/autoload.php');

/**
 * Class MyModule
 *
 * @property bool $bootstrap
 */
class MyModule extends Module
{
    /** Keys for module configuration options. */
    const OPT_TEXT_1 = 'OPT_TEXT_1';
    const OPT_TEXT_2 = 'OPT_TEXT_2';

    /**
     * MyModule constructor.
     */
    public function __construct()
    {
        $this->name = 'mymodule';
        $this->tab  = 'others';
        /**
         * @TODO Choose module tab
         *
         * administration      advertising_marketing  analytics_stats    billing_invoicing
         * checkout            content_management     emailing export    front_office_features
         * i18n_localization   market_place           merchandizing      migration_tools
         * mobile              others                 payments_gateways  payment_security
         * pricing_promotion   quick_bulk_update      search_filter      seo
         * shipping_logistics  slideshows             smart_shopping     social_networks
         */
        $this->version = '1.0.0';
        $this->author  = 'PrestaShop Module Developer';
        $this->need_instance = 0;
        $this->bootstrap = true;

        $this->ps_versions_compliancy = ['min' => '1.6.0.4', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->l('MyModule');
        $this->description = $this->l('Allows X to do Y in Z pages.');

        // @TODO This is a good place to add any Shop::addTableAssociation(...)
    }

    /**
     * Installs module to PrestaShop.
     *
     * @return bool
     */
    public function install()
    {
        try {
            parent::install();
            $installer = new \MyModule\Module\Installer($this);
            $installer->installModule();
            return true;
        } catch (Exception $e) {
            $this->_errors[] = get_class($e).': '.$e->getMessage();
            $this->uninstall();
            return false;
        }
    }

    /**
     * Uninstalls module from PrestaShop.
     *
     * @return bool
     */
    public function uninstall()
    {
        try {
            parent::uninstall();
            $installer = new \MyModule\Module\Installer($this);
            $installer->uninstallModule();
            return true;
        } catch (Exception $e) {
            $this->_errors[] = get_class($e).': '.$e->getMessage();
            return false;
        }
    }

    /**
     * Redirects administrator to module configuration page.
     */
    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminMyModuleConfiguration'));
    }

    /**
     * Adds JS scripts, JS variables and CSS stylesheets to the page.
     *
     * @return void
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->getLocalPath().'views/css/style.css');
        $this->context->controller->addJS([
            $this->getLocalPath().'views/js/front/feature1.js',
        ]);

        $jsVariables = [
            'ajax_url' => $this->context->link->getModuleLink($this->name, 'ajax'),
        ];

        $jsTranslations = [
            'TITLE_MY_ELEMENT' => $this->l('My Element Title'),
        ];

        Media::addJsDef([
            $this->name => [
                'variables'    => $jsVariables,
                'translations' => $jsTranslations,
            ],
        ]);
    }

    /**
     * @param array $args
     *
     * @return string HTML
     */
    public function hookDisplayLeftColumn(array $args)
    {
        return '';
    }

    /**
     * @param array $args
     *
     * @return string HTML
     */
    public function hookDisplayRightColumn(array $args)
    {
        return '';
    }
}
