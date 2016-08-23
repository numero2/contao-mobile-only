<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   mobile_only
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   Commercial
 * @copyright 2016 numero2 - Agentur für Internetdienstleistungen
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

        if( TL_MODE == "BE" ) {
            return $blnIsVisible;
        }

        if( $objElement instanceof \ArticleModel || $objElement instanceof \ContentModel ){

            $mobile = \Environment::get('agent')->mobile;

            // skip pages based on pc and mobile only
            if( $mobile && $objElement->pc_only ){
                return false;
            }
            if( (!$mobile) && $objElement->mobile_only ){
                return false;
            }

            return true;
        }

        // Otherwise we don't want to change the visibility state
        return $blnIsVisible;
    }
}
