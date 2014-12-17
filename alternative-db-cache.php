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

if (!(version_compare(PHP_VERSION, '5.4.0') > 0)) {
    $notice = 'PHP 5.4.0 以上が必要です。';
}
else if (!(extension_loaded('apc') && ini_get('apc.enabled'))) {
    $notice = 'APCが必要です。';
}

if (!$notice) {
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
} else {
    add_action('admin_notices', function () use ($notice) {
        echo "<div class=\"error\"><p>{$notice}</p></div>";
    });
}
