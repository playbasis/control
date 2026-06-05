<?php

/**
 * @file
 * Convenience file that registers autoload handler for HTML Purifier.
 * It also does some sanity checks.
 */

if (function_exists('spl_autoload_register')) {
    if (function_exists('spl_autoload_unregister')) {
        // We need unregister for our pre-registering functionality
        HTMLPurifier_Bootstrap::registerAutoload();
    } else {
        spl_autoload_register(array('HTMLPurifier_Bootstrap', 'autoload'));
    }
    if (function_exists('__autoload')) {
        // Be polite and ensure that userland autoload gets retained
        spl_autoload_register('__autoload');
    }
} elseif (!function_exists('__autoload') && version_compare(PHP_VERSION, '8.0.0', '<')) {
    eval('function __autoload($class) { return HTMLPurifier_Bootstrap::autoload($class); }');
}

if (ini_get('zend.ze1_compatibility_mode')) {
    trigger_error("HTML Purifier is not compatible with zend.ze1_compatibility_mode; please turn it off", E_USER_ERROR);
}

// vim: et sw=4 sts=4
