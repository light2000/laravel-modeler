# light2000/laravel-modeler

Laravel 集成包：提供 `ModelerServiceProvider`、可发布配置，以及 `modeler:install`、`modeler:studio`、`modeler:generate` 命令，用于下载并调用 Modeler Studio / Generator 二进制。

## 安装

```bash
composer require light2000/laravel-modeler
```

可选发布配置：

```bash
php artisan vendor:publish --tag=modeler-config
```

## 命令

- `php artisan modeler:install` — 下载当前平台的 `generator` 与 `studio` 到 `/.modeler/bin`目录（可通过 `MODELER_*_PATH` 调整）。
- `php artisan modeler:studio` — 并以前台方式启动 Studio（Ctrl+C 结束）。
- `php artisan modeler:generate` — 调用 Generator，使用同上 `config.json`。

## License

The PHP integration layer is licensed under MIT.

Studio and Generator binaries are proprietary software
licensed separately.
