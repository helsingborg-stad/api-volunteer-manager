<?php

namespace VolunteerManager\API\Employee\RequestFormatDecorators;

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
    protected function validator(WP_REST_Request $request)
    {
        $validator = new EmployeeApiValidator();

        $email = $request->get_param('email');
        // Validate Email
        if (!$validator->is_email_unique($email)) {
            return $this->generateErrorResponse('Email already exists', 'email');
        }

        $national_identity_number = $request->get_param('national_identity_number');
        if (!$validator->is_national_identity_unique($national_identity_number)) {
            return $this->generateErrorResponse('National identity number already exists', 'national_identity_number');
        }

        return $request;
    }

    private function generateErrorResponse(string $message, string $param): WP_Error
    {
        return WPResponseFactory::wp_error_response(
            'avm_employee_registration_error',
            __($message, 'api-volunteer-manager'),
            ['param' => $param]
        );
    }
}
