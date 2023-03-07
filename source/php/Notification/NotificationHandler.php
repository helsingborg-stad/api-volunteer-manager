<?php

namespace VolunteerManager\Notification;

class NotificationHandler
{
    private array $config;
    private NotificationSenderInterface $sender;

    public function __construct(array $config, NotificationSenderInterface $sender)
    {
        $this->config = $config;
        $this->sender = $sender;
    }

    public function addHooks(): void
    {
        add_action('avm_send_notification', array($this, 'sendNotification'), 10, 4);
    }

    /**
     * Handles a notification event by sending a message to the specified recipient.
     *
     * @param string $to      Recipient of the notification.
     * @param string $from    Sender of the notification.
     * @param string $subject The subject of the notification.
     * @param string $content The body of the notification.
     * @return void
     */
    public function sendNotification(string $to, string $from, string $subject, string $content): void
    {
        $this->sender->send($to, $from, $content, $subject);
    }

    /**
     * Registers a cron event to send a notification.
     *
     * @param array $notification An array containing the notification data.
     * @param int   $postId       The ID of the post associated with the notification.
     * @return void
     */
    public function scheduleNotificationCronEvent(array $notification, int $postId): void
    {
        $eventTime = time() + 10;
        $eventHook = 'avm_send_notification';
        $args = [
            'to' => '',
            'from' => '',
            'subject' => $notification['message']['subject'] ?? '',
            'content' => $notification['message']['content'] ?? '',
        ];
        $args = apply_filters('avm_notification', $args, $postId);
        $args = apply_filters("avm_{$notification['key']}_notification", $args, $postId);
        wp_schedule_single_event($eventTime, $eventHook, $args);
    }

    public function scheduleNotificationsForTermUpdates(array $newTermIds, array $oldTermIds, string $postType, string $taxonomy, int $postId)
    {
        $oldTermSlugs = $this->convertTermIdsToSlugs($oldTermIds, $taxonomy);
        $newTermSlugs = $this->convertTermIdsToSlugs($newTermIds, $taxonomy);
        $oldAndNewValues = $this->combineOldAndNewValues($oldTermSlugs, $newTermSlugs);
        $allTaxonomyNotifications = $this->getNotifications($postType, $taxonomy);
        $matchingEvents = $this->findMatchingEvents($allTaxonomyNotifications, $oldAndNewValues);

        foreach ($matchingEvents as $event) {
            if ($this->shouldScheduleNotification($postId, $event['rule'], 'get_field')) {
                $this->scheduleNotificationCronEvent($event, $postId);
            }
        }
    }

    /**
     * Determines whether an event should be run based on the event rule and post metadata.
     *
     * @param int      $postId     The ID of the post associated with the event.
     * @param array    $rule       An array containing the rule data.
     * @param callable $getFieldFn A function to retrieve post meta data for a given key.
     * @return bool Whether the event should be run.
     */
    public function shouldScheduleNotification(int $postId, array $rule, callable $getFieldFn): bool
    {
        if (empty($rule)) {
            return true;
        }

        $metaValue = call_user_func($getFieldFn, $rule['key'], $postId);

        switch ($rule['operator']) {
            case 'EQUAL':
                return $metaValue === $rule['value'];
            case 'NOT_EQUAL':
                return $metaValue !== $rule['value'];
            default:
                return false;
        }
    }

    /**
     * Returns an array of events for all events that has the same old and new values.
     *
     * @param array $searchCriteria An array of search criteria to match against the events.
     * @return array An array of event enums for all matching events.
     */
    public function findMatchingEvents(array $notifications, array $searchCriteria): array
    {
        $matches = array();
        foreach ($notifications as $notification) {
            if (in_array(array("oldValue" => $notification["oldValue"], "newValue" => $notification["newValue"]), $searchCriteria)) {
                $matches[] = $notification;
            }
        }
        return $matches;
    }

    /**
     * Combines old and new values into an array of arrays.
     *
     * @param array $oldValues An array of old values.
     * @param array $newValues An array of new values.
     * @return array An array of arrays containing the old and new values.
     */
    public function combineOldAndNewValues(array $oldValues, array $newValues): array
    {
        $combinedValues = array();
        for ($i = 0; $i < count($newValues); $i++) {
            $combinedValues[] = array(
                "oldValue" => $oldValues[$i],
                "newValue" => $newValues[$i]
            );
        }
        return $combinedValues;
    }

    /**
     * Converts an array of term IDs to an array of term slugs for a given taxonomy.
     *
     * @param array  $termIds  An array of term IDs.
     * @param string $taxonomy The taxonomy to retrieve term slugs from.
     * @return array An array of term slugs.
     */
    public function convertTermIdsToSlugs(array $termIds, string $taxonomy): array
    {
        foreach ($termIds as &$termId) {
            $term = get_term_by('id', (int)$termId, $taxonomy);
            $termId = $term->slug;
        }
        return $termIds;
    }

    /**
     * Gets the notifications for a given post type and key.
     *
     * @param string $postType The post type to get notifications for.
     * @param string $key      The taxonomy or meta field of the notifications to get.
     * @return array An array of notifications for the given post type and key.
     */
    public function getNotifications(string $postType, string $key): array
    {
        return $this->config[$postType][$key] ?? [];
    }
}