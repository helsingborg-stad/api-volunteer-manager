<?php

namespace VolunteerManager\Notification;

interface NotificationHandlerInterface
{
    public function addHooks(): void;

    public function sendNotification(string $to, string $from, string $subject, string $content): void;

    public function scheduleNotificationCronEvent(array $notification, int $postId): void;

    public function scheduleNotificationsForTermUpdates(array $newTermIds, array $oldTermIds, string $postType, string $taxonomy, int $postId): void;

    public function shouldScheduleNotification(int $postId, array $rule, callable $getFieldFn): bool;

    public function findMatchingEvents(array $notifications, array $searchCriteria): array;

    public function combineOldAndNewValues(array $oldValues, array $newValues): array;

    public function convertTermIdsToSlugs(array $termIds, string $taxonomy): array;

    public function getNotifications(string $postType, string $taxonomy): array;
}
