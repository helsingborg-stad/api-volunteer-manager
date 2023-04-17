<?php

namespace VolunteerManager\Helper\Admin;

use \VolunteerManager\Helper\Field as Field;

class UI
{
    /**
     * Prints colorful taxonomy pills
     *
     * @param array|bool|\WP_Error $taxonomy
     * @return string
     */
    public static function createTaxonomyPills($taxonomy): string
    {
        if (empty($taxonomy) || is_wp_error($taxonomy)) {
            return "-";
        }

        $pillsHtml = '';
        foreach ((array)$taxonomy as $item) {
            $pillsHtml .= self::generateTaxonomyPillHtml($item);
        }

        return $pillsHtml;
    }

    /**
     * Generates taxonomy pills html
     * @param object $item
     * @return string
     */
    public static function generateTaxonomyPillHtml(object $item): string
    {
        return sprintf(
            '<span style="background: %s; color: %s;" class="term-pill term-pill-%s">%s</span>',
            self::taxonomyColor($item->term_id, $item->taxonomy),
            self::taxonomyColorContrast($item->term_id, $item->taxonomy),
            $item->slug,
            $item->name
        );
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
        return get_field(
            'taxonomy_color',
            $taxonomySlug . '_' . $termId,
            true
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
