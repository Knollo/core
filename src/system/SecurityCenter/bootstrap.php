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

if (!defined('HTMLPURIFIER_PREFIX')) {
    define('HTMLPURIFIER_PREFIX', realpath(dirname(__FILE__) . '/vendor/htmlpurifier'));
}

if (!defined('PHPIDS_PATH_PREFIX')) {
    define('PHPIDS_PATH_PREFIX', realpath(dirname(__FILE__) . '/vendor/IDS'));
}

// include the PHPIDS and get access to the result object
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) .'/vendor'));
ZLoader::addAutoloader('HTMLPurifier', realpath(dirname(__FILE__) . '/vendor/htmlpurifier'));
ZLoader::addAutoloader('IDS', realpath(dirname(__FILE__) . '/vendor'));
ZLoader::addAutoloader('SecurityCenter', __DIR__);
include_once __DIR__.'/vendor/htmlpurifier/HTMLPurifier.php';

// register event handlers
//EventUtil::attachCustomHandlers('system/SecurityCenter/lib/SecurityCenter/EventHandler');
EventUtil::attachEventHandler('SecurityCenter\Listener\FilterListener');
