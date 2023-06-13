<?php

namespace VolunteerManager\API\Application;

use VolunteerManager\API\ApiHandler;
use VolunteerManager\API\WPResponseFactory;
use VolunteerManager\Entity\FieldSetter;
use WP_REST_Request;
use WP_REST_Response;
use WP_Post;

class ApplicationCreator extends ApiHandler
{
    public function create(WP_REST_Request $request, FieldSetter $fieldSetter, string $postSlug): WP_REST_Response
    {
        $applicationDetails = $this->extractDetailsFromRequest($request);
        $applicationId = $this->createApplicationPost(
            $postSlug,
        );

        $employee = $this->getEmployeeByIdentityNumber($applicationDetails['national_identity_number']);
        $fieldSetter->updateField('application_employee', $employee->ID ?? null, $applicationId);
        $fieldSetter->updateField('application_assignment', (int)$applicationDetails['assignment'], $applicationId);
        $fieldSetter->updateField('source', $request->get_header('host'), $applicationId);
        $fieldSetter->setPostStatus($applicationId, 'pending', 'application-status');

        return WPResponseFactory::wp_rest_response(
            'Application created',
            ['application_id' => $applicationId]
        );
    }

    private function extractDetailsFromRequest(WP_REST_Request $request): array
    {
        $requestParams = [
            'national_identity_number',
            'assignment',
        ];

        return $this->extractParamsFromRequest($request, $requestParams);
    }

    private function createApplicationPost(string $postType): int
    {
        $post = [
            //'post_title' => $title, TODO: add post title or trigger acd/save_posts
            'post_type' => $postType,
            'post_status' => 'pending',
            'post_date_gmt' => current_time('mysql', true),
            'post_modified_gmt' => current_time('mysql', true)
        ];

        return wp_insert_post($post);
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
