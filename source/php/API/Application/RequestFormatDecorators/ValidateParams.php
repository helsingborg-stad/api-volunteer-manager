<?php

namespace VolunteerManager\API\Application\RequestFormatDecorators;

use VolunteerManager\API\ValidateRestRequest;
use VolunteerManager\API\WPResponseFactory;
use VolunteerManager\Helper\Admin\EmployeeHelper;
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
        $employee = EmployeeHelper::getEmployeeByIdentityNumber($national_identity_number);
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

        if (!isset($employee->ID) || !$validator->post_exist($employee->ID)) {
            return WPResponseFactory::wp_error_response(
                'avm_application_validation_error',
                __('User with the given ID does not exist in the database.', AVM_TEXT_DOMAIN),
                ['status' => 404]
            );
        }

        if (!$validator->is_employee_approved($employee->ID)) {
            return WPResponseFactory::wp_error_response(
                'avm_application_validation_error',
                __('User has no permissions to create applications.', AVM_TEXT_DOMAIN),
                ['status' => 403]
            );
        }

        if (!$validator->is_application_unique($employee->ID, (int)$assignment)) {
            return WPResponseFactory::wp_error_response(
                'avm_application_validation_error',
                __('An application already exists for this user.', AVM_TEXT_DOMAIN),
                ['status' => 400]
            );
        }

        return $request;
    }
}
