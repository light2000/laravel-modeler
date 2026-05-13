{{- $project := .Project }}
{{- $item := .Item -}}
{{- $newIndex := .NewIndex -}}
<?php
/*
|--------------------------------------------------------------------------
|  ⚠️ 提示 ⚠️
|--------------------------------------------------------------------------
| 此文件由laravel-modeler自动生成，请勿直接手动修改。
| 如需调整，请在laravel-modeler修改/设计后重新生成。
|--------------------------------------------------------------------------
*/
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::table('{{$item.Table}}', function (Blueprint $table) {
    {{- if $newIndex.IsUnique}}
            $table->unique([{{$newIndex.ColumnNamesString}}], "{{$newIndex.IndexName}}");
    {{- end }}
    {{- if $newIndex.IsIndex}}
            $table->index([{{$newIndex.ColumnNamesString}}], "{{$newIndex.IndexName}}");
    {{- end}}
        });
    }

    public function down()
    {
        Schema::table('{{$item.Table}}', function (Blueprint $table) {
             $table->dropIndex('{{$newIndex.IndexName}}');
        });
    }
};
