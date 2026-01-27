<?php

namespace Ntfy\Adapters;

use Ntfy\Core\Exception\NotificationException;
use Ntfy\Core\Ntfy;

/**
 * Adapter for sending notifications via ntfy.sh.
 */
class Client implements Ntfy
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
    /** @var array{id: string, dev_only: bool} */
    private array $errorChannel;
    /** @var array{id: string, dev_only: bool} */
    private array $logChannel;
    /** @var array{id: string, dev_only: bool} */
    private array $urgentChannel;
    
    private string $environment;

    private string $actionDescription = '';


    public function startNewAction(string $description): void
    {
        $this->actionDescription = $description;
    }

    /**
     * @param array{id: string, dev_only: bool} $errorChannel
     * @param array{id: string, dev_only: bool} $logChannel
     * @param array{id: string, dev_only: bool} $urgentChannel
     * @param string $environment
     */
    public function __construct(array $errorChannel, array $logChannel, array $urgentChannel, string $environment = 'prod')
    {
        $this->errorChannel = $errorChannel;
        $this->logChannel = $logChannel;
        $this->urgentChannel = $urgentChannel;
        $this->environment = $environment;
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
        $message = "Error";
        if (!empty($this->actionDescription)) {
            $message .= " while doing action: " . $this->actionDescription;
        }

        if ($exception !== null) {
            $message .= "\n\nException: " . $exception->getMessage();
            $message .= "\nFile: " . $exception->getFile() . ":" . $exception->getLine();

            // Limit trace to 6 lines
            $trace = $exception->getTraceAsString();
            $lines = explode("\n", $trace);
            $limitedTrace = implode("\n", array_slice($lines, 0, 6));

            $message .= "\nStack Trace:\n" . $limitedTrace;
        }

        if (!empty($data)) {
            $message .= "\n\nData:\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $this->sendToNtfy($this->errorChannel, $message);
    }

    /**
     * Send a regular notification.
     *
     * @param string $message   The message content.
     * @param array $data       Additional data to append to the message.
     * @param string|null $channelId Optional channel ID to send to. Defaults to the configured log channel.
     *
     * @throws NotificationException
     */
    public function send(string $message, ?string $channelId = null, array $data = []): void
    {
        if (!empty($this->actionDescription)) {
            $message = "Action: " . $this->actionDescription . "\n" . $message;
        }

        if (!empty($data)) {
            $message .= "\n\nData:\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $targetChannel = $channelId 
            ? ['id' => $channelId, 'dev_only' => false] 
            : $this->logChannel;
            
        $this->sendToNtfy($targetChannel, $message);
    }

    /**
     * Send an urgent notification.
     *
     * @param string $message The urgent message content.
     * @param array $data     Additional data to append to the message.
     *
     * @throws NotificationException
     */
    public function urgent(string $message, array $data = []): void
    {
        if (!empty($this->actionDescription)) {
            $message = "Urgent: " . $this->actionDescription . "\n" . $message;
        } else {
            $message = "Urgent: " . $message;
        }

        if (!empty($data)) {
            $message .= "\n\nData:\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        // High priority headers for urgent messages could be added here if Ntfy supports it via headers, 
        // but for now we just send to the urgent channel.
        // Ntfy.sh supports Priority header: 5 (max) or 'urgent'. 
        // We will assume the channel itself is enough, but strictly the request asked for a function with a new channel.
        // However, to make it truly "urgent" in Ntfy terms, we might want to add headers later, but the plan only specified a channel.
        
        $this->sendToNtfy($this->urgentChannel, $message);
    }

    /**
     * Internal helper to send the notification via cURL.
     *
     * @param array{id: string, dev_only: bool} $channel
     * @param string $message
     * @throws NotificationException
     */
    private function sendToNtfy(array $channel, string $message): void
    {
        if ($channel['dev_only'] && $this->environment !== 'dev') {
            return;
        }

        $url = "https://ntfy.sh/" . urlencode($channel['id']);
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
