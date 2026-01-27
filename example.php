<?php

require __DIR__ . '/vendor/autoload.php';

use Ntfy\Adapters\Client;
use Ntfy\Core\Exception\NotificationException;

try {
    // Replace 'test_channel' with your actual ntfy.sh channel
    $errorChannelId = 'test_channel_error_' . bin2hex(random_bytes(4));
    $logChannelId = 'test_channel_log_' . bin2hex(random_bytes(4));
    $urgentChannelId = 'test_channel_urgent_' . bin2hex(random_bytes(4));
    
    echo "Error Channel: $errorChannelId\n";
    echo "Log Channel: $logChannelId\n";
    echo "Urgent Channel: $urgentChannelId\n";

    $notifier = new Client(
        ['id' => $errorChannelId, 'dev_only' => false],
        ['id' => $logChannelId, 'dev_only' => false],
        ['id' => $urgentChannelId, 'dev_only' => false]
    );

    $notifier->send('This is a log message from Notify Library.');
    $notifier->urgent(new \Exception('This is an urgent message from Notify Library.'));
    $notifier->exception(new \Exception('This is an error message from Notify Library.'));
    
    echo "Notifications sent successfully!\n";
    echo "Check https://ntfy.sh/$errorChannelId\n";
    echo "Check https://ntfy.sh/$logChannelId\n";
    echo "Check https://ntfy.sh/$urgentChannelId\n";

} catch (NotificationException $e) {
    echo "Error sending notification: " . $e->getMessage() . "\n";
    exit(1);
} catch (\Throwable $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
    exit(1);
}
