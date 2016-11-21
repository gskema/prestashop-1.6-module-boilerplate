# How to use PHP namespaces in PrestaShop modules

Require (once) `autoload.php` file at the top of main module file `mymodule.php`:

```php
<?php

require_once(__DIR__.'/autoload.php');

class MyModule extends Module {
...
```

Make sure that you use `__DIR__` constant, because sometimes without it file path is not resolved.

If your module has any controller classes, `autoload.php` file will need to be included there as well:

```php
<?php

require_once(__DIR__.'/../../autoload.php');

class MyModuleActionOneModuleFrontController extends ModuleFrontController {
...
```

This is **needed** when you want to put `use ...;` statements at the top of the class file or
when you are extending your own custom controller class:

```php
<?php

require_once(__DIR__.'/../../autoload.php');

class MyModuleActionOneModuleFrontController extends MyModuleModuleFrontController {
  ...
}
```

Modules controller files get included by PrestaShop before the actual module files,
that is why the must include the autoloader.

You cannot put `use ...;` statements at your main module file `mymodule.php`.
This is because `mymodule.php` file gets evaluated during installation using `eval(...)`
to check syntax errors, but `use ...;` statements cannot be used inside `eval()`.

**How to resolve this**?

  - Do not use `use ...;` statements in your `mymodule.php` file. Use fully qualified class names,
    e.g. `new \MyModule\Moduler\Installer()` or `new \MyModule\Moduler\Tools::minifyHTML()`.
  - **Or** make an override for `ModuleCore` class and eliminate `eval()` checking

Also, you **cannot** put your main module class under a namespace. This is because
PrestaShop expects module classes to be plain classes without a namespace.

## Object Models

`ObjectModel` classes cannot be used with namespaces either. This is because
the class names are used to build hook names:

```php
Hook::exec('actionObject'.get_class($this).'AddBefore', ['object' => $this]);
```

**How to resolve this**?

  - Use *snake_case* class names, e.g. `MyModule_Model1`
  - Define **PSR-0** autoloder for `models/` folder and **PSR-4** autoloader for `classes/` folder

You can still rename you classes using `use ...;` statement.

## Composer

Instead of inventing your own autloder, you should use Composer's autoloader.
To start, make a `composer.json` file in your module folder:

```json
{
  "autoload": {
    "psr-4": {
      "MyModule\\": "classes/"
    },
    "psr-0": {
      "MyModule_": "models/"
    }
  }
}
```

Then, run these command in your console:

```bash
cd modules/mymodule
composer dump-autoload -o
```

You should see a newly created folder `mymodule/vendor`.
This command generates and **optimized** autoloader, which you can now include
in your module file(s):

```php
<?php

require_once(__DIR__.'/vendor/autoload.php');

class MyModule extends Module {
  ...
}
```

Using composer is very convenient because it can generate a single autoloader
for both your classes and vendor packages.

## Using namespaced classes inside main module file

Because you can't put `use ...;` statement inside module classes, you may think that
this is all worthless, because you will have to spell out these long class names inside
the module file.

While this is true, you should strive to keep your main module file as short as possible
and delegate most of the logic to other classes and controllers, where you can use `use ...;`
statements.

We believe that using namespaces is worth it, even with these drawbacks.
