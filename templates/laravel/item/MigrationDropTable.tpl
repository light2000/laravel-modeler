{{- $project := .Project }}
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
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('{{$item.Table}}');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('{{$item.Table}}', function (Blueprint $table) {
            $table->id()->comment("{{$item.Name}}ID");
{{- range $item.Attrs}}
            {{.PhpMigration -}}
{{- end}}
{{- if $item.Timestamps}}
            $table->timestamps();
{{- else if $item.HasCreatedAt }}
            $table->timestamp('created_at')->nullable()->comment('创建时间');
{{- else if $item.HasUpdatedAt }}
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
{{- end}}
{{- if $item.SoftDel}}
            $table->softDeletes();
{{- end}}

{{- range $item.Indexes}}
    {{- if .IsUnique}}
            $table->unique([{{.ColumnNamesString}}], "{{.IndexName}}");
    {{- end }}
    {{- if .IsIndex}}
            $table->index([{{.ColumnNamesString}}], "{{.IndexName}}");
    {{- end}}
{{- end}}

            $table->comment("{{$item.Name}}");
        });
    }
};
