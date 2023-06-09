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
     * @param string     $message    Error message.
     * @param array      $data
     * @return WP_Error
     */
    public static function wp_error_response($error_code, string $message, array $data = []): WP_Error
    {
        return new WP_Error(
            $error_code,
            $message,
            array_merge(array(
                'status' => 400,
            ), $data)
        );
    }

    /**
     * Create a WP_REST_Response
     *
     * @param string $message Success message.
     * @param array  $optional_data
     * @return WP_REST_Response
     */
    public static function wp_rest_response(
        string $message,
        array  $optional_data = []
    ): WP_REST_Response
    {
        return new WP_REST_Response(
            array_merge(
                array(
                    'status' => 200,
                    'message' => $message,
                ),
                $optional_data
            )
        );
    }
}
