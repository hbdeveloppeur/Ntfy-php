<?php

namespace Notify\Adapters;

use Notify\Core\Exception\NotificationException;
use Notify\Core\Notifier;

/**
 * Adapter for sending notifications via ntfy.sh.
 */
class NtfyNotifier implements Notifier
{
    /**
     * Send a notification message to a channel.
     *
     * @param string $channelId The channel to send the notification to.
     * @param string $message   The message content.
     *
     * @return void
     *
     * @throws NotificationException If the notification fails (curl error or non-200 response).
     */
    private string $errorChannelId;
    private string $logChannelId;

    private string $actionDescription;


    public function startNewAction(string $description): void
    {
        $this->actionDescription = $description;
    }

    /**
     * @param string $errorChannelId The channel ID for error notifications.
     * @param string $logChannelId   The channel ID for log notifications.
     */
    public function __construct(string $errorChannelId, string $logChannelId)
    {
        $this->errorChannelId = $errorChannelId;
        $this->logChannelId = $logChannelId;
    }

    /**
     * Send an exception notification.
     *
     * @param \Throwable|null $exception The exception that occurred.
     * @param array $data     Additional data to append to the message.
     *
     * @throws NotificationException
     */
    public function exception( ?\Throwable $exception = null, array $data = []): void
    {
        $message = "Error while doing action: " . $this->actionDescription;

        if ($exception !== null) {
            $message .= "\n\nException: " . $exception->getMessage();
            $message .= "\nFile: " . $exception->getFile() . ":" . $exception->getLine();
            $message .= "\nStack Trace:\n" . $exception->getTraceAsString();
        }

        if (!empty($data)) {
            $message .= "\n\nData:\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $this->send($this->errorChannelId, $message);
    }

    /**
     * Send a log notification.
     *
     * @param string $message The log message content.
     * @param array $data     Additional data to append to the message.
     *
     * @throws NotificationException
     */
    public function log(string $message, array $data = []): void
    {
        $message = "Log: " . $this->actionDescription . "\n" . $message;

        if (!empty($data)) {
            $message .= "\n\nData:\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $this->send($this->logChannelId, $message);
    }

    /**
     * Internal helper to send the notification via cURL.
     *
     * @param string $channelId
     * @param string $message
     * @throws NotificationException
     */
    private function send(string $channelId, string $message): void
    {
        $url = "https://ntfy.sh/" . urlencode($channelId);
        $ch = curl_init($url);

        if ($ch === false) {
            throw new NotificationException("Failed to initialize cURL.");
        }

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set a timeout to avoid hanging indefinitely
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Always close resources
        // curl_close() is deprecated in PHP 8.0+ as it has no effect on CurlHandle objects,
        // but explicit unset or leaving scope handles cleanup in PHP 8+.
        // For compatibility with older PHP versions (if supported) or just good measure if using resources:
        if (is_resource($ch)) {
             curl_close($ch);
        }

        if ($errno !== 0) {
            throw new NotificationException("cURL error ($errno): $error");
        }

        if ($response === false) {
             throw new NotificationException("Failed to execute cURL request.");
        }

        if ($httpCode >= 400) {
            throw new NotificationException("Failed to send notification. HTTP Status: $httpCode. Response: " . (string)$response);
        }
    }
}
