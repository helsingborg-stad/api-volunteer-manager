<?php

namespace VolunteerManager\API\Application\RequestFormatDecorators;

class ApplicationApiValidator
{
    /**
     * @param int $employeeId
     * @param int $assignmentId
     * @return bool
     */
    public function is_application_unique(int $employeeId, int $assignmentId): bool
    {
        $args = array(
            'post_type' => 'application',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'application_assignment',
                    'value' => $assignmentId,
                    'compare' => '=',
                ),
                array(
                    'key' => 'application_employee',
                    'value' => $employeeId,
                    'compare' => '=',
                ),
            ),
            'post_status' => 'any',
            'posts_per_page' => 1,
        );

        $applications = get_posts($args);

        return empty($applications);
    }
}
