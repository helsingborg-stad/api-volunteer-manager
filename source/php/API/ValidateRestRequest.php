<?php

namespace VolunteerManager\API;

use WP_Error;
use WP_REST_Request;

/**
 * Base decorator class for validating REST requests
 */
class ValidateRestRequest implements RestFormatInterface
{
    protected RestFormatInterface $rest_format;

    public function __construct(RestFormatInterface $rest_format)
    {
        $this->rest_format = $rest_format;
    }

    /**
     * @inheritDoc
     */
    public function formatRestRequest(WP_REST_Request $request): WP_REST_Request|WP_Error
    {
        return $this->rest_format->formatRestRequest($request);
    }
}
