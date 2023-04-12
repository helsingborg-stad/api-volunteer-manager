<?php

namespace VolunteerManager\Employee;

use WP_Error;

interface IEmployeeApiValidator
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
    public function is_national_identity_number_in_use(string $national_identity_number): bool;

    public function is_email_in_use($email): bool;
}
