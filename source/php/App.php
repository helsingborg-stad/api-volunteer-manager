<?php

namespace VolunteerManager;

use \VolunteerManager\Helper\CacheBust as CacheBust;

class App
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('plugins_loaded', array($this, 'init'));

        add_filter('acf/fields/google_map/api', array($this, 'setGoogleApiKey'));
    }

    public function init()
    {
        //General
        new Api();

        //Post types
        $assignment = new Assignment();
        $assignment->addHooks();
        new Employee();

        //Admin
        new Admin();
    }

    /**
     * Enqueue required style
     * @return void
     */
    public function enqueueStyles()
    {
        wp_register_style(
            'api-volunteer-manager-css',
            VOLUNTEER_MANAGER_URL . '/dist/' .
            (new CacheBust())->name('css/api-volunteer-manager.css')
        );

        wp_enqueue_style('api-volunteer-manager-css');
    }

    /**
     * Enqueue required scripts
     * @return void
     */
    public function enqueueScripts()
    {
        wp_register_script(
            'api-volunteer-manager-js',
            VOLUNTEER_MANAGER_URL . '/dist/' .
            (new CacheBust())->name('js/api-volunteer-manager.js')
        );

        wp_enqueue_script('api-volunteer-manager-js');
    }

    /**
     * Filter that sets Google Maps API key
     * @param array $api
     * @return array $api
     */
    public function setGoogleApiKey($api)
    {
        $api['key'] = defined('GOOGLE_API_KEY') ? GOOGLE_API_KEY : '';
        return $api;
    }
}
