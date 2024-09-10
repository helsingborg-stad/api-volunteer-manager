<?php

namespace VolunteerManager\API;

use WP_REST_Request;

class ApiHandler
{
    public function extractParamsFromRequest(WP_REST_Request $request, array $requestParams): array
    {
        $params = [];
        foreach ($requestParams as $param) {
            $params[$param] = $request->get_param($param);
        }

        return $params;
    }

    public function extractSourceFromRequest(WP_REST_Request $request): string
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $source = __('External form submission', 'api-volunteer-manager');

        if ($referer) {
            // Check if the referer matches the WordPress site URL
            if (strpos($referer, get_site_url()) === 0) {
                $source = __('Internally created user', 'api-volunteer-manager');
            } else {
                $parsedSource = parse_url($referer, PHP_URL_HOST);
                $source = $parsedSource ? $parsedSource : $referer;
            }
        }

        return $source;
    }
}
