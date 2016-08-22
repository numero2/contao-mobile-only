<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   MobileOnly
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   Commercial
 * @copyright 2016 numero2 - Agentur für Internetdienstleistungen
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

));
