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


/**
 * Front end module "navigation".
 */
class ModuleMobileOnlyNavigation extends \Module {


    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_navigation';


    /**
     * Do not display the module if there are no menu items
     *
     * @return string
     */
    public function generate() {

        if( TL_MODE == 'BE' ){
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### MOBILE ONLY NAVIGATION ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        $strBuffer = parent::generate();

        return ($this->Template->items != '') ? $strBuffer : '';
    }


    /**
     * Generate the module
     */
    protected function compile() {

        /** @var \PageModel $objPage */
        global $objPage;

        // Set the trail and level
        if( $this->defineRoot && $this->rootPage > 0 ){
            $trail = array($this->rootPage);
            $level = 0;
        } else {
            $trail = $objPage->trail;
            $level = ($this->levelOffset > 0) ? $this->levelOffset : 0;
        }

        $lang = null;
        $host = null;

        // Overwrite the domain and language if the reference page belongs to a differnt root page (see #3765)
        if ($this->defineRoot && $this->rootPage > 0) {

            $objRootPage = \PageModel::findWithDetails($this->rootPage);

            // Set the language
            if (\Config::get('addLanguageToUrl') && $objRootPage->rootLanguage != $objPage->rootLanguage) {
                $lang = $objRootPage->rootLanguage;
            }

            // Set the domain
            if ($objRootPage->rootId != $objPage->rootId && $objRootPage->domain != '' && $objRootPage->domain != $objPage->domain) {
                $host = $objRootPage->domain;
            }
        }

        $this->Template->request = ampersand(\Environment::get('indexFreeRequest'));
        $this->Template->skipId = 'skipNavigation' . $this->id;
        $this->Template->skipNavigation = specialchars($GLOBALS['TL_LANG']['MSC']['skipNavigation']);
        $this->Template->items = self::renderNavigation($trail[$level], 1, $host, $lang);
    }


    /**
     * Recursively compile the navigation menu and return it as HTML string
     *
     * @param integer $pid
     * @param integer $level
     * @param string  $host
     * @param string  $language
     *
     * @return string
     */
    protected function renderNavigation($pid, $level=1, $host=null, $language=null) {

        // Get all active subpages
        $objSubpages = \PageModel::findPublishedSubpagesWithoutGuestsByPid($pid, $this->showHidden, $this instanceof \ModuleSitemap);

        if( $objSubpages === null ) {
            return '';
        }

        $items = array();
        $groups = array();

        // Get all groups of the current front end user
        if( FE_USER_LOGGED_IN ) {
            $this->import('FrontendUser', 'User');
            $groups = $this->User->groups;
        }

        // Layout template fallback
        if( $this->navigationTpl == '' ) {
            $this->navigationTpl = 'nav_default';
        }

        /** @var \FrontendTemplate|object $objTemplate */
        $objTemplate = new \FrontendTemplate($this->navigationTpl);

        $objTemplate->pid = $pid;
        $objTemplate->type = get_class($this);
        $objTemplate->cssID = $this->cssID; // see #4897
        $objTemplate->level = 'level_' . $level++;

        /** @var \PageModel $objPage */
        global $objPage;
        $mobile = \Environment::get('agent')->mobile;

        // Browse subpages
        foreach( $objSubpages as $objSubpage ) {

            // Skip hidden sitemap pages
            if( $this instanceof \ModuleSitemap && $objSubpage->sitemap == 'map_never' ) {
                continue;
            }

            // skip pages based on pc and mobile only
            if( $mobile && $objSubpage->pc_only ) {
                continue;
            }
            if( (!$objSubpage->display_mobile_elements) && ((!$mobile) && $objSubpage->mobile_only) ){
                continue;
            }

            $subitems = '';
            $_groups = deserialize($objSubpage->groups);

            // Override the domain (see #3765)
            if( $host !== null ) {
                $objSubpage->domain = $host;
            }

            // Do not show protected pages unless a back end or front end user is logged in
            if( !$objSubpage->protected || BE_USER_LOGGED_IN || (is_array($_groups) && count(array_intersect($_groups, $groups))) || $this->showProtected || ($this instanceof \ModuleSitemap && $objSubpage->sitemap == 'map_always') ) {

                // Check whether there will be subpages
                if( $objSubpage->subpages > 0 && (!$this->showLevel || $this->showLevel >= $level || (!$this->hardLimit && ($objPage->id == $objSubpage->id || in_array($objPage->id, $this->Database->getChildRecords($objSubpage->id, 'tl_page'))))) ) {
                    $subitems = $this->renderNavigation($objSubpage->id, $level, $host, $language);
                }

                $href = null;

                // Get href
                switch( $objSubpage->type ) {

                    case 'redirect':
                        $href = $objSubpage->url;

                        if( strncasecmp($href, 'mailto:', 7) === 0 ) {
                            $href = \StringUtil::encodeEmail($href);
                        }
                        break;

                    case 'forward':
                        if( $objSubpage->jumpTo ) {
                            /** @var \PageModel $objNext */
                            $objNext = $objSubpage->getRelated('jumpTo');
                        } else {
                            $objNext = \PageModel::findFirstPublishedRegularByPid($objSubpage->id);
                        }

                        // Hide the link if the target page is invisible
                        if( $objNext === null || !$objNext->published || ($objNext->start != '' && $objNext->start > time()) || ($objNext->stop != '' && $objNext->stop < time()) ) {
                            continue(2);
                        }

                        $href = $objNext->getFrontendUrl();
                        break;

                    default:
                        $href = $objSubpage->getFrontendUrl();
                        break;
                }

                $row = $objSubpage->row();
                $trail = in_array($objSubpage->id, $objPage->trail);

                // Active page
                if( ($objPage->id == $objSubpage->id || $objSubpage->type == 'forward' && $objPage->id == $objSubpage->jumpTo) && !$this instanceof \ModuleSitemap && $href == \Environment::get('request') ) {
                    // Mark active forward pages (see #4822)
                    $strClass = (($objSubpage->type == 'forward' && $objPage->id == $objSubpage->jumpTo) ? 'forward' . ($trail ? ' trail' : '') : 'active') . (($subitems != '') ? ' submenu' : '') . ($objSubpage->protected ? ' protected' : '') . (($objSubpage->cssClass != '') ? ' ' . $objSubpage->cssClass : '');

                    $row['isActive'] = true;
                    $row['isTrail'] = false;
                }

                // Regular page
                else {

                    $strClass = (($subitems != '') ? 'submenu' : '') . ($objSubpage->protected ? ' protected' : '') . ($trail ? ' trail' : '') . (($objSubpage->cssClass != '') ? ' ' . $objSubpage->cssClass : '');

                    // Mark pages on the same level (see #2419)
                    if( $objSubpage->pid == $objPage->pid ) {
                        $strClass .= ' sibling';
                    }

                    $row['isActive'] = false;
                    $row['isTrail'] = $trail;
                }
                $strClass .= ($objSubpage->pc_only?' desktop-only':'') . ($objSubpage->mobile_only?' mobile-only':'');

                $row['subitems'] = $subitems;
                $row['class'] = trim($strClass);
                $row['title'] = specialchars($objSubpage->title, true);
                $row['pageTitle'] = specialchars($objSubpage->pageTitle, true);
                $row['link'] = $objSubpage->title;
                $row['href'] = $href;
                $row['nofollow'] = (strncmp($objSubpage->robots, 'noindex,nofollow', 16) === 0);
                $row['target'] = '';
                $row['description'] = str_replace(array("\n", "\r"), array(' ' , ''), $objSubpage->description);

                // Override the link target
                if( $objSubpage->type == 'redirect' && $objSubpage->target ) {
                    $row['target'] = ($objPage->outputFormat == 'xhtml') ? ' onclick="return !window.open(this.href)"' : ' target="_blank"';
                }

                $items[] = $row;
            }
        }

        // Add classes first and last
        if( !empty($items) ) {

            $last = count($items) - 1;

            $items[0]['class'] = trim($items[0]['class'] . ' first');
            $items[$last]['class'] = trim($items[$last]['class'] . ' last');
        }

        $objTemplate->items = $items;

        return !empty($items) ? $objTemplate->parse() : '';
    }
}