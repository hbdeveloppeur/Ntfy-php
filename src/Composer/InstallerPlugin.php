<?php

namespace Notify\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

class InstallerPlugin implements PluginInterface, EventSubscriberInterface
{
    private $composer;
    private $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    public function getComposer(): Composer
    {
        return $this->composer;
    }

    public function getIO(): IOInterface
    {
        return $this->io;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'onPostInstall',
            ScriptEvents::POST_UPDATE_CMD => 'onPostInstall',
        ];
    }

    public function onPostInstall(Event $event): void
    {
        $projectRoot = getcwd();
        $configDir = $projectRoot . '/config/packages';
        $targetFile = $configDir . '/ntfy.yaml';

        if (!is_dir($configDir)) {
            // Not a standard Symfony project structure, or config/packages doesn't exist
            return;
        }

        if (file_exists($targetFile)) {
            $this->io->write('<info>[Notify]</info> Configuration file ntfy.yaml already exists.');
            return;
        }

        $sourceFile = __DIR__ . '/../../config/ntfy.yaml';
        
        if (!file_exists($sourceFile)) {
            // This should not happen if the library is correctly installed
            $this->io->write('<error>[Notify]</error> Source configuration file not found.');
            return;
        }

        if (copy($sourceFile, $targetFile)) {
            $this->io->write('<info>[Notify]</info> Created default configuration at <comment>config/packages/ntfy.yaml</comment>');
            $this->io->write('<info>[Notify]</info> Please update your channel IDs in that file.');
        } else {
            $this->io->write('<error>[Notify]</error> Failed to create configuration file.');
        }
    }
}
