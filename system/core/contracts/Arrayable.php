<?php

/**
 * @package      Amalgam
 * @author       Anton Zencenco
 * @copyright    Copyright (c) 2016 - 2017
 * @license      https://opensource.org/licenses/MIT MIT License
 * @since        0.0.0
 * @filesource
 */

namespace Amalgam\Contracts;

/**
 * Interface Arrayable
 * Contract for a class: return its content like an array
 *
 * @package Amalgam\Contracts
 */
interface Arrayable
{

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() :array;
}