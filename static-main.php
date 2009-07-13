<?php

/**
 * This file is designed to be the new 'server' of sites using StaticPublisher.
 * to use this, you need to modify your .htaccess to point all requests to
 * static-main.php, rather than main.php. This file also allows for using
 * static publisher with the subsites module.
 *
 * If you are using StaticPublisher+Subsites, set the following in _config.php:
 *   FilesystemPublisher::$domain_based_caching = true;
 */

if (file_exists('../subsites/host-map.php')) {
	include_once '../subsites/host-map.php';
	$subsiteHostmap['default'] = isset($subsiteHostmap['default']) ? $subsiteHostmap['default'] : '';
	
	// Look for the host, and find the cache dir
	$host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
	$cacheDir = (isset($subsiteHostmap[$host]) ? $subsiteHostmap[$host] : $subsiteHostmap['default']) . '/';
} else {
	$cacheDir = '';
}

// Look for the file in the cachedir
$file = preg_replace('/[^a-zA-Z0-9]/si', '_', trim($_SERVER['REQUEST_URI'], '/'));
$file = $file ? $file : 'index';
	
if (file_exists('../cache/'.$cacheDir.$file.'.html')) {
	header('X-cache: hit at '.date('r'));
	echo file_get_contents('../cache/'.$cacheDir.$file.'.html');
} elseif (file_exists('../cache/'.$cacheDir.$file.'.php')) {
	header('X-cache: hit at '.date('r'));
	include_once '../cache/'.$cacheDir.$file.'.php';
} else {
	header('X-cache: miss at '.date('r'));
	// No cache hit... fallback!!!
	include 'main.php';
}

?>