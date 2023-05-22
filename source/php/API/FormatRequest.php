<?php

namespace VolunteerManager\API;

use WP_REST_Request;

class FormatRequest implements RestFormatInterface
{
    /**
     * @inheritDoc
     */
    public function formatRestRequest(WP_REST_Request $request)
    {
        return $request;
    }
}
