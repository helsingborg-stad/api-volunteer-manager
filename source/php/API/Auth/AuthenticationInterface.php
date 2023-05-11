<?php

namespace VolunteerManager\API\Auth;

use WP_Error;
use WP_REST_Request;

interface AuthenticationInterface
{
    /**
     * Validate REST request with JWT
     * @param WP_REST_Request $request
     * @return WP_REST_Request|WP_Error
     */
    public function validateRequest(WP_REST_Request $request): WP_REST_Request|WP_Error;
}
