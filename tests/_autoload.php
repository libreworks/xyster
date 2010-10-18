<?php
/**
 * Autoloader, based on the ZF test autoloader
 *
 * @param string $class
 * @return void
 */
function XysterTest_Autoloader($class)
{
    $dir = __DIR__;
    $class = ltrim($class, '\\');

    if (!preg_match('#^(Xyster(Test)?|Zend|PHPUnit)(\\\\|_)#', $class)) {
        return false;
    }

    $segments = preg_split('#[\\\\_]#', $class);
    $ns = array_shift($segments);

    switch ($ns) {
        case 'Xyster':
            $file = dirname($dir) . '/library/Xyster/';
            break;
        case 'XysterTest':
            $file = $dir . '/Xyster/';
            break;
        default:
            $file = false;
            break;
    }

    if ($file) {
        $file .= implode(DIRECTORY_SEPARATOR, $segments) . '.php';
        if (file_exists($file)) {
            return include_once $file;
        }
    }

    $segments = explode('_', $class);
    $ns = array_shift($segments);

    switch ($ns) {
        case 'PHPUnit':
        case 'Zend':
            return include_once str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        case 'Xyster':
            $file = dirname($dir) . '/library/Xyster/';
            break;
        default:
            return false;
    }
    $file .= implode(DIRECTORY_SEPARATOR, $segments) . '.php';
    if (file_exists($file)) {
        return include_once $file;
    }
    return false;
}
spl_autoload_register('XysterTest_Autoloader', true, true);