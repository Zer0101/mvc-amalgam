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

use Amalgam\Services\Contracts\Pool;
use Amalgam\Services\Contracts\Service;
use Amalgam\Services\Contracts\SharedPool;

/**
 * Class ServiceManager
 * Implements basic object pool
 * Must be used to resolve issue with dependency injection
 *
 * @package Amalgam\Services
 */
class ServiceManager implements \ArrayAccess
{
    /**
     * Current container where services are stored
     *
     * @var Pool|SharedPool
     */
    protected $container;

    /**
     * ServiceManager constructor.
     * Accept specific service container(pool) or use default - MapPool
     *
     * @param \Amalgam\Services\Contracts\Pool|null $container
     */
    public function __construct(Pool $container = null)
    {
        if (empty($container)) {
            $container = Container::create('map');
        }

        $this->container = $container;
    }

    /**
     * Register one service
     *
     * @param string|integer $key     - name or index of a service
     * @param mixed          $service - instance of Service contract or:
     *                                fully qualified class name (Namespace\\Class) that implements Service interface
     *                                callable that return Service instance
     *                                indexed array - first item is one of the types above, when others - parameters
     * @param bool           $shared  - on true make service shared - treat service as singleton,
     *                                else on every time when ServiceManager will return object it will be service clone
     *
     * @return \Amalgam\Services\ServiceManager - return self to implement chain call
     * @throws \Exception
     * @throws \TypeError
     */
    public function addService($key, $service, bool $shared = false) : ServiceManager
    {
        if (!is_int($key) && !is_string($key)) {
            throw new \TypeError('Invalid argument type: $key must be string or integer');
        }

        if ($shared && !($this->container instanceof SharedPool)) {
            throw new \Exception('Cannot treat service from this type of pool as shared');
        }

        $this->container->add($key, $service, $shared);

        return $this;
    }

    /**
     * Return service by its name
     * There is used lazy loading - when Service is registered it is stored without initialization
     * Only by calling this method one can trigger Service instantiation
     *
     * @param $key - service name
     *
     * @return null|Service
     */
    public function getService($key)
    {
        if ($this->container->has($key)) {
            return $this->container->take($key);
        }

        return null;
    }

    /**
     * Delete registered service
     *
     * @param $key - service name
     */
    public function deleteService($key)
    {
        $this->container->unset($key);
    }

    /**
     * Check if service is registered in ths Service Manager
     *
     * @param $key - service name
     *
     * @return bool
     */
    public function hasService($key)
    {
        return $this->container->has($key);
    }

    /**
     * Make service shared
     * Irreversible operation
     *
     * @param $key - service name
     *
     * @throws \Exception
     */
    public function markAsShared($key)
    {
        if (!($this->container instanceof SharedPool)) {
            throw new \Exception('Cannot treat service from this type of pool as shared');
        }

        $this->container->changeToShared($key);
    }

    /**
     * Part of ArrayAccess interface - check if offset exists
     *
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return $this->hasService($offset);
    }

    /**
     * Part of ArrayAccess interface - get offset
     *
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->getService($offset);
    }

    /**
     * Part of ArrayAccess interface - set offset
     *
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->addService($offset, $value);
    }

    /**
     * Part of ArrayAccess interface - unset offset
     *
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $this->deleteService($offset);
    }
}