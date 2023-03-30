<?php

namespace php;

use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Application;
use PHPUnit\Framework\TestCase;
use VolunteerManager\ApplicationConfiguration;
use Brain\Monkey\Functions;

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
            'args' => [
                'description' => 'Applications',
                'public' => false,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'exclude_from_search' => true,
                'supports' => false,
            ]
        ];

        $this->application = new Application(...$mockApplicationArgs);

    }

    /**
     * @throws ExpectationArgsRequired
     */
    public function testInsertStatusTerms()
    {
        Functions\expect('')

        Functions\expect('insertTerms')
            ->once()
            ->with(ApplicationConfiguration::getStatusTerms())
            ->andReturn(true);

        $this->application->registerStatusTaxonomy();
        $insertTermsResult = $this->application->insertStatusTerms();

        $this->assertTrue($insertTermsResult);

    }
}
