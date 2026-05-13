<?php

namespace Light2000\Modeler\Support;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use UnitEnum;

class SetsCast implements CastsAttributes
{
    public function __construct(
        protected string $enumClass
    ) {}

    public function get($model, string $key, $value, array $attributes): array
    {
        if (empty($value)) {
            return [];
        }

        $values = is_string($value) ? json_decode($value, true) : $value;

        return array_map(
            fn($v) => $this->enumClass::tryFrom($v),
            $values
        );
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (empty($value)) {
            return null;
        }

        return json_encode(array_map(fn(UnitEnum|string $v) => $v instanceof UnitEnum ? $v->value : $v, $value), JSON_UNESCAPED_UNICODE);
    }

    public static function of(string $enum): string
    {
        return static::class . ':' . $enum;
    }
}
