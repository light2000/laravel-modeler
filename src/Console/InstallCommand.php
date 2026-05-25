<?php

namespace Light2000\Modeler\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Light2000\Modeler\Support\BinaryDownloader;

class InstallCommand extends Command
{
    const GENERATOR_VERSION = 'v0.0.3';

    const STUDIO_VERSION = 'v0.0.4';

    const GENERATOR_URL = 'https://gitee.com/light2000/laravel-modeler-generator/releases/download/';

    const STUDIO_URL = 'https://gitee.com/light2000/laravel-modeler-studio/releases/download/';

    const FALLBACK_GENERATOR_BASE_URL = 'https://github.com/light2000/laravel-modeler-generator/releases/download/';

    const FALLBACK_STUDIO_BASE_URL = 'https://github.com/light2000/laravel-modeler-studio/releases/download/';

    protected $signature = 'modeler:install
                            {--force : Overwrite existing binaries}';

    protected $description = 'Download Modeler generator and studio binaries for this platform.';

    protected static  $studioUrls = [
        'windows-amd64' => self::STUDIO_URL . self::STUDIO_VERSION . '/studio-windows-amd64.exe',
        'linux-amd64' => self::STUDIO_URL . self::STUDIO_VERSION . '/studio-linux-amd64',
        'darwin-arm64' => self::STUDIO_URL . self::STUDIO_VERSION . '/studio-darwin-arm64',
        'darwin-amd64' => self::STUDIO_URL . self::STUDIO_VERSION . '/studio-darwin-amd64',
    ];

    protected static $generatorUrls = [
        'windows-amd64' => self::GENERATOR_URL . self::GENERATOR_VERSION . '/generator-windows-amd64.exe',
        'linux-amd64' => self::GENERATOR_URL . self::GENERATOR_VERSION . '/generator-linux-amd64',
        'darwin-arm64' => self::GENERATOR_URL . self::GENERATOR_VERSION . '/generator-darwin-arm64',
        'darwin-amd64' => self::GENERATOR_URL . self::GENERATOR_VERSION . '/generator-darwin-amd64',
    ];

    protected static $fallbackStudioUrls = [
        'windows-amd64' => self::FALLBACK_STUDIO_BASE_URL . self::STUDIO_VERSION . '/studio-windows-amd64.exe',
        'linux-amd64' => self::FALLBACK_STUDIO_BASE_URL . self::STUDIO_VERSION . '/studio-linux-amd64',
        'darwin-arm64' => self::FALLBACK_STUDIO_BASE_URL . self::STUDIO_VERSION . '/studio-darwin-arm64',
        'darwin-amd64' => self::FALLBACK_STUDIO_BASE_URL . self::STUDIO_VERSION . '/studio-darwin-amd64',
    ];

    protected static  $fallbackGeneratorUrls = [
        'windows-amd64' => self::FALLBACK_GENERATOR_BASE_URL .  self::GENERATOR_VERSION . '/generator-windows-amd64.exe',
        'linux-amd64' => self::FALLBACK_GENERATOR_BASE_URL .  self::GENERATOR_VERSION . '/generator-linux-amd64',
        'darwin-arm64' => self::FALLBACK_GENERATOR_BASE_URL .  self::GENERATOR_VERSION . '/generator-darwin-arm64',
        'darwin-amd64' => self::FALLBACK_GENERATOR_BASE_URL .  self::GENERATOR_VERSION . '/generator-darwin-amd64',
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
            if (! isset(
                self::$generatorUrls[$platformKey],
                self::$studioUrls[$platformKey],
                self::$fallbackGeneratorUrls[$platformKey],
                self::$fallbackStudioUrls[$platformKey]
            )) {
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
            $generatorFallbackUrl = self::$fallbackGeneratorUrls[$platformKey];
            $studioFallbackUrl = self::$fallbackStudioUrls[$platformKey];
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Downloading generator…');
        if (! $this->downloadBinaryWithFallback(
            $downloader,
            $generatorUrl,
            $generatorFallbackUrl,
            $generatorPath,
            $force,
            'generator'
        )) {
            $this->printManualDownloadInstructions($platformKey, $generatorPath, $studioPath);

            return self::FAILURE;
        }
        $this->line('  → ' . $generatorPath);
        $this->ensureGitignoreIgnoresBinary($files, $generatorPath);

        $this->info('Downloading studio…');
        if (! $this->downloadBinaryWithFallback(
            $downloader,
            $studioUrl,
            $studioFallbackUrl,
            $studioPath,
            $force,
            'studio'
        )) {
            $this->printManualDownloadInstructions($platformKey, $generatorPath, $studioPath);

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

    /**
     * 先使用主镜像 URL 下载，失败则改用备用 URL。
     *
     * @return bool 成功返回 true；主镜像与备用均失败返回 false
     */
    private function downloadBinaryWithFallback(
        BinaryDownloader $downloader,
        string $primaryUrl,
        string $fallbackUrl,
        string $destinationPath,
        bool $force,
        string $label
    ): bool {
        try {
            $downloader->download($primaryUrl, $destinationPath, $force, false);

            return true;
        } catch (\Throwable $e) {
            $this->warn(sprintf('「%s」主镜像下载失败：%s', $label, $e->getMessage()));
        }

        try {
            $this->line(sprintf('正在从备用地址重试下载「%s」…', $label));
            $downloader->download($fallbackUrl, $destinationPath, $force, false);

            return true;
        } catch (\Throwable $e) {
            $this->error(sprintf('「%s」备用镜像下载失败：%s', $label, $e->getMessage()));

            return false;
        }
    }

    /**
     * 主镜像与备用镜像均失败后，输出备用链接并提示手动安装。
     */
    private function printManualDownloadInstructions(
        string $platformKey,
        string $generatorPath,
        string $studioPath
    ): void {
        $generatorFallbackUrl = self::$fallbackGeneratorUrls[$platformKey];
        $studioFallbackUrl = self::$fallbackStudioUrls[$platformKey];

        $this->error('主镜像与备用镜像均无法完成下载，请手动下载并安装：');
        $this->line('  Generator（备用链接）：' . $generatorFallbackUrl);
        $this->line('  Studio（备用链接）：' . $studioFallbackUrl);
        $this->newLine();
        $this->line('请将 generator 可执行文件保存到：' . $generatorPath);
        $this->line('请将 studio 可执行文件保存到：' . $studioPath);
        $this->newLine();
        $this->comment('保存后若为 Linux/macOS，请为该二进制添加执行权限（例如 chmod +x）。');
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
