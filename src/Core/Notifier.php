<?php

namespace Notify\Core;


use Notify\Core\Exception\NotificationException;

interface Notifier
{
    /**
     * Send a notification message to a channel.
     *
     * @param string $channelId The unique identifier for the channel.
     * @param string $message   The message content to send.
     *
     * @return void
     *
     * @throws NotificationException If the notification fails to send.
     */
    public function notify(string $channelId, string $message): void;
}
