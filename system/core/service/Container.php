<?php

/**
 * @package      Amalgam
 * @author       Anton Zencenco
 * @copyright    Copyright (c) 2016 - 2017
 * @license      https://opensource.org/licenses/MIT MIT License
 * @since        0.0.0
 * @filesource
 */

namespace Amalgam\Services;

/**
 * Class Container
 * Factory for Service Pools creation
 *
 * @package Amalgam\Services
 */
final class Container
{
    /**
     * Container constructor.
     * Locked constructor
     * Prevents class instantiation by new statement
     */
    private function __construct()
    {
    }

    /**
     * Factory method for ServicePool creation
     *
     * @param string $type - type of implemented pools
     *
     * @return null
     */
    public static function create(string $type)
    {
        $type = ucfirst($type) . "Pool";
        $class = __NAMESPACE__ . '\\' . $type;

        if (class_exists($class)) {
            return new $class();
        }

        return null;
    }
}