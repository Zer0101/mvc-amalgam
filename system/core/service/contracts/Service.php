<?php

/**
 * @package      Amalgam
 * @author       Anton Zencenco
 * @copyright    Copyright (c) 2016 - 2017
 * @license      https://opensource.org/licenses/MIT MIT License
 * @since        0.0.0
 * @filesource
 */

namespace Amalgam\Services\Contracts;

/**
 * Interface Service
 * Anyone who implemented this interface can be used by ServiceManager
 *
 * @package Amalgam\Services\Contracts
 */
interface Service
{
    /**
     * Return a clone of an object (by default)
     *
     * @return \Amalgam\Services\Contracts\Service
     */
    public function prototype() :Service;

    /**
     * Override this magic method to use not shallow clone but deep clone
     *
     * @return mixed
     */
    public function __clone();
}