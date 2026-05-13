{{- $project := .Project }}
{{- $module := .Module }}
{{- $item := .Item -}}
<?php

namespace {{$item.FactoryNamespace}};

class {{$item.Class}}Factory extends \{{$item.FactoryNamespace}}\_Generated\{{$item.Class}}Factory
{

}
