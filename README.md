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

### Automatic Setup (Symfony)

Upon installation, a default configuration file is automatically created at `config/packages/ntfy.yaml`. You just need to update it with your channel IDs:

```yaml
ntfy:
    channels:
        error: 'your-error-channel-id'
        log: 'your-log-channel-id'
```

### Environment Variables

Alternatively, you can use environment variables without any configuration file:

- `NTFY_ERROR_CHANNEL`
- `NTFY_LOG_CHANNEL`

### Manual Instantiation

If you are not using Symfony, you can instantiate the notifier directly:

```php
use Notify\Adapters\NtfyNotifier;

$notifier = new NtfyNotifier('your-error-channel-id', 'your-log-channel-id');
```

## Usage

Use the `Notify\Core\Notifier` interface to send notifications.

### Contextual Action

You can set a context action name that will be used for subsequent notifications:

```php
$this->notifier->startNewAction('User Registration');
```

### Exception Notifications

```php
use Notify\Core\Notifier;

class MyService
{
    public function __construct(
        private Notifier $notifier
    ) {}

    public function doSomething()
    {
        try {
            // ...
        } catch (\Exception $e) {
            $this->notifier->exception(
                exception: $e,
                data: ['user_id' => 123, 'context' => 'foo']
            );
        }
    }
}
```

### Log Notifications

```php
$this->notifier->log('Something happened', ['key' => 'value']);
```

## License

Apache-2.0
