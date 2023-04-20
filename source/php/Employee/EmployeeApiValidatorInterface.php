<?php

namespace VolunteerManager\Employee;

use WP_Error;

interface EmployeeApiValidatorInterface
{
    /**
     * Validate required parameters
     *
     * @param array $params
     * @return bool|WP_Error
     */
    public function validate_required_params(array $params);

    /**
     * Validate national identity number
     *
     * @param string $national_identity_number
     * @return bool|WP_Error
     */
    public function is_national_identity_unique(string $national_identity_number): bool;

    /**
     * @param $email
     * @return bool|WP_Error
     */
    public function is_email_unique($email): bool;
}
