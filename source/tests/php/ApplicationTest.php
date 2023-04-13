<?php

namespace php;

use PluginTestCase\PluginTestCase;
use Brain\Monkey\Functions;
use VolunteerManager\Application;
use VolunteerManager\Entity\Taxonomy;

class ApplicationTest extends PluginTestCase
{
    private Application $application;

    public function setUp(): void
    {
        parent::setUp();

        $mockApplicationArgs =
            [
                'slug' => 'application',
                'namePlural' => 'applications',
                'nameSingular' => 'application',
            ];

        $this->application = new Application(...$mockApplicationArgs);
    }

    public function testAddHooks()
    {
        $this->application->addHooks();
        self::assertNotFalse(has_action('init', [$this->application, 'initTaxonomiesAndTerms']));
        self::assertNotFalse(has_action('init', [$this->application, 'addStatusTableColumn']));
    }

    public function testInsertStatusTerms()
    {
        // Set up the mock for Taxonomy object
        $taxonomyMock = $this->getMockBuilder(Taxonomy::class)
            ->disableOriginalConstructor()
            ->getMock();

        $insertedTerms = [
            [
                'term_id' => 1,
                'term_taxonomy_id' => 1
            ],
            [
                'term_id' => 2,
                'term_taxonomy_id' => 2
            ]
        ];

        // Set up the method insertTerms() for the mock Taxonomy object
        // insertTerms() method shall return an array with term_id and term_taxonomy_id.
        $taxonomyMock->expects($this->once())
            ->method('insertTerms')
            ->willReturn($insertedTerms);


        // Test insertStatusTerms() method
        $result = $this->application->insertStatusTerms($taxonomyMock);
        $this->assertEquals($insertedTerms, $result);
    }

    public function testAddTableColumn()
    {
        $this->application->addStatusTableColumn();
        $actual = $this->application->tableColumns;
        $this->assertEquals(['status' => 'Status'], $actual);
    }


    /**
     * @throws \Brain\Monkey\Expectation\Exception\ExpectationArgsRequired
     */
    public function testSetApplicationPostTitle()
    {
        $post = (object)['ID' => 99];
        Functions\when('get_post_type')->justReturn("application");
        Functions\expect('get_field')
            ->twice()
            ->andReturn((object)['post_title' => 'Foo Bar'], (object)['post_title' => 'Assignment']);
        Functions\expect('wp_update_post')->once()->with(['post_title' => 'Foo Bar - Assignment', 'ID' => $post->ID]);
        $this->application->setApplicationPostTitle($post->ID);
    }
}
