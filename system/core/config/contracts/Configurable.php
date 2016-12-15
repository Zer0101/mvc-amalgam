<?php

/**
 * @package      Amalgam
 * @author       Anton Zencenco
 * @copyright    Copyright (c) 2016 - 2017
 * @license      https://opensource.org/licenses/MIT MIT License
 * @since        0.0.0
 * @filesource
 */

namespace Amalgam\Config\Contracts;

use Amalgam\Config\Base;

/**
 * Interface Configurable
 * Base contract for implement configuration decoration
 * Every class that wants to be decorator of class Config or another descendant of Amalgam\Config\Base must implement this contract
 *
 * @package Amalgam\Config
 */
interface Configurable
{
    /**
     * Configurable constructor.
     * Enforce to inject as parameter a descendant of class Amalgam\Config\Base
     *
     * @param \Amalgam\Config\Base $config
     */
    public function __construct(Base $config);
}