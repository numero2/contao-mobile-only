<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2020 Leo Feyer
 *
 * @package   Mobile Only
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2020 numero2 - Agentur für digitales Marketing GbR
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'numero2\MobileOnly',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Classes
    'numero2\MobileOnly\MobileOnly'                    => 'system/modules/mobile_only/classes/MobileOnly.php',

    // Modules
    'numero2\MobileOnly\ModuleMobileOnlyNavigation'     => 'system/modules/mobile_only/modules/ModuleMobileOnlyNavigation.php',
    'numero2\MobileOnly\ModuleMobileOnlyCustomnav'      => 'system/modules/mobile_only/modules/ModuleMobileOnlyCustomnav.php',

));
