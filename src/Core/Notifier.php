<?php

namespace Notify\Core;


use Notify\Core\Exception\NotificationException;

interface Notifier
{
    /**
     * Send an error notification.
     *
     * @param string $actionName The action name.
     * @param \Throwable|null $exception The exception that occurred.
     * @param array $data     Additional data to append to the message.
     *
     * @return void
     *
     * @throws NotificationException If the notification fails to send.
     */
    public function error(string $actionName, ?\Throwable $exception = null, array $data = []): void;

    /**
     * Send a log notification.
     *
     * @param string $actionName The action name.
     * @param string $message The log message content.
     * @param array $data     Additional data to append to the message.
     *
     * @return void
     *
     * @throws NotificationException If the notification fails to send.
     */
    public function log(string $actionName, string $message, array $data = []): void;
}
