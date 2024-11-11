<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('global_text_size_settings');
