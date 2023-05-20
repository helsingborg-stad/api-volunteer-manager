<?php

namespace VolunteerManager\API;

use WP_Error;
use WP_REST_Request;

class FormatRequest implements RestFormatInterface
{
    /**
     * @inheritDoc
     */
    public function formatRestRequest(array $request): array|WP_Error
    {
        return $request;
    }
}
