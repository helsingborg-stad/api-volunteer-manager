<?php

namespace VolunteerManager\PostType\Assignment;

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

        return $this->registerAssignment();
    }

    public function registerAssignment(): WP_REST_Response
    {
        return WPResponseFactory::wp_rest_response(
            'Assignment created',
            200
        );
    }
}
