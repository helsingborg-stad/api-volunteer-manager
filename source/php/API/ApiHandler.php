<?php

namespace VolunteerManager\API;

class ApiHandler
{
    public function extractParamsFromRequest(\WP_REST_Request $request, array $requestParams): array
    {
        $params = [];
        foreach ($requestParams as $param) {
            $params[$param] = $request->get_param($param);
        }

        return $params;
    }
}
