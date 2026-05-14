<?php

namespace Light2000\Modeler\Support;

use Illuminate\Contracts\Foundation\Application;

class WritesModelerConfigJson
{
    public static function configJsonPath(): string
    {
        return (string) config('modeler.setting.RUNTIME_PATH') . '/config.json';
    }

    public static function dump(Application $app): string
    {
        $path = self::configJsonPath();
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $setting = $app['config']->get('modeler.setting', []);
        $setting['STUDIO_AUTO_OPEN'] = is_bool($setting['STUDIO_AUTO_OPEN']) ? $setting['STUDIO_AUTO_OPEN'] : true;
        $setting['STUDIO_SERVER_PORT'] = is_numeric($setting['STUDIO_SERVER_PORT']) ? strval($setting['STUDIO_SERVER_PORT']) : 'auto';
        $setting['LLM_CLAUDE_MAX_TOKENS'] = (int) $setting['LLM_CLAUDE_MAX_TOKENS'];

        $noStrvalKeys = ['STUDIO_AUTO_OPEN', 'LLM_CLAUDE_MAX_TOKENS', 'STUDIO_SERVER_PORT'];
        foreach ($setting as $key => $value) {
            if (in_array($key, $noStrvalKeys, true)) {
                continue;
            }
            $setting[$key] = strval($value);
        }

        $payload = json_encode($setting, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($payload === false) {
            throw new \RuntimeException('Failed to encode modeler setting as JSON.');
        }

        if (file_put_contents($path, $payload . "\n") === false) {
            throw new \RuntimeException('Failed to write modeler config JSON: ' . $path);
        }

        return $path;
    }
}
