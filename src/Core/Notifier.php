<?php

namespace Notify\Core;


use Notify\Core\Exception\NotificationException;

interface Notifier
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
