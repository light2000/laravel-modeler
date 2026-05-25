{{- $project := .Project }}
{{- $item := .Item }}
{{- $newAttribute := .NewAttribute}}
{{- $oldAttribute := .OldAttribute -}}
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
            $table->renameColumn('{{$oldAttribute.Snake}}', '{{$newAttribute.Snake}}');
        });
    }

    public function down()
    {
        Schema::table('{{$item.Table}}', function (Blueprint $table) {
            $table->renameColumn('{{$newAttribute.Snake}}', '{{$oldAttribute.Snake}}');
        });
    }
};
