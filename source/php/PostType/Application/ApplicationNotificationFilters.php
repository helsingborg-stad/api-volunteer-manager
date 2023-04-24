<?php

namespace VolunteerManager\PostType\Application;

use VolunteerManager\Notification\NotificationFilters;

class ApplicationNotificationFilters extends NotificationFilters
{
    public function addHooks()
    {
        add_filter('avm_admin_external_application_new_notification', array($this, 'populateNotificationReceiverWithAdmin'), 10, 2);
        add_filter('avm_admin_external_application_new_notification', array($this, 'populateAdminNotificationWithContent'), 10, 2);
    }

    /**
     * Populate notification with subject and content
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateAdminNotificationWithContent(array $args, int $postId): array
    {
        $assignment = get_field('application_assignment', $postId);
        $adminUrl = get_edit_post_link($assignment->ID);
        $args['content'] = sprintf(
            $args['content'],
            $assignment->post_title,
            $adminUrl
        );
        return $args;
    }
}