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

namespace Zikula\Module\ErrorsModule\Controller;

use System, Zikula_Session, Zikula_View, SecurityUtil, PageUtil;
use Zikula\Core\Core;

class UserController extends \Zikula\Framework\Controller\AbstractController
{
    /**
     * Display an error
     * This function displays a generic error form
     * The template used is based on the error type passed
     *
     * @param string $args['type']    error type '404' or 'module'
     * @param string $args['message'] custom error message
     *
     * @return string HTML string
     */
    public function indexAction(array $args = array())
    {
        /* @var \Symfony\Component\HttpFoundation\Session\Session $session */
        $session = $this->request->getSession();
        $type      = $this->request->query->get('errtype', isset($args['type']) ? $args['type'] : 500);
        /* @var \Exception $exception */
        $exception = isset($args['exception']) ? $args['exception'] : null;
        $message   = isset($args['message']) ? $args['message'] : '';

        // load the stylesheet
        PageUtil::addVar('stylesheet', 'system/ErrorsModule/Resources/public/css/style.css');

        $this->view->setCaching(Zikula_View::CACHE_DISABLED);

        // assign the document info
        $this->view->assign('reportlevel', System::getVar('reportlevel'))
                   ->assign('currenturi', System::getCurrentUri())
                   ->assign('localreferer', System::localReferer())
                   ->assign('sitename', System::getVar('sitename'))
                   ->assign('reportlevel', System::getVar('reportlevel'))
                   ->assign('funtext', System::getVar('funtext'));

        $messages = $session->getFlashBag()->get(Zikula_Session::MESSAGE_ERROR);
        // show the detailed error message for admins only
        if (System::isDevelopmentMode() || SecurityUtil::checkPermission('::', '::', ACCESS_ADMIN)) {
            $message ? $messages[] = $message : null;
        }

        $trace = array();
        if (System::isDevelopmentMode() && $exception instanceof \Exception) {
            $line = $exception->getLine();
            $file = $exception->getFile();
            $trace = array(0 => '#0 '.$this->__f('Exception thrown in %1$s, line %2$s.', array($file, $line)));
            $trace += explode("\n", $exception->getTraceAsString());
        }

        // assign the list of registered errors
        // and the trace (if development mode is enabled)
        $this->view->assign('messages', $messages)
                   ->assign('trace', $trace);

        // return the template output
        if ($this->view->template_exists($template = "User/{$type}.tpl")) {
            return $this->response($this->view->fetch($template), $type);
        } else {
            return $this->response($this->view->fetch('User/main.tpl'), $type);
        }
    }

    /**
     * Display a system error
     */
    public function systemAction(array $args = array())
    {
        return $this->response($this->view->setCaching(Zikula_View::CACHE_DISABLED)
                          ->assign($args)
                          ->fetch('User/system.tpl'), 500);
    }
}
