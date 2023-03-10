<?php

/**
 * Plugin Name:       Volunteer Manager
 * Plugin URI:        https://github.com/helsingborg-stad/api-volunteer-manager
 * Description:       Creates a api that may be used to manage volunteer assignments
 * Version:           1.0.0
 * Author:            Sebastian Thulin @ Helsingborg Stad
 * Author URI:        https://github.com/helsingborg-stad
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       api-volunteer-manager
 * Domain Path:       /languages
 */

// Protect agains direct file access
if (!defined('WPINC')) {
    die;
}

define('VOLUNTEER_MANAGER_PATH', plugin_dir_path(__FILE__));
define('VOLUNTEER_MANAGER_URL', plugins_url('', __FILE__));
define('VOLUNTEER_MANAGER_TEMPLATE_PATH', VOLUNTEER_MANAGER_PATH . 'templates/');
const AVM_TEXT_DOMAIN = 'api-volunteer-manager';

load_plugin_textdomain(AVM_TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');

require_once VOLUNTEER_MANAGER_PATH . 'Public.php';

// Register the autoloader
require __DIR__ . '/vendor/autoload.php';

// Acf auto import and export
add_action('acf/init', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('api-volunteer-manager');
    $acfExportManager->setExportFolder(VOLUNTEER_MANAGER_PATH . 'source/php/AcfFields/');
    $acfExportManager->autoExport(array(
        'employer' => 'group_639308fb101ce',
        'employee' => 'group_639b042622dd1',
        'signup' => 'group_639b06c19d21f',
        'location' => 'group_63a0408b8601f',
        'taxonomy' => 'group_63986eae18b97',
        'assignment' => 'group_63dce32c807e2',
        'assignment-status' => 'group_63e2023f5baca',
        'options' => 'group_640b20dece43a'
    ));
    $acfExportManager->import();
});

// Start application
new VolunteerManager\App();
