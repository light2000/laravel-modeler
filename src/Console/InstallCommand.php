<?php

namespace Light2000\Modeler\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Light2000\Modeler\Support\BinaryDownloader;

class InstallCommand extends Command
{
    const GENERATOR_VERSION = 'v0.1.2';

    const STUDIO_VERSION = 'v0.1.2';

    const BASE_URL = 'http://laravel-modeler.test.upcdn.net/releases/';

    protected $signature = 'modeler:install
                            {--force : Overwrite existing binaries}';

    protected $description = 'Download Modeler generator and studio binaries for this platform.';

    protected static $studioUrls = [
        'windows-amd64' => self::BASE_URL . 'studio/' . self::STUDIO_VERSION . '/studio-windows-amd64.exe',
        'linux-amd64' => self::BASE_URL . 'studio/' . self::STUDIO_VERSION . '/studio-linux-amd64',
        'darwin-arm64' => self::BASE_URL . 'studio/' . self::STUDIO_VERSION . '/studio-darwin-arm64',
        'darwin-amd64' => self::BASE_URL . 'studio/' . self::STUDIO_VERSION . '/studio-darwin-amd64',
    ];

    protected static $generatorUrls = [
        'windows-amd64' => self::BASE_URL . 'generator/' . self::GENERATOR_VERSION . '/generator-windows-amd64.exe',
        'linux-amd64' => self::BASE_URL . 'generator/' . self::GENERATOR_VERSION . '/generator-linux-amd64',
        'darwin-arm64' => self::BASE_URL . 'generator/' . self::GENERATOR_VERSION . '/generator-darwin-arm64',
        'darwin-amd64' => self::BASE_URL . 'generator/' . self::GENERATOR_VERSION . '/generator-darwin-amd64',
    ];

    public function handle(BinaryDownloader $downloader): int
    {
        $force = (bool) $this->option('force');
        $generatorPath = (string) config('modeler.setting.GENERATOR_PATH');
        $studioPath = (string) config('modeler.setting.STUDIO_PATH');
        $templatesPath = (string) config('modeler.setting.TEMPLATES_PATH');
        $promptPath = (string) config('modeler.setting.PROMPT_PATH');
        $dataPath = (string) config('modeler.setting.DATA_PATH');
        $logPath = (string) config('modeler.setting.LOG_PATH');
        $runtimePath = (string) config('modeler.setting.RUNTIME_PATH');
        $files = new Filesystem();

        try {
            $platformKey = $this->resolvePlatformKey();
            if (! isset(self::$generatorUrls[$platformKey], self::$studioUrls[$platformKey])) {
                throw new \RuntimeException(sprintf(
                    'No download URLs for platform "%s" (PHP_OS_FAMILY=%s, php_uname("m")=%s). Supported: %s.',
                    $platformKey,
                    PHP_OS_FAMILY,
                    php_uname('m'),
                    implode(', ', array_keys(self::$generatorUrls))
                ));
            }
            $generatorUrl = self::$generatorUrls[$platformKey];
            $studioUrl = self::$studioUrls[$platformKey];
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Downloading generator…');
        try {
            $downloader->download($generatorUrl, $generatorPath, $force);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
        $this->line('  → ' . $generatorPath);
        $this->ensureGitignoreIgnoresBinary($files, $generatorPath);

        $this->info('Downloading studio…');
        try {
            $downloader->download($studioUrl, $studioPath, $force);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
        $this->line('  → ' . $studioPath);
        $this->ensureGitignoreIgnoresBinary($files, $studioPath);

        $packageRoot = dirname(__DIR__, 2);
        $sourceTemplatesPath = $packageRoot . '/templates';
        $sourcePromptPath = $packageRoot . '/prompt';

        try {
            $this->info('init runtime, data and log directories…');
            if ($runtimePath !== '') {
                $this->ensureDirectory($files, $runtimePath);
                $this->line('  → ' . $runtimePath);
                $this->ensureRuntimeDirectoryGitignoreIfAbsent($files, $runtimePath);
            }
            $this->ensureDirectory($files, $dataPath);
            $this->line('  → ' . $dataPath);
            $this->ensureDirectory($files, $logPath);
            $this->line('  → ' . $logPath);
            $this->ensureLogDirectoryGitignore($files, $logPath);
            $this->info('Copying templates and prompt…');
            $this->copyDirectory($files, $sourceTemplatesPath, $templatesPath);
            $this->line('  → ' . $templatesPath);
            $this->copyDirectory($files, $sourcePromptPath, $promptPath);
            $this->line('  → ' . $promptPath);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Done.');

        return self::SUCCESS;
    }

    private function ensureDirectory(Filesystem $files, string $path): void
    {
        if ($path === '') {
            throw new \RuntimeException('Modeler path config cannot be empty.');
        }

        if (! $files->isDirectory($path)) {
            $files->makeDirectory($path, 0755, true);
        }
    }

    private function copyDirectory(Filesystem $files, string $source, string $destination): void
    {
        if ($destination === '') {
            throw new \RuntimeException('Modeler path config cannot be empty.');
        }
        if (! $files->isDirectory($source)) {
            throw new \RuntimeException("Source directory not found: {$source}");
        }

        $this->ensureDirectory($files, $destination);
        $files->copyDirectory($source, $destination);
    }

    /**
     * 在二进制文件所在目录写入 .gitignore，忽略该文件名，避免将下载的二进制纳入版本管理。
     */
    private function ensureGitignoreIgnoresBinary(Filesystem $files, string $binaryPath): void
    {
        if ($binaryPath === '') {
            return;
        }

        $directory = dirname($binaryPath);
        if (! $files->isDirectory($directory)) {
            $files->makeDirectory($directory, 0755, true);
        }

        $basename = basename($binaryPath);
        $gitignorePath = $directory . DIRECTORY_SEPARATOR . '.gitignore';

        $lines = [];
        if ($files->exists($gitignorePath)) {
            $lines = array_map('trim', preg_split('/\R/', (string) $files->get($gitignorePath)) ?: []);
            $lines = array_values(array_filter($lines, static fn(string $line): bool => $line !== ''));
        }

        if (in_array($basename, $lines, true)) {
            return;
        }

        $lines[] = $basename;
        $files->put($gitignorePath, implode("\n", $lines) . "\n");
    }

    /**
     * 在日志目录写入 .gitignore：忽略目录内除 .gitignore 外的所有文件。
     */
    private function ensureLogDirectoryGitignore(Filesystem $files, string $logDirectory): void
    {
        if ($logDirectory === '') {
            return;
        }

        $gitignorePath = $logDirectory . DIRECTORY_SEPARATOR . '.gitignore';
        $expectedLines = ['*', '!.gitignore'];

        if ($files->exists($gitignorePath)) {
            $lines = array_map('trim', preg_split('/\R/', (string) $files->get($gitignorePath)) ?: []);
            $lines = array_values(array_filter($lines, static fn(string $line): bool => $line !== '' && ! str_starts_with($line, '#')));
            if ($lines === $expectedLines) {
                return;
            }
        }

        $files->put($gitignorePath, "*\n!.gitignore\n");
    }

    /**
     * 若 runtime 目录下尚无 .gitignore，则创建并忽略目录内除 .gitignore 外的所有文件。
     */
    private function ensureRuntimeDirectoryGitignoreIfAbsent(Filesystem $files, string $runtimeDirectory): void
    {
        if ($runtimeDirectory === '') {
            return;
        }

        $gitignorePath = $runtimeDirectory . DIRECTORY_SEPARATOR . '.gitignore';
        if ($files->exists($gitignorePath)) {
            return;
        }

        $files->put($gitignorePath, "*\n!.gitignore\n");
    }

    /**
     * 根据 PHP 操作系统族与 php_uname('m') 解析与 {@see self::$generatorUrls} / {@see self::$studioUrls} 一致的键。
     */
    private function resolvePlatformKey(): string
    {
        $machine = strtolower((string) php_uname('m'));

        $arch = match (true) {
            in_array($machine, ['amd64', 'x86_64', 'x64'], true) => 'amd64',
            in_array($machine, ['arm64', 'aarch64'], true) => 'arm64',
            default => throw new \RuntimeException(
                sprintf('Unsupported CPU architecture "%s" (php_uname("m")).', php_uname('m'))
            ),
        };

        return match (PHP_OS_FAMILY) {
            'Windows' => 'windows-' . $arch,
            'Linux' => 'linux-' . $arch,
            'Darwin' => 'darwin-' . $arch,
            default => throw new \RuntimeException(
                sprintf('Unsupported operating system family "%s".', PHP_OS_FAMILY)
            ),
        };
    }
}
