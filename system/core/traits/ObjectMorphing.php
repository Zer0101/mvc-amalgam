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
 * Class ObjectMorphing
 * Trait used for adding possibility to a class to transform array into stdObject
 *
 * @package Amalgam\System\Traits
 */
trait ObjectMorphing
{
    /**
     * That method gets array or something else and trying to transform it into stdObject
     * It is working recursive
     *
     * @param array|mixed $array
     *
     * @return \stdClass|mixed
     */
    protected function morphToObject($array)
    {
        if (!is_array($array)) {
            return $array;
        }

        $object = new \stdClass();
        foreach ($array as $name => $value) {
            $name = strtolower(trim($name));
            if (!empty($name)) {
                $object->$name = $this->morphToObject($value);
            }
        }

        return $object;
    }
}