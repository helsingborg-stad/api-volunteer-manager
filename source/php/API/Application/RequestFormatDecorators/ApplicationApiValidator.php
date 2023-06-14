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

    /**
     * @param int $postId
     * @return bool
     */
    public function post_exist(int $postId): bool
    {
        return (bool)get_post($postId);
    }

    /**
     * @param int $postId
     * @return bool
     */
    public function is_post_published(int $postId): bool
    {
        $status = get_post_status($postId);
        return $status === 'publish';
    }

    /**
     * @param int $employeeId
     * @return bool
     */
    public function is_employee_approved(int $employeeId): bool
    {
        $statuses = get_the_terms($employeeId, 'employee-registration-status');
        $status = $statuses[0]->slug ?? null;
        return $status === 'approved';
    }
}
