<?php

namespace VolunteerManager\API\Application\RequestFormatDecorators;

use VolunteerManager\API\ValidateRestRequest;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;
use WP_REST_Request;
use WP_Post;

/**
 * Validate duplication of the requested creation
 *
 * @param array $request
 * @return array|WP_Error
 */
class ValidateParams extends ValidateRestRequest
{
    protected function validator(WP_REST_Request $request)
    {
        $validator = new ApplicationApiValidator();

        $national_identity_number = $request->get_param('national_identity_number');
        $employee = $this->getEmployeeByIdentityNumber($national_identity_number);

        // TODO: Validate if user is approved

        // TODO: Validate if assignment exists

        $assignment = $request->get_param('assignment');
        if (!$validator->is_application_unique($employee->ID, (int)$assignment)) {
            return $this->generateErrorResponse("Application already exists for this user.", 'national_identity_number');
        }

        return $request;
    }

    private function generateErrorResponse(string $message, string $param): WP_Error
    {
        return WPResponseFactory::wp_error_response(
            'avm_application_creation_error',
            __($message, AVM_TEXT_DOMAIN),
            ['param' => $param]
        );
    }

    /**
     * Retrieve employee by national identity number
     *
     * @param string $nationalIdentityNumber The national identity number of the employee
     * @return null|WP_Post The employee data matching the national identity number
     */
    public function getEmployeeByIdentityNumber(string $nationalIdentityNumber): ?WP_Post
    {
        $employee = get_posts(array(
            'post_type' => 'employee',
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'national_identity_number',
                    'value' => $nationalIdentityNumber,
                    'compare' => '=',
                )
            )
        ));
        return !empty($employee[0]) ? $employee[0] : null;
    }
}
