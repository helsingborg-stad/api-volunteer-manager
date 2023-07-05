<?php

namespace VolunteerManager\API\Application;

use VolunteerManager\API\ApiHandler;
use VolunteerManager\API\WPResponseFactory;
use VolunteerManager\Entity\FieldSetter;
use VolunteerManager\Helper\Admin\EmployeeHelper;
use WP_REST_Request;
use WP_REST_Response;

class ApplicationCreator extends ApiHandler
{
    public function create(WP_REST_Request $request, FieldSetter $fieldSetter, string $postSlug): WP_REST_Response
    {
        $applicationDetails = $this->extractDetailsFromRequest($request);
        $applicationId = $this->createApplicationPost(
            $postSlug,
        );

        $employee = EmployeeHelper::getEmployeeByIdentityNumber($applicationDetails['national_identity_number']);
        $fieldSetter->updateField('application_employee', $employee->ID ?? null, $applicationId);
        $fieldSetter->updateField('application_assignment', (int)$applicationDetails['assignment_id'], $applicationId);
        $fieldSetter->updateField('source', (new ApiHandler())->extractSourceFromRequest($request), $applicationId);
        $fieldSetter->setPostStatus($applicationId, 'pending', 'application-status');

        do_action('acf/save_post', $applicationId);

        return WPResponseFactory::wp_rest_response(
            'Application created',
            ['application_id' => $applicationId]
        );
    }

    private function extractDetailsFromRequest(WP_REST_Request $request): array
    {
        $requestParams = [
            'national_identity_number',
            'assignment_id',
        ];

        return $this->extractParamsFromRequest($request, $requestParams);
    }

    private function createApplicationPost(string $postType): int
    {
        $post = [
            'post_type' => $postType,
            'post_status' => 'pending',
            'post_date_gmt' => current_time('mysql', true),
            'post_modified_gmt' => current_time('mysql', true)
        ];

        return wp_insert_post($post);
    }
}
