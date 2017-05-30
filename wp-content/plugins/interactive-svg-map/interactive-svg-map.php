<?php
/**
 * @package Interactive SVG Map
 */
/*
Plugin Name: Interactive SVG Map
Plugin URI: http://www.clariontechnologies.co.in
Description: Interactive SVG Map
Version: 1.0.0
Author: Yogesh Pawar , Clarion Technologies
Author URI: http://www.clariontechnologies.co.in
License: GPLv2 or later
Text Domain: Interactive SVG Map
*/

//Plugin Constant
defined('ABSPATH') or die('Restricted direct access!');
define('AUTH_PLUGINS_PATH', plugins_url());
$plugin = plugin_basename(__FILE__);

//Main Plugin files
if (!class_exists('Interactive_Map')) {
    require('classes/class.interactive.map.php');
}

//Initialising Class Plugin
new Interactive_Map();
?>