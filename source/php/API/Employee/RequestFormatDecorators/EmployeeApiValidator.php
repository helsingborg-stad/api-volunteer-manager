<?php

namespace VolunteerManager\API\Employee\RequestFormatDecorators;

class EmployeeApiValidator
{
    /**
     * @param string $national_identity_number
     * @return bool
     */
    public function is_national_identity_unique(string $national_identity_number): bool
    {
        $employees = get_posts(array(
            'post_type' => 'employee',
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'national_identity_number',
                    'value' => $national_identity_number,
                    'compare' => '=',
                )
            )
        ));

        return empty($employees);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function is_email_unique(string $email): bool
    {
        $employees = get_posts(array(
            'post_type' => 'employee',
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'email',
                    'value' => $email,
                    'compare' => '=',
                )
            )
        ));

        return empty($employees);
    }
}
