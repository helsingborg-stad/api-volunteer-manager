<?php

namespace VolunteerManager;

use VolunteerManager\Entity\PostTypeNew;
use VolunteerManager\Entity\Taxonomy as Taxonomy;
use WP_Error;

class Application extends PostTypeNew
{
    private Taxonomy $applicationTaxonomy;
    public function addHooks(): void
    {
        parent::addHooks();

        add_action('init', [$this, 'initTaxonomiesAndTerms']);
    }

    public function initTaxonomiesAndTerms(): void
    {
        $this->registerStatusTaxonomy();
        $this->insertStatusTerms();
    }

    /**
     * Register status taxonomy
     *
     * @return void
     */
    public function registerStatusTaxonomy(): void
    {
        $this->applicationTaxonomy = new Taxonomy(
            'Application statuses',
            'Application status',
            'application-status',
            array($this->slug),
            array(
                'hierarchical' => false,
                'show_ui' => false
            )
        );

        $this->applicationTaxonomy->registerTaxonomy();
    }

    /**
     * Insert status terms
     *
     * @return array|WP_Error
     */
    public function insertStatusTerms()
    {
        return $this->applicationTaxonomy->insertTerms(ApplicationConfiguration::getStatusTerms());
    }
}
