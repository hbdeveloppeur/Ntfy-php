<?php

require __DIR__ . '/vendor/autoload.php';

use Notify\Adapters\NtfyNotifier;
use Notify\Core\Exception\NotificationException;

try {
    // Replace 'test_channel' with your actual ntfy.sh channel
    $errorChannel = 'test_channel_error_' . bin2hex(random_bytes(4));
    $logChannel = 'test_channel_log_' . bin2hex(random_bytes(4));
    
    echo "Error Channel: $errorChannel\n";
    echo "Log Channel: $logChannel\n";

    $notifier = new NtfyNotifier($errorChannel, $logChannel);

    $notifier->log('This is a log message from Notify Library.');
    $notifier->error('This is an error message from Notify Library.');
    
    echo "Notifications sent successfully!\n";
    echo "Check https://ntfy.sh/$errorChannel\n";
    echo "Check https://ntfy.sh/$logChannel\n";

} catch (NotificationException $e) {
    echo "Error sending notification: " . $e->getMessage() . "\n";
    exit(1);
} catch (\Throwable $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
    exit(1);
}
