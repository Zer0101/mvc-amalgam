<?php

/**
 * @package      Amalgam
 * @author       Anton Zencenco
 * @copyright    Copyright (c) 2016 - 2017
 * @license      https://opensource.org/licenses/MIT MIT License
 * @since        0.0.0
 * @filesource
 */

namespace Amalgam\Services\Contracts;

interface Pool
{
    /**
     * Add service to pool
     *
     * @param      $key     - key of the service
     * @param      $service - service value
     * @param bool $shared  - share this service or not
     *
     * @return mixed
     */
    public function add($key, $service, $shared = false);

    /**
     * Take service from the pool
     *
     * @param $key - key of the service
     *
     * @return mixed
     */
    public function take($key);

    /**
     * Delete service from the pool
     *
     * @param $key - key of the service
     *
     * @return mixed
     */
    public function unset($key);

    /**
     * Check if service with such name is registered in this pool
     *
     * @param $key - key of the service
     *
     * @return bool
     */
    public function has($key): bool;
}