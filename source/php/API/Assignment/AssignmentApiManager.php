<?php

namespace VolunteerManager\API\Assignment;

use VolunteerManager\API\Auth\AuthenticationDecorator;
use VolunteerManager\API\Auth\AuthenticationInterface;
use VolunteerManager\API\FormatRequest;
use VolunteerManager\API\ValidateRequiredRestParams;
use WP_REST_Request;

class AssignmentApiManager
{
    private AuthenticationInterface $authentication;
    private AssignmentCreator $assignmentCreator;
    private AssignmentFieldSetter $assignmentFieldSetter;

    public function __construct(
        AuthenticationInterface $authentication,
        AssignmentCreator       $assignmentCreator,
        AssignmentFieldSetter   $assignmentFieldSetter
    )
    {
        $this->authentication = $authentication;
        $this->assignmentCreator = $assignmentCreator;
        $this->assignmentFieldSetter = $assignmentFieldSetter;
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
                'callback' => [$this, 'handleAssignmentCreationRequest'],
                'permission_callback' => '__return_true'
            )
        );
    }

    public function handleAssignmentCreationRequest(WP_REST_Request $request)
    {
        $formatRequest = new FormatRequest();
        $requiredParamsValidator = new ValidateRequiredRestParams(
            $formatRequest,
            ['assignment_eligibility']
        );

        $validatedParams = $requiredParamsValidator->formatRestRequest($request);
        if (is_wp_error($validatedParams)) {
            return $validatedParams;
        }

        return $this->assignmentCreator->create($request, $this->assignmentFieldSetter);
    }
}
