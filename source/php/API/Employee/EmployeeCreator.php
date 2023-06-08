<?php

namespace VolunteerManager\API\Employee;

use VolunteerManager\API\ApiHandler;
use VolunteerManager\API\WPResponseFactory;
use VolunteerManager\Entity\FieldSetter;
use WP_REST_Request;
use WP_REST_Response;

class EmployeeCreator extends ApiHandler
{
    public function create(WP_REST_Request $request, FieldSetter $fieldSetter, string $postSlug): WP_REST_Response
    {
        $employeeDetails = $this->extractEmployeeDetailsFromRequest($request);
        $employeeId = $this->createEmployeePost(
            $employeeDetails['email'],
            $postSlug
        );

        $fieldSetter->updateFields($employeeId, $employeeDetails);
        $fieldSetter->updateField('registration_date', date('Y-m-d'), $employeeId);
        $fieldSetter->setPostStatus($employeeId, 'new', 'employee-registration-status');
        $fieldSetter->updateField('source', $request->get_header('host'), $employeeId);

        return WPResponseFactory::wp_rest_response(
            'Employee created',
            ['employee_id' => $employeeId]
        );
    }

    private function extractEmployeeDetailsFromRequest(WP_REST_Request $request): array
    {
        $requestParams = [
            'email',
            'first_name',
            'surname',
            'national_identity_number',
            'phone_number',
            'newsletter'
        ];

        return $this->extractParamsFromRequest($request, $requestParams);
    }

    private function createEmployeePost(string $title, string $postType): int
    {
        $post = [
            'post_title' => $title,
            'post_type' => $postType,
            'post_status' => 'pending',
            'post_date_gmt' => current_time('mysql', true),
            'post_modified_gmt' => current_time('mysql', true)
        ];

        return wp_insert_post($post);
    }
}
