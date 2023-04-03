<?php

namespace VolunteerManager\Application;

use VolunteerManager\Entity\Taxonomy as Taxonomy;

class RegisterTaxonomy
{
    /**
     * Register status taxonomy
     *
     * @return Taxonomy
     */
    public function registerStatusTaxonomy(): Taxonomy
    {
        return new Taxonomy(
            'Application statuses',
            'Application status',
            'application-status',
            array($this->slug),
            array(
                'hierarchical' => false,
                'show_ui' => false
            )
        );
    }
}
