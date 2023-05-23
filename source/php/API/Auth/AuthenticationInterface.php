<?php

namespace VolunteerManager\API\Auth;

use WP_REST_Request;

interface AuthenticationInterface
{
    /**
     * Validate REST request with JWT
     * @param WP_REST_Request $request
     * @return \WP_Error|array
     */
    public function validateRequest(WP_REST_Request $request);
}