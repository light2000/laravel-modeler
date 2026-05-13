{{- $project := .Project }}
{{- $item := .Item -}}
{{- $previtem := .PrevItem -}}
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
        Schema::rename('{{$previtem.Table}}', '{{$item.Table}}');
    }

    public function down()
    {
        Schema::rename('{{$item.Table}}', '{{$previtem.Table}}');
    }
};
