<?php

namespace VolunteerManager\API\Application;

use VolunteerManager\API\Application\RequestFormatDecorators\ValidateParams;
use VolunteerManager\API\Auth\AuthenticationDecorator;
use VolunteerManager\API\Auth\AuthenticationInterface;
use VolunteerManager\API\FormatRequest;
use VolunteerManager\API\ValidateRequiredRestParams;
use VolunteerManager\Entity\FieldSetter;
use WP_REST_Request;

class ApplicationApiManager
{
    private AuthenticationInterface $authentication;
    private ApplicationCreator $applicationCreator;
    private FieldSetter $applicationFieldSetter;
    private string $applicationPostSlug;

    public function __construct(
        AuthenticationInterface $authentication,
        ApplicationCreator      $applicationCreator,
        FieldSetter             $applicationFieldSetter,
        string                  $applicationPostSlug
    )
    {
        $this->authentication = $authentication;
        $this->applicationCreator = $applicationCreator;
        $this->applicationFieldSetter = $applicationFieldSetter;
        $this->applicationPostSlug = $applicationPostSlug;
    }

    public function addHooks()
    {
        add_action('rest_api_init', array($this, 'registerPostEndpoint'));
    }

    public function registerPostEndpoint()
    {
        register_rest_route(
            'wp/v2',
            'application',
            array(
                'methods' => 'POST',
                'callback' => new AuthenticationDecorator(
                    [$this, 'handleApplicationCreationRequest'],
                    $this->authentication
                ),
                'permission_callback' => '__return_true'
            )
        );
    }

    public function handleApplicationCreationRequest(WP_REST_Request $request)
    {
        $format_request = new FormatRequest();
        $unique_params = new ValidateParams($format_request);
        $required_params = new ValidateRequiredRestParams(
            $unique_params,
            ['national_identity_number', 'assignment_id']
        );

        $validated_params = $required_params->formatRestRequest($request);
        if (is_wp_error($validated_params)) {
            return $validated_params;
        }

        return $this->applicationCreator->create(
            $request,
            $this->applicationFieldSetter,
            $this->applicationPostSlug
        );
    }
}
