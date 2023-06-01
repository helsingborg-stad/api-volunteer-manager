<?php

namespace VolunteerManager\API\Employee;

use VolunteerManager\API\WPResponseFactory;
use VolunteerManager\Entity\FieldSetter;
use WP_REST_Request;
use WP_REST_Response;

class EmployeeCreator
{
    public function create(WP_REST_Request $request, FieldSetter $fieldSetter): WP_REST_Response
    {
        $employeeDetails = $this->extractEmployeeDetailsFromRequest($request);
        $employeeId = $this->createEmployeePost($employeeDetails['email']);

        $fieldSetter->updateFields($employeeId, $employeeDetails);
        $fieldSetter->updateField('registration_date', date('Y-m-d'), $employeeId);
        $fieldSetter->setPostStatus($employeeId, 'new', 'employee-registration-status');

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
            'source',
            'newsletter'
        ];

        $params = [];
        foreach ($requestParams as $param) {
            $params[$param] = $request->get_param($param);
        }

        return $params;
    }

    private function createEmployeePost(string $title): int
    {
        $post = [
            'post_title' => $title,
            'post_type' => 'employee',
            'post_status' => 'pending',
            'post_date_gmt' => current_time('mysql', true),
            'post_modified_gmt' => current_time('mysql', true)
        ];

        return wp_insert_post($post);
    }
}
