<?php

/**
 * @package      Amalgam
 * @author       Anton Zencenco
 * @copyright    Copyright (c) 2016 - 2017
 * @license      https://opensource.org/licenses/MIT MIT License
 * @since        0.0.0
 * @filesource
 */

namespace Amalgam\Traits;

/**
 * Class ArrayMorphing
 * Trait used for adding possibility to a class to transform stdObject into array
 *
 * @package Amalgam\System\Traits
 */
trait ArrayMorphing
{
    /**
     * That method gets object or something else and trying to transform it into array
     * It is working recursive
     *
     * @param object|mixed $object
     *
     * @return array|mixed
     */
    protected function morphToArray($object)
    {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }

        return array_map([$this, 'morphToArray'], (array)$object);
    }
}