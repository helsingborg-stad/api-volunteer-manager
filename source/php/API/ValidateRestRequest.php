<?php

namespace VolunteerManager\API;

use WP_Error;
use WP_REST_Request;

/**
 * Base decorator class for validating REST requests
 */
abstract class ValidateRestRequest implements RestFormatInterface
{
    protected RestFormatInterface $rest_format;

    public function __construct(RestFormatInterface $rest_format)
    {
        $this->rest_format = $rest_format;
    }

    /**
     * @inheritDoc
     */
    public function formatRestRequest(array $request): array|WP_Error
    {
        $validation_result = $this->validator($request);
        if (is_wp_error($validation_result)) {
            return $validation_result;
        }

        return $this->rest_format->formatRestRequest($request);
    }

    abstract protected function validator(array $request): array|WP_Error;
}
