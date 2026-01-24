# Notify via Ntfy.sh (PHP Library)

Send notifications to your phone in one line via [ntfy.sh](https://ntfy.sh).

## Installation

Install the package via Composer:

```bash
composer require x00/ntfy-php
```


## Configuration

### Symfony

Create a configuration file `config/packages/ntfy.yaml`:

```yaml
ntfy:
    channels:
        error: 'your-error-channel-id'
        log: 'your-log-channel-id'
```

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
