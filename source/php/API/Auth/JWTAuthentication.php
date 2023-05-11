<?php

namespace VolunteerManager\API\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use WP_Error;
use WP_REST_Request;

class JWTAuthentication implements AuthenticationInterface
{
    private string $secretKey;

    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * Validate rest request with JWT
     * @param WP_REST_Request $request
     * @return WP_REST_Request|WP_Error
     */
    public function validateRequest(WP_REST_Request $request): WP_REST_Request|WP_Error
    {
        $authHeader = $request->get_header('Authorization');

        if (!$authHeader) {
            return new WP_Error(
                'jwt_auth_no_auth_header',
                'Authorization header not found.',
                [
                    'status' => 403,
                ]
            );
        }

        [$token] = sscanf($authHeader, 'Bearer %s');

        if (!$token) {
            return new WP_Error(
                'jwt_auth_bad_auth_header',
                'Authorization header malformed.',
                [
                    'status' => 403,
                ]
            );
        }

        if (!$this->secretKey) {
            return new WP_Error(
                'jwt_auth_bad_config',
                'JWT key is not configured properly.',
                [
                    'status' => 403,
                ]
            );
        }

        try {
            $decodedToken = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $request->decoded_token = (array)$decodedToken;
            return $request;
        } catch (\Exception $e) {
            return new WP_Error(
                'jwt_auth_invalid_token',
                'Invalid authentication token.',
                [
                    'status' => 403,
                ]
            );
        }
    }
}