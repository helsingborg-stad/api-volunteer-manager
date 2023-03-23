<?php

namespace VolunteerManager;

use VolunteerManager\Entity\PostTypeNew;
use VolunteerManager\Entity\Taxonomy as Taxonomy;

class Application extends PostTypeNew
{
    public function addHooks(): void
    {
        parent::addHooks();

        add_action('init', [$this, 'registerStatusTaxonomy']);
    }

    /**
     * Register status taxonomy
     *
     * @return void
     */
    public function registerStatusTaxonomy(): void
    {
        $statusTaxonomy = new Taxonomy(
            'Application statuses',
            'Application status',
            'application-status',
            array($this->slug),
            array(
                'hierarchical' => false,
                'show_ui' => false
            )
        );

        $statusTaxonomy->registerTaxonomy();
    }
}