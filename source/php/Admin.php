<?php

namespace VolunteerManager;

class Admin
{

  public function __construct() {
    add_filter('get_sample_permalink_html', array($this, 'replacePermalink'), 10, 5);
  }

  /**
   * Replaces permalink on edit post with API-url
   * @return string
   */
  public function replacePermalink($return, $post_id, $new_title, $new_slug, $post)
  {
      $postType = $post->post_type;
      $jsonUrl = home_url() . '/json/wp/v2/' . $postType . '/';
      $apiUrl = $jsonUrl . $post_id;

      return '<strong>' . __('API-url', 'api-volunteer-manager') . ':</strong> <a href="' . $apiUrl . '" target="_blank">' . $apiUrl . '</a>';
  }
}
