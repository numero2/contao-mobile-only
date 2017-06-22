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


/**
 * Namespace
 */
namespace numero2\MobileOnly;


class MobileOnly extends \System {


    /**
     * Checks that not both new settings are set.
     *
     * @param DataContainer $dc
     */
    public function save_callback( $value, \DataContainer $dc ) {

        self::loadLanguageFile('mobile_only');

        if( $dc->activeRecord->pc_only && $value ){
            throw new \Exception($GLOBALS['TL_LANG']['mobile_only']['pc_mobile_only']['err']);
        }

        return $value;
    }


    /**
     * Determins if an element is visible based on pc or mobile only flags
     *
     * @param $objElement
     * @param $blnIsVisible
     *
     * @return $blnIsVisible
     */
    public function isVisibleHook( $objElement, $blnIsVisible ){

        global $objPage;

        if( TL_MODE === "BE" ) {
            return $blnIsVisible;
        }

        if( isset($objElement->pc_only) || isset($objElement->mobile_only) ){

            $isMobileDevice = false;
            $isMobileDevice = \Environment::get('agent')->mobile;

            $showOnMobile = false;

            if( TL_MODE === "FE" && !empty($objPage->display_mobile_elements) ){
                $showOnMobile = true;
            }

            // skip pages based on pc and mobile only
            if( $isMobileDevice && $objElement->pc_only ){
                return false;
            }
            if( !$showOnMobile && (!$isMobileDevice && $objElement->mobile_only) ){
                return false;
            }

            // add css classes to elements
            $classes = $objElement->classes;
            if( $objElement->pc_only ) { $classes[] = 'desktop-only'; }
            if( $objElement->mobile_only ) { $classes[] = 'mobile-only'; }
            $objElement->classes = $classes;

            return true;
        }

        // otherwise we don't want to change the visibility state
        return $blnIsVisible;
    }
}
