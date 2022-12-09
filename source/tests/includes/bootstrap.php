<?php

// Get around direct access blockers.
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/../../../');
}

define('VOLUNTEER_MANAGER_PATH', __DIR__ . '/../../../');
define('VOLUNTEER_MANAGER_URL', 'https://example.com/wp-content/plugins/' . 'modularity-api-volunteer-manager');
define('VOLUNTEER_MANAGER_TEMPLATE_PATH', VOLUNTEER_MANAGER_PATH . 'templates/');


// Register the autoloader
$loader = require __DIR__ . '/../../../vendor/autoload.php';
$loader->addPsr4('VolunteerManager\\Test\\', __DIR__ . '/../php/');

require_once __DIR__ . '/PluginTestCase.php';
