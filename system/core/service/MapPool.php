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
use Amalgam\Services\Contracts\SharedPool;

class MapPool extends Pool implements PoolContract, SharedPool
{
    /**
     * Pool of services
     *
     * @var array
     */
    protected $pool = [];

    /**
     * Pool of shared services
     *
     * @var array
     */
    protected $shared = [];

    /**
     * @inheritdoc
     */
    public function add($key, $service, $shared = false)
    {
        if (!$shared) {
            $this->pool[$key] = $service;
        } else {
            $this->shared[$key] = $service;
        }
    }

    /**
     * @inheritdoc
     */
    public function take($key)
    {
        if (!$this->has($key)) {
            return null;
        }

        if (!$this->isShared($key)) {
            return ($this->parseService($this->pool[$key]))->prototype();
        }

        if (!($this->shared[$key] instanceof SharedService)) {
            $this->shared[$key] = SharedService::create($this->parseService($this->shared[$key]));
        }

        return $this->shared[$key]::instance();
    }

    /**
     * @inheritdoc
     */
    public function unset($key)
    {
        if ($this->has($key)) {
            if (!$this->isShared($key)) {
                unset($this->pool[$key]);
            } else {
                $this->unset($this->shared[$key]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function has($key): bool
    {
        return isset($this->pool[$key]) || isset($this->shared[$key]);
    }

    /**
     * @inheritdoc
     */
    public function changeToShared($key)
    {
        if ($this->has($key) || !$this->isShared($key)) {
            $this->shared[$key] = $this->pool[$key];;
            unset($this->pool[$key]);
        }
    }

    /**
     * @inheritdoc
     */
    public function isShared($key): bool
    {
        return isset($this->shared[$key]);
    }
}