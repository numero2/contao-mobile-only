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
  * BACK END MODULES
  */
 $GLOBALS['BE_MOD']['design']['page']['stylesheet'] = 'system/modules/mobile_only/assets/backend.css';
 $GLOBALS['BE_MOD']['content']['article']['stylesheet'] = 'system/modules/mobile_only/assets/backend.css';


/**
 * FRONT END MODULES
 */
$GLOBALS['FE_MOD']['navigationMenu']['navigation'] = 'numero2\MobileOnly\ModuleMobileOnlyNavigation';
$GLOBALS['FE_MOD']['navigationMenu']['customnav'] = 'numero2\MobileOnly\ModuleMobileOnlyCustomnav';

/**
 * REGISTER HOOKS
 */
$GLOBALS['TL_HOOKS']['isVisibleElement'][] = array('numero2\MobileOnly\MobileOnly', 'isVisibleHook');
