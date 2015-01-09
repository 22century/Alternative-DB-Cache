<?php
/**
 * Plugin Name: Alternative DB Cache
 * Plugin URI: https://github.com/22century/Alternative-DB-Cache
 * Description: APCによるクエリキャッシュ
 * Version: 0.1.0
 * Author: 22century
 * Author URI: https://github.com/22century
 * License: GPL2
 */

defined('ABSPATH') or die();

$notice = '';

if (!(extension_loaded('apc') && ini_get('apc.enabled'))) {
    $notice = 'プラグイン: "Alternative DB Cache" の動作にはAPCが必要です。';
}

if ($notice) {
    add_action('admin_notices', function () use ($notice) {
        echo "<div class=\"error\"><p>{$notice}</p></div>";
    });
}

// install
register_activation_hook(__FILE__, function () {
    symlink(__DIR__ . '/db.php', ABSPATH . '/wp-content/db.php');
});

// uninstall
register_deactivation_hook( __FILE__, function () {
    if (file_exists(ABSPATH . '/wp-content/db.php')) {
        unlink(ABSPATH . '/wp-content/db.php');
    }
});
