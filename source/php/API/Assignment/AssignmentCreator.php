<?php

namespace VolunteerManager\API\Assignment;

use VolunteerManager\API\WPResponseFactory;
use WP_REST_Request;
use WP_REST_Response;

class AssignmentCreator
{
    public function create(WP_REST_Request $request, AssignmentFieldSetter $fieldSetter): WP_REST_Response
    {
        $assignmentDetails = $this->extractAssignmentDetailsFromRequest($request);
        $assignmentId = $this->createAssignmentPost($assignmentDetails['title']);

        $fieldSetter->updateAssignmentFields($assignmentId, $assignmentDetails);
        $fieldSetter->setAssignmentStatus($assignmentId);
        $fieldSetter->setAssignmentEligibility($request, $assignmentId);
        $fieldSetter->setAssignmentSource($request, $assignmentId);
        $fieldSetter->setAssignmentSignupValues($request, $assignmentId);

        return WPResponseFactory::wp_rest_response(
            'Assignment created',
            ['assignment_id' => $assignmentId]
        );
    }

    private function extractAssignmentDetailsFromRequest(WP_REST_Request $request): array
    {
        $requestParams = [
            'title',
            'description',
            'qualifications',
            'schedule',
            'benefits',
            'number_of_available_spots'
        ];

        $params = [];
        foreach ($requestParams as $param) {
            $params[$param] = $request->get_param($param);
        }

        return $params;
    }

    private function createAssignmentPost(string $title): int
    {
        return wp_insert_post(
            [
                'post_title' => $title,
                'post_type' => 'assignment',
                'post_status' => 'pending',
                'post_date_gmt' => current_time('mysql', true),
                'post_modified_gmt' => current_time('mysql', true),
            ]
        );
    }
}
