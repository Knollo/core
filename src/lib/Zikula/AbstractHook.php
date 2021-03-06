<?php
/**
 * Copyright 2010 Zikula Foundation
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

use Zikula\Component\HookDispatcher\Hook;

/**
 * AbstractHook class.
 */
class Zikula_AbstractHook extends Hook
{
    /**
     * Subscriber object id.
     *
     * @var integer
     */
    protected $id;

    /**
     * Subscriber area id.
     *
     * @var integer
     */
    protected $areaId;

    /**
     * Caller.
     *
     * @var string
     */
    protected $caller;

    /**
     * Get caller.
     *
     * @return string
     */
    public function getCaller()
    {
        return $this->caller;
    }

    /**
     * Set caller.
     *
     * @param string $caller Caller name.
     *
     * @return Zikula_AbstractHook
     */
    public function setCaller($caller)
    {
        $this->caller = $caller;

        return $this;
    }

    /**
     * Get subscriber object id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get subscriber area id.
     *
     * @return integer
     */
    public function getAreaId()
    {
        return $this->areaId;
    }

    /**
     * Set subscriber area id.
     *
     * @param string $areaId ID of the area.
     *
     * @return Zikula_DisplayHook
     */
    public function setAreaId($areaId)
    {
        $this->areaId = $areaId;

        return $this;
    }

    /**
     * Stop futher notification.
     *
     * @deprecated
     * @see Zikula_AbstractHook::stopPropagation()
     *
     * @return Zikula_AbstractHook
     */
    public function stop()
    {
        $this->stopPropagation();

        return $this;
    }

    /**
     * Has event stopped.
     *
     * @deprecated
     * @see Zikula_AbstractHook::isPropagationStopped()
     *
     * @return boolean
     */
    public function isStopped()
    {
        return $this->isPropagationStopped();
    }

    /**
     * Sets the EventManager property.
     *
     * @param EventDispatcherInterface $eventManager
     *
     * @deprecated
     * @see Zikula_AbstractHook::setDispatcher()
     *
     * @return void
     */
    public function setEventManager(EventDispatcherInterface $eventManager)
    {
        $this->setDispatcher($eventManager);
    }

    /**
     * Gets the EventManager.
     *
     * @deprecated
     * @see Zikula_AbstractHook::getDispatcher()
     *
     * @return EventDispatcherInterface
     */
    public function getEventManager()
    {
        return $this->getDispatcher();
    }
}
