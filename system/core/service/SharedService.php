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

use Amalgam\Services\Contracts\Service;

/**
 * Class SharedService
 * Singleton decorator for services
 * It is used when service is marked as shared
 *
 * @package Amalgam\Services
 */
final class SharedService
{
    /**
     * Here will be stored singleton instance
     *
     * @var SharedService
     */
    private static $instance = null;

    /**
     * Here will be stored Service instance
     *
     * @var Service
     */
    private static $service = null;

    /**
     * SharedService constructor.
     * Locked constructor - one cannot create instance of this class
     */
    private function __construct()
    {
        //Private constructor to prevent instantiation
    }

    /**
     * Factory method for creation of a singleton decoration for Service instance
     *
     * @param \Amalgam\Services\Contracts\Service $service
     *
     * @return \Amalgam\Services\SharedService
     */
    public static function create(Service $service)
    {
        static::$instance = new self;
        static::$service = $service;

        return static::$instance;
    }

    /**
     * Get a singleton instance or null if is empty
     *
     * @return \Amalgam\Services\SharedService|null
     */
    public static function instance()
    {
        if (static::$instance === null) {
            return null;
        }

        return static::$instance;
    }

    /**
     * Magic call method - every method call from instance of this decorator(wrapper) will be redirected to $service
     * Exception is made for cloning and serialization/deserialization - they not allowed for singleton
     *
     * @param string $method
     * @param array  $arguments
     */
    public function __call($method, $arguments)
    {
        if (!in_array($method, ['__clone', '__wakeup', '__sleep', 'prototype'])) {
            static::$service->$method(...$arguments);
        }
    }

    /**
     * Magic static call method - every static method call from instance of this decorator(wrapper) will be redirected to $service
     * Exception is made for cloning and serialization/deserialization - they not allowed for singleton
     *
     * @param string $method
     * @param array  $arguments
     */
    public static function __callStatic($method, $arguments)
    {
        if (!in_array($method, ['__clone', '__wakeup', '__sleep'])) {
            $service = static::$service;
            $service::$method(...$arguments);
        }
    }

    /**
     * Magic method for accessing properties from stored service
     * Every access to singleton instance property will be redirected here to sored service
     *
     * @param $property
     *
     * @return null|mixed
     */
    public function __get($property)
    {
        if (isset(static::$service->$property)) {
            return static::$service->$property;
        }

        return null;
    }

    /**
     * Magic method for set properties from stored service it is redirected from singleton instance
     *
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        if (isset(static::$service->$property)) {
            static::$service->$property = $value;
        }
    }

    /**
     * Magic method that will be triggered on use of constructions isset and empty for singleton instance properties.
     * They will treated like check of service properties
     *
     * @param $property
     *
     * @return bool
     */
    public function __isset($property)
    {
        return isset(static::$service->$property);
    }

    /**
     * Magic method to unset properties of stored service
     * Every unset call to singleton instance property will unset service property
     *
     * @param $property
     */
    public function __unset($property)
    {
        unset(static::$service->$property);
    }

    /**
     * Magic method for preventing serialization
     */
    public function __sleep()
    {
        // Do nothing
    }

    /**
     * Magic method for preventing deserialization
     */
    public function __wakeup()
    {
        // Do nothing
    }
}