<?php

namespace VolunteerManager\PostType\Assignment;

use VolunteerManager\Notification\NotificationFilters;

class AssignmentNotificationFilters extends NotificationFilters
{
    public function addHooks()
    {
        add_filter('avm_notification', array($this, 'populateNotificationSender'), 10, 1);
        add_filter('avm_external_assignment_approved_notification', array($this, 'populateNotificationReceiverWithSubmitter'), 10, 2);
        add_filter('avm_external_assignment_approved_notification', array($this, 'populateStatusNotificationWithContent'), 11, 2);
        add_filter('avm_external_assignment_denied_notification', array($this, 'populateNotificationReceiverWithSubmitter'), 10, 2);
        add_filter('avm_external_assignment_denied_notification', array($this, 'populateStatusNotificationWithContent'), 10, 2);
        add_filter('avm_admin_external_assignment_new_notification', array($this, 'populateNotificationReceiverWithAdmin'), 10, 2);
        add_filter('avm_admin_external_assignment_new_notification', array($this, 'populateAdminNotificationWithContent'), 10, 2);
    }

    /**
     * Populate notification with receiver email address
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateNotificationReceiverWithSubmitter(array $args, int $postId): array
    {
        $receiver = get_post_meta($postId, 'submitted_by_email', true);
        $args['to'] = $receiver ?? '';
        return $args;
    }

    /**
     * Populate notification with subject and content
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateStatusNotificationWithContent(array $args, int $postId): array
    {
        $post = get_post($postId);
        $submitterFirstName = get_post_meta($postId, 'submitted_by_first_name', true) ?? '';
        $args['subject'] = sprintf(
            $args['subject'],
            $post->post_title
        );
        $args['content'] = sprintf(
            $args['content'],
            $submitterFirstName,
            $post->post_title
        );
        return $args;
    }

    /**
     * Populate notification with content
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateAdminNotificationWithContent(array $args, int $postId): array
    {
        $post = get_post($postId);
        $adminUrl = get_admin_url(null, 'post.php?action=edit&post=' . $postId);

        $args['content'] = sprintf(
            $args['content'],
            $post->post_title,
            $adminUrl
        );
        return $args;
    }
}