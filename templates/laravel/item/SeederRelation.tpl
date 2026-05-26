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
{{- range $relationItem := $item.RelationItems}}
{{- if ne $relationItem.Id $item.Id}}
use {{$relationItem.ModelNamespace}}\{{$relationItem.Class}};
{{- end}}
{{- end}}
use Illuminate\Support\Facades\Log;

class {{$item.Class}}RelationSeeder extends Seeder
{
    public function run(): void
    {
        ${{$item.Vars}} = {{$item.Class}}::orderBy('id')->get();
        $totalCount = count(${{$item.Vars}});
{{- if $item.IsNormalFake}}
    {{- range $relation := $item.Relations}}
        {{- if $relation.IsNotSelfRelation}}
            {{- if $relation.IsBelongsTo}}
        ${{$relation.TargetItem.Vars}}Ids = collect({{$relation.TargetItem.Class}}::pluck('id'))->shuffle()->toArray();
        ${{$relation.TargetItem.Var}}Count = count(${{$relation.TargetItem.Vars}}Ids);
        ${{$item.Vars}}->each(function (${{$item.Var}}, $index) use (${{$relation.TargetItem.Vars}}Ids, ${{$relation.TargetItem.Var}}Count) {
            try {
                ${{$item.Var}}->update([
                    '{{$relation.Attribute.Snake}}' => ${{$relation.TargetItem.Vars}}Ids[$index%${{$relation.TargetItem.Var}}Count],
                ]);
            } catch (\Exception $e) {
                Log::info("update {{$item.Snake}} {{$relation.Attribute.Snake}} failed: " . $e->getMessage());
            }
        });
            {{- end}}

            {{- if $relation.IsManyToManySeederSide}}
        ${{$relation.TargetItem.Vars}}Ids = {{$relation.TargetItem.Class}}::pluck('id');
        $groupIds = collect([]);
        $groupBaseIds = collect([]);
        while ($groupIds->count() < ${{$item.Vars}}->count()) {
            if ($groupBaseIds->isEmpty()) {
                $groupBaseIds = ${{$relation.TargetItem.Vars}}Ids->shuffle()->chunk(3)->map(function ($group) {
                    return $group->mapWithKeys(fn ($id) => [$id => {{$relation.PivotItem.Class}}::factory()->make()->toArray()])->all();
                });
            }
            $groupIds->push($groupBaseIds->shift());
        }
        ${{$item.Vars}}->each(function (${{$item.Var}}) use ($groupIds) {
            ${{$item.Var}}->{{$relation.Method}}()->sync(
                $groupIds->shift()
            );
        });
            {{- end}}

            {{- if $relation.IsMorphTo}}
                {{- range $targetItem := $relation.RightItems}}
        ${{$targetItem.Vars}} = {{$targetItem.Class}}::all()->shuffle();
                {{- end}}
        $indexes = [];
                {{- range $targetItem := $relation.RightItems}}
        $indexes['{{$targetItem.MorphMapKey}}'] = 0;
                {{- end}}
        foreach (${{$item.Vars}} as $index => ${{$item.Var}}) {
            {{- range $k, $targetItem := $relation.RightItems}}
            if ($index % {{len $relation.RightItems}} == {{$k}}) {
                try {
                    ${{$item.Var}}->{{$relation.Method}}()->associate(${{$targetItem.Vars}}[($indexes['{{$targetItem.MorphMapKey}}']++) % count(${{$targetItem.Vars}})]);
                    ${{$item.Var}}->save();
                } catch (\Exception $e) {
                    Log::info("update {{$item.Snake}} {{$relation.Method}} associate failed: " . $e->getMessage());
                }
                continue;
            }
            {{- end}}
        }
            {{- end}}
        {{- end}}

        {{- if $relation.IsSelfRelation}}
        $L1 = max(1, floor(pow($totalCount, 1/3)));
        $L2 = $L1 * $L1;

        $level1 = ${{$item.Vars}}->slice(0, $L1)->values();
        $level2 = ${{$item.Vars}}->slice($L1, $L2)->values();
        $level3 = ${{$item.Vars}}->slice($L1 + $L2)->values();

        $index2 = 0;
        $index3 = 0;
        foreach ($level1 as $i => $node1) {
            $node1->{{$relation.Attribute.Snake}} = 0;
            $node1->save();

            for ($j = 0; $j < $L1 && $index2 < count($level2); $j++, $index2++) {
                $node2 = $level2[$index2];
                $node2->{{$relation.Attribute.Snake}} = $node1->id;
                $node2->save();

                for ($k = 0; $k < $L1 && $index3 < count($level3); $k++, $index3++) {
                    $node3 = $level3[$index3];
                    $node3->{{$relation.Attribute.Snake}} = $node2->id;
                    $node3->save();
                }
            }
        }
        {{- end}}
    {{- end}}
{{- end}}
    }
}
