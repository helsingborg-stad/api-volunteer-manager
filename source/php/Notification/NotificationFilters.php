<?php

namespace VolunteerManager\Notification;

class NotificationFilters
{
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
}