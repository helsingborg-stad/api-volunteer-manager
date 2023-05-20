<?php

namespace VolunteerManager\PostType\Employee;

use WP_Error;

interface EmployeeApiValidatorInterface
{
    /**
     * Validate national identity number
     *
     * @param string $national_identity_number
     * @return bool
     */
    public function is_national_identity_unique(string $national_identity_number): bool;

    /**
     * @param $email
     * @return bool
     */
    public function is_email_unique($email): bool;
}
