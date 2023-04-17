<?php

namespace VolunteerManager\PostType\Assignment;

class Notifications
{
    public function addHooks()
    {
        add_filter('avm_notification', array($this, 'populateNotificationSender'), 10, 1);
        add_filter('avm_external_assignment_approved_notification', array($this, 'populateNotificationReceiverWithSubmitter'), 10, 2);
        add_filter('avm_external_assignment_approved_notification', array($this, 'populateAssignmentApprovedWithMessage'), 11, 2);
        add_filter('avm_external_assignment_denied_notification', array($this, 'populateNotificationReceiverWithSubmitter'), 10, 2);
    }

    /**
     * Populates notifications with sender email address
     * @param array $args
     * @return array
     */
    public function populateNotificationSender(array $args): array
    {
        $senderOption = get_field('notification_sender', 'option');
        $senderEmail = $senderOption['email'] ?? '';
        $sender = $senderOption['name'] ? "{$senderOption['name']} <{$senderEmail}>" : $senderEmail;
        $args['from'] = $sender;
        return $args;
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
}