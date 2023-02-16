<?php

namespace VolunteerManager\Helper\Admin;

class URL
{
    /**
     * Create URL to trigger edit post action
     * @param string  $action
     * @param array   $args
     * @param         $createNonce
     * @return string
     */
    public static function createPostActionUrl(string $action, array $args, $createNonce): string
    {
        $paged = self::getRequestParameter('paged');
        $args = array_merge(
            array(
                'nonce' => $createNonce($action),
                'action' => $action,
                'paged' => $paged,
            ),
            $args
        );
        $queryString = http_build_query($args);
        return admin_url('admin-post.php') . '?' . $queryString;
    }

    /**
     * Creates a cryptographic token
     * @param string $action
     * @return string
     */
    public static function wpCreateNonce(string $action): string
    {
        return wp_create_nonce($action);
    }

    /**
     * Get request parameters
     * @param string $paramName
     * @param        $default
     * @return mixed|null
     */
    public static function getRequestParameter(string $paramName, $default = null)
    {
        $paramValue = filter_input(INPUT_GET, $paramName, FILTER_SANITIZE_STRING);
        return $paramValue ?? $default;
    }
}