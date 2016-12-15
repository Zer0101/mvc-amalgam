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

use Amalgam\Services\Contracts\Pool as PoolContract;

class ArrayPool extends Pool implements \Countable, PoolContract, \ArrayAccess
{
    /**
     * Property - pool of services
     *
     * @var array
     */
    protected $pool = [];

    /**
     * Current offset
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * Size of the pool
     *
     * @var int
     */
    protected $size = 0;

    /**
     * @inheritdoc
     */
    public function add($key, $service, $shared = false)
    {
        $this->pool[] = $service;
        $this->size++;
    }

    /**
     * @inheritdoc
     */
    public function take($key)
    {
        return $this->has($key) ? ($this->parseService($this->pool[$key]))->prototype() : null;
    }

    /**
     * @inheritdoc
     */
    public function unset($key)
    {
        unset($this->pool[$key]);
        $this->size = $this->size > 0 ? $this->size-- : $this->size;
    }

    /**
     * @inheritdoc
     */
    public function has($key): bool
    {
        return isset($this->pool[$key]);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->size;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->take($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $this->unset($offset);
    }
}