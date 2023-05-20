<?php

namespace VolunteerManager\PostType\Employee;

use VolunteerManager\API\ValidateRestRequest;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;
use WP_REST_Request;

/**
 * Validate unique parameters of the REST request
 *
 * @param array $request
 * @return array|WP_Error
 */
class ValidateUniqueParams extends ValidateRestRequest
{
    protected function validator(array $request): array|WP_Error
    {
        $validator = new EmployeeApiValidator();

        // Validate Email
        if (!$validator->is_email_unique($request['email'])) {
            return $this->generateErrorResponse('Email already exists', 'email');
        }

        if (!$validator->is_national_identity_unique($request['national_identity_number'])) {
            return $this->generateErrorResponse('National identity number already exists', 'national_identity_number');
        }

        return $request;
    }

    private function generateErrorResponse(string $message, string $param): WP_Error
    {
        return WPResponseFactory::wp_error_response(
            'avm_employee_registration_error',
            __($message, AVM_TEXT_DOMAIN),
            $param
        );
    }
}
