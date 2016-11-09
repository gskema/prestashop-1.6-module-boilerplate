<?php

namespace MyModule\Core;

use \Translate;

/**
 * Class Translatable
 * @package MyModule\Core
 */
trait Translatable
{
    /**
     * Translates a text string.
     * Text string must be used within this file as a call argument for this function
     * and must be typed out explicitly, e.g. $this->l('Item has been saved successfully.');
     *
     * @param string $textString
     *
     * @return string
     */
    public function l($textString)
    {
        return Translate::getModuleTranslation('mymodule', $textString, basename(__FILE__, '.php'));
    }
}
