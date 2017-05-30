<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

require_once 'includes/aphms-config.php';
delete_option(APHMS_OPTION);
delete_option(APHMS_OPTION_NOTICE);
delete_option(APHMS_OPTION_VERSION);
?>