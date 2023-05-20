<?php

namespace VolunteerManager\API;

use WP_Error;
use WP_REST_Request;

class FormatRequest implements RestFormatInterface
{
    /**
     * @inheritDoc
     */
    public function formatRestRequest(WP_REST_Request $request): WP_REST_Request|WP_Error
    {
        return $request;
    }
}
