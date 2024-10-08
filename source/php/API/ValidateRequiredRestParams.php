<?php

namespace VolunteerManager\API;

use WP_Error;
use WP_REST_Request;

class ValidateRequiredRestParams extends ValidateRestRequest
{
    private array $required_request_keys;

    public function __construct(RestFormatInterface $rest_format, array $required_request_keys)
    {
        parent::__construct($rest_format);
        $this->required_request_keys = $required_request_keys;
    }

    protected function validator(WP_REST_Request $request)
    {
        foreach ($this->required_request_keys as $key) {
            if (empty($request[$key])) {
                return $this->generateErrorResponse($key);
            }
        }

        return $request;
    }

    private function generateErrorResponse(string $param): WP_Error
    {
        return WPResponseFactory::wp_error_response(
            'avm_param_error',
            __('Missing required parameter', 'api-volunteer-manager'),
            ['param' => $param]
        );
    }
}
