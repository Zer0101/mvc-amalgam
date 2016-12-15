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

interface SharedPool
{
    /**
     * Changes service to share state
     *
     * @param $key - name of the service
     *
     * @return mixed
     */
    public function changeToShared($key);

    /**
     * Check if registered service is shared
     *
     * @param $key - name of the service
     *
     * @return bool
     */
    public function isShared($key): bool;
}