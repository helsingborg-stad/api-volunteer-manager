<?php

namespace VolunteerManager;

class Admin
{
    public function addHooks()
    {
        add_filter('get_sample_permalink_html', array($this, 'replacePermalink'), 10, 5);
        add_action('acf/init', array($this, 'addOptionsPage'));
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
}
