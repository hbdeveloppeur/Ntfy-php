<?php

namespace Notify\Core;


use Notify\Core\Exception\NotificationException;

interface Notifier
{
    /**
     * Send an error notification.
     *
     * @param string $action  The action name.
     * @param string $message The error message content.
     * @param \Throwable|null $exception The exception that occurred.
     * @param array $data     Additional data to append to the message.
     *
     * @return void
     *
     * @throws NotificationException If the notification fails to send.
     */
    public function error(string $action = "", string $message, ?\Throwable $exception = null, array $data = []): void;

    /**
     * Send a log notification.
     *
     * @param string $message The log message content.
     * @param array $data     Additional data to append to the message.
     *
     * @return void
     *
     * @throws NotificationException If the notification fails to send.
     */
    public function log(string $message, array $data = []): void;
}
