{{- $project := .Project }}
{{- $module := .Module }}
{{- $item := .Item -}}
<?php

namespace {{$item.ModelNamespace}};

use Illuminate\Database\Eloquent\Model;
use {{$item.ModelNamespace}}\_Generated\_{{$item.Class}}Trait;


class {{$item.Class}} extends Model
{
    use _{{$item.Class}}Trait;
    protected $guarded = [];
}
