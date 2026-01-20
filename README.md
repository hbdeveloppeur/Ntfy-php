# Notify via Ntfy.sh (PHP Library)

A simple, clean-architecture PHP library for sending notifications via [ntfy.sh](https://ntfy.sh).

## Installation

Install the package via Composer:

```bash
composer require x00/ntfy-php
```

## Configuration

### 1. Register the Bundle

If you are using Symfony, make sure the bundle is registered in `config/bundles.php`:

```php
return [
    // ...
    Notify\NtfyBundle::class => ['all' => true],
];
```

### 2. Configure Channels

Since this package does not include a Flex recipe yet, you need to manually configure the channels.

Create a new file `config/packages/ntfy.yaml` (or copy it from `vendor/x00/ntfy-php/config/ntfy.yaml`):

```yaml
ntfy:
    channels:
        # Replace these with your actual ntfy.sh topic names
        error: 'my_project_errors_secret_123'
        log: 'my_project_logs_secret_123'
```

> **Security Note:** It is recommended to use secret topic names (e.g., `project_name_error_base64uniquestring`) to prevent others from subscribing to your notifications.

## Usage

Inject the `Notify\Core\NotifierInterface` into your services:

```php
use Notify\Core\Notifier;

class MyService
{
    private Notifier $notifier;

    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    public function doSomething()
    {
        // specific error channel
        $this->notifier->error('Something went wrong!');

        // specific log channel
        $this->notifier->log('Process finished successfully.');
    }
}
```

## License

Apache-2.0
