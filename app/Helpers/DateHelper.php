<?php

use Carbon\Carbon;

if (! function_exists('date_time_id')) {
    function date_time_id($date, $withTime = true)
    {
        if (!$date) {
            return null;
        }

        $carbon = Carbon::parse($date)->locale('id');
        $carbon->settings(['formatFunction' => 'translatedFormat']);

        return $withTime
            ? $carbon->translatedFormat('d F Y H:i')
            : $carbon->translatedFormat('d F Y');
    }
}
