<?php

namespace Light2000\Modeler\Console;

use Illuminate\Console\Command;
use Light2000\Modeler\Support\WritesModelerConfigJson;
use Symfony\Component\Process\Process;

class GenerateCommand extends Command
{
    protected $signature = 'modeler:generate';

    protected $description = 'Run the Modeler generator against .modeler/runtime/config.json.';

    public function handle(): int
    {
        $configPath = WritesModelerConfigJson::configJsonPath($this->laravel);
        if (! is_file($configPath)) {
            WritesModelerConfigJson::dump($this->laravel);
            $this->info('Created ' . $configPath);
        }

        $generatorPath = (string) config('modeler.setting.GENERATOR_PATH');
        if (! is_file($generatorPath)) {
            $this->error('Generator binary not found: ' . $generatorPath);
            $this->line('Run: php artisan modeler:install');

            return self::FAILURE;
        }

        $process = new Process([$generatorPath, '-config', $configPath]);
        $process->setTimeout(null);
        $process->setIdleTimeout(null);

        try {
            $process->mustRun(function (string $type, string $buffer): void {
                $this->output->write($buffer);
            });
        } catch (\Symfony\Component\Process\Exception\ProcessFailedException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return $process->getExitCode() === 0 ? self::SUCCESS : self::FAILURE;
    }
}
