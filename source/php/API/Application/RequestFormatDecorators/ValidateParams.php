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
        $assignment = $request->get_param('assignment_id');

        if (!$validator->post_exist((int)$assignment)) {
            return WPResponseFactory::wp_error_response(
                'avm_application_validation_error',
                __('Assignment with the given ID does not exist in the database.', AVM_TEXT_DOMAIN),
                [
                    'param' => 'assignment_id',
                    'status' => 404
                ]
            );
        }

        // TODO: Validate if user exists and is approved volunteer

        if (!$validator->is_application_unique($employee->ID, (int)$assignment)) {
            return WPResponseFactory::wp_error_response(
                'avm_application_validation_error',
                __('An application already exists for this user.', AVM_TEXT_DOMAIN),
                ['param' => 'national_identity_number']
            );
        }

        return $request;
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
