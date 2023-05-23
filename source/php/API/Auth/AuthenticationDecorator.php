<?php

namespace VolunteerManager\API\Auth;

use WP_REST_Request;

class AuthenticationDecorator
{
    private $callback;
    private AuthenticationInterface $authentication;

    public function __construct($callback, $authentication)
    {
        $this->callback = $callback;
        $this->authentication = $authentication;
    }

    public function __invoke(WP_REST_Request $request)
    {
        $validatedRequest = $this->authentication->validateRequest($request);

        if (is_wp_error($validatedRequest)) {
            return $validatedRequest;
        }

        $newRequest = $this->setTokenRequestParams($request, $validatedRequest);

        return call_user_func($this->callback, $newRequest);
    }

    public function setTokenRequestParams(WP_REST_Request $request, array $params): WP_REST_Request
    {
        $request->set_param('session_id', $params['sessionId'] ?? null);
        $request->set_param('national_identity_number', $params['id'] ?? null);
        $request->set_param('name', $params['name'] ?? null);
        $request->set_param('first_name', $params['firstName'] ?? null);
        $request->set_param('surname', $params['lastName'] ?? null);
        $request->set_param('source', isset($params['iss']) ? stripslashes($params['iss']) : null);
        $request->set_param('aud', isset($params['aud']) ? stripslashes($params['aud']) : null);
        return $request;
    }
}
