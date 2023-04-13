<?php

namespace VolunteerManager\Components\ApplicationMetaBox;

interface ApplicationMetaBoxInterface
{
    public function register(): void;

    public function getApplications($postId): array;

    public function render(object $post, array $args): void;
}