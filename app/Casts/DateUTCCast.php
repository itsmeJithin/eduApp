<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Carbon;

class DateUTCCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function get($model, $key, $value, $attributes)
    {
        if (!$value)
            return $value;
        $timeZone = config("app.timezone");
        $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $value, "UTC")->setTimezone($timeZone);
        return $carbon->format("d-m-Y h:i A");
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param array $value
     * @param array $attributes
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        if (!$value)
            return $value;
        $timeZone = config("app.timezone");
        $carbon = Carbon::createFromFormat('d-m-Y h:i A', $value, $timeZone)->setTimezone("UTC");
        return $carbon->format("Y-m-d H:i:s");
    }
}
