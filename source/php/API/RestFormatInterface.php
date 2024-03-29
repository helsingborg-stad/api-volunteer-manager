<?php

namespace VolunteerManager\API;

use WP_Error;
use WP_REST_Request;

interface RestFormatInterface
{
    /**
     * Validate required parameters of the REST request
     *
     * @param WP_REST_Request $request
     * @return array|WP_Error
     */
    public function formatRestRequest(WP_REST_Request $request);
}
