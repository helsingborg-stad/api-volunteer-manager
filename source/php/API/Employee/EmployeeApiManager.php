<?php

namespace VolunteerManager\API\Employee;

use VolunteerManager\API\Auth\AuthenticationDecorator;
use VolunteerManager\API\Auth\AuthenticationInterface;
use VolunteerManager\API\Employee\RequestFormatDecorators\ValidateUniqueParams;
use VolunteerManager\API\FormatRequest;
use VolunteerManager\API\ValidateRequiredRestParams;
use VolunteerManager\Entity\FieldSetter;
use VolunteerManager\Helper\Admin\EmployeeHelper;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class EmployeeApiManager
{
    private AuthenticationInterface $authentication;
    private EmployeeCreator $employeeCreator;
    private FieldSetter $employeeFieldSetter;
    private string $employeePostSlug;

    public function __construct(
        AuthenticationInterface $authentication,
        EmployeeCreator         $employeeCreator,
        FieldSetter             $employeeFieldSetter,
        string                  $employeePostSlug
    )
    {
        $this->authentication = $authentication;

        $this->employeeCreator = $employeeCreator;
        $this->employeeFieldSetter = $employeeFieldSetter;
        $this->employeePostSlug = $employeePostSlug;
    }

    public function addHooks()
    {
        add_action('rest_api_init', array($this, 'registerPostEndpoint'));
    }

    public function registerPostEndpoint()
    {
        register_rest_route(
            'wp/v2',
            'employee',
            array(
                'methods' => 'POST',
                'callback' => new AuthenticationDecorator(
                    [$this, 'handleEmployeeCreationRequest'],
                    $this->authentication
                ),
                'permission_callback' => '__return_true'
            )
        );

        register_rest_route(
            'wp/v2',
            'employee',
            array(
                'methods' => 'GET',
                'callback' => new AuthenticationDecorator([$this, 'handleGetRequest'], $this->authentication),
                'permission_callback' => '__return_true',
            )
        );
    }

    public function handleEmployeeCreationRequest(WP_REST_Request $request)
    {
        $format_request = new FormatRequest();
        $unique_params = new ValidateUniqueParams($format_request);
        $required_params = new ValidateRequiredRestParams(
            $unique_params,
            ['email', 'first_name', 'surname', 'national_identity_number']
        );

        $validated_params = $required_params->formatRestRequest($request);
        if (is_wp_error($validated_params)) {
            return $validated_params;
        }

        return $this->employeeCreator->create(
            $request,
            $this->employeeFieldSetter,
            $this->employeePostSlug
        );
    }

    public function handleGetRequest(WP_REST_Request $request)
    {
        $format_request = new FormatRequest();
        $required_params = new ValidateRequiredRestParams(
            $format_request,
            ['national_identity_number']
        );
        $validated_params = $required_params->formatRestRequest($request);
        if (is_wp_error($validated_params)) {
            return $validated_params;
        }

        $nationalIdentityNumber = $request->get_param('national_identity_number');
        $employee = EmployeeHelper::getEmployeeByIdentityNumber($nationalIdentityNumber);

        if (!$employee) {
            return new WP_Error(
                'rest_post_invalid_id',
                'Invalid post ID.',
                array(
                    'status' => 404,
                )
            );
        }

        return new WP_REST_Response(
            $this->getEmployeeDetails($employee->ID),
            200
        );
    }

    /**
     * Retrieve employee data and format it into an array
     *
     * @param int $employeeId The ID of the employee
     * @return array The formatted employee data
     */
    public function getEmployeeDetails(int $employeeId): array
    {
        $employeeFields = get_fields($employeeId);
        $statuses = get_the_terms($employeeId, 'employee-registration-status');
        $status = $statuses ? $this->formatStatus($statuses) : null;
        $employeeApplications = $this->getEmployeeApplications($employeeId);

        return [
            'id' => $employeeId,
            'national_identity_number' => $employeeFields['national_identity_number'] ?? null,
            'first_name' => $employeeFields['first_name'] ?? null,
            'surname' => $employeeFields['surname'] ?? null,
            'email' => $employeeFields['email'] ?? null,
            'phone_number' => $employeeFields['phone_number'] ?? null,
            'newsletter' => $employeeFields['newsletter'] ?? null,
            'registration_date' => $employeeFields['registration_date'] ?? null,
            'status' => $status,
            'assignments' => $this->formatApplications($employeeApplications),
        ];
    }

    /**
     * Format status
     * @param $statuses
     * @return array
     */
    public function formatStatus($statuses): array
    {
        $status = array_pop($statuses);
        return [
            'term_id' => $status->term_id,
            'name' => $status->name,
            'slug' => $status->slug,
        ];
    }

    /**
     * Format applications list
     * @param $applications
     * @return array
     */
    public function formatApplications($applications): array
    {
        $formattedApplications = [];
        foreach ($applications as $application) {
            $status = get_the_terms($application->ID, 'application-status');
            $assignmentId = get_field('application_assignment', $application->ID);
            $assignmentObject = get_post($assignmentId);
            if ($assignmentObject) {
                $formattedApplications[] = [
                    'id' => $assignmentObject->ID,
                    'title' => $assignmentObject->post_title,
                    'status' => $status ? $this->formatStatus($status) : null,
                    'date' => $application->post_date,
                ];
            }
        }

        return $formattedApplications;
    }

    /**
     * Get employee applications
     * @param $employeeId
     * @return array
     */
    public function getEmployeeApplications($employeeId): array
    {
        return get_posts(
            [
                'post_type' => 'application',
                'post_status' => 'any',
                'orderby' => 'post_date',
                'order' => 'ASC',
                'posts_per_page' => -1,
                'suppress_filters' => true,
                'meta_query' => [
                    [
                        'key' => 'application_employee',
                        'value' => $employeeId,
                        'compare' => '='
                    ]
                ],
            ]
        );
    }
}
