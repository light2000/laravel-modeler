{{- $project := .Project -}}
<?php
/*
|--------------------------------------------------------------------------
|  ⚠️ 提示 ⚠️
|--------------------------------------------------------------------------
| 此文件由laravel-modeler自动生成，请勿直接手动修改。
| 如需调整，请在laravel-modeler修改/设计后重新生成。
| 如需自定义逻辑，请在config/modeler.php中编写。
|--------------------------------------------------------------------------
*/
    {{- range $module := $project.Modules}}
        {{- range $item := $module.Items}}
use {{$item.ModelNamespace}}\{{$item.Class}};
        {{- end}}
    {{- end}}
    {{- range $module := $project.Modules}}
        {{- range $item := $module.Items}}
            {{- if $item.HasSeeder}}
use {{$item.SeederNamespace}}\_Generated\{{$item.Class}}BaseSeeder;
            {{- end}}
            {{- if $item.HasRelationSeeder}}
use {{$item.SeederNamespace}}\_Generated\{{$item.Class}}RelationSeeder;
            {{- end}}
        {{- end}}
    {{- end}}

return [
    'morph_map' => [
    {{- range $module := $project.Modules}}
        {{- range $item := $module.Items}}
        '{{$item.Snake}}' => {{$item.Class}}::class,
        {{- end}}
    {{- end}}
    ],
    'seeders' => [
    {{- range $module := $project.Modules}}
        {{- range $item := $module.Items}}
            {{- if $item.HasSeeder}}
        {{$item.Class}}BaseSeeder::class,
            {{- end}}
        {{- end}}
     {{- end}}
    {{- range $module := $project.Modules}}
        {{- range $item := $module.Items}}
            {{- if $item.HasRelationSeeder}}
        {{$item.Class}}RelationSeeder::class,
            {{- end}}
        {{- end}}
     {{- end}}
    ],
    'migration_dirs' => [
    {{- range $dir := $project.MigrationDirs}}
        '{{$dir}}',
    {{- end}}
    ]
];
