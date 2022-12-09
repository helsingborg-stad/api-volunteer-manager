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
if (! defined('WPINC')) {
    die;
}

define('VOLUNTEER_MANAGER_PATH', plugin_dir_path(__FILE__));
define('VOLUNTEER_MANAGER_URL', plugins_url('', __FILE__));
define('VOLUNTEER_MANAGER_TEMPLATE_PATH', VOLUNTEER_MANAGER_PATH . 'templates/');
define('VOLUNTEER_MANAGER_TEXT_DOMAIN', 'api-volunteer-manager');

load_plugin_textdomain(VOLUNTEER_MANAGER_TEXT_DOMAIN, false, VOLUNTEER_MANAGER_PATH . '/languages');

require_once VOLUNTEER_MANAGER_PATH . 'Public.php';

// Register the autoloader
require __DIR__ . '/vendor/autoload.php';

// Acf auto import and export
add_action('acf/init', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('api-volunteer-manager');
    $acfExportManager->setExportFolder(VOLUNTEER_MANAGER_PATH . 'source/php/AcfFields/');
    $acfExportManager->autoExport(array(
        'api-volunteer-manager-settings' => 'group_61ea7a87e8aaa' //Update with acf id here, settings view
    ));
    $acfExportManager->import();
});

// Start application
new VolunteerManager\App();
