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


 self::loadLanguageFile('mobile_only');


/**
 * Add palettes to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['published'] = 'pc_only,mobile_only,'.$GLOBALS['TL_DCA']['tl_content']['subpalettes']['published'];

foreach ($GLOBALS['TL_DCA']['tl_content']['palettes'] as $key => $value) {
    if( $key == "__selector__" )
        continue;
    if( strpos($value, "invisible,start") !== false ) {
        $GLOBALS['TL_DCA']['tl_content']['palettes'][$key] = str_replace("invisible,start", "invisible,pc_only,mobile_only,start", $value);
    }
}


/**
 * Overwrite child record callback
 */
$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] = array('tl_content_mobile_only', 'addCteType');


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
,   'save_callback' => array(array('\numero2\MobileOnly\MobileOnly', "save_callback" ))
,   'sql'       => "char(1) NOT NULL default ''"
);


class tl_content_mobile_only extends tl_content {


    /**
     * Add the type of content element
     *
     * @param array $arrRow
     *
     * @return string
     */
    public function addCteType($arrRow) {

        $defaultRow = NULL;
        $defaultRow = parent::addCteType($arrRow);

        if( $arrRow['pc_only'] || $arrRow['mobile_only'] ) {

            $title = $arrRow['pc_only'] ? $GLOBALS['TL_LANG']['mobile_only']['pc_only'][0] : $GLOBALS['TL_LANG']['mobile_only']['mobile_only'][0];

            $row = NULL;
            $row = preg_replace('/<div(.*?cte_type.*?)>(.*?)<\/div>/', '<div ${1}>${2} <span class="mobile-only">['.$title.']</span></div>', $defaultRow);

            return $row;
        }

        return $defaultRow;
    }
}