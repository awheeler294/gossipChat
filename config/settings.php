<?php
session_start();

//Set Constants
define('SITE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/../');

//Autoload PHP classes
function loadClass($class_name) {
	$path = '../' . 'classes/' . $class_name . '.php';
    $includesPath = SITE_DIR . '/includes/'. $class_name . '.php';;
//    error_log('[gossip][settings][loadClass]::$path: ' . print_r($path, true));
//    error_log('[gossip][settings][loadClass]::file_exists: ' . print_r(file_exists($path), true));

	if (file_exists($path)) {
        require_once($path);
    }
    else if (file_exists($includesPath)) {
        require_once($includesPath);
    }
	else if (stream_resolve_include_path($class_name . 'php') !== false) {
        require_once($class_name . '.php');
    }
    else {
        $paths  = explode(':', get_include_path());
        foreach ($paths as $path) {
            $filePath = $path . DIRECTORY_SEPARATOR . $class_name . '.php';
            if (file_exists($filePath)) {
    
                require_once($filePath);
            }
        }
    }
}
spl_autoload_register('loadClass');