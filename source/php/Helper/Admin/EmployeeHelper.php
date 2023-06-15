<?php

namespace VolunteerManager\Helper\Admin;

class EmployeeHelper
{
    /**
     * Retrieve employee by national identity number
     *
     * @param string $nationalIdentityNumber The national identity number of the employee
     * @return null|\WP_Post The employee data matching the national identity number
     */
    public static function getEmployeeByIdentityNumber(string $nationalIdentityNumber): ?\WP_Post
    {
        $employee = get_posts(array(
            'post_type' => 'employee',
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'national_identity_number',
                    'value' => $nationalIdentityNumber,
                    'compare' => '=',
                )
            )
        ));
        return !empty($employee[0]) ? $employee[0] : null;
    }
}
