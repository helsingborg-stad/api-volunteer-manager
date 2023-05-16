<?php

namespace VolunteerManager\API\Auth;

use WP_REST_Request;

interface AuthenticationInterface
{
    /**
     * Validate REST request with JWT
     * @param WP_REST_Request $request
     * @return mixed Returns WP_REST_Request on success, or WP_Error on failure.
     */
    public function validateRequest(WP_REST_Request $request): WP_REST_Request;
}