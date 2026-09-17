<?php

namespace App\Notifications;

/**
 * Strategy Pattern Interface for Notification Channels (LINE, Email, SMS, Slack)
 */
interface NotificationChannelInterface
{
    /**
     * Send a notification to a specific target (User ID, Channel, Group)
     */
    public function send(string $target, string $message, array $extra = []): bool;
}
