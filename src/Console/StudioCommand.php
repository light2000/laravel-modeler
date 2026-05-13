<?php

namespace Light2000\Modeler\Console;

use Illuminate\Console\Command;
use Light2000\Modeler\Support\Package;
use Light2000\Modeler\Support\PortChecker;
use Light2000\Modeler\Support\WritesModelerConfigJson;
use Symfony\Component\Process\Process;

class StudioCommand extends Command
{
    protected $signature = 'modeler:studio';

    protected $description = 'Start Modeler Studio (foreground; Ctrl+C to stop).';

    public function handle(): int
    {
        $setting = config('modeler.setting', []);
        $dataPath = (string) ($setting['DATA_PATH'] ?? '');
        $logPath = (string) ($setting['LOG_PATH'] ?? '');
        $studioPath = (string) ($setting['STUDIO_PATH'] ?? '');
        $port = (string) ($setting['STUDIO_SERVER_PORT'] ?? 'auto');

        foreach ([$dataPath, $logPath, dirname($studioPath)] as $dir) {
            if ($dir !== '' && ! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $configPath = WritesModelerConfigJson::dump($this->laravel);
        $this->info('Config: ' . $configPath);

        if ($port !== 'auto' && PortChecker::isInUse('127.0.0.1', $port)) {
            $this->error("Port {$port} is already in use. Set MODELER_SERVER_PORT in .env or config to a free port.");

            return self::FAILURE;
        }

        $latestPath = rtrim($dataPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'latest.json';
        if (! is_file($latestPath)) {
            $stub = Package::stubLatestJsonPath();
            if (! is_file($stub)) {
                $this->error('Stub not found: ' . $stub);

                return self::FAILURE;
            }
            if (! copy($stub, $latestPath)) {
                $this->error('Failed to create ' . $latestPath);

                return self::FAILURE;
            }
            $this->info('Initialized ' . $latestPath);
        }

        if (! is_file($studioPath)) {
            $this->error('Studio binary not found: ' . $studioPath);
            $this->line('Run: php artisan modeler:install');

            return self::FAILURE;
        }

        $process = new Process([$studioPath, '-config', $configPath]);
        $process->setTimeout(null);
        $process->setIdleTimeout(null);

        if (function_exists('pcntl_async_signals') && function_exists('pcntl_signal')) {
            $term = defined('SIGTERM') ? SIGTERM : 15;
            pcntl_async_signals(true);
            pcntl_signal(SIGINT, function () use ($process, $term): void {
                if ($process->isRunning()) {
                    $process->signal($term);
                }
            });
            pcntl_signal(SIGTERM, function () use ($process, $term): void {
                if ($process->isRunning()) {
                    $process->signal($term);
                }
            });
        }

        try {
            $code = $process->run(function (string $type, string $buffer): void {
                $this->output->write($buffer);
            });

            return $code === 0 ? self::SUCCESS : self::FAILURE;
        } finally {
            if ($configPath !== '' && is_file($configPath)) {
                @unlink($configPath);
            }
        }
    }
}
