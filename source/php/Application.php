<?php

namespace VolunteerManager;

use VolunteerManager\Entity\ITerm;
use VolunteerManager\Entity\PostType;
use VolunteerManager\Entity\Taxonomy as Taxonomy;
use VolunteerManager\Helper\Admin\UI as AdminUI;

class Application extends PostType
{
    private Taxonomy $applicationTaxonomy;

    public function addHooks(): void
    {
        parent::addHooks();

        add_action('init', [$this, 'initTaxonomiesAndTerms']);
        add_action('init', [$this, 'addStatusTableColumn']);
    }

    public function initTaxonomiesAndTerms(): void
    {
        $this->registerStatusTaxonomy();
        $this->insertStatusTerms($this->applicationTaxonomy);
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
    public function insertStatusTerms(ITerm $taxonomy)
    {
        return $taxonomy->insertTerms(ApplicationConfiguration::getStatusTerms());
    }

    /**
     * Adds a column with status
     * @return void
     */
    public function addStatusTableColumn(): void
    {
        $this->addTableColumn(
            'status',
            __('Status', AVM_TEXT_DOMAIN),
            true,
            function ($column, $postId) {
                echo AdminUI::createTaxonomyPills(
                    get_the_terms(
                        $postId,
                        'application-status'
                    )
                );
            }
        );
    }
}
