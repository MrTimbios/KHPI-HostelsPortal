<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/

return array(
    // custom source example
    'general' => array(
     	$min_documentRoot . '/engine/classes/js/jquery.js',
    ),
	
    'general3' => array(
     	$min_documentRoot . '/engine/classes/js/jquery3.js',
    ),
	
    'admin' => array(
     	$min_documentRoot . '/engine/skins/javascripts/application.js', 
    ),
);