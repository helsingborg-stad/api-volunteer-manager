<?php

namespace VolunteerManager\Notification;

class NotificationHandler implements NotificationHandlerInterface
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
        add_action('set_object_terms', array($this, 'scheduleTermNotifications'), 10, 6);
        add_action('avm_send_notification', array($this, 'sendNotification'), 10, 4);
    }

    /**
     * Registers notification events when status taxonomy changes
     * @param int    $objectId
     * @param array  $terms
     * @param array  $newIds
     * @param string $taxonomy
     * @param bool   $append
     * @param array  $oldIds
     * @return void
     */
    public function scheduleTermNotifications(int $objectId, array $terms, array $newIds, string $taxonomy, bool $append, array $oldIds): void
    {
        $this->scheduleNotificationsForTermUpdates($newIds, $oldIds, $taxonomy, $objectId);
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

    /**
     * Schedule notifications for terms.
     *
     * @param array  $newTermIds An array of term IDs that were newly assigned to the post.
     * @param array  $oldTermIds An array of term IDs that were previously assigned to the post.
     * @param string $taxonomy   The taxonomy for which the terms are being updated.
     * @param int    $postId     The ID of the post being updated.
     * @return void
     */
    public function scheduleNotificationsForTermUpdates(array $newTermIds, array $oldTermIds, string $taxonomy, int $postId): void
    {
        $oldTermSlugs = $this->convertTermIdsToSlugs($oldTermIds, $taxonomy);
        $newTermSlugs = $this->convertTermIdsToSlugs($newTermIds, $taxonomy);
        $oldAndNewValues = $this->combineOldAndNewValues($oldTermSlugs, $newTermSlugs);
        $taxonomyNotifications = $this->getNotificationsByTaxonomy($this->config, $taxonomy);
        $matchingEvents = $this->findMatchingEvents($taxonomyNotifications, $oldAndNewValues);
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
                return $metaValue == $rule['value'];
            case 'NOT_EQUAL':
                return $metaValue != $rule['value'];
            default:
                return false;
        }
    }

    /**
     * Returns an array of events for all events that has the same old and new values.
     *
     * @param array $notifications
     * @param array $searchCriteria An array of search criteria to match against the events
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
                "oldValue" => $oldValues[$i] ?? null,
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
     * Returns an array of all notifications that have a certain taxonomy value.
     *
     * @param array  $notifications List of notifications.
     * @param string $taxonomy      The desired taxonomy value to match notifications against.
     * @return array An array of matching notifications.
     */
    public function getNotificationsByTaxonomy(array $notifications, string $taxonomy): array
    {
        $matchingNotifications = [];
        foreach ($notifications as $notification) {
            if ($notification['taxonomy'] === $taxonomy) {
                $matchingNotifications[] = $notification;
            }
        }
        return $matchingNotifications;
    }
}