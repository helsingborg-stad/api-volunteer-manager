<?php

namespace VolunteerManager\Entity;

use VolunteerManager\Helper\Field;
use WP_Error;

class Taxonomy implements ITerm
{
    public $namePlural;
    public $nameSingular;
    public $slug;
    public $args;
    public $postTypes;

    private $defaultArgs = [
        'show_in_rest' => true
    ];

    public function __construct($namePlural, $nameSingular, $slug, $postTypes, $args)
    {
        $this->namePlural = $namePlural;
        $this->nameSingular = $nameSingular;
        $this->slug = $slug;
        $this->postTypes = $postTypes;
        $this->args = array_merge(
            $this->defaultArgs,
            $args
        );
    }

    public function registerTaxonomy() : string
    {
        $labels = array(
            'name'              => $this->namePlural,
            'singular_name'     => $this->nameSingular,
            'search_items'      => sprintf(__('Search %s', 'api-volunteer-manager'), $this->namePlural),
            'all_items'         => sprintf(__('All %s', 'api-volunteer-manager'), $this->namePlural),
            'parent_item'       => sprintf(__('Parent %s:', 'api-volunteer-manager'), $this->nameSingular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'api-volunteer-manager'), $this->nameSingular) . ':',
            'edit_item'         => sprintf(__('Edit %s', 'api-volunteer-manager'), $this->nameSingular),
            'update_item'       => sprintf(__('Update %s', 'api-volunteer-manager'), $this->nameSingular),
            'add_new_item'      => sprintf(__('Add New %s', 'api-volunteer-manager'), $this->nameSingular),
            'new_item_name'     => sprintf(__('New %s Name', 'api-volunteer-manager'), $this->nameSingular),
            'menu_name'         => $this->nameSingular,
        );

        $this->args['labels'] = $labels;

        register_taxonomy($this->slug, $this->postTypes, $this->args);

        return $this->slug;
    }

    /**
     * Inserts terms into the given taxonomy if they do not already exist.
     *
     * @param array  $terms         An array of terms to be inserted. Each term should be an associative array
     *                              with keys: 'name', 'slug' (optional), and 'description' (optional).
     * @param array  $args          An optional array of arguments to override the default term properties.
     *
     * @return array|WP_Error       Returns an array of successfully inserted terms' information
     *                              (term_id and term_taxonomy_id) or a WP_Error object if the provided
     *                              taxonomy does not exist.
     */
    public function insertTerms(array $terms, array $args = [])
    {
        $inserted_terms = [];

        if (!taxonomy_exists($this->slug)) {
            return new WP_Error(
                'invalid_taxonomy',
                'The provided taxonomy does not exist.');
        }

        $terms = $this->validateTerms($terms);

        foreach ($terms as $term) {
            $default_args = [
                'slug' => $term['slug'] ?? '',
                'description' => $term['description'] ?? '',
            ];

            $result = wp_insert_term(
                $term['name'],
                $this->slug,
                array_merge($default_args, $args)
            );

            if (!is_wp_error($result)) {
                if (isset($term['color'])) {
                    Field::update('taxonomy_color', $term['color'], $this->slug . '_' . $result['term_id']);
                }

                $inserted_terms[] = $result;
            }
        }

        return $inserted_terms;
    }

    /**
     * Validates the terms array, ensuring each term is well-formed and removing any existing terms.
     *
     * @param array $terms An array of terms to be validated.
     *
     * @return array Returns an array of well-formed terms with existing terms removed.
     */
    private function validateTerms(array $terms): array
    {
        return array_filter($terms, function ($term) {
            return !empty($term['name']) && !term_exists($term['name'], $this->slug);
        });
    }
}
