<?php

define('WPINC', 'wp-includes');

function plugin_dir_path($file) { return dirname($file) . '/'; }
function plugins_url($path, $file) { return 'https://example.test/plugin'; }
function plugin_basename($file) { return basename($file); }
function load_plugin_textdomain(...$args) {}
function add_action(...$args) { $GLOBALS['hooks'][] = $args[0]; }

if (isset($argv[2])) {
    require $argv[2];
}
require $argv[1];

if (!in_array('plugins_loaded', $GLOBALS['hooks'] ?? [], true)) {
    throw new RuntimeException('Plugin did not register its initialization hook.');
}
