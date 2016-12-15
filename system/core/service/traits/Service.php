<?php

/**
 * @package      Amalgam
 * @author       Anton Zencenco
 * @copyright    Copyright (c) 2016 - 2017
 * @license      https://opensource.org/licenses/MIT MIT License
 * @since        0.0.0
 * @filesource
 */

namespace Amalgam\Services\Traits;

use Amalgam\Services\Contracts\Service as ServiceContract;

/**
 * Class Service
 * Simple implementation of Service interface
 * One need only implement Srvice interface and use this trait - and class is fully qualified service
 *
 * @package Amalgam\Services\Traits
 */
trait Service
{

    /**
     * This method is called to make a clone of object that use this trait
     * Method is forced by Service contract
     *
     * @return \Amalgam\Services\Contracts\Service
     */
    public function prototype() :ServiceContract
    {
        return clone $this;
    }

    /**
     * Clone magic method that is forced by Service contract
     * This is basic empty implementation
     * If one want to make deep copy he must override this method in class that implements Service contract
     */
    public function __clone()
    {
        // This MUST be overwritten in class if one wants to make a deep clone
    }
}