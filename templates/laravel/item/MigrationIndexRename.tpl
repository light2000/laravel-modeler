{{- $project := .Project }}
{{- $item := .Item -}}
{{- $newIndex := .NewIndex -}}
{{- $prevIndex := .PrevIndex -}}
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
            $table->renameIndex('{{$prevIndex.IndexName}}', '{{$newIndex.IndexName}}');
        });
    }

    public function down()
    {
        Schema::table('{{$item.Table}}', function (Blueprint $table) {
            $table->renameIndex('{{$newIndex.IndexName}}', '{{$prevIndex.IndexName}}');
        });
    }
};
