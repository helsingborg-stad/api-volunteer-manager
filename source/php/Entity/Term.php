<?php

namespace VolunteerManager\Entity;

use WP_Error;

class Term implements ITerm
{
    /**
     * Inserts terms into the given taxonomy if they do not already exist.
     *
     * @param array  $terms         An array of terms to be inserted. Each term should be an associative array
     *                              with keys: 'name', 'slug' (optional), and 'description' (optional).
     * @param string $taxonomy_slug The slug of the taxonomy into which the terms should be inserted.
     * @param array  $args          An optional array of arguments to override the default term properties.
     *
     * @return array|WP_Error       Returns an array of successfully inserted terms' information
     *                              (term_id and term_taxonomy_id) or a WP_Error object if the provided
     *                              taxonomy does not exist.
     */
    public function insertTerms(array $terms, string $taxonomy_slug, array $args = [])
    {
        $inserted_terms = [];

        if (!taxonomy_exists($taxonomy_slug)) {
            return new WP_Error('invalid_taxonomy', 'The provided taxonomy does not exist.');
        }

        foreach ($terms as $term) {
            if (empty($term['name']) || term_exists($term['name'], $taxonomy_slug)) {
                continue;
            }

            $default_args = [
                'slug' => $term['slug'] ?? '',
                'description' => $term['description'] ?? '',
            ];

            $result = wp_insert_term(
                $term['name'],
                $taxonomy_slug,
                array_merge($default_args, $args)
            );

            if (!is_wp_error($result)) {
                $inserted_terms[] = $result;
            }
        }

        return $inserted_terms;
    }
}
