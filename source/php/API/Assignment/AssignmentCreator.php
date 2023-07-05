<?php

namespace VolunteerManager\API\Assignment;

use VolunteerManager\API\ApiHandler;
use VolunteerManager\API\WPResponseFactory;
use VolunteerManager\Entity\FieldSetter;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class AssignmentCreator extends ApiHandler
{
    /**
     * Creates a new assignment from a given request
     *
     * @param WP_REST_Request $request     The request containing the assignment details.
     * @param FieldSetter     $fieldSetter The field setter used to set assignment fields.
     *
     * @return WP_REST_Response|WP_Error The response of the assignment creation operation.
     */
    public function create(WP_REST_Request $request, FieldSetter $fieldSetter, string $postSlug)
    {
        $assignmentDetails = $this->extractAssignmentDetailsFromRequest($request);
        $assignmentId = $this->createAssignmentPost(
            $assignmentDetails['title'],
            $postSlug
        );

        $fieldSetter->updateFields($assignmentId, $assignmentDetails);
        $fieldSetter->setPostByParam($request, $assignmentId, 'assignment_eligibility');
        $internal_assignment = $request->get_param('internal_assignment') === 'true';
        $fieldSetter->updateField('internal_assignment', $internal_assignment, $assignmentId);
        $fieldSetter->updateField('source', (new ApiHandler())->extractSourceFromRequest($request), $assignmentId);
        $signup_methods = $this->getAssignmentSignupValues($request, $assignmentId);
        $fieldSetter->updateField('signup_methods', $signup_methods, $assignmentId);

        if (!empty($_FILES['featured_media'])) {
            $postMedia = $fieldSetter->savePostMedia('featured_media', $assignmentId, array('image/jpeg', 'image/png'));
            if (is_wp_error($postMedia)) {
                wp_delete_post($assignmentId, true);
                return $postMedia;
            }
        }

        $fieldSetter->setPostStatus($assignmentId, 'pending', 'assignment-status');

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
            'number_of_available_spots',
            'submitted_by_email',
            'submitted_by_first_name',
            'submitted_by_phone',
            'employer_name',
            'employer_contacts',
            'employer_website',
            'employer_about',
            'street_address',
            'postal_code',
            'city',
        ];

        return $this->extractParamsFromRequest($request, $requestParams);
    }

    /**
     * Creates a new assignment post.
     *
     * @param string $title The title of the assignment.
     *
     * @return int The ID of the newly created assignment.
     */
    private function createAssignmentPost(string $title, string $postType): int
    {
        $post = [
            'post_title' => $title,
            'post_type' => $postType,
            'post_status' => 'pending',
            'post_date_gmt' => current_time('mysql', true),
            'post_modified_gmt' => current_time('mysql', true),
        ];

        return wp_insert_post($post);
    }

    /**
     * Get the signup methods from the request.
     *
     * Prefix 'signup_' will be removed from the param name.
     *
     * @param WP_REST_Request $request
     * @param int             $assignment_id
     * @return array
     */
    private function getAssignmentSignupValues(WP_REST_Request $request, int $assignment_id): array
    {
        $signup_methods = get_fields('signup_methods', $assignment_id);
        $signup_methods = is_array($signup_methods) ? $signup_methods : [];

        $methods = ['link', 'email', 'phone'];

        foreach ($methods as $method) {
            $param_value = $request->get_param('signup_' . $method);
            if (empty($param_value)) continue;

            update_field('signup_' . $method, $param_value, $assignment_id);
            $signup_methods[] = $method;
        }

        return $signup_methods;
    }

}
