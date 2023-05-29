<?php

namespace VolunteerManager\API\Assignment;

use VolunteerManager\API\Auth\AuthenticationDecorator;
use VolunteerManager\API\Auth\AuthenticationInterface;
use VolunteerManager\API\FormatRequest;
use VolunteerManager\API\ValidateRequiredRestParams;
use VolunteerManager\API\WPResponseFactory;
use WP_REST_Request;
use WP_REST_Response;

class AssignmentApiManager
{
    private AuthenticationInterface $authentication;

    public function __construct(AuthenticationInterface $authentication)
    {
        $this->authentication = $authentication;
    }

    public function addHooks()
    {
        add_action('rest_api_init', array($this, 'registerPostEndpoint'));
    }

    public function registerPostEndpoint()
    {
        register_rest_route(
            'wp/v2',
            'assignment',
            array(
                'methods' => 'POST',
                'callback' => new AuthenticationDecorator([$this, 'handlePostRequest'], $this->authentication),
                'permission_callback' => '__return_true'
            )
        );
    }

    public function handlePostRequest(WP_REST_Request $request)
    {
        $format_request = new FormatRequest();
        $required_params = new ValidateRequiredRestParams(
            $format_request,
            ['assignment_eligibility']
        );

        $validated_params = $required_params->formatRestRequest($request);
        if (is_wp_error($validated_params)) {
            return $validated_params;
        }

        return $this->registerAssignment($request);
    }

    public function registerAssignment(WP_REST_Request $request): WP_REST_Response
    {
        $request_params = [
            'title',
            'assignment_eligibility',
            'description',
            'qualifications',
            'schedule',
            'benefits',
            'number_of_available_spots'
        ];

        $params = [];
        foreach ($request_params as $param) {
            $params[$param] = $request->get_param($param);
        }

        $assignment_id = wp_insert_post(
            [
                'post_title' => $params['title'],
                'post_type' => 'assignment',
                'post_status' => 'pending',
                'post_date_gmt' => current_time('mysql', true),
                'post_modified_gmt' => current_time('mysql', true),
            ]
        );

        $assignment_status_term = get_term_by('slug', 'pending', 'assignment-status');
        if ($assignment_status_term) {
            wp_set_post_terms($assignment_id, [$assignment_status_term->term_id], 'assignment-status');
        }

        $optional_response_params = ['assignment_id' => $assignment_id];
        return WPResponseFactory::wp_rest_response(
            'Assignment created',
            $optional_response_params
        );
    }
}
