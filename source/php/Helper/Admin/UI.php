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
    public static function createTaxonomyPills($taxonomy) {
        if (empty($taxonomy)) {
            echo "-";
        } else {
            foreach ((array)$taxonomy as $item) {
                echo sprintf(
                    '<span style="background: %s;" class="todo-term-pill %s">%s</span>',
                    self::taxonomyColor($item->term_id, $item->taxonomy),
                    $item->slug,
                    $item->name
                ); 
            }
        }
    }

    /**
     * Returns colorcode by taxonomy id
     * @return string
     */

    public static function taxonomyColor($termId, $taxonomySlug) : string
    {
        return Field::get(
            'taxonomy_color', 
            $taxonomySlug . '_' . $termId
        ) ?? '#eee'; 
    }
}
