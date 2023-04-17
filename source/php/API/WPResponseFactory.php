<?php

namespace VolunteerManager\API;

use WP_Error;
use WP_REST_Response;

class WPResponseFactory
{


    /**
     * Create a WP_Error response
     *
     * @param string|int $error_code Error code.
     * @param string $message Error message.
     * @param string $param
     * @return WP_Error
     */
    public static function wp_error_response($error_code, string $message, string $param): WP_Error
    {
        return new WP_Error(
            $error_code,
            $message,
            array(
                'status' => 400,
                'param' => $param
            )
        );
    }

    /**
     * Create a WP_REST_Response
     *
     * @param string $message Success message.
     * @param int $employee_id Employee ID.
     * @param int $status Status code.
     * @return WP_REST_Response
     */
    public static function wp_rest_response(
        string $message,
        int    $employee_id,
        int    $status = 200
    ): WP_REST_Response
    {
        return new WP_REST_Response(
            array(
                'status' => $status,
                'message' => $message,
                'employee_id' => $employee_id
            ),
            $status
        );
    }
}
