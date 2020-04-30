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
 * Add palettes to tl_page
 */
if( !empty($GLOBALS['TL_DCA']['tl_page']['subpalettes']['published']) ) {

    $GLOBALS['TL_DCA']['tl_page']['subpalettes']['published'] = 'pc_only,mobile_only,'.$GLOBALS['TL_DCA']['tl_page']['subpalettes']['published'];

} else {

    foreach( array('regular','forward','redirect') as $key => $value ) {
        $GLOBALS['TL_DCA']['tl_page']['palettes'][$value] = str_replace(
            ",published,",
            ",published,pc_only,mobile_only,",
            $GLOBALS['TL_DCA']['tl_page']['palettes'][$value]
        );
    }
}

foreach( array('regular','forward','redirect') as $key => $value ) {
    $GLOBALS['TL_DCA']['tl_page']['palettes'][$value] = str_replace(
        ",includeLayout",
        ",display_mobile_elements,includeLayout",
        $GLOBALS['TL_DCA']['tl_page']['palettes'][$value]
    );
}


/**
 * Overwrite label callback
 */
$GLOBALS['TL_DCA']['tl_page']['list']['label']['label_callback'] = array('tl_page_mobile_only', 'addMobileIcon');


/**
 * Add fields to tl_page
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['pc_only'] = array(
    'label'      => &$GLOBALS['TL_LANG']['mobile_only']['pc_only']
,   'inputType'  => 'checkbox'
,   'eval'       => array( 'mandatory' => false, 'tl_class'=>'w50' )
,   'sql'        => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['mobile_only'] = array(
    'label'      => &$GLOBALS['TL_LANG']['mobile_only']['mobile_only']
,   'inputType'  => 'checkbox'
,   'eval'       => array( 'mandatory' => false, 'tl_class'=>'w50' )
,   'save_callback' => array(array('\numero2\MobileOnly\MobileOnly', "save_callback" ))
,   'sql'        => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['display_mobile_elements'] = array(
    'label'      => &$GLOBALS['TL_LANG']['mobile_only']['display_mobile_elements']
,   'inputType'  => 'checkbox'
,   'eval'       => array( 'mandatory' => false )
,   'sql'        => "char(1) NOT NULL default ''"
);


class tl_page_mobile_only extends tl_page {


    /**
    * Add an image to each page in the tree
    *
    * @param array         $row
    * @param string        $label
    * @param DataContainer $dc
    * @param string        $imageAttribute
    * @param boolean       $blnReturnImage
    * @param boolean       $blnProtected
    *
    * @return string
    */
    public function addMobileIcon($row, $label, DataContainer $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false) {

        $defaultIcon = NULL;
        $defaultIcon = parent::addIcon($row, $label, $dc, $imageAttribute, $blnReturnImage, $blnProtected);

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
