{{- $request := .Request -}}
{{- $project := .Project -}}
请根据以下信息生成表字段，并在合适时推荐关联关系与枚举字典。

【当前表信息】
表名：{{ $request.Code }}
中文名称：{{  $request.Name }}
描述：{{ $request.Description }}
所属模块：{{$request.ModuleName}}

【当前项目可选 Model Candidates】
{{- if $project.Modules }}
    {{- range $module := $project.Modules }}
        {{- range .Items }}
- model: {{ .Code }}, 中文名: {{ .Name }}, 模块: {{  $module.Name }}{{ if .Description }}, 描述: {{ .Description }}{{ end }}
        {{- end }}
    {{- end }}
{{- else }}
- 无
{{- end }}

【当前项目可选 Enum Candidates】
{{- if $project.Dictionaries }}
{{- range $project.Dictionaries }}
- name: {{ .Code }}, 中文名: {{ .Name }}{{ if .Options }}, values: [{{- range $i, $v := .Options }}{{ if $i }}, {{ end }}{{ $v.Code }}={{ $v.Name }}{{- end }}]{{ end }}
{{- end }}
{{- else }}
- 无
{{- end }}

请严格按 system 要求输出 JSON。
