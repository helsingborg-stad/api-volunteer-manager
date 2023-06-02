<?php

namespace VolunteerManager\API\Assignment;

use VolunteerManager\API\Assignment\RequestFormatDecorators\SanitizeAssignmentParams;
use VolunteerManager\API\FormatRequest;
use VolunteerManager\API\ValidateRequiredRestParams;
use VolunteerManager\Entity\FieldSetter;
use WP_REST_Request;

class AssignmentApiManager
{
    private string $assignmentPostSlug;
    private AssignmentCreator $assignmentCreator;
    private FieldSetter $assignmentFieldSetter;

    public function __construct(
        AssignmentCreator $assignmentCreator,
        FieldSetter       $assignmentFieldSetter,
        string            $assignmentPostSlug
    )
    {
        $this->assignmentCreator = $assignmentCreator;
        $this->assignmentFieldSetter = $assignmentFieldSetter;

        $this->assignmentPostSlug = $assignmentPostSlug;
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
        $sanitizeParams = new SanitizeAssignmentParams($formatRequest);
        $requiredParamsValidator = new ValidateRequiredRestParams(
            $sanitizeParams,
            ['assignment_eligibility']
        );

        $validatedParams = $requiredParamsValidator->formatRestRequest($request);
        if (is_wp_error($validatedParams)) {
            return $validatedParams;
        }

        return $this->assignmentCreator->create(
            $request,
            $this->assignmentFieldSetter,
            $this->assignmentPostSlug
        );
    }
}
