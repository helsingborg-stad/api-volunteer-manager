<?php

namespace VolunteerManager\Entity;

use Exception;
use WP_Query;

class PostType implements PostTypeInterface
{
    public string $slug;
    public string $namePlural;
    public string $nameSingular;
    public array $labels;
    public array $args;
    public array $tableColumns = array();
    public array $tableSortableColumns = array();
    public array $tableColumnsContentCallback = array();

    public function __construct(string $slug, string $namePlural, string $nameSingular, array $args = array(), array $labels = array())
    {
        $this->slug = $slug;
        $this->namePlural = $namePlural;
        $this->nameSingular = $nameSingular;
        $this->args = $args;
        $this->labels = $labels;
    }

    /**
     * Register wp actions and filters
     * @return void
     */
    public function addHooks(): void
    {
        add_action('init', [$this, 'registerPostType']);
        add_action('manage_' . $this->slug . '_posts_custom_column', [$this, 'tableColumnsContent'], 10, 2);
        add_filter('manage_edit-' . $this->slug . '_columns', [$this, 'setTableColumns']);
        add_filter('manage_edit-' . $this->slug . '_sortable_columns', [$this, 'tableSortableColumns']);
        add_action('pre_get_posts' , [$this, 'sortColumns']);
    }

    public function sortColumns(WP_Query $query): void {
        if (!$query->is_main_query()||empty($query->query_vars['orderby'])){
            return;
        }
        if ($query->query_vars['orderby']==='end_date'){
            $query->query_vars['orderby']='meta_value';
            $query->query_vars['meta_query']=array(
                'relation' => 'OR',
                'end_date_not_exist' => array(
                    'key' => 'end_date',
                    'compare' => 'NOT EXISTS'
                ),
                'end_date_sort' => array(
                    'key' => 'end_date',
                    'compare' => 'EXISTS'
                )
            );
        }
    }

    /**
     * Registers a custom post type
     * @return void
     */
    public function registerPostType(): void
    {
        $labels = array_merge(
            array(
                'name' => __(ucfirst($this->namePlural), 'api-volunteer-manager'),
                'singular_name' => __(ucfirst($this->nameSingular), 'api-volunteer-manager'),
                'add_new' => sprintf(__('Add new %s', 'api-volunteer-manager'), __(strtolower($this->nameSingular), 'api-volunteer-manager')),
                'add_new_item' => sprintf(__('Add new %s', 'api-volunteer-manager'), __(strtolower($this->nameSingular), 'api-volunteer-manager')),
                'edit_item' => sprintf(__('Edit %s', 'api-volunteer-manager'), __(strtolower($this->nameSingular), 'api-volunteer-manager')),
                'new_item' => sprintf(__('New %s', 'api-volunteer-manager'), __(strtolower($this->nameSingular), 'api-volunteer-manager')),
                'view_item' => sprintf(__('View %s', 'api-volunteer-manager'), __(strtolower($this->nameSingular), 'api-volunteer-manager')),
                'search_items' => sprintf(__('Search %s', 'api-volunteer-manager'), __(strtolower($this->namePlural), 'api-volunteer-manager')),
                'not_found' => sprintf(__('No %s found', 'api-volunteer-manager'), __(strtolower($this->namePlural), 'api-volunteer-manager')),
                'not_found_in_trash' => sprintf(__('No %s found in trash', 'api-volunteer-manager'), __(strtolower($this->namePlural), 'api-volunteer-manager')),
                'parent_item_colon' => sprintf(__('Parent %s:', 'api-volunteer-manager'), __(strtolower($this->nameSingular), 'api-volunteer-manager')),
                'menu_name' => __(ucfirst($this->namePlural), 'api-volunteer-manager')
            ),
            $this->labels
        );

        $mergedArgs = array_merge(
            array(
                'labels' => $labels,
                'rewrite' => array(
                    'slug' => $this->slug,
                    'with_front' => false
                ),
            ),
            $this->args
        );

        register_post_type($this->slug, $mergedArgs);
    }

    /**
     * Adds a column to the admin list table
     * @param string        $key              Column key
     * @param string        $title            Column title
     * @param boolean       $sortable         Sortable or not
     * @param callable|null $contentCallback  Callback function for displaying
     *                                        column content (params: $columnKey, $postId)
     * @return void
     */
    public function addTableColumn(string $key, string $title, bool $sortable = false, $contentCallback = null): void
    {
        $this->tableColumns[$key] = $title;

        if ($sortable === true) {
            $this->tableSortableColumns[$key] = $key;
        }

        if ($contentCallback) {
            $this->tableColumnsContentCallback[$key] = $contentCallback;
        }
    }

    /**
     * Set up table columns
     * @param array $columns Default columns
     * @return array          New columns
     */
    public function setTableColumns(array $columns): array
    {
        if (!empty($this->tableColumns)) {
            $columns = array_merge(
                array_splice($columns, 0, 2),
                $this->tableColumns,
                array_splice($columns, 0, count($columns))
            );
        }

        return $columns;
    }

    /**
     * Setup sortable columns
     * @param array $columns Default columns
     * @return array          New columns
     */
    public function tableSortableColumns(array $columns): array
    {
        if (!empty($this->tableSortableColumns)) {
            $columns = $this->tableSortableColumns;
        }

        return unserialize(strtolower(serialize($columns)));
    }

    /**
     * Set table column content with callback functions
     * @param string  $column Key of the column
     * @param integer $postId Post id of the current row in table
     * @return void
     */
    public function tableColumnsContent(string $column, int $postId): void
    {
        if (!isset($this->tableColumnsContentCallback[$column])) {
            return;
        }

        call_user_func_array($this->tableColumnsContentCallback[$column], array($column, $postId));
    }
}
