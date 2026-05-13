{{- $project := .Project }}
{{- $module := .Module }}
{{- $item := .Item -}}
<?php
/*
|--------------------------------------------------------------------------
|  ⚠️ 提示 ⚠️
|--------------------------------------------------------------------------
| 此文件由laravel-modeler自动生成，请勿直接手动修改。
| 如需调整，请在laravel-modeler修改/设计后重新生成。
| 如需自定义逻辑，请在{{$item.FactoryNamespace}}\{{$item.Class}}Factory中编写。
|--------------------------------------------------------------------------
*/
namespace {{$item.FactoryNamespace}}\_Generated;

use Illuminate\Database\Eloquent\Factories\Factory;

class {{$item.Class}}Factory extends Factory
{

    protected $model = \{{$item.ModelNamespace}}\{{$item.Class}}::class;

    public function definition(): array
    {
        return [
{{- range $item.Attrs}}
    {{- if .IsFakeField}}
            '{{.Snake}}' => {{.PhpFakeValue}}
    {{- end}}
{{- end}}
        ];
    }
}
