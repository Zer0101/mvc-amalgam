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
 * Class Pool
 * Ancestor of every Service Pool
 * Enforce the to use or override parseService method
 *
 * @package Amalgam\Services
 */
abstract class Pool
{
    /**
     * Resolve service - in any of this ways:
     *      - service instance - return
     *      - class name - instantiate it
     *      - callable - call it
     *
     * @param $service - mixed service value
     *
     * @return \Amalgam\Services\Contracts\Service
     * @throws \TypeError
     */
    protected function parseService($service) :Service
    {
        if ($service instanceof Service) {
            return $service;
        }

        $arguments = [];
        if (is_array($service) && !is_callable($service)) {
            $arguments = array_slice($service, 1);
            $service = array_shift($service);
        }

        if (is_string($service)) {
            if (class_exists($service)) {
                $service = new $service(...$arguments);
            }
        } else {
            if (is_callable($service)) {
                $service = $service->call($this, ...$arguments);
            }
        }

        if (!($service instanceof Service)) {
            throw new \TypeError('Invalid type: $service must be instance of Service');
        }

        return $service;
    }
}