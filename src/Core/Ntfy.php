<?php

namespace Ntfy\Core;


use Ntfy\Core\Exception\NotificationException;

interface Ntfy
{

    public function startNewAction(string $description): void;

    /**
     * Send an exception notification.
     *
     * @param \Throwable|null $exception The exception that occurred.
     * @param array $data     Additional data to append to the message.
     *
     * @return void
     *
     * @throws NotificationException If the notification fails to send.
     */
    public function exception(?\Throwable $exception = null, array $data = []): void;

    /**
     * Send a regular notification.
     * 
     * @param string $message   The message content.
     * @param array $data       Additional data to append to the message.
     * @param string|null $channelId Optional channel ID to send to. Defaults to the configured log channel.
     *
     * @return void
     *
     * @throws NotificationException If the notification fails to send.
     */
    public function send(string $message, ?string $channelId = null, array $data = []): void;

    /**
     * Send an urgent notification.
     *
     * @param \Throwable|null $exception The exception/urgent issue that occurred.
     * @param array $data     Additional data to append to the message.
     *
     * @return void
     *
     * @throws NotificationException If the notification fails to send.
     */
    public function urgent(?\Throwable $exception = null, array $data = []): void;
}
