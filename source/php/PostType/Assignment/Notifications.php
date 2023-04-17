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
        add_filter('avm_external_assignment_denied_notification', array($this, 'populateAssignmentDeniedWithMessage'), 10, 2);
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

    /**
     * Populate notification with message
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateAssignmentApprovedWithMessage(array $args, int $postId): array
    {
        $post = get_post($postId);
        $submitterFirstName = get_post_meta($postId, 'submitted_by_first_name', true) ?? '';

        $args['subject'] = sprintf(__('Your assignment "%s" is approved', AVM_TEXT_DOMAIN), $post->post_title);
        $args['content'] = sprintf(
            __('Hello %s,<br><br>It\'s great that you want to involve more people from Helsingborg! Your assignment "%s" has now been approved for publication on <a href="https://helsingborg.se">helsingborg.se</a> and will be visible to many engaged Helsingborg residents. If you would like to make changes, update something, or remove the assignment, please send an email to engagemang@helsingborg.se.<br><br>Good luck!<br><br>Best regards,<br>Engagemang Helsingborg', AVM_TEXT_DOMAIN),
            $submitterFirstName,
            $post->post_title
        );
        return $args;
    }

    /**
     * Populate notification with message
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateAssignmentDeniedWithMessage(array $args, int $postId): array
    {
        $post = get_post($postId);
        $submitterFirstName = get_post_meta($postId, 'submitted_by_first_name', true) ?? '';

        $args['subject'] = sprintf(__('Your assignment "%s" is denied', AVM_TEXT_DOMAIN), $post->post_title);
        $args['content'] = sprintf(
            __('Hello %s,<br><br>Thank you for wanting to register an assignment for engaged residents of Helsingborg. The assignment "%s" has been processed by an administrator and unfortunately it cannot be published. Please contact Engagemang Helsingborg for more information at engagemang@helsingborg.se.<br><br>Best regards,<br>Engagemang Helsingborg', AVM_TEXT_DOMAIN),
            $submitterFirstName,
            $post->post_title
        );
        return $args;
    }
}