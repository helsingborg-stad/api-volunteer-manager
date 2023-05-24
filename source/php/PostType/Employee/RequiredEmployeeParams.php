<?php

namespace VolunteerManager\PostType\Employee;

use VolunteerManager\API\RestFormatInterface;
use VolunteerManager\API\ValidateRestRequest;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;
use WP_REST_Request;

class RequiredEmployeeParams extends ValidateRestRequest
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
            'avm_employee_param_error',
            __('Missing required parameter', AVM_TEXT_DOMAIN),
            $param
        );
    }
}
