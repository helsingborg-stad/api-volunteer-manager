<?php

namespace php;

use PluginTestCase\PluginTestCase;
use Brain\Monkey\Functions;
use VolunteerManager\Entity\PostTypeNew;

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
        $customPostType = new PostTypeNew(...$args);
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
        $customPostType = new PostTypeNew(...$args);
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
        $customPostType = new PostTypeNew(...$args);
        $result = $customPostType->addTableColumn('foo', 'Foo', false, fn() => "foo");
        $this->assertTrue($result);
    }

    /**
     * @dataProvider postTypeProvider
     */
    public function testSetTableColumns($args)
    {
        $customPostType = new PostTypeNew(...$args);
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
        $customPostType = new PostTypeNew(...$args);
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
     * @throws \Brain\Monkey\Expectation\Exception\ExpectationArgsRequired
     */
    public function testTableColumnsContent($args)
    {
        $customPostType = new PostTypeNew(...$args);

        $mockCallback = function (string $column, int $postId) {
            $this->assertEquals('column1', $column);
            $this->assertEquals(123, $postId);
            echo $postId;
        };

        $customPostType->tableColumnsContentCallback['column1'] = $mockCallback;

        Functions\expect('call_user_func_array')
            ->once()
            ->with($mockCallback, ['column1', 123]);

        $customPostType->tableColumnsContent('column1', 123);
    }

}
