<?php

namespace VolunteerManager\API\Assignment;

use VolunteerManager\API\WPResponseFactory;
use WP_REST_Request;
use WP_REST_Response;

class AssignmentCreator
{
    /**
     * Creates a new assignment from a given request
     *
     * @param WP_REST_Request $request The request containing the assignment details.
     * @param AssignmentFieldSetter $fieldSetter The field setter used to set assignment fields.
     *
     * @return WP_REST_Response The response of the assignment creation operation.
     */
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

    /**
     * Extracts assignment details from the request.
     *
     * @param WP_REST_Request $request The request containing the assignment details.
     *
     * @return array An array containing the assignment details.
     */
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

    /**
     * Creates a new assignment post.
     *
     * @param string $title The title of the assignment.
     *
     * @return int The ID of the newly created assignment.
     */
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
