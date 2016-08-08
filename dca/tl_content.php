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
 * Add palettes to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['published'] = 'pc_only,mobile_only,'.$GLOBALS['TL_DCA']['tl_content']['subpalettes']['published'];


self::loadLanguageFile('mobile_only');

foreach ($GLOBALS['TL_DCA']['tl_content']['palettes'] as $key => $value) {
    if( $key == "__selector__" )
        continue;
    if( strpos($value, "invisible,start") !== false ) {
        $GLOBALS['TL_DCA']['tl_content']['palettes'][$key] = str_replace("invisible,start", "invisible,pc_only,mobile_only,start", $value);
    }
}


/**
 * Add fields to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['pc_only'] = array(
    'label'      => &$GLOBALS['TL_LANG']['mobile_only']['pc_only']
,   'inputType'  => 'checkbox'
,   'eval'       => array( 'mandatory' => false, 'tl_class'=>'w50' )
,   'sql'       => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['mobile_only'] = array(
    'label'      => &$GLOBALS['TL_LANG']['mobile_only']['mobile_only']
,   'inputType'  => 'checkbox'
,   'eval'       => array( 'mandatory' => false, 'tl_class'=>'w50' )
,   'save_callback' => array(array("mobile_only", "save_callback" ))
,   'sql'       => "char(1) NOT NULL default ''"
);
