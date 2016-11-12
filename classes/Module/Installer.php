<?php

namespace MyModule\Module;

use \MyModule;
use \Db;
use \Tab;
use \Language;
use \MyModule\Core\Translatable;

/**
 * Class Installer
 * @package MyModule\Module
 */
class Installer
{
    use Translatable;

    /** @var MyModule */
    protected $module;

    /** @var Db */
    protected $db;

    /**
     * InstallHelper constructor.
     *
     * @param MyModule $module
     */
    public function __construct(MyModule $module)
    {
        $this->module = $module;
        $this->db = Db::getInstance();
    }

    /**
     * Installs module to PrestaShop
     */
    public function installModule()
    {
        // Check you module requirements, throw an Exception

        // Make directories for images, caches, logs, etc.

        // Install database tables
        // $this->db->execute($this->getDatabaseMigrationSQL('up/model1'));

        // Register hooks
        $this->module->registerHook(array(
            // 'displayHeader',
        ));

        // Install admin controllers
        $this->installAdminController('AdminMyModuleConfiguration', 'MyModule Configuration');
    }

    /**
     * Uninstalls module from PrestaShop
     */
    public function uninstallModule()
    {
        // $this->db->execute($this->getDatabaseMigrationSQL('down'));
        $this->uninstallAdminController('AdminMyModuleConfiguration');
    }

    /**
     * Returns SQL string from migration file
     *
     * @param string $name
     * @return string|false
     */
    public function getDatabaseMigrationSQL($name)
    {
        $filePath = $this->module->getLocalPath().'database/migrations/'.$name.'.sql';

        if (!file_exists($filePath)) {
            return false;
        }

        return str_replace(
            array(
                '`ps_',
                'TABLE `ps_',
                'TABLE ps_',
                'EXISTS `ps_',
                'EXISTS ps_',
                'ENGINE = INNODB'
            ),
            array(
                '`'._DB_PREFIX_,
                'TABLE `'._DB_PREFIX_,
                'TABLE '._DB_PREFIX_,
                'EXISTS `'._DB_PREFIX_,
                'EXISTS '._DB_PREFIX_,
                'ENGINE = '._MYSQL_ENGINE_
            ),
            file_get_contents($filePath)
        );
    }

    /**
     * Returns language IDs
     *
     * @param bool     $active
     * @param int|null $id_shop
     *
     * @return array
     */
    public static function getLangIds($active = false, $id_shop = null)
    {
        $ids = array();
        foreach (Language::getLanguages($active, $id_shop) as $lang) {
            $ids[] = $lang['id_lang'];
        }

        return $ids;
    }

    /**
     * Installs a module admin controller and a back-office tab (optional)
     *
     * @param string     $className Controller class name without word 'Controller' at the end
     * @param string     $tabTitle  Single string or a language array
     * @param string|int $tabParent Parent tab class name or ID
     *
     * @return int|false
     */
    protected function installAdminController($className, $tabTitle = '', $tabParent = -1)
    {
        $title = empty($tabTitle) ? $className : $tabTitle;

        $tab = new Tab();
        $tab->class_name = $className;
        $tab->module     = $this->module->name;
        $tab->name       = is_array($title) ? $title : array_fill_keys(self::getLangIds(), $title);

        if (!empty($tabParent) && is_string($tabParent)) {
            $tab->id_parent = (int)Tab::getIdFromClassName($tabParent);
        } elseif (is_int($tabParent)) {
            $tab->id_parent = $tabParent;
        } else {
            $tab->id_parent = 0;
        }

        return $tab->add() ? (int)$tab->id : false;
    }

    /**
     * Uninstalls a specified module admin controller
     *
     * @param string $className - Controller class name without word 'Controller' at the end
     *
     * @return bool
     */
    protected function uninstallAdminController($className)
    {
        $id_tab = (int)Tab::getIdFromClassName($className);
        $tab = new Tab($id_tab);

        // Don't let uninstall other tabs
        if ($tab->module != $this->module->name) {
            return false;
        }

        return $tab->delete();
    }
}
