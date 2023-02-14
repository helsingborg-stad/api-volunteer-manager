<?php

namespace VolunteerManager\Helper\Admin;

use \VolunteerManager\Helper\Field as Field;

class UI
{
    /**
     * Prints colorful taxonomy pills
     *
     * @param WP_Taxonomy $taxonomy
     * @return void
     */
    public static function createTaxonomyPills($taxonomy)
    {
        if (empty($taxonomy)) {
            echo "-";
        } else {
            foreach ((array)$taxonomy as $item) {
                echo sprintf(
                    '<span style="background: %s; color: %s;" class="term-pill term-pill-%s">%s</span>',
                    self::taxonomyColor($item->term_id, $item->taxonomy),
                    self::taxonomyColorContrast($item->term_id, $item->taxonomy),
                    $item->slug,
                    $item->name
                );
            }
        }
    }

    /**
     * Returns colorcode by taxonomy id
     *
     * @param int $termId
     * @param string $taxonomySlug
     * @return string
     */
    public static function taxonomyColor($termId, $taxonomySlug) : string
    {
        return Field::get(
            'taxonomy_color',
            $taxonomySlug . '_' . $termId
        ) ?? '#eee';
    }

    /**
     * Get contrast
     *
     * @param int $termId
     * @param string $taxonomySlug
     * @return string
     */
    public static function taxonomyColorContrast($termId, $taxonomySlug)
    {
        $color = self::taxonomyColor($termId, $taxonomySlug);

        $r = hexdec(substr($hexcolor, 1, 2));
        $g = hexdec(substr($hexcolor, 3, 2));
        $b = hexdec(substr($hexcolor, 5, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return ($yiq >= 128) ? '#000' : '#fff';
    }

    public static function createEditStatusUrl(int $postId, string $postStatus): String
    {
        $nonce = wp_create_nonce('edit_status_nonce');
        $getData = http_build_query(array(
            'nonce' => $nonce,
            'action' => 'edit_post_status',
            'post_status' => $postStatus,
            'post_id' => $postId,
            'paged' => $_GET['paged']
        ));
        $url = admin_url('admin-post.php') . '?' . $getData;
        return $url;
    }
}
