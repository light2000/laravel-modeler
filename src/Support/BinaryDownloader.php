<?php

namespace Light2000\Modeler\Support;

use GuzzleHttp\Client;

class BinaryDownloader
{
    public function __construct(
        protected Client $client
    ) {}

    public function download(string $url, string $destinationPath, bool $force): void
    {
        $dir = dirname($destinationPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (file_exists($destinationPath) && ! $force) {
            return;
        }

        $response = $this->client->get($url, [
            'sink' => $destinationPath,
            'http_errors' => false,
        ]);

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            @unlink($destinationPath);
            throw new \RuntimeException('Download failed: HTTP '.$response->getStatusCode().' for '.$url);
        }

        if (PHP_OS_FAMILY !== 'Windows' && PHP_OS_FAMILY !== 'Unknown') {
            @chmod($destinationPath, 0755);
        }
    }
}
