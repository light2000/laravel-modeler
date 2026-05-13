{{- $project := .Project }}
{{- $module := .Module }}
{{- $dict := .Dict -}}
<?php
/*
|--------------------------------------------------------------------------
|  ⚠️ 提示 ⚠️
|--------------------------------------------------------------------------
| 此文件由laravel-modeler自动生成，请勿直接手动修改。
| 如需调整，请在laravel-modeler修改/设计后重新生成。
| 如需自定义逻辑，请在{{$dict.Namespace}}\Concerns\{{$dict.Class}}Ext中编写。
|--------------------------------------------------------------------------
*/
namespace {{$dict.Namespace}};

use {{$dict.Namespace}}\Concerns\{{$dict.Class}}Ext;

enum {{$dict.Class}}:string
{
    use {{$dict.Class}}Ext;

    {{- range $dict.Options}}
    case {{.Class}} = '{{.Snake}}';
    {{- end}}

    public function label(): string
    {
        return $this->extLabel() ?? $this->defaultLabel();
    }

    public function defaultLabel(): string
    {
        return match($this) {
    {{- range $dict.Options}}
            self::{{.Class}}     => "{{PHPEscape .Name}}",
    {{- end}}
        };
    }
}
