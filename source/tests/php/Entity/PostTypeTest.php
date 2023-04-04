<?php

namespace php\Entity;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Entity\PostType;

class PostTypeTest extends PluginTestCase
{
    public function postTypeProvider(): array
    {
        return [
            [
                [
                    'slug' => 'example',
                    'namePlural' => 'examples',
                    'nameSingular' => 'example',
                    'args' => [
                        'label' => 'Custom label',
                        'public' => false,
                        'menu_position' => 20,
                    ],
                ],
                [
                    'public' => false,
                    'menu_position' => 20,
                    'rewrite' => [
                        'slug' => 'example',
                        'with_front' => false
                    ],
                    'label' => 'Custom label',
                    'labels' => [
                        'name' => 'Examples',
                        'singular_name' => 'Example',
                        'add_new' => 'Add new example',
                        'add_new_item' => 'Add new example',
                        'edit_item' => 'Edit example',
                        'new_item' => 'New example',
                        'view_item' => 'View example',
                        'search_items' => 'Search examples',
                        'not_found' => 'No examples found',
                        'not_found_in_trash' => 'No examples found in trash',
                        'parent_item_colon' => 'Parent example:',
                        'menu_name' => 'Examples',
                    ],
                ]
            ]
        ];
    }

    /**
     * @dataProvider postTypeProvider
     */
    public function testAddHooks($args)
    {
        $customPostType = new PostType(...$args);
        $customPostType->addHooks();
        self::assertNotFalse(has_action('init', [$customPostType, 'registerPostType']));
        self::assertNotFalse(has_action('manage_' . $customPostType->slug . '_posts_custom_column', [$customPostType, 'tableColumnsContent']));
        self::assertNotFalse(has_filter('manage_edit-' . $customPostType->slug . '_columns', [$customPostType, 'setTableColumns']));
        self::assertNotFalse(has_filter('manage_edit-' . $customPostType->slug . '_sortable_columns', [$customPostType, 'tableSortableColumns']));
    }

    /**
     * @dataProvider postTypeProvider
     * @throws \Brain\Monkey\Expectation\Exception\ExpectationArgsRequired
     */
    public function testRegisterPostType($args, $expectedArgs)
    {
        $customPostType = new PostType(...$args);
        Functions\expect('register_post_type')
            ->once()
            ->with($customPostType->slug, $expectedArgs);
        $customPostType->registerPostType();
    }

    /**
     * @dataProvider postTypeProvider
     */
    public function testAddTableColumn($args)
    {
        $customPostType = new PostType(...$args);
        $customPostType->addTableColumn('foo', 'Foo', true, fn() => "callback");
        $this->assertEquals(['foo' => 'Foo'], $customPostType->tableColumns);
    }

    /**
     * @dataProvider postTypeProvider
     */
    public function testAddTableColumnCallback($args)
    {
        $customPostType = new PostType(...$args);
        $customPostType->addTableColumn('foo', 'Foo', true, fn() => "some callback");
        $this->assertEquals(['foo' => fn() => "some callback"], $customPostType->tableColumnsContentCallback);
    }

    /**
     * @dataProvider postTypeProvider
     */
    public function testSetTableColumns($args)
    {
        $customPostType = new PostType(...$args);
        $customPostType->tableColumns = [
            'column1' => 'Column 1',
        ];
        $result = $customPostType->setTableColumns(['column2' => 'Column 2']);
        $this->assertEquals(
            ['column1' => 'Column 1', 'column2' => 'Column 2'],
            $result
        );
    }

    /**
     * @dataProvider postTypeProvider
     */
    public function testTableSortableColumns($args)
    {
        $customPostType = new PostType(...$args);
        $customPostType->tableColumns = [
            'column1' => 'Column 1',
            'column2' => 'Column 2',
        ];
        $customPostType->tableSortableColumns = [
            'column2'
        ];
        $result = $customPostType->tableSortableColumns(['column5' => 'Column 5']);
        $this->assertEquals(['column2'], $result);
    }

    /**
     * @dataProvider postTypeProvider
     */
    public function testTableColumnsContent($args)
    {
        $customPostType = new PostType(...$args);

        $columnKey = 'column1';
        $postId = 123;

        $callback = function ($column, $id) {
            echo "Column: {$column}, post ID: {$id}";
        };

        $customPostType->tableColumnsContentCallback = [$columnKey => $callback];

        ob_start();
        $customPostType->tableColumnsContent($columnKey, $postId);
        $output = ob_get_clean();

        $expectedOutput = "Column: {$columnKey}, post ID: {$postId}";
        $this->assertEquals($expectedOutput, $output);
    }
}
