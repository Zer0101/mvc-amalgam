<?php

/**
 * @package      Amalgam
 * @author       Anton Zencenco
 * @copyright    Copyright (c) 2016 - 2017
 * @license      https://opensource.org/licenses/MIT MIT License
 * @since        0.0.0
 * @filesource
 */

namespace Amalgam\Autoload;

/**
 * Class Loader
 *
 * An autoloader, that uses PSR4, traditional classmap and file loading
 *
 * @package Amalgam\System
 */
class Loader
{
    /**
     * Array of PSR-4 style pairs namespaces:path
     *
     * @var array
     */
    protected $psr4 = [];

    /**
     * An old fashioned classmap: a class name and full pah to file
     * Full path is used because it will be too long to wait with relative path before autoloader will find them
     *
     * @var array
     */
    protected $classmap = [];

    /**
     * An array of files that will be loaded on the start
     *
     * @var array
     */
    protected $files = [];

    /**
     * Set autoload configurations for all Amalgam system classes
     *
     * @param \Amalgam\Autoload\Loader|null $loader - instance of Loader (new instance will be created if this parameter is empty)
     *
     * @return \Amalgam\Autoload\Loader
     */
    public static function defaultLoader(Loader $loader = null)
    {
        if (empty($loader)) {
            $loader = new Loader();
        }

        if (is_readable(__DIR__ . '/registered/autoload_namespaces.php')) {
            $loader->registerNamespaces(require_once __DIR__ . '/registered/autoload_namespaces.php');
        }

        if (is_readable(__DIR__ . '/registered/autoload_classmap.php')) {
            $loader->registerClasses(require_once __DIR__ . '/registered/autoload_classmap.php');
        }

        if (is_readable(__DIR__ . '/registered/autoload_files.php')) {
            $loader->registerFiles(require_once __DIR__ . '/registered/autoload_files.php');
        }

        return $loader;
    }

    /**
     * Register an array of namespaces
     *
     *
     *
     * @param array $namespaces - an array of namespaces.
     *                          Values are accepted in form: array('Namespace\\Another' => 'path/to/file/', )
     * @param bool  $merge      - if this value is set to true - namespaces will be merged with already registered
     *                          Else it will override registered values
     *
     * @return \Amalgam\Autoload\Loader - return self to use chain call
     */
    public function registerNamespaces(array $namespaces, bool $merge = false) :Loader
    {
        if (!empty($namespaces)) {
            $namespaces = array_filter($namespaces);
            $this->psr4 = $merge ? array_merge($this->psr4, $namespaces) : $namespaces;
        }

        return $this;
    }

    /**
     * Register a classmap
     *
     * @param array $classmap - a classmap in form: array('Namespace\\Another\\Class' => 'path/to/file.php', )
     * @param bool  $merge    - if this value is set to true - classmap will be merged with already registered
     *                        Else it will override registered values
     *
     * @return \Amalgam\Autoload\Loader - return self to use chain call
     */
    public function registerClasses(array $classmap, bool $merge = false) :Loader
    {
        if (!empty($classmap)) {
            $classmap = array_filter($classmap);
            $this->classmap = $merge ? array_merge($this->classmap, $classmap) : $classmap;
        }

        return $this;
    }

    /**
     * Register an array of files that will be loaded immediately
     *
     * @param array $filemap - a list of paths in form: array('path/to/file1.php', 'path/to/file2.php')
     * @param bool  $merge   - if this value is set to true - list of file paths will be merged with already registered
     *                       Else it will override registered values
     *
     * @return \Amalgam\Autoload\Loader - return self to use chain call
     */
    public function registerFiles(array $filemap, bool $merge = false) :Loader
    {
        if (!empty($filemap)) {
            $filemap = array_filter($filemap);
            $this->files = $merge ? array_merge($this->files, $filemap) : $filemap;
        }

        return $this;
    }

    /**
     * Register autoload functions
     */
    public function register()
    {
        //First - prepend autoloader for mapped classes
        spl_autoload_register([$this, 'loadMapped'], true, true);

        //Second prepend PSR-4 autoloader
        spl_autoload_register([$this, 'load'], true, true);

        //Third - connect directly registered static files
        if (!empty($this->files)) {
            foreach ($this->files as $staticFile) {
                $this->load($staticFile, true);
                unset($staticFile);
            }
        }
    }

    /**
     * Unregister current autoloaders
     * But it cannot unload already loaded files
     */
    public function unregister()
    {
        spl_autoload_unregister([$this, 'load']);
        spl_autoload_unregister([$this, 'loadMapped']);
    }

    /**
     * Autoload method to load files from psr4 array and simple files
     *
     * @param      $name
     * @param bool $is_file
     *
     * @return bool
     */
    public function load($name, $is_file = false) :bool
    {
        if (empty($name) || (strpos($name, '\\') === false)) {
            return false;
        }

        if ($is_file) {
            return $this->connectFile($name);
        }

        $name = trim($name, '\\');
        $name = str_ireplace(['.php'], '', $name);

        foreach ($this->psr4 as $namespace => $directory) {
            if (strpos($name, $namespace) === 0) {
                $length = strlen($namespace);
                $fileName = $directory . str_replace('\\', '/', substr($name, $length)) . '.php';
                $fileName = $this->connectFile($fileName);

                if ($fileName) {
                    return $fileName;
                }
            }
        }

        return false;
    }

    /**
     * Autoload method to load files from your classmap table
     *
     * @param $class
     *
     * @return bool
     */
    public function loadMapped($class)
    {
        //Class will be loaded if it exists in our classmap
        if (isset($this->classmap[$class])) {
            //Get path to file
            $path = $this->classmap[$class];

            //Trying to connect file
            //If it is failed then we make concat of class name and file path and trying again
            if (!$this->connectFile($path)) {
                return $this->connectFile(rtrim($path, '/') . '/' . ucfirst($class) . '.php');
            }

            return true;
        }

        return false;
    }

    /**
     * Sanitize file name from illegal symbols
     *
     * @param $name
     *
     * @return string
     */
    public function sanitize($name) :string
    {
        //Autoloader MUST not return any value or throw exception
        //so if name was empty (ex. null) simple return empty string
        $name = preg_replace('/[^a-zA-Z0-9\_\-\s\/\.\:\\\\]/', '', $name) ?? '';

        return $name ?? '';
    }

    /**
     * Connect file (if it is readable - in more case it will check file existence and sometimes - if file is not corrupt)
     *
     * @param string $name
     *
     * @return bool
     */
    protected function connectFile(string $name) :bool
    {
        $name = $this->sanitize($name);

        if (is_readable($name)) {
            return (bool)require $name;
        }

        return false;
    }
}