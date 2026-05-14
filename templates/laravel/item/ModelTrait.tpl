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
| 如需自定义逻辑，请在{{$item.ModelNamespace}}\{{$item.Class}}中编写。
|--------------------------------------------------------------------------
*/
namespace {{$item.ModelNamespace}}\_Generated;

{{- if $item.HasSets}}
use Light2000\Modeler\Support\SetsCast;
{{- end}}

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
{{- range $relationItem := $item.RelationItems}}
use {{$relationItem.ModelNamespace}}\{{$relationItem.Class}};
{{- end}}
{{if $item.SoftDel}}
use Illuminate\Database\Eloquent\SoftDeletes;
{{- end}}
{{- if $item.HasFactory}}
use Illuminate\Database\Eloquent\Factories\HasFactory;
use {{$item.FactoryNamespace}}\{{$item.Class}}Factory;
{{- end}}
{{- range $item.DictUsed}}
use {{.Namespace}}\{{.Class}};
{{- end}}

trait _{{$item.Class}}Trait
{
    {{- if $item.HasFactory}}
    use HasFactory;
    {{- end}}
    {{- if $item.SoftDel}}
    use SoftDeletes;
    {{- end}}

    public function getTable()
    {
        return '{{$item.Table}}';
    }
{{if $item.HasFactory}}
    protected static function newFactory()
    {
        return {{$item.Class}}Factory::new();
    }
{{- end}}

    protected function casts(): array
    {
        return [
    {{- range $item.Attrs}}
        {{- if .IsInteger}}
            '{{.Snake}}' => 'integer',
        {{- end}}
        {{- if .IsDecimal}}
            '{{.Snake}}' => 'float',
        {{- end}}
        {{- if .IsBool}}
            '{{.Snake}}' => 'boolean',
        {{- end}}
        {{- if .IsString}}
            {{- if .IsPassword}}
            '{{.Snake}}' => 'hashed',
            {{- else}}
            '{{.Snake}}' => 'string',
            {{- end}}
        {{- end}}
        {{- if .IsText}}
            '{{.Snake}}' => 'string',
        {{- end}}
        {{- if .IsLongText}}
            '{{.Snake}}' => 'string',
        {{- end}}
        {{- if .IsDate }}
            '{{.Snake}}' => 'date',
        {{- end}}
        {{- if .IsTime }}
            '{{.Snake}}' => 'string',
        {{- end}}
        {{- if .IsDateTime }}
            '{{.Snake}}' => 'datetime',
        {{- end}}
        {{- if .IsYear }}
            '{{.Snake}}' => 'integer',
        {{- end}}
        {{- if .IsEnum }}
            '{{.Snake}}' => {{.Dict.Class}}::class,
        {{- end}}
        {{- if .IsSets }}
            '{{.Snake}}' => SetsCast::of({{.Dict.Class}}::class),
        {{- end}}
        {{- if .IsFile }}
            '{{.Snake}}' => 'string',
        {{- end}}
        {{- if .IsFiles }}
            '{{.Snake}}' => 'array',
        {{- end}}
    {{- end}}
        ];
    }
{{range $relation := $item.Relations}}
    {{- if $relation.IsBelongsTo}}
    public function {{$relation.Method}}(): BelongsTo
    {
        return $this->belongsTo({{$relation.GetTargetClass}}::class, '{{$relation.GetFKColumn}}');
    }
    {{end}}
    {{- if $relation.IsHasOne}}
    public function {{$relation.Method}}(): HasOne
    {
        return $this->hasOne({{$relation.GetTargetClass}}::class, '{{$relation.GetFKColumn}}');
    }
    {{end}}
    {{- if $relation.IsHasMany}}
    public function {{$relation.Method}}(): HasMany
    {
        return $this->hasMany({{$relation.GetTargetClass}}::class, '{{$relation.GetFKColumn}}');
    }
    {{end}}
    {{- if $relation.IsBelongsToMany}}
    public function {{$relation.Method}}(): BelongsToMany
    {
        return $this->belongsToMany({{$relation.GetTargetClass}}::class, '{{$relation.GetPivotTable}}', '{{$relation.GetPivotForeignKey}}', '{{$relation.GetPivotRelatedKey}}')
        {{- if $relation.HasPivotExtraAttrs}}
            ->withPivot([
            {{- range $pivotAttr := $relation.PivotExtraAttrs}}
                '{{$pivotAttr.Snake}}',
            {{- end}}
            ])
        {{- end}}
        ;
    }
    {{end}}
    {{- if $relation.IsMorphOne}}
    public function {{$relation.Method}}(): MorphOne
    {
        return $this->morphOne({{$relation.GetTargetClass}}::class, '{{$relation.MorphAble}}');
    }
    {{end}}
    {{- if $relation.IsMorphMany}}
    public function {{$relation.Method}}(): MorphMany
    {
        return $this->morphMany({{$relation.GetTargetClass}}::class, '{{$relation.MorphAble}}');
    }
    {{end}}
    {{- if $relation.IsMorphedByMany}}
        {{- range $target := $relation.MorphTargets}}
            {{- $targetItem := index $project.MapItems $target.TargetItemId}}
    public function {{ToVar $target.Code}}(): MorphToMany
    {
        return $this->morphedByMany({{$targetItem.Class}}::class, '{{$relation.MorphAble}}', '{{$relation.GetPivotTable}}', '{{$relation.GetMorphPivotForeignKey}}', '{{$relation.GetMorphPivotRelatedKey}}')
        {{- if $relation.HasPivotExtraAttrs}}
            ->withPivot([
            {{- range $pivotAttr := $relation.PivotExtraAttrs}}
                '{{$pivotAttr.Snake}}',
            {{- end}}
            ])
        {{- end}}
        ;
    }
        {{end}}
    {{- end}}
    {{- if $relation.IsMorphToMany}}
    public function {{$relation.Method}}(): MorphToMany
    {
        return $this->morphToMany({{$relation.GetTargetClass}}::class, '{{$relation.MorphAble}}', '{{$relation.GetPivotTable}}', '{{$relation.GetMorphPivotForeignKey}}', '{{$relation.GetMorphPivotRelatedKey}}')
        {{- if $relation.HasPivotExtraAttrs}}
            ->withPivot([
            {{- range $pivotAttr := $relation.PivotExtraAttrs}}
                '{{$pivotAttr.Snake}}',
            {{- end}}
            ])
        {{- end}}
        ;
    }
    {{end}}
    {{- if $relation.IsMorphTo}}
    public function {{$relation.Method}}(): MorphTo
    {
        return $this->morphTo(__FUNCTION__);
    }
    {{end}}
{{- end}}
}
