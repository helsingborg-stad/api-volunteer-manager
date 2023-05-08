<?php

namespace php\PostType\Application;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Entity\Taxonomy;
use VolunteerManager\PostType\Application\Application;

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
        self::assertNotFalse(has_action('acf/save_post', [$this->application, 'setApplicationPostTitle']));
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
        $this->assertEquals(['status' => 'Status', 'submitted_from' => 'Submitted from'], $actual);
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

    public function testRegisterEligibilityMetaBoxWithEmptyResult()
    {
        $post = (object)['ID' => 99];
        Functions\expect('get_field')->times(2)->andReturn(null, null);
        Functions\expect('add_meta_box')->never();
        $this->application->registerEligibilityMetaBox('', $post);
    }

    public function testRegisterEligibilityMetaBoxWithExistingResult()
    {
        $post = (object)['ID' => 1];
        Functions\expect('get_field')->times(2)->andReturn(
            (object)['ID' => 2],
            (object)['ID' => 3],
        );
        Functions\expect('add_meta_box')->once();
        $this->application->registerEligibilityMetaBox('', $post);
    }

    public function testRenderEligibilityMetaBox()
    {
        $post = (object)['ID' => 1];
        $args = ['args' => [
            'employee' => (object)['ID' => 2, 'post_title' => 'Foo'],
            'assignment' => (object)['ID' => 3, 'post_title' => 'Bar'],
        ]];
        Functions\expect('get_field')->once()->andReturn(true);
        Functions\expect('get_the_terms')->once()->andReturn([(object)['slug' => '1'], (object)['slug' => '1']]);
        Functions\expect('get_edit_post_link')->times(2)->andReturn('https//:test.se/1/edit', 'https//:test.se/2/edit');

        ob_start();
        $this->application->renderEligibilityMetaBox($post, $args);
        $output = ob_get_clean();
        
        $stringsToCheck = ['https//:test.se/1/edit', 'https//:test.se/2/edit', 'Foo', 'Bar', 'Level 2', 'Level 1'];
        foreach ($stringsToCheck as $string) {
            $this->assertStringContainsString($string, $output);
        }
    }
}
