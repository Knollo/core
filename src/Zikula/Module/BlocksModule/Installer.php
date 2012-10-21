<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Zikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

namespace Zikula\Module\BlocksModule;

use UserUtil, ModUtil, SecurityUtil, LogUtil, DataUtil, System, ZLanguage, CategoryRegistryUtil, CategoryUtil;
use PageUtil, ThemeUtil, BlockUtil, EventUtil, Zikula_View;
use Zikula\Framework\Exception\FatalException;
use BlocksModule\Entity\BlockPlacement;

class Installer extends \Zikula\Framework\AbstractInstaller
{
    /**
     * initialise the blocks module
     *
     * @return       bool       true on success, false otherwise
     */
    public function install()
    {
        // create tables
        $classes = array(
            'BlocksModule\Entity\Block',
            'BlocksModule\Entity\BlockPosition',
            'BlocksModule\Entity\BlockPlacement',
            'BlocksModule\Entity\UserBlock'
        );

        try {
            \DoctrineHelper::createSchema($this->entityManager, $classes);
        } catch (\Exception $e) {
            return false;
        }

        // Set a default value for a module variable
        $this->setVar('collapseable', 0);

        // Initialisation successful
        return true;
    }

    /**
     * upgrade the module from an old version
     *
     * This function must consider all the released versions of the module!
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param        string   $oldVersion   version number string to upgrade from
     * @return       mixed    true on success, last valid version string or false if fails
     */
    public function upgrade($oldversion)
    {
        // Upgrade dependent on old version number
        switch ($oldversion) {
            case '3.8.0':
                // update empty filter fields to an empty array
                $entity = $this->name . '\Entity\Block';
                $dql = "UPDATE $entity p SET p.filter = 'a:0:{}' WHERE p.filter = '' OR p.filter = 's:0:\"\";'";
                $query = $this->entityManager->createQuery($dql);
                $query->getResult();

            case '3.8.1':
                // future upgrade routines
        }

        // Update successful
        return true;
    }

    /**
     * delete the blocks module
     *
     * Since the blocks module should never be deleted we'all always return false here
     * @return       bool       false
     */
    public function uninstall()
    {
        // Deletion not allowed
        return false;
    }

    /**
     * Add default block data for new installs
     * This is called after a complete pn installation since the blocks
     * need to be populated with module id's which are only available
     * once the install has been completed
     */
    public function defaultdata()
    {
        // load block api
        ModUtil::loadApi('Blocks', 'admin', true);

        // sanity check - truncate existing tables to ensure a clean blocks setup
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('blocks', true));
        $connection->executeUpdate($platform->getTruncateTableSQL('block_positions', true));
        $connection->executeUpdate($platform->getTruncateTableSQL('block_placements', true));

        // create the default block positions - left, right and center for the traditional 3 column layout
        $left = ModUtil::apiFunc('BlocksModule', 'admin', 'createposition', array('name' => 'left', 'description' => $this->__('Left blocks')));
        $right = ModUtil::apiFunc('BlocksModule', 'admin', 'createposition', array('name' => 'right', 'description' => $this->__('Right blocks')));
        $center = ModUtil::apiFunc('BlocksModule', 'admin', 'createposition', array('name' => 'center', 'description' => $this->__('Center blocks')));
        $search = ModUtil::apiFunc('BlocksModule', 'admin', 'createposition', array('name' => 'search', 'description' => $this->__('Search block')));
        $header = ModUtil::apiFunc('BlocksModule', 'admin', 'createposition', array('name' => 'header', 'description' => $this->__('Header block')));
        $footer = ModUtil::apiFunc('BlocksModule', 'admin', 'createposition', array('name' => 'footer', 'description' => $this->__('Footer block')));
        $topnav = ModUtil::apiFunc('BlocksModule', 'admin', 'createposition', array('name' => 'topnav', 'description' => $this->__('Top navigation block')));
        $bottomnav = ModUtil::apiFunc('BlocksModule', 'admin', 'createposition', array('name' => 'bottomnav', 'description' => $this->__('Bottom navigation block')));

        // define an array of the default blocks
        $blocks = array();

        // build the menu content
        $languages = ZLanguage::getInstalledLanguages();
        $saveLanguage = ZLanguage::getLanguageCode();
        $menucontent = array();
        $topnavcontent = array();
        foreach ($languages as $lang) {
            ZLanguage::setLocale($lang);
            ZLanguage::bindCoreDomain();

            $menucontent['displaymodules'] = '0';
            $menucontent['stylesheet'] = 'extmenu.css';
            $menucontent['template'] = 'Block/extmenu.tpl';
            $menucontent['blocktitles'][$lang] = $this->__('Main menu');

            // insert the links
            $menucontent['links'][$lang][] = array('name' => $this->__('Home'), 'url' => '{homepage}', 'title' => $this->__("Go to the home page"), 'level' => 0, 'parentid' => null, 'image' => '', 'active' => '1');
            $menucontent['links'][$lang][] = array('name' => $this->__('Administration'), 'url' => '{Admin:admin:adminpanel}', 'title' => $this->__('Go to the site administration'), 'level' => 0, 'parentid' => null, 'image' => '', 'active' => '1');
            $menucontent['links'][$lang][] = array('name' => $this->__('My Account'), 'url' => '{Users}', 'title' => $this->__('Go to your account panel'), 'level' => 0, 'parentid' => null, 'image' => '', 'active' => '1');
            $menucontent['links'][$lang][] = array('name' => $this->__('Log out'), 'url' => '{Users:user:logout}', 'title' => $this->__('Log out of this site'), 'level' => 0, 'parentid' => null, 'image' => '', 'active' => '1');
            $menucontent['links'][$lang][] = array('name' => $this->__('Site search'), 'url' => '{Search}', 'title' => $this->__('Search this site'), 'level' => 0, 'parentid' => null, 'image' => '', 'active' => '1');

            $topnavcontent['displaymodules'] = '0';
            $topnavcontent['stylesheet'] = 'extmenu.css';
            $topnavcontent['template'] = 'Block/extmenu_topnav.tpl';
            $topnavcontent['blocktitles'][$lang] = $this->__('Top navigation');

            // insert the links
            $topnavcontent['links'][$lang][] = array('name' => $this->__('Home'), 'url' => '{homepage}', 'title' => $this->__("Go to the site's home page"), 'level' => 0, 'parentid' => null, 'image' => '', 'active' => '1');
            $topnavcontent['links'][$lang][] = array('name' => $this->__('My Account'), 'url' => '{Users}', 'title' => $this->__('Go to your account panel'), 'level' => 0, 'parentid' => null, 'image' => '', 'active' => '1');
            $topnavcontent['links'][$lang][] = array('name' => $this->__('Site search'), 'url' => '{Search}', 'title' => $this->__('Search this site'), 'level' => 0, 'parentid' => null, 'image' => '', 'active' => '1');
        }

        ZLanguage::setLocale($saveLanguage);

        $menucontent = serialize($menucontent);
        $topnavcontent = serialize($topnavcontent);
        $searchcontent = array('displaySearchBtn' => 1,
                               'active' => array('Users' => 1));
        $searchcontent = serialize($searchcontent);

        $hellomessage = $this->__('<p><a href="http://zikula.org/">Zikula</a> is a content management system (CMS) and application framework. It is secure and stable, and is a good choice for sites with a large volume of traffic.</p><p>With Zikula:</p><ul><li>you can customise all aspects of the site\'s appearance through themes, with support for CSS style sheets, JavaScript, Flash and all other modern web development technologies;</li><li>you can mark content as being suitable for either a single language or for all languages, and can control all aspects of localisation and internationalisation of your site;</li><li>you can be sure that your pages will display properly in all browsers, thanks to Zikula\'s full compliance with W3C HTML standards;</li><li>you get a standard application-programming interface (API) that lets you easily augment your site\'s functionality through modules, blocks and other extensions;</li><li>you can get help and support from the Zikula community of webmasters and developers at <a href="http://www.zikula.org">zikula.org</a>.</li></ul><p>Enjoy using Zikula!</p><p><strong>The Zikula team</strong></p><p><em>Note: Zikula is Free Open Source Software (FOSS) licensed under the GNU General Public License.</em></p>');
        $blocks[] = array('bkey' => 'Extmenu', 'collapsable' => 1, 'defaultstate' => 1, 'language' => '', 'mid' => ModUtil::getIdFromName('Blocks'), 'title' => $this->__('Main menu'), 'description' => $this->__('Main menu'), 'content' => $menucontent, 'positions' => array($left));
        $blocks[] = array('bkey' => 'Search', 'collapsable' => 1, 'defaultstate' => 1, 'language' => '', 'mid' => ModUtil::getIdFromName('Search'), 'title' => $this->__('Search box'), 'description' => $this->__('Search block'), 'content' => $searchcontent, 'positions' => array($search));
        $blocks[] = array('bkey' => 'Html', 'collapsable' => 1, 'defaultstate' => 1, 'language' => '', 'mid' => ModUtil::getIdFromName('Blocks'), 'title' => $this->__("This site is powered by Zikula!"), 'description' => $this->__('HTML block'), 'content' => $hellomessage, 'positions' => array($center));
        $blocks[] = array('bkey' => 'Login', 'collapsable' => 1, 'defaultstate' => 1, 'language' => '', 'mid' => ModUtil::getIdFromName('Users'), 'title' => $this->__('User log-in'), 'description' => $this->__('Login block'), 'positions' => array($right));
        //$blocks[] = array('bkey' => 'Online', 'collapsable' => 1, 'defaultstate' => 1, 'language' => '', 'mid' => ModUtil::getIdFromName('Users'), 'title' => $this->__('Who\'s on-line'), 'description' => $this->__('Online block'), 'positions' => array($right));
        $blocks[] = array('bkey' => 'Extmenu', 'collapsable' => 1, 'defaultstate' => 1, 'language' => '', 'mid' => ModUtil::getIdFromName('Blocks'), 'title' => $this->__('Top navigation'), 'description' => $this->__('Theme navigation'), 'content' => $topnavcontent, 'positions' => array($topnav));

        // create each block and then update the block
        // the create creates the initial block record, the update sets the block placement
        foreach ($blocks as $position => $block) {
            ModUtil::apiFunc('BlocksModule', 'admin', 'create', $block);
        }

        return;
    }
}
