<?php

namespace VolunteerManager\API\Auth;

class AuthenticationDecorator
{
    private $callback;
    private AuthenticationInterface $authentication;

    public function __construct($callback, $authentication)
    {
        $this->callback = $callback;
        $this->authentication = $authentication;
    }

    public function __invoke($request)
    {
        $validatedRequest = $this->authentication->validateRequest($request);

        if (is_wp_error($validatedRequest)) {
            return $validatedRequest;
        }

        return call_user_func($this->callback, $validatedRequest);
    }
}
