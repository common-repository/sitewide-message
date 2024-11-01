<?php
//if uninstall not called from WordPress exit
if(!defined('WP_UNINSTALL_PLUGIN')){ exit(); }

delete_option("tb_msg_settings");

global $wpdb;
$GLOBALS['wpdb']->query("OPTIMIZE TABLE `" . $GLOBALS['wpdb']->prefix . "options`");