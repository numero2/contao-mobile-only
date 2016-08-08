<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   Rheinhessische
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   Commercial
 * @copyright 2016 numero2 - Agentur für Internetdienstleistungen
 */


/**
 * FRONT END MODULES
 */
$GLOBALS['FE_MOD']['navigationMenu']['navigation'] = 'ModuleMobileOnlyNavigation';

/**
 * REGISTER HOOKS
 */
$GLOBALS['TL_HOOKS']['isVisibleElement'][] = array('numero2\mobile_only\mobile_only', 'isVisibleHook');
