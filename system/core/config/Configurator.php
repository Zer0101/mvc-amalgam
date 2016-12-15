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

use Amalgam\Config\Contracts\Configurable;

/**
 * Class Configurator
 * This class is served as basic decorator implementation for Amalgam\Config
 * Simple say this is only simple wrapper that creates inside of class a protected value and set there
 * injected Amalgam\Config\Base class descendant
 * But one must remember: every decorator of Amalgam\Config MUST extend this class or use interface Configurable
 *
 * @package Amalgam\Config
 */
abstract class Configurator implements Configurable
{
    /**
     * @var Base
     */
    protected $config;

    public function __construct(Base $config)
    {
        $this->config = $config;
    }
}