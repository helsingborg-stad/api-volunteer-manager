<?php

namespace VolunteerManager\Entity;

use WP_Error;

interface ITerm
{
    /**
     * Inserts terms into the given taxonomy if they do not already exist.
     *
     * @param array $terms An array of terms to be inserted. Each term should be an associative array
     *                              with keys: 'name', 'slug' (optional), and 'description' (optional).
     * @param string $taxonomy_slug The slug of the taxonomy into which the terms should be inserted.
     * @param array $args An optional array of arguments to override the default term properties.
     *
     * @return array|WP_Error       Returns an array of successfully inserted terms' information
     *                              (term_id and term_taxonomy_id) or a WP_Error object if the provided
     *                              taxonomy does not exist.
     */
    public function insertTerms(array $terms, string $taxonomy_slug, array $args = []);
}
