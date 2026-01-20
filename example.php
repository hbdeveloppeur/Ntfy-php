<?php

require __DIR__ . '/vendor/autoload.php';

use Notify\Adapters\NtfyNotifier;
use Notify\Core\Exception\NotificationException;

try {
    $notifier = new NtfyNotifier();

    // Replace 'test_channel' with your actual ntfy.sh channel
    $channel = 'test_channel_' . bin2hex(random_bytes(4));
    echo "Sending notification to channel: $channel\n";

    $notifier->notify($channel, 'Hello from Notify Library with Exceptions!');
    
    echo "Notification sent successfully! Check https://ntfy.sh/$channel\n";

} catch (NotificationException $e) {
    echo "Error sending notification: " . $e->getMessage() . "\n";
    exit(1);
} catch (\Throwable $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
    exit(1);
}
