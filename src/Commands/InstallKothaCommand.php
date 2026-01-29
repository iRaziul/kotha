<?php

declare(strict_types=1);

namespace Larament\Kotha\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;

#[AsCommand(name: 'kotha:install', description: 'Install the Kotha package and publish the configuration')]
final class InstallKothaCommand extends Command
{
    public function handle(): int
    {
        $this->publishConfigs();

        $this->askToStarRepo('iRaziul/kotha');

        return self::SUCCESS;
    }

    private function publishConfigs(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'kotha-config',
        ]);
    }

    private function askToStarRepo(string $repoVendorPath): void
    {
        if (confirm('Would you like to star this repo on GitHub?', true)) {
            $repoUrl = "https://github.com/{$repoVendorPath}";

            match (mb_strtolower(PHP_OS_FAMILY)) {
                'darwin' => exec("open {$repoUrl}"),
                'linux' => exec("xdg-open {$repoUrl}"),
                'windows' => exec("start {$repoUrl}"),
                default => null,
            };
        }

        $this->components->info('Thank you ❤️');
    }
}
