<?php

namespace VolunteerManager\API\Employee;

class EmployeeFieldSetter
{
    public function updateEmployeeFields(int $employee_id, array $params): void
    {
        foreach ($params as $key => $value) {
            update_field($key, $value, $employee_id);
        }
    }

    public function setEmployeeDate(int $employee_id): void
    {
        update_field('registration_date', date('Y-m-d'), $employee_id);
    }

    public function setEmployeeStatus(int $employee_id, string $status_slug): void
    {
        $new_status_term = get_term_by('slug', 'new', 'employee-registration-status');
        if ($new_status_term) {
            wp_set_post_terms($employee_id, array($new_status_term->term_id), 'employee-registration-status');
        }
    }
}
