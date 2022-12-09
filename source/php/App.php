<?php

namespace VolunteerManager;

class App
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));

        $this->cacheBust = new \VolunteerManager\Helper\CacheBust();
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
            $this->cacheBust->name('css/api-volunteer-manager.css')
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
            $this->cacheBust->name('js/api-volunteer-manager.js')
        );

        wp_enqueue_script('api-volunteer-manager-js');
    }
}
