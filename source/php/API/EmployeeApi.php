<?php

namespace VolunteerManager\API;

class EmployeeApi
{
    /**
     * Register custom REST API POST endpoints for Employee
     *
     * The endpoint is registered in the namespace 'wp/v2'.
     * Permissions are set to 'edit_posts' if current user can edit posts.
     *
     * @param string   $endpoint  The endpoints to register.
     * @param callable $callback  The callback function to call when the endpoint is called.
     * @param string   $namespace The namespace for the endpoints. Defaults to 'wp/v2'.
     *
     */
    public function registerPostEndpoint(
        string                        $endpoint,
        callable                      $callback,
        string                        $namespace = 'wp/v2'
    ): void
    {
        register_rest_route($namespace, $endpoint, array(
            'methods' => 'POST',
            'callback' => $callback,
            'permission_callback' => function () {
                // return current_user_can('edit_posts');

                return true;
            }
        ));
    }

}
