<?php

namespace php\Entity;

use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Entity\Taxonomy;
use Brain\Monkey\Functions;

class TaxonomyTest extends PluginTestCase
{
    protected Taxonomy $taxonomy;

    public function setUp(): void
    {
        parent::setUp();

        $this->taxonomy = new Taxonomy(
            'Registration statuses',
            'Registration status',
            'employee-registration-status',
            array('some-post-type-slug'),
            array (
                'hierarchical' => false,
                'show_ui' => true
            )
        );
    }

    /**
     * @throws ExpectationArgsRequired
     */
    public function testRegisterTaxonomy()
    {
        $taxonomy = new Taxonomy(
            'Registration statuses',
            'Registration status',
            'employee-registration-status',
            array('some-post-type-slug'),
            array (
                'hierarchical' => false,
                'show_ui' => true
            )
        );

        Functions\expect('register_taxonomy')
            ->once()
            ->with(
            'employee-registration-status',
            array('some-post-type-slug'),
            \Mockery::on(function ($args) {
                return isset($args['labels']['singular_name']) && $args['labels']['singular_name'] === 'Registration status'
                    && isset($args['labels']['name']) && $args['labels']['name'] === 'Registration statuses'
                    && isset($args['hierarchical']) && $args['hierarchical'] === false
                    && isset($args['show_ui']) && $args['show_ui'] === true;
            })
        );

        $taxonomy->registerTaxonomy();
    }

    /**
     * @dataProvider termItemsProvider
     */
    public function testInsertTermsSuccessfully(array $term_items)
    {
        Functions\when('taxonomy_exists')
            ->justReturn(true);

        Functions\when('term_exists')
            ->justReturn(false);

        Functions\when('wp_insert_term')
            ->justReturn(['term_id' => 1, 'term_taxonomy_id' => 1]);

        $this->taxonomy->insertTerms($term_items);

        $this->assertIsArray(['term_id' => 1, 'term_taxonomy_id' => 1]);
    }

    public function termItemsProvider(): array
    {
        return [
            [
                [
                    [
                        'name' => 'New',
                        'slug' => 'new',
                        'description' => 'New employee. Employee needs to be processed.'
                    ],
                    [
                        'name' => 'Ongoing',
                        'slug' => 'ongoing',
                        'description' => 'Employee under investigation.'
                    ],
                    [
                        'name' => 'Approved',
                        'slug' => 'approved',
                        'description' => 'Employee approved for assignments.'
                    ],
                    [
                        'name' => 'Denied',
                        'slug' => 'denied',
                        'description' => 'Employee denied. Employee can\'t apply.'
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider termItemsProvider
     */
    public function testInsertTermThatAlreadyExists(array $term_items)
    {
        Functions\when('taxonomy_exists')
            ->justReturn(false);

        $mock_wp_error = \Mockery::mock('WP_Error');

        Functions\when('WP_Error')
            ->justReturn($mock_wp_error);

        $result = $this->taxonomy->insertTerms($term_items);

        $this->assertInstanceOf('WP_Error', $result);
    }

    public function testInsertTermsWithEmptyName()
    {
        Functions\when('taxonomy_exists')
            ->justReturn(true);

        Functions\when('term_exists')
            ->justReturn(false);

        Functions\when('wp_insert_term')
            ->justReturn(['term_id' => 1, 'term_taxonomy_id' => 1]);;

        $term_items = [
            [
                'name' => '',
                'slug' => 'empty-name',
                'description' => 'This term has an empty name.'
            ]
        ];

        $result = $this->taxonomy->insertTerms($term_items);

        $this->assertEmpty($result);
    }

    public function testInsertTermsWithDefaultSlugAndDescription()
    {
        Functions\when('taxonomy_exists')
            ->justReturn(true);

        Functions\when('term_exists')
            ->justReturn(false);

        Functions\when('wp_insert_term')
            ->justReturn(['term_id' => 1, 'term_taxonomy_id' => 1]);

        $taxonomy = new Taxonomy(
            'Registration statuses',
            'Registration status',
            'employee-registration-status',
            array('some-post-type-slug'),
            array (
                'hierarchical' => false,
                'show_ui' => true
            )
        );

        $term_items = [
            [
                'name' => 'Incomplete',
            ]
        ];

        $result = $taxonomy->insertTerms($term_items);

        $this->assertNotEmpty($result);
        $this->assertEquals([['term_id' => 1, 'term_taxonomy_id' => 1]], $result);
    }

    public function testInsertTermsWithProvidedArgs()
    {
        Functions\when('taxonomy_exists')
            ->justReturn(true);

        Functions\when('term_exists')
            ->justReturn(false);

        Functions\when('wp_insert_term')
            ->justReturn(['term_id' => 1, 'term_taxonomy_id' => 1]);

        $taxonomy = new Taxonomy(
            'Registration statuses',
            'Registration status',
            'employee-registration-status',
            array('some-post-type-slug'),
            array (
                'hierarchical' => false,
                'show_ui' => true
            )
        );

        $term_items = [
            [
                'name' => 'Archived',
                'slug' => 'archived',
                'description' => 'Employee archived. No longer active.'
            ]
        ];

        $args = [
            'slug' => 'custom-slug',
            'description' => 'Custom description'
        ];

        $result = $taxonomy->insertTerms($term_items, $args);

        $this->assertNotEmpty($result);
        $this->assertEquals([['term_id' => 1, 'term_taxonomy_id' => 1]], $result);
    }

    /**
     * @dataProvider termWithColorsProvider
     */
    public function testInsertTermsWithColors(array $term_items)
    {
        Functions\when('taxonomy_exists')
            ->justReturn(true);

        Functions\when('term_exists')
            ->justReturn(false);

        Functions\when('wp_insert_term')
            ->justReturn(['term_id' => 1, 'term_taxonomy_id' => 1]);

        Functions\expect('update_field')
            ->once()
            ->with('taxonomy_color', '#00ff00', 'employee-registration-status_1');

        Functions\expect('update_field')
            ->once()
            ->with('taxonomy_color', '#0000ff', 'employee-registration-status_1');

        $this->taxonomy->insertTerms($term_items);
    }

    public function termWithColorsProvider(): array
    {
        return [
            "Mixed color terms" => [
                [
                    [
                        'name' => 'New',
                        'slug' => 'new',
                        'description' => 'New employee. Employee needs to be processed.',
                        'color' => '#00ff00',
                    ],
                    [
                        'name' => 'Ongoing',
                        'slug' => 'ongoing',
                        'description' => 'Employee under investigation.',
                    ],
                    [
                        'name' => 'Approved',
                        'slug' => 'approved',
                        'description' => 'Employee approved for assignments.',
                        'color' => '#0000ff',
                    ],
                    [
                        'name' => 'Denied',
                        'slug' => 'denied',
                        'description' => 'Employee denied. Employee can\'t apply.',
                    ]
                ]
            ]
        ];
    }
}
