<?php
/**
 * Plugin Name: APH Merge Scripts
 * Description: Merge and minify css & javascript files. Please go to <a href="options-general.php?page=aph-merge-scripts" target="_blank">Settings &raquo; APH Merge Scripts</a> to change some option, or you can leave it as is
 * Version: 1.3.1
 * Author: Agus Prawoto Hadi
 * Author URI: http://www.webdevcorner.com
 */
 
// Helper for shared function used by backend and frontend
require_once 'includes/aphms-config.php';

// Notice manager
require_once 'includes/aphms-admin-notices.php';
require_once 'includes/aphms-admin.php';
new Aphms_Admin();

// ADMIN BAR
require_once 'includes/aphms-admin-bar.php';
$merge = new Aphms_Admin_Bar();
$merge->init();

// FRONT (MERGE SCRIPTS)
require_once 'includes/aphms-front.php';
$merge = new Aphms_Front();
$merge->init();
?>