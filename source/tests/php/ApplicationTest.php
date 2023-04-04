<?php

namespace php;

use PluginTestCase\PluginTestCase;
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
}
