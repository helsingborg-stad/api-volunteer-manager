<?php

namespace php\Entity;

use Exception;
use PhpParser\Node\Expr\FuncCall;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Entity\Taxonomy;
use Brain\Monkey\Functions;

class TaxonomyTest extends PluginTestCase
{
    protected Taxonomy $taxonomy;
    protected array $term_items;

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

        $this->term_items = [
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
        ];
    }

    public function testInsertTermsSuccessfully()
    {
        Functions\when('taxonomy_exists')
            ->justReturn(true);

        Functions\when('term_exists')
            ->justReturn(false);

        Functions\when('wp_insert_term')
            ->justReturn(['term_id' => 1, 'term_taxonomy_id' => 1]);



        $this->taxonomy->insertTerms($this->term_items);

        $this->assertIsArray(['term_id' => 1, 'term_taxonomy_id' => 1]);
    }

    public function testInsertTermThatAlreadyExists()
    {
        Functions\when('taxonomy_exists')
            ->justReturn(false);

        $mock_wp_error = \Mockery::mock('WP_Error');

        Functions\when('WP_Error')
            ->justReturn($mock_wp_error);

        $result = $this->taxonomy->insertTerms($this->term_items);

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

}
