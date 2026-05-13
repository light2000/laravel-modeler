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
|--------------------------------------------------------------------------
*/
namespace {{$item.SeederNamespace}}\_Generated;

use {{$item.ModelNamespace}}\{{$item.Class}};
use Illuminate\Database\Seeder;

class {{$item.Class}}BaseSeeder extends Seeder
{
    public function run(): void
    {
    {{- if $item.IsNormalFake}}
        $num = config('modeler.fake_quantity');
        {{$item.Class}}::unguard();
        {{$item.Class}}::factory()
            ->count($num)
            {{- if $item.IsHasSequenceFakeField}}
            ->sequence(fn ($sequence) => [
                {{- range $attr := $item.SequenceFakeAttrs}}
                '{{$attr.Snake}}' => {{$attr.SequencePhpFakeValue}},
                {{- if $attr.IsMorphId}}
                '{{$attr.MorphType}}' => {{$attr.MorphTypeFakeValue}},
                {{- end}}
                {{- end}}
            ])
            {{- end}}
            ->create();
    {{- end}}
    }
}
