<?php

namespace VolunteerManager;

use VolunteerManager\Helper\CacheBust;

class Admin
{
    public function addHooks()
    {
        add_filter('get_sample_permalink_html', array($this, 'replacePermalink'), 10, 5);
        add_action('acf/init', array($this, 'addOptionsPage'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('after_setup_theme', array($this, 'themeSupport'));
    }

    /**
     * Replaces permalink on edit post with API-url
     * @param $return
     * @param $post_id
     * @param $new_title
     * @param $new_slug
     * @param $post
     * @return string
     */
    public function replacePermalink($return, $post_id, $new_title, $new_slug, $post): string
    {
        $postType = $post->post_type;
        $jsonUrl = home_url() . '/json/wp/v2/' . $postType . '/';
        $apiUrl = $jsonUrl . $post_id;

        return '<strong>' . __('API-url', 'api-volunteer-manager') . ':</strong> <a href="' . $apiUrl . '" target="_blank">' . $apiUrl . '</a>';
    }

    /**
     * Registers option page
     * @return void
     */
    public function addOptionsPage(): void
    {
        acf_add_options_sub_page(array(
            'page_title' => __('Options', 'api-volunteer-manager'),
            'menu_title' => __('Options', 'api-volunteer-manager'),
            'menu_slug' => 'options',
            'parent_slug' => 'edit.php?post_type=assignment',
            'capability' => 'install_themes'
        ));
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
     * Add theme support
     */
    public function themeSupport()
    {
        add_theme_support('post-thumbnails');
    }
}
