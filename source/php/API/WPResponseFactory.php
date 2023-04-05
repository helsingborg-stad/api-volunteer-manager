<?php

namespace VolunteerManager\API;

use WP_Error;

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
    public static function wp_error_response($error_code, string $message, $param): WP_Error
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
}
