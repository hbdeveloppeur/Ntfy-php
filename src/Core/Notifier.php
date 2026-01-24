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
     *
     * @return void
     *
     * @throws NotificationException If the notification fails to send.
     */
    public function error(string $action = "", string $message, ?\Throwable $exception = null): void;

    /**
     * Send a log notification.
     *
     * @param string $message The log message content.
     *
     * @return void
     *
     * @throws NotificationException If the notification fails to send.
     */
    public function log(string $message): void;
}
