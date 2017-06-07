<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @package   Mobile Only
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2017 numero2 - Agentur für Internetdienstleistungen
 */


self::loadLanguageFile('mobile_only');


/**
 * Add palettes to tl_article
 */
$GLOBALS['TL_DCA']['tl_article']['subpalettes']['published'] = 'pc_only,mobile_only,'.$GLOBALS['TL_DCA']['tl_article']['subpalettes']['published'];


/**
 * Overwrite label callback
 */
$GLOBALS['TL_DCA']['tl_article']['list']['label']['label_callback_prev_mobile_only'] = $GLOBALS['TL_DCA']['tl_article']['list']['label']['label_callback'];
$GLOBALS['TL_DCA']['tl_article']['list']['label']['label_callback'] = array('tl_article_mobile_only', 'addIcon');


/**
 * Add fields to tl_article
 */
$GLOBALS['TL_DCA']['tl_article']['fields']['pc_only'] = array(
    'label'         => &$GLOBALS['TL_LANG']['mobile_only']['pc_only']
,   'inputType'     => 'checkbox'
,   'eval'          => array( 'mandatory' => false, 'tl_class'=>'w50' )
,   'sql'           => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_article']['fields']['mobile_only'] = array(
    'label'         => &$GLOBALS['TL_LANG']['mobile_only']['mobile_only']
,   'inputType'     => 'checkbox'
,   'eval'          => array( 'mandatory' => false, 'tl_class'=>'w50' )
,   'save_callback' => array(array('\numero2\MobileOnly\MobileOnly', "save_callback" ))
,   'sql'           => "char(1) NOT NULL default ''"
);


class tl_article_mobile_only extends tl_article {


    /**
    * Add an image to each article in the tree
    *
    * @param array         $row
    * @param string        $label
    *
    * @return string
    */
    public function addIcon($row, $label) {

        $defaultIcon = NULL;
        $defaultIcon = parent::addIcon($row, $label);

        if( !empty($GLOBALS['TL_DCA']['tl_article']['list']['label']['label_callback_prev_mobile_only']) ) {

            $prevCb = NULL;
            $prevCb = $GLOBALS['TL_DCA']['tl_article']['list']['label']['label_callback_prev_mobile_only'];

            if( $prevCb[0] != 'tl_article_mobile_only' && property_exists($prevCb[0], $prevCb[1]) ) {
                $defaultIcon = $prevCb[0]::$prevCb[1]($row, $label);
            }
        }

        if( $row['pc_only'] || $row['mobile_only'] ) {

            $visibility = $row['pc_only'] ? 'desktop' : 'mobile';
            $title = $row['pc_only'] ? $GLOBALS['TL_LANG']['mobile_only']['pc_only'][1] : $GLOBALS['TL_LANG']['mobile_only']['mobile_only'][1];

            $icon = NULL;
            $icon = preg_replace('/(<a.*?data-icon=.*?><\/a>)/', '${1}<span class="mobile-only-icon" data-visibility="'.$visibility.'" title="'.$title.'"></span>', $defaultIcon);

            return $icon;
        }

        return $defaultIcon;
    }
}