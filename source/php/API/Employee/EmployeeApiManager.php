<?php

namespace VolunteerManager\API\Employee;

use VolunteerManager\API\Auth\AuthenticationDecorator;
use VolunteerManager\API\Auth\AuthenticationInterface;
use VolunteerManager\API\Employee\RequestFormatDecorators\ValidateUniqueParams;
use VolunteerManager\API\FormatRequest;
use VolunteerManager\API\ValidateRequiredRestParams;
use VolunteerManager\Entity\FieldSetter;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class EmployeeApiManager
{
    private AuthenticationInterface $authentication;
    private EmployeeCreator $employeeCreator;
    private FieldSetter $employeeFieldSetter;

    public function __construct(
        AuthenticationInterface $authentication,
        EmployeeCreator         $employeeCreator,
        FieldSetter             $employeeFieldSetter
    )
    {
        $this->authentication = $authentication;

        $this->employeeCreator = $employeeCreator;
        $this->employeeFieldSetter = $employeeFieldSetter;
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

        return $this->employeeCreator->create($request, $this->employeeFieldSetter);
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
        $employee = $this->getEmployeeByIdentityNumber($nationalIdentityNumber);

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
        $status = get_the_terms($employeeId, 'employee-registration-status');
        $status = !empty($status[0]) ? [
            'term_id' => $status[0]->term_id,
            'name' => $status[0]->name,
            'slug' => $status[0]->slug,
        ] : null;

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
        ];
    }

    /**
     * Retrieve employee by national identity number
     *
     * @param string $nationalIdentityNumber The national identity number of the employee
     * @return null|\WP_Post The employee data matching the national identity number
     */
    public function getEmployeeByIdentityNumber(string $nationalIdentityNumber): ?\WP_Post
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
