<?php

namespace VolunteerManager\Helper\Admin;

use \VolunteerManager\Helper\Field as Field;

class UI
{
    /**
     * Prints colorful taxonomy pills
     *
     * @param array|bool|\WP_Error $taxonomy
     * @return void
     */
    public static function createTaxonomyPills($taxonomy): void
    {
        if (empty($taxonomy) || is_wp_error($taxonomy)) {
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
     * @param int    $termId
     * @param string $taxonomySlug
     * @return string
     */
    public static function taxonomyColor($termId, $taxonomySlug): string
    {
        return Field::get(
            'taxonomy_color',
            $taxonomySlug . '_' . $termId
        ) ?? '#eee';
    }

    /**
     * Get contrast
     *
     * @param int    $termId
     * @param string $taxonomySlug
     * @return string
     */
    public static function taxonomyColorContrast($termId, $taxonomySlug)
    {
        $hexcolor = self::taxonomyColor($termId, $taxonomySlug);

        $r = hexdec(substr($hexcolor, 1, 2));
        $g = hexdec(substr($hexcolor, 3, 2));
        $b = hexdec(substr($hexcolor, 5, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return ($yiq >= 128) ? '#000' : '#fff';
    }
}
