<?php

namespace VolunteerManager\PostType\Employee;

class EmployeeNotifications
{
    public function addHooks()
    {
        add_filter('avm_external_volunteer_new_notification', array($this, 'populateNotificationReceiverWithSubmitter'), 10, 2);
        add_filter('avm_external_volunteer_new_notification', array($this, 'populateNotificationWithContent'), 10, 2);
        add_filter('avm_admin_external_volunteer_new_notification', array($this, 'populateNotificationReceiverWithAdmin'), 10, 2);
    }

    /**
     * Populate notification receiver with submitter email address
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateNotificationReceiverWithSubmitter(array $args, int $postId): array
    {
        $receiver = get_field('email', $postId);
        $args['to'] = $receiver ?? '';
        return $args;
    }

    /**
     * Populate notification receiver with admin email addresses
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateNotificationReceiverWithAdmin(array $args, int $postId): array
    {
        $receivers = get_field('notification_receivers', 'option') ?? [];
        $emailArray = array_column($receivers, 'email');
        $emailsString = implode(',', $emailArray);
        $args['to'] = $emailsString;
        return $args;
    }

    /**
     * Populate notification with subject and content
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateNotificationWithContent(array $args, int $postId): array
    {
        $firstName = get_field($postId, 'first_name') ?? '';
        $args['subject'] = __($args['subject'], AVM_TEXT_DOMAIN);
        $args['content'] = sprintf(
            __($args['content'], AVM_TEXT_DOMAIN),
            $firstName,
        );
        return $args;
    }
}