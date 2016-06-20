<?php
/**
 * Created by PhpStorm.
 * User: Norbert
 * Date: 2016. 06. 20.
 * Time: 21:00
 */
clearstatcache();

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('ROOT')) {
    define('ROOT', dirname(__FILE__));
}

function debug($args = []) {
    echo '<pre>';
    print_r($args);
    echo '</pre>';
}



spl_autoload_register(function ($className) {
    $className = ltrim($className, '\\');

    $namespace = '';
    $fileName = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = ROOT . DS;
    }
    $fileName .= str_replace('_', DS, $className) . '.php';
    require_once $fileName;
});