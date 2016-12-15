<?php

/**
 * @package      Amalgam
 * @author       Anton Zencenco
 * @copyright    Copyright (c) 2016 - 2017
 * @license      https://opensource.org/licenses/MIT MIT License
 * @since        0.0.0
 * @filesource
 */

namespace Amalgam\Config;

/**
 * Class Amalgam\Config\Base
 * A basic configuration class
 * This class is served as ancestor to any configuration class
 * It's enforce every one of its descendants to use store configuration in container and access them
 * via magic methods __get and __set
 *
 * @package Amalgam\Config
 */
abstract class Base
{
    /**
     * A container for configurations
     *
     * @var \stdClass
     */
    protected $container;

    /**
     * Amalgam\Config\Base class constructor.
     *
     * @param array $config - array with configurations
     */
    abstract public function __construct(array $config = []);

    /**
     * Magical method to get something from class
     * In context of the configuration class it is allows access only to container with configurations
     *
     * @param mixed $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        if (!empty($this->container->$name)) {
            return $this->container->$name;
        }

        return null;
    }

    /**
     * Magical method to set something to class
     * In context of the configuration class it is allows access only to container with configurations
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function __set(string $name, $value)
    {
        //Transform values to object using trait
        $this->container->$name = $value;
    }

    /**
     * Return configurations in form of array
     * This method is very important because sometimes we need to take configurations as array
     *
     * @param string|null $index
     *
     * @return array
     */
    abstract public function asArray($index = null) :array;
}