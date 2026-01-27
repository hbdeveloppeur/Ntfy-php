<?php

namespace Ntfy\Composer;

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
        
        $this->createConfiguration($projectRoot);
        $this->registerBundle($projectRoot);
    }

    private function createConfiguration(string $projectRoot): void
    {
        $configDir = $projectRoot . '/config/packages';
        $targetFile = $configDir . '/ntfy.yaml';

        if (!is_dir($configDir)) {
            // Not a standard Symfony project structure
            return;
        }

        if (file_exists($targetFile)) {
            $this->io->write('<info>[Ntfy]</info> Configuration file ntfy.yaml already exists.');
            return;
        }

        $sourceFile = __DIR__ . '/../../config/ntfy.yaml';
        
        if (!file_exists($sourceFile)) {
            $this->io->write('<error>[Ntfy]</error> Source configuration file not found.');
            return;
        }

        if (copy($sourceFile, $targetFile)) {
            $this->io->write('<info>[Ntfy]</info> Created default configuration at <comment>config/packages/ntfy.yaml</comment>');
            $this->io->write('<info>[Ntfy]</info> Please update your channel IDs in that file.');
        } else {
            $this->io->write('<error>[Ntfy]</error> Failed to create configuration file.');
        }
    }

    private function registerBundle(string $projectRoot): void
    {
        $bundlesFile = $projectRoot . '/config/bundles.php';
        
        if (!file_exists($bundlesFile)) {
            return;
        }

        $content = file_get_contents($bundlesFile);
        if (strpos($content, 'Notify\NtfyBundle') !== false) {
            return;
        }

        $newBundle = "    Ntfy\NtfyBundle::class => ['all' => true],\n";
        
        // Find the last occurrence of ];
        $pos = strrpos($content, '];');
        if ($pos !== false) {
            $newContent = substr($content, 0, $pos) . $newBundle . substr($content, $pos);
            if (file_put_contents($bundlesFile, $newContent)) {
                $this->io->write('<info>[Ntfy]</info> Registered NtfyBundle in <comment>config/bundles.php</comment>');
            }
        }
    }
}
