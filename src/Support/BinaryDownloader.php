<?php

namespace Light2000\Modeler\Support;

use GuzzleHttp\Client;

class BinaryDownloader
{
    public function __construct(
        protected Client $client
    ) {}

    /**
     * @param  bool  $verifySsl  为 false 时跳过 TLS 证书校验（仅建议在备用镜像等受信场景使用）
     */
    public function download(string $url, string $destinationPath, bool $force, bool $verifySsl = true): void
    {
        $dir = dirname($destinationPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (file_exists($destinationPath) && ! $force) {
            return;
        }

        $options = [
            'sink' => $destinationPath,
            'http_errors' => false,
        ];
        if (! $verifySsl) {
            $options['verify'] = false;
        }

        $response = $this->client->get($url, $options);

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            @unlink($destinationPath);
            throw new \RuntimeException('Download failed: HTTP '.$response->getStatusCode().' for '.$url);
        }

        if (PHP_OS_FAMILY !== 'Windows' && PHP_OS_FAMILY !== 'Unknown') {
            @chmod($destinationPath, 0755);
        }
    }
}
