{{- $project := .Project }}
{{- $module := .Module }}
{{- $dict := .Dict -}}
<?php

namespace {{$dict.Namespace}}\Concerns;

trait {{$dict.Class}}Ext
{
    public function extLabel(): ?string
    {
        return null;
    }
}
