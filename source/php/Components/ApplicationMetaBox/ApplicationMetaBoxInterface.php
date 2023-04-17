<?php

namespace VolunteerManager\Components\ApplicationMetaBox;

interface ApplicationMetaBoxInterface
{
    public function register(): void;

    public function getApplications(): array;

    public function render(array $posts): void;
}