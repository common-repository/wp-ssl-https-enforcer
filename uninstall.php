<?php

if(!defined('WP_UNINSTALL_PLUGIN')){
    exit();
}

$option_name = "wse_plugin_settings";

delete_option($option_name);
