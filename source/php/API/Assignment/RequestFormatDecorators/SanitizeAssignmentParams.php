<?php

namespace VolunteerManager\API\Assignment\RequestFormatDecorators;

use VolunteerManager\API\ValidateRestRequest;
use WP_REST_Request;

class SanitizeAssignmentParams extends ValidateRestRequest
{
    protected function validator(WP_REST_Request $request): WP_REST_Request
    {
        $requestTextAreaParams = [
            'assignment_eligibility',
            'title',
            'description',
            'qualifications',
            'schedule',
            'benefits',
            'number_of_available_spots',
            'signup_link',
            'signup_email',
            'signup_phone',
        ];

        foreach ($requestTextAreaParams as $param) {
            $request->set_param(
                $param,
                sanitize_textarea_field($request->get_param($param))
            );
        }

        return $request;
    }
}
