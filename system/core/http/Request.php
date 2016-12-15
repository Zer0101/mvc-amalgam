<?php

/**
 * @package      Amalgam
 * @author       Anton Zencenco
 * @copyright    Copyright (c) 2016 - 2017
 * @license      https://opensource.org/licenses/MIT MIT License
 * @since        0.0.0
 * @filesource
 */

namespace Amalgam\Http;

use Amalgam\Config\Base;
use Amalgam\Services\Contracts\Service;

/**
 * Class Request
 * Used for processing incoming request
 * It can be used like a service
 *
 * @package Amalgam\Http
 */
class Request implements Service
{
    use \Amalgam\Services\Traits\Service;

    /**
     * Stored configurations
     *
     * @var Base
     */
    protected $configs;

    /**
     * User IP
     *
     * @var string
     */
    protected $ip = '0.0.0.0';

    /**
     * List of trusted proxy
     *
     * @var array
     */
    protected $trust_proxy = [];

    /**
     * User agent string
     *
     * @var string
     */
    protected $user_agent = '';

    /**
     * Request uri string
     *
     * @var mixed|null|string
     */
    protected $uri = null;

    /**
     * Request referrer
     *
     * @var null
     */
    protected $referrer = null;

    /**
     * Protocol used for request
     *
     * @var mixed|null|string
     */
    protected $protocol = null;

    /**
     * Show if request contains header X-REQUESTED-WITH
     *
     * @var null
     */
    protected $request_with = null;

    /**
     * Request method
     *
     * @var null|string
     */
    protected $request_method = null;

    /**
     * Request body
     *
     * @var null|string
     */
    protected $request_body = null;

    /**
     * Request headers
     *
     * @var array|false|null
     */
    protected $request_headers = null;

    /**
     * Indicates if request came by secure connection
     *
     * @var bool
     */
    protected $secure_connection = false;

    /**
     * Request constructor.
     * Set main class parameters mostly from $_SERVER and $config
     *
     * @param \Amalgam\Config\Base $config - basic configurations for this class
     */
    public function __construct(Base $config)
    {
        $this->configs = $config;
        $this->trust_proxy = $config->thrust_hosts ?? [];
        $this->protocol = $config->protocol ?? 'http';
        $this->uri = $this->detect_uri();
        $this->ip = $this->forward_to();
        $this->secure_connection = $this->is_secure_connection();
        $this->request_method = !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $this->request_body = $this->request_method !== 'GET' ? stream_get_contents(STDIN) : $_SERVER['QUERY_STRING'];
        $this->request_headers = getallheaders();
        $this->referrer = $_SERVER['HTTP_REFERER'] ?? null;
        $this->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $this->request_with = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? null;
    }

    /**
     * Check if connection is secure (via SSL or TSL)
     *
     * @return bool
     */
    public function is_secure_connection()
    {
        if (!empty($_SERVER['HTTPS']) && filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) {
            return true;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        }
        if (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && filter_var($_SERVER['HTTP_FRONT_END_HTTPS'],
                FILTER_VALIDATE_BOOLEAN)
        ) {
            return true;
        }
        if (in_array($_SERVER['REMOTE_ADDR'], $this->trust_proxy)) {
            return true;
        }

        return false;
    }

    /**
     * Check if request is forwarded
     *
     * @return mixed|string
     */
    private function forward_to()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $this->trust_proxy)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = array_shift(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = array_shift(explode(',', $_SERVER['HTTP_CLIENT_IP']));
            }
        }

        return $ip;
    }

    /**
     * Strip raw URI string from $_SERVER
     *
     * @return mixed|null|string
     */
    private function strip_uri()
    {
        $uri = $_SERVER['PATH_INFO'] ?? null;

        if (empty($uri) && isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
            $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (!empty($request_uri)) {
                // Valid URL path found, set it.
                $uri = $request_uri;
            }

            $uri = rawurldecode($uri);
        }

        if (empty($uri) && isset($_SERVER['PHP_SELF'])) {
            $uri = $_SERVER['PHP_SELF'];
        }

        if (empty($uri) && isset($_SERVER['REDIRECT_URL'])) {
            $uri = $_SERVER['REDIRECT_URL'];
        }

        return $uri;
    }

    /**
     * Trying to detect URI string
     *
     * @return mixed|null|string
     */
    private function detect_uri()
    {
        $uri = $this->strip_uri();
        $index = $this->configs->index_file ?? '';
        $base_url = parse_url($this->configs->base_url, PHP_URL_PATH);
        $uri = strpos($uri, $base_url) === 0 ? (string)substr($uri, strlen($base_url)) : $uri;
        if (!empty($index)) {
            $uri = strpos($uri, $index) === 0 ? (string)substr($uri, strlen($index)) : $uri;
        }

        return $uri;
    }

    /**
     * Get cleaned URI string
     *
     * @return mixed|null|string
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * Get query string (if it is set)
     *
     * @return null|string
     */
    public function query()
    {
        if ($this->request_method !== 'GET') {
            return null;
        }

        return $this->request_body;
    }

    /**
     * Get post values
     *
     * @param string $index - specific index in $_POST
     *
     * @return null
     */
    public function post($index = '')
    {
        if ($this->request_method !== 'POST') {
            return null;
        }

        if (!is_array($index) && !empty($index)) {
            return $_POST[$index];
        }

        return $_POST;
    }

    /**
     * Get name of request method
     *
     * @return null|string
     */
    public function method()
    {
        return $this->request_method;
    }

    /**
     * Get raw request body
     *
     * @return null|string
     */
    public function body()
    {
        return $this->request_body;
    }

    /**
     * Get request headers
     *
     * @return array|false|null
     */
    public function headers()
    {
        return $this->request_headers;
    }

    /**
     * Get referrer
     *
     * @return null
     */
    public function referrer()
    {
        return $this->referrer;
    }

    /**
     * Check if this is CLI request
     *
     * @return bool
     */
    public function is_cli()
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Check if this is ajax request
     *
     * @return bool
     */
    public function is_ajax()
    {
        return $this->request_with !== null;
    }
}