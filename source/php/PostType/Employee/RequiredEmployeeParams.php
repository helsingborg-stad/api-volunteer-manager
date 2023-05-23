<?php

namespace VolunteerManager\PostType\Employee;

use VolunteerManager\API\ValidateRestRequest;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;
use WP_REST_Request;

class RequiredEmployeeParams extends ValidateRestRequest
{
    protected function validator(WP_REST_Request $request)
    {
        $required_request_keys = [
            'email',
            'first_name',
            'surname',
            'national_identity_number',
        ];

        foreach ($required_request_keys as $key) {
            if (empty($request[$key])) {
                return $this->generateErrorResponse($key);
            }
        }

        return $request;
    }

    private function generateErrorResponse(string $param): WP_Error
    {
        return WPResponseFactory::wp_error_response(
            'avm_employee_registration_error',
            __('Missing required parameter', AVM_TEXT_DOMAIN),
            $param
        );
    }
}
