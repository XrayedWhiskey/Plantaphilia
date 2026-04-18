<?php
/*
 * ----------------------------------------------------------------------
 *
 *                          Borlabs Cookie
 *                    developed by Borlabs GmbH
 *
 * ----------------------------------------------------------------------
 *
 * Copyright 2018-2022 Borlabs GmbH. All rights reserved.
 * This file may not be redistributed in whole or significant part.
 * Content of this file is protected by international copyright laws.
 *
 * ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 * @copyright Borlabs GmbH, https://borlabs.io
 * @author Benjamin A. Bornschein
 *
 */

namespace BorlabsCookie;

class Autoloader
{
    /**
     * prefixes.
     *
     * (default value: [])
     *
     * @var mixed
     */
    protected $prefixes = [];

    /**
     * addNamespace function.
     *
     * @param mixed $prefix
     * @param mixed $baseDir
     * @param bool  $prepend (default: false)
     */
    public function addNamespace($prefix, $baseDir, $prepend = false)
    {
        $prefix = trim($prefix, '\\') . '\\';

        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/';

        if (!isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = [];
        }

        if ($prepend == false) {
            array_push($this->prefixes[$prefix], $baseDir);
        } else {
            array_unshift($this->prefixes[$prefix], $baseDir);
        }
    }

    /**
     * loadClass function.
     *
     * @param mixed $class
     */
    public function loadClass($class)
    {
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);

            $relativeClass = substr($class, $pos + 1);

            $fileLoaded = $this->loadFile($prefix, $relativeClass);

            if ($fileLoaded) {
                return $fileLoaded;
            }

            $prefix = rtrim($prefix, '\\');
        }
    }

    /**
     * loadFile function.
     *
     * @param mixed $prefix
     * @param mixed $relativeClass
     */
    public function loadFile($prefix, $relativeClass)
    {
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }

        $relativeClass = str_replace('\\', '/', $relativeClass);

        foreach ($this->prefixes[$prefix] as $baseDir) {
            $file = $baseDir . $relativeClass . '.php';

            if ($this->requireFile($file)) {
                return true;
            }
        }

        return false;
    }

    /**
     * register function.
     */
    public function register()
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * requireFile function.
     *
     * @param mixed $file
     */
    public function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;

            return true;
        }

        return false;
    }
}
