<?php

use Carbon\Carbon;

if (! function_exists('display_date')) {
    function display_date($date)
    {
        if (!$date) return '';

        return Carbon::parse($date)->format('d/m/Y');
    }
}
