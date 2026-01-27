# Notify via Ntfy.sh (PHP Library)

Send notifications to your phone in one line via [ntfy.sh](https://ntfy.sh).

## Installation

Install the package via Composer:

```bash
composer require x00/ntfy-php
```

> [!NOTE]
> During installation, you may be asked to allow the `x00/ntfy-php` plugin. This is required to automatically generate the configuration file.

## Configuration

The library now supports **zero-configuration** for Symfony projects.

### Automatic Notifications Channels (Symfony)

Upon installation, a default configuration file is automatically created at `config/packages/ntfy.yaml`. You just need to update it with your channel IDs:

```yaml
ntfy:
    channels:
        error: 
            id: 'your-error-channel-id'
            dev_only: true
        log: 
            id: 'your-log-channel-id'
            dev_only: true # Optional: Only send in 'dev' environment
        urgent:
            id: 'your-urgent-channel-id'
            dev_only: false
```

### Environment Variables

Alternatively, you can use environment variables without any configuration file:

- `NTFY_ERROR_CHANNEL`
- `NTFY_LOG_CHANNEL`
- `NTFY_URGENT_CHANNEL`

## Usage

Use the `Ntfy\Core\Ntfy` interface to send notifications.

### Regular Notifications

```php
use Ntfy\Core\Ntfy;

class MyService
{
    public function __construct(
        private Ntfy $notifier
    ) {}

    public function doSomething()
    {
        // Send to log channel
        $this->notifier->send('Something else happened');

        // Send with data
        $this->notifier->send('Something happened', null, ['key' => 'value']);

        // Send to specific channel
        $this->notifier->send('Something happened', 'my-custom-channel-id', ['key' => 'value']);
    }
}
```

### Exception Notifications

```php
try {
    // ...
} catch (\Throwable $e) {
    $this->notifier->exception($e, ['user_id' => 123, 'context' => 'foo']);
}
```

### Urgent Notifications

```php
$this->notifier->urgent(new \Exception('Server is down!'));
```

## License

Apache-2.0
