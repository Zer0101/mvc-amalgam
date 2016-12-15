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

use Amalgam\Traits\{
    ArrayMorphing, ObjectMorphing
};

/**
 * Class Config
 * Default core configuration class
 *
 * @package Amalgam\Config
 */
class Config extends Base
{
    /**
     * Connecting ArrayMorphing trait
     */
    use ArrayMorphing;
    /**
     * Connecting ObjectMorphing trait
     */
    use ObjectMorphing;

    /**
     * Base class constructor.
     *
     * @param array $config - array with configurations
     */
    public function __construct(array $config = [])
    {
        $this->container = (object)[];
        if (!empty($config)) {
            //Transform configs to object
            $this->container = $this->morphToObject($config);
        }
    }

    /**
     * @inheritdoc
     */
    public function __set(string $name, $value)
    {
        //Transform values to object using trait
        $this->container->$name = $this->morphToObject($value);
    }

    /**
     * @inheritdoc
     */
    public function asArray($index = null) :array
    {
        if (!empty($this->container->$index)) {
            //Transform configs to array back
            //But only if we hav no index
            return $this->morphToArray($this->container->$index);
        }

        //Fetch and transform everithing
        return $this->morphToArray($this->container) ?? [];
    }
}