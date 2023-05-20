<?php

namespace VolunteerManager\PostType\Employee;

use VolunteerManager\API\ValidateRestRequest;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;
use WP_REST_Request;

/**
 * Validate unique parameters of the REST request
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Request|WP_Error
 */
class ValidateUniqueParams extends ValidateRestRequest
{
    protected function validator(WP_REST_Request $request): WP_REST_Request|WP_Error
    {
        $validator = new EmployeeApiValidator();

        // Validate Email
        $email = $request->get_param('email');
        if (!$validator->is_email_unique($email)) {
            return $this->generateErrorResponse('Email already exists', 'email');
        }

        // Validate National Identity Number
        $nationalIdentityNumber = $request->get_param('national_identity_number');
        if (!$validator->is_national_identity_unique($nationalIdentityNumber)) {
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
